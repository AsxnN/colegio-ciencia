<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Mensaje de bienvenida -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg mb-6">
                <div class="p-6">
                    <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">
                        ¡Bienvenido, {{ Auth::user()->nombres }}!
                    </h3>
                    <p class="text-gray-600 dark:text-gray-400">
                        Aquí puedes consultar tu información académica, tus cursos, notas y recursos educativos.
                    </p>
                </div>
            </div>

            <!-- Acciones rápidas para el estudiante -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <!-- Perfil -->
                <a href="{{ route('estudiante.perfil', Auth::id()) }}" class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg hover:shadow-2xl transition transform hover:-translate-y-1">
                    <div class="p-6 flex items-center">
                        <div class="flex-shrink-0 bg-blue-500 rounded-md p-3">
                            <svg class="h-8 w-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A9.001 9.001 0 0112 15c2.21 0 4.21.805 5.879 2.146M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                        </div>
                        <div class="ml-4">
                            <h4 class="text-lg font-semibold text-gray-900 dark:text-white">Mi Perfil</h4>
                            <p class="text-gray-500 dark:text-gray-400 text-sm">Ver y editar tu información personal.</p>
                        </div>
                    </div>
                </a>

                <!-- Cursos -->
                <a href="{{ route('estudiante.cursos', Auth::id()) }}" class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg hover:shadow-2xl transition transform hover:-translate-y-1">
                    <div class="p-6 flex items-center">
                        <div class="flex-shrink-0 bg-green-500 rounded-md p-3">
                            <svg class="h-8 w-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6l4 2" />
                            </svg>
                        </div>
                        <div class="ml-4">
                            <h4 class="text-lg font-semibold text-gray-900 dark:text-white">Mis Cursos</h4>
                            <p class="text-gray-500 dark:text-gray-400 text-sm">Consulta los cursos en los que estás inscrito.</p>
                        </div>
                    </div>
                </a>

                <!-- Notas -->
                <a href="{{ route('estudiante.notas', Auth::id()) }}" class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg hover:shadow-2xl transition transform hover:-translate-y-1">
                    <div class="p-6 flex items-center">
                        <div class="flex-shrink-0 bg-yellow-500 rounded-md p-3">
                            <svg class="h-8 w-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-6h6v6m-6 0h6" />
                            </svg>
                        </div>
                        <div class="ml-4">
                            <h4 class="text-lg font-semibold text-gray-900 dark:text-white">Mis Notas</h4>
                            <p class="text-gray-500 dark:text-gray-400 text-sm">Revisa tus calificaciones y desempeño.</p>
                        </div>
                    </div>
                </a>

                <!-- Predicciones -->
                <a href="{{ route('estudiante.predicciones', Auth::id()) }}" class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg hover:shadow-2xl transition transform hover:-translate-y-1">
                    <div class="p-6 flex items-center">
                        <div class="flex-shrink-0 bg-purple-500 rounded-md p-3">
                            <svg class="h-8 w-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m4 0h-1v4h-1" />
                            </svg>
                        </div>
                        <div class="ml-4">
                            <h4 class="text-lg font-semibold text-gray-900 dark:text-white">Predicciones</h4>
                            <p class="text-gray-500 dark:text-gray-400 text-sm">Consulta tus predicciones académicas.</p>
                        </div>
                    </div>
                </a>

                <!-- Recomendaciones -->
                <a href="{{ route('estudiante.recomendaciones', Auth::id()) }}" class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg hover:shadow-2xl transition transform hover:-translate-y-1">
                    <div class="p-6 flex items-center">
                        <div class="flex-shrink-0 bg-pink-500 rounded-md p-3">
                            <svg class="h-8 w-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 17l4-4 4 4" />
                            </svg>
                        </div>
                        <div class="ml-4">
                            <h4 class="text-lg font-semibold text-gray-900 dark:text-white">Recomendaciones</h4>
                            <p class="text-gray-500 dark:text-gray-400 text-sm">Recibe sugerencias para mejorar tu rendimiento.</p>
                        </div>
                    </div>
                </a>

                <!-- Recursos educativos -->
                <a href="{{ route('estudiante.recursos', Auth::id()) }}" class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg hover:shadow-2xl transition transform hover:-translate-y-1">
                    <div class="p-6 flex items-center">
                        <div class="flex-shrink-0 bg-teal-500 rounded-md p-3">
                            <svg class="h-8 w-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 20h9" />
                            </svg>
                        </div>
                        <div class="ml-4">
                            <h4 class="text-lg font-semibold text-gray-900 dark:text-white">Recursos Educativos</h4>
                            <p class="text-gray-500 dark:text-gray-400 text-sm">Accede a materiales y recursos de apoyo.</p>
                        </div>
                    </div>
                </a>
            </div>
        </div>
    </div>
</x-app-layout>