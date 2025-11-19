@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">📋 Gestión de Actividades</h1>
            <p class="text-gray-600 mt-2">Crea, programa y supervisa actividades educativas</p>
        </div>
        <div class="flex space-x-3">
            <button onclick="crearActividad()" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700">
                ➕ Nueva Actividad
            </button>
            <a href="{{ route('docente.dashboard') }}" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700">
                ← Dashboard
            </a>
        </div>
    </div>

    <!-- Filtros y Vista -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-6 gap-4 items-end">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Tipo de Actividad</label>
                <select id="filtro-tipo" class="w-full px-3 py-2 border border-gray-300 rounded-md">
                    <option value="">Todos los tipos</option>
                    <option value="examen">📝 Examen</option>
                    <option value="tarea">📚 Tarea</option>
                    <option value="proyecto">🎯 Proyecto</option>
                    <option value="laboratorio">🧪 Laboratorio</option>
                    <option value="presentacion">💼 Presentación</option>
                    <option value="quiz">❓ Quiz</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Materia</label>
                <select id="filtro-materia" class="w-full px-3 py-2 border border-gray-300 rounded-md">
                    <option value="">Todas las materias</option>
                    <option value="matematicas">🧮 Matemáticas</option>
                    <option value="lenguaje">📖 Lenguaje</option>
                    <option value="ciencias">🔬 Ciencias</option>
                    <option value="historia">🏛️ Historia</option>
                    <option value="geografia">🌍 Geografía</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Estado</label>
                <select id="filtro-estado" class="w-full px-3 py-2 border border-gray-300 rounded-md">
                    <option value="">Todos los estados</option>
                    <option value="programada">⏰ Programada</option>
                    <option value="en_progreso">🔄 En Progreso</option>
                    <option value="completada">✅ Completada</option>
                    <option value="cancelada">❌ Cancelada</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Fecha Desde</label>
                <input type="date" id="fecha-desde" class="w-full px-3 py-2 border border-gray-300 rounded-md">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Fecha Hasta</label>
                <input type="date" id="fecha-hasta" class="w-full px-3 py-2 border border-gray-300 rounded-md">
            </div>
            <div>
                <button onclick="aplicarFiltros()" class="w-full bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">
                    🔍 Filtrar
                </button>
            </div>
        </div>
        
        <!-- Vista de Calendario/Lista -->
        <div class="mt-4 border-t pt-4">
            <div class="flex justify-between items-center">
                <div class="flex space-x-2">
                    <button onclick="cambiarVista('lista')" id="btn-lista" class="px-4 py-2 bg-blue-600 text-white rounded">
                        📋 Lista
                    </button>
                    <button onclick="cambiarVista('calendario')" id="btn-calendario" class="px-4 py-2 bg-gray-300 text-gray-700 rounded">
                        📅 Calendario
                    </button>
                </div>
                <div class="text-sm text-gray-600">
                    <span id="total-actividades">{{ $total_actividades ?? 24 }}</span> actividades encontradas
                </div>
            </div>
        </div>
    </div>

    <!-- Estadísticas Rápidas -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center">
                <div class="text-3xl text-blue-600 mr-4">📋</div>
                <div>
                    <p class="text-sm font-medium text-gray-600">Total Actividades</p>
                    <p class="text-2xl font-bold text-blue-600">{{ $actividades_totales ?? 24 }}</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center">
                <div class="text-3xl text-orange-600 mr-4">⏰</div>
                <div>
                    <p class="text-sm font-medium text-gray-600">Pendientes</p>
                    <p class="text-2xl font-bold text-orange-600">{{ $actividades_pendientes ?? 8 }}</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center">
                <div class="text-3xl text-green-600 mr-4">✅</div>
                <div>
                    <p class="text-sm font-medium text-gray-600">Completadas</p>
                    <p class="text-2xl font-bold text-green-600">{{ $actividades_completadas ?? 14 }}</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center">
                <div class="text-3xl text-purple-600 mr-4">📈</div>
                <div>
                    <p class="text-sm font-medium text-gray-600">Promedio Entrega</p>
                    <p class="text-2xl font-bold text-purple-600">{{ $promedio_entrega ?? '85%' }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Vista Lista de Actividades -->
    <div id="vista-lista" class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="px-6 py-4 bg-gray-50 border-b">
            <h3 class="text-lg font-semibold text-gray-800">Lista de Actividades</h3>
        </div>
        
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actividad</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tipo</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Materia</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fecha</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Estado</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Participación</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Acciones</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200" id="tabla-actividades">
                    <!-- Actividades de ejemplo -->
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4">
                            <div>
                                <div class="text-sm font-medium text-gray-900">Examen Parcial Álgebra</div>
                                <div class="text-sm text-gray-500">Evaluación de conceptos básicos de álgebra</div>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-900">
                            <span class="px-2 py-1 text-xs bg-red-100 text-red-800 rounded">📝 Examen</span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-900">🧮 Matemáticas</td>
                        <td class="px-6 py-4 text-sm text-gray-900">
                            <div>15/11/2024</div>
                            <div class="text-xs text-gray-500">10:00 AM</div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 text-xs bg-orange-100 text-orange-800 rounded">⏰ Programada</span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-900">
                            <div class="flex items-center">
                                <div class="w-8 h-2 bg-gray-200 rounded-full mr-2">
                                    <div class="w-6 h-2 bg-green-500 rounded-full"></div>
                                </div>
                                <span>28/32</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-sm font-medium space-x-2">
                            <button onclick="verDetalleActividad(1)" class="text-blue-600 hover:text-blue-800">👁️</button>
                            <button onclick="editarActividad(1)" class="text-green-600 hover:text-green-800">✏️</button>
                            <button onclick="eliminarActividad(1)" class="text-red-600 hover:text-red-800">🗑️</button>
                        </td>
                    </tr>
                    
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4">
                            <div>
                                <div class="text-sm font-medium text-gray-900">Proyecto Ciencias Naturales</div>
                                <div class="text-sm text-gray-500">Investigación sobre el sistema solar</div>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-900">
                            <span class="px-2 py-1 text-xs bg-purple-100 text-purple-800 rounded">🎯 Proyecto</span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-900">🔬 Ciencias</td>
                        <td class="px-6 py-4 text-sm text-gray-900">
                            <div>20/11/2024</div>
                            <div class="text-xs text-gray-500">Entrega</div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 text-xs bg-yellow-100 text-yellow-800 rounded">🔄 En Progreso</span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-900">
                            <div class="flex items-center">
                                <div class="w-8 h-2 bg-gray-200 rounded-full mr-2">
                                    <div class="w-4 h-2 bg-yellow-500 rounded-full"></div>
                                </div>
                                <span>18/32</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-sm font-medium space-x-2">
                            <button onclick="verDetalleActividad(2)" class="text-blue-600 hover:text-blue-800">👁️</button>
                            <button onclick="editarActividad(2)" class="text-green-600 hover:text-green-800">✏️</button>
                            <button onclick="eliminarActividad(2)" class="text-red-600 hover:text-red-800">🗑️</button>
                        </td>
                    </tr>
                    
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4">
                            <div>
                                <div class="text-sm font-medium text-gray-900">Quiz Comprensión Lectora</div>
                                <div class="text-sm text-gray-500">Evaluación rápida de lectura crítica</div>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-900">
                            <span class="px-2 py-1 text-xs bg-blue-100 text-blue-800 rounded">❓ Quiz</span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-900">📖 Lenguaje</td>
                        <td class="px-6 py-4 text-sm text-gray-900">
                            <div>12/11/2024</div>
                            <div class="text-xs text-gray-500">Completado</div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 text-xs bg-green-100 text-green-800 rounded">✅ Completada</span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-900">
                            <div class="flex items-center">
                                <div class="w-8 h-2 bg-gray-200 rounded-full mr-2">
                                    <div class="w-8 h-2 bg-green-500 rounded-full"></div>
                                </div>
                                <span>32/32</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-sm font-medium space-x-2">
                            <button onclick="verDetalleActividad(3)" class="text-blue-600 hover:text-blue-800">👁️</button>
                            <button onclick="verResultados(3)" class="text-purple-600 hover:text-purple-800">📊</button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        
        <!-- Paginación -->
        <div class="px-6 py-4 bg-gray-50 border-t">
            <div class="flex items-center justify-between">
                <div class="text-sm text-gray-700">
                    Mostrando <span class="font-medium">1</span> a <span class="font-medium">10</span> de <span class="font-medium">24</span> actividades
                </div>
                <div class="flex space-x-2">
                    <button class="px-3 py-1 border border-gray-300 rounded text-sm hover:bg-gray-100">Anterior</button>
                    <button class="px-3 py-1 bg-blue-600 text-white rounded text-sm">1</button>
                    <button class="px-3 py-1 border border-gray-300 rounded text-sm hover:bg-gray-100">2</button>
                    <button class="px-3 py-1 border border-gray-300 rounded text-sm hover:bg-gray-100">3</button>
                    <button class="px-3 py-1 border border-gray-300 rounded text-sm hover:bg-gray-100">Siguiente</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Vista Calendario (oculta por defecto) -->
    <div id="vista-calendario" class="bg-white rounded-lg shadow-md p-6 hidden">
        <div class="text-center py-12">
            <span class="text-6xl block mb-4">📅</span>
            <h3 class="text-xl font-semibold text-gray-600 mb-2">Vista de Calendario</h3>
            <p class="text-gray-500 mb-6">Próximamente: Vista calendario interactiva con arrastrar y soltar</p>
            <button onclick="cambiarVista('lista')" class="bg-blue-600 text-white px-4 py-2 rounded">
                ← Volver a Lista
            </button>
        </div>
    </div>
</div>

<!-- Modal para Nueva/Editar Actividad -->
<div id="modal-actividad" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden">
    <div class="relative top-10 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-medium text-gray-900" id="modal-title">➕ Nueva Actividad</h3>
                <button onclick="cerrarModalActividad()" class="text-gray-400 hover:text-gray-600">
                    <span class="text-2xl">×</span>
                </button>
            </div>
            
            <form id="form-actividad">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Título de la Actividad</label>
                        <input type="text" id="actividad-titulo" class="w-full px-3 py-2 border border-gray-300 rounded-md" 
                               placeholder="Ej: Examen Parcial de Matemáticas" required>
                    </div>
                    
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Descripción</label>
                        <textarea id="actividad-descripcion" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-md" 
                                  placeholder="Describe los objetivos y contenido de la actividad..."></textarea>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Tipo de Actividad</label>
                        <select id="actividad-tipo" class="w-full px-3 py-2 border border-gray-300 rounded-md" required>
                            <option value="">Seleccionar tipo</option>
                            <option value="examen">📝 Examen</option>
                            <option value="tarea">📚 Tarea</option>
                            <option value="proyecto">🎯 Proyecto</option>
                            <option value="laboratorio">🧪 Laboratorio</option>
                            <option value="presentacion">💼 Presentación</option>
                            <option value="quiz">❓ Quiz</option>
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Materia</label>
                        <select id="actividad-materia" class="w-full px-3 py-2 border border-gray-300 rounded-md" required>
                            <option value="">Seleccionar materia</option>
                            <option value="matematicas">🧮 Matemáticas</option>
                            <option value="lenguaje">📖 Lenguaje</option>
                            <option value="ciencias">🔬 Ciencias</option>
                            <option value="historia">🏛️ Historia</option>
                            <option value="geografia">🌍 Geografía</option>
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Fecha</label>
                        <input type="date" id="actividad-fecha" class="w-full px-3 py-2 border border-gray-300 rounded-md" required>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Hora</label>
                        <input type="time" id="actividad-hora" class="w-full px-3 py-2 border border-gray-300 rounded-md">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Duración (minutos)</label>
                        <input type="number" id="actividad-duracion" class="w-full px-3 py-2 border border-gray-300 rounded-md" 
                               placeholder="60" min="15" max="300">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Puntaje Máximo</label>
                        <input type="number" id="actividad-puntaje" class="w-full px-3 py-2 border border-gray-300 rounded-md" 
                               placeholder="100" min="1" step="0.1">
                    </div>
                </div>
                
                <div class="mt-6 flex justify-end space-x-3">
                    <button type="button" onclick="cerrarModalActividad()" 
                            class="bg-gray-300 text-gray-700 px-4 py-2 rounded hover:bg-gray-400">
                        Cancelar
                    </button>
                    <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">
                        💾 Guardar Actividad
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal de Detalle de Actividad -->
<div id="modal-detalle" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden">
    <div class="relative top-10 mx-auto p-5 border w-11/12 md:w-4/5 lg:w-3/5 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-medium text-gray-900">👁️ Detalle de Actividad</h3>
                <button onclick="cerrarModalDetalle()" class="text-gray-400 hover:text-gray-600">
                    <span class="text-2xl">×</span>
                </button>
            </div>
            
            <div id="contenido-detalle">
                <!-- El contenido se carga dinámicamente -->
            </div>
        </div>
    </div>
</div>

<script>
let vistaActual = 'lista';
let actividadEditando = null;

function cambiarVista(vista) {
    vistaActual = vista;
    
    if (vista === 'lista') {
        document.getElementById('vista-lista').classList.remove('hidden');
        document.getElementById('vista-calendario').classList.add('hidden');
        document.getElementById('btn-lista').className = 'px-4 py-2 bg-blue-600 text-white rounded';
        document.getElementById('btn-calendario').className = 'px-4 py-2 bg-gray-300 text-gray-700 rounded';
    } else {
        document.getElementById('vista-lista').classList.add('hidden');
        document.getElementById('vista-calendario').classList.remove('hidden');
        document.getElementById('btn-lista').className = 'px-4 py-2 bg-gray-300 text-gray-700 rounded';
        document.getElementById('btn-calendario').className = 'px-4 py-2 bg-blue-600 text-white rounded';
    }
}

function aplicarFiltros() {
    console.log('Aplicando filtros de actividades...');
    // Implementar filtrado
}

function crearActividad() {
    actividadEditando = null;
    document.getElementById('modal-title').textContent = '➕ Nueva Actividad';
    document.getElementById('form-actividad').reset();
    document.getElementById('modal-actividad').classList.remove('hidden');
}

function editarActividad(id) {
    actividadEditando = id;
    document.getElementById('modal-title').textContent = '✏️ Editar Actividad';
    
    // Cargar datos de la actividad (simulado)
    document.getElementById('actividad-titulo').value = 'Examen Parcial Álgebra';
    document.getElementById('actividad-descripcion').value = 'Evaluación de conceptos básicos de álgebra';
    document.getElementById('actividad-tipo').value = 'examen';
    document.getElementById('actividad-materia').value = 'matematicas';
    document.getElementById('actividad-fecha').value = '2024-11-15';
    document.getElementById('actividad-hora').value = '10:00';
    
    document.getElementById('modal-actividad').classList.remove('hidden');
}

function verDetalleActividad(id) {
    const contenido = `
        <div class="space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h4 class="text-lg font-semibold mb-4">📋 Información General</h4>
                    <div class="space-y-3">
                        <div>
                            <span class="text-sm font-medium text-gray-600">Título:</span>
                            <p class="text-sm">Examen Parcial Álgebra</p>
                        </div>
                        <div>
                            <span class="text-sm font-medium text-gray-600">Tipo:</span>
                            <span class="px-2 py-1 text-xs bg-red-100 text-red-800 rounded ml-2">📝 Examen</span>
                        </div>
                        <div>
                            <span class="text-sm font-medium text-gray-600">Materia:</span>
                            <p class="text-sm">🧮 Matemáticas</p>
                        </div>
                        <div>
                            <span class="text-sm font-medium text-gray-600">Fecha y Hora:</span>
                            <p class="text-sm">15/11/2024 - 10:00 AM</p>
                        </div>
                        <div>
                            <span class="text-sm font-medium text-gray-600">Duración:</span>
                            <p class="text-sm">90 minutos</p>
                        </div>
                        <div>
                            <span class="text-sm font-medium text-gray-600">Puntaje Máximo:</span>
                            <p class="text-sm">100 puntos</p>
                        </div>
                    </div>
                </div>
                
                <div>
                    <h4 class="text-lg font-semibold mb-4">📊 Estadísticas</h4>
                    <div class="space-y-3">
                        <div>
                            <span class="text-sm font-medium text-gray-600">Participación:</span>
                            <div class="flex items-center mt-1">
                                <div class="w-32 h-3 bg-gray-200 rounded-full mr-3">
                                    <div class="w-28 h-3 bg-green-500 rounded-full"></div>
                                </div>
                                <span class="text-sm">28/32 (87.5%)</span>
                            </div>
                        </div>
                        <div>
                            <span class="text-sm font-medium text-gray-600">Estado:</span>
                            <span class="px-2 py-1 text-xs bg-orange-100 text-orange-800 rounded ml-2">⏰ Programada</span>
                        </div>
                        <div>
                            <span class="text-sm font-medium text-gray-600">Recordatorios Enviados:</span>
                            <p class="text-sm">2 recordatorios</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <div>
                <h4 class="text-lg font-semibold mb-4">📝 Descripción</h4>
                <p class="text-sm text-gray-700 bg-gray-50 p-4 rounded">
                    Evaluación de conceptos básicos de álgebra incluyendo: operaciones con polinomios, 
                    factorización, ecuaciones lineales y cuadráticas, sistemas de ecuaciones.
                </p>
            </div>
            
            <div>
                <h4 class="text-lg font-semibold mb-4">👥 Estudiantes Participantes</h4>
                <div class="max-h-32 overflow-y-auto">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                        <div class="text-sm p-2 bg-gray-50 rounded">✅ Juan Pérez</div>
                        <div class="text-sm p-2 bg-gray-50 rounded">✅ María González</div>
                        <div class="text-sm p-2 bg-gray-50 rounded">✅ Carlos López</div>
                        <div class="text-sm p-2 bg-red-50 rounded">❌ Ana Martínez (ausente)</div>
                        <div class="text-sm p-2 bg-gray-50 rounded">✅ Pedro Sánchez</div>
                        <div class="text-sm p-2 bg-gray-50 rounded">✅ Laura Rivera</div>
                    </div>
                </div>
            </div>
            
            <div class="flex justify-end space-x-3 pt-4 border-t">
                <button onclick="editarActividad(${id})" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                    ✏️ Editar
                </button>
                <button onclick="enviarRecordatorio(${id})" class="bg-yellow-600 text-white px-4 py-2 rounded hover:bg-yellow-700">
                    📧 Enviar Recordatorio
                </button>
                <button onclick="cerrarModalDetalle()" class="bg-gray-300 text-gray-700 px-4 py-2 rounded hover:bg-gray-400">
                    Cerrar
                </button>
            </div>
        </div>
    `;
    
    document.getElementById('contenido-detalle').innerHTML = contenido;
    document.getElementById('modal-detalle').classList.remove('hidden');
}

function verResultados(id) {
    alert(`Ver resultados de actividad ${id}`);
}

function eliminarActividad(id) {
    if (confirm('¿Estás seguro de que deseas eliminar esta actividad?')) {
        alert(`Actividad ${id} eliminada`);
        // Implementar eliminación
    }
}

function enviarRecordatorio(id) {
    alert(`Recordatorio enviado para actividad ${id}`);
}

function cerrarModalActividad() {
    document.getElementById('modal-actividad').classList.add('hidden');
    actividadEditando = null;
}

function cerrarModalDetalle() {
    document.getElementById('modal-detalle').classList.add('hidden');
}

// Event listener para el formulario de actividad
document.getElementById('form-actividad').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const titulo = document.getElementById('actividad-titulo').value;
    const tipo = document.getElementById('actividad-tipo').value;
    const materia = document.getElementById('actividad-materia').value;
    const fecha = document.getElementById('actividad-fecha').value;
    
    if (!titulo || !tipo || !materia || !fecha) {
        alert('Por favor completa todos los campos requeridos');
        return;
    }
    
    const accion = actividadEditando ? 'actualizada' : 'creada';
    alert(`✅ Actividad ${accion} exitosamente`);
    
    cerrarModalActividad();
    // Aquí recargarías la lista de actividades
});

// Cerrar modales al hacer clic fuera
document.addEventListener('click', function(e) {
    if (e.target.id === 'modal-actividad') {
        cerrarModalActividad();
    }
    if (e.target.id === 'modal-detalle') {
        cerrarModalDetalle();
    }
});
</script>
@endsection