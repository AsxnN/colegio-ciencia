@extends('layouts.app')

@section('title', 'Predicción de Rendimiento')

@section('content')
<div class="container mx-auto p-6">
    <h1 class="text-3xl font-bold mb-6">Predicción de Rendimiento Académico</h1>

    {{-- Mensaje de estado o error --}}
    @if (!empty($mensaje))
        <div class="bg-blue-100 border border-blue-400 text-blue-700 px-4 py-3 rounded mb-6">
            {{ $mensaje }}
        </div>
    @endif

    {{-- Información del estudiante --}}
    @if ($estudiante)
        <div class="mb-6">
            <h4 class="text-xl font-semibold">Estudiante: <span class="text-gray-700">{{ $estudiante->usuario->name ?? 'N/A' }}</span></h4>
            <p class="text-gray-600"><strong>Sección:</strong> {{ $estudiante->seccion->nombre ?? 'No asignado' }}</p>
        </div>
    @else
        <p class="text-red-600 mb-6">No se encontró información del estudiante.</p>
    @endif

    {{-- Predicciones recientes por curso --}}
    <h3 class="text-2xl font-semibold mb-4">Predicciones por Curso (Últimos 30 días)</h3>
    @if($prediccionesPorCurso->isEmpty())
        <p class="text-gray-600 mb-6">No hay predicciones recientes.</p>
    @else
        <table class="min-w-full bg-white border border-gray-300 rounded mb-6">
            <thead>
                <tr class="bg-gray-100 text-left">
                    <th class="px-4 py-2 border-b border-gray-300">Curso</th>
                    <th class="px-4 py-2 border-b border-gray-300">Probabilidad de Aprobar (%)</th>
                    <th class="px-4 py-2 border-b border-gray-300">Recomendaciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($prediccionesPorCurso as $prediccion)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-2 border-b border-gray-300">{{ $prediccion->curso->nombre }}</td>
                        <td class="px-4 py-2 border-b border-gray-300">{{ $prediccion->probabilidad_aprobar_curso }}</td>
                        <td class="px-4 py-2 border-b border-gray-300">
                            @if (!empty($prediccion->recomendaciones_curso))
                                <ul class="list-disc pl-5">
                                    @foreach ($prediccion->recomendaciones_curso as $recomendacion)
                                        <li>{{ $recomendacion }}</li>
                                    @endforeach
                                </ul>
                            @else
                                <span class="text-gray-500">Sin recomendaciones específicas</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    {{-- Recomendaciones personalizadas --}}
    <h3 class="text-2xl font-semibold mb-4">Recomendaciones Personalizadas</h3>
    @if($recomendacionesPersonalizadas->isEmpty())
        <p class="text-gray-600 mb-6">No hay recomendaciones en este momento.</p>
    @else
        <div class="space-y-4 mb-6">
            @foreach ($recomendacionesPersonalizadas as $rec)
                <div @class([
                    'p-4 rounded border-l-4',
                    'bg-red-50 border-red-500 text-red-700' => $rec['prioridad'] == 'muy_alta',
                    'bg-yellow-50 border-yellow-500 text-yellow-700' => $rec['prioridad'] == 'media',
                    'bg-green-50 border-green-500 text-green-700' => $rec['prioridad'] == 'baja',
                ])>
                    <h5 class="font-semibold mb-1">{{ $rec['titulo'] }}</h5>
                    <p class="mb-2">{{ $rec['contenido'] }}</p>
                    @if (!empty($rec['acciones']))
                        <strong>Acciones sugeridas:</strong>
                        <ul class="list-disc pl-5">
                            @foreach ($rec['acciones'] as $accion)
                                <li>{{ $accion }}</li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            @endforeach
        </div>
    @endif

    {{-- Recursos educativos --}}
    <h3 class="text-2xl font-semibold mb-4">Recursos Educativos</h3>
    @if($recursosEducativos->isEmpty())
        <p class="text-gray-600 mb-6">No hay recursos disponibles.</p>
    @else
        <ul class="list-disc pl-5 mb-6">
            @foreach($recursosEducativos as $recurso)
                <li><a href="{{ $recurso->url ?? '#' }}" target="_blank" class="text-blue-600 hover:underline">{{ $recurso->titulo }}</a> <span class="text-xs text-gray-500">(Prioridad: {{ $recurso->prioridad }})</span></li>
            @endforeach
        </ul>
    @endif

    {{-- Cursos del estudiante --}}
    <h3 class="text-2xl font-semibold mb-4">Cursos del Estudiante</h3>
    @if($cursosDelEstudiante->isEmpty())
        <p class="text-gray-600 mb-6">El estudiante no tiene cursos asignados.</p>
    @else
        <ul class="list-disc pl-5 mb-6">
            @foreach($cursosDelEstudiante as $curso)
                <li>{{ $curso->nombre }}</li>
            @endforeach
        </ul>
    @endif

    {{-- Historial de predicciones --}}
    <h3 class="text-2xl font-semibold mb-4">Historial de Predicciones</h3>
    @if($historial_predicciones->isEmpty())
        <p class="text-gray-600 mb-6">No se encontraron predicciones anteriores.</p>
    @else
        <table class="min-w-full bg-white border border-gray-300 rounded">
            <thead>
                <tr class="bg-gray-100 text-left">
                    <th class="px-4 py-2 border-b border-gray-300">Fecha</th>
                    <th class="px-4 py-2 border-b border-gray-300">Probabilidad de Aprobar</th>
                    <th class="px-4 py-2 border-b border-gray-300">Nivel de Riesgo</th>
                    <th class="px-4 py-2 border-b border-gray-300">Análisis</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($historial_predicciones as $historial)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-2 border-b border-gray-300">{{ $historial->created_at->format('d/m/Y H:i') }}</td>
                        <td class="px-4 py-2 border-b border-gray-300">{{ $historial->probabilidad_aprobar }}</td>
                        <td class="px-4 py-2 border-b border-gray-300">{{ $historial->nivel_riesgo }}</td>
                        <td class="px-4 py-2 border-b border-gray-300">{{ $historial->analisis }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</div>
@endsection
