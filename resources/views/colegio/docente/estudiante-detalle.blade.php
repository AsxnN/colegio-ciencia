@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">👨‍🎓 Detalle del Estudiante</h1>
            <p class="text-gray-600 mt-2">{{ $estudiante->nombre ?? 'Sin nombre' }}</p>
        </div>
        <div class="flex space-x-3">
            @if(($estudiante->nivel_riesgo ?? '') == 'Alto')
            <a href="{{ route('docente.estudiante.plan-accion', $estudiante->id) }}" 
               class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700">
                🚨 Crear Plan de Acción
            </a>
            @endif
            <button onclick="generarPrediccion()" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
                🔮 Nueva Predicción
            </button>
            <a href="{{ route('docente.estudiantes') }}" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700">
                ← Volver a Lista
            </a>
        </div>
    </div>

    <!-- Información General -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        <!-- Datos Básicos -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-semibold mb-4">📋 Información Básica</h2>
            <div class="space-y-3">
                <div>
                    <label class="text-sm font-medium text-gray-600">Nombre Completo:</label>
                    <p class="text-sm">{{ $estudiante->nombre ?? 'Sin nombre' }}</p>
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-600">Sección:</label>
                    <p class="text-sm">{{ $estudiante->seccion ?? 'Sin sección' }}</p>
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-600">Código:</label>
                    <p class="text-sm">EST-{{ str_pad($estudiante->id ?? 0, 4, '0', STR_PAD_LEFT) }}</p>
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-600">Estado:</label>
                    <span class="px-2 py-1 text-xs bg-green-100 text-green-800 rounded">Activo</span>
                </div>
            </div>
        </div>

        <!-- Estado de Riesgo -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-semibold mb-4">⚠️ Estado de Riesgo</h2>
            @php
                $riesgo = $estudiante->nivel_riesgo ?? 'Sin datos';
                $color_bg = match($riesgo) {
                    'Alto' => 'bg-red-100 border-red-300',
                    'Medio' => 'bg-yellow-100 border-yellow-300',
                    'Bajo' => 'bg-green-100 border-green-300',
                    default => 'bg-gray-100 border-gray-300'
                };
                $color_text = match($riesgo) {
                    'Alto' => 'text-red-800',
                    'Medio' => 'text-yellow-800',
                    'Bajo' => 'text-green-800',
                    default => 'text-gray-800'
                };
            @endphp
            <div class="border rounded-lg p-4 {{ $color_bg }}">
                <div class="text-center">
                    <span class="text-3xl block mb-2">
                        @if($riesgo == 'Alto') 🔴
                        @elseif($riesgo == 'Medio') 🟡
                        @elseif($riesgo == 'Bajo') 🟢
                        @else ⚪
                        @endif
                    </span>
                    <h3 class="font-semibold {{ $color_text }}">{{ $riesgo }}</h3>
                    <p class="text-sm mt-2">
                        @if($riesgo == 'Alto')
                            Requiere intervención inmediata
                        @elseif($riesgo == 'Medio')
                            Monitoreo continuo necesario
                        @elseif($riesgo == 'Bajo')
                            Progreso satisfactorio
                        @else
                            Evaluación pendiente
                        @endif
                    </p>
                </div>
            </div>
        </div>

        <!-- Estadísticas Rápidas -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-semibold mb-4">📊 Estadísticas</h2>
            <div class="space-y-4">
                <div>
                    <div class="flex justify-between items-center mb-1">
                        <span class="text-sm text-gray-600">Promedio General</span>
                        <span class="text-sm font-semibold">14.5</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-blue-600 h-2 rounded-full" style="width: 72.5%"></div>
                    </div>
                </div>
                <div>
                    <div class="flex justify-between items-center mb-1">
                        <span class="text-sm text-gray-600">Asistencia</span>
                        <span class="text-sm font-semibold">85%</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-yellow-600 h-2 rounded-full" style="width: 85%"></div>
                    </div>
                </div>
                <div>
                    <div class="flex justify-between items-center mb-1">
                        <span class="text-sm text-gray-600">Participación</span>
                        <span class="text-sm font-semibold">78%</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-green-600 h-2 rounded-full" style="width: 78%"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Contenido con Pestañas -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <!-- Navegación de pestañas -->
        <div class="border-b border-gray-200">
            <nav class="-mb-px flex space-x-8 px-6">
                <button onclick="cambiarTab('predicciones')" 
                        class="tab-button py-4 px-1 border-b-2 font-medium text-sm border-blue-500 text-blue-600" 
                        data-tab="predicciones">
                    🔮 Predicciones
                </button>
                <button onclick="cambiarTab('notas')" 
                        class="tab-button py-4 px-1 border-b-2 font-medium text-sm border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300" 
                        data-tab="notas">
                    📝 Notas
                </button>
                <button onclick="cambiarTab('asistencias')" 
                        class="tab-button py-4 px-1 border-b-2 font-medium text-sm border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300" 
                        data-tab="asistencias">
                    📅 Asistencias
                </button>
                <button onclick="cambiarTab('recomendaciones')" 
                        class="tab-button py-4 px-1 border-b-2 font-medium text-sm border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300" 
                        data-tab="recomendaciones">
                    💡 Recomendaciones
                </button>
            </nav>
        </div>

        <!-- Contenido de las pestañas -->
        <div class="p-6">
            <!-- Tab: Predicciones -->
            <div id="tab-predicciones" class="tab-content">
                <h3 class="text-lg font-semibold mb-4">🔮 Historial de Predicciones</h3>
                <div class="space-y-4">
                    <div class="border rounded-lg p-4">
                        <div class="flex justify-between items-start mb-2">
                            <div>
                                <h4 class="font-medium">Predicción de Rendimiento - Matemáticas</h4>
                                <p class="text-sm text-gray-500">Generada hace 2 días</p>
                            </div>
                            <span class="px-2 py-1 text-xs bg-red-100 text-red-800 rounded">Riesgo Alto</span>
                        </div>
                        <p class="text-sm text-gray-700">
                            El estudiante presenta dificultades en conceptos básicos de álgebra. 
                            Se recomienda refuerzo inmediato.
                        </p>
                        <div class="mt-2">
                            <span class="text-xs text-gray-500">Confianza: 87%</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tab: Notas -->
            <div id="tab-notas" class="tab-content hidden">
                <h3 class="text-lg font-semibold mb-4">📝 Registro de Notas</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Materia</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Evaluación</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Nota</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Fecha</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr>
                                <td class="px-4 py-2 text-sm">Matemáticas</td>
                                <td class="px-4 py-2 text-sm">Examen Parcial</td>
                                <td class="px-4 py-2 text-sm font-medium text-red-600">12</td>
                                <td class="px-4 py-2 text-sm text-gray-500">15/11/2024</td>
                            </tr>
                            <tr>
                                <td class="px-4 py-2 text-sm">Ciencias</td>
                                <td class="px-4 py-2 text-sm">Proyecto</td>
                                <td class="px-4 py-2 text-sm font-medium text-green-600">16</td>
                                <td class="px-4 py-2 text-sm text-gray-500">10/11/2024</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Tab: Asistencias -->
            <div id="tab-asistencias" class="tab-content hidden">
                <h3 class="text-lg font-semibold mb-4">📅 Registro de Asistencias</h3>
                <div class="grid grid-cols-7 gap-2 mb-4">
                    <div class="text-center text-xs font-medium text-gray-500 py-2">L</div>
                    <div class="text-center text-xs font-medium text-gray-500 py-2">M</div>
                    <div class="text-center text-xs font-medium text-gray-500 py-2">X</div>
                    <div class="text-center text-xs font-medium text-gray-500 py-2">J</div>
                    <div class="text-center text-xs font-medium text-gray-500 py-2">V</div>
                    <div class="text-center text-xs font-medium text-gray-500 py-2">S</div>
                    <div class="text-center text-xs font-medium text-gray-500 py-2">D</div>
                    <!-- Simulación de calendario de asistencias -->
                    <div class="text-center text-xs py-2 bg-green-100 text-green-800 rounded">✓</div>
                    <div class="text-center text-xs py-2 bg-green-100 text-green-800 rounded">✓</div>
                    <div class="text-center text-xs py-2 bg-red-100 text-red-800 rounded">✗</div>
                    <div class="text-center text-xs py-2 bg-green-100 text-green-800 rounded">✓</div>
                    <div class="text-center text-xs py-2 bg-green-100 text-green-800 rounded">✓</div>
                    <div class="text-center text-xs py-2 bg-gray-100 text-gray-400 rounded">-</div>
                    <div class="text-center text-xs py-2 bg-gray-100 text-gray-400 rounded">-</div>
                </div>
            </div>

            <!-- Tab: Recomendaciones -->
            <div id="tab-recomendaciones" class="tab-content hidden">
                <h3 class="text-lg font-semibold mb-4">💡 Recomendaciones Activas</h3>
                <div class="space-y-4">
                    <div class="border border-red-200 rounded-lg p-4 bg-red-50">
                        <div class="flex justify-between items-start mb-2">
                            <div>
                                <h4 class="font-medium text-red-800">Refuerzo en Matemáticas</h4>
                                <p class="text-sm text-red-600">Prioridad Alta</p>
                            </div>
                            <span class="px-2 py-1 text-xs bg-red-200 text-red-800 rounded">Activa</span>
                        </div>
                        <p class="text-sm text-red-700 mb-2">
                            Implementar sesiones de refuerzo individualizadas enfocadas en álgebra básica.
                        </p>
                        <ul class="text-xs text-red-600 space-y-1">
                            <li>• Ejercicios de práctica diarios</li>
                            <li>• Apoyo con material visual</li>
                            <li>• Evaluación semanal de progreso</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function cambiarTab(tabName) {
    // Ocultar todas las pestañas
    document.querySelectorAll('.tab-content').forEach(tab => {
        tab.classList.add('hidden');
    });
    
    // Mostrar la pestaña seleccionada
    document.getElementById(`tab-${tabName}`).classList.remove('hidden');
    
    // Actualizar estilos de botones
    document.querySelectorAll('.tab-button').forEach(button => {
        button.className = 'tab-button py-4 px-1 border-b-2 font-medium text-sm border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300';
    });
    
    // Activar botón seleccionado
    document.querySelector(`[data-tab="${tabName}"]`).className = 'tab-button py-4 px-1 border-b-2 font-medium text-sm border-blue-500 text-blue-600';
}

function generarPrediccion() {
    alert('Generando nueva predicción para el estudiante...');
}
</script>
@endsection