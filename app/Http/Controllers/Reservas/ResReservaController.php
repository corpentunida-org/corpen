<?php

namespace App\Http\Controllers\Reservas;

use App\Http\Controllers\Controller;
use App\Mail\ReservaInmueble;
use App\Models\Reserva\Res_inmueble;
use App\Models\Reserva\Res_reserva;
use App\Models\Reserva\Res_reserva_evidencia;
use Carbon\Carbon;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\AuditoriaController;

class ResReservaController extends Controller implements HasMiddleware
{
    use AuthorizesRequests;
    private function auditoria($accion)
    {
        $auditoriaController = app(AuditoriaController::class);
        $auditoriaController->create($accion, 'RESERVAS');
    }

    public static function middleware(): array
    {
        return ['auth'];
    }

    public function index()
    {
        $reservas = Res_reserva::where('user_id', auth()->user()->id)
            ->where('puntuacion_asociado', null)
            ->orderBy('fecha_inicio', 'desc')
            ->get();
        $inmuebles = Res_inmueble::where('active', 1)
            ->with('fotosrel')
            ->get()
            ->map(function ($inmueble) {
                $inmueble->fotosrel->map(function ($foto) {
                    if (Storage::disk('s3')->exists($foto->attached)) {
                        $foto->url = Storage::disk('s3')->temporaryUrl($foto->attached, now()->addMinutes(5));
                    }
                    return $foto;
                });
                return $inmueble;
            });
        return view('reserva.asociado.index', compact('reservas', 'inmuebles'));
    }

    public function createReserva($id)
    {
        $inmueble = Res_inmueble::findOrFail($id);
        /*if (!$inmueble->active) {
            return redirect()->route('reserva.reserva.index')->with('error', 'El inmueble seleccionado no está disponible para reservas en este momento.');
        }*/
        date_default_timezone_set('America/Bogota');
        $fecha = date('Y-m-d');
        $reservas = Res_reserva::where('res_inmueble_id', $id)->where('fecha_fin', '>=', $fecha)->whereIn('res_status_id', [1,2,5])->orderBy('fecha_inicio', 'asc')->get();
        return view('reserva.asociado.create', compact('inmueble', 'reservas'));
    }

    public function storeReserva(Request $request)
    {
        $request->validate([
            'fechaInicio' => 'required|date',
            'endDate' => 'required|date|after:fechaInicio',
            'description' => 'nullable|string|max:200',
        ]);

        $inmueble_id = $request->input('inmueble_id');
        $fechaInicio = $request->input('fechaInicio');
        $endDate = $request->input('endDate');
        date_default_timezone_set('America/Bogota');
        $fecha_actual = Carbon::now()->toDateString();

        if ($fechaInicio < $fecha_actual) {
            return redirect()->back()->with('error', 'No se puede procesar la reserva, ya que la fecha de inicio seleccionada es anterior a la fecha actual.');
        }

        //validar que la fecha de inicio no sea mayor a 365 dias a partir de la fecha actual
        $fechaLimite = Carbon::parse($fecha_actual)->addYear()->format('Y-m-d');
        if ($fechaInicio > $fechaLimite) {
            return redirect()->back()->with('error', 'No se puede procesar la reserva, ya que la fecha de inicio seleccionada es mayor a 365 días a partir de la fecha actual.');
        }

        //consultar si el usuario ya tiene una reserva en el ultimo año
        $reservaExistente = Res_reserva::where('user_id', auth()->user()->id)
            ->where('fecha_inicio', '>=', Carbon::parse($fechaInicio)->subYear()->format('Y-m-d')) // Fecha menor a un año antes de $fechaInicio
            ->count();

        if ($reservaExistente) {
            return redirect()->back()->with('error', 'No se puede procesar la reserva, debido a que ya que ya tiene una reserva en el último año.');
        }

        //validar que la fecha de inicio y fin no supere los 5 dias
        $fechaInicio = date_create($fechaInicio);
        $endDate = date_create($endDate);
        $diff = date_diff($fechaInicio, $endDate);
        $dias = $diff->format('%a');
        if ($dias >= 5) {
            return redirect()->back()->with('error', 'La reserva no puede ser procesada, ya que excede el límite máximo de 5 días permitidos por reserva.');
        }

        $existe_reserva = Res_reserva::where('res_inmueble_id', $inmueble_id)
            ->whereNotIn('res_status_id', [3, 4])
            ->where(function ($query) use ($fechaInicio, $endDate) {
                $query->whereBetween('fecha_inicio', [$fechaInicio, $endDate])
                    ->orWhereBetween('fecha_fin', [$fechaInicio, $endDate])
                    ->orWhere(function ($query) use ($fechaInicio, $endDate) {
                        $query->where('fecha_inicio', '<=', $fechaInicio)->where('fecha_fin', '>=', $endDate);
                    });
            })
            ->first();

        if ($existe_reserva) {
            return redirect()->back()->with('error', 'Ya existe una reserva para este inmueble en esa fecha seleccionada.');
        }

        $reservaacrear = Res_reserva::create([
            'res_inmueble_id' => $request->inmueble_id,
            'res_status_id' => 1,
            'user_id' => auth()->user()->id,
            'nid' => auth()->user()->nid,
            'fecha_solicitud' => $fecha_actual,
            'fecha_inicio' => $request->fechaInicio,
            'fecha_fin' => $request->endDate,
            'comentario_reserva' => $request->description,
            'celular' => $request->celular,
            'celular_respaldo' => $request->celulartwo,
        ]);
        $inmueble = Res_inmueble::findOrFail($inmueble_id);
        $texto = 'Hemos recibido su solicitud de reserva con ingreso el ' . $request->input('fechaInicio') . ' y salida el ' . $request->input('endDate') . '. En las próximas horas, un funcionario se comunicará con usted para validar los datos de la reserva. ¡Dios le bendiga!.';
        Mail::to(auth()->user()->email)->send(new ReservaInmueble(auth()->user()->name, $texto, 'Reserva de Inmueble ' . $inmueble->name, true, $inmueble));
        return redirect()->route('reserva.reserva.index')->with('success', 'Reserva creada con éxito');

    }

    //contabilidad confirmar reserva
    public function update(Request $request, Res_reserva $reserva)
    {
        $request->validate([
            'observacion' => 'required|string|max:1000',
        ]);
        //contabilidad confirmar reserva
        $data = [
            'revision_user_id' => auth()->id(),
            'revision_fecha' => now(),
            'revision_comentario' => $request->observacion,
        ];

        if ($request->boolean('cancelar_reserva')) {
            $data['res_status_id'] = 4;
        } else {
            $data['res_status_id'] = 2;
        }
        $reserva->update($data);

        if ($request->boolean('notificar_pastor')) {
            $texto = $request->boolean('cancelar_reserva') ? 'Lamentamos informarle que su reserva ha sido cancelada. Por favor, revise los comentarios del funcionario para más detalles.' : 'Su reserva ha sido confirmada. Por favor, revise los comentarios del funcionario para más detalles.';
            $texto .= "<br><br><strong>Comentario del funcionario:</strong><br>" . nl2br(e($request->observacion));
            Mail::to($reserva->user->email)->send(new ReservaInmueble($reserva->user->name, $texto, 'Actualización de estado de reserva', false, $reserva->res_inmueble));
        }

        $this->auditoria('Update estado de reserva a confirmado soporte de pago ID: ', $reserva->id);
        return redirect()->back()->with('success', 'Cambio de estado realizado correctamente.');
    }

    public function destroy($id)
    {
        $reserva = Res_reserva::findOrFail($id);
        $reserva->delete();

        return redirect()->route('reserva.reserva.index')->with('success', 'Reserva eliminada con éxito');
    }

    public function createSoporte($id)
    {
        $reserva = Res_reserva::findOrFail($id);
        if (!auth()->user()->hasPermission('reservas.soportes.todos') && $reserva->user_id != auth()->id()) {
            abort(403);
        }
        return view('reserva.asociado.createSoporte', compact('reserva'));
    }

    public function storeSoporte(Request $request)
    {
        $request->validate([
            'archivo' => ['required', 'file', 'max:5120'],
        ]);

        $reserva = Res_reserva::findOrFail($request->input('reserva_id'));

        $path = $request->file('archivo');
        $url = 'corpentunida/reservas/' . $reserva->id;
        $reserva->soporte_pago = Storage::disk('s3')->put($url, $path);
        $reserva->res_status_id = 5;
        $reserva->save();

        $texto = 'Hemos recibido el comprobante de pago del servicio de aseo correspondiente a su reserva. En las próximas horas, uno de nuestros funcionarios se comunicará con usted para verificar el pago y brindarle las recomendaciones necesarias para confirmar su reserva. ¡Dios le bendiga!';
        Mail::to(auth()->user()->email)->send(new ReservaInmueble(auth()->user()->name, $texto, 'Soporte pago del Aseo - Reserva', false, $reserva->res_inmueble));
        return redirect()->route('reserva.reserva.index')->with('success', 'Soporte de pago cargado con éxito');
    }

    public function indexConfirmacion()
    {
        $reservas = Res_reserva::select('id', 'res_inmueble_id', 'res_status_id', 'user_id', 'nid', 'fecha_inicio', 'fecha_fin')
            ->where('res_status_id', [2])
            ->orderBy('fecha_inicio', 'asc')
            ->with(['tercero', 'user'])
            ->get();
        return view('reserva.funcionario.indexConfirmacion', compact('reservas'));
    }

    public function showConfirmacion($id)
    {
        $reserva = Res_reserva::with(['comments.user'])->findOrFail($id);
        return view('reserva.funcionario.showConfirmacion', compact('reserva'));
    }

    /*public function notificarAjuste(Request $request)
    {
        date_default_timezone_set('America/Bogota');
        $reserva_id = $request->input('reserva_id');
        $reserva = Res_reserva::findOrFail($reserva_id);
        $reserva->res_status_id = 5;
        $reserva->revision_user_id = auth()->user()->id;
        $reserva->revision_comentario = $request->input('comentario');
        $reserva->revision_fecha = date('Y-m-d');
        $reserva->save();

        $texto = 'Estimado asociado, revisando el soporte de pago del aseo de su reserva se ha encontrado lo siguiente: ' . $request->input('comentario') . '. Por favor, revise el soporte y envíenos uno nuevo. ¡Dios le bendiga!';
        Mail::to($reserva->user->email)->send(new ReservaInmueble($reserva->user->name, $texto, 'Revisión soporte pago del Aseo'));
        return redirect()->route('reserva.inmueble.confirmacion')->with('success', 'Reserva enviada a revisión con éxito');
    }*/

    public function calificacionAsociado(Request $request)
    {
        $reserva = Res_reserva::findOrFail($request->input('reserva_id'));
        $reserva->update([
            'retroalimentacion' => $request->input('comentario'),
            'fecha_retroalimentacion' => now()->toDateString(),
            'puntuacion_asociado' => (int) $request->calificacion,
        ]);
        return redirect()->back()->with('success', 'Gracias por calificar tu estadía. Tu opinión es muy importante para nosotros y nos ayuda a mejorar nuestro servicio. ¡Dios te bendiga!');
    }

    public function confirmar(Request $request)
    {
        $reserva = Res_reserva::findOrFail($request->input('reserva_id'));
        Res_reserva_evidencia::create([
            'res_reserva_id' => $reserva->id,
            'description' => $request->input('comentario'),
            'user_id' => auth()->user()->id,
        ]);
        if ($request->filled('calificacion')) {
            $reserva->update([
                'res_status_id' => 3,
                'puntuacion_admin' => (int) $request->calificacion,
                'observacion_recibido' => $request->input('comentario'),
                'fecha_recibido' => now()->toDateString(),
                'user_id_recibido' => auth()->user()->id,
            ]);
            $texto = 'Queremos agradecerle por haber elegido nuestro ' . $reserva->res_inmueble->name . ' Ha sido un placer recibirle y esperamos que haya disfrutado de su tiempo y que su experiencia haya sido cómoda y agradable. <br> Su opinión es muy importante para nosotros, ya que nos ayuda a seguir mejorando nuestro servicio. Le invitamos cordialmente a dejar una reseña sobre su estadía dentro de la <a href="https://app.corpentunida.org.co" target="_blank">app.corpentunida.org.co</a>. ¡Dios le bendiga!';
            $titulomail = 'Califica tu estadía';
            $condicionesmail = false;
        } else {
            $texto = 'Felicitaciones su reserva fue confirmada ingresando el día ' . $reserva->fecha_inicio . ' y saliendo el día: ' . $reserva->fecha_fin . ', por favor tener en cuenta las siguientes recomendaciones: ' . $request->input('comentario') . '. ¡Dios le bendiga!';
            $titulomail = 'Confirmación de la reserva';
            $condicionesmail = true;
        }
        if ($request->boolean('notificar')) {
            Mail::to($reserva->user->email)->send(new ReservaInmueble($reserva->user->name, $texto, $titulomail, $condicionesmail, $reserva->res_inmueble));
        }
        return redirect()->route('reserva.inmueble.confirmacion')->with('success', 'Comentario registrado con éxito.');
    }

    public function indexHistorico()
    {
        $historicosres = Res_reserva::select('id', 'res_inmueble_id', 'res_status_id', 'user_id', 'nid', 'fecha_inicio', 'fecha_fin')
            ->with(['tercero:nom_ter'])
            ->orderby('fecha_solicitud', 'desc')
            ->get();
        return view('reserva.funcionario.historico', compact('historicosres'));
    }

    public function reservaspagos()
    { 
        $reservas = Res_reserva::where('res_status_id', 5)
            ->whereNotNull('soporte_pago')
            ->with(['user', 'res_inmueble'])
            ->get();

        $reservascon = Res_reserva::where('res_status_id', 2)
            ->whereNotNull('soporte_pago')
            ->with(['user', 'res_inmueble'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('reserva.funcionario.respagos', compact('reservas', 'reservascon'));
    }

    public function indexCartera()
    {
        $fecha = date('Y-m-d');
        $reservas = Res_reserva::where('res_status_id', 1)
        ->where('fecha_fin', '>=', $fecha)->orderBy('fecha_inicio', 'asc')->with(['user', 'res_inmueble'])->get();
        return view('reserva.funcionario.rescartera', compact('reservas'));
    }
}
