<?php

namespace App\Http\Controllers;

use App\Models\Estudiante;
use App\Models\HistorialAcademico;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MlController extends Controller
{
    public function showForm(\Illuminate\Http\Request $request)
    {
        // Mostrar formulario vacío o con lista de estudiantes
        $estudiantes = Estudiante::all();
        $historial = HistorialAcademico::all();

        // Opcional: si se pasa estudiante_id en query string, cargar sus notas para mostrar en el formulario
        $estudiante = null;
        $notas = null;

        if ($request->filled('estudiante_id')) {
            $estudiante = Estudiante::with('notas.curso')->find($request->input('estudiante_id'));
            if ($estudiante) {
                Log::info('MlController.showForm cargó estudiante', [
                    'estudiante_id' => $estudiante->id,
                    'notas_count' => $estudiante->notas->count(),
                    'promedio_general' => $estudiante->notas->count() ? round($estudiante->notas->avg('promedio_final'),2) : null,
                ]);
            } else {
                Log::warning('MlController.showForm no encontró estudiante con id', ['estudiante_id' => $request->input('estudiante_id')]);
            }
        }

        return view('ml.form', compact('estudiantes', 'historial', 'estudiante', 'notas'));
    }

    public function predict(Request $request)
    {
        $flaskUrl = env('FLASK_URL', 'http://127.0.0.1:5000');
        // Mapear campos del formulario (es) a los esperados por la API Flask (en)
        // El formulario puede enviar nombres en español (ej. 'horas_estudio') o directamente en inglés.
        $payload = [
            'Hours_Studied' => $request->input('Hours_Studied') ?? $request->input('horas_estudio') ?? $request->input('hours_studied') ?? 0,
            'Attendance' => $request->input('Attendance') ?? $request->input('asistencia') ?? $request->input('attendance') ?? 0,
            'Sleep_Hours' => $request->input('Sleep_Hours') ?? $request->input('horas_sueno') ?? $request->input('sleep_hours') ?? 0,
            'Previous_Scores' => $request->input('Previous_Scores') ?? $request->input('promedio_anterior') ?? $request->input('previous_scores') ?? 0,
            'Tutoring_Sessions' => $request->input('Tutoring_Sessions') ?? $request->input('sesiones_tutoria') ?? $request->input('tutoring_sessions') ?? 0,
            'Physical_Activity' => $request->input('Physical_Activity') ?? $request->input('actividad_fisica') ?? $request->input('physical_activity') ?? 0,
        ];

        // Optional student identifier
        if ($request->filled('Student_ID')) {
            $payload['Student_ID'] = $request->input('Student_ID');
        } elseif ($request->filled('estudiante_id')) {
            $payload['Student_ID'] = $request->input('estudiante_id');
        }

        // For safety, cast numeric-like values
        foreach (['Hours_Studied','Attendance','Sleep_Hours','Previous_Scores','Tutoring_Sessions','Physical_Activity'] as $k) {
            if (is_numeric($payload[$k])) {
                $payload[$k] = (float) $payload[$k];
            }
        }

        try {
            // Enviar JSON a Flask — Http::post enviará JSON cuando se pase un array
            $response = Http::timeout(30)
                ->withHeaders(['Accept' => 'application/json'])
                ->post(rtrim($flaskUrl, '/') . '/predict', $payload);

            if ($response->successful()) {
                $body = $response->json();

                if (isset($body['error'])) {
                    return back()->withErrors(['ml' => $body['error']])->withInput();
                }

                // 🔹 Guardar historial si hay Student_ID
                if ($request->filled('Student_ID')) {
                    $estudiante = Estudiante::find($request->input('Student_ID'));

                    if ($estudiante) {
                        HistorialAcademico::create([
                            'estudiante_id' => $estudiante->id,
                            'anio' => now()->year,
                            'promedio' => $request->input('Previous_Scores', 0),
                            'horas_estudio' => $request->input('Hours_Studied', 0),
                            'horas_sueno' => $request->input('Sleep_Hours', 0),
                            'actividad_fisica' => $request->input('Physical_Activity', 0),
                            'padres_divorciados' => $estudiante->padres_divorciados ?? 0,
                        ]);
                    }
                }

                return view('ml.result', [
                    'prediction' => $body['prediction'] ?? null,
                    'notes_history' => $body['notes_history'] ?? [],
                ]);
            }

            return back()->withErrors(['ml' => 'Respuesta no exitosa del servicio ML: ' . $response->body()])->withInput();

        } catch (\Exception $e) {
            return back()->withErrors(['ml' => 'Error al comunicarse con el servicio ML: ' . $e->getMessage()])->withInput();
        }
    }
}
