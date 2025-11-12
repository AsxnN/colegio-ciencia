
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
                    {{-- Menú para ADMINISTRADOR --}}
                    @if(Auth::check() && Auth::user()->rol && Auth::user()->rol->nombre === 'Administrador')
                        <x-nav-link href="{{ route('dashboard') }}" :active="request()->routeIs('dashboard')">
                            {{ __('Dashboard') }}
                        </x-nav-link>
                        <x-nav-link href="{{ route('usuarios.index') }}" :active="request()->routeIs('usuarios.*')">
                            {{ __('Usuarios') }}
                        </x-nav-link>
                        <x-nav-link href="{{ route('administradores.index') }}" :active="request()->routeIs('administradores.*')">
                            {{ __('Administradores') }}
                        </x-nav-link>
                        <x-nav-link href="{{ route('docentes.index') }}" :active="request()->routeIs('docentes.*')">
                            {{ __('Docentes') }}
                        </x-nav-link>
                        <x-nav-link href="{{ route('estudiantes.index') }}" :active="request()->routeIs('estudiantes.*')">
                            {{ __('Estudiantes') }}
                        </x-nav-link>
                        <x-nav-link href="{{ route('secciones.index') }}" :active="request()->routeIs('secciones.*')">
                            {{ __('Secciones') }}
                        </x-nav-link>
                        <x-nav-link href="{{ route('cursos.index') }}" :active="request()->routeIs('cursos.*')">
                            {{ __('Cursos') }}
                        </x-nav-link>
                        <x-nav-link href="{{ route('notas.index') }}" :active="request()->routeIs('notas.*')">
                            {{ __('Notas') }}
                        </x-nav-link>
                        <x-nav-link href="{{ route('asistencias.index') }}" :active="request()->routeIs('asistencias.*')">
                            {{ __('Asistencias') }}
                        </x-nav-link>
                        <x-nav-link href="{{ route('recursos.index') }}" :active="request()->routeIs('recursos.*')">
                            {{ __('Recursos') }}
                        </x-nav-link>
                        <x-nav-link href="{{ route('predicciones.index') }}" :active="request()->routeIs('predicciones.*')">
                            {{ __('Predicciones') }}
                        </x-nav-link>
                    
                    {{-- Menú para DOCENTE --}}
                    @elseif(Auth::check() && Auth::user()->rol && Auth::user()->rol->nombre === 'Docente')
                        <x-nav-link href="{{ route('dashboard') }}" :active="request()->routeIs('dashboard')">
                            {{ __('Dashboard') }}
                        </x-nav-link>
                        <x-nav-link href="{{ route('notas.index') }}" :active="request()->routeIs('notas.*')">
                            {{ __('Notas') }}
                        </x-nav-link>
                        <x-nav-link href="{{ route('asistencias.index') }}" :active="request()->routeIs('asistencias.*')">
                            {{ __('Asistencias') }}
                        </x-nav-link>
                        <x-nav-link href="{{ route('recursos.index') }}" :active="request()->routeIs('recursos.*')">
                            {{ __('Recursos') }}
                        </x-nav-link>
                        <x-nav-link href="{{ route('docente.predicciones') }}" :active="request()->routeIs('docente.predicciones')">
                            {{ __('Predicciones') }}
                        </x-nav-link>
                    
                    {{-- Menú para ESTUDIANTE --}}
                    @elseif(Auth::check() && Auth::user()->rol && Auth::user()->rol->nombre === 'Estudiante')
                        <x-nav-link href="{{ route('estudiant.dashboard') }}" :active="request()->routeIs('estudiant.dashboard')">
                            {{ __('Inicio') }}
                        </x-nav-link>
                        <x-nav-link href="{{ route('estudiant.notas') }}" :active="request()->routeIs('estudiant.notas')">
                            {{ __('Notas') }}
                        </x-nav-link>
                        <x-nav-link href="{{ route('estudiant.prediccion') }}" :active="request()->routeIs('estudiant.prediccion')">
                            {{ __('Predicción') }}
                        </x-nav-link>
                        <x-nav-link href="{{ route('estudiant.recomendaciones') }}" :active="request()->routeIs('estudiant.recomendaciones')">
                            {{ __('Recomendaciones') }}
                        </x-nav-link>
                        <x-nav-link href="{{ route('estudiant.recursos') }}" :active="request()->routeIs('estudiant.recursos')">
                            {{ __('Recursos') }}
                        </x-nav-link>
                        <x-nav-link href="{{ route('estudiant.alertas') }}" :active="request()->routeIs('estudiant.alertas')">
                            {{ __('Alertas') }}
                        </x-nav-link>
                        <x-nav-link href="{{ route('estudiant.tutorias') }}" :active="request()->routeIs('estudiant.tutorias')">
                            {{ __('Tutorías') }}
                        </x-nav-link>
                        <x-nav-link href="{{ route('estudiant.soporte') }}" :active="request()->routeIs('estudiant.soporte')">
                            {{ __('Soporte') }}
                        </x-nav-link>
                    
                    {{-- Menú por defecto si no tiene rol --}}
                    @else
                        <x-nav-link href="{{ route('dashboard') }}" :active="request()->routeIs('dashboard')">
                            {{ __('Dashboard') }}
                        </x-nav-link>
                    @endif
                </div>
            </div>

            <!-- Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ms-6">
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

                        @if(Auth::check() && Auth::user()->rol && Auth::user()->rol->nombre === 'Estudiante')
                            <x-dropdown-link href="{{ route('estudiant.perfil') }}">
                                {{ __('Mi Perfil Académico') }}
                            </x-dropdown-link>
                        @endif

                        @if (Laravel\Jetstream\Jetstream::hasApiFeatures())
                            <x-dropdown-link href="{{ route('api-tokens.index') }}">
                                {{ __('API Tokens') }}
                            </x-dropdown-link>
                        @endif

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
            {{-- Menú Responsive para ADMINISTRADOR --}}
            @if(Auth::check() && Auth::user()->rol && Auth::user()->rol->nombre === 'Administrador')
                <x-responsive-nav-link href="{{ route('dashboard') }}" :active="request()->routeIs('dashboard')">
                    {{ __('Dashboard') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link href="{{ route('usuarios.index') }}" :active="request()->routeIs('usuarios.*')">
                    {{ __('Usuarios') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link href="{{ route('administradores.index') }}" :active="request()->routeIs('administradores.*')">
                    {{ __('Administradores') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link href="{{ route('docentes.index') }}" :active="request()->routeIs('docentes.*')">
                    {{ __('Docentes') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link href="{{ route('estudiantes.index') }}" :active="request()->routeIs('estudiantes.*')">
                    {{ __('Estudiantes') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link href="{{ route('secciones.index') }}" :active="request()->routeIs('secciones.*')">
                    {{ __('Secciones') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link href="{{ route('cursos.index') }}" :active="request()->routeIs('cursos.*')">
                    {{ __('Cursos') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link href="{{ route('notas.index') }}" :active="request()->routeIs('notas.*')">
                    {{ __('Notas') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link href="{{ route('asistencias.index') }}" :active="request()->routeIs('asistencias.*')">
                    {{ __('Asistencias') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link href="{{ route('recursos.index') }}" :active="request()->routeIs('recursos.*')">
                    {{ __('Recursos') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link href="{{ route('predicciones.index') }}" :active="request()->routeIs('predicciones.*')">
                    {{ __('Predicciones') }}
                </x-responsive-nav-link>

            {{-- Menú Responsive para DOCENTE --}}
            @elseif(Auth::check() && Auth::user()->rol && Auth::user()->rol->nombre === 'Docente')
                <x-responsive-nav-link href="{{ route('dashboard') }}" :active="request()->routeIs('dashboard')">
                    {{ __('Dashboard') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link href="{{ route('notas.index') }}" :active="request()->routeIs('notas.*')">
                    {{ __('Notas') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link href="{{ route('asistencias.index') }}" :active="request()->routeIs('asistencias.*')">
                    {{ __('Asistencias') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link href="{{ route('recursos.index') }}" :active="request()->routeIs('recursos.*')">
                    {{ __('Recursos') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link href="{{ route('docente.predicciones') }}" :active="request()->routeIs('docente.predicciones')">
                    {{ __('Predicciones') }}
                </x-responsive-nav-link>

            {{-- Menú Responsive para ESTUDIANTE --}}
            @elseif(Auth::check() && Auth::user()->rol && Auth::user()->rol->nombre === 'Estudiante')
                <x-responsive-nav-link href="{{ route('estudiant.dashboard') }}" :active="request()->routeIs('estudiant.dashboard')">
                    🏠 {{ __('Inicio') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link href="{{ route('estudiant.cursos') }}" :active="request()->routeIs('estudiant.cursos')">
                    📚 {{ __('Cursos') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link href="{{ route('estudiant.notas') }}" :active="request()->routeIs('estudiant.notas')">
                    📊 {{ __('Notas') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link href="{{ route(name: 'estudiant.prediccion') }}" :active="request()->routeIs('estudiant.prediccion')">
                    🔮 {{ __('Predicción') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link href="{{ route('estudiant.recomendaciones') }}" :active="request()->routeIs('estudiant.recomendaciones')">
                    💡 {{ __('Recomendaciones') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link href="{{ route('estudiant.alertas') }}" :active="request()->routeIs('estudiant.alertas')">
                    🔔 {{ __('Alertas') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link href="{{ route('estudiant.tutorias') }}" :active="request()->routeIs('estudiant.tutorias')">
                    👨‍🏫 {{ __('Tutorías') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link href="{{ route('estudiant.soporte') }}" :active="request()->routeIs('estudiant.soporte')">
                    💬 {{ __('Soporte') }}
                </x-responsive-nav-link>

            {{-- Menú por defecto --}}
            @else
                <x-responsive-nav-link href="{{ route('dashboard') }}" :active="request()->routeIs('dashboard')">
                    {{ __('Dashboard') }}
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

                @if(Auth::check() && Auth::user()->rol && Auth::user()->rol->nombre === 'Estudiante')
                    <x-responsive-nav-link href="{{ route('estudiant.perfil') }}" :active="request()->routeIs('estudiant.perfil')">
                        {{ __('Mi Perfil Académico') }}
                    </x-responsive-nav-link>
                @endif

                @if (Laravel\Jetstream\Jetstream::hasApiFeatures())
                    <x-responsive-nav-link href="{{ route('api-tokens.index') }}" :active="request()->routeIs('api-tokens.index')">
                        {{ __('API Tokens') }}
                    </x-responsive-nav-link>
                @endif

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