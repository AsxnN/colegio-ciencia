@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">📊 Historial Académico Detallado</h1>
            <p class="text-gray-600 mt-2">{{ $estudiante->usuario->name ?? 'Estudiante' }} - {{ $estudiante->seccion->nombre ?? 'Sin sección' }}</p>
        </div>
        <div class="flex space-x-3">
            <button onclick="exportarHistorial()" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700">
                📄 Exportar PDF
            </button>
            <a href="{{ route('docente.estudiantes') }}" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700">
                ← Volver
            </a>
        </div>
    </div>

    <!-- Información del Estudiante -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <div class="text-center">
                <div class="w-20 h-20 mx-auto bg-gradient-to-r from-blue-400 to-purple-500 rounded-full flex items-center justify-center text-white text-2xl font-bold mb-3">
                    {{ substr($estudiante->usuario->name ?? 'N', 0, 1) }}
                </div>
                <h3 class="font-semibold text-gray-800">{{ $estudiante->usuario->name ?? 'Sin nombre' }}</h3>
                <p class="text-sm text-gray-600">{{ $estudiante->codigo ?? 'Sin código' }}</p>
            </div>
            <div>
                <h4 class="font-semibold text-gray-700 mb-2">Información Académica</h4>
                <p class="text-sm"><strong>Sección:</strong> {{ $estudiante->seccion->nombre ?? 'N/A' }}</p>
                <p class="text-sm"><strong>Grado:</strong> {{ $estudiante->seccion->grado ?? 'N/A' }}°</p>
                <p class="text-sm"><strong>Año Académico:</strong> {{ date('Y') }}</p>
            </div>
            <div>
                <h4 class="font-semibold text-gray-700 mb-2">Rendimiento General</h4>
                <p class="text-sm"><strong>Promedio:</strong> 
                    <span class="font-bold {{ $estudiante->promedio_general >= 14 ? 'text-green-600' : 'text-red-600' }}">
                        {{ number_format($estudiante->promedio_general ?? 0, 2) }}
                    </span>
                </p>
                <p class="text-sm"><strong>Asistencia:</strong> 
                    <span class="font-bold {{ $estudiante->porcentaje_asistencia >= 85 ? 'text-green-600' : 'text-red-600' }}">
                        {{ $estudiante->porcentaje_asistencia ?? 0 }}%
                    </span>
                </p>
            </div>
            <div>
                <h4 class="font-semibold text-gray-700 mb-2">Estado Actual</h4>
                @php
                    $riesgo = $estudiante->nivel_riesgo ?? 'Sin datos';
                    $color = match($riesgo) {
                        'Alto' => 'bg-red-100 text-red-800',
                        'Medio' => 'bg-yellow-100 text-yellow-800',
                        'Bajo' => 'bg-green-100 text-green-800',
                        default => 'bg-gray-100 text-gray-800'
                    };
                @endphp
                <span class="inline-block px-3 py-1 text-sm rounded-full {{ $color }} mb-2">
                    {{ $riesgo }}
                </span>
                <p class="text-sm text-gray-600">Última actualización: {{ $estudiante->ultima_actualizacion ?? 'N/A' }}</p>
            </div>
        </div>
    </div>

    <!-- Pestañas de Navegación -->
    <div class="bg-white rounded-lg shadow-md mb-6">
        <div class="border-b border-gray-200">
            <nav class="-mb-px flex space-x-8 px-6">
                <a href="#notas" onclick="mostrarSeccion('notas')" class="tab-link border-b-2 border-blue-500 py-4 px-1 text-blue-600 font-medium" data-tab="notas">
                    📊 Notas por Bimestre
                </a>
                <a href="#asistencias" onclick="mostrarSeccion('asistencias')" class="tab-link border-b-2 border-transparent py-4 px-1 text-gray-500 hover:text-gray-700" data-tab="asistencias">
                    📅 Asistencias
                </a>
                <a href="#comportamiento" onclick="mostrarSeccion('comportamiento')" class="tab-link border-b-2 border-transparent py-4 px-1 text-gray-500 hover:text-gray-700" data-tab="comportamiento">
                    👤 Comportamiento
                </a>
                <a href="#observaciones" onclick="mostrarSeccion('observaciones')" class="tab-link border-b-2 border-transparent py-4 px-1 text-gray-500 hover:text-gray-700" data-tab="observaciones">
                    📝 Observaciones
                </a>
            </nav>
        </div>
    </div>

    <!-- Contenido de las Pestañas -->

    <!-- Sección: Notas por Bimestre -->
    <div id="seccion-notas" class="tab-content">
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <h3 class="text-xl font-semibold mb-4">📊 Notas por Curso y Bimestre</h3>
            
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Curso</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Bim 1</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Bim 2</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Bim 3</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Bim 4</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Promedio</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Estado</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($estudiante->notas ?? [] as $nota)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <span class="text-2xl mr-3">📚</span>
                                    <div>
                                        <div class="text-sm font-medium text-gray-900">{{ $nota->curso->nombre ?? 'N/A' }}</div>
                                        <div class="text-sm text-gray-500">{{ $nota->curso->codigo ?? '' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="text-sm font-medium {{ $nota->bimestre1 >= 14 ? 'text-green-600' : 'text-red-600' }}">
                                    {{ $nota->bimestre1 ?? '--' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="text-sm font-medium {{ $nota->bimestre2 >= 14 ? 'text-green-600' : 'text-red-600' }}">
                                    {{ $nota->bimestre2 ?? '--' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="text-sm font-medium {{ $nota->bimestre3 >= 14 ? 'text-green-600' : 'text-red-600' }}">
                                    {{ $nota->bimestre3 ?? '--' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="text-sm font-medium {{ $nota->bimestre4 >= 14 ? 'text-green-600' : 'text-red-600' }}">
                                    {{ $nota->bimestre4 ?? '--' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="text-sm font-bold {{ $nota->promedio_final >= 14 ? 'text-green-600' : 'text-red-600' }}">
                                    {{ number_format($nota->promedio_final ?? 0, 2) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if($nota->promedio_final >= 14)
                                    <span class="px-2 py-1 text-xs bg-green-100 text-green-800 rounded-full">Aprobado</span>
                                @else
                                    <span class="px-2 py-1 text-xs bg-red-100 text-red-800 rounded-full">En Riesgo</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                                No hay notas registradas
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Gráfico de Evolución -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h3 class="text-xl font-semibold mb-4">📈 Evolución del Promedio</h3>
            <div class="h-64 bg-gray-100 rounded-lg flex items-center justify-center">
                <div class="text-center text-gray-500">
                    <span class="text-4xl block mb-2">📊</span>
                    <p>Gráfico de evolución</p>
                    <p class="text-sm">(Implementar con Chart.js)</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Sección: Asistencias -->
    <div id="seccion-asistencias" class="tab-content hidden">
        <div class="bg-white rounded-lg shadow-md p-6">
            <h3 class="text-xl font-semibold mb-4">📅 Registro de Asistencias</h3>
            
            <!-- Resumen de Asistencias -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                <div class="bg-green-50 p-4 rounded-lg">
                    <div class="text-2xl font-bold text-green-600">{{ $estudiante->asistencias_presentes ?? 0 }}</div>
                    <div class="text-sm text-gray-600">Días Presentes</div>
                </div>
                <div class="bg-red-50 p-4 rounded-lg">
                    <div class="text-2xl font-bold text-red-600">{{ $estudiante->asistencias_ausentes ?? 0 }}</div>
                    <div class="text-sm text-gray-600">Días Ausentes</div>
                </div>
                <div class="bg-yellow-50 p-4 rounded-lg">
                    <div class="text-2xl font-bold text-yellow-600">{{ $estudiante->asistencias_tardanzas ?? 0 }}</div>
                    <div class="text-sm text-gray-600">Tardanzas</div>
                </div>
                <div class="bg-blue-50 p-4 rounded-lg">
                    <div class="text-2xl font-bold text-blue-600">{{ $estudiante->porcentaje_asistencia ?? 0 }}%</div>
                    <div class="text-sm text-gray-600">% Asistencia</div>
                </div>
            </div>

            <!-- Calendario de Asistencias -->
            <div class="bg-gray-100 p-4 rounded-lg">
                <div class="text-center text-gray-500">
                    <span class="text-4xl block mb-2">📅</span>
                    <p>Calendario de Asistencias</p>
                    <p class="text-sm">(Implementar calendario interactivo)</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Sección: Comportamiento -->
    <div id="seccion-comportamiento" class="tab-content hidden">
        <div class="bg-white rounded-lg shadow-md p-6">
            <h3 class="text-xl font-semibold mb-4">👤 Evaluación de Comportamiento</h3>
            
            <!-- Indicadores de Comportamiento -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                <div class="text-center p-4 border rounded-lg">
                    <div class="text-3xl mb-2">😊</div>
                    <div class="text-lg font-semibold">Responsabilidad</div>
                    <div class="text-2xl font-bold text-green-600">B</div>
                </div>
                <div class="text-center p-4 border rounded-lg">
                    <div class="text-3xl mb-2">🤝</div>
                    <div class="text-lg font-semibold">Respeto</div>
                    <div class="text-2xl font-bold text-blue-600">A</div>
                </div>
                <div class="text-center p-4 border rounded-lg">
                    <div class="text-3xl mb-2">⚡</div>
                    <div class="text-lg font-semibold">Puntualidad</div>
                    <div class="text-2xl font-bold text-yellow-600">C</div>
                </div>
            </div>

            <!-- Incidentes -->
            <div class="border-t pt-6">
                <h4 class="font-semibold mb-4">Registro de Incidentes</h4>
                <div class="space-y-3">
                    <div class="border-l-4 border-yellow-400 pl-4 py-2">
                        <div class="text-sm text-gray-600">15 Nov 2024</div>
                        <div class="font-medium">Tardanza reiterada</div>
                        <div class="text-sm text-gray-700">Llegó 15 minutos tarde por tercera vez esta semana</div>
                    </div>
                    <div class="border-l-4 border-green-400 pl-4 py-2">
                        <div class="text-sm text-gray-600">10 Nov 2024</div>
                        <div class="font-medium">Participación destacada</div>
                        <div class="text-sm text-gray-700">Excelente participación en clase de matemáticas</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Sección: Observaciones -->
    <div id="seccion-observaciones" class="tab-content hidden">
        <div class="bg-white rounded-lg shadow-md p-6">
            <h3 class="text-xl font-semibold mb-4">📝 Observaciones del Docente</h3>
            
            <!-- Formulario para Nueva Observación -->
            <div class="border-b pb-6 mb-6">
                <h4 class="font-semibold mb-3">Agregar Nueva Observación</h4>
                <form class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Tipo de Observación</label>
                        <select class="w-full px-3 py-2 border border-gray-300 rounded-md">
                            <option>Académica</option>
                            <option>Comportamental</option>
                            <option>Social</option>
                            <option>Familiar</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Observación</label>
                        <textarea class="w-full px-3 py-2 border border-gray-300 rounded-md" rows="3" 
                                  placeholder="Describe la observación..."></textarea>
                    </div>
                    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">
                        Guardar Observación
                    </button>
                </form>
            </div>

            <!-- Lista de Observaciones -->
            <div class="space-y-4">
                <div class="border rounded-lg p-4">
                    <div class="flex justify-between items-start mb-2">
                        <span class="px-2 py-1 text-xs bg-blue-100 text-blue-800 rounded">Académica</span>
                        <span class="text-sm text-gray-500">14 Nov 2024</span>
                    </div>
                    <p class="text-gray-700">El estudiante muestra mejoras significativas en matemáticas después de las sesiones de refuerzo.</p>
                    <div class="text-sm text-gray-500 mt-2">Por: Prof. García</div>
                </div>
                
                <div class="border rounded-lg p-4">
                    <div class="flex justify-between items-start mb-2">
                        <span class="px-2 py-1 text-xs bg-yellow-100 text-yellow-800 rounded">Comportamental</span>
                        <span class="text-sm text-gray-500">12 Nov 2024</span>
                    </div>
                    <p class="text-gray-700">Se distrae con facilidad durante las clases. Recomiendo hablar con los padres.</p>
                    <div class="text-sm text-gray-500 mt-2">Por: Prof. López</div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function mostrarSeccion(seccion) {
    // Ocultar todas las secciones
    document.querySelectorAll('.tab-content').forEach(el => {
        el.classList.add('hidden');
    });
    
    // Quitar clase activa de todas las pestañas
    document.querySelectorAll('.tab-link').forEach(el => {
        el.classList.remove('border-blue-500', 'text-blue-600');
        el.classList.add('border-transparent', 'text-gray-500');
    });
    
    // Mostrar la sección seleccionada
    document.getElementById('seccion-' + seccion).classList.remove('hidden');
    
    // Activar la pestaña seleccionada
    const tab = document.querySelector(`[data-tab="${seccion}"]`);
    tab.classList.remove('border-transparent', 'text-gray-500');
    tab.classList.add('border-blue-500', 'text-blue-600');
}

function exportarHistorial() {
    alert('Generando PDF del historial académico...');
    // Implementar exportación real
}
</script>
@endsection