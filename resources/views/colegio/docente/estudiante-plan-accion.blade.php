@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">🚨 Plan de Acción</h1>
            <p class="text-gray-600 mt-2">Estudiante: {{ $estudiante->nombre ?? 'Sin nombre' }} - {{ $estudiante->seccion ?? 'Sin sección' }}</p>
        </div>
        <div class="flex space-x-3">
            <a href="{{ route('docente.estudiante.detalle', $estudiante->id) }}" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700">
                ← Volver al Detalle
            </a>
        </div>
    </div>

    <!-- Alerta de Riesgo -->
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
        <div class="flex items-center">
            <span class="text-2xl mr-2">⚠️</span>
            <div>
                <strong class="font-bold">Estudiante en Riesgo Alto</strong>
                <span class="block sm:inline">Este estudiante requiere intervención inmediata para mejorar su rendimiento académico.</span>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Información del Estudiante -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-semibold mb-4">📋 Información del Estudiante</h2>
            <div class="space-y-3">
                <div>
                    <label class="text-sm font-medium text-gray-600">Nombre:</label>
                    <p class="text-sm">{{ $estudiante->nombre ?? 'Sin nombre' }}</p>
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-600">Sección:</label>
                    <p class="text-sm">{{ $estudiante->seccion ?? 'Sin sección' }}</p>
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-600">Nivel de Riesgo:</label>
                    <span class="px-2 py-1 text-xs bg-red-100 text-red-800 rounded">{{ $estudiante->nivel_riesgo ?? 'Alto' }}</span>
                </div>
            </div>

            <!-- Problemas Identificados -->
            <div class="mt-6">
                <h3 class="text-lg font-semibold mb-3">🔍 Problemas Identificados</h3>
                <ul class="space-y-2">
                    @foreach($estudiante->problemas_identificados ?? [] as $problema)
                    <li class="flex items-start">
                        <span class="text-red-500 mr-2">•</span>
                        <span class="text-sm text-gray-700">{{ $problema }}</span>
                    </li>
                    @endforeach
                </ul>
            </div>
        </div>

        <!-- Formulario del Plan de Acción -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-semibold mb-4">📝 Crear Plan de Acción</h2>
                
                <form action="{{ route('docente.estudiante.plan-accion.crear', $estudiante->id) }}" method="POST">
                    @csrf
                    
                    <!-- Objetivo Principal -->
                    <div class="mb-6">
                        <label for="objetivo" class="block text-sm font-medium text-gray-700 mb-2">
                            🎯 Objetivo Principal del Plan
                        </label>
                        <input type="text" 
                               id="objetivo" 
                               name="objetivo" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" 
                               placeholder="Ej: Mejorar rendimiento en matemáticas y aumentar asistencia"
                               required>
                        @error('objetivo')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Descripción del Plan -->
                    <div class="mb-6">
                        <label for="descripcion" class="block text-sm font-medium text-gray-700 mb-2">
                            📄 Descripción Detallada
                        </label>
                        <textarea id="descripcion" 
                                  name="descripcion" 
                                  rows="4" 
                                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" 
                                  placeholder="Describe las estrategias y metodologías que se implementarán para ayudar al estudiante..."
                                  required></textarea>
                        @error('descripcion')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Fechas -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                        <div>
                            <label for="fecha_inicio" class="block text-sm font-medium text-gray-700 mb-2">
                                📅 Fecha de Inicio
                            </label>
                            <input type="date" 
                                   id="fecha_inicio" 
                                   name="fecha_inicio" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                   value="{{ date('Y-m-d') }}"
                                   required>
                            @error('fecha_inicio')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="fecha_fin" class="block text-sm font-medium text-gray-700 mb-2">
                                📅 Fecha de Finalización
                            </label>
                            <input type="date" 
                                   id="fecha_fin" 
                                   name="fecha_fin" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                   value="{{ date('Y-m-d', strtotime('+1 month')) }}"
                                   required>
                            @error('fecha_fin')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Actividades Específicas -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            ✅ Actividades Específicas
                        </label>
                        <div id="actividades-container">
                            <div class="actividad-item flex items-center space-x-2 mb-3">
                                <input type="text" 
                                       name="actividades[]" 
                                       class="flex-1 px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" 
                                       placeholder="Describe una actividad específica del plan...">
                                <button type="button" 
                                        onclick="agregarActividad()" 
                                        class="bg-green-600 text-white px-3 py-2 rounded hover:bg-green-700">
                                    +
                                </button>
                            </div>
                        </div>
                        <button type="button" 
                                onclick="agregarActividad()" 
                                class="text-blue-600 hover:text-blue-800 text-sm">
                            + Agregar otra actividad
                        </button>
                    </div>

                    <!-- Responsables -->
                    <div class="mb-6">
                        <label for="responsables" class="block text-sm font-medium text-gray-700 mb-2">
                            👥 Responsables
                        </label>
                        <input type="text" 
                               id="responsables" 
                               name="responsables" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" 
                               placeholder="Docente, coordinador académico, psicopedagogo..."
                               value="{{ auth()->user()->name }}">
                    </div>

                    <!-- Indicadores de Éxito -->
                    <div class="mb-6">
                        <label for="indicadores" class="block text-sm font-medium text-gray-700 mb-2">
                            📊 Indicadores de Éxito
                        </label>
                        <textarea id="indicadores" 
                                  name="indicadores" 
                                  rows="3" 
                                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" 
                                  placeholder="Ej: Mejorar promedio a 14 puntos, alcanzar 90% de asistencia, completar tareas asignadas..."></textarea>
                    </div>

                    <!-- Botones -->
                    <div class="flex justify-end space-x-3">
                        <a href="{{ route('docente.estudiante.detalle', $estudiante->id) }}" 
                           class="bg-gray-300 text-gray-700 px-6 py-2 rounded-md hover:bg-gray-400">
                            Cancelar
                        </a>
                        <button type="submit" 
                                class="bg-red-600 text-white px-6 py-2 rounded-md hover:bg-red-700">
                            💾 Crear Plan de Acción
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Plantillas Sugeridas -->
    <div class="mt-8 bg-white rounded-lg shadow-md p-6">
        <h2 class="text-xl font-semibold mb-4">📝 Plantillas Sugeridas</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="border rounded-lg p-4 hover:bg-gray-50 cursor-pointer" onclick="aplicarPlantilla('matematicas')">
                <div class="flex items-center mb-2">
                    <span class="text-2xl mr-3">🧮</span>
                    <h3 class="font-semibold">Refuerzo en Matemáticas</h3>
                </div>
                <p class="text-sm text-gray-600">Plan específico para estudiantes con dificultades en matemáticas</p>
            </div>
            <div class="border rounded-lg p-4 hover:bg-gray-50 cursor-pointer" onclick="aplicarPlantilla('asistencia')">
                <div class="flex items-center mb-2">
                    <span class="text-2xl mr-3">📅</span>
                    <h3 class="font-semibold">Mejora de Asistencia</h3>
                </div>
                <p class="text-sm text-gray-600">Estrategias para reducir el ausentismo escolar</p>
            </div>
            <div class="border rounded-lg p-4 hover:bg-gray-50 cursor-pointer" onclick="aplicarPlantilla('integral')">
                <div class="flex items-center mb-2">
                    <span class="text-2xl mr-3">🎯</span>
                    <h3 class="font-semibold">Plan Integral</h3>
                </div>
                <p class="text-sm text-gray-600">Abordaje completo para múltiples dificultades</p>
            </div>
        </div>
    </div>
</div>

<script>
function agregarActividad() {
    const container = document.getElementById('actividades-container');
    const nuevaActividad = document.createElement('div');
    nuevaActividad.className = 'actividad-item flex items-center space-x-2 mb-3';
    nuevaActividad.innerHTML = `
        <input type="text" 
               name="actividades[]" 
               class="flex-1 px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" 
               placeholder="Describe una actividad específica del plan...">
        <button type="button" 
                onclick="this.parentElement.remove()" 
                class="bg-red-600 text-white px-3 py-2 rounded hover:bg-red-700">
            -
        </button>
    `;
    container.appendChild(nuevaActividad);
}

function aplicarPlantilla(tipo) {
    const plantillas = {
        matematicas: {
            objetivo: 'Mejorar el rendimiento en matemáticas y desarrollar habilidades de resolución de problemas',
            descripcion: 'Implementar un plan de refuerzo académico personalizado que incluya sesiones individuales de tutoría, ejercicios de práctica diarios y evaluaciones semanales de progreso.',
            actividades: [
                'Sesiones de tutoría individualizada 3 veces por semana',
                'Ejercicios de práctica diarios con retroalimentación inmediata',
                'Uso de material didáctico visual y manipulativo',
                'Evaluación semanal del progreso y ajuste de estrategias'
            ],
            indicadores: 'Mejorar el promedio en matemáticas a 14 puntos, completar el 90% de las tareas asignadas, demostrar comprensión de conceptos básicos en evaluaciones semanales'
        },
        asistencia: {
            objetivo: 'Mejorar la asistencia escolar y reducir el ausentismo del estudiante',
            descripcion: 'Desarrollar estrategias para motivar al estudiante a asistir regularmente, identificar barreras que impiden la asistencia y crear un sistema de apoyo familiar.',
            actividades: [
                'Reunión semanal con el estudiante y los padres',
                'Sistema de seguimiento diario de asistencia',
                'Actividades motivacionales en el aula',
                'Coordinación con trabajo social si es necesario'
            ],
            indicadores: 'Alcanzar 95% de asistencia mensual, reducir faltas injustificadas, mejorar puntualidad en un 90%'
        },
        integral: {
            objetivo: 'Abordar integralmente las dificultades académicas y conductuales del estudiante',
            descripcion: 'Plan multidisciplinario que aborda aspectos académicos, emocionales y sociales, involucrando a docentes, familia y especialistas según sea necesario.',
            actividades: [
                'Evaluación psicopedagógica integral',
                'Plan de refuerzo académico personalizado',
                'Sesiones de orientación psicológica',
                'Talleres de habilidades sociales',
                'Seguimiento familiar continuo'
            ],
            indicadores: 'Mejorar promedio general a 14 puntos, alcanzar 90% de asistencia, mostrar mejoras en relaciones interpersonales y conducta en el aula'
        }
    };

    const plantilla = plantillas[tipo];
    if (plantilla) {
        document.getElementById('objetivo').value = plantilla.objetivo;
        document.getElementById('descripcion').value = plantilla.descripcion;
        document.getElementById('indicadores').value = plantilla.indicadores;

        // Limpiar actividades existentes
        document.getElementById('actividades-container').innerHTML = '';

        // Agregar actividades de la plantilla
        plantilla.actividades.forEach(actividad => {
            const container = document.getElementById('actividades-container');
            const nuevaActividad = document.createElement('div');
            nuevaActividad.className = 'actividad-item flex items-center space-x-2 mb-3';
            nuevaActividad.innerHTML = `
                <input type="text" 
                       name="actividades[]" 
                       class="flex-1 px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" 
                       value="${actividad}">
                <button type="button" 
                        onclick="this.parentElement.remove()" 
                        class="bg-red-600 text-white px-3 py-2 rounded hover:bg-red-700">
                    -
                </button>
            `;
            container.appendChild(nuevaActividad);
        });

        alert(`Plantilla "${tipo}" aplicada exitosamente`);
    }
}
</script>
@endsection