<?php

namespace App\Http\Controllers;

use App\Mail\ReservaInmueble;
use App\Models\Reserva\Res_inmueble;
use App\Models\Reserva\Res_reserva;
use Carbon\Carbon;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class ResReservaController extends Controller  implements HasMiddleware
{
    use AuthorizesRequests;
    //
    public static function middleware(): array
    {
        return [
            'auth',
        ];
    }

    public function index()
    {
        $reservas = Res_reserva::where('user_id', auth()->user()->id)
            ->orderBy('fecha_inicio', 'desc')
            ->get();
        $inmuebles = Res_inmueble::where('active', 1)
            ->orderBy('name', 'asc')
            ->get();
        return view('reserva.asociado.index', compact('reservas', 'inmuebles'));
    }

    public function createReserva ( $id )
    {
        $inmueble = Res_inmueble::findOrFail($id);
        date_default_timezone_set('America/Bogota');
        $fecha = date('Y-m-d');
        $reservas = Res_reserva::where('res_inmueble_id', $id)
            ->where('fecha_inicio', '>=', $fecha)
            ->orderBy('fecha_inicio', 'asc')
            ->get();
        return view('reserva.asociado.create', compact('inmueble', 'reservas'));
    }

    public function storeReserva (Request $request)
    {
        $request->validate([
            'fechaInicio' => ['required', 'date'],
            'endDate' => ['required', 'date'],
        ]);

        $inmueble_id = $request->input('inmueble_id');
        $fechaInicio = $request->input('fechaInicio');
        $endDate = $request->input('endDate');
        date_default_timezone_set('America/Bogota');
        $fecha_actual = date('Y-m-d');

        if($fechaInicio < $fecha_actual) {
            return redirect()->back()->with('error', 'No se puede procesar la reserva, ya que la fecha de inicio seleccionada es anterior a la fecha actual.');
        }

        //validar que la fecha de inicio no sea mayor a 365 dias a partir de la fecha actual
        $fechaLimite = Carbon::parse($fecha_actual)->addYear()->format('Y-m-d');
        if($fechaInicio > $fechaLimite) {
            return redirect()->back()->with('error', 'No se puede procesar la reserva, ya que la fecha de inicio seleccionada es mayor a 365 días a partir de la fecha actual.');
        }

        //echo Carbon::parse($fechaInicio)->subYear()->format('Y-m-d');

        //consultar si el usuario ya tiene una reserva en el ultimo año 2025-04-22 <= 2025-03-27
        $reservaExistente = Res_reserva::where('user_id', auth()->user()->id)
            ->where('fecha_inicio', '>=', Carbon::parse($fechaInicio)->subYear()->format('Y-m-d')) // Fecha menor a un año antes de $fechaInicio
            ->where('res_inmueble_id', $inmueble_id)
            ->count();

        if($reservaExistente) {
            return redirect()->back()->with('error', 'No se puede procesar la reserva, ya que ya tiene una reserva en el último año.');
        }

        //validar que la fecha de inicio y fin no supere los 5 dias
        $fechaInicio = date_create($fechaInicio);
        $endDate = date_create($endDate);
        $diff = date_diff($fechaInicio, $endDate);
        $dias = $diff->format('%a');
        if($dias >= 5) {
            return redirect()->back()->with('error', 'La reserva no puede ser procesada, ya que excede el límite máximo de 5 días permitidos por reserva.');
        }

        $existe_reserva = Res_reserva::where('res_inmueble_id', $inmueble_id)
            ->where(function ($query) use ($fechaInicio, $endDate) {
                $query->whereBetween('fecha_inicio', [$fechaInicio, $endDate])
                    ->orWhereBetween('fecha_fin', [$fechaInicio, $endDate])
                    ->orWhere(function ($query) use ($fechaInicio, $endDate) {
                        $query->where('fecha_inicio', '<=', $fechaInicio)
                            ->where('fecha_fin', '>=', $endDate);
                    });
            })
            ->first();

        if($existe_reserva) {
            return redirect()->back()->with('error', 'Ya existe una reserva para este inmueble en estas fechas.');
        }

        //fecha  fecha de inicio el dia anterior
        $fechaAnt = Carbon::parse($fechaInicio)->subDay()->format('Y-m-d');
        $fechaDespues = Carbon::parse($endDate)->addDay()->format('Y-m-d');


        $reserva = new Res_reserva();
        $reserva->res_inmueble_id = $inmueble_id;
        $reserva->res_status_id = 1;
        $reserva->user_id = auth()->user()->id;
        $reserva->nid  = auth()->user()->nid;
        $reserva->celular = $request->input('celular');
        $reserva->fecha_solicitud = $fecha_actual;
        $reserva->fecha_inicio = $fechaInicio;
        $reserva->fecha_fin = $endDate;
        $reserva->comentario_reserva = $request->input('description');
        $reserva->save();

        $reservaAnt = new Res_reserva();
        $reservaAnt->res_inmueble_id = $inmueble_id;
        $reservaAnt->res_status_id = 1;
        $reservaAnt->user_id = 4;
        $reservaAnt->nid  = '0000000000';
        $reservaAnt->fecha_solicitud = $fecha_actual;
        $reservaAnt->fecha_inicio = $fechaAnt;
        $reservaAnt->fecha_fin = $fechaAnt;
        $reservaAnt->save();

        $reservaDes = new Res_reserva();
        $reservaDes->res_inmueble_id = $inmueble_id;
        $reservaDes->res_status_id = 1;
        $reservaDes->user_id = 4;
        $reservaDes->nid  = '0000000000';
        $reservaDes->fecha_solicitud = $fecha_actual;
        $reservaDes->fecha_inicio = $fechaDespues;
        $reservaDes->fecha_fin = $fechaDespues;
        $reservaDes->save();

        $reserva->reserva_antes = $reservaAnt->id;
        $reserva->reserva_despues = $reservaDes->id;
        $reserva->save();

        $texto = "Hemos recibido su solicitud de reserva con ingreso el " . $request->input('fechaInicio') .  " y salida el " . $request->input('endDate') . ". En las próximas horas, un funcionario se comunicará con usted para validar los datos de la reserva. ¡Dios le bendiga!.";
        Mail::to(auth()->user()->email)
            ->cc( 'jesdis@hotmail.com' )
            ->send(New ReservaInmueble(auth()->user()->name, $texto, 'Reserva de Inmueble'));

        return redirect()->route('reserva.index')->with('message', 'Reserva creada con éxito');
    }

    public function destroy ( $id )
    {
        $reserva = Res_reserva::findOrFail($id);
        $reserva->res_status_id = 4;
        $reserva->save();

        $reserva_antes = Res_reserva::find($reserva->reserva_antes);
        if($reserva_antes) {
            $reserva_antes->delete();
        }

        $reserva_despues = Res_reserva::find($reserva->reserva_despues);
        if($reserva_despues) {
            $reserva_despues->delete();
        }

        $reserva->delete();

        return redirect()->route('reserva.index')->with('success', 'Reserva eliminada con éxito');
    }

    public function createSoporte ( $id )
    {
        $reserva = Res_reserva::findOrFail($id);
        return view('reserva.asociado.createSoporte', compact('reserva'));
    }

    public function storeSoporte (Request $request)
    {
        $request->validate([
            'archivo' => ['required', 'file', 'max:5120'],
        ]);

        $reserva = Res_reserva::findOrFail($request->input('reserva_id'));

        $path = $request->file('archivo');
        $url = 'corpentunida/reservas/' . $reserva->id ;
        $reserva->soporte_pago = Storage::disk('s3')->put($url, $path);
        $reserva->res_status_id = 1;
        $reserva->save();

        $texto = "Hemos recibido el comprobante de pago del servicio de aseo correspondiente a su reserva. En las próximas horas, uno de nuestros funcionarios se comunicará con usted para verificar el pago y brindarle las recomendaciones necesarias para confirmar su reserva. ¡Dios le bendiga!";
        Mail::to(auth()->user()->email)
            ->cc( 'jesdis@hotmail.com' )
            ->send(New ReservaInmueble(auth()->user()->name, $texto, 'Soporte pago del Aseo - Reserva'));

        return redirect()->route('reserva.index')->with('success', 'Soporte de pago cargado con éxito');
    }

    public function indexConfirmacion()
    {
        $reservas = Res_reserva::where('res_status_id', 1)
            ->where('nid', '<>', '0000000000')
            ->orderBy('fecha_inicio', 'asc')
            ->get();

        return view('reserva.funcionario.indexConfirmacion', compact('reservas'));
    }

    public function showConfirmacion ( $id )
    {
        $reserva = Res_reserva::findOrFail($id);

        return view('reserva.funcionario.showConfirmacion', compact('reserva'));
    }

    public function notificarAjuste ( Request $request )
    {
        date_default_timezone_set('America/Bogota');
        $reserva_id = $request->input('reserva_id');
        $reserva = Res_reserva::findOrFail($reserva_id);
        $reserva->res_status_id = 5;
        $reserva->revision_user_id = auth()->user()->id;
        $reserva->revision_comentario = $request->input('comentario');
        $reserva->revision_fecha = date('Y-m-d');
        $reserva->save();

        $texto = "Estimado asociado, revisando el soporte de pago del aseo de su reserva se ha encontrado lo siguiente: " . $request->input('comentario') . ". Por favor, revise el soporte y envíenos uno nuevo. ¡Dios le bendiga!";
        Mail::to($reserva->user->email)
            ->send(New ReservaInmueble($reserva->user->name, $texto, 'Revisión oporte pago del Aseo'));

        return redirect()->route('reserva.inmueble.confirmacion')->with('success', 'Reserva enviada a revisión con éxito');
    }


    public function confirmar ( Request $request )
    {
        date_default_timezone_set('America/Bogota');
        $reserva_id = $request->input('reserva_id');
        $reserva = Res_reserva::findOrFail($reserva_id);
        $reserva->res_status_id = 2;
        $reserva->confirmar_user_id = auth()->user()->id;
        $reserva->confirmar_comentario = $request->input('comentario');
        $reserva->confirmar_fecha = date('Y-m-d');
        $reserva->save();

        $texto = "Felicitaciones su reserva fue confirmada ingresando el día " . $reserva->fecha_inicio . " y saliendo el día: " . $reserva->fecha_fin . ", por favor tener en cuenta las siguientes recomendaciones: " . $request->input('comentario') . ". ¡Dios le bendiga!";
        Mail::to($reserva->user->email)
            ->send(New ReservaInmueble($reserva->user->name, $texto, 'Confirmación de la reserva'));

        return redirect()->route('reserva.inmueble.confirmacion')->with('success', 'Reserva confirmada con éxito');
    }


}
