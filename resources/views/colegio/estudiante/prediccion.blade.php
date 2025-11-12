<!-- filepath: c:\laragon\www\colegio-ciencia\resources\views\colegio\estudiante\predicciones.blade.php -->
@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-800">🧠 Predicción de Rendimiento Académico</h1>
        <p class="text-gray-600 mt-2">Analiza tu rendimiento futuro basado en tus hábitos de estudio</p>
    </div>

    <!-- Formulario de Predicción -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-semibold mb-4">📝 Datos para Predicción</h2>
            
            <form id="prediction-form">
                @csrf
                <input type="hidden" name="Student_ID" value="{{ auth()->user()->id }}">
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            📚 Horas de Estudio Semanal
                        </label>
                        <input type="number" name="Hours_Studied" 
                               value="{{ $estudiante->horas_estudio_semanal ?? 10 }}"
                               min="0" max="100" step="0.5"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            📊 Asistencia (%)
                        </label>
                        <input type="number" name="Attendance" 
                               value="{{ $porcentaje_asistencia ?? 85 }}"
                               min="0" max="100" step="1"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            😴 Horas de Sueño Diarias
                        </label>
                        <input type="number" name="Sleep_Hours" 
                               value="{{ $estudiante->horas_sueno ?? 8 }}"
                               min="4" max="12" step="0.5"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            🏃‍♂️ Actividad Física (días/semana)
                        </label>
                        <input type="number" name="Physical_Activity" 
                               value="{{ $estudiante->actividad_fisica ?? 3 }}"
                               min="0" max="7" step="1"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            👨‍🏫 Sesiones de Tutoría (mes)
                        </label>
                        <input type="number" name="Tutoring_Sessions" 
                               value="{{ $estudiante->sesiones_tutoria ?? 0 }}"
                               min="0" max="20" step="1"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            📈 Promedio Anterior
                        </label>
                        <input type="number" name="Previous_Scores" 
                               value="{{ $promedio_anterior ?? 12 }}"
                               min="0" max="20" step="0.1" readonly
                               class="w-full px-3 py-2 bg-gray-100 border border-gray-300 rounded-md">
                        <small class="text-gray-500">Calculado automáticamente</small>
                    </div>
                </div>

                <button type="submit" 
                        class="mt-6 w-full bg-blue-600 text-white px-6 py-3 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-colors">
                    🔮 Generar Predicción
                </button>
            </form>
        </div>

        <!-- Resultado de Predicción -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-semibold mb-4">🎯 Resultado de Predicción</h2>
            
            <div id="prediction-result" class="hidden">
                <div class="text-center mb-4">
                    <div class="text-6xl font-bold" id="predicted-score">--</div>
                    <div class="text-gray-600">Nota predicha (0-20)</div>
                </div>
                
                <div class="bg-gray-50 p-4 rounded-lg mb-4">
                    <div class="text-sm text-gray-600 mb-2">Modelo utilizado:</div>
                    <div id="model-type" class="font-medium">--</div>
                </div>

                <div id="interpretation" class="text-lg font-medium text-center p-3 rounded-lg">
                    --
                </div>

                <!-- Factores de Influencia -->
                <div class="mt-6">
                    <h3 class="font-semibold mb-3">📊 Factores Analizados</h3>
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span>Horas de estudio:</span>
                            <span id="factor-study">--</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Asistencia:</span>
                            <span id="factor-attendance">--</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Horas de sueño:</span>
                            <span id="factor-sleep">--</span>
                        </div>
                    </div>
                </div>
            </div>

            <div id="prediction-loading" class="text-center text-gray-500">
                Completa el formulario para ver tu predicción
            </div>
        </div>
    </div>

    <!-- Historial de Predicciones -->
    <div class="mt-8 bg-white rounded-lg shadow-md p-6">
        <h2 class="text-xl font-semibold mb-4">📚 Historial de Predicciones</h2>
        
        <div class="overflow-x-auto">
            <table class="min-w-full table-auto">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Fecha</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Predicción</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Horas Estudio</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Asistencia</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Estado</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($historial_predicciones ?? [] as $prediccion)
                    <tr>
                        <td class="px-4 py-2 text-sm text-gray-900">
                            {{ $prediccion->created_at->format('d/m/Y H:i') }}
                        </td>
                        <td class="px-4 py-2 text-sm font-medium">
                            <span class="px-2 py-1 rounded-full text-xs 
                                @if($prediccion->nota_predicha >= 16) bg-green-100 text-green-800
                                @elseif($prediccion->nota_predicha >= 14) bg-blue-100 text-blue-800  
                                @elseif($prediccion->nota_predicha >= 12) bg-yellow-100 text-yellow-800
                                @else bg-red-100 text-red-800 @endif">
                                {{ number_format($prediccion->nota_predicha, 2) }}
                            </span>
                        </td>
                        <td class="px-4 py-2 text-sm text-gray-600">{{ $prediccion->horas_estudio }}h</td>
                        <td class="px-4 py-2 text-sm text-gray-600">{{ $prediccion->asistencia }}%</td>
                        <td class="px-4 py-2 text-sm">
                            @if($prediccion->nota_predicha >= 14)
                                <span class="text-green-600">✅ Excelente</span>
                            @elseif($prediccion->nota_predicha >= 12)
                                <span class="text-yellow-600">⚠️ Promedio</span>
                            @else
                                <span class="text-red-600">🚨 Riesgo</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-4 py-8 text-center text-gray-500">
                            No hay predicciones anteriores. ¡Genera tu primera predicción!
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
document.getElementById('prediction-form').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const loadingEl = document.getElementById('prediction-loading');
    const resultEl = document.getElementById('prediction-result');
    
    loadingEl.textContent = '🔄 Generando predicción...';
    resultEl.classList.add('hidden');
    
    try {
        const formData = new FormData(this);
        const data = Object.fromEntries(formData.entries());
        
        const response = await fetch('{{ route("estudiant.prediccion.generar") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('[name="_token"]').value
            },
            body: JSON.stringify(data)
        });
        
        const result = await response.json();
        
        if (result.success) {
            document.getElementById('predicted-score').textContent = result.prediction.toFixed(2);
            document.getElementById('model-type').textContent = result.model_type === 'neural_network' ? 
                '🧠 Red Neuronal [10-20-10]' : '🌲 Random Forest';
            document.getElementById('interpretation').textContent = result.interpretacion;
            
            // Actualizar factores
            document.getElementById('factor-study').textContent = data.Hours_Studied + 'h';
            document.getElementById('factor-attendance').textContent = data.Attendance + '%';
            document.getElementById('factor-sleep').textContent = data.Sleep_Hours + 'h';
            
            // Mostrar resultado
            loadingEl.classList.add('hidden');
            resultEl.classList.remove('hidden');
            
            // Recargar página para mostrar nuevo historial
            setTimeout(() => window.location.reload(), 2000);
        } else {
            loadingEl.textContent = '❌ Error: ' + result.error;
        }
    } catch (error) {
        loadingEl.textContent = '❌ Error de conexión';
        console.error('Error:', error);
    }
});
</script>
@endsection