<?php

namespace App\Http\Controllers\Indicators;

use App\Http\Controllers\Controller;
use App\Models\Indicators\IndPreguntas;
use App\Models\Indicators\IndRespuestas;
use App\Models\Indicators\IndUsuarios;
use Illuminate\Http\Request;

class QuizController extends Controller
{
    public function index()
    {
        return view('indicators.inicioquiz');
    }

    public function generarpreguntas()
    {
        $idsPreguntas = IndPreguntas::inRandomOrder()->limit(5)->pluck('id')->toArray();
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
            ];
        }
        return view('indicators.quizTI', compact('preguntas'));
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

            $acertada = $respuestaCorrecta && $respuestaCorrecta->id == $idRespuesta;
            if ($acertada) {
                $puntaje++;
            }

            $resultado[] = [
                'pregunta' => $pregunta?->texto,
                'idrespuesta' => $idRespuesta,
                'respuesta_usuario' => $respuestaUsuario?->texto,
                'idrespuesta_correcta' => $respuestaCorrecta?->id,
                'respuesta_correcta' => $respuestaCorrecta?->texto,
                'acertada' => $acertada,
            ];
        }

        $quizuser = IndUsuarios::create([
            'id_correo' => $request->correoUsuario,
            'nombre' => strtoupper($request->nombreUsuario),
            'preguntas' => collect($preguntas)->pluck('idpregunta')->toArray(), 
            'respuestas' => collect($preguntas)->pluck('idrespuesta')->toArray(),
            'puntaje' => $puntaje . '/' . count($preguntas),
            'fecha' => now(),
        ]);

        return view('indicators.resultadoquiz', [
            'nombre' => strtoupper($request->nombreUsuario),
            'resultado' => $resultado,
            'puntaje' => $quizuser->puntaje,
            'total' => count($preguntas),
            'fecha' => $quizuser->fecha,
        ]);
    }
}