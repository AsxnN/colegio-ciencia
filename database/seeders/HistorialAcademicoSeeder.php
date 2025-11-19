<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class HistorialAcademicoSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create();

        $totalAlumnos = 600;
        $years = [2021, 2022, 2023];
        $bimestres = [1, 2, 3, 4];

        $data = [];

        foreach (range(1, $totalAlumnos) as $estudiante_id) {
            foreach ($years as $anio) {
                foreach ($bimestres as $bimestre) {
                    $data[] = [
                        'estudiante_id' => $estudiante_id,
                        'anio' => $anio,
                        'bimestre' => $bimestre, // Recuerda agregar este campo en la BD
                        'promedio' => $faker->randomFloat(2, 10, 20),
                        'horas_estudio' => $faker->numberBetween(5, 40),
                        'horas_sueno' => $faker->numberBetween(4, 10),
                        'actividad_fisica' => $faker->numberBetween(0, 7),
                        'padres_divorciados' => $faker->boolean(30) ? 1 : 0,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
            }
        }

        // Insertar en lotes para mejor desempeño
        foreach (array_chunk($data, 500) as $chunk) {
            DB::table('historial_academico')->insert($chunk);
        }
    }
}
