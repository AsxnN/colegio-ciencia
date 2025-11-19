@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Header -->
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-800">👨‍🏫 Panel Docente</h1>
        <p class="text-gray-600 mt-2">Bienvenido, {{ auth()->user()->name }}. Gestiona tus cursos y monitorea el rendimiento estudiantil.</p>
    </div>

    <!-- Alertas Tempranas -->
    @if(isset($alertas_criticas) && $alertas_criticas > 0)
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
        <div class="flex items-center">
            <span class="text-2xl mr-2">🚨</span>
            <div>
                <strong class="font-bold">¡Alerta Crítica!</strong>
                <span class="block sm:inline">Tienes {{ $alertas_criticas }} estudiantes en riesgo alto que requieren intervención inmediata.</span>
            </div>
            <a href="{{ route('docente.predicciones') }}" class="ml-auto bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700">
                Ver Alertas
            </a>
        </div>
    </div>
    @endif

    <!-- Estadísticas Rápidas -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Total Estudiantes -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center">
                <div class="text-3xl text-blue-600 mr-4">👥</div>
                <div>
                    <p class="text-sm font-medium text-gray-600">Estudiantes Total</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $total_estudiantes ?? 0 }}</p>
                </div>
            </div>
        </div>

        <!-- Riesgo Alto -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center">
                <div class="text-3xl text-red-600 mr-4">🔴</div>
                <div>
                    <p class="text-sm font-medium text-gray-600">Riesgo Alto</p>
                    <p class="text-2xl font-bold text-red-600">{{ $estudiantes_alto_riesgo ?? 0 }}</p>
                </div>
            </div>
        </div>

        <!-- Riesgo Medio -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center">
                <div class="text-3xl text-yellow-600 mr-4">🟡</div>
                <div>
                    <p class="text-sm font-medium text-gray-600">Riesgo Medio</p>
                    <p class="text-2xl font-bold text-yellow-600">{{ $estudiantes_medio_riesgo ?? 0 }}</p>
                </div>
            </div>
        </div>

        <!-- Bajo Riesgo -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center">
                <div class="text-3xl text-green-600 mr-4">🟢</div>
                <div>
                    <p class="text-sm font-medium text-gray-600">Bajo Riesgo</p>
                    <p class="text-2xl font-bold text-green-600">{{ $estudiantes_bajo_riesgo ?? 0 }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Acciones Rápidas -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <!-- Panel Izquierdo: Acciones -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-semibold mb-4">🎯 Acciones Rápidas</h2>
            <div class="space-y-3">
                <a href="{{ route('docente.predicciones') }}" class="flex items-center p-3 bg-blue-50 rounded-lg hover:bg-blue-100 transition-colors">
                    <span class="text-2xl mr-3">🔮</span>
                    <div>
                        <p class="font-medium">Ver Predicciones</p>
                        <p class="text-sm text-gray-600">Análisis completo de rendimiento</p>
                    </div>
                </a>
                <a href="{{ route('docente.cursos') }}" class="flex items-center p-3 bg-green-50 rounded-lg hover:bg-green-100 transition-colors">
                    <span class="text-2xl mr-3">📚</span>
                    <div>
                        <p class="font-medium">Gestionar Cursos</p>
                        <p class="text-sm text-gray-600">Administrar grupos y secciones</p>
                    </div>
                </a>
                <a href="{{ route('docente.estudiantes') }}" class="flex items-center p-3 bg-purple-50 rounded-lg hover:bg-purple-100 transition-colors">
                    <span class="text-2xl mr-3">👨‍🎓</span>
                    <div>
                        <p class="font-medium">Lista de Estudiantes</p>
                        <p class="text-sm text-gray-600">Ver detalles y historial</p>
                    </div>
                </a>
                <a href="{{ route('docente.actividades') }}" class="flex items-center p-3 bg-orange-50 rounded-lg hover:bg-orange-100 transition-colors">
                    <span class="text-2xl mr-3">📋</span>
                    <div>
                        <p class="font-medium">Gestión de Actividades</p>
                        <p class="text-sm text-gray-600">Planificar y hacer seguimiento</p>
                    </div>
                </a>
            </div>
        </div>

        <!-- Panel Derecho: Cursos -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-semibold mb-4">📖 Mis Cursos</h2>
            @if(isset($cursos) && $cursos->count() > 0)
                <div class="space-y-3">
                    @foreach($cursos as $curso)
                    <div class="flex items-center justify-between p-3 border rounded-lg">
                        <div class="flex items-center">
                            <span class="text-2xl mr-3">📕</span>
                            <div>
                                <p class="font-medium">{{ $curso->nombre }}</p>
                                <p class="text-sm text-gray-600">{{ $curso->estudiantes_count ?? 0 }} estudiantes</p>
                            </div>
                        </div>
                        <a href="{{ route('docente.cursos') }}" class="text-blue-600 hover:text-blue-800">
                            Ver →
                        </a>
                    </div>
                    @endforeach
                </div>
            @else
                <div class="text-center text-gray-500 py-8">
                    <span class="text-4xl block mb-2">📚</span>
                    <p>No tienes cursos asignados</p>
                    <p class="text-sm">Contacta al administrador para asignaciones</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Estudiantes con Alertas -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <h2 class="text-xl font-semibold mb-4">⚠️ Estudiantes que Requieren Atención</h2>
        @if(isset($estudiantes_atencion) && $estudiantes_atencion->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full table-auto">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Estudiante</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Curso</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Riesgo</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Última Predicción</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Acción</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($estudiantes_atencion as $estudiante)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-2">
                                <div class="flex items-center">
                                    <div class="w-8 h-8 bg-gray-300 rounded-full flex items-center justify-center mr-3">
                                        {{ substr($estudiante->usuario->name, 0, 1) }}
                                    </div>
                                    <div>
                                        <p class="font-medium">{{ $estudiante->usuario->name }}</p>
                                        <p class="text-sm text-gray-600">{{ $estudiante->codigo ?? 'N/A' }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-2 text-sm text-gray-900">
                                {{ $estudiante->seccion->nombre ?? 'Sin sección' }}
                            </td>
                            <td class="px-4 py-2">
                                @php
                                    $riesgo = $estudiante->ultima_prediccion->nivel_riesgo ?? 'Sin datos';
                                    $color = match($riesgo) {
                                        'Alto' => 'bg-red-100 text-red-800',
                                        'Medio' => 'bg-yellow-100 text-yellow-800',
                                        'Bajo' => 'bg-green-100 text-green-800',
                                        default => 'bg-gray-100 text-gray-800'
                                    };
                                @endphp
                                <span class="px-2 py-1 text-xs rounded-full {{ $color }}">
                                    {{ $riesgo }}
                                </span>
                            </td>
                            <td class="px-4 py-2 text-sm text-gray-600">
                                {{ $estudiante->ultima_prediccion->created_at?->diffForHumans() ?? 'Sin predicciones' }}
                            </td>
                            <td class="px-4 py-2">
                                <a href="{{ route('docente.estudiante.detalle', $estudiante->id) }}" 
                                   class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                    Ver Detalle
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center text-gray-500 py-8">
                <span class="text-4xl block mb-2">✅</span>
                <p>¡Excelente! No hay estudiantes en riesgo crítico</p>
                <p class="text-sm">Todos tus estudiantes están progresando bien</p>
            </div>
        @endif
    </div>
</div>
@endsection
