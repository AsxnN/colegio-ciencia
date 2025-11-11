<!-- filepath: c:\laragon\www\colegio-ciencia\resources\views\colegio\predicciones\docente.blade.php -->
@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-800">🎯 Predicciones de Estudiantes</h1>
        <p class="text-gray-600 mt-2">Analiza el rendimiento predicho de tus estudiantes</p>
    </div>

    <!-- Filtros -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Sección</label>
                <select name="seccion_id" class="w-full px-3 py-2 border border-gray-300 rounded-md">
                    <option value="">Todas las secciones</option>
                    @foreach($secciones as $seccion)
                        <option value="{{ $seccion->id }}">{{ $seccion->nombre }}</option>
                    @endforeach
                </select>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Curso</label>
                <select name="curso_id" class="w-full px-3 py-2 border border-gray-300 rounded-md">
                    <option value="">Todos los cursos</option>
                    @foreach($cursos as $curso)
                        <option value="{{ $curso->id }}">{{ $curso->nombre }}</option>
                    @endforeach
                </select>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Estado de Riesgo</label>
                <select name="riesgo" class="w-full px-3 py-2 border border-gray-300 rounded-md">
                    <option value="">Todos</option>
                    <option value="alto">🚨 Alto Riesgo (< 12)</option>
                    <option value="medio">⚠️ Riesgo Medio (12-14)</option>
                    <option value="bajo">✅ Bajo Riesgo (> 14)</option>
                </select>
            </div>
            
            <div class="flex items-end">
                <button type="button" onclick="filtrarEstudiantes()" 
                        class="w-full bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">
                    🔍 Filtrar
                </button>
            </div>
        </div>
    </div>

    <!-- Estadísticas Rápidas -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-red-50 p-4 rounded-lg border-l-4 border-red-500">
            <div class="text-red-800 text-sm font-medium">Alto Riesgo</div>
            <div class="text-2xl font-bold text-red-600">{{ $estudiantes_alto_riesgo }}</div>
        </div>
        <div class="bg-yellow-50 p-4 rounded-lg border-l-4 border-yellow-500">
            <div class="text-yellow-800 text-sm font-medium">Riesgo Medio</div>
            <div class="text-2xl font-bold text-yellow-600">{{ $estudiantes_medio_riesgo }}</div>
        </div>
        <div class="bg-green-50 p-4 rounded-lg border-l-4 border-green-500">
            <div class="text-green-800 text-sm font-medium">Bajo Riesgo</div>
            <div class="text-2xl font-bold text-green-600">{{ $estudiantes_bajo_riesgo }}</div>
        </div>
        <div class="bg-blue-50 p-4 rounded-lg border-l-4 border-blue-500">
            <div class="text-blue-800 text-sm font-medium">Promedio General</div>
            <div class="text-2xl font-bold text-blue-600">{{ number_format($promedio_general, 2) }}</div>
        </div>
    </div>

    <!-- Lista de Estudiantes -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
            <h2 class="text-lg font-semibold">📊 Lista de Estudiantes</h2>
            <div class="flex space-x-2">
                <button onclick="generarPrediccionesGrupales()" 
                        class="bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700 text-sm">
                    🔮 Generar Predicciones Grupales
                </button>
                <button onclick="exportarReporte()" 
                        class="bg-purple-600 text-white px-4 py-2 rounded-md hover:bg-purple-700 text-sm">
                    📄 Exportar Reporte
                </button>
            </div>
        </div>
        
        <div class="overflow-x-auto">
            <table class="min-w-full table-auto">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Estudiante</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Sección</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Predicción</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Última Act.</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Asistencia</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Estado</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Acciones</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200" id="estudiantes-tabla">
                    @foreach($estudiantes as $estudiante)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3">
                            <div class="flex items-center">
                                <img class="h-8 w-8 rounded-full mr-3" 
                                     src="{{ $estudiante->usuario->profile_photo_url ?? '/default-avatar.png' }}" 
                                     alt="{{ $estudiante->usuario->name }}">
                                <div>
                                    <div class="text-sm font-medium text-gray-900">
                                        {{ $estudiante->usuario->name }}
                                    </div>
                                    <div class="text-sm text-gray-500">
                                        {{ $estudiante->usuario->correo }}
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-600">
                            {{ $estudiante->seccion->nombre ?? 'Sin sección' }}
                        </td>
                        <td class="px-4 py-3">
                            @if($estudiante->ultima_prediccion)
                                <span class="px-2 py-1 rounded-full text-xs font-medium
                                    @if($estudiante->ultima_prediccion->nota_predicha >= 16) bg-green-100 text-green-800
                                    @elseif($estudiante->ultima_prediccion->nota_predicha >= 14) bg-blue-100 text-blue-800  
                                    @elseif($estudiante->ultima_prediccion->nota_predicha >= 12) bg-yellow-100 text-yellow-800
                                    @else bg-red-100 text-red-800 @endif">
                                    {{ number_format($estudiante->ultima_prediccion->nota_predicha, 2) }}
                                </span>
                            @else
                                <span class="text-gray-400 text-sm">Sin predicción</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-600">
                            {{ $estudiante->ultima_prediccion?->created_at?->diffForHumans() ?? 'Nunca' }}
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-600">
                            {{ $estudiante->porcentaje_asistencia ?? 'N/A' }}%
                        </td>
                        <td class="px-4 py-3">
                            @if($estudiante->ultima_prediccion)
                                @if($estudiante->ultima_prediccion->nota_predicha >= 14)
                                    <span class="text-green-600 text-sm">✅ Excelente</span>
                                @elseif($estudiante->ultima_prediccion->nota_predicha >= 12)
                                    <span class="text-yellow-600 text-sm">⚠️ Promedio</span>
                                @else
                                    <span class="text-red-600 text-sm">🚨 Riesgo</span>
                                @endif
                            @else
                                <span class="text-gray-400 text-sm">Sin evaluar</span>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex space-x-2">
                                <button onclick="verDetalleEstudiante({{ $estudiante->id }})" 
                                        class="text-blue-600 hover:text-blue-900 text-sm">
                                    👁️ Ver
                                </button>
                                <button onclick="generarPrediccionIndividual({{ $estudiante->id }})" 
                                        class="text-green-600 hover:text-green-900 text-sm">
                                    🔮 Predecir
                                </button>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
function filtrarEstudiantes() {
    // Lógica de filtrado con AJAX
    console.log('Filtrar estudiantes');
}

function generarPrediccionesGrupales() {
    // Generar predicciones para todos los estudiantes
    console.log('Generar predicciones grupales');
}

function exportarReporte() {
    // Exportar reporte en PDF/Excel
    window.open('{{ route("predicciones.export") }}', '_blank');
}

function verDetalleEstudiante(estudianteId) {
    // Abrir modal con detalle del estudiante
    window.open(`/colegio/estudiantes/${estudianteId}`, '_blank');
}

function generarPrediccionIndividual(estudianteId) {
    // Generar predicción individual
    console.log('Generar predicción para estudiante:', estudianteId);
}
</script>
@endsection