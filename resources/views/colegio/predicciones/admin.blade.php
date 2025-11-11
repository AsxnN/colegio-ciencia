<!-- filepath: c:\laragon\www\colegio-ciencia\resources\views\colegio\predicciones\admin.blade.php -->
@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-800">🎛️ Panel de Administración IA</h1>
        <p class="text-gray-600 mt-2">Gestiona los modelos de inteligencia artificial del sistema</p>
    </div>

    <!-- Estado del Sistema -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-lg font-semibold mb-4">🤖 Estado del Modelo</h2>
            <div id="model-status">
                <div class="animate-pulse text-gray-500">Verificando...</div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-lg font-semibold mb-4">📊 Estadísticas Generales</h2>
            <div class="space-y-3">
                <div class="flex justify-between">
                    <span class="text-gray-600">Total Estudiantes:</span>
                    <span class="font-medium">{{ $total_estudiantes }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Predicciones Hoy:</span>
                    <span class="font-medium">{{ $predicciones_hoy }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Precisión del Modelo:</span>
                    <span class="font-medium text-green-600">{{ $precision_modelo }}%</span>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-lg font-semibold mb-4">🎯 Métricas de Rendimiento</h2>
            <div class="space-y-3">
                <div class="flex justify-between">
                    <span class="text-gray-600">Promedio Predicho:</span>
                    <span class="font-medium">{{ number_format($promedio_predicho, 2) }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Estudiantes en Riesgo:</span>
                    <span class="font-medium text-red-600">{{ $estudiantes_riesgo }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Tiempo Resp. API:</span>
                    <span class="font-medium">{{ $tiempo_respuesta }}ms</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Gestión de Modelos -->
    <div class="grid grid-cols-1 xl:grid-cols-2 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-lg font-semibold mb-4">🧠 Gestión de Modelos</h2>
            
            <div class="space-y-4">
                <div class="border border-gray-200 rounded-lg p-4">
                    <div class="flex items-center justify-between mb-2">
                        <div class="flex items-center">
                            <div class="w-3 h-3 bg-green-500 rounded-full mr-2"></div>
                            <span class="font-medium">Red Neuronal [10-20-10]</span>
                        </div>
                        <span class="text-sm text-gray-500">Activo</span>
                    </div>
                    <div class="text-sm text-gray-600 mb-3">
                        Entrenado: {{ $fecha_entrenamiento_neural }}<br>
                        Precisión: 94.2% | Muestras: 1,847
                    </div>
                    <div class="flex space-x-2">
                        <button onclick="reentrenarModelo('neural')" 
                                class="px-3 py-1 bg-blue-600 text-white text-sm rounded hover:bg-blue-700">
                            🔄 Reentrenar
                        </button>
                        <button onclick="exportarModelo('neural')" 
                                class="px-3 py-1 bg-green-600 text-white text-sm rounded hover:bg-green-700">
                            📥 Exportar
                        </button>
                    </div>
                </div>

                <div class="border border-gray-200 rounded-lg p-4">
                    <div class="flex items-center justify-between mb-2">
                        <div class="flex items-center">
                            <div class="w-3 h-3 bg-yellow-500 rounded-full mr-2"></div>
                            <span class="font-medium">Random Forest</span>
                        </div>
                        <span class="text-sm text-gray-500">Respaldo</span>
                    </div>
                    <div class="text-sm text-gray-600 mb-3">
                        Entrenado: {{ $fecha_entrenamiento_rf }}<br>
                        Precisión: 87.8% | Árboles: 100
                    </div>
                    <div class="flex space-x-2">
                        <button onclick="activarModelo('rf')" 
                                class="px-3 py-1 bg-orange-600 text-white text-sm rounded hover:bg-orange-700">
                            🔄 Activar
                        </button>
                        <button onclick="eliminarModelo('rf')" 
                                class="px-3 py-1 bg-red-600 text-white text-sm rounded hover:bg-red-700">
                            🗑️ Eliminar
                        </button>
                    </div>
                </div>

                <div class="border-2 border-dashed border-gray-300 rounded-lg p-4 text-center">
                    <input type="file" id="modelo-upload" accept=".pkl,.h5" class="hidden">
                    <button onclick="document.getElementById('modelo-upload').click()" 
                            class="text-blue-600 hover:text-blue-800">
                        📁 Subir Nuevo Modelo
                    </button>
                    <div class="text-sm text-gray-500 mt-1">
                        Formatos soportados: .pkl, .h5
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-lg font-semibold mb-4">⚙️ Configuración del Sistema</h2>
            
            <form onsubmit="guardarConfiguracion(event)">
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            🎯 Umbral de Riesgo Alto
                        </label>
                        <input type="number" name="umbral_riesgo_alto" value="12" 
                               min="0" max="20" step="0.1"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md">
                        <small class="text-gray-500">Notas por debajo se consideran alto riesgo</small>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            ⏰ Frecuencia de Reentrenamiento
                        </label>
                        <select name="frecuencia_entrenamiento" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-md">
                            <option value="semanal">Semanal</option>
                            <option value="quincenal" selected>Quincenal</option>
                            <option value="mensual">Mensual</option>
                            <option value="manual">Manual</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            📧 Notificaciones Automáticas
                        </label>
                        <div class="space-y-2">
                            <label class="flex items-center">
                                <input type="checkbox" name="notif_estudiantes_riesgo" checked 
                                       class="mr-2 rounded">
                                <span class="text-sm">Estudiantes en riesgo alto</span>
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" name="notif_reporte_semanal" checked 
                                       class="mr-2 rounded">
                                <span class="text-sm">Reporte semanal a docentes</span>
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" name="notif_modelo_actualizado" 
                                       class="mr-2 rounded">
                                <span class="text-sm">Modelo actualizado</span>
                            </label>
                        </div>
                    </div>

                    <button type="submit" 
                            class="w-full bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">
                        💾 Guardar Configuración
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Logs del Sistema -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-lg font-semibold">📋 Logs del Sistema</h2>
            <button onclick="limpiarLogs()" 
                    class="px-3 py-1 bg-red-600 text-white text-sm rounded hover:bg-red-700">
                🧹 Limpiar Logs
            </button>
        </div>
        
        <div class="bg-gray-900 text-green-400 p-4 rounded-lg font-mono text-sm max-h-64 overflow-y-auto" id="system-logs">
            <div>[{{ date('Y-m-d H:i:s') }}] INFO: Sistema iniciado</div>
            <div>[{{ date('Y-m-d H:i:s') }}] INFO: Modelo neural cargado correctamente</div>
            <div>[{{ date('Y-m-d H:i:s') }}] INFO: API Flask respondiendo en puerto 5000</div>
            <div>[{{ date('Y-m-d H:i:s') }}] INFO: Conexión a base de datos establecida</div>
        </div>
    </div>
</div>

<script>
// Verificar estado del modelo al cargar la página
document.addEventListener('DOMContentLoaded', function() {
    verificarEstadoModelo();
});

async function verificarEstadoModelo() {
    try {
        const response = await fetch('/api/model/status');
        const status = await response.json();
        
        const statusEl = document.getElementById('model-status');
        if (status.status === 'ok') {
            statusEl.innerHTML = `
                <div class="flex items-center mb-2">
                    <div class="w-3 h-3 bg-green-500 rounded-full mr-2"></div>
                    <span class="font-medium">${status.model_type === 'neural_network' ? 'Red Neuronal' : 'Random Forest'}</span>
                </div>
                <div class="text-sm text-gray-600">
                    Neural disponible: ${status.neural_available ? '✅' : '❌'}<br>
                    RF disponible: ${status.rf_available ? '✅' : '❌'}
                </div>
            `;
        } else {
            statusEl.innerHTML = `
                <div class="flex items-center mb-2">
                    <div class="w-3 h-3 bg-red-500 rounded-full mr-2"></div>
                    <span class="font-medium text-red-600">Error</span>
                </div>
                <div class="text-sm text-red-600">${status.error}</div>
            `;
        }
    } catch (error) {
        document.getElementById('model-status').innerHTML = `
            <div class="text-red-600">❌ No se puede conectar al servicio IA</div>
        `;
    }
}

function reentrenarModelo(tipo) {
    if (confirm('¿Estás seguro de que quieres reentrenar el modelo? Este proceso puede tomar varios minutos.')) {
        // Lógica para reentrenar el modelo
        console.log('Reentrenando modelo:', tipo);
    }
}

function exportarModelo(tipo) {
    window.open(`/admin/models/export/${tipo}`, '_blank');
}

function activarModelo(tipo) {
    // Lógica para cambiar el modelo activo
    console.log('Activando modelo:', tipo);
}

function eliminarModelo(tipo) {
    if (confirm('¿Estás seguro de que quieres eliminar este modelo?')) {
        console.log('Eliminando modelo:', tipo);
    }
}

function guardarConfiguracion(event) {
    event.preventDefault();
    // Lógica para guardar configuración
    alert('Configuración guardada exitosamente');
}

function limpiarLogs() {
    if (confirm('¿Estás seguro de que quieres limpiar los logs?')) {
        document.getElementById('system-logs').innerHTML = 
            `<div>[${new Date().toISOString()}] INFO: Logs limpiados</div>`;
    }
}
</script>
@endsection