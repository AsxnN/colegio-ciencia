@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">👨‍🎓 Lista de Estudiantes</h1>
            <p class="text-gray-600 mt-2">Gestiona y monitorea el progreso de tus estudiantes</p>
        </div>
        <div class="flex space-x-3">
            <button onclick="generarPrediccionesMasivas()" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
                🔮 Generar Predicciones
            </button>
            <a href="{{ route('docente.dashboard') }}" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700">
                ← Volver
            </a>
        </div>
    </div>

    <!-- Estadísticas Rápidas -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow-md p-4">
            <div class="flex items-center">
                <span class="text-2xl mr-3">👥</span>
                <div>
                    <p class="text-sm text-gray-600">Total Estudiantes</p>
                    <p class="text-xl font-bold">{{ $estudiantes->count() }}</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow-md p-4">
            <div class="flex items-center">
                <span class="text-2xl mr-3">🔴</span>
                <div>
                    <p class="text-sm text-gray-600">Riesgo Alto</p>
                    <p class="text-xl font-bold text-red-600">{{ $estudiantes->where('nivel_riesgo', 'Alto')->count() }}</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow-md p-4">
            <div class="flex items-center">
                <span class="text-2xl mr-3">🟡</span>
                <div>
                    <p class="text-sm text-gray-600">Riesgo Medio</p>
                    <p class="text-xl font-bold text-yellow-600">{{ $estudiantes->where('nivel_riesgo', 'Medio')->count() }}</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow-md p-4">
            <div class="flex items-center">
                <span class="text-2xl mr-3">🟢</span>
                <div>
                    <p class="text-sm text-gray-600">Bajo Riesgo</p>
                    <p class="text-xl font-bold text-green-600">{{ $estudiantes->where('nivel_riesgo', 'Bajo')->count() }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtros y Búsqueda -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Buscar Estudiante</label>
                <input type="text" id="filtro-nombre" placeholder="Nombre del estudiante..." 
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Sección</label>
                <select id="filtro-seccion" class="w-full px-3 py-2 border border-gray-300 rounded-md">
                    <option value="">Todas las secciones</option>
                    @foreach($secciones ?? [] as $seccion)
                        <option value="{{ $seccion->id }}">{{ $seccion->nombre }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Nivel de Riesgo</label>
                <select id="filtro-riesgo" class="w-full px-3 py-2 border border-gray-300 rounded-md">
                    <option value="">Todos los niveles</option>
                    <option value="Alto">🔴 Riesgo Alto</option>
                    <option value="Medio">🟡 Riesgo Medio</option>
                    <option value="Bajo">🟢 Riesgo Bajo</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Promedio</label>
                <select id="filtro-promedio" class="w-full px-3 py-2 border border-gray-300 rounded-md">
                    <option value="">Todos</option>
                    <option value="excelente">Excelente (16-20)</option>
                    <option value="bueno">Bueno (14-15)</option>
                    <option value="regular">Regular (11-13)</option>
                    <option value="deficiente">Deficiente (0-10)</option>
                </select>
            </div>
            <div class="flex items-end">
                <button onclick="aplicarFiltros()" class="w-full bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">
                    🔍 Filtrar
                </button>
            </div>
        </div>
    </div>

    <!-- Lista de Estudiantes -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Estudiante
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Sección
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Promedio
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Asistencia
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Riesgo
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Última Predicción
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Acciones
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($estudiantes ?? [] as $estudiante)
                    <tr class="hover:bg-gray-50 {{ $estudiante->nivel_riesgo == 'Alto' ? 'border-l-4 border-red-500' : '' }}">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10">
                                    <div class="h-10 w-10 rounded-full bg-gradient-to-r from-blue-400 to-purple-500 flex items-center justify-center text-white font-bold">
                                        {{ substr($estudiante->nombre ?? 'N', 0, 1) }}
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">
                                        {{ $estudiante->nombre ?? 'Sin nombre' }}
                                    </div>
                                    <div class="text-sm text-gray-500">
                                        EST-{{ str_pad($estudiante->id ?? 0, 4, '0', STR_PAD_LEFT) }}
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ $estudiante->seccion ?? 'Sin sección' }}</div>
                            <div class="text-sm text-gray-500">6° Grado</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @php
                                $promedio = $estudiante->promedio_general ?? 0;
                                $color_promedio = $promedio >= 16 ? 'text-green-600' : ($promedio >= 14 ? 'text-blue-600' : ($promedio >= 11 ? 'text-yellow-600' : 'text-red-600'));
                            @endphp
                            <div class="text-sm font-medium {{ $color_promedio }}">
                                {{ number_format($promedio, 2) }}
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @php
                                $asistencia = $estudiante->porcentaje_asistencia ?? 0;
                                $color_asistencia = $asistencia >= 90 ? 'text-green-600' : ($asistencia >= 75 ? 'text-yellow-600' : 'text-red-600');
                            @endphp
                            <div class="text-sm {{ $color_asistencia }}">
                                {{ $asistencia }}%
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @php
                                $riesgo = $estudiante->nivel_riesgo ?? 'Sin datos';
                                $color = match($riesgo) {
                                    'Alto' => 'bg-red-100 text-red-800',
                                    'Medio' => 'bg-yellow-100 text-yellow-800',
                                    'Bajo' => 'bg-green-100 text-green-800',
                                    default => 'bg-gray-100 text-gray-800'
                                };
                            @endphp
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $color }}">
                                {{ $riesgo }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $estudiante->ultima_prediccion_fecha ?? 'Sin predicciones' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                            <a href="{{ route('docente.estudiante.detalle', $estudiante->id) }}" 
                               class="text-indigo-600 hover:text-indigo-900">
                                Ver Detalle
                            </a>
                            <button onclick="generarPrediccion({{ $estudiante->id }})" 
                                    class="text-blue-600 hover:text-blue-900">
                                🔮 Predecir
                            </button>
                            @if($estudiante->nivel_riesgo == 'Alto')
                            <a href="{{ route('docente.estudiante.plan-accion', $estudiante->id) }}" 
                               class="text-red-600 hover:text-red-900">
                                🚨 Plan Acción
                            </a>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center">
                            <span class="text-4xl block mb-4">👨‍🎓</span>
                            <h3 class="text-lg font-medium text-gray-600 mb-2">No hay estudiantes</h3>
                            <p class="text-gray-500">No tienes estudiantes asignados aún</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Paginación -->
    @if(method_exists($estudiantes ?? collect(), 'hasPages') && $estudiantes->hasPages())
    <div class="mt-6">
        {{ $estudiantes->links() }}
    </div>
    @endif
</div>

<!-- Modal de Progreso -->
<div id="modal-progreso" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3 text-center">
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-blue-100">
                <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
            </div>
            <h3 class="text-lg leading-6 font-medium text-gray-900 mt-2">Generando Predicción</h3>
            <div class="mt-2 px-7 py-3">
                <p class="text-sm text-gray-500" id="progreso-texto">
                    Analizando datos del estudiante...
                </p>
            </div>
        </div>
    </div>
</div>

<script>
function aplicarFiltros() {
    // Implementar lógica de filtrado
    console.log('Aplicando filtros...');
}

function generarPrediccion(estudianteId) {
    document.getElementById('modal-progreso').classList.remove('hidden');
    
    // Simular progreso
    const textos = [
        'Analizando datos del estudiante...',
        'Consultando historial académico...',
        'Calculando predicciones...',
        'Generando recomendaciones...'
    ];
    
    let i = 0;
    const interval = setInterval(() => {
        document.getElementById('progreso-texto').textContent = textos[i];
        i++;
        if (i >= textos.length) {
            clearInterval(interval);
            // Redirigir o actualizar vista
            setTimeout(() => {
                document.getElementById('modal-progreso').classList.add('hidden');
                window.location.href = `/docente/estudiantes/${estudianteId}`;
            }, 1000);
        }
    }, 1500);
}

function generarPrediccionesMasivas() {
    if (confirm('¿Deseas generar predicciones para todos los estudiantes? Esto puede tomar varios minutos.')) {
        document.getElementById('modal-progreso').classList.remove('hidden');
        document.getElementById('progreso-texto').textContent = 'Generando predicciones masivas...';
        
        // En una implementación real, esto haría una petición AJAX
        setTimeout(() => {
            document.getElementById('modal-progreso').classList.add('hidden');
            alert('Predicciones generadas exitosamente');
        }, 5000);
    }
}

// Cerrar modal al hacer clic fuera
document.getElementById('modal-progreso').addEventListener('click', function(e) {
    if (e.target === this) {
        this.classList.add('hidden');
    }
});
</script>
@endsection