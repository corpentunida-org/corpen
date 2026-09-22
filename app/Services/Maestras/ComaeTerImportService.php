<?php

namespace App\Services\Maestras;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;

/**
 * Importa el export legado "CoMae_ter" (sistema externo de la Asociación Gremial de Ministros
 * de la IPUC), hoja "Comae_ter": ~22.170 filas cuyos encabezados coinciden casi 1 a 1 (157 de
 * 160) con las columnas reales de MaeTerceros. A diferencia del import de Pastores, este
 * archivo NO es solo pastores — mezcla personas, proveedores (NIT) y otros terceros — y casi
 * todas las filas (22.168 de 22.169 reales) ya existen en la base por cod_ter; solo 1 es nueva.
 *
 * Reglas acordadas con el usuario:
 * - "Solo rellenar vacíos": si el dato ya existe en MaeTerceros (aunque sea distinto al del
 *   Excel), NUNCA se sobrescribe. Solo se llena lo que hoy está vacío — mismo criterio ya usado
 *   en el import de Pastores (TerceroImportService), pero aquí generalizado a las ~155 columnas
 *   comparables en vez de una lista fija de 9.
 * - congrega/cod_dist quedan excluidos de este import: siguen bloqueados y solo se tocan desde
 *   MaeCongregacionController (regla de gobierno ya vigente para toda la Maestra de Terceros).
 * - id (autoincremental) y cod_ter (clave de cruce) no se tratan como "campo a rellenar".
 * - El mapeo de columnas es genérico por NOMBRE exacto de columna (no por alias, a diferencia de
 *   Pastores): se toma la intersección entre los encabezados del Excel y las columnas reales de
 *   MaeTerceros. Los encabezados del Excel sin columna real (idrow, apell1, apell2, XXX,
 *   parentezco) se ignoran — se verificó que están vacíos en las 22.169 filas del archivo real.
 * - La fila cod_ter=1 ("No definido") es un placeholder del export legado y se excluye.
 * - Fechas: el archivo mezcla números de serie de Excel, strings ya formateados ("Y-m-d ...") y
 *   los centinelas de "sin fecha" 1899-12-30/1900-01-01/0000-00-00 — un solo parser normaliza
 *   los tres formatos y trata los centinelas como vacío (null), nunca como fecha real.
 */
class ComaeTerImportService
{
    private const TABLA = 'MaeTerceros';

    // id: autoincremental. cod_ter: clave de cruce, no un "campo a rellenar". congrega/cod_dist:
    // bloqueados, solo se tocan desde Congregaciones.
    private const CAMPOS_EXCLUIDOS = ['id', 'cod_ter', 'congrega', 'cod_dist'];

    private const COLUMNAS_FECHA = [
        'fec_ing', 'fec_cump', 'fec_dat', 'fec_nac', 'fecha_aded', 'fec_minis',
        'fec_falle', 'fecha_lice', 'fecha_ipuc', 'fec_aport', 'fec_expcc',
    ];

    private const FECHAS_CENTINELA = ['1899-12-30', '1900-01-01', '0000-00-00'];

    private const NOMBRE_PLACEHOLDER = 'NO DEFINIDO';

    private const TAMANO_LOTE_INSERTAR = 300;

    private const TAMANO_LOTE_RELLENAR = 500;

    /** @var array<string,array{tipo:string,longitud:?int}>|null */
    private ?array $columnasComparables = null;

    public function leerExcel(string $rutaArchivo): array
    {
        $tipo = IOFactory::identify($rutaArchivo);
        $reader = IOFactory::createReader($tipo);
        $reader->setReadDataOnly(true);
        $spreadsheet = $reader->load($rutaArchivo);

        [$sheet, $filaEncabezado, $columnas] = $this->ubicarHojaDeDatos($spreadsheet);

        $todas = $sheet->rangeToArray(
            'A' . ($filaEncabezado + 1) . ':' . $sheet->getHighestColumn() . $sheet->getHighestRow()
        );

        // El spreadsheet completo mide ~1GB en memoria (medido directamente) — se libera antes
        // de armar el array final de filas, que ya solo necesita las columnas comparables.
        $spreadsheet->disconnectWorksheets();
        unset($spreadsheet);

        $camposComparables = $this->columnasComparables();
        $idxCodTer = $columnas['cod_ter'];
        $idxNomTer = $columnas['nom_ter'] ?? null;

        $resultado = [];
        foreach ($todas as $numeroFila => $fila) {
            $codTexto = trim((string) ($fila[$idxCodTer] ?? ''));
            if ($codTexto === '' || !preg_match('/^\d+$/', $codTexto)) {
                continue;
            }

            $nomTer = $idxNomTer !== null ? trim((string) ($fila[$idxNomTer] ?? '')) : '';
            if (mb_strtoupper($nomTer, 'UTF-8') === self::NOMBRE_PLACEHOLDER) {
                continue; // fila placeholder del export legado (cod_ter=1)
            }

            $datosCrudos = [];
            foreach ($camposComparables as $columna => $meta) {
                if (isset($columnas[$columna])) {
                    $datosCrudos[$columna] = trim((string) ($fila[$columnas[$columna]] ?? ''));
                }
            }

            $resultado[] = [
                'fila_excel' => $filaEncabezado + $numeroFila + 1,
                'cod_ter' => (string) (int) $codTexto,
                'nom_ter' => $nomTer,
                'datos_crudos' => $datosCrudos,
            ];
        }

        return $resultado;
    }

    /**
     * Recorre las hojas del archivo y usa la primera fila (de las primeras ~15 de cada hoja)
     * donde aparezca una columna "cod_ter" real — esa es la fila de encabezados.
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

        throw new \RuntimeException('No se encontró una fila de encabezados reconocible (se esperaba una columna "cod_ter") en ninguna hoja del archivo.');
    }

    /** @return array<string,int> nombre real de columna de MaeTerceros => índice de columna Excel (base 0) */
    private function mapearColumnas(array $encabezados): array
    {
        $columnasDb = array_flip(Schema::getColumnListing(self::TABLA));
        $columnas = [];
        foreach ($encabezados as $idx => $texto) {
            $nombre = trim((string) $texto);
            if ($nombre === '' || !isset($columnasDb[$nombre])) {
                continue;
            }
            if (!isset($columnas[$nombre])) {
                $columnas[$nombre] = $idx;
            }
        }

        return $columnas;
    }

    /** @return array<string,array{tipo:string,longitud:?int}> columna comparable => metadata */
    private function columnasComparables(): array
    {
        if ($this->columnasComparables !== null) {
            return $this->columnasComparables;
        }

        $meta = DB::select(
            'SELECT COLUMN_NAME, DATA_TYPE, CHARACTER_MAXIMUM_LENGTH FROM information_schema.columns
             WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? ORDER BY ORDINAL_POSITION',
            [self::TABLA]
        );

        $columnas = [];
        foreach ($meta as $col) {
            if (in_array($col->COLUMN_NAME, self::CAMPOS_EXCLUIDOS, true)) {
                continue;
            }
            $columnas[$col->COLUMN_NAME] = [
                'tipo' => $col->DATA_TYPE,
                'longitud' => $col->CHARACTER_MAXIMUM_LENGTH !== null ? (int) $col->CHARACTER_MAXIMUM_LENGTH : null,
            ];
        }

        return $this->columnasComparables = $columnas;
    }

    /**
     * Encabezados para la plantilla descargable: cod_ter/nom_ter primero (identifican la fila),
     * seguidos de las columnas comparables en el mismo orden en que existen en MaeTerceros.
     *
     * @return string[]
     */
    public function columnasPlantilla(): array
    {
        // nom_ter ya es parte de las columnas comparables (se puede rellenar como cualquier
        // otra) — array_unique evita que aparezca duplicada por venir también en el prefijo fijo.
        return array_values(array_unique(['cod_ter', 'nom_ter', ...array_keys($this->columnasComparables())]));
    }

    public function analizar(array $filas): array
    {
        $camposComparables = $this->columnasComparables();

        $existentes = collect();
        foreach (array_chunk(array_column($filas, 'cod_ter'), 2000) as $grupo) {
            $existentes = $existentes->merge(
                DB::table(self::TABLA)
                    ->whereIn('cod_ter', $grupo)
                    ->get(['cod_ter', ...array_keys($camposComparables)])
            );
        }
        $existentes = $existentes->keyBy(fn ($t) => (string) $t->cod_ter);

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

            $actual = $existentes[$fila['cod_ter']] ?? null;

            if ($actual !== null) {
                $campos = $this->camposParaRellenar($fila['datos_crudos'], $actual, $camposComparables);
                if ($campos) {
                    $enriquecer[] = ['cod_ter' => $fila['cod_ter'], 'nombre' => $fila['nom_ter'], 'campos' => $campos];
                } else {
                    $yaExistenSinCambios++;
                }
                continue;
            }

            $datos = ['cod_ter' => $fila['cod_ter']];
            foreach ($camposComparables as $columna => $metaColumna) {
                $valor = $this->normalizarValor($columna, $fila['datos_crudos'][$columna] ?? '', $metaColumna);
                if ($valor !== null) {
                    $datos[$columna] = $valor;
                }
            }
            if (!isset($datos['nom_ter'])) {
                $datos['nom_ter'] = $fila['nom_ter'] !== '' ? $fila['nom_ter'] : $fila['cod_ter'];
            }

            $nuevos[] = ['cod_ter' => $fila['cod_ter'], 'nombre' => $fila['nom_ter'], 'datos' => $datos];
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
            foreach (array_chunk($analisis['nuevos'], self::TAMANO_LOTE_INSERTAR) as $lote) {
                DB::table(self::TABLA)->insert(array_column($lote, 'datos'));
            }

            $this->rellenarCampos($analisis['enriquecer']);
        });

        return [
            'insertados' => count($analisis['nuevos']),
            'enriquecidos' => count($analisis['enriquecer']),
            'ya_existian' => $analisis['ya_existen_count'],
            'excepciones' => count($analisis['excepciones']),
        ];
    }

    /** Campos que hoy están vacíos en MaeTerceros y el Excel sí trae — nunca sobrescribe. */
    private function camposParaRellenar(array $crudos, object $actual, array $camposComparables): array
    {
        $campos = [];
        foreach ($camposComparables as $columna => $metaColumna) {
            if (!$this->esVacioEnBd($actual->$columna ?? null, $columna)) {
                continue;
            }
            $valor = $this->normalizarValor($columna, $crudos[$columna] ?? '', $metaColumna);
            if ($valor !== null) {
                $campos[$columna] = $valor;
            }
        }

        return $campos;
    }

    /**
     * UPDATE ... CASE cod_ter WHEN ... END, una columna a la vez, con el WHERE IN restringido
     * exactamente a los cod_ter que necesitan ESA columna (sin ELSE).
     *
     * Antes esto agrupaba varias columnas en un solo UPDATE por lote de registros, con
     * "ELSE columna" para las filas que no necesitaban ese campo — pero esta tabla legada tiene
     * fechas inválidas preexistentes ('0000-00-00') dispersas en varias columnas datetime, y bajo
     * modo estricto MySQL revalida el valor de CADA columna para CADA fila del WHERE al hacer el
     * UPDATE, incluso cuando el ELSE solo reescribe el valor que ya tenía. Eso rompía el lote
     * completo por una fecha mala en una columna que ni siquiera se estaba tocando para esa fila.
     * Al ir columna por columna y limitar el WHERE a los cod_ter con un WHEN real, nunca se
     * reescribe (ni se revalida) una columna que no se está rellenando.
     */
    private function rellenarCampos(array $enriquecer): void
    {
        $porColumna = [];
        foreach ($enriquecer as $item) {
            foreach ($item['campos'] as $columna => $valor) {
                $porColumna[$columna][$item['cod_ter']] = $valor;
            }
        }

        foreach ($porColumna as $columna => $valores) {
            foreach (array_chunk($valores, self::TAMANO_LOTE_RELLENAR, true) as $grupo) {
                $case = "{$columna} = CASE cod_ter ";
                $bindings = [];
                foreach ($grupo as $codTer => $valor) {
                    $case .= 'WHEN ? THEN ? ';
                    $bindings[] = $codTer;
                    $bindings[] = $valor;
                }
                $case .= 'END';

                $codTers = array_keys($grupo);
                $placeholders = implode(',', array_fill(0, count($codTers), '?'));

                DB::update(
                    'UPDATE ' . self::TABLA . " SET {$case} WHERE cod_ter IN ({$placeholders})",
                    [...$bindings, ...$codTers]
                );
            }
        }
    }

    /** @param array{tipo:string,longitud:?int} $metaColumna */
    private function esVacioEnBd($valor, string $columna): bool
    {
        if ($valor === null) {
            return true;
        }
        $texto = trim((string) $valor);
        if ($texto === '') {
            return true;
        }
        if (in_array($columna, self::COLUMNAS_FECHA, true) && in_array(substr($texto, 0, 10), self::FECHAS_CENTINELA, true)) {
            return true;
        }

        return false;
    }

    /** @param array{tipo:string,longitud:?int} $metaColumna */
    private function normalizarValor(string $columna, string $crudo, array $metaColumna): ?string
    {
        $texto = trim($crudo);
        if ($texto === '') {
            return null;
        }

        if (in_array($columna, self::COLUMNAS_FECHA, true)) {
            return $this->parsearFechaGenerica($texto);
        }

        if ($metaColumna['longitud'] !== null) {
            return mb_substr($texto, 0, $metaColumna['longitud']);
        }

        return $texto;
    }

    /**
     * El archivo mezcla, columna por columna, fechas ya formateadas ("Y-m-d..." — así las
     * entrega el reader de XLS cuando la celda tiene formato de fecha) con números de serie de
     * Excel crudos (cuando no lo tiene, ej. fecha_aded="45436.826...") y con los centinelas de
     * "sin fecha" 1899-12-30/1900-01-01. Un solo parser cubre los tres casos.
     */
    private function parsearFechaGenerica(string $texto): ?string
    {
        if (preg_match('/^\d{4}-\d{2}-\d{2}/', $texto)) {
            $fecha = substr($texto, 0, 10);
            return in_array($fecha, self::FECHAS_CENTINELA, true) ? null : $fecha;
        }

        if (is_numeric($texto)) {
            $serial = (float) $texto;
            // Serial 0/1 son el mismo "sin fecha" que 1899-12-30/1900-01-01, solo que sin
            // formato de fecha aplicado a la celda. Aparte, excelToDateTimeObject(0) tiene un
            // caso límite conocido que devuelve el epoch Unix 1970-01-01 en vez de 1899-12-30 —
            // así que hay que cortarlo ANTES de convertir, no después.
            if ($serial <= 1) {
                return null;
            }
            try {
                $fecha = ExcelDate::excelToDateTimeObject($serial)->format('Y-m-d');
                return in_array($fecha, self::FECHAS_CENTINELA, true) ? null : $fecha;
            } catch (\Throwable) {
                return null;
            }
        }

        try {
            return Carbon::createFromFormat('d/m/Y', $texto)->toDateString();
        } catch (\Throwable) {
            return null;
        }
    }
}
