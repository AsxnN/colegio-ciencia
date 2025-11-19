@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">📚 Gestión de Cursos</h1>
            <p class="text-gray-600 mt-2">Administra tus grupos y secciones</p>
        </div>
        <a href="{{ route('docente.dashboard') }}" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700">
            ← Volver al Dashboard
        </a>
    </div>

    <!-- Filtros y Búsqueda -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Buscar Curso</label>
                <input type="text" id="filtro-nombre" placeholder="Nombre del curso..." 
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Nivel</label>
                <select id="filtro-nivel" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">Todos los niveles</option>
                    <option value="Primaria">Primaria</option>
                    <option value="Secundaria">Secundaria</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Grado</label>
                <select id="filtro-grado" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">Todos los grados</option>
                    @for($i = 1; $i <= 6; $i++)
                        <option value="{{ $i }}">{{ $i }}° Grado</option>
                    @endfor
                </select>
            </div>
            <div class="flex items-end">
                <button onclick="aplicarFiltros()" class="w-full bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">
                    🔍 Filtrar
                </button>
            </div>
        </div>
    </div>

    <!-- Lista de Cursos -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($cursos ?? [] as $curso)
        <div class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition-shadow">
            <!-- Header del Curso -->
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center">
                    <span class="text-3xl mr-3">
                        @switch($curso->nombre)
                            @case('Matemáticas')
                                🧮
                                @break
                            @case('Comunicación')
                                📝
                                @break
                            @case('Ciencias')
                                🧪
                                @break
                            @case('Historia')
                                📚
                                @break
                            @default
                                📖
                        @endswitch
                    </span>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-800">{{ $curso->nombre }}</h3>
                        <p class="text-sm text-gray-600">{{ $curso->codigo ?? 'Sin código' }}</p>
                    </div>
                </div>
                <!-- Indicador de Riesgo -->
                @php
                    $estudiantes_riesgo = $curso->estudiantes_alto_riesgo ?? 0;
                    $color_riesgo = $estudiantes_riesgo > 3 ? 'bg-red-100 text-red-800' : ($estudiantes_riesgo > 0 ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800');
                @endphp
                <span class="px-2 py-1 text-xs rounded-full {{ $color_riesgo }}">
                    {{ $estudiantes_riesgo }} en riesgo
                </span>
            </div>

            <!-- Estadísticas del Curso -->
            <div class="grid grid-cols-2 gap-4 mb-4">
                <div class="text-center p-3 bg-gray-50 rounded-lg">
                    <p class="text-2xl font-bold text-blue-600">{{ $curso->estudiantes_count ?? 0 }}</p>
                    <p class="text-xs text-gray-600">Estudiantes</p>
                </div>
                <div class="text-center p-3 bg-gray-50 rounded-lg">
                    <p class="text-2xl font-bold text-green-600">{{ number_format($curso->promedio_general ?? 0, 1) }}</p>
                    <p class="text-xs text-gray-600">Promedio</p>
                </div>
            </div>

            <!-- Progreso -->
            <div class="mb-4">
                <div class="flex justify-between text-sm mb-1">
                    <span>Progreso del Curso</span>
                    <span>{{ $curso->progreso ?? 75 }}%</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2">
                    <div class="bg-blue-600 h-2 rounded-full" style="width: {{ $curso->progreso ?? 75 }}%"></div>
                </div>
            </div>

            <!-- Acciones -->
            <div class="space-y-2">
                <a href="{{ route('docente.curso.detalle', $curso->id) }}" 
                   class="w-full bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 text-center block">
                    Ver Detalle Completo
                </a>
                <div class="flex space-x-2">
                    <a href="{{ route('docente.curso.estudiantes', $curso->id) }}" 
                       class="flex-1 bg-gray-100 text-gray-700 px-3 py-2 rounded-md hover:bg-gray-200 text-center text-sm">
                        👥 Estudiantes
                    </a>
                    <a href="{{ route('docente.curso.predicciones', $curso->id) }}" 
                       class="flex-1 bg-gray-100 text-gray-700 px-3 py-2 rounded-md hover:bg-gray-200 text-center text-sm">
                        🔮 Predicciones
                    </a>
                </div>
            </div>
        </div>
        @empty
        <div class="col-span-full text-center py-12">
            <span class="text-6xl block mb-4">📚</span>
            <h3 class="text-xl font-semibold text-gray-600 mb-2">No hay cursos asignados</h3>
            <p class="text-gray-500">Contacta al administrador para asignarte cursos</p>
        </div>
        @endforelse
    </div>
</div>

<script>
function aplicarFiltros() {
    const nombre = document.getElementById('filtro-nombre').value.toLowerCase();
    const nivel = document.getElementById('filtro-nivel').value;
    const grado = document.getElementById('filtro-grado').value;
    
    // Implementar lógica de filtrado aquí
    console.log('Filtros aplicados:', { nombre, nivel, grado });
    // En una implementación real, esto haría una petición AJAX o filtraría los elementos DOM
}
</script>
@endsection