<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHistorialAcademicoTable extends Migration
{
    public function up()
    {
        Schema::create('historial_academico', function (Blueprint $table) {
            $table->id(); // bigint unsigned auto_increment primary
            $table->foreignId('estudiante_id')->constrained('estudiantes')->cascadeOnDelete(); // bigint unsigned, index
            $table->integer('anio');
            $table->integer('bimestre');
            $table->decimal('promedio', 4, 2);
            $table->integer('horas_estudio');
            $table->integer('horas_sueno');
            $table->integer('actividad_fisica');
            $table->boolean('padres_divorciados')->default(false);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('historial_academico');
    }
}