<?php

namespace App\Http\Controllers\Indicators;

use App\Http\Controllers\Controller;
use App\Models\Indicators\IndPreguntas;
use App\Models\Indicators\IndRespuestas;
use App\Models\Indicators\IndUsuarios;
use App\Models\Indicators\IndQuiz;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class QuizController extends Controller
{
    public function index()
    {
        return view('indicators.inicioquiz');
    }

    public function generarpreguntas(int $pruebaid)
    {
        $activeQuiz = IndQuiz::where('id', $pruebaid)->where('estado', 1)->exists();
        if (!$activeQuiz) {
            return view('indicators.quizTI', ['quizActivo' => false]);
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
        return view('indicators.quizTI', [
            'quizActivo' => true,
            'preguntas' => $preguntas,
            'pruebaid' => $pruebaid,
        ]);
    }

    public function store(Request $request)
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

        return view('indicators.resultadoquiz', [
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
