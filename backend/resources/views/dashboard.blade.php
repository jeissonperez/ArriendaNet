@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @if(auth()->user()->rol_usuario === 'Arrendador')
            <!-- Estadísticas de Inmuebles -->
            <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Mis Inmuebles</h3>
                    <div class="space-y-3">
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">Disponibles</span>
                            <span class="text-green-600 font-semibold">{{ $estadisticas['inmuebles_disponibles'] ?? 0 }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">Arrendados</span>
                            <span class="text-blue-600 font-semibold">{{ $estadisticas['inmuebles_arrendados'] ?? 0 }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">En Mantenimiento</span>
                            <span class="text-yellow-600 font-semibold">{{ $estadisticas['inmuebles_mantenimiento'] ?? 0 }}</span>
                        </div>
                    </div>
                    <a href="{{ route('inmuebles.index') }}" class="mt-4 inline-block text-blue-600 hover:text-blue-800">
                        Ver todos los inmuebles →
                    </a>
                </div>
            </div>

            <!-- Resumen de Contratos -->
            <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Contratos Activos</h3>
                    <div class="space-y-3">
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">Total Activos</span>
                            <span class="text-green-600 font-semibold">{{ $estadisticas['contratos_activos'] ?? 0 }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">Por Vencer</span>
                            <span class="text-yellow-600 font-semibold">{{ $estadisticas['contratos_por_vencer'] ?? 0 }}</span>
                        </div>
                    </div>
                    <a href="{{ route('contratos.index') }}" class="mt-4 inline-block text-blue-600 hover:text-blue-800">
                        Ver todos los contratos →
                    </a>
                </div>
            </div>

            <!-- Pagos Pendientes -->
            <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Pagos Pendientes</h3>
                    <div class="space-y-3">
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">Por Verificar</span>
                            <span class="text-yellow-600 font-semibold">{{ $estadisticas['pagos_por_verificar'] ?? 0 }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">Cuentas Vencidas</span>
                            <span class="text-red-600 font-semibold">{{ $estadisticas['cuentas_vencidas'] ?? 0 }}</span>
                        </div>
                    </div>
                    <a href="{{ route('cuentas-cobro.index') }}" class="mt-4 inline-block text-blue-600 hover:text-blue-800">
                        Ver todas las cuentas →
                    </a>
                </div>
            </div>
        @else
            <!-- Información del Arrendatario -->
            <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Mis Contratos</h3>
                    <div class="space-y-3">
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">Contratos Activos</span>
                            <span class="text-green-600 font-semibold">{{ $estadisticas['mis_contratos_activos'] ?? 0 }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">Inmuebles Arrendados</span>
                            <span class="text-blue-600 font-semibold">{{ $estadisticas['inmuebles_arrendados'] ?? 0 }}</span>
                        </div>
                    </div>
                    <a href="{{ route('contratos.index') }}" class="mt-4 inline-block text-blue-600 hover:text-blue-800">
                        Ver mis contratos →
                    </a>
                </div>
            </div>

            <!-- Pagos -->
            <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Estado de Pagos</h3>
                    <div class="space-y-3">
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">Pendientes</span>
                            <span class="text-yellow-600 font-semibold">{{ $estadisticas['pagos_pendientes'] ?? 0 }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">Vencidos</span>
                            <span class="text-red-600 font-semibold">{{ $estadisticas['pagos_vencidos'] ?? 0 }}</span>
                        </div>
                    </div>
                    <a href="{{ route('pagos.create') }}" class="mt-4 inline-block text-blue-600 hover:text-blue-800">
                        Registrar nuevo pago →
                    </a>
                </div>
            </div>

            <!-- Solicitudes -->
            <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Mis Solicitudes</h3>
                    <div class="space-y-3">
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">Pendientes</span>
                            <span class="text-yellow-600 font-semibold">{{ $estadisticas['solicitudes_pendientes'] ?? 0 }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">En Proceso</span>
                            <span class="text-blue-600 font-semibold">{{ $estadisticas['solicitudes_proceso'] ?? 0 }}</span>
                        </div>
                    </div>
                    <a href="{{ route('solicitudes.create') }}" class="mt-4 inline-block text-blue-600 hover:text-blue-800">
                        Nueva solicitud de mantenimiento →
                    </a>
                </div>
            </div>
        @endif
    </div>

    <!-- Notificaciones Recientes -->
    <div class="mt-8">
        <h2 class="text-xl font-semibold text-gray-900 mb-4">Notificaciones Recientes</h2>
        <div class="bg-white shadow-sm rounded-lg divide-y divide-gray-200">
            @forelse($notificaciones ?? [] as $notificacion)
                <div class="p-4 hover:bg-gray-50">
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            @if($notificacion->tipo === 'pago')
                                <span class="text-green-500">💰</span>
                            @elseif($notificacion->tipo === 'contrato')
                                <span class="text-blue-500">📄</span>
                            @elseif($notificacion->tipo === 'mantenimiento')
                                <span class="text-yellow-500">🔧</span>
                            @endif
                        </div>
                        <div class="ml-3 w-0 flex-1">
                            <p class="text-sm text-gray-900">{{ $notificacion->mensaje }}</p>
                            <p class="mt-1 text-sm text-gray-500">{{ $notificacion->created_at->diffForHumans() }}</p>
                        </div>
                    </div>
                </div>
            @empty
                <div class="p-4">
                    <p class="text-gray-500 text-center">No hay notificaciones recientes</p>
                </div>
            @endforelse
        </div>
    </div>
@endsection