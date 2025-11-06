<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class EstudianteController extends Controller
{
    public function dashboard()
    {
        return view('colegio.estudiante.dashboard');
    }
}
