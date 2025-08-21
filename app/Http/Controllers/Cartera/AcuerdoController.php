<?php

namespace App\Http\Controllers\Cartera;

use App\Http\Controllers\Controller;
use App\Models\Cartera\Acuerdo;
use App\Models\Creditos\Credito;
use App\Enums\AcuerdoEstadoEnum;
use App\Http\Requests\StoreAcuerdoRequest; // <-- Nuestro "guardia" para crear
use App\Http\Requests\UpdateAcuerdoRequest; // <-- Nuestro "guardia" para actualizar
use Illuminate\Support\Facades\Auth; // Para obtener el usuario logueado

class AcuerdoController extends Controller
{
    /**
     * Muestra una lista paginada de todos los acuerdos.
     */
    public function index()
    {
        // Usamos with() para cargar las relaciones. Esto es una optimización
        // muy importante para evitar cientos de consultas a la base de datos.
        $acuerdos = Acuerdo::with(['credito', 'usuario'])->latest()->paginate(15);

        // Retornamos una vista (un archivo .blade.php) y le pasamos la variable 'acuerdos'.
        return view('cartera.acuerdos.index', compact('acuerdos'));
    }

    /**
     * Muestra el formulario para crear un nuevo acuerdo.
     */
    public function create()
    {
        // Necesitamos enviar a la vista la lista de créditos y los posibles estados
        // para rellenar los menús desplegables (<select>).
        $creditos = Credito::all();
        $estados = AcuerdoEstadoEnum::cases(); // ¡La magia de los Enums!

        return view('cartera.acuerdos.create', compact('creditos', 'estados'));
    }

    /**
     * Guarda el nuevo acuerdo en la base de datos.
     */
    public function store(StoreAcuerdoRequest $request)
    {
        // Laravel automáticamente ejecuta la validación de StoreAcuerdoRequest.
        // Si falla, redirige al usuario atrás con los errores.
        // Si tiene éxito, el código continúa.

        $datosValidados = $request->validated();
        
        // Añadimos el ID del usuario autenticado que está realizando la acción.
        $datosValidados['user_id'] = Auth::id();

        Acuerdo::create($datosValidados);

        return redirect()->route('acuerdos.index')->with('success', 'Acuerdo creado exitosamente.');
    }

    /**
     * Muestra los detalles de un acuerdo específico.
     */
    public function show(Acuerdo $acuerdo)
    {
        // Gracias al "Route Model Binding", Laravel busca automáticamente el
        // acuerdo por el ID de la URL. No necesitamos hacer Acuerdo::find($id).
        $acuerdo->load(['credito', 'usuario']);

        return view('cartera.acuerdos.show', compact('acuerdo'));
    }

    /**
     * Muestra el formulario para editar un acuerdo existente.
     */
    public function edit(Acuerdo $acuerdo)
    {
        $creditos = Credito::all();
        $estados = AcuerdoEstadoEnum::cases();

        return view('cartera.acuerdos.edit', compact('acuerdo', 'creditos', 'estados'));
    }

    /**
     * Actualiza el acuerdo en la base de datos.
     */
    public function update(UpdateAcuerdoRequest $request, Acuerdo $acuerdo)
    {
        // La validación de UpdateAcuerdoRequest se ejecuta automáticamente.
        $acuerdo->update($request->validated());

        return redirect()->route('acuerdos.index')->with('success', 'Acuerdo actualizado exitosamente.');
    }

    /**
     * Elimina un acuerdo de la base de datos.
     */
    public function destroy(Acuerdo $acuerdo)
    {
        $acuerdo->delete();

        return redirect()->route('acuerdos.index')->with('success', 'Acuerdo eliminado exitosamente.');
    }
}