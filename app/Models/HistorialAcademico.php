<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class HistorialAcademico extends Model
{
    use HasFactory;

    // Nombre exacto de la tabla en tu BD
    protected $table = 'historial_academico';

    protected $fillable = [
        'estudiante_id',
        'anio',
        'promedio',
        'bimestre',
        'horas_estudio',
        'horas_sueno',
        'actividad_fisica',
        'padres_divorciados',
    ];
}
