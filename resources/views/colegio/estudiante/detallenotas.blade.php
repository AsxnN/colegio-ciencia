@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="bg-white shadow-sm rounded-lg p-6">
        <div class="sm:flex sm:items-start sm:justify-between">
            <div>
                <h1 class="text-2xl font-semibold text-gray-800">Detalle de la Nota</h1>
                <p class="mt-1 text-sm text-gray-500">Información detallada y desglose por bimestres.</p>
            </div>

            <div class="mt-4 sm:mt-0 sm:flex sm:gap-3">
                <a href="{{ url()->previous() }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-200 rounded-md text-sm text-gray-700 hover:bg-gray-50">← Volver</a>
                <a href="#" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-md text-sm hover:bg-blue-700">Imprimir / Exportar</a>
            </div>
        </div>

        <div class="mt-6 grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="col-span-2">
                <div class="mb-4">
                    <h2 class="text-sm font-medium text-gray-500">Curso</h2>
                    <div class="mt-1 text-lg font-semibold text-gray-900">{{ $nota->curso->nombre ?? '—' }} <span class="text-xs text-gray-500">{{ $nota->curso->codigo ?? '' }}</span></div>
                </div>

                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                    <div class="p-4 bg-gray-50 rounded-lg text-center">
                        <div class="text-xs text-gray-500">Bimestre 1</div>
                        <div class="mt-1 text-xl font-semibold text-gray-900">{{ number_format($nota->bimestre1 ?? 0, 1) }}</div>
                    </div>

                    <div class="p-4 bg-gray-50 rounded-lg text-center">
                        <div class="text-xs text-gray-500">Bimestre 2</div>
                        <div class="mt-1 text-xl font-semibold text-gray-900">{{ number_format($nota->bimestre2 ?? 0, 1) }}</div>
                    </div>

                    <div class="p-4 bg-gray-50 rounded-lg text-center">
                        <div class="text-xs text-gray-500">Bimestre 3</div>
                        <div class="mt-1 text-xl font-semibold text-gray-900">{{ number_format($nota->bimestre3 ?? 0, 1) }}</div>
                    </div>

                    <div class="p-4 bg-gray-50 rounded-lg text-center">
                        <div class="text-xs text-gray-500">Bimestre 4</div>
                        <div class="mt-1 text-xl font-semibold text-gray-900">{{ number_format($nota->bimestre4 ?? 0, 1) }}</div>
                    </div>
                </div>

                <div class="mt-6">
                    <h3 class="text-sm font-medium text-gray-500">Observaciones</h3>
                    <div class="mt-2 text-gray-700">{{ $nota->observaciones ?? 'Sin observaciones.' }}</div>
                </div>
            </div>

            <aside class="space-y-4">
                @php
                    $prom = floatval($nota->promedio_final ?? (($nota->bimestre1 + $nota->bimestre2 + $nota->bimestre3 + $nota->bimestre4) / 4 ?? 0));
                    if ($prom >= 16) { $badge = 'bg-green-100 text-green-800'; $label = 'Excelente'; }
                    elseif ($prom >= 14) { $badge = 'bg-blue-100 text-blue-800'; $label = 'Bueno'; }
                    elseif ($prom >= 12) { $badge = 'bg-yellow-100 text-yellow-800'; $label = 'Promedio'; }
                    else { $badge = 'bg-red-100 text-red-800'; $label = 'Riesgo'; }
                @endphp

                <div class="p-4 bg-white border rounded-lg">
                    <div class="text-sm text-gray-500">Promedio Final</div>
                    <div class="mt-2 flex items-center gap-3">
                        <div class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold {{ $badge }}">{{ number_format($prom, 2) }}</div>
                        <div class="text-sm text-gray-600">{{ $label }}</div>
                    </div>
                </div>

                <div class="p-4 bg-white border rounded-lg">
                    <div class="text-sm text-gray-500">Estudiante</div>
                    <div class="mt-2 text-sm font-medium text-gray-900">{{ $nota->estudiante->user->name ?? $nota->estudiante->nombre ?? '—' }}</div>
                </div>

                <div class="p-4 bg-white border rounded-lg">
                    <div class="text-sm text-gray-500">Acciones</div>
                    <div class="mt-3 flex flex-col gap-2">
                        <a href="#" class="text-sm text-indigo-600 hover:underline">Solicitar revisión</a>
                        <a href="#" class="text-sm text-indigo-600 hover:underline">Ver historial de notas</a>
                    </div>
                </div>
            </aside>
        </div>
    </div>
</div>
@endsection
