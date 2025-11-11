@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
    <!-- Header similar to predicción page -->
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Predicción de Rendimiento Académico</h1>
            <p class="text-sm text-gray-500">Ingresa los datos del estudiante para generar una predicción rápida usando el servicio de ML.</p>
        </div>
        <div class="text-right">
            <a href="{{ url()->previous() }}" class="inline-flex items-center px-3 py-2 bg-gray-100 text-gray-700 rounded-md hover:bg-gray-200">← Volver</a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Left: student / historial card -->
        <div class="lg:col-span-1">
            <div class="bg-white shadow rounded-lg p-5">
                <div class="flex items-center gap-4">
                    <div class="w-14 h-14 rounded-full bg-indigo-50 flex items-center justify-center text-indigo-600 font-bold">IA</div>
                    <div>
                        <h3 class="font-semibold text-gray-800">Predicción Rápida</h3>
                        <p class="text-sm text-gray-500">Historial y parámetros</p>
                    </div>
                </div>

                @isset($historial)
                    <div class="mt-4">
                        <h4 class="text-sm font-medium text-gray-700 mb-2">📘 Historial del estudiante</h4>
                        <div class="max-h-48 overflow-auto border border-gray-100 rounded">
                            <table class="min-w-full text-sm">
                                <thead class="bg-gray-50 text-gray-600">
                                    <tr>
                                        <th class="px-2 py-2 text-left">Año</th>
                                        <th class="px-2 py-2 text-left">Prom.</th>
                                        <th class="px-2 py-2 text-left">Horas</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($historial as $item)
                                        <tr class="border-t">
                                            <td class="px-2 py-1">{{ $item->anio }}</td>
                                            <td class="px-2 py-1">{{ number_format($item->promedio, 2) }}</td>
                                            <td class="px-2 py-1">{{ $item->horas_estudio ?? '-' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @else
                    <div class="mt-4 text-sm text-gray-500">No hay historial disponible.</div>
                @endisset

                {{-- Notas actuales del estudiante (misma lógica que en predicciones/show) --}}
                @if(isset($estudiante))
                    <div class="mt-4 mb-2 text-sm text-gray-700">
                        <strong>Estudiante seleccionado:</strong>
                        @if($estudiante->usuario) {{ $estudiante->usuario->name }} @else ID {{ $estudiante->id }} @endif
                        &middot; <strong>Cursos con notas:</strong> {{ $estudiante->notas->count() }}
                        @if($estudiante->notas->count())
                            &middot; <strong>Promedio general:</strong> {{ number_format($estudiante->notas->avg('promedio_final'), 2) }}
                        @endif
                    </div>

                    @if($estudiante->notas->count())
                    <div class="mt-6">
                        <h4 class="text-sm font-medium text-gray-700 mb-2">📊 Notas Actuales</h4>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 text-sm">
                                <thead class="bg-gray-50 text-gray-600">
                                    <tr>
                                        <th class="px-3 py-2 text-left">Curso</th>
                                        <th class="px-3 py-2 text-center">Bim 1</th>
                                        <th class="px-3 py-2 text-center">Bim 2</th>
                                        <th class="px-3 py-2 text-center">Bim 3</th>
                                        <th class="px-3 py-2 text-center">Bim 4</th>
                                        <th class="px-3 py-2 text-center">Promedio</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-100">
                                    @foreach($estudiante->notas as $nota)
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-3 py-2 font-medium text-gray-900">{{ $nota->curso->nombre }}</td>
                                            <td class="px-3 py-2 text-center">
                                                <input type="text" value="{{ $nota->bimestre1 ?? '-' }}" readonly class="w-20 mx-auto text-center bg-gray-50 rounded border border-gray-100 text-sm p-1" />
                                            </td>
                                            <td class="px-3 py-2 text-center">
                                                <input type="text" value="{{ $nota->bimestre2 ?? '-' }}" readonly class="w-20 mx-auto text-center bg-gray-50 rounded border border-gray-100 text-sm p-1" />
                                            </td>
                                            <td class="px-3 py-2 text-center">
                                                <input type="text" value="{{ $nota->bimestre3 ?? '-' }}" readonly class="w-20 mx-auto text-center bg-gray-50 rounded border border-gray-100 text-sm p-1" />
                                            </td>
                                            <td class="px-3 py-2 text-center">
                                                <input type="text" value="{{ $nota->bimestre4 ?? '-' }}" readonly class="w-20 mx-auto text-center bg-gray-50 rounded border border-gray-100 text-sm p-1" />
                                            </td>
                                            <td class="px-3 py-2 text-center">
                                                <span class="px-2 py-1 rounded-full font-bold text-sm
                                                    @if($nota->promedio_final >= 14) bg-green-100 text-green-800
                                                    @elseif($nota->promedio_final >= 11) bg-yellow-100 text-yellow-800
                                                    @else bg-red-100 text-red-800
                                                    @endif">
                                                    {{ number_format($nota->promedio_final, 2) }}
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    @endif

                        {{-- Si no hay notas por curso, mostrar promedio anterior (si existe) para que no quede vacío --}}
                        @if(!$estudiante->notas->count())
                            <div class="mt-4 text-sm text-gray-600">
                                @if(!is_null($estudiante->promedio_anterior))
                                    <strong>Promedio registrado del estudiante:</strong> {{ number_format($estudiante->promedio_anterior, 2) }}
                                @else
                                    <em>No hay notas por curso registradas para este estudiante.</em>
                                @endif
                            </div>
                        @endif

                @elseif(isset($notas) && count($notas) > 0)
                    <div class="mt-6">
                        <h4 class="text-sm font-medium text-gray-700 mb-2">📊 Notas Actuales</h4>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 text-sm">
                                <thead class="bg-gray-50 text-gray-600">
                                    <tr>
                                        <th class="px-3 py-2 text-left">Curso</th>
                                        <th class="px-3 py-2 text-center">Bim 1</th>
                                        <th class="px-3 py-2 text-center">Bim 2</th>
                                        <th class="px-3 py-2 text-center">Bim 3</th>
                                        <th class="px-3 py-2 text-center">Bim 4</th>
                                        <th class="px-3 py-2 text-center">Promedio</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-100">
                                    @foreach($notas as $nota)
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-3 py-2 font-medium text-gray-900">{{ $nota->curso->nombre ?? ($nota['curso'] ?? '-') }}</td>
                                            <td class="px-3 py-2 text-center"><input type="text" value="{{ $nota->bimestre1 ?? ($nota['bimestre1'] ?? '-') }}" readonly class="w-20 mx-auto text-center bg-gray-50 rounded border border-gray-100 text-sm p-1" /></td>
                                            <td class="px-3 py-2 text-center"><input type="text" value="{{ $nota->bimestre2 ?? ($nota['bimestre2'] ?? '-') }}" readonly class="w-20 mx-auto text-center bg-gray-50 rounded border border-gray-100 text-sm p-1" /></td>
                                            <td class="px-3 py-2 text-center"><input type="text" value="{{ $nota->bimestre3 ?? ($nota['bimestre3'] ?? '-') }}" readonly class="w-20 mx-auto text-center bg-gray-50 rounded border border-gray-100 text-sm p-1" /></td>
                                            <td class="px-3 py-2 text-center"><input type="text" value="{{ $nota->bimestre4 ?? ($nota['bimestre4'] ?? '-') }}" readonly class="w-20 mx-auto text-center bg-gray-50 rounded border border-gray-100 text-sm p-1" /></td>
                                            <td class="px-3 py-2 text-center"><span class="px-2 py-1 rounded-full font-bold text-sm {{ (isset($nota->promedio_final) ? ($nota->promedio_final >= 14 ? 'bg-green-100 text-green-800' : ($nota->promedio_final >= 11 ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800')) : '') }}">{{ isset($nota->promedio_final) ? number_format($nota->promedio_final, 2) : (isset($nota['promedio_final']) ? number_format($nota['promedio_final'],2) : '-') }}</span></td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @else
                    <div class="mt-4 text-sm text-gray-500">No hay notas registradas.</div>
                @endif
            </div>
        </div>

        <!-- Right: form card spanning 2 cols on large screens -->
        <div class="lg:col-span-2">
            <div class="bg-white shadow rounded-lg p-6">
                {{-- Errores de validación --}}
                @if ($errors->any())
                    <div class="mb-4 p-3 rounded bg-red-50 border border-red-200 text-red-700">
                        <ul class="list-disc pl-5">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('ml.predict') }}" method="POST" class="space-y-6">
                    @csrf

                    @php
                        $fields = [
                            'horas_estudio' => 10,
                            'asistencia' => 90,
                            'horas_sueno' => 7,
                            'promedio_anterior' => 14,
                            'sesiones_tutoria' => 2,
                            'actividad_fisica' => 3,
                        ];
                    @endphp

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        @foreach($fields as $key => $default)
                            <div>
                                <label for="{{ $key }}" class="block text-sm font-medium text-gray-700 capitalize">{{ str_replace('_', ' ', $key) }}</label>
                                <div class="mt-1">
                                    <input id="{{ $key }}" name="{{ $key }}" type="number" step="any" value="{{ old($key, $default) }}" 
                                        class="block w-full rounded-md border-gray-200 bg-gray-50 p-2 focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" />
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div>
                        <label for="estudiante_select" class="block text-sm font-medium text-gray-700">Estudiante (opcional)</label>
                        <div class="mt-1 flex items-center gap-3">
                            <select id="estudiante_select" class="rounded-md border-gray-200 bg-white p-2 text-sm">
                                <option value="">-- Seleccionar estudiante (ver notas) --</option>
                                @foreach($estudiantes as $est)
                                    <option value="{{ $est->id }}" @if(isset($estudiante) && $estudiante && $estudiante->id == $est->id) selected @endif>
                                        {{ $est->usuario->name }} (ID: {{ $est->id }})
                                    </option>
                                @endforeach
                            </select>

                            {{-- Mantener campo opcional por compatibilidad con el backend POST --}}
                            <input id="estudiante_id" name="estudiante_id" type="hidden" value="{{ old('estudiante_id', isset($estudiante) ? $estudiante->id : '') }}" />
                            <button type="button" id="verNotasBtn" class="px-3 py-2 bg-gray-100 rounded text-sm text-gray-700 hover:bg-gray-200">Ver notas</button>
                        </div>
                    </div>

                    <div class="flex items-center gap-3">
                        <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Predecir
                        </button>
                        <a href="{{ url()->previous() }}" class="text-sm text-gray-600 hover:underline">Volver</a>

                        {{-- opción para limpiar/usar valores por defecto --}}
                        <button type="button" onclick="document.querySelectorAll('form input[type=number]').forEach(i=>i.value='')" class="ml-auto text-sm text-gray-500 hover:text-gray-700">Limpiar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<script>
    (function(){
        const select = document.getElementById('estudiante_select');
        const verBtn = document.getElementById('verNotasBtn');
        const hidden = document.getElementById('estudiante_id');

        if (select) {
            // Sincronizar hidden cuando cambie la selección
            select.addEventListener('change', function(){
                if (hidden) hidden.value = this.value;
            });
        }

        if (verBtn) {
            verBtn.addEventListener('click', function(){
                const id = select ? select.value : '';
                if (!id) {
                    alert('Selecciona un estudiante para ver sus notas.');
                    return;
                }
                // Redirigir a la misma ruta de formulario pero con query string para cargar notas
                const url = new URL(window.location.href);
                url.searchParams.set('estudiante_id', id);
                window.location.href = url.pathname + '?' + url.searchParams.toString();
            });
        }
    })();
</script>

@endsection
