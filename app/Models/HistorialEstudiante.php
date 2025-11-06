<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HistorialEstudiante extends Model
{
    protected $fillable = [
        'estudiante_id',
        'anio',
        'promedio',
        'horas_estudio',
        'horas_sueno',
        'actividad_fisica',
        'padres_divorciados',
    ];
}
