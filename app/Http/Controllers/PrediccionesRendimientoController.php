<?php
namespace App\Http\Controllers;

use App\Models\PrediccionRendimiento;
use App\Models\Estudiante;
use App\Models\Seccion;
use App\Models\Curso;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;

class PrediccionesRendimientoController extends Controller
{
    public function estudiantePrediccion(Request $request)
    {
        // 1) intentar obtener id desde query ?estudiante_id o ruta {estudiante}
        $estudianteId = $request->input('estudiante_id') ?? $request->route('estudiante');

        if ($estudianteId) {
            $estudiante = Estudiante::find($estudianteId);
        } else {
            // 2) si no viene id, usar estudiante del usuario autenticado (si existe)
            // 3) si tampoco existe, usar el primer estudiante como fallback (para pruebas)
            $estudiante = optional(Auth::user())->estudiante ?? Estudiante::first();
        }

        // 4) si sigue sin estudiante, renderizar vista con mensaje informativo (no redirigir)
        if (! $estudiante) {
            $mensaje = 'No se encontró estudiante. Pasa ?estudiante_id=ID o crea un registro en la BD.';
            $viewName = view()->exists('colegio.estudiante.prediccion')
                ? 'colegio.estudiante.prediccion'
                : 'colegio.estudiante.predicciones';
            return view($viewName, compact('mensaje'));
        }

        
        // Calcular datos del estudiante
        $promedio_anterior = $estudiante->notas->avg('promedio_final') ?? 0;
        $porcentaje_asistencia = $this->calcularPorcentajeAsistencia($estudiante);
        
        // Historial de predicciones
        $historial_predicciones = PrediccionRendimiento::where('estudiante_id', $estudiante->id)
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();
        
        return view('colegio.estudiante.prediccion', compact(
            'estudiante',
            'promedio_anterior', 
            'porcentaje_asistencia',
            'historial_predicciones'
        ));
    }
    

    public function estudianteGenerar(Request $request)
    {
        $validated = $request->validate([
            'Student_ID' => 'required|exists:estudiantes,id',
            'Hours_Studied' => 'required|numeric|min:0|max:100',
            'Attendance' => 'required|numeric|min:0|max:100',
            'Sleep_Hours' => 'required|numeric|min:4|max:12',
            'Physical_Activity' => 'required|numeric|min:0|max:7',
            'Tutoring_Sessions' => 'required|numeric|min:0|max:20',
            'Previous_Scores' => 'nullable|numeric|min:0|max:20',
        ]);
        
        try {
            $flaskUrl = env('FLASK_URL', 'http://127.0.0.1:5000');
            
            $response = Http::timeout(30)->post("{$flaskUrl}/predict", $validated);
            
            if ($response->successful()) {
                $result = $response->json();
                
                // Guardar la predicción en la base de datos
                $estudiante = Estudiante::find($validated['Student_ID']);
                
                PrediccionRendimiento::create([
                    'estudiante_id' => $estudiante->id,
                    'probabilidad_aprobar' => $result['prediction'],
                    'prediccion_binaria' => $result['prediction'] >= 14,
                    'nivel_riesgo' => $this->determinarNivelRiesgo($result['prediction']),
                    'analisis' => 'Predicción generada por el estudiante',
                    'metadatos' => [
                        'generated_by' => 'student',
                        'model_type' => $result['model_type'] ?? 'unknown',
                        'input_data' => $validated
                    ]
                ]);
                
                return response()->json([
                    'success' => true,
                    'prediction' => $result['prediction'],
                    'model_type' => $result['model_type'] ?? 'unknown',
                    'interpretacion' => $this->generarInterpretacion($result['prediction'])
                ]);
            }
            
            return response()->json([
                'success' => false,
                'error' => 'Error en el servicio de predicción'
            ], 500);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function docenteDashboard()
    {
        // Obtener estudiantes con predicciones
        $estudiantes = Estudiante::with(['usuario', 'seccion', 'predicciones' => function($query) {
            $query->latest()->limit(1);
        }])
        ->whereHas('notas')
        ->get()
        ->map(function($estudiante) {
            $estudiante->ultima_prediccion = $estudiante->predicciones->first();
            $estudiante->porcentaje_asistencia = $this->calcularPorcentajeAsistencia($estudiante);
            return $estudiante;
        });
        
        // Estadísticas
        $estudiantes_alto_riesgo = $estudiantes->filter(function($est) {
            return $est->ultima_prediccion && $est->ultima_prediccion->probabilidad_aprobar < 12;
        })->count();
        
        $estudiantes_medio_riesgo = $estudiantes->filter(function($est) {
            return $est->ultima_prediccion && 
                   $est->ultima_prediccion->probabilidad_aprobar >= 12 && 
                   $est->ultima_prediccion->probabilidad_aprobar < 14;
        })->count();
        
        $estudiantes_bajo_riesgo = $estudiantes->filter(function($est) {
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
        if ($prediccion >= 16) return "Excelente rendimiento esperado 🌟";
        if ($prediccion >= 14) return "Buen rendimiento esperado 👍";
        if ($prediccion >= 12) return "Rendimiento promedio 📊";
        return "Necesita mejorar estrategias de estudio 📈";
    }

}