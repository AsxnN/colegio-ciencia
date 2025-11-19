@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <h1 class="text-3xl font-bold mb-6">Predicción de Rendimiento por Curso</h1>

    <table class="min-w-full table-auto border border-gray-300">
        <thead class="bg-gray-100">
            <tr>
                <th class="border px-4 py-2">Curso</th>
                <th class="border px-4 py-2">Nota Actual</th>
                <th class="border px-4 py-2">Predicción</th>
                <th class="border px-4 py-2">Estado</th>
            </tr>
        </thead>
        <tbody>
            @forelse($predicciones_por_curso as $item)
                <tr class="{{ $item['estado'] == 'Riesgo' ? 'bg-red-100' : ($item['estado'] == 'Promedio' ? 'bg-yellow-100' : 'bg-green-100') }}">
                    <td class="border px-4 py-2">{{ $item['curso'] }}</td>
                    <td class="border px-4 py-2">{{ number_format($item['nota_actual'], 2) }}</td>
                    <td class="border px-4 py-2">{{ number_format($item['prediccion'], 2) }}</td>
                    <td class="border px-4 py-2 font-semibold">{{ $item['estado'] }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="px-4 py-8 text-center text-gray-500">No hay datos de predicción disponibles.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
