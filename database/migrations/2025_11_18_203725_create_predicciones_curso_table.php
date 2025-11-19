<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('predicciones_curso', function (Blueprint $table) {
             $table->id();
            $table->foreignId('estudiante_id')->constrained('estudiantes')->onDelete('cascade');
            $table->foreignId('curso_id')->constrained('cursos')->onDelete('cascade');
            $table->timestamp('fecha_prediccion')->useCurrent();
            
            // Predicción específica del curso
            $table->decimal('nota_predicha_bimestre', 4, 2)->comment('Nota predicha para próximo bimestre');
            $table->decimal('nota_predicha_final', 4, 2)->comment('Nota predicha final del curso');
            $table->decimal('probabilidad_aprobar_curso', 5, 2)->comment('Probabilidad de aprobar este curso específico');
            
            // Análisis por curso
            $table->text('analisis_curso')->nullable();
            $table->json('fortalezas_curso')->nullable();
            $table->json('debilidades_curso')->nullable();
            $table->json('recomendaciones_curso')->nullable();
            
            // Factores específicos del curso
            $table->integer('asistencias_curso')->default(0);
            $table->decimal('tendencia_notas', 3, 2)->nullable()->comment('Tendencia: +1=mejorando, 0=estable, -1=empeorando');
            
            $table->json('metadatos')->nullable();
            $table->timestamps();
            
            // Índices
            $table->unique(['estudiante_id', 'curso_id', 'fecha_prediccion'], 'unique_prediccion_curso');
            $table->index(['fecha_prediccion']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('predicciones_curso');
    }
};
