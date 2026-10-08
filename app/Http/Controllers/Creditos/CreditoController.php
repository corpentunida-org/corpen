<?php

namespace App\Http\Controllers\Creditos;

use App\Http\Controllers\Controller;
use App\Models\Creditos\Credito;
use App\Models\Creditos\Estado;
use App\Models\Creditos\LineaCredito;
use App\Models\Maestras\MaeTerceros; // Asegúrate que la ruta a tu modelo Tercero sea correcta
use App\Http\Requests\StoreCreditoRequest;
use App\Http\Requests\UpdateCreditoRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CreditoController extends Controller
{
    /**
     * Muestra una lista de todos los créditos.
     */

    public function index()
    {
        // 1. Empezamos la consulta con query() para poder añadirle condiciones.
        $query = Credito::query();

        // 2. Aplicamos el filtro de búsqueda si el parámetro 'nombre' existe en la URL.
        $query->when(request('nombre'), function ($q, $nombre) {
            // Usamos whereHas para buscar en la tabla relacionada 'tercero'.
            // Esto buscará créditos DONDE el tercero asociado CUMPLA esta condición.
            return $q->whereHas('tercero', function ($subQuery) use ($nombre) {
                // Buscamos coincidencias parciales en el nombre del tercero.
                $subQuery->where('nom_ter', 'like', "%{$nombre}%");
            });
        });

        // 3. Añadimos el resto de tu lógica original.
        // El filtro de estado y el Eager Loading se aplican a la consulta ya filtrada (o no).
        $creditos = $query->where('cre_estados_id', 16)->with('tercero', 'lineaCredito.tipoCredito', 'estado.etapa')->paginate(10);

        // 4. Devolvemos la vista con los créditos (filtrados o no).
        return view('creditos.creditos.index', compact('creditos'));
        /*
        $creditos = Credito::where('cre_estados_id', 16)->with('tercero','lineaCredito.tipoCredito', 'estado.etapa')->paginate(10);
        return view('creditos.creditos.index', compact('creditos')); */
    }
    /**
     * Muestra el formulario para crear un nuevo crédito.
     */
    public function create()
    {
        // El select de Cliente (Tercero) se llena por búsqueda AJAX (buscarTerceros), no
        // cargando aquí los ~26.000 registros de MaeTerceros::all() — eso era lo que hacía
        // tardar tanto en abrir este formulario.
        $estados = Estado::all();
        $lineasCredito = LineaCredito::all();

        return view('creditos.creditos.crear', compact('estados', 'lineasCredito'));
    }

    /**
     * Búsqueda AJAX de terceros para el select de Cliente del formulario de crédito (Select2).
     * Por cédula (cod_ter) o nombre (nom_ter), máximo 20 resultados por página.
     */
    public function buscarTerceros(Request $request)
    {
        $q = trim((string) $request->query('q', ''));
        $page = max(1, (int) $request->query('page', 1));
        $perPage = 20;

        $query = MaeTerceros::query();
        if ($q !== '') {
            $query->where(function ($sub) use ($q) {
                $sub->where('nom_ter', 'like', "%{$q}%")
                    ->orWhere('cod_ter', 'like', "%{$q}%");
            });
        }

        $total = $query->count();
        $terceros = $query->orderBy('nom_ter')
            ->forPage($page, $perPage)
            ->get(['cod_ter', 'nom_ter']);

        return response()->json([
            'results' => $terceros->map(fn ($t) => [
                'id' => $t->cod_ter,
                'text' => "{$t->nom_ter} ({$t->cod_ter})",
            ]),
            'pagination' => [
                'more' => ($page * $perPage) < $total,
            ],
        ]);
    }

    /**
     * Historial de crédito de un asociado por cédula: créditos activos (cre_creditos) +
     * archivo histórico del sistema anterior (solicitudes y pagarés). Primera pieza de ir
     * enlazando ambos esquemas sin fusionarlos todavía.
     */
    public function historial(Request $request)
    {
        $cedula = trim((string) $request->query('cedula', ''));
        $tercero = null;
        $creditosActuales = collect();
        $emptyPaginator = fn () => new \Illuminate\Pagination\LengthAwarePaginator([], 0, 25);
        $solicitudes = $emptyPaginator();
        $pagares = $emptyPaginator();

        if ($cedula !== '') {
            $tercero = MaeTerceros::where('cod_ter', $cedula)->first(['cod_ter', 'nom_ter']);

            $creditosActuales = Credito::where('mae_terceros_cod_ter', $cedula)
                ->with(['estado', 'lineaCredito'])
                ->orderByDesc('fecha_desembolso')
                ->get();

            // Paginados por separado (pageName distinto cada uno): algunas cédulas no son un
            // pastor individual sino una entidad (ej. IPUC Nacional) con miles de solicitudes/
            // pagarés históricos asociados — listarlos todos de una sentada no es viable.
            $solicitudes = DB::table('cre_legacy_solicitudes')
                ->where('doc_deu', $cedula)
                ->orderByDesc('fecha')
                ->paginate(25, ['*'], 'pagina_solicitudes')
                ->withQueryString();

            $pagares = DB::table('cre_legacy_pagares')
                ->where('cod_deu', $cedula)
                ->orderByDesc('fec_apro')
                ->paginate(25, ['*'], 'pagina_pagares')
                ->withQueryString();
        }

        return view('creditos.creditos.historial', compact('cedula', 'tercero', 'creditosActuales', 'solicitudes', 'pagares'));
    }

    /**
     * Guarda el nuevo crédito en la base de datos.
     */
    public function store(StoreCreditoRequest $request)
    {
        // La validación se ejecuta automáticamente gracias al Form Request.
        Credito::create($request->validated());

        return redirect()->route('creditos.credito.index')->with('success', 'Crédito creado exitosamente.');
    }

    /**
     * Muestra los detalles de un crédito específico y todas sus relaciones.
     */
    public function show(Credito $credito)
    {
        // Cargamos todas las relaciones del crédito para mostrarlas en la vista de detalle.
        $credito->load(['estado', 'lineaCredito', 'tercero', 'pagareRelacionado', 'escritura', 'notificaciones']);

        return view('creditos.creditos.show', compact('credito'));
    }

    /**
     * Muestra el formulario para editar un crédito existente.
     */
    public function edit(Credito $credito)
    {
        $estados = Estado::all();
        $lineasCredito = LineaCredito::all();
        // Solo el tercero ya asignado a este crédito, para precargar el Select2 — el resto se
        // busca por AJAX igual que en create().
        $terceroActual = MaeTerceros::where('cod_ter', $credito->mae_terceros_cod_ter)->first(['cod_ter', 'nom_ter']);

        return view('creditos.creditos.edit', compact('credito', 'estados', 'lineasCredito', 'terceroActual'));
    }

    /**
     * Actualiza el crédito en la base de datos.
     */
    public function update(UpdateCreditoRequest $request, Credito $credito)
    {
        $credito->update($request->validated());

        return redirect()->route('creditos.credito.index')->with('success', 'Crédito actualizado exitosamente.');
    }

    /**
     * Elimina un crédito de la base de datos.
     */
    public function destroy(Credito $credito)
    {
        $credito->delete();

        return redirect()->route('creditos.credito.index')->with('success', 'Crédito eliminado exitosamente.');
    }
}
