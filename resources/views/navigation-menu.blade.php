<nav x-data="{ open: false }" class="bg-white border-b border-gray-100">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}">
                        <x-application-mark class="block h-9 w-auto" />
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    @php
                        $dashboardRoute = route('dashboard');
                        if (Auth::check()) {
                            if (Auth::user()->esAdministrador()) {
                                $dashboardRoute = route('admin.dashboard');
                            } elseif (Auth::user()->esDocente()) {
                                $dashboardRoute = route('docente.dashboard');
                            } elseif (Auth::user()->esEstudiante()) {
                                $dashboardRoute = route('estudiante.dashboard');
                            }
                        }
                    @endphp
                    <x-nav-link href="{{ $dashboardRoute }}" :active="request()->routeIs('dashboard') || request()->routeIs('admin.*') || request()->routeIs('docente.*') || request()->routeIs('estudiante.*')">
                        {{ __('Dashboard') }}
                    </x-nav-link>

                    @if(Auth::check() && Auth::user()->esAdministrador())
                        <!-- Enlaces para Administrador - SOLO rutas que existen -->
                        <x-nav-link href="{{ route('admin.usuarios.index') }}" :active="request()->routeIs('admin.usuarios.*')">
                            {{ __('Usuarios') }}
                        </x-nav-link>
                        <x-nav-link href="{{ route('admin.administradores.index') }}" :active="request()->routeIs('admin.administradores.*')">
                            {{ __('Administradores') }}
                        </x-nav-link>
                        <x-nav-link href="{{ route('admin.docentes.index') }}" :active="request()->routeIs('admin.docentes.*')">
                            {{ __('Docentes') }}
                        </x-nav-link>
                        <x-nav-link href="{{ route('admin.estudiantes.index') }}" :active="request()->routeIs('admin.estudiantes.*')">
                            {{ __('Estudiantes') }}
                        </x-nav-link>
                        <x-nav-link href="{{ route('admin.secciones.index') }}" :active="request()->routeIs('admin.secciones.*')">
                            {{ __('Secciones') }}
                        </x-nav-link>
                        <x-nav-link href="{{ route('admin.cursos.index') }}" :active="request()->routeIs('admin.cursos.*')">
                            {{ __('Cursos') }}
                        </x-nav-link>
                        <x-nav-link href="{{ route('admin.notas.index') }}" :active="request()->routeIs('admin.notas.*')">
                            {{ __('Notas') }}
                        </x-nav-link>
                        <x-nav-link href="{{ route('admin.asistencias.index') }}" :active="request()->routeIs('admin.asistencias.*')">
                            {{ __('Asistencias') }}
                        </x-nav-link>
                        <x-nav-link href="{{ route('admin.recursos.index') }}" :active="request()->routeIs('admin.recursos.*')">
                            {{ __('Recursos') }}
                        </x-nav-link>
                        {{-- ✅ USAR RUTA DE PREDICCIONES COMPARTIDA EN LUGAR DE admin.predicciones --}}
                        <x-nav-link href="{{ route('predicciones.index') }}" :active="request()->routeIs('predicciones.*')">
                            {{ __('Predicciones') }}
                        </x-nav-link>

                    @elseif(Auth::check() && Auth::user()->esDocente())
                        <!-- Enlaces para Docente -->
                        <x-nav-link href="{{ route('docente.cursos') }}" :active="request()->routeIs('docente.cursos')">
                            {{ __('Mis Cursos') }}
                        </x-nav-link>
                        <x-nav-link href="{{ route('docente.estudiantes') }}" :active="request()->routeIs('docente.estudiantes')">
                            {{ __('Estudiantes') }}
                        </x-nav-link>
                        <x-nav-link href="{{ route('docente.actividades') }}" :active="request()->routeIs('docente.actividades')">
                            {{ __('Actividades') }}
                        </x-nav-link>
                        <x-nav-link href="{{ route('docente.predicciones') }}" :active="request()->routeIs('docente.predicciones')">
                            {{ __('Predicciones IA') }}
                        </x-nav-link>
                        <x-nav-link href="{{ route('docente.recomendaciones') }}" :active="request()->routeIs('docente.recomendaciones')">
                            {{ __('Recomendaciones') }}
                        </x-nav-link>
                        <x-nav-link href="{{ route('docente.historial') }}" :active="request()->routeIs('docente.historial')">
                            {{ __('Historial') }}
                        </x-nav-link>

                    @elseif(Auth::check() && Auth::user()->esEstudiante())
                        <!-- Enlaces para Estudiante -->
                        <x-nav-link href="{{ route('estudiante.prediccion') }}" :active="request()->routeIs('estudiante.prediccion')">
                            {{ __('Mi Predicción') }}
                        </x-nav-link>
                        @php $estudiante = Auth::user()->estudiante ?? null; @endphp
                        @if($estudiante)
                            <x-nav-link href="{{ route('estudiante.perfil', $estudiante->id) }}" :active="request()->routeIs('estudiante.perfil')">
                                {{ __('Mi Perfil') }}
                            </x-nav-link>
                            <x-nav-link href="{{ route('estudiante.notas', $estudiante->id) }}" :active="request()->routeIs('estudiante.notas')">
                                {{ __('Mis Notas') }}
                            </x-nav-link>
                            <x-nav-link href="{{ route('estudiante.cursos', $estudiante->id) }}" :active="request()->routeIs('estudiante.cursos')">
                                {{ __('Mis Cursos') }}
                            </x-nav-link>
                        @else
                            <x-nav-link href="{{ route('predicciones.index') }}" :active="request()->routeIs('predicciones.*')">
                                {{ __('Mis Predicciones') }}
                            </x-nav-link>
                        @endif

                    @else
                        <!-- Usuario sin rol definido: mostrar enlaces básicos -->
                        <x-nav-link href="{{ route('predicciones.index') }}" :active="request()->routeIs('predicciones.*')">
                            {{ __('Predicciones') }}
                        </x-nav-link>
                    @endif
                </div>
            </div>

            <!-- Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <!-- Settings Dropdown -->
                <div class="ms-3 relative">
                    <x-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            @if (Laravel\Jetstream\Jetstream::managesProfilePhotos())
                            <button class="flex text-sm border-2 border-transparent rounded-full focus:outline-none focus:border-gray-300 transition">
                                <img class="h-8 w-8 rounded-full object-cover" src="{{ Auth::user()->profile_photo_url }}" alt="{{ Auth::user()->name }}" />
                            </button>
                            @else
                            <span class="inline-flex rounded-md">
                                <button type="button" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none focus:bg-gray-50 active:bg-gray-50 transition ease-in-out duration-150">
                                    {{ Auth::user()->name }}
                                    <svg class="ms-2 -me-0.5 h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                                    </svg>
                                </button>
                            </span>
                            @endif
                        </x-slot>

                        <x-slot name="content">
                            <!-- Account Management -->
                            <div class="block px-4 py-2 text-xs text-gray-400">
                                {{ __('Manage Account') }}
                            </div>

                            <x-dropdown-link href="{{ route('profile.show') }}">
                                {{ __('Profile') }}
                            </x-dropdown-link>

                            <div class="border-t border-gray-200"></div>

                            <!-- Authentication -->
                            <form method="POST" action="{{ route('logout') }}" x-data>
                                @csrf
                                <x-dropdown-link href="{{ route('logout') }}"
                                    @click.prevent="$root.submit();">
                                    {{ __('Log Out') }}
                                </x-dropdown-link>
                            </form>
                        </x-slot>
                    </x-dropdown>
                </div>
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            @php
                $dashboardRouteResponsive = route('dashboard');
                if (Auth::check()) {
                    if (Auth::user()->esAdministrador()) {
                        $dashboardRouteResponsive = route('admin.dashboard');
                    } elseif (Auth::user()->esDocente()) {
                        $dashboardRouteResponsive = route('docente.dashboard');
                    } elseif (Auth::user()->esEstudiante()) {
                        $dashboardRouteResponsive = route('estudiante.dashboard');
                    }
                }
            @endphp
            <x-responsive-nav-link href="{{ $dashboardRouteResponsive }}" :active="request()->routeIs('dashboard') || request()->routeIs('admin.*') || request()->routeIs('docente.*') || request()->routeIs('estudiante.*')">
                {{ __('Dashboard') }}
            </x-responsive-nav-link>

            @if(Auth::check() && Auth::user()->esAdministrador())
                <x-responsive-nav-link href="{{ route('admin.usuarios.index') }}" :active="request()->routeIs('admin.usuarios.*')">
                    {{ __('Usuarios') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link href="{{ route('admin.administradores.index') }}" :active="request()->routeIs('admin.administradores.*')">
                    {{ __('Administradores') }}
                </x-responsive-nav-link>

            @elseif(Auth::check() && Auth::user()->esDocente())
                <x-responsive-nav-link href="{{ route('docente.cursos') }}" :active="request()->routeIs('docente.cursos')">
                    {{ __('Mis Cursos') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link href="{{ route('docente.estudiantes') }}" :active="request()->routeIs('docente.estudiantes')">
                    {{ __('Estudiantes') }}
                </x-responsive-nav-link>

            @elseif(Auth::check() && Auth::user()->esEstudiante())
                <x-responsive-nav-link href="{{ route('estudiante.prediccion') }}" :active="request()->routeIs('estudiante.*')">
                    {{ __('Mi Predicción') }}
                </x-responsive-nav-link>
                @php $estudiante = Auth::user()->estudiante ?? null; @endphp
                @if($estudiante)
                    <x-responsive-nav-link href="{{ route('estudiante.cursos', $estudiante->id) }}" :active="request()->routeIs('estudiante.cursos')">
                        {{ __('Mis Cursos') }}
                    </x-responsive-nav-link>
                @endif
            @else
                <x-responsive-nav-link href="{{ route('predicciones.index') }}" :active="request()->routeIs('predicciones.*')">
                    {{ __('Predicciones') }}
                </x-responsive-nav-link>
            @endif
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-gray-200">
            <div class="flex items-center px-4">
                @if (Laravel\Jetstream\Jetstream::managesProfilePhotos())
                <div class="shrink-0 me-3">
                    <img class="h-10 w-10 rounded-full object-cover" src="{{ Auth::user()->profile_photo_url }}" alt="{{ Auth::user()->name }}" />
                </div>
                @endif

                <div>
                    <div class="font-medium text-base text-gray-800">{{ Auth::user()->name }}</div>
                    <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
                </div>
            </div>

            <div class="mt-3 space-y-1">
                <!-- Account Management -->
                <x-responsive-nav-link href="{{ route('profile.show') }}" :active="request()->routeIs('profile.show')">
                    {{ __('Profile') }}
                </x-responsive-nav-link>

                <!-- Authentication -->
                <form method="POST" action="{{ route('logout') }}" x-data>
                    @csrf
                    <x-responsive-nav-link href="{{ route('logout') }}"
                        @click.prevent="$root.submit();">
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>