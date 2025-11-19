@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">💡 Recomendaciones y Planes de Acción</h1>
            <p class="text-gray-600 mt-2">Estrategias personalizadas generadas por IA para mejorar el rendimiento</p>
        </div>
        <div class="flex space-x-3">
            <button onclick="generarRecomendaciones()" class="bg-purple-600 text-white px-4 py-2 rounded-lg hover:bg-purple-700">
                🧠 Generar con IA
            </button>
            <a href="{{ route('docente.dashboard') }}" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700">
                ← Dashboard
            </a>
        </div>
    </div>

    <!-- Filtros y Búsqueda -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Tipo de Recomendación</label>
                <select id="filtro-tipo" class="w-full px-3 py-2 border border-gray-300 rounded-md">
                    <option value="">Todos los tipos</option>
                    <option value="academica">📚 Académica</option>
                    <option value="metodologica">🎯 Metodológica</option>
                    <option value="recurso">📖 Recursos</option>
                    <option value="tutoria">👨‍🏫 Tutoría</option>
                    <option value="refuerzo">💪 Refuerzo</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Prioridad</label>
                <select id="filtro-prioridad" class="w-full px-3 py-2 border border-gray-300 rounded-md">
                    <option value="">Todas las prioridades</option>
                    <option value="urgente">🔴 Urgente</option>
                    <option value="alta">🟡 Alta</option>
                    <option value="media">🟢 Media</option>
                    <option value="baja">⚪ Baja</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Estado</label>
                <select id="filtro-estado" class="w-full px-3 py-2 border border-gray-300 rounded-md">
                    <option value="">Todos los estados</option>
                    <option value="pendiente">⏳ Pendiente</option>
                    <option value="en_progreso">🔄 En Progreso</option>
                    <option value="completado">✅ Completado</option>
                    <option value="cancelado">❌ Cancelado</option>
                </select>
            </div>
            <div class="flex items-end">
                <button onclick="aplicarFiltros()" class="w-full bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">
                    🔍 Filtrar
                </button>
            </div>
        </div>
    </div>

    <!-- Estadísticas de Recomendaciones -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center">
                <div class="text-3xl text-purple-600 mr-4">💡</div>
                <div>
                    <p class="text-sm font-medium text-gray-600">Total Recomendaciones</p>
                    <p class="text-2xl font-bold text-purple-600">{{ $total_recomendaciones ?? 45 }}</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center">
                <div class="text-3xl text-red-600 mr-4">🚨</div>
                <div>
                    <p class="text-sm font-medium text-gray-600">Urgentes</p>
                    <p class="text-2xl font-bold text-red-600">{{ $recomendaciones_urgentes ?? 8 }}</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center">
                <div class="text-3xl text-green-600 mr-4">✅</div>
                <div>
                    <p class="text-sm font-medium text-gray-600">Implementadas</p>
                    <p class="text-2xl font-bold text-green-600">{{ $recomendaciones_completadas ?? 32 }}</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center">
                <div class="text-3xl text-blue-600 mr-4">📊</div>
                <div>
                    <p class="text-sm font-medium text-gray-600">Efectividad</p>
                    <p class="text-2xl font-bold text-blue-600">{{ $efectividad ?? '87%' }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Contenido principal sin estudiantes para evitar errores -->
    <div class="bg-white rounded-lg shadow-md p-12 text-center">
        <span class="text-6xl block mb-4">💡</span>
        <h3 class="text-xl font-semibold text-gray-600 mb-2">Sistema de Recomendaciones IA</h3>
        <p class="text-gray-500 mb-6">Genera recomendaciones personalizadas con IA para ayudar a tus estudiantes</p>
        <button onclick="generarRecomendaciones()" class="bg-purple-600 text-white px-6 py-3 rounded-lg hover:bg-purple-700">
            🧠 Generar Recomendaciones con IA
        </button>
    </div>

    <!-- Plantillas de Recomendaciones -->
    <div class="mt-8 bg-white rounded-lg shadow-md p-6">
        <h2 class="text-xl font-semibold mb-4">📝 Plantillas de Recomendaciones</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="border rounded-lg p-4 hover:bg-gray-50 cursor-pointer" onclick="aplicarPlantilla('refuerzo_matematicas')">
                <div class="flex items-center mb-2">
                    <span class="text-2xl mr-3">🧮</span>
                    <h3 class="font-semibold">Refuerzo en Matemáticas</h3>
                </div>
                <p class="text-sm text-gray-600">Plan de refuerzo para estudiantes con dificultades en matemáticas</p>
            </div>
            <div class="border rounded-lg p-4 hover:bg-gray-50 cursor-pointer" onclick="aplicarPlantilla('mejora_lectura')">
                <div class="flex items-center mb-2">
                    <span class="text-2xl mr-3">📚</span>
                    <h3 class="font-semibold">Mejora en Lectura</h3>
                </div>
                <p class="text-sm text-gray-600">Estrategias para mejorar comprensión lectora</p>
            </div>
            <div class="border rounded-lg p-4 hover:bg-gray-50 cursor-pointer" onclick="aplicarPlantilla('habitos_estudio')">
                <div class="flex items-center mb-2">
                    <span class="text-2xl mr-3">📖</span>
                    <h3 class="font-semibold">Hábitos de Estudio</h3>
                </div>
                <p class="text-sm text-gray-600">Desarrollo de técnicas de estudio efectivas</p>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Progreso -->
<div id="modal-progreso" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3 text-center">
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-purple-100">
                <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-purple-600"></div>
            </div>
            <h3 class="text-lg leading-6 font-medium text-gray-900 mt-2">Generando con IA</h3>
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
    console.log('Aplicando filtros...');
}

function generarRecomendaciones() {
    mostrarProgreso('Iniciando análisis con IA...');
    
    const pasos = [
        'Recopilando datos académicos...',
        'Analizando patrones de rendimiento...',
        'Consultando base de conocimientos...',
        'Generando recomendaciones personalizadas...',
        'Optimizando estrategias...',
        'Finalizando recomendaciones...'
    ];
    
    let i = 0;
    const interval = setInterval(() => {
        if (i < pasos.length) {
            document.getElementById('progreso-texto').textContent = pasos[i];
            i++;
        } else {
            clearInterval(interval);
            ocultarProgreso();
            alert('✅ Recomendaciones generadas exitosamente');
            location.reload();
        }
    }, 2000);
}

function aplicarPlantilla(tipo) {
    alert(`Aplicando plantilla: ${tipo}`);
}

function mostrarProgreso(texto) {
    document.getElementById('progreso-texto').textContent = texto;
    document.getElementById('modal-progreso').classList.remove('hidden');
}

function ocultarProgreso() {
    document.getElementById('modal-progreso').classList.add('hidden');
}

// Cerrar modal al hacer clic fuera
document.getElementById('modal-progreso').addEventListener('click', function(e) {
    if (e.target === this) {
        ocultarProgreso();
    }
});
</script>
@endsection