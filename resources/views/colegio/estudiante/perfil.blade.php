{{-- filepath: c:\laragon\www\colegio\resources\views\colegio\estudiante\perfil.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="bg-white shadow rounded-lg overflow-hidden">
        <div class="px-6 py-4 border-b">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-xl font-semibold text-gray-900">Perfil del Estudiante</h1>
                    <p class="mt-1 text-sm text-gray-500">Información personal y accesos rápidos.</p>
                </div>

                <div class="flex items-center gap-3">
                    <a href="{{ route('estudiante.cursos', $estudiante->id ?? null) }}" class="px-3 py-2 bg-white border rounded text-sm text-gray-700 hover:bg-gray-50">Cursos</a>
                    <a href="{{ route('estudiante.notas', $estudiante->id ?? null) }}" class="px-3 py-2 bg-indigo-600 text-white rounded text-sm hover:bg-indigo-700">Notas</a>
                </div>
            </div>
        </div>

        <div class="p-6 grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="md:col-span-2">
                <div class="flex items-center gap-4">
                    <div class="h-20 w-20 rounded-full bg-gray-100 overflow-hidden flex items-center justify-center text-2xl text-gray-400">
                        {{ strtoupper(substr($estudiante->usuario->nombres ?? 'U', 0, 1)) }}
                    </div>
                    <div>
                        <h2 class="text-lg font-semibold text-gray-900">{{ $estudiante->usuario->nombres ?? '—' }} {{ $estudiante->usuario->apellidos ?? '' }}</h2>
                        <div class="text-sm text-gray-500">DNI: {{ $estudiante->usuario->dni ?? '—' }}</div>
                        <div class="text-sm text-gray-500">Email: {{ $estudiante->usuario->email ?? '—' }}</div>
                        <div class="text-sm text-gray-500">Tel: {{ $estudiante->usuario->telefono ?? '—' }}</div>
                        <div class="text-sm text-gray-500 mt-1">Sección: {{ $estudiante->seccion->grado ?? '' }}{{ $estudiante->seccion->nombre ?? '' }}</div>
                    </div>
                </div>

                <div class="mt-6 bg-gray-50 p-4 rounded">
                    <h3 class="text-sm font-medium text-gray-700">Resumen Académico</h3>
                    <p class="mt-2 text-sm text-gray-600">Accede rápidamente a tus cursos, notas y recomendaciones generadas por el sistema.</p>
                    <div class="mt-4 flex gap-3">
                        <a href="{{ route('estudiante.predicciones', $estudiante->id ?? null) }}" class="px-4 py-2 bg-white border rounded text-sm text-gray-700 hover:bg-gray-50">Mis Predicciones</a>
                        <a href="{{ route('estudiante.recomendaciones', $estudiante->id ?? null) }}" class="px-4 py-2 bg-white border rounded text-sm text-gray-700 hover:bg-gray-50">Recomendaciones IA</a>
                        <a href="{{ route('estudiante.recursos', $estudiante->id ?? null) }}" class="px-4 py-2 bg-white border rounded text-sm text-gray-700 hover:bg-gray-50">Recursos Educativos</a>
                    </div>
                </div>
            </div>

            <aside class="space-y-4">
                <div class="p-4 bg-white border rounded">
                    <div class="text-xs text-gray-500">Estado</div>
                    <div class="mt-2 text-lg font-semibold text-gray-900">{{ $estudiante->estado ?? 'Activo' }}</div>
                </div>

                <div class="p-4 bg-white border rounded">
                    <div class="text-xs text-gray-500">Fecha de Ingreso</div>
                    <div class="mt-2 text-sm text-gray-700">{{ optional($estudiante->created_at)->format('d/m/Y') ?? '—' }}</div>
                </div>

                <div class="p-4 bg-white border rounded">
                    <a href="{{ route('profile.show') }}" class="w-full inline-block text-center px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700">Editar Perfil</a>
                </div>
            </aside>
        </div>
    </div>
</div>
@endsection