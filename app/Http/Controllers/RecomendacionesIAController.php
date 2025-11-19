<?php
// filepath: c:\laragon\www\colegio-ciencia\app\Http\Controllers\RecomendacionesIAController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Estudiante;
use App\Models\PrediccionCurso;

class RecomendacionesIAController extends Controller
{
    public function index($id = null)
    {
        try {
            $user = Auth::user();
            $estudiante = $id ? Estudiante::find($id) : $user->estudiante;

            if (!$estudiante) {
                return view('colegio.estudiante.recomendaciones', [
                    'recomendaciones' => collect(),
                    'estudiante' => null
                ]);
            }

            // Obtener recomendaciones basadas en predicciones
            $predicciones = PrediccionCurso::where('estudiante_id', $estudiante->id)
                ->with('curso')
                ->get();

            $recomendaciones = collect();
            
            foreach ($predicciones as $prediccion) {
                if (isset($prediccion->recomendaciones_curso)) {
                    $recomendaciones->push([
                        'curso' => $prediccion->curso->nombre,
                        'acciones' => $prediccion->recomendaciones_curso,
                        'prioridad' => $prediccion->probabilidad_aprobar_curso < 70 ? 'alta' : 'media',
                        'estado' => $prediccion->estado_curso
                    ]);
                }
            }

            return view('colegio.estudiante.recomendaciones', compact('recomendaciones', 'estudiante'));

        } catch (\Exception $e) {
            return view('colegio.estudiante.recomendaciones', [
                'recomendaciones' => collect(),
                'estudiante' => null,
                'error' => $e->getMessage()
            ]);
        }
    }
}