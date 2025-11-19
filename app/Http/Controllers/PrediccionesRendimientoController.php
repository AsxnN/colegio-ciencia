<?php

namespace App\Http\Controllers;

use App\Models\PrediccionRendimiento;
use App\Models\Estudiante;
use App\Models\Seccion;
use App\Models\Curso;
use App\Models\PrediccionCurso;
use App\Models\RecursoEducativo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Schema;


class PrediccionesRendimientoController extends Controller
{
    public function estudiantePrediccion()
    {
        try {
            $user = Auth::user();
            $estudiante = $user->estudiante;

            if (!$estudiante) {
                return view('colegio.estudiante.prediccion', [
                    'mensaje' => 'No se encontró información del estudiante.',
                    'prediccionesPorCurso' => collect(),
                    'historial_predicciones' => collect(),
                    'recomendacionesPersonalizadas' => collect(),
                    'cursosDelEstudiante' => collect(),
                    'recursosEducativos' => collect(),
                    'estudiante' => null
                ]);
            }

            // ✅ OBTENER PREDICCIONES POR CURSO
            $prediccionesPorCurso = PrediccionCurso::where('estudiante_id', $estudiante->id)
                ->with(['curso'])
                ->where('fecha_prediccion', '>=', now()->subDays(30))
                ->orderBy('fecha_prediccion', 'desc')
                ->get()
                ->unique('curso_id')
                ->values(); // Importante: convertir a collection indexada numericamente
            // ✅ OBTENER TODOS LOS CURSOS DEL ESTUDIANTE
            $cursosDelEstudiante = $estudiante->notas()
                ->with(['curso'])
                ->select('curso_id')
                ->distinct()
                ->get()
                ->pluck('curso')
                ->filter()
                ->values();

            // ✅ GENERAR RECOMENDACIONES PERSONALIZADAS
            $recomendacionesPersonalizadas = collect();
            foreach ($prediccionesPorCurso as $prediccion) {
                if ($prediccion->probabilidad_aprobar_curso < 75) {
                    $recomendacionesPersonalizadas->push([
                        'tipo' => 'urgente',
                        'titulo' => $prediccion->probabilidad_aprobar_curso < 60 ?
                            "🚨 Atención Urgente: {$prediccion->curso->nombre}" :
                            "⚠️ Necesita Mejorar: {$prediccion->curso->nombre}",
                        'contenido' => "Probabilidad actual de aprobar: {$prediccion->probabilidad_aprobar_curso}%. " .
                            ($prediccion->probabilidad_aprobar_curso < 60 ? 'Requiere acción inmediata.' : 'Hay margen para mejorar.'),
                        'prioridad' => $prediccion->probabilidad_aprobar_curso < 60 ? 'muy_alta' : 'media',
                        'curso' => $prediccion->curso->nombre,
                        'acciones' => $prediccion->recomendaciones_curso ?? []
                    ]);
                }
            }

            // ✅ RECURSOS EDUCATIVOS ESPECÍFICOS
            $recursosEducativos = RecursoEducativo::orderBy('prioridad', 'desc')->limit(6)->get();

            // Historial de predicciones generales
            $historial_predicciones = collect();
            if (Schema::hasTable('predicciones_rendimiento')) {
                $historial_predicciones = PrediccionRendimiento::where('estudiante_id', $estudiante->id)
                    ->orderBy('created_at', 'desc')
                    ->limit(10)
                    ->get();
            }

            // ✅ LOG PARA DEBUG (temporal)
            Log::info('Vista de predicción cargada', [
                'estudiante_id' => $estudiante->id,
                'predicciones_count' => $prediccionesPorCurso->count(),
                'cursos_count' => $cursosDelEstudiante->count(),
                'recomendaciones_count' => $recomendacionesPersonalizadas->count(),
                'recursos_count' => $recursosEducativos->count()
            ]);

            return view('colegio.estudiante.prediccion', compact(
                'estudiante',
                'prediccionesPorCurso',
                'historial_predicciones',
                'recomendacionesPersonalizadas',
                'recursosEducativos',
                'cursosDelEstudiante'
            ));
        } catch (\Exception $e) {
            Log::error('Error en estudiantePrediccion', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'user_id' => Auth::id()
            ]);

            return view('colegio.estudiante.prediccion', [
                'mensaje' => 'Error al cargar las predicciones: ' . $e->getMessage(),
                'prediccionesPorCurso' => collect(),
                'historial_predicciones' => collect(),
                'recomendacionesPersonalizadas' => collect(),
                'cursosDelEstudiante' => collect(),
                'recursosEducativos' => collect(),
                'estudiante' => $user->estudiante ?? null
            ]);
        }
    }

    // ✅ MÉTODO PARA OBTENER PREDICCIONES POR CURSO
    private function obtenerPrediccionesPorCurso($estudiante)
    {
        return $estudiante->prediccionesCurso()
            ->with(['curso'])
            ->where('fecha_prediccion', '>=', now()->subDays(30)) // Últimas 30 días
            ->orderBy('fecha_prediccion', 'desc')
            ->get()
            ->groupBy('curso_id')
            ->map(function ($prediccionesCurso) {
                return $prediccionesCurso->first(); // La más reciente
            })
            ->values();
    }

    // ✅ OBTENER CURSOS DEL ESTUDIANTE
    private function obtenerCursosEstudiante($estudiante)
    {
        return $estudiante->notas()
            ->with(['curso'])
            ->select('curso_id')
            ->distinct()
            ->get()
            ->pluck('curso')
            ->filter(); // Remover nulls
    }

    // ✅ GENERAR RECOMENDACIONES PERSONALIZADAS
    private function generarRecomendacionesPersonalizadas($prediccionesPorCurso, $estudiante)
    {
        $recomendaciones = collect();

        foreach ($prediccionesPorCurso as $prediccion) {
            $curso = $prediccion->curso;
            $estado = $prediccion->estado_curso;

            if ($estado === 'En Riesgo') {
                $recomendaciones->push([
                    'tipo' => 'urgente',
                    'curso' => $curso->nombre,
                    'titulo' => "🚨 Atención Urgente: {$curso->nombre}",
                    'contenido' => "Tu predicción en {$curso->nombre} es {$prediccion->nota_predicha_final}. " .
                        "Probabilidad de aprobar: {$prediccion->probabilidad_aprobar_curso}%. " .
                        "Requiere intervención inmediata.",
                    'prioridad' => 'muy_alta',
                    'acciones' => $prediccion->recomendaciones_curso ?? [
                        'Solicitar tutoría urgente',
                        'Dedicar 2+ horas diarias a esta materia',
                        'Formar grupo de estudio',
                        'Consultar con padres/tutores'
                    ]
                ]);
            } elseif ($estado === 'Regular') {
                $recomendaciones->push([
                    'tipo' => 'mejora',
                    'curso' => $curso->nombre,
                    'titulo' => "📈 Oportunidad de Mejora: {$curso->nombre}",
                    'contenido' => "Tienes potencial para mejorar en {$curso->nombre}. " .
                        "Predicción actual: {$prediccion->nota_predicha_final}",
                    'prioridad' => 'media',
                    'acciones' => $prediccion->recomendaciones_curso ?? [
                        'Incrementar tiempo de estudio',
                        'Practicar ejercicios adicionales',
                        'Participar más en clases'
                    ]
                ]);
            } else {
                $recomendaciones->push([
                    'tipo' => 'mantenimiento',
                    'curso' => $curso->nombre,
                    'titulo' => "✅ Excelente en {$curso->nombre}",
                    'contenido' => "¡Felicitaciones! Mantienes un excelente nivel. Considera ayudar a compañeros.",
                    'prioridad' => 'baja',
                    'acciones' => ['Mantener rutina', 'Ser tutor de compañeros']
                ]);
            }
        }

        return $recomendaciones;
    }

    // ✅ OBTENER RECURSOS ESPECÍFICOS
    private function obtenerRecursosEspecificos($prediccionesPorCurso)
    {
        $cursosEnRiesgo = $prediccionesPorCurso
            ->where('estado_curso', 'En Riesgo')
            ->pluck('curso_id');

        if ($cursosEnRiesgo->isEmpty()) {
            // Si no hay cursos en riesgo, mostrar recursos generales
            return \App\Models\RecursoEducativo::whereNull('curso_id')
                ->orderBy('created_at', 'desc')
                ->limit(6)
                ->get();
        }

        return \App\Models\RecursoEducativo::whereIn('curso_id', $cursosEnRiesgo)
            ->orWhereNull('curso_id')
            ->orderByRaw('CASE WHEN curso_id IS NOT NULL THEN 1 ELSE 2 END')
            ->limit(10)
            ->get();
    }


    public function estudianteGenerar(Request $request)
    {
        // Pre-procesar input y mapear Student_ID si el frontend envía user_id en lugar de estudiante_id
        $input = $request->all();

        // Si viene Student_ID pero no existe como estudiante.id, intentar mapear por usuario_id
        if (!empty($input['Student_ID'])) {
            $maybeEst = Estudiante::find($input['Student_ID']);
            if (!$maybeEst) {
                // intentar interpretar como user id
                $estByUser = Estudiante::where('usuario_id', $input['Student_ID'])->first();
                if ($estByUser) {
                    $input['Student_ID'] = $estByUser->id;
                }
            }
        }

        // ✅ NUEVA VALIDACIÓN PARA TODOS LOS CAMPOS DEL MODELO
        $validator = Validator::make($input, [
            'Student_ID' => 'nullable', // Opcional para compatibilidad

            // ✅ CAMPOS REQUERIDOS POR EL MODELO TENSORFLOW
            'Horas_Estudio' => 'required|numeric|min:0|max:12',
            'Horas_Sueno' => 'required|numeric|min:4|max:12',
            'Actividad_Fisica' => 'required|numeric|min:0|max:7',
            'Padres_Divorciados' => 'required|numeric|in:0,1',
            'Horas_Estudio_Semanal' => 'required|numeric|min:0|max:100',
            'Participacion_Clases' => 'required|numeric|min:0|max:10',
            'Internet_Casa' => 'required|numeric|in:0,1',
            'Dispositivo_Propio' => 'required|numeric|in:0,1',
            'Promedio_Anterior' => 'required|numeric|min:0|max:20',
            'Faltas' => 'required|numeric|min:0|max:50',
            'Nivel_Socioeconomico' => 'required|numeric|in:1,2,3',
            'Motivacion' => 'required|numeric|in:1,2,3',
            'Vive_Con' => 'required|numeric|in:1,2,3,4',
        ]);

        if ($validator->fails()) {
            Log::warning('Validación fallida en estudianteGenerar', [
                'errors' => $validator->errors()->all(),
                'input' => $input
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Datos inválidos',
                'details' => $validator->errors()->all()
            ], 422);
        }

        $validated = $validator->validated();

        Log::info('Datos enviados a Flask para predicción', $validated);


        // ✅ AGREGAR STUDENT_ID SI NO VIENE (usar usuario actual)
        if (empty($validated['Student_ID'])) {
            $estudiante = Auth::user()->estudiante ?? Estudiante::first();
            if ($estudiante) {
                $validated['Student_ID'] = $estudiante->id;
            }
        }

        try {
            $flaskUrl = env('FLASK_URL', 'http://127.0.0.1:5000');

            Log::info('Enviando petición al servicio Flask', [
                'url' => "{$flaskUrl}/predict",
                'payload' => $validated
            ]);

            // ✅ ENVIAR TODOS LOS CAMPOS AL SERVICIO FLASK
            $response = Http::withHeaders([
                'Accept' => 'application/json',
                'Content-Type' => 'application/json'
            ])->timeout(30)->post("{$flaskUrl}/predict", $validated);



            Log::info('Respuesta del servicio Flask', [
                'status' => $response->status(),
                'body_preview' => substr($response->body(), 0, 1000)
            ]);



            if ($response->successful()) {
                $result = $response->json();


                // ✅ OBTENER ESTUDIANTE PARA GUARDAR PREDICCIÓN
                $estudiante = null;
                if (!empty($validated['Student_ID'])) {
                    $estudiante = Estudiante::find($validated['Student_ID']);
                }

                if (!$estudiante) {
                    $estudiante = Auth::user()->estudiante ?? Estudiante::first();
                }

                // ✅ GUARDAR PREDICCIÓN CON DATOS EXTENDIDOS
                if ($estudiante) {
                    PrediccionRendimiento::create([
                        'estudiante_id' => $estudiante->id,
                        'probabilidad_aprobar' => $result['prediction'],
                        'prediccion_binaria' => $result['prediction'] >= 14,
                        'nivel_riesgo' => $this->determinarNivelRiesgo($result['prediction']),
                        'analisis' => 'Predicción generada con modelo neural completo',
                        'metadatos' => [
                            'generated_by' => 'student',
                            'model_type' => $result['model_type'] ?? 'tensorflow_neural',
                            'input_data' => $validated,
                            'factors' => [
                                'horas_estudio' => $validated['Horas_Estudio'],
                                'horas_sueno' => $validated['Horas_Sueno'],
                                'actividad_fisica' => $validated['Actividad_Fisica'],
                                'motivacion' => $validated['Motivacion'],
                                'recursos_tecnologicos' => [
                                    'internet' => $validated['Internet_Casa'] == 1,
                                    'dispositivo' => $validated['Dispositivo_Propio'] == 1
                                ]
                            ]
                        ]
                    ]);
                }

                $historial_predicciones = [];
                if ($estudiante) {
                    $historial_predicciones = PrediccionRendimiento::where('estudiante_id', $estudiante->id)
                        ->orderBy('created_at', 'desc')
                        ->take(10)
                        ->get();
                }

                return response()->json([
                    'success' => true,
                    'prediction' => $result['prediction'],
                    'model_type' => $result['model_type'] ?? 'tensorflow_neural',
                    'interpretacion' => $this->generarInterpretacion($result['prediction']),
                    'factors_analyzed' => [
                        'estudio' => $validated['Horas_Estudio'] . 'h diarias',
                        'sueno' => $validated['Horas_Sueno'] . 'h',
                        'actividad' => $validated['Actividad_Fisica'] . ' días/sem',
                        'motivacion' => $this->getMotivacionText($validated['Motivacion']),
                        'contexto_familiar' => $this->getContextoFamiliarText($validated),
                        'recursos' => $this->getRecursosText($validated),

                    ],
                    'historial_predicciones' => $historial_predicciones
                ]);
            }

            Log::error('Servicio Flask respondió con error', [
                'status' => $response->status(),
                'body' => $response->body()
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Error en el servicio de predicción',
                'status' => $response->status(),
                'details' => $response->body()
            ], 500);
        } catch (\Exception $e) {
            Log::error('Excepción al conectar con Flask', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Error de conexión con el servicio de predicción',
                'details' => $e->getMessage()
            ], 500);
        }
    }

    public function docenteDashboard()
    {
        // Obtener estudiantes con predicciones
        $estudiantes = Estudiante::with(['usuario', 'seccion', 'predicciones' => function ($query) {
            $query->latest()->limit(1);
        }])
            ->whereHas('notas')
            ->get()
            ->map(function ($estudiante) {
                $estudiante->ultima_prediccion = $estudiante->predicciones->first();
                $estudiante->porcentaje_asistencia = $this->calcularPorcentajeAsistencia($estudiante);
                return $estudiante;
            });

        // Estadísticas
        $estudiantes_alto_riesgo = $estudiantes->filter(function ($est) {
            return $est->ultima_prediccion && $est->ultima_prediccion->probabilidad_aprobar < 12;
        })->count();

        $estudiantes_medio_riesgo = $estudiantes->filter(function ($est) {
            return $est->ultima_prediccion &&
                $est->ultima_prediccion->probabilidad_aprobar >= 12 &&
                $est->ultima_prediccion->probabilidad_aprobar < 14;
        })->count();

        $estudiantes_bajo_riesgo = $estudiantes->filter(function ($est) {
            return $est->ultima_prediccion && $est->ultima_prediccion->probabilidad_aprobar >= 14;
        })->count();

        $promedio_general = $estudiantes->where('ultima_prediccion')->avg('ultima_prediccion.probabilidad_aprobar') ?? 0;

        $secciones = Seccion::all();
        $cursos = Curso::all();

        return view('colegio.predicciones.docente', compact(
            'estudiantes',
            'secciones',
            'cursos',
            'estudiantes_alto_riesgo',
            'estudiantes_medio_riesgo',
            'estudiantes_bajo_riesgo',
            'promedio_general'
        ));
    }

    public function exportarReporte()
    {
        // Generar reporte en PDF o Excel
        return response()->json(['message' => 'Funcionalidad en desarrollo']);
    }

    private function calcularPorcentajeAsistencia($estudiante)
    {
        $totalAsistencias = $estudiante->asistencias->count();

        if ($totalAsistencias === 0) return 0;

        $asistenciasPresentes = $estudiante->asistencias->where('presente', true)->count();

        return round(($asistenciasPresentes / $totalAsistencias) * 100, 2);
    }

    private function determinarNivelRiesgo($prediccion)
    {
        if ($prediccion < 12) return 'Alto';
        if ($prediccion < 14) return 'Medio';
        return 'Bajo';
    }

    private function generarInterpretacion($prediccion)
    {
        if ($prediccion >= 16) return "🌟 Excelente rendimiento esperado";
        if ($prediccion >= 14) return "👍 Buen rendimiento esperado";
        if ($prediccion >= 12) return "📊 Rendimiento promedio";
        return "📈 Necesita mejorar estrategias de estudio";
    }

    // ✅ NUEVAS FUNCIONES AUXILIARES PARA INTERPRETACIÓN
    private function getMotivacionText($motivacion)
    {
        switch ($motivacion) {
            case 3:
                return '😄 Alta';
            case 2:
                return '🙂 Media';
            case 1:
                return '😐 Baja';
            default:
                return '🙂 Media';
        }
    }

    private function getContextoFamiliarText($data)
    {
        $padres = $data['Padres_Divorciados'] == 1 ? 'Divorciados' : 'Unidos';

        $vive_con = '';
        switch ($data['Vive_Con']) {
            case 1:
                $vive_con = 'Ambos padres';
                break;
            case 2:
                $vive_con = 'Solo madre';
                break;
            case 3:
                $vive_con = 'Solo padre';
                break;
            case 4:
                $vive_con = 'Otros familiares';
                break;
            default:
                $vive_con = 'No especificado';
        }

        return "{$vive_con} ({$padres})";
    }

    private function getRecursosText($data)
    {
        $internet = $data['Internet_Casa'] == 1 ? '✅' : '❌';
        $dispositivo = $data['Dispositivo_Propio'] == 1 ? '✅' : '❌';

        return "Internet: {$internet} | Dispositivo: {$dispositivo}";
    }
}
