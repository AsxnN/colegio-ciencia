@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-800">💡 Recomendaciones Personalizadas IA</h1>
        <p class="text-gray-600 mt-2">Sugerencias inteligentes basadas en tu rendimiento académico</p>
    </div>

    @if(isset($mensaje))
        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-6">
            <p class="text-yellow-800">{{ $mensaje }}</p>
        </div>
    @endif

    @if(isset($recomendaciones) && $recomendaciones->count() > 0)
        <div class="grid gap-6 mb-8">
            @foreach($recomendaciones as $recomendacion)
                <div class="bg-white rounded-lg shadow-md p-6 border-l-4 
                    @if($recomendacion['prioridad'] === 'alta') border-red-500
                    @elseif($recomendacion['prioridad'] === 'media') border-yellow-500
                    @else border-green-500 @endif">
                    
                    <div class="flex items-start justify-between mb-4">
                        <div class="flex items-center">
                            <span class="text-2xl mr-3">{{ $recomendacion['icono'] ?? '💡' }}</span>
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900">{{ $recomendacion['titulo'] }}</h3>
                                <p class="text-gray-600 mt-1">{{ $recomendacion['descripcion'] }}</p>
                            </div>
                        </div>
                        <span class="px-2 py-1 text-xs font-semibold rounded-full
                            @if($recomendacion['prioridad'] === 'alta') bg-red-100 text-red-800
                            @elseif($recomendacion['prioridad'] === 'media') bg-yellow-100 text-yellow-800
                            @else bg-green-100 text-green-800 @endif">
                            {{ ucfirst($recomendacion['prioridad']) }}
                        </span>
                    </div>

                    @if(isset($recomendacion['acciones']))
                        <div class="mt-4">
                            <h4 class="font-medium text-gray-800 mb-2">Acciones recomendadas:</h4>
                            <ul class="list-disc list-inside text-sm text-gray-600 space-y-1">
                                @foreach($recomendacion['acciones'] as $accion)
                                    <li>{{ $accion }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    @endif

    @if(isset($recursosRecomendados) && $recursosRecomendados->count() > 0)
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-semibold mb-4">📚 Recursos Educativos Recomendados</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($recursosRecomendados as $recurso)
                    <div class="border border-gray-200 rounded-lg p-4 hover:shadow-lg transition-shadow">
                        <h3 class="font-semibold text-gray-900 mb-2">{{ $recurso->titulo }}</h3>
                        <p class="text-sm text-gray-600 mb-3">{{ Str::limit($recurso->descripcion, 80) }}</p>
                        
                        <div class="flex items-center justify-between">
                            <span class="px-2 py-1 text-xs font-medium rounded-full bg-gray-100 text-gray-800">
                                {{ ucfirst($recurso->tipo) }}
                            </span>
                            <a href="{{ $recurso->url }}" target="_blank" 
                               class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                Acceder →
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>
@endsection