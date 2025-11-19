<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PrediccionCurso extends Model
{
    protected $table = 'predicciones_curso';

    protected $fillable = [
        'estudiante_id',
        'curso_id', 
        'fecha_prediccion',
        'nota_predicha_bimestre',
        'nota_predicha_final',
        'probabilidad_aprobar_curso',
        'analisis_curso',
        'fortalezas_curso',
        'debilidades_curso', 
        'recomendaciones_curso',
        'asistencias_curso',
        'tendencia_notas',
        'metadatos',
    ];

    protected $casts = [
        'fecha_prediccion' => 'datetime',
        'fortalezas_curso' => 'array',
        'debilidades_curso' => 'array',
        'recomendaciones_curso' => 'array',
        'metadatos' => 'array',
        'nota_predicha_bimestre' => 'decimal:2',
        'nota_predicha_final' => 'decimal:2',
        'probabilidad_aprobar_curso' => 'decimal:2',
        'tendencia_notas' => 'decimal:2',
    ];

    public function estudiante(): BelongsTo
    {
        return $this->belongsTo(Estudiante::class);
    }

    public function curso(): BelongsTo
    {
        return $this->belongsTo(Curso::class);
    }

    // Accessor para determinar el estado del curso
    public function getEstadoCursoAttribute(): string
    {
        if ($this->probabilidad_aprobar_curso >= 80) return 'Excelente';
        if ($this->probabilidad_aprobar_curso >= 60) return 'Bueno';
        if ($this->probabilidad_aprobar_curso >= 40) return 'Regular';
        return 'En Riesgo';
    }

    public function getNivelRiesgoAttribute(): string
    {
        if ($this->nota_predicha_final >= 16) return 'Muy Bajo';
        if ($this->nota_predicha_final >= 14) return 'Bajo';
        if ($this->nota_predicha_final >= 12) return 'Medio';
        return 'Alto';
    }

    public function getColorEstadoAttribute(): string
    {
        return match($this->estado_curso) {
            'Excelente' => 'green',
            'Bueno' => 'blue',
            'Regular' => 'yellow',
            'En Riesgo' => 'red',
            default => 'gray'
        };
    }
}