<?php

namespace App\Services\Maestras;

use App\Models\Maestras\MaeTerceros;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\IOFactory;

/**
 * Importa el listado de pastores (Excel "PasListadoPastoresV2", hoja "Hoja1"). Dos modos según
 * si el pastor ya existe en MaeTerceros (por cod_ter):
 * - Nuevo: se crea completo (ver reglas abajo).
 * - Ya existe: NUNCA se sobrescribe un dato que ya tenga — solo se rellenan los campos que hoy
 *   están vacíos con lo que traiga el Excel (tel, cel, email, tipo_sangre, fec_nac, lugar_expcc,
 *   cod_lice, id_conyuge, nom_conyug). congrega/cod_dist quedan explícitamente fuera de este
 *   relleno: esos dos SOLO se tocan desde MaeCongregacionController, nunca desde aquí.
 *
 * Reglas acordadas para cada pastor nuevo:
 * - Clase/Estado del Excel se ignoran: siempre se fija cod_clase=10, cod_est=01, estado=1
 *   ("para nosotros todos son pastor y estado activo").
 * - tip_prv=5 ("Pastor Activo" en el catálogo MaeTipos de Gestión Documental) — hallazgo
 *   adicional, mismo criterio de "es un pastor activo".
 * - "Dto" del Excel se descarta: MaeTerceros.cod_dist usa el mismo esquema legado de 2 dígitos
 *   que esa columna, que nunca coincidió con MaeDistritos.COD_DIST real. En vez de eso,
 *   "Templo" se cruza por NOMBRE contra MaeCongregaciones (ya sincronizada) y de ahí salen
 *   `congrega` (codigo real) y `cod_dist` (distrito real) juntos, del mismo registro.
 * - "Li" -> cod_lice tal cual (ya es un campo existente, no descubierto por este import).
 * - Tipo Sangre -> tipo_sangre (columna nueva); "-999" y vacío se tratan como sin dato.
 * - Cédula/Nombre esposa -> id_conyuge/nom_conyug, mismo patrón que ya usa un registro real hoy
 *   (id_conyuge guarda la cédula directamente, no un id interno).
 * - "Dto", "Tipo Contribuyente" y fecha de nacimiento de la esposa se descartan: no hay campo
 *   destino confiable para ellos.
 */
class TerceroImportService
{
    private const COD_CLASE_PASTOR = '10';
    private const COD_EST_ACTIVO = '01';
    private const TIP_PRV_PASTOR_ACTIVO = '5';

    // campo Excel (clave de prepararFila) => columna MaeTerceros. Deliberadamente sin
    // congrega/cod_dist — esos dos solo se cascaden desde MaeCongregacionController.
    private const CAMPOS_RELLENABLES = [
        'tel' => 'tel',
        'cel' => 'cel',
        'email' => 'email',
        'tipo_sangre' => 'tipo_sangre',
        'fec_nac' => 'fec_nac',
        'lugar_expcc' => 'lugar_expcc',
        'cod_lice' => 'cod_lice',
        'id_conyuge' => 'id_conyuge',
        'nom_conyug' => 'nom_conyug',
    ];

    private ?array $indiceCongregaciones = null;

    // clave interna => encabezados aceptados (normalizados al vuelo). La primera fila que
    // matchea "cod_ter" se toma como fila de encabezados — así funciona tanto con la plantilla
    // nueva (una sola hoja, encabezados limpios) como con el export legado de 2 hojas
    // ("PasListadoPastoresV2", portada + "Hoja1") sin tener que mantener índices de columna fijos.
    private const ENCABEZADOS_ACEPTADOS = [
        'cod_ter' => ['CED PASTOR', 'CEDULA', 'CEDULA PASTOR'],
        'nombre' => ['NOMBRE COMPLETO', 'NOMBRE'],
        'expe_cedula' => ['EXPE CEDULA', 'LUGAR EXPEDICION', 'EXPEDICION CEDULA'],
        'li' => ['LI', 'LICENCIA', 'TIPO LICENCIA'],
        'tipo_sangre' => ['TIPO SANGRE', 'SANGRE', 'RH'],
        'telefono' => ['TELEFONO'],
        'celular' => ['CELULAR'],
        'correo' => ['CORREO ELECTRONICO', 'CORREO', 'EMAIL'],
        'templo' => ['TEMPLO', 'CONGREGACION'],
        'fec_nac_texto' => ['FECHA NACIMIENTO'],
        'cedula_esposa' => ['CEDULA ESPOSA'],
        'nombre_esposa' => ['NOMBRE ESPOSA'],
    ];

    public const HEADER_PLANTILLA = [
        'Ced. Pastor', 'Nombre Completo', 'Expe Cedula', 'Li', 'Tipo Sangre', 'Telefono',
        'Celular', 'Correo Electronico', 'Templo', 'Fecha Nacimiento', 'Cedula Esposa', 'Nombre Esposa',
    ];

    public function leerExcel(string $rutaArchivo): array
    {
        $spreadsheet = IOFactory::load($rutaArchivo);
        [$sheet, $filaEncabezado, $columnas] = $this->ubicarHojaDeDatos($spreadsheet);

        $filas = $sheet->rangeToArray('A' . ($filaEncabezado + 1) . ':' . $sheet->getHighestColumn() . $sheet->getHighestRow());

        $resultado = [];
        foreach ($filas as $numeroFila => $fila) {
            $cedulaTexto = trim((string) ($fila[$columnas['cod_ter']] ?? ''));
            if ($cedulaTexto === '' || !preg_match('/^\d+$/', $cedulaTexto)) {
                continue;
            }

            $valor = fn (string $clave) => isset($columnas[$clave]) ? trim((string) ($fila[$columnas[$clave]] ?? '')) : '';

            $resultado[] = [
                'fila_excel' => $filaEncabezado + $numeroFila + 1,
                'cod_ter' => (string) (int) $cedulaTexto,
                'nombre' => $valor('nombre'),
                'expe_cedula' => $valor('expe_cedula'),
                'li' => $valor('li'),
                'tipo_sangre' => $valor('tipo_sangre'),
                'telefono' => $valor('telefono'),
                'celular' => $valor('celular'),
                'correo' => $valor('correo'),
                'templo' => $valor('templo'),
                'fec_nac_texto' => $valor('fec_nac_texto'),
                'cedula_esposa' => $valor('cedula_esposa'),
                'nombre_esposa' => $valor('nombre_esposa'),
            ];
        }

        return $resultado;
    }

    /**
     * Recorre las hojas del archivo y usa la primera fila (de las primeras ~15 de cada hoja)
     * donde aparezca un encabezado de "cod_ter" reconocido — esa es la fila de encabezados real.
     *
     * @return array{0: \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet, 1: int, 2: array<string,int>}
     */
    private function ubicarHojaDeDatos(\PhpOffice\PhpSpreadsheet\Spreadsheet $spreadsheet): array
    {
        foreach ($spreadsheet->getAllSheets() as $sheet) {
            $maxFila = min($sheet->getHighestRow(), 15);
            for ($fila = 1; $fila <= $maxFila; $fila++) {
                $encabezados = $sheet->rangeToArray('A' . $fila . ':' . $sheet->getHighestColumn() . $fila)[0];
                $columnas = $this->mapearColumnas($encabezados);
                if (isset($columnas['cod_ter'])) {
                    return [$sheet, $fila, $columnas];
                }
            }
        }

        throw new \RuntimeException('No se encontró una fila de encabezados reconocible (se esperaba una columna "Ced. Pastor" o "Cedula") en ninguna hoja del archivo.');
    }

    /** @return array<string,int> clave interna => índice de columna (base 0) */
    private function mapearColumnas(array $encabezados): array
    {
        $columnas = [];
        foreach ($encabezados as $idx => $texto) {
            $normalizado = $this->normalizar((string) $texto);
            if ($normalizado === '') {
                continue;
            }
            foreach (self::ENCABEZADOS_ACEPTADOS as $clave => $aliases) {
                // "First match wins": si dos columnas comparten encabezado (ej. "Fecha
                // Nacimiento" del pastor y de la esposa en el export legado), la primera se
                // queda con la clave y la segunda simplemente no matchea nada más — se ignora,
                // igual que ya se decidió para la fecha de nacimiento de la esposa.
                if (!isset($columnas[$clave]) && in_array($normalizado, $aliases, true)) {
                    $columnas[$clave] = $idx;
                    break;
                }
            }
        }

        return $columnas;
    }

    public function analizar(array $filas): array
    {
        $this->construirIndiceCongregaciones();

        $cedulas = array_column($filas, 'cod_ter');
        $existentes = DB::table('MaeTerceros')
            ->whereIn('cod_ter', $cedulas)
            ->pluck('cod_ter')
            ->map(fn ($c) => (string) $c)
            ->flip();

        $existentesConDatos = DB::table('MaeTerceros')
            ->whereIn('cod_ter', $cedulas)
            ->get(['cod_ter', ...array_values(self::CAMPOS_RELLENABLES)])
            ->keyBy(fn ($t) => (string) $t->cod_ter);

        $nuevos = [];
        $enriquecer = [];
        $excepciones = [];
        $yaExistenSinCambios = 0;
        $duplicados = [];
        $vistos = [];

        foreach ($filas as $fila) {
            if (isset($vistos[$fila['cod_ter']])) {
                $duplicados[] = $fila['cod_ter'];
                continue;
            }
            $vistos[$fila['cod_ter']] = true;

            if (isset($existentes[$fila['cod_ter']])) {
                $camposAllenar = $this->camposParaRellenar($fila, $existentesConDatos[$fila['cod_ter']]);
                if ($camposAllenar) {
                    $enriquecer[] = ['cod_ter' => $fila['cod_ter'], 'nombre' => $fila['nombre'], 'campos' => $camposAllenar];
                } else {
                    $yaExistenSinCambios++;
                }
                continue;
            }

            $resultado = $this->prepararFila($fila);
            if ($resultado['excepciones']) {
                $excepciones[] = ['cod_ter' => $fila['cod_ter'], 'nombre' => $fila['nombre'], 'motivos' => $resultado['excepciones']];
            } else {
                $nuevos[] = $resultado;
            }
        }

        return [
            'total_filas' => count($filas),
            'ya_existen_count' => $yaExistenSinCambios + count($enriquecer),
            'ya_existen_sin_cambios_count' => $yaExistenSinCambios,
            'duplicados' => $duplicados,
            'nuevos' => $nuevos,
            'enriquecer' => $enriquecer,
            'excepciones' => $excepciones,
        ];
    }

    public function aplicar(array $filas): array
    {
        $analisis = $this->analizar($filas);

        DB::transaction(function () use ($analisis) {
            foreach (array_chunk($analisis['nuevos'], 300) as $lote) {
                MaeTerceros::query()->insert(array_column($lote, 'datos'));
            }

            foreach (array_chunk($analisis['enriquecer'], 300) as $lote) {
                $this->rellenarCamposEnLote($lote);
            }
        });

        return [
            'insertados' => count($analisis['nuevos']),
            'enriquecidos' => count($analisis['enriquecer']),
            'ya_existian' => $analisis['ya_existen_count'],
            'excepciones' => count($analisis['excepciones']),
        ];
    }

    /** Campos que hoy están vacíos en MaeTerceros y el Excel sí trae — nunca sobrescribe. */
    private function camposParaRellenar(array $fila, object $actual): array
    {
        $valoresExcel = $this->valoresBasicos($fila);
        $campos = [];

        foreach (self::CAMPOS_RELLENABLES as $claveExcel => $columna) {
            $valorActual = trim((string) ($actual->$columna ?? ''));
            $valorNuevo = $valoresExcel[$claveExcel];
            if ($valorActual === '' && $valorNuevo !== null && $valorNuevo !== '') {
                $campos[$columna] = $valorNuevo;
            }
        }

        return $campos;
    }

    /**
     * UPDATE ... CASE cod_ter WHEN ... END por columna, un round-trip por lote. Cada persona
     * solo tiene las columnas que realmente le faltaban (ver camposParaRellenar), así que cada
     * CASE cubre distintos cod_ter según el campo.
     */
    private function rellenarCamposEnLote(array $lote): void
    {
        $porColumna = [];
        foreach ($lote as $item) {
            foreach ($item['campos'] as $columna => $valor) {
                $porColumna[$columna][$item['cod_ter']] = $valor;
            }
        }
        if (!$porColumna) {
            return;
        }

        $sets = [];
        $bindings = [];
        $todosCodTer = [];
        foreach ($porColumna as $columna => $valores) {
            $case = "{$columna} = CASE cod_ter ";
            foreach ($valores as $codTer => $valor) {
                $case .= 'WHEN ? THEN ? ';
                $bindings[] = $codTer;
                $bindings[] = $valor;
                $todosCodTer[$codTer] = true;
            }
            $case .= "ELSE {$columna} END";
            $sets[] = $case;
        }

        $codTers = array_keys($todosCodTer);
        $placeholders = implode(',', array_fill(0, count($codTers), '?'));

        DB::update(
            'UPDATE MaeTerceros SET ' . implode(', ', $sets) . " WHERE cod_ter IN ({$placeholders})",
            [...$bindings, ...$codTers]
        );
    }

    private function prepararFila(array $fila): array
    {
        $excepciones = [];

        $congregacion = null;
        if ($fila['templo'] !== '' && $fila['templo'] !== '-') {
            $congregacion = $this->resolverCongregacion($fila['templo']);
            if ($congregacion === null) {
                $excepciones[] = "templo '{$fila['templo']}' no encontrado en MaeCongregaciones";
            }
        }

        if ($excepciones) {
            return ['excepciones' => $excepciones];
        }

        $datos = [
            'cod_ter' => $fila['cod_ter'],
            'nom_ter' => $this->truncar(strtoupper($fila['nombre']), 255),
            'estado' => 1,
            'cod_clase' => self::COD_CLASE_PASTOR,
            'cod_est' => self::COD_EST_ACTIVO,
            'tip_prv' => self::TIP_PRV_PASTOR_ACTIVO,
            'congrega' => $congregacion['codigo'] ?? null,
            'cod_dist' => $congregacion['distrito'] ?? null,
            ...$this->valoresBasicos($fila),
        ];

        return ['excepciones' => [], 'cod_ter' => $fila['cod_ter'], 'nombre' => $fila['nombre'], 'datos' => $datos];
    }

    /**
     * Valores ya limpios/normalizados de los campos que NO dependen de resolver congregación —
     * compartido entre crear (prepararFila) y rellenar vacíos de un existente (camposParaRellenar).
     */
    private function valoresBasicos(array $fila): array
    {
        $tipoSangre = strtoupper($fila['tipo_sangre']);
        if ($tipoSangre === '-999' || $tipoSangre === '') {
            $tipoSangre = null;
        }

        $cedulaEsposa = preg_match('/^\d+$/', $fila['cedula_esposa']) ? (string) (int) $fila['cedula_esposa'] : null;

        return [
            'lugar_expcc' => $fila['expe_cedula'] ?: null,
            'cod_lice' => $this->truncar($fila['li'], 10),
            'tipo_sangre' => $tipoSangre,
            'tel' => $this->truncar($fila['telefono'], 255),
            'cel' => $this->truncar($fila['celular'], 100),
            'email' => filter_var($fila['correo'], FILTER_VALIDATE_EMAIL) ?: null,
            'fec_nac' => $this->parsearFecha($fila['fec_nac_texto']),
            'id_conyuge' => $cedulaEsposa,
            'nom_conyug' => $this->truncar(strtoupper($fila['nombre_esposa']), 255),
        ];
    }

    /** @return array{codigo:string,distrito:?string}|null */
    private function resolverCongregacion(string $templo): ?array
    {
        $pos = strpos($templo, '-');
        $nombre = $pos !== false ? trim(substr($templo, $pos + 1)) : $templo;
        $clave = $this->normalizar($nombre);

        return $this->indiceCongregaciones[$clave] ?? null;
    }

    private function normalizar(string $s): string
    {
        $s = trim($s);
        $s = mb_strtoupper($s, 'UTF-8');
        $s = str_replace(['Á', 'É', 'Í', 'Ó', 'Ú', 'Ñ', 'Ü'], ['A', 'E', 'I', 'O', 'U', 'N', 'U'], $s);
        $s = str_replace(['.', ':'], '', $s);

        return trim(preg_replace('/\s+/', ' ', $s));
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

    private function truncar(?string $texto, int $limite): ?string
    {
        if ($texto === null || $texto === '') {
            return null;
        }

        return mb_substr($texto, 0, $limite);
    }

    private function construirIndiceCongregaciones(): void
    {
        if ($this->indiceCongregaciones !== null) {
            return;
        }

        $this->indiceCongregaciones = [];
        $congregaciones = DB::table('MaeCongregaciones')->select('codigo', 'nombre', 'distrito')->orderBy('codigo')->get();
        foreach ($congregaciones as $c) {
            $clave = $this->normalizar($c->nombre);
            // Si dos congregaciones comparten nombre normalizado, se queda la primera (menor
            // codigo) en vez de sobrescribir con una posiblemente incorrecta.
            if (!isset($this->indiceCongregaciones[$clave])) {
                $this->indiceCongregaciones[$clave] = ['codigo' => (string) $c->codigo, 'distrito' => $c->distrito];
            }
        }
    }
}
