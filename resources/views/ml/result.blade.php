@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
    <div class="bg-white shadow rounded-lg p-6">
        <h2 class="text-2xl font-semibold text-gray-800 mb-4">Resultado de la predicción</h2>

        @if(isset($prediction))
            <div class="mb-4 p-4 bg-green-50 border border-green-200 rounded">
                <div class="text-sm text-gray-600">Predicción</div>
                <div class="text-3xl font-bold text-green-700">{{ round($prediction, 2) }}</div>
            </div>
        @else
            <div class="mb-4 p-3 bg-yellow-50 border border-yellow-200 rounded text-yellow-700">No se recibió predicción.</div>
        @endif

        @if(!empty($notes_history))
            <h3 class="text-lg font-medium text-gray-800 mb-2">Historial de notas</h3>
            <div class="max-h-96 overflow-auto divide-y divide-gray-100">
                @foreach($notes_history as $n)
                    <div class="py-3 flex items-start justify-between">
                        <div class="text-sm text-gray-800">{{ $n['curso'] ?? 'curso' }}</div>
                        <div class="ml-4 text-sm font-medium text-gray-700">{{ isset($n['promedio_final']) ? number_format($n['promedio_final'], 2) : 'N/A' }}</div>
                        <div class="ml-6 text-xs text-gray-500">{{ $n['fecha'] ?? '' }}</div>
                    </div>
                @endforeach
            </div>
        @endif

        <div class="mt-6">
            <a href="{{ route('ml.form') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-gray-600 hover:bg-gray-700">Volver</a>
        </div>
    </div>
</div>

@endsection
