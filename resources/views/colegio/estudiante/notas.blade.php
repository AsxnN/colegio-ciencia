{{-- filepath: c:\laragon\www\colegio\resources\views\colegio\estudiante\notas.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="bg-white shadow-sm rounded-lg p-6">
        <div class="sm:flex sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-semibold text-gray-800">📘 Notas del Estudiante</h1>
                <p class="mt-1 text-sm text-gray-500">Resumen de calificaciones por curso y bimestres.</p>
            </div>

            <div class="mt-4 sm:mt-0 sm:flex sm:space-x-3">
                <a href="#" class="inline-flex items-center px-4 py-2 bg-white border border-gray-200 rounded-md text-sm text-gray-700 hover:bg-gray-50">🔁 Actualizar</a>
                <a href="#" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-md text-sm hover:bg-blue-700">⬇️ Exportar</a>
            </div>
        </div>

        <div class="mt-6">
            @if(empty($notas) || $notas->isEmpty())
                <div class="p-6 text-center border-2 border-dashed border-gray-200 rounded-lg">
                    <p class="text-gray-500">No hay notas registradas todavía. Genera o importa las calificaciones para ver los resultados aquí.</p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Curso</th>
                                <th scope="col" class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">B1</th>
                                <th scope="col" class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">B2</th>
                                <th scope="col" class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">B3</th>
                                <th scope="col" class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">B4</th>
                                <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Promedio</th>
                                <th scope="col" class="px-6 py-3"></th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($notas as $nota)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">{{ $nota->curso->nombre ?? '—' }}</div>
                                        <div class="text-xs text-gray-500">{{ $nota->curso->codigo ?? '' }}</div>
                                    </td>

                                    <td class="px-4 py-4 text-center text-sm text-gray-700">{{ number_format($nota->bimestre1 ?? 0, 1) }}</td>
                                    <td class="px-4 py-4 text-center text-sm text-gray-700">{{ number_format($nota->bimestre2 ?? 0, 1) }}</td>
                                    <td class="px-4 py-4 text-center text-sm text-gray-700">{{ number_format($nota->bimestre3 ?? 0, 1) }}</td>
                                    <td class="px-4 py-4 text-center text-sm text-gray-700">{{ number_format($nota->bimestre4 ?? 0, 1) }}</td>

                                    @php
                                        $prom = floatval($nota->promedio_final ?? 0);
                                        if ($prom >= 16) { $badge = 'bg-green-100 text-green-800'; $label = 'Excelente'; }
                                        elseif ($prom >= 14) { $badge = 'bg-blue-100 text-blue-800'; $label = 'Bueno'; }
                                        elseif ($prom >= 12) { $badge = 'bg-yellow-100 text-yellow-800'; $label = 'Promedio'; }
                                        else { $badge = 'bg-red-100 text-red-800'; $label = 'Riesgo'; }
                                    @endphp

                                    <td class="px-6 py-4 text-center">
                                        <div class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold {{ $badge }}">
                                            {{ number_format($prom, 2) }}
                                        </div>
                                        <div class="text-xs text-gray-500 mt-1">{{ $label }}</div>
                                    </td>

                                    <td class="px-6 py-4 text-right text-sm">
                                        <a href="{{ route('estudiante.detallenotas', $nota->id ?? 0) }}" class="text-indigo-600 hover:text-indigo-900">Ver</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection