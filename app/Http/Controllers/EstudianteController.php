<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PrediccionRendimiento;
use Illuminate\Support\Facades\Auth;
use App\Models\Nota;
class EstudianteController extends Controller
{
    public function dashboard()
    {
        return view('colegio.estudiante.dashboard');
    }

    public function cursos()
    {
        $estudiante = Auth::user()->estudiante;
        $cursos = $estudiante && $estudiante->seccion ? $estudiante->seccion->cursos()->with('docente.usuario')->get() : collect();
        return view('colegio.estudiante.cursos', compact('estudiante', 'cursos'));
    }

    // Historial de notas
    public function notas()
    {
        $estudiante = Auth::user()->estudiante;
        $notas = $estudiante ? Nota::where('estudiante_id', $estudiante->id)->with('curso')->get() : collect();
        return view('colegio.estudiante.notas', compact('estudiante', 'notas'));
    }

    // Recomendaciones personalizadas
    public function recomendaciones()
    {
        $estudiante = Auth::user()->estudiante;
        $recomendaciones = collect(); // Aquí puedes agregar lógica personalizada
        return view('colegio.estudiante.recomendaciones', compact('estudiante', 'recomendaciones'));
    }

    // Alertas y seguimiento
    public function alertas()
    {
        $estudiante = Auth::user()->estudiante;
        $alertas = []; // Aquí puedes agregar lógica personalizada
        return view('colegio.estudiante.alertas', compact('estudiante', 'alertas'));
    }

    // Panel de tutorías
    public function tutorias()
    {
        $estudiante = Auth::user()->estudiante;
        $tutorias = collect(); // Aquí puedes agregar lógica personalizada
        return view('colegio.estudiante.tutorias', compact('estudiante', 'tutorias'));
    }

    // Soporte y mensajería
    public function soporte()
    {
        $estudiante = Auth::user()->estudiante;
        $mensajes = collect(); // Aquí puedes agregar lógica personalizada
        return view('colegio.estudiante.soporte', compact('estudiante', 'mensajes'));
    }

    // Recursos educativos
    public function recursos()
    {
        $estudiante = Auth::user()->estudiante;
        $recursos = collect(); // Aquí puedes agregar lógica personalizada
        return view('colegio.estudiante.recursos', compact('estudiante', 'recursos'));
    }

    // Historial de predicciones
    public function predicciones()
    {
        $estudiante = Auth::user()->estudiante;
        $predicciones = $estudiante ? PrediccionRendimiento::where('estudiante_id', $estudiante->id)->get() : collect();
        return view('colegio.estudiante.predicciones', compact('estudiante', 'predicciones'));
    }
    
}
