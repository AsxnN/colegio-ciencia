<?php

namespace App\Http\Controllers;

use App\Models\PrediccionRendimiento;
use App\Models\Estudiante;
use App\Models\Seccion;
use App\Models\Curso;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class IAAdminController extends Controller
{
    public function index()
    {
        // Estadísticas generales
        $total_estudiantes = Estudiante::count();
        $predicciones_hoy = PrediccionRendimiento::whereDate('created_at', today())->count();
        $precision_modelo = 94.2; // Puedes calcularlo dinámicamente
        
        $promedio_predicho = PrediccionRendimiento::avg('probabilidad_aprobar') ?? 0;
        $estudiantes_riesgo = PrediccionRendimiento::where('nivel_riesgo', 'Alto')->count();
        $tiempo_respuesta = $this->medirTiempoRespuestaAPI();
        
        // Fechas de entrenamiento (puedes guardarlas en configuración)
        $fecha_entrenamiento_neural = config('app.neural_model_trained_at', '2024-01-15');
        $fecha_entrenamiento_rf = config('app.rf_model_trained_at', '2024-01-10');
        
        return view('colegio.predicciones.admin', compact(
            'total_estudiantes',
            'predicciones_hoy', 
            'precision_modelo',
            'promedio_predicho',
            'estudiantes_riesgo',
            'tiempo_respuesta',
            'fecha_entrenamiento_neural',
            'fecha_entrenamiento_rf'
        ));
    }

    public function modelStatus()
    {
        try {
            $flaskUrl = env('FLASK_URL', 'http://127.0.0.1:5000');
            
            $response = Http::timeout(5)->get("{$flaskUrl}/model/status");
            
            if ($response->successful()) {
                return response()->json($response->json());
            }
            
            return response()->json([
                'status' => 'error',
                'error' => 'No se puede conectar al servicio Flask',
                'flask_url' => $flaskUrl
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'error' => $e->getMessage()
            ]);
        }
    }

    public function reentrenarModelo(Request $request, $tipo)
    {
        try {
            Log::info("🔄 Iniciando reentrenamiento de modelo: {$tipo}");
            
            $flaskUrl = env('FLASK_URL', 'http://127.0.0.1:5000');
            
            // Llamar al endpoint de entrenamiento en Flask
            $response = Http::timeout(300)->post("{$flaskUrl}/retrain", [
                'model_type' => $tipo,
                'force' => true
            ]);
            
            if ($response->successful()) {
                $result = $response->json();
                
                Log::info("✅ Modelo reentrenado exitosamente", [
                    'tipo' => $tipo,
                    'precision' => $result['accuracy'] ?? 'N/A'
                ]);
                
                return response()->json([
                    'success' => true,
                    'message' => "Modelo {$tipo} reentrenado exitosamente",
                    'data' => $result
                ]);
            }
            
            return response()->json([
                'success' => false,
                'error' => 'Error en el reentrenamiento: ' . $response->body()
            ], 500);
            
        } catch (\Exception $e) {
            Log::error("❌ Error reentrenando modelo {$tipo}", [
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function exportarModelo($tipo)
    {
        try {
            $flaskUrl = env('FLASK_URL', 'http://127.0.0.1:5000');
            
            $response = Http::timeout(30)->get("{$flaskUrl}/export/{$tipo}");
            
            if ($response->successful()) {
                $filename = "modelo_{$tipo}_" . date('Y-m-d_H-i-s') . 
                           ($tipo === 'neural' ? '.h5' : '.pkl');
                
                return response($response->body())
                    ->header('Content-Type', 'application/octet-stream')
                    ->header('Content-Disposition', "attachment; filename={$filename}");
            }
            
            return back()->with('error', 'No se pudo exportar el modelo');
            
        } catch (\Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function guardarConfiguracion(Request $request)
    {
        $validated = $request->validate([
            'umbral_riesgo_alto' => 'required|numeric|min:0|max:20',
            'frecuencia_entrenamiento' => 'required|in:semanal,quincenal,mensual,manual',
            'notif_estudiantes_riesgo' => 'boolean',
            'notif_reporte_semanal' => 'boolean', 
            'notif_modelo_actualizado' => 'boolean',
        ]);
        
        try {
            // Guardar en configuración del sistema
            foreach ($validated as $key => $value) {
                DB::table('configuracion_ia')->updateOrInsert(
                    ['clave' => $key],
                    ['valor' => $value, 'updated_at' => now()]
                );
            }
            
            Log::info('⚙️ Configuración de IA actualizada', $validated);
            
            return response()->json([
                'success' => true,
                'message' => 'Configuración guardada exitosamente'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function limpiarLogs()
    {
        try {
            // Limpiar logs de Laravel
            $logPath = storage_path('logs/laravel.log');
            if (file_exists($logPath)) {
                file_put_contents($logPath, '');
            }
            
            Log::info('🧹 Logs del sistema limpiados por administrador');
            
            return response()->json([
                'success' => true,
                'message' => 'Logs limpiados exitosamente'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    private function medirTiempoRespuestaAPI()
    {
        try {
            $start = microtime(true);
            
            $flaskUrl = env('FLASK_URL', 'http://127.0.0.1:5000');
            Http::timeout(5)->get("{$flaskUrl}/model/status");
            
            $end = microtime(true);
            
            return round(($end - $start) * 1000); // en milisegundos
            
        } catch (\Exception $e) {
            return 'N/A';
        }
    }
}