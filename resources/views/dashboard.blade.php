<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Dashboard') }} - Sistema de Predicción Académica
            </h2>
            <div class="text-sm text-gray-600 dark:text-gray-400">
                <span class="font-medium">{{ Auth::user()->nombres }} {{ Auth::user()->apellidos }}</span>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <!-- Welcome Message -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg mb-6">
                <div class="p-6">
                    <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">
                        ¡Bienvenido al Sistema de Predicción Académica!
                    </h3>
                    <p class="text-gray-600 dark:text-gray-400">
                        Utiliza inteligencia artificial para predecir el rendimiento académico y tomar decisiones informadas.
                    </p>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <!-- Generar Predicción -->
                <a href="{{ route('predicciones.seleccionar') }}" class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg hover:shadow-2xl transition transform hover:-translate-y-1">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-indigo-500 rounded-md p-3">
                                <svg class="h-8 w-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                </svg>
                            </div>
                            <div class="ml-4">
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                                    Predicciones IA
                                </h3>
                                <p class="text-sm text-gray-500 dark:text-gray-400">
                                    Generar análisis predictivo
                                </p>
                            </div>
                        </div>
                    </div>
                </a>

                <!-- Gestionar Estudiantes -->
                <a href="{{ route('estudiantes.index') }}" class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg hover:shadow-2xl transition transform hover:-translate-y-1">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-green-500 rounded-md p-3">
                                <svg class="h-8 w-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                </svg>
                            </div>
                            <div class="ml-4">
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                                    Estudiantes
                                </h3>
                                <p class="text-sm text-gray-500 dark:text-gray-400">
                                    Gestionar alumnos
                                </p>
                            </div>
                        </div>
                    </div>
                </a>

                <!-- Gestionar Docentes -->
                <a href="{{ route('admin.docentes.index') }}" class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg hover:shadow-2xl transition transform hover:-translate-y-1">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-purple-500 rounded-md p-3">
                                <svg class="h-8 w-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                </svg>
                            </div>
                            <div class="ml-4">
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                                    Docentes
                                </h3>
                                <p class="text-sm text-gray-500 dark:text-gray-400">
                                    Gestionar profesores
                                </p>
                            </div>
                        </div>
                    </div>
                </a>

                <!-- Registrar Notas -->
                <a href="{{ route('admin.notas.create') }}" class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg hover:shadow-2xl transition transform hover:-translate-y-1">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-blue-500 rounded-md p-3">
                                <svg class="h-8 w-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                </svg>
                            </div>
                            <div class="ml-4">
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                                    Notas
                                </h3>
                                <p class="text-sm text-gray-500 dark:text-gray-400">
                                    Registrar calificaciones
                                </p>
                            </div>
                        </div>
                    </div>
                </a>

                <!-- Registrar Asistencias -->
                <a href="{{ route('admin.asistencias.registrar') }}" class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg hover:shadow-2xl transition transform hover:-translate-y-1">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-yellow-500 rounded-md p-3">
                                <svg class="h-8 w-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                                </svg>
                            </div>
                            <div class="ml-4">
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                                    Asistencias
                                </h3>
                                <p class="text-sm text-gray-500 dark:text-gray-400">
                                    Control de asistencia
                                </p>
                            </div>
                        </div>
                    </div>
                </a>

                <!-- Recursos Educativos -->
                <a href="{{ route('admin.recursos.index') }}" class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg hover:shadow-2xl transition transform hover:-translate-y-1">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-red-500 rounded-md p-3">
                                <svg class="h-8 w-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                </svg>
                            </div>
                            <div class="ml-4">
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                                    Recursos
                                </h3>
                                <p class="text-sm text-gray-500 dark:text-gray-400">
                                    Material educativo
                                </p>
                            </div>
                        </div>
                    </div>
                </a>
            </div>

        </div>
    </div>
</x-app-layout>