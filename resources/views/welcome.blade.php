<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Sistema de Predicción Académica - Colegio</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Styles -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="antialiased bg-gradient-to-br from-gray-50 to-gray-100 dark:from-gray-900 dark:to-gray-800">
        <div class="min-h-screen flex flex-col">
            <!-- Header / Navigation -->
            <header class="bg-white dark:bg-gray-800 shadow-sm">
                <nav class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="flex justify-between items-center h-16">
                        <div class="flex items-center">
                            <svg class="h-8 w-8 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span class="ml-2 text-xl font-bold text-gray-900 dark:text-white">
                                Sistema de Predicción Académica
                            </span>
                        </div>

                        @if (Route::has('login'))
                            <div class="flex items-center space-x-4">
                                @auth
                                    <a href="{{ url('/dashboard') }}" class="text-sm font-medium text-gray-700 dark:text-gray-300 hover:text-indigo-600 dark:hover:text-indigo-400 transition">
                                        Dashboard
                                    </a>
                                @else
                                    <a href="{{ route('login') }}" class="text-sm font-medium text-gray-700 dark:text-gray-300 hover:text-indigo-600 dark:hover:text-indigo-400 transition">
                                        Iniciar Sesión
                                    </a>

                                    @if (Route::has('register'))
                                        <a href="{{ route('register') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition">
                                            Registrarse
                                        </a>
                                    @endif
                                @endauth
                            </div>
                        @endif
                    </div>
                </nav>
            </header>

            <!-- Hero Section -->
            <main class="flex-1">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 sm:py-16 lg:py-20">
                    <div class="lg:grid lg:grid-cols-12 lg:gap-8">
                        <!-- Content -->
                        <div class="sm:text-center md:max-w-2xl md:mx-auto lg:col-span-6 lg:text-left">
                            <h1>
                                <span class="block text-sm font-semibold uppercase tracking-wide text-indigo-600 dark:text-indigo-400">
                                    Inteligencia Artificial Educativa
                                </span>
                                <span class="mt-1 block text-4xl tracking-tight font-extrabold sm:text-5xl xl:text-6xl">
                                    <span class="block text-gray-900 dark:text-white">Sistema de</span>
                                    <span class="block text-indigo-600 dark:text-indigo-400">Predicción Académica</span>
                                </span>
                            </h1>
                            <p class="mt-3 text-base text-gray-500 dark:text-gray-400 sm:mt-5 sm:text-xl lg:text-lg xl:text-xl">
                                Plataforma avanzada que utiliza Inteligencia Artificial para predecir el rendimiento académico de los estudiantes, 
                                identificar áreas de mejora y proporcionar recomendaciones personalizadas.
                            </p>

                            <!-- Features -->
                            <div class="mt-8 space-y-4">
                                <div class="flex items-start">
                                    <div class="flex-shrink-0">
                                        <svg class="h-6 w-6 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </div>
                                    <p class="ml-3 text-base text-gray-700 dark:text-gray-300">
                                        Predicciones precisas basadas en IA avanzada
                                    </p>
                                </div>
                                <div class="flex items-start">
                                    <div class="flex-shrink-0">
                                        <svg class="h-6 w-6 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </div>
                                    <p class="ml-3 text-base text-gray-700 dark:text-gray-300">
                                        Análisis de rendimiento en tiempo real
                                    </p>
                                </div>
                                <div class="flex items-start">
                                    <div class="flex-shrink-0">
                                        <svg class="h-6 w-6 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </div>
                                    <p class="ml-3 text-base text-gray-700 dark:text-gray-300">
                                        Recomendaciones personalizadas para cada estudiante
                                    </p>
                                </div>
                                <div class="flex items-start">
                                    <div class="flex-shrink-0">
                                        <svg class="h-6 w-6 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </div>
                                    <p class="ml-3 text-base text-gray-700 dark:text-gray-300">
                                        Gestión integral de notas, asistencias y recursos educativos
                                    </p>
                                </div>
                            </div>

                            <!-- CTA Buttons -->
                            @guest
                            <div class="mt-8 sm:max-w-lg sm:mx-auto sm:text-center lg:text-left lg:mx-0">
                                <div class="flex flex-col sm:flex-row gap-4">
                                    <a href="{{ route('register') }}" class="inline-flex items-center justify-center px-6 py-3 border border-transparent text-base font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 transition">
                                        Comenzar Ahora
                                        <svg class="ml-2 -mr-1 w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z" clip-rule="evenodd" />
                                        </svg>
                                    </a>
                                    <a href="{{ route('login') }}" class="inline-flex items-center justify-center px-6 py-3 border border-gray-300 dark:border-gray-600 text-base font-medium rounded-md text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                                        Iniciar Sesión
                                    </a>
                                </div>
                            </div>
                            @endguest
                        </div>

                        <!-- Illustration -->
                        <div class="mt-12 relative sm:max-w-lg sm:mx-auto lg:mt-0 lg:max-w-none lg:mx-0 lg:col-span-6 lg:flex lg:items-center">
                            <div class="relative mx-auto w-full rounded-lg shadow-lg lg:max-w-md">
                                <div class="relative block w-full bg-white dark:bg-gray-800 rounded-lg overflow-hidden">
                                    <div class="p-8">
                                        <!-- Stats -->
                                        <div class="space-y-6">
                                            <div class="flex items-center justify-between p-4 bg-indigo-50 dark:bg-indigo-900/20 rounded-lg">
                                                <div>
                                                    <p class="text-sm font-medium text-indigo-600 dark:text-indigo-400">Predicciones Generadas</p>
                                                    <p class="text-2xl font-bold text-gray-900 dark:text-white">1,234+</p>
                                                </div>
                                                <svg class="h-12 w-12 text-indigo-600 dark:text-indigo-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                                </svg>
                                            </div>

                                            <div class="flex items-center justify-between p-4 bg-green-50 dark:bg-green-900/20 rounded-lg">
                                                <div>
                                                    <p class="text-sm font-medium text-green-600 dark:text-green-400">Estudiantes Activos</p>
                                                    <p class="text-2xl font-bold text-gray-900 dark:text-white">850+</p>
                                                </div>
                                                <svg class="h-12 w-12 text-green-600 dark:text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                                </svg>
                                            </div>

                                            <div class="flex items-center justify-between p-4 bg-purple-50 dark:bg-purple-900/20 rounded-lg">
                                                <div>
                                                    <p class="text-sm font-medium text-purple-600 dark:text-purple-400">Precisión de IA</p>
                                                    <p class="text-2xl font-bold text-gray-900 dark:text-white">94.5%</p>
                                                </div>
                                                <svg class="h-12 w-12 text-purple-600 dark:text-purple-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
                                                </svg>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Features Section -->
                <div class="bg-white dark:bg-gray-800 mt-16">
                    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
                        <div class="text-center">
                            <h2 class="text-3xl font-extrabold text-gray-900 dark:text-white sm:text-4xl">
                                Características Principales
                            </h2>
                            <p class="mt-4 text-lg text-gray-500 dark:text-gray-400">
                                Todo lo que necesitas para mejorar el rendimiento académico
                            </p>
                        </div>

                        <div class="mt-12">
                            <div class="grid grid-cols-1 gap-8 sm:grid-cols-2 lg:grid-cols-3">
                                <!-- Feature 1 -->
                                <div class="pt-6">
                                    <div class="flow-root bg-gray-50 dark:bg-gray-700 rounded-lg px-6 pb-8 h-full">
                                        <div class="-mt-6">
                                            <div>
                                                <span class="inline-flex items-center justify-center p-3 bg-indigo-500 rounded-md shadow-lg">
                                                    <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                                    </svg>
                                                </span>
                                            </div>
                                            <h3 class="mt-8 text-lg font-medium text-gray-900 dark:text-white tracking-tight">Predicciones con IA</h3>
                                            <p class="mt-5 text-base text-gray-500 dark:text-gray-400">
                                                Algoritmos avanzados analizan el rendimiento histórico para predecir resultados futuros con alta precisión.
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Feature 2 -->
                                <div class="pt-6">
                                    <div class="flow-root bg-gray-50 dark:bg-gray-700 rounded-lg px-6 pb-8 h-full">
                                        <div class="-mt-6">
                                            <div>
                                                <span class="inline-flex items-center justify-center p-3 bg-green-500 rounded-md shadow-lg">
                                                    <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                                    </svg>
                                                </span>
                                            </div>
                                            <h3 class="mt-8 text-lg font-medium text-gray-900 dark:text-white tracking-tight">Recursos Educativos</h3>
                                            <p class="mt-5 text-base text-gray-500 dark:text-gray-400">
                                                Biblioteca completa de recursos personalizados recomendados según las necesidades de cada estudiante.
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Feature 3 -->
                                <div class="pt-6">
                                    <div class="flow-root bg-gray-50 dark:bg-gray-700 rounded-lg px-6 pb-8 h-full">
                                        <div class="-mt-6">
                                            <div>
                                                <span class="inline-flex items-center justify-center p-3 bg-purple-500 rounded-md shadow-lg">
                                                    <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                                                    </svg>
                                                </span>
                                            </div>
                                            <h3 class="mt-8 text-lg font-medium text-gray-900 dark:text-white tracking-tight">Gestión de Notas</h3>
                                            <p class="mt-5 text-base text-gray-500 dark:text-gray-400">
                                                Sistema completo para registrar, analizar y visualizar el progreso académico de los estudiantes.
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Feature 4 -->
                                <div class="pt-6">
                                    <div class="flow-root bg-gray-50 dark:bg-gray-700 rounded-lg px-6 pb-8 h-full">
                                        <div class="-mt-6">
                                            <div>
                                                <span class="inline-flex items-center justify-center p-3 bg-yellow-500 rounded-md shadow-lg">
                                                    <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                    </svg>
                                                </span>
                                            </div>
                                            <h3 class="mt-8 text-lg font-medium text-gray-900 dark:text-white tracking-tight">Control de Asistencia</h3>
                                            <p class="mt-5 text-base text-gray-500 dark:text-gray-400">
                                                Registro y seguimiento de asistencia con análisis de impacto en el rendimiento académico.
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Feature 5 -->
                                <div class="pt-6">
                                    <div class="flow-root bg-gray-50 dark:bg-gray-700 rounded-lg px-6 pb-8 h-full">
                                        <div class="-mt-6">
                                            <div>
                                                <span class="inline-flex items-center justify-center p-3 bg-red-500 rounded-md shadow-lg">
                                                    <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                                    </svg>
                                                </span>
                                            </div>
                                            <h3 class="mt-8 text-lg font-medium text-gray-900 dark:text-white tracking-tight">Alertas Tempranas</h3>
                                            <p class="mt-5 text-base text-gray-500 dark:text-gray-400">
                                                Detección automática de estudiantes en riesgo para intervención oportuna.
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Feature 6 -->
                                <div class="pt-6">
                                    <div class="flow-root bg-gray-50 dark:bg-gray-700 rounded-lg px-6 pb-8 h-full">
                                        <div class="-mt-6">
                                            <div>
                                                <span class="inline-flex items-center justify-center p-3 bg-blue-500 rounded-md shadow-lg">
                                                    <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                    </svg>
                                                </span>
                                            </div>
                                            <h3 class="mt-8 text-lg font-medium text-gray-900 dark:text-white tracking-tight">Reportes Detallados</h3>
                                            <p class="mt-5 text-base text-gray-500 dark:text-gray-400">
                                                Informes completos y visualizaciones para toma de decisiones informadas.
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>

            <!-- Footer -->
            <footer class="bg-white dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700">
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    <p class="text-center text-sm text-gray-500 dark:text-gray-400">
                        &copy; {{ date('Y') }} Sistema de Predicción Académica. Desarrollado con Laravel y Tecnología de IA.
                    </p>
                </div>
            </footer>
        </div>
    </body>
</html>