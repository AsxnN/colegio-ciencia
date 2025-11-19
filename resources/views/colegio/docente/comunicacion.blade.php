@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">💬 Comunicación y Soporte</h1>
            <p class="text-gray-600 mt-2">Herramientas de comunicación con estudiantes y padres</p>
        </div>
        <div class="flex space-x-3">
            <button onclick="nuevoMensaje()" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
                ✉️ Nuevo Mensaje
            </button>
            <a href="{{ route('docente.dashboard') }}" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700">
                ← Dashboard
            </a>
        </div>
    </div>

    <!-- Contenido principal -->
    <div class="bg-white rounded-lg shadow-md p-12 text-center">
        <span class="text-6xl block mb-4">🚧</span>
        <h3 class="text-xl font-semibold text-gray-600 mb-2">Módulo en Desarrollo</h3>
        <p class="text-gray-500 mb-6">Las funciones de comunicación estarán disponibles próximamente</p>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-8">
            <div class="border rounded-lg p-4">
                <span class="text-2xl block mb-2">📧</span>
                <h4 class="font-semibold">Mensajería</h4>
                <p class="text-sm text-gray-500">Comunicación directa con estudiantes</p>
            </div>
            <div class="border rounded-lg p-4">
                <span class="text-2xl block mb-2">👨‍👩‍👧‍👦</span>
                <h4 class="font-semibold">Contacto Padres</h4>
                <p class="text-sm text-gray-500">Comunicación con padres de familia</p>
            </div>
            <div class="border rounded-lg p-4">
                <span class="text-2xl block mb-2">📢</span>
                <h4 class="font-semibold">Anuncios</h4>
                <p class="text-sm text-gray-500">Publicar anuncios generales</p>
            </div>
        </div>
    </div>
</div>

<script>
function nuevoMensaje() {
    alert('Función de mensajería en desarrollo');
}
</script>
@endsection