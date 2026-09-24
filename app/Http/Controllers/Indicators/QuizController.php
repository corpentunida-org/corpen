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
use Illuminate\Support\Facades\Auth;

class QuizController extends Controller
{
    public function quizinicio()
    {
        return view('indicators.quiz.inicioquiz');
    }

    public function index()
    {
        $lista_quizes = IndQuiz::all();

        $pruebausuarios = IndUsuarios::all();
        $respuestas = IndUsuarios::pluck('respuestas');
        $puntajesQuiz = [1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0];
        $ticcorpen = [1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0];
        $ticsoft = [1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0, 'n/a' => 0];

        foreach ($pruebausuarios as $usuario) {
            if (!empty($usuario->puntaje)) {
                $puntaje = explode('/', $usuario->puntaje)[0];
                if (is_numeric($puntaje)) {
                    $puntaje = (int) $puntaje;
                    if ($puntaje >= 1 && $puntaje <= 5) {
                        $puntajesQuiz[$puntaje]++;
                    }
                }
            }
        }

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
                } elseif (is_numeric($array[6])) {
                    $valor6 = (int) $array[6];
                    if ($valor6 >= 1 && $valor6 <= 5) {
                        $ticsoft[$valor6]++;
                    }
                }
            }
        }

        $datacharts = [
            'usuariosmaspuntaje' => IndUsuarios::whereRaw("CAST(SUBSTRING_INDEX(puntaje, '/', 1) AS UNSIGNED) > 3")->count(),
            'totalempleados' => GdoEmpleado::where('id', '>', 11)->count(),
            'puntajesQuiz' => $puntajesQuiz,
            'ticcorpen' => $ticcorpen,
            'ticsoft' => $ticsoft,
        ];

        return view('indicators.quiz.index', compact('pruebausuarios', 'datacharts', 'lista_quizes'));
    }

    public function generarpreguntas(int $pruebaid)
    {
        $activeQuiz = IndQuiz::where('id', $pruebaid)->where('estado', 1)->exists();
        if (!$activeQuiz) {
            return view('indicators.quiz.quizTI', ['quizActivo' => false]);
        }

        $idsEspeciales = [1, 2];
        $idsPreguntas = IndPreguntas::where('ref_quiz', $pruebaid)
            ->whereNotIn('id', $idsEspeciales)
            ->inRandomOrder()
            ->limit(5)
            ->pluck('id')
            ->toArray();

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

    public function create()
    {
        return view('indicators.quiz.create');
    }

    public function store(Request $request)
    {
        DB::transaction(function () use ($request) {
            $quiz = IndQuiz::create([
                'nombre' => $request->titulo,
                'total_preguntas' => count($request->preguntas),
                'estado' => 1,
                'usuario_creador' => Auth::id(),
                'area' => null,
            ]);

            foreach ($request->preguntas as $pregunta) {
                $preguntaModel = IndPreguntas::create([
                    'texto' => $pregunta['pregunta'],
                    'ref_quiz' => $quiz->id,
                ]);

                foreach ($pregunta['respuestas'] as $indice => $respuesta) {
                    IndRespuestas::create([
                        'pregunta_id' => $preguntaModel->id,
                        'texto' => $respuesta,
                        'correcta' => $indice == $pregunta['correcta'],
                    ]);
                }
            }
        });

        return redirect()->route('indicators.quizes.index')->with('success', 'Quiz creado exitosamente.');
    }

    /**
     * Método para exportar/descargar el informe en formato CSV (compatible con Excel).
     */
    public function exportarInforme()
    {
        $fileName = 'informe_quiz_usuarios_' . date('Y-m-d_H-i-s') . '.csv';

        // Obtenemos los usuarios con su respectiva información del quiz asociado si existe relación
        $usuarios = IndUsuarios::all();

        $headers = [
            "Content-type"        => "text/csv; charset=UTF-8",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $callback = function() use ($usuarios) {
            $file = fopen('php://output', 'w');

            // Añadir BOM para que Excel reconozca correctamente las tildes y caracteres especiales UTF-8
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

            // Encabezados de las columnas del archivo CSV
            fputcsv($file, ['ID', 'Correo', 'Nombre', 'Puntaje', 'Prueba ID', 'Tiempo Transcurrido', 'Fecha de Registro'], ';');

            // Recorrer los registros y escribirlos fila por fila
            foreach ($usuarios as $usuario) {
                fputcsv($file, [
                    $usuario->id,
                    $usuario->id_correo,
                    $usuario->nombre,
                    $usuario->puntaje,
                    $usuario->prueba,
                    $usuario->tiempo,
                    $usuario->fecha
                ], ';');
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
