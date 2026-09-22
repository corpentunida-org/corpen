<?php

namespace App\Services\Maestras;

use App\Models\Maestras\MaeCongregacion;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\IOFactory;

/**
 * Importa el listado periódico de congregaciones (Excel "PasListadoTemplo", ~mensual) y
 * sincroniza MaeCongregaciones con las reglas acordadas:
 *
 * 1. Si el pastor del Excel es distinto al actual: el actual pasa a pastorAnterior, se pone
 *    el nuevo. Si el Excel no trae cédula, el actual pasa a pastorAnterior y el pastor queda
 *    vacío (no se "inventa" un pastor ni se deja el viejo).
 * 2. Congregaciones que no existen (por código) se insertan completas.
 * 3. El distrito se actualiza en TODAS las congregaciones de la lista, existan o no.
 * 4. El pastor recién asignado (nuevo o igual, mientras haya cédula) también queda enlazado
 *    en su propio MaeTerceros.congrega, usando el código real de MaeCongregaciones — no el
 *    formato legado ("NNNN - XXX") que hoy está desincronizado en ~95% de los terceros.
 *
 * Las congregaciones que hoy existen en la BD y no aparecen en el Excel NO se tocan (decisión
 * explícita: el Excel es una actualización parcial, no el listado completo).
 */
class CongregacionImportService
{
    // Municipios "Municipio-Departamento" (ya normalizados: mayúsculas, sin tildes) cuyo texto
    // no matchea directo contra el catálogo real — encontrados al analizar el archivo de
    // septiembre 2026. Mapea al texto CORRECTO en el mismo formato, que sí matchea.
    private const ALIAS_MUNICIPIO_TEXTO = [
        'RIOBLANCO-TOLIMA' => 'RIO BLANCO-TOLIMA',
        'VALLE DEL GUAMUEZ-PUTUMAYO' => 'VALLE DE GUAMEZ-PUTUMAYO',
        'DIBULLA-LA GUAJIRA' => 'DIBULA-LA GUAJIRA',
        'CHIBOLO-MAGDALENA' => 'CHIVOLO-MAGDALENA',
        'VISTAHERMOSA-META' => 'VISTA HERMOSA-META',
        'PUEBLOVIEJO-MAGDALENA' => 'PUEBLO VIEJO-MAGDALENA',
        'BUENAVISTA-BOYACA' => 'BUENA VISTA-BOYACA',
        'PUERTO RICO-BOLIVAR' => 'TIQUISIO-BOLIVAR', // Puerto Rico es la cabecera municipal de Tiquisio
        'CATATUMBO-NORTE DE SANTANDER' => 'TIBU-NORTE DE SANTANDER', // Catatumbo es la subregión, no el municipio
    ];

    // Clase de texto libre del Excel -> id de MaeClaseCongregacion. La mayoría matchea con
    // normalizar mayúsculas/tildes; "PROYECCION" es la única excepción real (BD dice "EN
    // PROYECCION").
    private const ALIAS_CLASE_TEXTO = [
        'PROYECCION' => 'EN PROYECCION',
    ];

    private ?array $indiceMunicipios = null;
    private ?array $indiceMunicipiosPorNombre = null;
    private ?array $indiceClases = null;
    private ?int $municipioBogotaId = null;
    private ?int $municipioElAguilaId = null;
    private ?int $municipioSanAndresId = null;

    /**
     * Lee el Excel y devuelve las filas ya normalizadas (sin tocar la BD).
     */
    public function leerExcel(string $rutaArchivo): array
    {
        $spreadsheet = IOFactory::load($rutaArchivo);
        $sheet = $spreadsheet->getSheet(0);
        $filas = $sheet->rangeToArray('A2:N' . $sheet->getHighestRow());

        $resultado = [];
        foreach ($filas as $numeroFila => $fila) {
            $codigoTexto = trim((string) ($fila[0] ?? ''));
            // El archivo trae una fila de totales al final (ej. "Total" / "5526") que no es una
            // congregación — un código de congregación siempre es numérico.
            if ($codigoTexto === '' || !preg_match('/^\d+$/', $codigoTexto)) {
                continue;
            }
            // MaeCongregaciones.codigo es INT: "0101" y "101" son la MISMA fila en la BD. Sin
            // normalizar aquí, una congregación existente con ceros a la izquierda en el Excel
            // se clasifica como "nueva" y el INSERT truena por PK duplicada.
            $codigo = (string) (int) $codigoTexto;

            $resultado[] = [
                'fila_excel' => $numeroFila + 2,
                'codigo' => $codigo,
                'codigo_texto' => $codigoTexto,
                'nombre' => trim((string) ($fila[1] ?? '')),
                'estado_texto' => trim((string) ($fila[2] ?? '')),
                'clase_texto' => trim((string) ($fila[3] ?? '')),
                'municipio_texto' => trim((string) ($fila[4] ?? '')),
                'direccion' => trim((string) ($fila[5] ?? '')),
                'telefono' => trim((string) ($fila[6] ?? '')),
                'celular' => trim((string) ($fila[7] ?? '')),
                'distrito' => trim((string) ($fila[8] ?? '')),
                'apertura_texto' => trim((string) ($fila[9] ?? '')),
                'cierre_texto' => trim((string) ($fila[10] ?? '')),
                'observacion' => trim((string) ($fila[11] ?? '')),
                'cedula_pastor' => trim((string) ($fila[12] ?? '')),
                'nombre_pastor_excel' => trim((string) ($fila[13] ?? '')),
            ];
        }

        return $resultado;
    }

    /**
     * Analiza las filas contra el estado actual de la BD, sin escribir nada. Devuelve el plan
     * de lo que se haría (para mostrarlo en pantalla) y las excepciones que necesitan revisión
     * manual antes de poder aplicarse.
     */
    public function analizar(array $filas): array
    {
        $this->construirIndices();

        $codigos = array_column($filas, 'codigo');
        $congregacionesActuales = DB::table('MaeCongregaciones')
            ->whereIn('codigo', $codigos)
            ->get()
            ->keyBy(fn ($c) => (string) $c->codigo);

        $distritosValidos = DB::table('MaeDistritos')->pluck('COD_DIST')->map(fn ($d) => (string) $d)->flip();
        $cedulasValidas = DB::table('MaeTerceros')
            ->whereIn('cod_ter', array_filter(array_column($filas, 'cedula_pastor')))
            ->pluck('cod_ter')->map(fn ($c) => (string) $c)->flip();

        $plan = ['nuevos' => [], 'actualizaciones' => []];
        $excepciones = [];
        $duplicados = [];
        $vistos = [];

        foreach ($filas as $fila) {
            if (isset($vistos[$fila['codigo']])) {
                $duplicados[] = $fila['codigo'];
                continue;
            }
            $vistos[$fila['codigo']] = true;

            $resultadoFila = $this->prepararFila($fila, $congregacionesActuales, $distritosValidos, $cedulasValidas);

            if ($resultadoFila['excepciones']) {
                $excepciones[] = ['codigo' => $fila['codigo'], 'nombre' => $fila['nombre'], 'motivos' => $resultadoFila['excepciones']];
                continue;
            }

            if ($resultadoFila['es_nuevo']) {
                $plan['nuevos'][] = $resultadoFila;
            } else {
                $plan['actualizaciones'][] = $resultadoFila;
            }
        }

        $pastorCambia = array_filter($plan['actualizaciones'], fn ($f) => $f['pastor_cambia']);
        $distritoCambia = array_filter($plan['actualizaciones'], fn ($f) => $f['distrito_cambia']);

        return [
            'total_filas' => count($filas),
            'duplicados' => $duplicados,
            'nuevos' => $plan['nuevos'],
            'actualizaciones' => $plan['actualizaciones'],
            'pastor_cambia_count' => count($pastorCambia),
            'distrito_cambia_count' => count($distritoCambia),
            'sin_cambios_count' => count($plan['actualizaciones']) - count(array_filter($plan['actualizaciones'], fn ($f) => $f['pastor_cambia'] || $f['distrito_cambia'] || $f['otros_cambian'])),
            'excepciones' => $excepciones,
        ];
    }

    /**
     * Aplica el plan ya analizado. Se re-valida contra la BD en el momento (no confía en un
     * análisis viejo) para no pisar cambios hechos por otra persona entre el análisis y la
     * confirmación.
     */
    // ~5.500 filas por corrida: una query por fila (más de 10.000 en total contando la cascada
    // a MaeTerceros) tarda varios minutos contra RDS solo en latencia de red. Se escribe en
    // lotes de UPSERT / UPDATE masivo en su lugar — la misma corrida baja de minutos a segundos.
    private const TAMANO_LOTE = 300;

    private const COLUMNAS_UPSERT = [
        'nombre', 'clase', 'estado', 'municipio', 'municipio_exterior_detalle',
        'direccion', 'telefono', 'celular', 'distrito', 'apertura', 'cierre',
        'observacion', 'pastor', 'pastorAnterior', 'codigo_texto',
    ];

    public function aplicar(array $filas): array
    {
        $analisis = $this->analizar($filas);
        $porAplicar = array_merge($analisis['nuevos'], $analisis['actualizaciones']);

        // cod_ter => [codigo, distrito] de la congregación que pastorea, para la cascada a
        // MaeTerceros (congrega + cod_dist juntos — antes solo se cascadeaba congrega, dejando
        // cod_dist desincronizado para todo pastor que solo pasara por esta importación y no
        // por el backfill manual).
        $pastorPorCongregacion = collect($porAplicar)
            ->filter(fn ($f) => !empty($f['datos']['pastor']))
            ->mapWithKeys(fn ($f) => [$f['datos']['pastor'] => ['codigo' => $f['datos']['codigo'], 'distrito' => $f['datos']['distrito']]]);

        DB::transaction(function () use ($porAplicar, $pastorPorCongregacion) {
            foreach (array_chunk($porAplicar, self::TAMANO_LOTE) as $lote) {
                MaeCongregacion::query()->upsert(
                    array_column($lote, 'datos'),
                    ['codigo'],
                    self::COLUMNAS_UPSERT
                );
            }

            foreach ($pastorPorCongregacion->chunk(self::TAMANO_LOTE) as $lote) {
                $this->actualizarCongregaEnLote($lote);
            }
        });

        return [
            'insertados' => count($analisis['nuevos']),
            'actualizados' => count(array_filter($analisis['actualizaciones'], fn ($f) => $f['pastor_cambia'] || $f['distrito_cambia'] || $f['otros_cambian'])),
            'pastores_enlazados' => $pastorPorCongregacion->count(),
            'excepciones' => count($analisis['excepciones']),
        ];
    }

    /** UPDATE ... CASE cod_ter WHEN ... THEN ... END — un solo round-trip por lote en vez de uno por pastor. */
    private function actualizarCongregaEnLote($lote): void
    {
        $codTers = $lote->keys()->all();
        if (!$codTers) {
            return;
        }

        $casoCongrega = 'CASE cod_ter ';
        $casoDistrito = 'CASE cod_ter ';
        $bindingsCongrega = [];
        $bindingsDistrito = [];
        foreach ($lote as $codTer => $datos) {
            $casoCongrega .= 'WHEN ? THEN ? ';
            $bindingsCongrega[] = $codTer;
            $bindingsCongrega[] = $datos['codigo'];
            $casoDistrito .= 'WHEN ? THEN ? ';
            $bindingsDistrito[] = $codTer;
            $bindingsDistrito[] = $datos['distrito'];
        }
        $casoCongrega .= 'END';
        $casoDistrito .= 'END';

        $placeholders = implode(',', array_fill(0, count($codTers), '?'));

        DB::update(
            "UPDATE MaeTerceros SET congrega = {$casoCongrega}, cod_dist = {$casoDistrito} WHERE cod_ter IN ({$placeholders})",
            [...$bindingsCongrega, ...$bindingsDistrito, ...$codTers]
        );
    }

    private function prepararFila(array $fila, $congregacionesActuales, $distritosValidos, $cedulasValidas): array
    {
        $excepciones = [];

        $actual = $congregacionesActuales->get($fila['codigo']);
        $esNuevo = !$actual;

        // --- Distrito (regla 3: se actualiza siempre que esté en la lista) ---
        if ($fila['distrito'] === '' || !isset($distritosValidos[$fila['distrito']])) {
            $excepciones[] = "distrito '{$fila['distrito']}' no existe en MaeDistritos";
        }

        // --- Clase ---
        $claseId = $this->mapearClase($fila['clase_texto']);
        if ($claseId === null) {
            $excepciones[] = "clase '{$fila['clase_texto']}' no reconocida";
        }

        // --- Municipio ---
        $municipio = $this->mapearMunicipio($fila['municipio_texto']);
        if ($municipio === null) {
            $excepciones[] = "municipio '{$fila['municipio_texto']}' no encontrado en el catálogo";
        }

        // Lima (y cualquier fila mapeada a Otro/Exterior con esta convención) se marca de una
        // vez como Obra Misionera, salvo que el Excel ya traiga una clase más específica.
        if ($municipio && $municipio['id'] === MaeCongregacion::MUNICIPIO_EXTERIOR_ID && $claseId === null) {
            $claseId = $this->indiceClases['OBRA MISIONERA'] ?? $claseId;
        }

        // --- Pastor (regla 1) ---
        $cedulaExcel = $fila['cedula_pastor'];
        $pastorActual = $actual ? (string) ($actual->pastor ?? '') : '';
        $pastorAnteriorActual = $actual ? (string) ($actual->pastorAnterior ?? '') : '';

        if ($cedulaExcel !== '' && !isset($cedulasValidas[$cedulaExcel])) {
            $excepciones[] = "cédula de pastor '{$cedulaExcel}' no existe en MaeTerceros";
        }

        $pastorCambia = false;
        $nuevoPastor = $pastorActual !== '' ? $pastorActual : null;
        $nuevoPastorAnterior = $pastorAnteriorActual !== '' ? $pastorAnteriorActual : null;

        if (!$esNuevo && empty($excepciones)) {
            if ($cedulaExcel === '' && $pastorActual !== '') {
                // Sin cédula en el Excel: el actual pasa a anterior, pastor queda vacío.
                $nuevoPastorAnterior = $pastorActual;
                $nuevoPastor = null;
                $pastorCambia = true;
            } elseif ($cedulaExcel !== '' && $cedulaExcel !== $pastorActual) {
                $nuevoPastorAnterior = $pastorActual !== '' ? $pastorActual : $nuevoPastorAnterior;
                $nuevoPastor = $cedulaExcel;
                $pastorCambia = true;
            }
        } elseif ($esNuevo) {
            $nuevoPastor = $cedulaExcel !== '' ? $cedulaExcel : null;
        }

        if ($excepciones) {
            return ['excepciones' => $excepciones];
        }

        $estado = strtoupper($fila['estado_texto']) === 'ABIERTO' ? 1 : ($fila['estado_texto'] === '' ? 1 : 0);
        $apertura = $this->parsearFecha($fila['apertura_texto']);
        $cierre = $this->parsearFecha($fila['cierre_texto']);

        $datos = [
            // Las columnas de MaeCongregaciones son mucho más angostas de lo que sugiere el
            // Excel (nombre/direccion varchar(45), observacion varchar(255)) — se trunca en
            // vez de dejar que un INSERT/UPDATE tumbe toda la fila (y con ella, dentro de la
            // misma transacción, el resto del lote).
            'nombre' => $this->truncar(strtoupper($fila['nombre']), 45),
            'clase' => $claseId,
            'estado' => $estado,
            'municipio' => $municipio['id'],
            'municipio_exterior_detalle' => $this->truncar($municipio['detalle'], 150),
            'direccion' => $this->truncar(strtoupper($fila['direccion']), 45),
            'telefono' => $this->truncar($fila['telefono'], 45),
            'celular' => $this->truncar($fila['celular'], 45),
            'distrito' => $fila['distrito'],
            'apertura' => $apertura,
            'cierre' => $cierre,
            'observacion' => $this->truncar($fila['observacion'], 255),
            'pastor' => $nuevoPastor,
            'pastorAnterior' => $nuevoPastorAnterior,
            // Texto crudo del Excel (con ceros a la izquierda si los trae) — solo trazabilidad,
            // no cambia cómo se identifica la congregación (ver migración codigo_texto).
            'codigo_texto' => $fila['codigo_texto'],
        ];

        // Siempre presente (no solo para nuevos): aplicar() hace upsert en lote y necesita el
        // codigo en cada fila del lote como llave de conflicto.
        $datos['codigo'] = $fila['codigo'];

        return [
            'excepciones' => [],
            'codigo' => $fila['codigo'],
            'nombre' => $fila['nombre'],
            'es_nuevo' => $esNuevo,
            'pastor_cambia' => $pastorCambia,
            'distrito_cambia' => !$esNuevo && (string) $actual->distrito !== $fila['distrito'],
            'otros_cambian' => !$esNuevo && (
                strtoupper($actual->nombre) !== strtoupper($fila['nombre'])
                || (int) $actual->clase !== $claseId
                || (int) $actual->estado !== $estado
                || (string) $actual->municipio !== (string) $municipio['id']
            ),
            'pastor_anterior_texto' => $pastorActual,
            'pastor_nuevo_texto' => $nuevoPastor,
            'datos' => $datos,
        ];
    }

    private function mapearClase(string $textoOriginal): ?int
    {
        $texto = $this->normalizar($textoOriginal);
        $texto = self::ALIAS_CLASE_TEXTO[$texto] ?? $texto;

        return $this->indiceClases[$texto] ?? null;
    }

    /** @return array{id:int,detalle:?string}|null */
    private function mapearMunicipio(string $textoOriginal): ?array
    {
        $texto = $this->normalizar($textoOriginal);

        if (str_starts_with($texto, 'LIMA-')) {
            return ['id' => MaeCongregacion::MUNICIPIO_EXTERIOR_ID, 'detalle' => 'LIMA, PERU'];
        }

        if (str_starts_with($texto, 'BOGOTA')) {
            return $this->municipioBogotaId ? ['id' => $this->municipioBogotaId, 'detalle' => null] : null;
        }

        $texto = self::ALIAS_MUNICIPIO_TEXTO[$texto] ?? $texto;

        $pos = strrpos($texto, '-');
        if ($pos === false) {
            return null;
        }
        $municipio = trim(substr($texto, 0, $pos));
        $departamento = trim(substr($texto, $pos + 1));

        if ($municipio === 'SAN ANDRES' && $departamento === 'SAN ANDRES') {
            return $this->municipioSanAndresId ? ['id' => $this->municipioSanAndresId, 'detalle' => null] : null;
        }
        if ($municipio === 'EL AGUILA' && $departamento === 'VALLE DEL CAUCA') {
            return $this->municipioElAguilaId ? ['id' => $this->municipioElAguilaId, 'detalle' => null] : null;
        }

        $key = $municipio . '|' . $departamento;
        if (isset($this->indiceMunicipios[$key])) {
            return ['id' => $this->indiceMunicipios[$key], 'detalle' => null];
        }

        if (isset($this->indiceMunicipiosPorNombre[$municipio]) && count($this->indiceMunicipiosPorNombre[$municipio]) === 1) {
            return ['id' => $this->indiceMunicipiosPorNombre[$municipio][0], 'detalle' => null];
        }

        return null;
    }

    private function truncar(?string $texto, int $limite): ?string
    {
        if ($texto === null || $texto === '') {
            return null;
        }

        return mb_substr($texto, 0, $limite);
    }

    private function parsearFecha(string $texto): ?string
    {
        if ($texto === '') {
            return null;
        }

        try {
            return Carbon::createFromFormat('d/m/Y', $texto)->toDateString();
        } catch (\Throwable) {
            return null;
        }
    }

    private function normalizar(string $s): string
    {
        $s = trim($s);
        $s = mb_strtoupper($s, 'UTF-8');
        $s = str_replace(['Á', 'É', 'Í', 'Ó', 'Ú', 'Ñ', 'Ü'], ['A', 'E', 'I', 'O', 'U', 'N', 'U'], $s);

        return preg_replace('/\s+/', ' ', $s);
    }

    private function construirIndices(): void
    {
        if ($this->indiceMunicipios !== null) {
            return;
        }

        $municipios = DB::table('MaeMunicipios as m')
            ->join('MaeDepartamentos as d', 'd.codigo_Dane', '=', 'm.id_departamento')
            ->select('m.id', 'm.nombre as municipio', 'd.nombre as departamento')
            ->get();

        $this->indiceMunicipios = [];
        $this->indiceMunicipiosPorNombre = [];
        foreach ($municipios as $m) {
            $muniNorm = $this->normalizar($m->municipio);
            $deptoNorm = $this->normalizar($m->departamento);
            $this->indiceMunicipios[$muniNorm . '|' . $deptoNorm] = $m->id;
            $this->indiceMunicipiosPorNombre[$muniNorm][] = $m->id;

            if (str_contains($muniNorm, 'BOGOTA')) {
                $this->municipioBogotaId = $m->id;
            }
            if (str_contains($muniNorm, 'GUILA') && $deptoNorm === 'VALLE DEL CAUCA') {
                $this->municipioElAguilaId = $m->id;
            }
            if ($muniNorm === 'SAN ANDRES' && str_contains($deptoNorm, 'ARCHIPIELAGO')) {
                $this->municipioSanAndresId = $m->id;
            }
        }

        $this->indiceClases = DB::table('MaeClaseCongregacion')
            ->get()
            ->mapWithKeys(fn ($c) => [$this->normalizar($c->nombre) => $c->id])
            ->toArray();
    }
}
