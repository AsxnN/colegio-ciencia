<?php

namespace App\Http\Controllers\Docente;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Docente;
use App\Models\Curso;
use App\Models\Estudiante;
use App\Models\Seccion;
use App\Models\PrediccionRendimiento;
use App\Models\RecomendacionIA;
use App\Models\Nota;
use App\Models\Asistencia;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $docente = $user->docente;
        
        // Obtener datos reales del docente
        $data = [
            'user' => $user,
            'alertas_criticas' => $this->getAlertasCriticas($docente),
            'alertas_detalle' => $this->getAlertasDetalle($docente),
            'total_estudiantes' => $this->getTotalEstudiantes($docente),
            'estudiantes_riesgo_alto' => $this->getEstudiantesRiesgoAlto($docente),
            'estudiantes_medio_riesgo' => $this->getEstudiantesRiesgoMedio($docente),
            'estudiantes_bajo_riesgo' => $this->getEstudiantesRiesgoBajo($docente),
            'predicciones_pendientes' => $this->getPrediccionesPendientes($docente),
            'actividades_proximas' => $this->getActividadesProximas(),
            'estudiantes_atencion' => $this->getEstudiantesQueNecesitanAtencion($docente),
            'cursos' => $this->getCursosDocente($docente),
        ];

        return view('colegio.docente.dashboard', $data);
    }

    private function getCursosDocente($docente)
    {
        return $docente->cursos()->withCount([
            'notas as estudiantes_count' => function($query) {
                $query->select(DB::raw('count(distinct estudiante_id)'));
            }
        ])->get();
    }

    public function cursos()
    {
        $user = Auth::user();
        $docente = $user->docente;
        
        // Obtener cursos reales del docente
        $cursos = Curso::where('docente_id', $docente->id)
            ->with(['notas.estudiante.usuario', 'notas.estudiante.seccion'])
            ->get()
            ->map(function ($curso) {
                // Obtener estudiantes únicos de este curso
                $estudiantes = $curso->notas->pluck('estudiante')->unique('id');
                $curso->estudiantes_count = $estudiantes->count();
                
                // Calcular niveles de riesgo
                $curso->estudiantes_riesgo = [
                    'alto' => $estudiantes->filter(function($est) { return $this->calcularNivelRiesgoReal($est) === 'Alto'; })->count(),
                    'medio' => $estudiantes->filter(function($est) { return $this->calcularNivelRiesgoReal($est) === 'Medio'; })->count(),
                    'bajo' => $estudiantes->filter(function($est) { return $this->calcularNivelRiesgoReal($est) === 'Bajo'; })->count(),
                ];
                
                // Calcular progreso basado en promedios
                $promedio = $estudiantes->avg('promedio_anterior') ?? 0;
                $curso->progreso = min(100, max(0, ($promedio / 20) * 100)); // Convertir a porcentaje
                
                return $curso;
            });

        $data = [
            'cursos' => $cursos,
            'total_cursos' => $cursos->count(),
            'cursos_riesgo_alto' => $cursos->filter(function($c) { return $c->estudiantes_riesgo['alto'] > 0; })->count(),
        ];

        return view('colegio.docente.cursos', $data);
    }

    public function estudiantes()
    {
        $user = Auth::user();
        $docente = $user->docente;
        
        // Obtener estudiantes reales de los cursos del docente
        $cursosDelDocente = Curso::where('docente_id', $docente->id)->pluck('id');
        
        $estudiantes = Estudiante::whereHas('notas', function($query) use ($cursosDelDocente) {
                $query->whereIn('curso_id', $cursosDelDocente);
            })
            ->with(['usuario', 'seccion', 'notas.curso', 'asistencias', 'predicciones'])
            ->distinct()
            ->get()
            ->map(function ($estudiante) {
                // Calcular promedio general
                $estudiante->promedio_general = $estudiante->notas->avg('calificacion') ?? $estudiante->promedio_anterior ?? 0;
                
                // Calcular porcentaje de asistencia
                $totalClases = $estudiante->asistencias->count();
                $asistenciasPresentes = $estudiante->asistencias->where('estado', 'presente')->count();
                $estudiante->porcentaje_asistencia = $totalClases > 0 ? ($asistenciasPresentes / $totalClases) * 100 : 0;
                
                // Calcular nivel de riesgo
                $estudiante->nivel_riesgo = $this->calcularNivelRiesgoReal($estudiante);
                
                // Obtener última predicción
                $ultimaPrediccion = $estudiante->predicciones->sortByDesc('created_at')->first();
                $estudiante->ultima_prediccion_fecha = $ultimaPrediccion ? $ultimaPrediccion->created_at->format('Y-m-d') : null;
                
                return $estudiante;
            });

        // Obtener todas las secciones
        $secciones = Seccion::all();

        $data = [
            'estudiantes' => $estudiantes,
            'total_estudiantes' => $estudiantes->count(),
            'estudiantes_riesgo_alto' => $estudiantes->where('nivel_riesgo', 'Alto')->count(),
            'estudiantes_riesgo_medio' => $estudiantes->where('nivel_riesgo', 'Medio')->count(),
            'estudiantes_riesgo_bajo' => $estudiantes->where('nivel_riesgo', 'Bajo')->count(),
            'secciones' => $secciones,
        ];

        return view('colegio.docente.estudiantes', $data);
    }

    public function actividades()
    {
        $user = Auth::user();
        
        // Obtener actividades del docente (si tienes modelo de actividades)
        $actividades = collect(); // Placeholder para actividades reales
        
        $data = [
            'actividades' => $actividades,
            'total_actividades' => 24, // Datos simulados
            'actividades_pendientes' => 8,
            'actividades_completadas' => 14,
            'promedio_entrega' => '85%',
        ];

        return view('colegio.docente.actividades', $data);
    }

    public function recomendaciones()
    {
        $user = Auth::user();
        $docente = $user->docente;
        
        // Obtener estudiantes con recomendaciones reales
        $cursosIds = $docente->cursos->pluck('id');
        
        $estudiantes_con_recomendaciones = Estudiante::whereHas('notas', function($query) use ($cursosIds) {
            $query->whereIn('curso_id', $cursosIds);
        })
        ->with(['usuario', 'seccion'])
        ->whereHas('recomendaciones')
        ->get();
        
        // Contar recomendaciones totales
        $totalRecomendaciones = RecomendacionIA::whereIn('estudiante_id', 
            Estudiante::whereHas('notas', function($query) use ($cursosIds) {
                $query->whereIn('curso_id', $cursosIds);
            })->pluck('id')
        )->count();
        
        $recomendacionesUrgentes = RecomendacionIA::whereIn('estudiante_id', 
            Estudiante::whereHas('notas', function($query) use ($cursosIds) {
                $query->whereIn('curso_id', $cursosIds);
            })->pluck('id')
        )->where('prioridad', 'urgente')->count();
        
        $recomendacionesCompletadas = RecomendacionIA::whereIn('estudiante_id', 
            Estudiante::whereHas('notas', function($query) use ($cursosIds) {
                $query->whereIn('curso_id', $cursosIds);
            })->pluck('id')
        )->where('estado', 'completada')->count();

        $data = [
            'estudiantes_con_recomendaciones' => $estudiantes_con_recomendaciones,
            'total_recomendaciones' => $totalRecomendaciones,
            'recomendaciones_urgentes' => $recomendacionesUrgentes,
            'recomendaciones_completadas' => $recomendacionesCompletadas,
            'efectividad' => $totalRecomendaciones > 0 ? round(($recomendacionesCompletadas / $totalRecomendaciones) * 100) . '%' : '0%',
        ];

        return view('colegio.docente.recomendaciones', $data);
    }

    public function historial()
    {
        $user = Auth::user();
        $docente = $user->docente;
        
        // Obtener estudiantes reales para historial
        $cursosIds = $docente->cursos->pluck('id');
        
        $estudiantes = Estudiante::whereHas('notas', function($query) use ($cursosIds) {
            $query->whereIn('curso_id', $cursosIds);
        })
        ->with(['usuario', 'seccion', 'notas.curso', 'asistencias'])
        ->distinct()
        ->get();

        $data = [
            'estudiantes' => $estudiantes,
        ];

        return view('colegio.docente.historial', $data);
    }

    public function comunicacion()
    {
        // Vista placeholder para comunicación
        return view('colegio.docente.comunicacion');
    }

    // Métodos auxiliares para obtener datos
    private function getAlertasCriticas($docente)
    {
        // Contar estudiantes con riesgo alto de los cursos del docente
        $cursosIds = $docente->cursos->pluck('id');
        
        $estudiantesRiesgo = Estudiante::whereHas('notas', function($query) use ($cursosIds) {
            $query->whereIn('curso_id', $cursosIds);
        })
        ->with(['notas', 'asistencias'])
        ->get()
        ->filter(function($estudiante) {
            return $this->calcularNivelRiesgoReal($estudiante) === 'Alto';
        });
        
        return $estudiantesRiesgo->count();
    }

    private function getAlertasDetalle($docente)
    {
        $cursosIds = $docente->cursos->pluck('id');
        
        $estudiantesRiesgo = Estudiante::whereHas('notas', function($query) use ($cursosIds) {
            $query->whereIn('curso_id', $cursosIds);
        })
        ->with(['usuario', 'notas', 'asistencias'])
        ->get()
        ->filter(function($estudiante) {
            return $this->calcularNivelRiesgoReal($estudiante) === 'Alto';
        })
        ->take(5) // Solo las primeras 5 alertas
        ->map(function($estudiante) {
            $promedio = $estudiante->notas->avg('calificacion') ?? $estudiante->promedio_anterior ?? 0;
            $faltas = $estudiante->faltas ?? $estudiante->asistencias->where('estado', 'ausente')->count();
            
            if ($promedio < 11) {
                return [
                    'estudiante' => $estudiante->usuario->name,
                    'tipo' => 'Rendimiento Bajo',
                    'mensaje' => "Promedio actual: {$promedio}"
                ];
            } elseif ($faltas > 10) {
                return [
                    'estudiante' => $estudiante->usuario->name,
                    'tipo' => 'Asistencia',
                    'mensaje' => "{$faltas} faltas registradas"
                ];
            }
            
            return [
                'estudiante' => $estudiante->usuario->name,
                'tipo' => 'Riesgo Alto',
                'mensaje' => 'Requiere atención'
            ];
        });
        
        return $estudiantesRiesgo->values()->toArray();
    }

    private function getTotalEstudiantes($docente)
    {
        $cursosIds = $docente->cursos->pluck('id');
        
        return Estudiante::whereHas('notas', function($query) use ($cursosIds) {
            $query->whereIn('curso_id', $cursosIds);
        })->distinct()->count();
    }

    private function getEstudiantesRiesgoAlto($docente)
    {
        return $this->getEstudiantesPorNivelRiesgo($docente, 'Alto');
    }
    
    private function getEstudiantesRiesgoMedio($docente)
    {
        return $this->getEstudiantesPorNivelRiesgo($docente, 'Medio');
    }
    
    private function getEstudiantesRiesgoBajo($docente)
    {
        return $this->getEstudiantesPorNivelRiesgo($docente, 'Bajo');
    }
    
    private function getEstudiantesPorNivelRiesgo($docente, $nivel)
    {
        $cursosIds = $docente->cursos->pluck('id');
        
        $estudiantes = Estudiante::whereHas('notas', function($query) use ($cursosIds) {
            $query->whereIn('curso_id', $cursosIds);
        })
        ->with(['notas', 'asistencias'])
        ->distinct()
        ->get();
        
        return $estudiantes->filter(function($estudiante) use ($nivel) {
            return $this->calcularNivelRiesgoReal($estudiante) === $nivel;
        })->count();
    }

    private function getPrediccionesPendientes($docente)
    {
        $cursosIds = $docente->cursos->pluck('id');
        
        // Contar estudiantes sin predicciones recientes (más de 30 días)
        $estudiantesSinPrediccion = Estudiante::whereHas('notas', function($query) use ($cursosIds) {
            $query->whereIn('curso_id', $cursosIds);
        })
        ->whereDoesntHave('predicciones', function($query) {
            $query->where('created_at', '>=', now()->subDays(30));
        })
        ->count();
        
        return $estudiantesSinPrediccion;
    }

    private function getActividadesProximas()
    {
        // Placeholder para actividades próximas
        return 3;
    }

    private function getEstudiantesQueNecesitanAtencion($docente)
    {
        $cursosIds = $docente->cursos->pluck('id');
        
        $estudiantes = Estudiante::whereHas('notas', function($query) use ($cursosIds) {
            $query->whereIn('curso_id', $cursosIds);
        })
        ->with(['usuario', 'seccion', 'predicciones', 'notas', 'asistencias'])
        ->get()
        ->filter(function($estudiante) {
            return $this->calcularNivelRiesgoReal($estudiante) === 'Alto';
        })
        ->take(5)
        ->map(function($estudiante) {
            $ultimaPrediccion = $estudiante->predicciones->sortByDesc('created_at')->first();
            
            return (object)[
                'id' => $estudiante->id,
                'codigo' => 'EST' . str_pad($estudiante->id, 3, '0', STR_PAD_LEFT),
                'usuario' => $estudiante->usuario,
                'seccion' => $estudiante->seccion,
                'ultima_prediccion' => $ultimaPrediccion ? (object)[
                    'nivel_riesgo' => 'Alto',
                    'created_at' => $ultimaPrediccion->created_at
                ] : null
            ];
        });
        
        return $estudiantes->values();
    }

    private function getEstudiantesRiesgoPorCurso($cursoId)
    {
        return ['alto' => 2, 'medio' => 5, 'bajo' => 15];
    }

    private function getProgresoCurso($cursoId)
    {
        return rand(60, 95); // Placeholder
    }

    private function calcularNivelRiesgo($estudiante)
    {
        // Método legacy - usar calcularNivelRiesgoReal
        return $this->calcularNivelRiesgoReal($estudiante);
    }
    
    private function calcularNivelRiesgoReal($estudiante)
    {
        $factoresRiesgo = 0;
        $pesoTotal = 0;
        
        // Factor 1: Promedio académico (peso: 40%)
        $promedio = $estudiante->notas->avg('calificacion') ?? $estudiante->promedio_anterior ?? 0;
        if ($promedio < 11) {
            $factoresRiesgo += 40;
        } elseif ($promedio < 14) {
            $factoresRiesgo += 20;
        }
        $pesoTotal += 40;
        
        // Factor 2: Asistencias (peso: 25%)
        $totalClases = $estudiante->asistencias->count();
        if ($totalClases > 0) {
            $porcentajeAsistencia = ($estudiante->asistencias->where('estado', 'presente')->count() / $totalClases) * 100;
            if ($porcentajeAsistencia < 70) {
                $factoresRiesgo += 25;
            } elseif ($porcentajeAsistencia < 85) {
                $factoresRiesgo += 12;
            }
        } else {
            // Si no hay datos de asistencia, usar faltas del perfil
            $faltas = $estudiante->faltas ?? 0;
            if ($faltas > 15) {
                $factoresRiesgo += 25;
            } elseif ($faltas > 8) {
                $factoresRiesgo += 12;
            }
        }
        $pesoTotal += 25;
        
        // Factor 3: Factores socioeconómicos (peso: 20%)
        if ($estudiante->nivel_socioeconomico === 'bajo') {
            $factoresRiesgo += 10;
        }
        if (!$estudiante->internet_en_casa || !$estudiante->dispositivo_propio) {
            $factoresRiesgo += 5;
        }
        if ($estudiante->padres_divorciados || $estudiante->vive_con !== 'padres') {
            $factoresRiesgo += 5;
        }
        $pesoTotal += 20;
        
        // Factor 4: Participación y motivación (peso: 15%)
        if ($estudiante->participacion_clases !== null) {
            if ($estudiante->participacion_clases < 4) {
                $factoresRiesgo += 15;
            } elseif ($estudiante->participacion_clases < 6) {
                $factoresRiesgo += 7;
            }
        }
        if ($estudiante->motivacion === 'Baja') {
            $factoresRiesgo += 5;
        }
        $pesoTotal += 15;
        
        // Calcular porcentaje de riesgo
        $porcentajeRiesgo = $pesoTotal > 0 ? ($factoresRiesgo / $pesoTotal) * 100 : 0;
        
        // Determinar nivel de riesgo
        if ($porcentajeRiesgo >= 60) {
            return 'Alto';
        } elseif ($porcentajeRiesgo >= 30) {
            return 'Medio';
        } else {
            return 'Bajo';
        }
    }

    private function getUltimaPrediccion($estudiante)
    {
        try {
            return $estudiante->prediccionesRendimiento()
                ->latest()
                ->first();
        } catch (\Throwable $e) {
            return null;
        }
    }

    // Métodos para manejar actividades (API)
    public function crearActividad(Request $request)
    {
        // Implementar creación de actividad
        return response()->json(['success' => true, 'message' => 'Actividad creada exitosamente']);
    }

    public function editarActividad(Request $request, $id)
    {
        // Implementar edición de actividad
        return response()->json(['success' => true, 'message' => 'Actividad actualizada exitosamente']);
    }

    public function eliminarActividad($id)
    {
        // Implementar eliminación de actividad
        return response()->json(['success' => true, 'message' => 'Actividad eliminada exitosamente']);
    }

    public function generarRecomendaciones(Request $request)
    {
        // Implementar generación de recomendaciones con IA
        return response()->json(['success' => true, 'message' => 'Recomendaciones generadas exitosamente']);
    }

    public function completarRecomendacion($id)
    {
        // Implementar completar recomendación
        return response()->json(['success' => true, 'message' => 'Recomendación completada']);
    }

    public function estudianteDetalle($id)
    {
        $user = Auth::user();
        $docente = $user->docente;
        
        // Verificar que el estudiante pertenece a los cursos del docente
        $cursosIds = $docente->cursos->pluck('id');
        
        $estudiante = Estudiante::whereHas('notas', function($query) use ($cursosIds) {
            $query->whereIn('curso_id', $cursosIds);
        })
        ->with(['usuario', 'seccion', 'notas.curso', 'asistencias', 'predicciones'])
        ->findOrFail($id);
        
        // Calcular información adicional
        $estudiante->nivel_riesgo = $this->calcularNivelRiesgoReal($estudiante);
        $estudiante->promedio_general = $estudiante->notas->avg('calificacion') ?? $estudiante->promedio_anterior ?? 0;
        
        $totalClases = $estudiante->asistencias->count();
        $estudiante->porcentaje_asistencia = $totalClases > 0 ? 
            ($estudiante->asistencias->where('estado', 'presente')->count() / $totalClases) * 100 : 0;
        
        return view('colegio.docente.estudiante-detalle', compact('estudiante'));
    }

    public function historialEstudiante($estudianteId)
    {
        $user = Auth::user();
        $docente = $user->docente;
        
        // Verificar que el estudiante pertenece a los cursos del docente
        $cursosIds = $docente->cursos->pluck('id');
        
        $estudiante = Estudiante::whereHas('notas', function($query) use ($cursosIds) {
            $query->whereIn('curso_id', $cursosIds);
        })
        ->with([
            'usuario', 
            'seccion', 
            'notas' => function($query) {
                $query->with('curso')->orderBy('created_at', 'desc');
            },
            'asistencias' => function($query) {
                $query->orderBy('fecha', 'desc');
            },
            'predicciones' => function($query) {
                $query->orderBy('created_at', 'desc');
            }
        ])
        ->findOrFail($estudianteId);
        
        return view('colegio.docente.historial-estudiante', compact('estudiante'));
    }

    public function estudiantePlanAccion($id)
    {
        $user = Auth::user();
        $docente = $user->docente;
        
        // Verificar que el estudiante pertenece a los cursos del docente
        $cursosIds = $docente->cursos->pluck('id');
        
        $estudiante = Estudiante::whereHas('notas', function($query) use ($cursosIds) {
            $query->whereIn('curso_id', $cursosIds);
        })
        ->with(['usuario', 'seccion', 'notas.curso', 'asistencias'])
        ->findOrFail($id);
        
        // Identificar problemas automáticamente
        $problemas = [];
        
        $promedio = $estudiante->notas->avg('calificacion') ?? $estudiante->promedio_anterior ?? 0;
        if ($promedio < 11) {
            $problemas[] = "Bajo rendimiento académico (promedio: {$promedio})";
        }
        
        $faltas = $estudiante->faltas ?? $estudiante->asistencias->where('estado', 'ausente')->count();
        if ($faltas > 10) {
            $problemas[] = "Inasistencias frecuentes ({$faltas} faltas)";
        }
        
        if ($estudiante->participacion_clases !== null && $estudiante->participacion_clases < 4) {
            $problemas[] = "Baja participación en clases";
        }
        
        if ($estudiante->motivacion === 'Baja') {
            $problemas[] = "Baja motivación académica";
        }
        
        if (!$estudiante->internet_en_casa || !$estudiante->dispositivo_propio) {
            $problemas[] = "Limitaciones de recursos tecnológicos";
        }
        
        if ($estudiante->padres_divorciados || $estudiante->vive_con !== 'padres') {
            $problemas[] = "Situación familiar compleja";
        }
        
        $estudiante->problemas_identificados = $problemas;
        $estudiante->nivel_riesgo = $this->calcularNivelRiesgoReal($estudiante);
        
        return view('colegio.docente.estudiante-plan-accion', compact('estudiante'));
    }

    public function crearPlanAccion(Request $request, $id)
    {
        // Implementar creación del plan de acción
        $request->validate([
            'objetivo' => 'required|string|max:255',
            'descripcion' => 'required|string',
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date|after:fecha_inicio',
            'actividades' => 'array'
        ]);

        // Aquí se guardaría en la base de datos
        // PlanAccion::create([...]);

        return redirect()->route('docente.estudiante.detalle', $id)
            ->with('success', 'Plan de acción creado exitosamente');
    }
}
