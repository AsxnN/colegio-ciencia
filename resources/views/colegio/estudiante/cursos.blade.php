{{-- filepath: c:\laragon\www\colegio\resources\views\colegio\estudiante\cursos.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="bg-white shadow rounded-lg overflow-hidden">
        <div class="px-6 py-4 border-b">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-lg font-semibold text-gray-900">Cursos asignados</h1>
                    <p class="mt-1 text-sm text-gray-500">Lista de cursos y docentes responsables.</p>
                </div>
                <div>
                    <a href="#" class="inline-flex items-center px-3 py-2 bg-indigo-600 text-white rounded text-sm hover:bg-indigo-700">Ver calendario</a>
                </div>
            </div>
        </div>

        <div class="p-6">
            @if($cursos->isEmpty())
                <div class="p-6 text-center border-2 border-dashed border-gray-200 rounded-lg">
                    <p class="text-gray-500">No hay cursos asignados.</p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Curso</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Docente</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Descripción</th>
                                <th class="px-4 py-3"></th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($cursos as $curso)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">{{ $curso->nombre ?? '—' }}</div>
                                        <div class="text-xs text-gray-500">Código: {{ $curso->codigo ?? '—' }}</div>
                                    </td>

                                    <td class="px-4 py-4 whitespace-nowrap">
                                        <div class="flex items-center gap-3">
                                            <div class="h-8 w-8 rounded-full bg-gray-100 flex items-center justify-center text-sm text-gray-500">
                                                {{ strtoupper(substr($curso->docente->usuario->nombres ?? 'U',0,1)) }}
                                            </div>
                                            <div class="text-sm text-gray-900">{{ $curso->docente->usuario->nombres ?? '—' }} {{ $curso->docente->usuario->apellidos ?? '' }}</div>
                                        </div>
                                    </td>

                                    <td class="px-4 py-4 text-sm text-gray-700">{{ Str::limit($curso->descripcion ?? 'Sin descripción', 120) }}</td>

                                    <td class="px-4 py-4 text-right text-sm">
                                        <button type="button" 
                                                class="text-indigo-600 hover:text-indigo-900 focus:outline-none btn-detalles"
                                                data-curso-id="{{ $curso->id }}"
                                                data-curso-nombre="{{ $curso->nombre ?? '' }}"
                                                data-curso-descripcion="{{ $curso->descripcion ?? '' }}"
                                                data-docente-nombre="{{ ($curso->docente->usuario->nombres ?? '') . ' ' . ($curso->docente->usuario->apellidos ?? '') }}">
                                            Detalles
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Modal de Detalles del Curso -->
<div id="modalDetallesCurso" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-1/2 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900" id="modalTitulo">Detalles del Curso</h3>
                <button type="button" class="text-gray-400 hover:text-gray-600" onclick="cerrarModal()">
                    <span class="sr-only">Cerrar</span>
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Nombre del Curso</label>
                    <p id="modalNombreCurso" class="mt-1 text-sm text-gray-900"></p>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700">Docente</label>
                    <p id="modalDocenteNombre" class="mt-1 text-sm text-gray-900"></p>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700">Descripción</label>
                    <p id="modalDescripcion" class="mt-1 text-sm text-gray-900"></p>
                </div>
            </div>
            
            <div class="mt-6 flex justify-end">
                <button type="button" 
                        class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 focus:outline-none"
                        onclick="cerrarModal()">
                    Cerrar
                </button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const botonesDetalles = document.querySelectorAll('.btn-detalles');
    
    botonesDetalles.forEach(boton => {
        boton.addEventListener('click', function() {
            const cursoId = this.getAttribute('data-curso-id');
            const cursoNombre = this.getAttribute('data-curso-nombre');
            const cursoDescripcion = this.getAttribute('data-curso-descripcion');
            const docenteNombre = this.getAttribute('data-docente-nombre');
            
            document.getElementById('modalNombreCurso').textContent = cursoNombre || '—';
            document.getElementById('modalDocenteNombre').textContent = docenteNombre || '—';
            document.getElementById('modalDescripcion').textContent = cursoDescripcion || 'Sin descripción';
            
            document.getElementById('modalDetallesCurso').classList.remove('hidden');
        });
    });
});

function cerrarModal() {
    document.getElementById('modalDetallesCurso').classList.add('hidden');
}

// Cerrar modal al hacer clic fuera de él
document.addEventListener('click', function(event) {
    const modal = document.getElementById('modalDetallesCurso');
    if (event.target === modal) {
        cerrarModal();
    }
});
</script>

@endsection