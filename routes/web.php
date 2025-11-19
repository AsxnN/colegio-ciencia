<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Http;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\AdministradoresController;
use App\Http\Controllers\DocentesController;
use App\Http\Controllers\EstudiantesController;
use App\Http\Controllers\SeccionesController;
use App\Http\Controllers\CursosController;
use App\Http\Controllers\NotasController;
use App\Http\Controllers\AsistenciasController;
use App\Http\Controllers\IAAdminController;
use App\Http\Controllers\RecursosEducativosController;
use App\Http\Controllers\PrediccionesController;
use App\Http\Controllers\RecomendacionesIAController;
use App\Http\Controllers\PrediccionesRendimientoController;
use App\Http\Controllers\PrediccionCursoController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Docente\DashboardController as DocenteDashboardController;
use App\Http\Controllers\EstudianteController;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {

    // ============================================================
    // DASHBOARD PRINCIPAL CON REDIRECCIÓN POR ROL
    // ============================================================
    Route::get('/dashboard', function () {
        $user = auth()->user();

        if ($user->rol_id == 1) { // Administrador
            return redirect()->route('admin.dashboard');
        } elseif ($user->rol_id == 2) { // Docente
            return redirect()->route('docente.dashboard');
        } elseif ($user->rol_id == 3) { // Estudiante
            return redirect()->route('estudiante.dashboard');
        }

        // Fallback por si no tiene rol definido
        return view('dashboard');
    })->name('dashboard');

    // ============================================================
    // RUTAS COMPARTIDAS (disponibles para todos los roles autenticados)
    // ============================================================



    // ✅ RUTA FIJA PARA PREDICCIONES (disponible para estudiantes)
    Route::match(['GET', 'POST'], '/prediccion/generar', [PrediccionesRendimientoController::class, 'estudianteGenerar'])
        ->name('prediccion.generar');

    // ============================================================
    // RUTAS DE PREDICCIONES CON IA (compartidas)
    // ============================================================
    Route::prefix('predicciones')->name('predicciones.')->group(function () {
        Route::get('/', [PrediccionesController::class, 'index'])->name('index');
        Route::get('/seleccionar', [PrediccionesController::class, 'seleccionar'])->name('seleccionar');
        Route::post('/generar/{estudiante}', [PrediccionesController::class, 'generar'])->name('generar');
        Route::post('/generar-todas', [PrediccionesController::class, 'generarTodas'])->name('generar-todas');
        Route::get('/{id}', [PrediccionesController::class, 'show'])->name('show');
        Route::delete('/{id}', [PrediccionesController::class, 'destroy'])->name('destroy');
    });

    // ============================================================
    // RUTAS DE PRUEBA Y DIAGNÓSTICO (compartidas)
    // ============================================================

    Route::get('/ia-models', function () {
        $apiKey = config('services.gemini.api_key');
        $baseUrl = config('services.gemini.base_url');

        try {
            $response = Http::timeout(10)->get("{$baseUrl}/models?key={$apiKey}");

            if ($response->successful()) {
                $data = $response->json();
                $modelos = collect($data['models'] ?? [])->filter(function ($model) {
                    return in_array('generateContent', $model['supportedGenerationMethods'] ?? []);
                })->map(function ($model) {
                    return [
                        'name' => $model['name'],
                        'displayName' => $model['displayName'] ?? 'N/A',
                        'description' => $model['description'] ?? 'N/A',
                    ];
                })->values();

                return response()->json([
                    'success' => true,
                    'total' => $modelos->count(),
                    'modelos' => $modelos
                ], 200, [], JSON_PRETTY_PRINT);
            }

            return response()->json(['success' => false, 'error' => $response->body()]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()]);
        }
    });

    Route::get('/test-ia', function () {
        $service = new \App\Services\GeminiService();

        $config = [
            'api_key_configured' => $service->verificarConfiguracion(),
            'api_key_length' => strlen(config('services.gemini.api_key') ?? ''),
            'api_key_preview' => substr(config('services.gemini.api_key') ?? '', 0, 10) . '...',
            'model' => config('services.gemini.model'),
        ];

        $testConexion = $service->testConexion();

        return response()->json([
            'sistema' => 'Sistema de Predicción Académica con IA',
            'version' => '1.0',
            'configuracion' => $config,
            'test_conexion' => $testConexion,
        ], 200, [], JSON_PRETTY_PRINT);
    });

    // ============================================================
    // RUTAS DE ADMINISTRADOR (rol_id = 1)
    // ============================================================
    Route::middleware(\App\Http\Middleware\RoleMiddleware::class . ':1')->prefix('admin')->name('admin.')->group(function () {

        // Dashboard administrador
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

        // Panel de IA
        Route::get('/ia-panel', [IAAdminController::class, 'index'])->name('ia.panel');
        Route::get('/api/model/status', [IAAdminController::class, 'modelStatus'])->name('api.model.status');

        // ✅ MÓDULOS DE ADMINISTRACIÓN
        Route::resource('usuarios', UsersController::class);
        Route::resource('administradores', AdministradoresController::class);
        Route::resource('docentes', DocentesController::class);
        Route::resource('estudiantes', EstudiantesController::class);
        Route::get('/estudiantes-estadisticas', [EstudiantesController::class, 'estadisticas'])->name('estudiantes.estadisticas');

        // Rutas específicas de estudiantes para admin
        Route::get('/estudiantes/{id}/perfil', [EstudiantesController::class, 'perfil'])->name('estudiantes.perfil');
        Route::get('/estudiantes/{id}/cursos', [EstudiantesController::class, 'cursos'])->name('estudiantes.cursos');
        Route::get('/estudiantes/{id}/notas', [EstudiantesController::class, 'notas'])->name('estudiantes.notas');
        Route::get('/estudiantes/{id}/predicciones', [EstudiantesController::class, 'predicciones'])->name('estudiantes.predicciones');
        Route::get('/estudiantes/{id}/recomendaciones', [EstudiantesController::class, 'recomendaciones'])->name('estudiantes.recomendaciones');
        Route::get('/estudiantes/{id}/recursos', [EstudiantesController::class, 'recursos'])->name('estudiantes.recursos');

        // Secciones
        Route::resource('secciones', SeccionesController::class);
        Route::get('/secciones/{seccione}/estudiantes', [SeccionesController::class, 'estudiantes'])->name('secciones.estudiantes');
        Route::post('/secciones/{seccione}/asignar-estudiante', [SeccionesController::class, 'asignarEstudiante'])->name('secciones.asignar-estudiante');
        Route::delete('/secciones/{seccione}/remover-estudiante', [SeccionesController::class, 'removerEstudiante'])->name('secciones.remover-estudiante');
        Route::put('/secciones/{seccione}/transferir-estudiante', [SeccionesController::class, 'transferirEstudiante'])->name('secciones.transferir-estudiante');

        // Cursos
        Route::resource('cursos', CursosController::class);

        // Notas
        Route::resource('notas', NotasController::class);
        Route::get('/notas/estudiante/{estudiante}', [NotasController::class, 'porEstudiante'])->name('notas.por-estudiante');
        Route::get('/notas/curso/{curso}', [NotasController::class, 'porCurso'])->name('notas.por-curso');

        // Asistencias
        Route::get('/asistencias', [AsistenciasController::class, 'index'])->name('asistencias.index');
        Route::get('/asistencias/registrar', [AsistenciasController::class, 'registrar'])->name('asistencias.registrar');
        Route::post('/asistencias/guardar-masivo', [AsistenciasController::class, 'guardarMasivo'])->name('asistencias.guardar-masivo');
        Route::get('/asistencias/create', [AsistenciasController::class, 'create'])->name('asistencias.create');
        Route::post('/asistencias', [AsistenciasController::class, 'store'])->name('asistencias.store');
        Route::get('/asistencias/{asistencia}/edit', [AsistenciasController::class, 'edit'])->name('asistencias.edit');
        Route::put('/asistencias/{asistencia}', [AsistenciasController::class, 'update'])->name('asistencias.update');
        Route::delete('/asistencias/{asistencia}', [AsistenciasController::class, 'destroy'])->name('asistencias.destroy');
        Route::get('/asistencias/estudiante/{estudiante}', [AsistenciasController::class, 'porEstudiante'])->name('asistencias.por-estudiante');
        Route::get('/asistencias/curso/{curso}', [AsistenciasController::class, 'porCurso'])->name('asistencias.por-curso');
        Route::get('/asistencias/reporte-mensual', [AsistenciasController::class, 'reporteMensual'])->name('asistencias.reporte-mensual');

        // Recursos educativos
        Route::resource('recursos', RecursosEducativosController::class)->names('recursos');
        Route::get('/recursos/curso/{curso}', [RecursosEducativosController::class, 'porCurso'])->name('recursos.por-curso');
    });

    // ============================================================
    // RUTAS PARA DOCENTES (rol_id = 2)
    // ============================================================
    Route::middleware(\App\Http\Middleware\RoleMiddleware::class . ':2')->prefix('docente')->name('docente.')->group(function () {

        Route::get('/dashboard', [DocenteDashboardController::class, 'index'])->name('dashboard');
        Route::get('/cursos', [DocenteDashboardController::class, 'cursos'])->name('cursos');
        Route::get('/estudiantes', [DocenteDashboardController::class, 'estudiantes'])->name('estudiantes');
        Route::get('/estudiantes/{id}', [DocenteDashboardController::class, 'estudianteDetalle'])->name('estudiante.detalle');
        Route::get('/estudiantes/{id}/plan-accion', [DocenteDashboardController::class, 'estudiantePlanAccion'])->name('estudiante.plan-accion');
        Route::post('/estudiantes/{id}/plan-accion', [DocenteDashboardController::class, 'crearPlanAccion'])->name('estudiante.plan-accion.crear');
        Route::get('/actividades', [DocenteDashboardController::class, 'actividades'])->name('actividades');
        Route::post('/actividades', [DocenteDashboardController::class, 'crearActividad'])->name('actividades.crear');
        Route::put('/actividades/{id}', [DocenteDashboardController::class, 'editarActividad'])->name('actividades.editar');
        Route::delete('/actividades/{id}', [DocenteDashboardController::class, 'eliminarActividad'])->name('actividades.eliminar');
        Route::get('/predicciones', [PrediccionesRendimientoController::class, 'docenteDashboard'])->name('predicciones');
        Route::get('/predicciones/export', [PrediccionesRendimientoController::class, 'exportarReporte'])->name('predicciones.export');
        Route::post('/predicciones/generar', [PrediccionesRendimientoController::class, 'generarPredicciones'])->name('predicciones.generar');
        Route::get('/recomendaciones', [DocenteDashboardController::class, 'recomendaciones'])->name('recomendaciones');
        Route::post('/recomendaciones/generar', [DocenteDashboardController::class, 'generarRecomendaciones'])->name('recomendaciones.generar');
        Route::post('/recomendaciones/{id}/completar', [DocenteDashboardController::class, 'completarRecomendacion'])->name('recomendaciones.completar');
        Route::get('/historial', [DocenteDashboardController::class, 'historial'])->name('historial');
        Route::get('/historial/{estudianteId}', [DocenteDashboardController::class, 'historialEstudiante'])->name('historial.estudiante');
        Route::get('/comunicacion', [DocenteDashboardController::class, 'comunicacion'])->name('comunicacion');
    });

    // ============================================================
    // RUTAS PARA ESTUDIANTES (rol_id = 3)
    // ============================================================
    Route::middleware(\App\Http\Middleware\RoleMiddleware::class . ':3')->prefix('estudiante')->name('estudiante.')->group(function () {

        Route::get('/dashboard', [EstudianteController::class, 'dashboardEstudiante'])->name('dashboard');

        // ✅ RUTA DE PREDICCIÓN PRINCIPAL (LA QUE FALTA)
        Route::get('/prediccion', [PrediccionesRendimientoController::class, 'estudiantePrediccion'])->name('prediccion');
        Route::post('/prediccion/generar', [PrediccionesRendimientoController::class, 'generar'])->name('prediccion.generar');

        // ✅ RUTAS DE PREDICCIÓN PARA ESTUDIANTES
        Route::post('/prediccion/curso/{curso}/generar', [PrediccionCursoController::class, 'generar'])->name('prediccion.curso.generar');
        Route::post('/prediccion/cursos/generar-todas', [PrediccionCursoController::class, 'generarTodas'])->name('prediccion.curso.generar.todas');
        Route::get('/prediccion/curso/{curso}/detalle', [PrediccionCursoController::class, 'detalle'])->name('prediccion.curso.detalle');
        Route::post('/estudiante/generar-prediccion', [PrediccionesRendimientoController::class, 'estudianteGenerar'])
            ->name('estudiante.generarPrediccion')
            ->middleware('auth');

        // ✅ Recomendaciones y recursos
        Route::get('/recomendaciones/{id?}', [RecomendacionesIAController::class, 'index'])->name('recomendaciones');
        Route::get('/recursos/{id?}', [RecursosEducativosController::class, 'index'])->name('recursos');

        // Rutas específicas del estudiante
        Route::get('/perfil/{id?}', [EstudiantesController::class, 'perfil'])->name('perfil');
        Route::get('/notas/{id?}', [EstudiantesController::class, 'notas'])->name('notas');
        Route::get('/cursos/{id?}', [EstudiantesController::class, 'cursos'])->name('cursos');
        Route::get('/predicciones/{id?}', [EstudiantesController::class, 'predicciones'])->name('predicciones');
        Route::get('/recomendaciones/{id?}', [EstudiantesController::class, 'recomendaciones'])->name('recomendaciones');
        Route::get('/recursos/{id?}', [EstudiantesController::class, 'recursos'])->name('recursos');
        Route::get('/detallenotas/{nota}', [EstudianteController::class, 'detalleNotas'])->name('detallenotas');
    });
});
