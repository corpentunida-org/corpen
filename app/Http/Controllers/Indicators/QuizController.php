<?php

namespace App\Http\Controllers\Indicators;

use App\Http\Controllers\Controller;
use App\Models\Indicators\IndPreguntas;
use App\Models\Indicators\IndRespuestas;
use App\Models\Indicators\IndUsuarios;
use App\Models\Indicators\IndQuiz;
use App\Models\Archivo\GdoEmpleado;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class QuizController extends Controller
{
    public function quizinicio()
    {
        return view('indicators.quiz.inicioquiz');
    }
    /*
        $usuarios = IndUsuarios::whereRaw("CAST(SUBSTRING_INDEX(puntaje, '/', 1) AS UNSIGNED) > 4")->count();
                return ($usuarios / GdoEmpleado::count()) * 100;
    */
    public function index()
    {
        $pruebausuarios = IndUsuarios::all();
        $respuestas = IndUsuarios::pluck('respuestas');
        $ticcorpen = [1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0];
        $ticsoft = [1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0, 'n/a' => 0];

        foreach ($respuestas as $respuesta) {
            $array = is_string($respuesta) ? json_decode($respuesta, true) : $respuesta;
            if (isset($array[5]) && is_numeric($array[5])) {
                $valor5 = (int) $array[5];
                if ($valor5 >= 1 && $valor5 <= 5) {
                    $ticcorpen[$valor5]++;
                }
            }
            if (isset($array[6])) {
                if ($array[6] === 'n/a') {
                    $ticsoft['n/a']++;
                }
                elseif (is_numeric($array[6])) {
                    $valor6 = (int) $array[6];
                    if ($valor6 >= 1 && $valor6 <= 5) {
                        $ticsoft[$valor6]++;
                    }
                }
            }
        }

        $datacharts = [
            'usuariosmaspuntaje' => IndUsuarios::whereRaw("CAST(SUBSTRING_INDEX(puntaje, '/', 1) AS UNSIGNED) > 3")->count(),
            'totalempleados' => GdoEmpleado::count(),
            'ticcorpen' => $ticcorpen,
            'ticsoft' => $ticsoft,
        ];
        return view('indicators.quiz.index', compact('pruebausuarios', 'datacharts'));
    }

    public function generarpreguntas(int $pruebaid)
    {
        $activeQuiz = IndQuiz::where('id', $pruebaid)->where('estado', 1)->exists();
        if (!$activeQuiz) {
            return view('indicators.quiz.quizTI', ['quizActivo' => false]);
        }
        $idsEspeciales = [1, 2];
        $idsPreguntas = IndPreguntas::where('ref_quiz', $pruebaid)->whereNotIn('id', $idsEspeciales)->inRandomOrder()->limit(5)->pluck('id')->toArray();
        $idsPreguntas = array_merge($idsPreguntas, $idsEspeciales);
        $preguntas = [];

        foreach ($idsPreguntas as $idPregunta) {
            $pregunta = IndPreguntas::find($idPregunta);

            $correcta = IndRespuestas::where('pregunta_id', $idPregunta)->where('correcta', 1)->first();
            $incorrectas = IndRespuestas::where('pregunta_id', $idPregunta)->where('correcta', 0)->inRandomOrder()->limit(3)->get();
            $respuestas = collect([$correcta])
                ->merge($incorrectas)
                ->shuffle();

            $preguntas[] = [
                'pregunta' => $pregunta,
                'respuestas' => $respuestas,
                'indicador' => in_array($idPregunta, $idsEspeciales),
            ];
        }
        return view('indicators.quiz.quizTI', [
            'quizActivo' => true,
            'preguntas' => $preguntas,
            'pruebaid' => $pruebaid,
        ]);
    }

    public function storeQuiz(Request $request)
    {
        $preguntas = $request->preguntas;
        $resultado = [];
        $puntaje = 0;

        foreach ($preguntas as $item) {
            $idPregunta = $item['idpregunta'];
            $idRespuesta = $item['idrespuesta'];

            $pregunta = IndPreguntas::find($idPregunta);
            $respuestaCorrecta = IndRespuestas::where('pregunta_id', $idPregunta)->where('correcta', 1)->first();
            $respuestaUsuario = IndRespuestas::find($idRespuesta);

            if ($respuestaCorrecta) {
                $acertada = $respuestaCorrecta->id == $idRespuesta;

                if ($acertada) {
                    $puntaje++;
                }
                $resultado[] = [
                    'pregunta' => $pregunta?->texto,
                    'idrespuesta' => $idRespuesta,
                    'respuesta_usuario' => $respuestaUsuario?->texto,
                    'idrespuesta_correcta' => $respuestaCorrecta->id,
                    'respuesta_correcta' => $respuestaCorrecta->texto,
                    'acertada' => $acertada,
                ];
            }
        }

        $quizuser = IndUsuarios::create([
            'id_correo' => $request->correoUsuario,
            'nombre' => strtoupper($request->nombreUsuario),
            'preguntas' => collect($preguntas)->pluck('idpregunta')->toArray(),
            'respuestas' => collect($preguntas)->pluck('idrespuesta')->toArray(),
            'puntaje' => $puntaje . '/' . (count($preguntas) - 2),
            'fecha' => now(),
            'tiempo' => $request->tiempo_transcurrido,
            'prueba' => $request->pruebaid,
        ]);

        return view('indicators.quiz.resultadoquiz', [
            'nombre' => strtoupper($request->nombreUsuario),
            'resultado' => $resultado,
            'puntaje' => $quizuser->puntaje,
            'total' => count($preguntas),
            'fecha' => $quizuser->fecha,
        ]);
    }

    public function validar(Request $request)
    {
        $request->validate([
            'correoUsuario' => 'required|email',
        ]);

        $correo = $request->correoUsuario;
        $existe = DB::table('gdo_cargo')->where('correo_corporativo', $correo)->exists();
        $respondido = DB::table('Ind_usuarios')->where('id_correo', $correo)->where('prueba', $request->pruebaid)->exists();
        return response()->json([
            'existe' => $existe,
            'respondido' => $respondido,
        ]);
    }
}
