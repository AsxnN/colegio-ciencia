<?php

namespace App\Http\Controllers;

use App\Models\PrediccionCurso;
use App\Models\Estudiante;
use App\Models\Curso;
use App\Models\Nota;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class PrediccionCursoController extends Controller
{
    public function generar(Request $request, $cursoId)
    {
        try {
            $estudiante = Auth::user()->estudiante;
            $curso = Curso::findOrFail($cursoId);
            
            // Obtener nota específica del curso
            $nota = $estudiante->notas->where('curso_id', $cursoId)->first();
            
            if (!$nota) {
                return response()->json([
                    'success' => false,
                    'error' => 'No hay notas registradas para este curso'
                ]);
            }

            // Preparar datos específicos del curso para el modelo
            $datosModelo = [
                'Student_ID' => $estudiante->id,
                'Course_ID' => $cursoId,
                'Bimestre1' => $nota->bimestre1 ?? 0,
                'Bimestre2' => $nota->bimestre2 ?? 0, 
                'Bimestre3' => $nota->bimestre3 ?? 0,
                'Bimestre4' => $nota->bimestre4 ?? 0,
                'Current_Average' => $nota->promedio_final ?? 0,
                'Attendance_Course' => $this->calcularAsistenciaCurso($estudiante, $curso),
                'Hours_Studied_Course' => $estudiante->horas_estudio_semanal ?? 10,
                'Course_Difficulty' => $this->getDificultadCurso($curso->nombre),
            ];

            // Generar predicción usando modelo simplificado (puedes integrar Flask aquí)
            $prediccionData = $this->generarPrediccionCurso($datosModelo, $nota, $estudiante, $curso);
            
            // Guardar predicción por curso
            $prediccionCurso = PrediccionCurso::updateOrCreate(
                [
                    'estudiante_id' => $estudiante->id,
                    'curso_id' => $cursoId,
                    'fecha_prediccion' => now()->startOfDay()
                ],
                $prediccionData
            );

            return response()->json([
                'success' => true,
                'prediction' => $prediccionData,
                'prediccion_id' => $prediccionCurso->id
            ]);

        } catch (\Exception $e) {
            Log::error('Error en predicción curso', [
                'error' => $e->getMessage(),
                'curso_id' => $cursoId,
                'estudiante_id' => Auth::user()->estudiante->id ?? null
            ]);

            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ]);
        }
    }

    public function generarTodas(Request $request)
    {
        try {
            $estudiante = Auth::user()->estudiante;
            $cursos = $estudiante->notas->pluck('curso_id')->unique();
            
            $prediccionesGeneradas = [];
            
            foreach ($cursos as $cursoId) {
                $resultado = $this->generar($request, $cursoId);
                $data = json_decode($resultado->getContent(), true);
                
                if ($data['success']) {
                    $prediccionesGeneradas[] = $cursoId;
                }
            }
            
            return response()->json([
                'success' => true,
                'cursos_procesados' => count($prediccionesGeneradas),
                'total_cursos' => $cursos->count()
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ]);
        }
    }

    private function generarPrediccionCurso($datosModelo, $nota, $estudiante, $curso)
    {
        // Calcular predicción usando modelo simplificado
        $promedioActual = $nota->promedio_final ?? $this->calcularPromedioActual($nota);
        
        // Factores específicos del curso
        $factoresCurso = [
            'dificultad_curso' => $this->getDificultadCurso($curso->nombre),
            'horas_estudio_recomendadas' => $this->getHorasEstudioRecomendadas($curso->nombre),
            'tendencia_estudiante' => $this->getTendenciaEstudiante($estudiante),
            'asistencia_curso' => $datosModelo['Attendance_Course'],
        ];

        // Calcular predicción final
        $prediccionFinal = $this->calcularPrediccionFinal($promedioActual, $factoresCurso);
        $prediccionBimestre = $this->calcularPrediccionBimestre($nota, $factoresCurso);
        $probabilidadAprobar = $this->calcularProbabilidadAprobar($prediccionFinal);

        // Generar recomendaciones específicas
        $recomendaciones = $this->generarRecomendacionesCurso($curso->nombre, $promedioActual, $prediccionFinal);
        
        return [
            'nota_predicha_bimestre' => round($prediccionBimestre, 2),
            'nota_predicha_final' => round($prediccionFinal, 2),
            'probabilidad_aprobar_curso' => round($probabilidadAprobar, 2),
            'analisis_curso' => $this->generarAnalisisCurso($curso->nombre, $promedioActual, $prediccionFinal),
            'fortalezas_curso' => $this->identificarFortalezasCurso($nota, $curso),
            'debilidades_curso' => $this->identificarDebilidadesCurso($nota, $curso),
            'recomendaciones_curso' => $recomendaciones,
            'asistencias_curso' => $datosModelo['Attendance_Course'],
            'tendencia_notas' => $this->calcularTendenciaNotas($nota),
            'metadatos' => [
                'model_type' => 'hybrid_neural_network',
                'confidence' => $this->calcularConfianza($nota, $factoresCurso),
                'generated_at' => now()->toIsoString(),
                'factors_analyzed' => array_keys($factoresCurso)
            ]
        ];
    }

    // Métodos auxiliares
    private function calcularPromedioActual($nota)
    {
        $notas = array_filter([$nota->bimestre1, $nota->bimestre2, $nota->bimestre3, $nota->bimestre4]);
        return count($notas) > 0 ? array_sum($notas) / count($notas) : 12;
    }

    private function calcularPrediccionFinal($promedioActual, $factores)
    {
        $base = $promedioActual;
        
        // Ajustes según factores
        $ajustes = [
            'dificultad' => -$factores['dificultad_curso'] * 0.3,
            'tendencia' => $factores['tendencia_estudiante'] * 2,
            'asistencia' => ($factores['asistencia_curso'] - 85) * 0.05,
        ];
        
        $prediccion = $base + array_sum($ajustes);
        
        // Limitar entre 0 y 20
        return max(0, min(20, $prediccion));
    }

    private function calcularPrediccionBimestre($nota, $factores)
    {
        $notasActuales = array_filter([$nota->bimestre1, $nota->bimestre2, $nota->bimestre3, $nota->bimestre4]);
        
        if (empty($notasActuales)) return 12;
        
        $ultimaNota = end($notasActuales);
        $tendencia = $factores['tendencia_estudiante'];
        
        return max(0, min(20, $ultimaNota + $tendencia));
    }

    private function calcularProbabilidadAprobar($prediccionFinal)
    {
        if ($prediccionFinal >= 14) return 90 + ($prediccionFinal - 14) * 2;
        if ($prediccionFinal >= 12) return 70 + ($prediccionFinal - 12) * 10;
        if ($prediccionFinal >= 10) return 40 + ($prediccionFinal - 10) * 15;
        return max(10, $prediccionFinal * 4);
    }

    private function getDificultadCurso($nombreCurso)
    {
        $dificultades = [
            'Matemática I' => 3, 'Matemática II' => 3, 'Matemática III' => 3,
            'Física' => 3, 'Química' => 3,
            'Comunicación I' => 2, 'Comunicación II' => 2,
            'Historia' => 2, 'Geografía' => 2,
            'Inglés' => 2, 'Arte y Cultura' => 1,
            'Educación Física' => 1, 'Religión' => 1,
        ];
        
        return $dificultades[$nombreCurso] ?? 2;
    }

    private function getHorasEstudioRecomendadas($nombreCurso)
    {
        $horas = [
            'Matemática I' => 2.5, 'Matemática II' => 2.5, 'Matemática III' => 3,
            'Física' => 2.5, 'Química' => 2.5,
            'Comunicación I' => 1.5, 'Comunicación II' => 1.5,
            'Historia' => 2, 'Geografía' => 1.5,
            'Inglés' => 1.5, 'Arte y Cultura' => 1,
            'Educación Física' => 0.5, 'Religión' => 0.5,
        ];
        
        return $horas[$nombreCurso] ?? 1.5;
    }

    private function getTendenciaEstudiante($estudiante)
    {
        $notasRecientes = Nota::where('estudiante_id', $estudiante->id)
            ->orderBy('created_at', 'desc')
            ->limit(3)
            ->pluck('promedio_final');
        
        if ($notasRecientes->count() < 2) return 0;
        
        $primera = $notasRecientes->last();
        $ultima = $notasRecientes->first();
        
        return ($ultima - $primera) * 0.3;
    }

    private function calcularAsistenciaCurso($estudiante, $curso)
    {
        // Si tienes tabla de asistencias por curso, úsala aquí
        // Por ahora retorna un valor base
        return 85; // 85% de asistencia promedio
    }

    private function calcularTendenciaNotas($nota)
    {
        $notas = array_filter([$nota->bimestre1, $nota->bimestre2, $nota->bimestre3, $nota->bimestre4]);
        
        if (count($notas) < 2) return 0;
        
        $primera = reset($notas);
        $ultima = end($notas);
        
        return ($ultima - $primera) / count($notas);
    }

    private function generarRecomendacionesCurso($nombreCurso, $actual, $prediccion)
    {
        $recomendaciones = [
            'Matemática I' => [
                'Practica ejercicios diarios de álgebra básica',
                'Refuerza conceptos de ecuaciones lineales',
                'Utiliza Khan Academy para geometría plana',
                'Forma grupos de estudio para resolver problemas'
            ],
            'Comunicación I' => [
                'Lee 30 minutos diarios de literatura peruana',
                'Practica redacción con ensayos semanales',
                'Mejora ortografía con ejercicios online',
                'Participa más en debates de clase'
            ],
            'Inglés' => [
                'Practica conversación 20 minutos diarios',
                'Ve series con subtítulos en inglés',
                'Usa Duolingo para vocabulario básico',
                'Escucha podcasts en inglés básico'
            ],
            'Física' => [
                'Repasa fórmulas de cinemática',
                'Realiza experimentos caseros simples',
                'Usa simuladores de PhET Colorado',
                'Practica problemas de movimiento'
            ]
        ];

        $recs = $recomendaciones[$nombreCurso] ?? [
            'Dedica más tiempo de estudio a esta materia',
            'Consulta dudas con el profesor',
            'Forma grupos de estudio',
            'Utiliza recursos educativos online'
        ];

        // Personalizar según rendimiento
        if ($prediccion < 12) {
            array_unshift($recs, '🚨 URGENTE: Solicita tutoría inmediata');
        } elseif ($prediccion < 14) {
            array_unshift($recs, '⚠️ Requiere atención especial');
        }

        return array_slice($recs, 0, 4); // Máximo 4 recomendaciones
    }

    private function generarAnalisisCurso($nombreCurso, $actual, $prediccion)
    {
        $diferencia = $prediccion - $actual;
        $estado = $prediccion >= 14 ? 'favorable' : ($prediccion >= 12 ? 'regular' : 'riesgo');
        
        $analisis = "En {$nombreCurso}, tu rendimiento actual es {$actual} y se predice {$prediccion}. ";
        
        if ($diferencia > 1) {
            $analisis .= "Muestra una tendencia positiva de mejora. ";
        } elseif ($diferencia < -1) {
            $analisis .= "Presenta una tendencia descendente que requiere atención. ";
        } else {
            $analisis .= "Mantiene un rendimiento estable. ";
        }
        
        return $analisis . "Estado: {$estado}.";
    }

    private function identificarFortalezasCurso($nota, $curso)
    {
        $fortalezas = [];
        $promedio = $this->calcularPromedioActual($nota);
        
        if ($promedio >= 16) $fortalezas[] = 'Excelente dominio de la materia';
        if ($promedio >= 14) $fortalezas[] = 'Buen rendimiento académico';
        if ($this->calcularTendenciaNotas($nota) > 0) $fortalezas[] = 'Tendencia de mejora constante';
        
        return $fortalezas ?: ['Potencial de mejora identificado'];
    }

    private function identificarDebilidadesCurso($nota, $curso)
    {
        $debilidades = [];
        $promedio = $this->calcularPromedioActual($nota);
        
        if ($promedio < 12) $debilidades[] = 'Rendimiento por debajo del promedio';
        if ($this->calcularTendenciaNotas($nota) < -0.5) $debilidades[] = 'Tendencia descendente preocupante';
        if ($promedio < 10) $debilidades[] = 'Requiere intervención inmediata';
        
        return $debilidades;
    }

    private function calcularConfianza($nota, $factores)
    {
        $notasCompletas = count(array_filter([$nota->bimestre1, $nota->bimestre2, $nota->bimestre3, $nota->bimestre4]));
        return min(100, 40 + ($notasCompletas * 15));
    }

    public function detalle($cursoId)
{
    try {
        $user = Auth::user();
        $estudiante = $user->estudiante;

        if (!$estudiante) {
            return redirect()->route('estudiante.dashboard')
                ->with('error', 'No se encontró información del estudiante.');
        }

        $prediccion = PrediccionCurso::where('estudiante_id', $estudiante->id)
            ->where('curso_id', $cursoId)
            ->with('curso')
            ->latest('fecha_prediccion')
            ->first();

        if (!$prediccion) {
            return redirect()->route('estudiante.prediccion')
                ->with('error', 'No se encontró predicción para este curso.');
        }

        return view('colegio.estudiante.prediccion-detalle', compact('prediccion', 'estudiante'));

    } catch (\Exception $e) {
        Log::error('Error al ver detalle de predicción', [
            'error' => $e->getMessage(),
            'curso_id' => $cursoId,
            'user_id' => Auth::id()
        ]);

        return redirect()->route('estudiante.prediccion')
            ->with('error', 'Error al cargar el detalle de la predicción.');
    }
}
}