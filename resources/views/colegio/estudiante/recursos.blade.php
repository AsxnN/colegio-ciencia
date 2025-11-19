@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-800">📚 Recursos Educativos</h1>
        <p class="text-gray-600 mt-2">Herramientas y materiales personalizados para tu aprendizaje</p>
    </div>

    @if(isset($mensaje))
        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-6">
            <p class="text-yellow-800">{{ $mensaje }}</p>
        </div>
    @endif

    <!-- Recursos Personalizados -->
    @if(isset($recursosPersonalizados) && $recursosPersonalizados->count() > 0)
        <div class="mb-8">
            <h2 class="text-2xl font-semibold mb-4">🎯 Recursos Recomendados para Ti</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($recursosPersonalizados as $recurso)
                    <div class="bg-gradient-to-br from-blue-50 to-indigo-50 border border-blue-200 rounded-lg p-4">
                        <div class="flex items-start justify-between mb-2">
                            <h3 class="font-semibold text-gray-900">{{ $recurso->titulo }}</h3>
                            <span class="px-2 py-1 text-xs font-medium rounded-full bg-blue-100 text-blue-800">
                                Recomendado
                            </span>
                        </div>
                        <p class="text-sm text-gray-600 mb-3">{{ Str::limit($recurso->descripcion, 100) }}</p>
                        @if($recurso->curso)
                            <p class="text-xs text-blue-600 mb-2">Para: {{ $recurso->curso->nombre }}</p>
                        @endif
                        <a href="{{ $recurso->url }}" target="_blank" 
                           class="inline-block w-full text-center bg-blue-600 text-white px-3 py-2 rounded text-sm hover:bg-blue-700">
                            Acceder al Recurso
                        </a>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Recursos por Categoría -->
    @if(isset($recursosPorCategoria) && $recursosPorCategoria->count() > 0)
        @foreach($recursosPorCategoria as $categoria => $recursos)
            <div class="mb-8">
                <h2 class="text-xl font-semibold mb-4 capitalize">
                    @switch($categoria)
                        @case('video') 🎥 Videos Educativos @break
                        @case('documento') 📄 Documentos y Guías @break
                        @case('herramienta') 🛠️ Herramientas Interactivas @break
                        @case('curso') 🎓 Cursos Online @break
                        @case('aplicacion') 📱 Aplicaciones @break
                        @default 📚 {{ ucfirst($categoria) }}
                    @endswitch
                </h2>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    @foreach($recursos as $recurso)
                        <div class="bg-white border border-gray-200 rounded-lg p-4 hover:shadow-lg transition-shadow">
                            <h3 class="font-semibold text-gray-900 mb-2">{{ $recurso->titulo }}</h3>
                            <p class="text-sm text-gray-600 mb-3">{{ Str::limit($recurso->descripcion, 80) }}</p>
                            
                            @if($recurso->curso)
                                <p class="text-xs text-gray-500 mb-2">{{ $recurso->curso->nombre }}</p>
                            @endif
                            
                            <div class="flex items-center justify-between">
                                <span class="px-2 py-1 text-xs rounded-full 
                                    @if($recurso->nivel_dificultad === 'basico') bg-green-100 text-green-800
                                    @elseif($recurso->nivel_dificultad === 'intermedio') bg-yellow-100 text-yellow-800
                                    @else bg-red-100 text-red-800 @endif">
                                    {{ ucfirst($recurso->nivel_dificultad) }}
                                </span>
                                <a href="{{ $recurso->url }}" target="_blank" 
                                   class="text-blue-600 hover:text-blue-800 text-sm">
                                    Ver →
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach
    @endif

    <!-- Recursos Generales -->
    @if(isset($recursosGenerales) && $recursosGenerales->count() > 0)
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-semibold mb-4">🌟 Recursos Generales</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($recursosGenerales as $recurso)
                    <div class="border border-gray-200 rounded-lg p-4">
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