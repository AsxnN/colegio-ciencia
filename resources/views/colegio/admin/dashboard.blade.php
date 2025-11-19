<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                <h1 class="text-2xl font-bold">Panel de Administración</h1>
                <p class="mt-2 text-gray-600">Bienvenido, {{ $user->nombres ?? $user->name }}.</p>

                <div class="mt-6 grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="p-4 bg-gray-50 rounded">
                        <div class="text-sm text-gray-500">Total Estudiantes</div>
                        <div class="text-3xl font-bold">{{ $totalEstudiantes }}</div>
                    </div>
                    <div class="p-4 bg-gray-50 rounded">
                        <div class="text-sm text-gray-500">Acciones</div>
                        <div class="mt-2"><a href="{{ route('admin.usuarios.index') }}" class="text-blue-600">Gestionar usuarios</a></div>
                    </div>
                    <div class="p-4 bg-gray-50 rounded">
                        <div class="text-sm text-gray-500">IA</div>
                        <div class="mt-2"><a href="{{ route('admin.ia.panel') }}" class="text-blue-600">Panel IA</a></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
