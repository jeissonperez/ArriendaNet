@extends('layouts.app')

@section('title', 'Notificaciones')

@section('content')
    <div class="max-w-3xl mx-auto">
        <div class="bg-white rounded-lg shadow">
            <!-- Encabezado -->
            <div class="p-6 border-b border-gray-200">
                <div class="flex justify-between items-center">
                    <h2 class="text-xl font-semibold text-gray-800">Notificaciones</h2>
                    <form action="{{ route('notificaciones.marcar-todas') }}" method="POST" class="inline">
                        @csrf
                        <button type="submit"
                                class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                            Marcar todas como leídas
                        </button>
                    </form>
                </div>
            </div>

            <!-- Lista de Notificaciones -->
            <div class="divide-y divide-gray-200">
                @forelse($notificaciones as $notificacion)
                    <div class="p-6 hover:bg-gray-50 {{ $notificacion->leida ? 'bg-gray-50' : 'bg-white' }}">
                        <div class="flex items-start space-x-3">
                            <!-- Icono -->
                            <div class="flex-shrink-0">
                                @if($notificacion->tipo === 'contrato')
                                    <span class="text-blue-500">📄</span>
                                @elseif($notificacion->tipo === 'pago')
                                    <span class="text-green-500">💰</span>
                                @elseif($notificacion->tipo === 'mantenimiento')
                                    <span class="text-yellow-500">🔧</span>
                                @endif
                            </div>

                            <!-- Contenido -->
                            <div class="flex-1">
                                <div class="flex items-center justify-between">
                                    <p class="text-sm font-medium text-gray-900">{{ $notificacion->titulo }}</p>
                                    <p class="text-sm text-gray-500">{{ $notificacion->created_at->diffForHumans() }}</p>
                                </div>
                                <p class="mt-1 text-sm text-gray-600">{{ $notificacion->mensaje }}</p>
                                
                                <!-- Acciones -->
                                <div class="mt-2 flex items-center space-x-4">
                                    @if($notificacion->url)
                                        <a href="{{ $notificacion->url }}" 
                                           class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                            Ver detalles
                                        </a>
                                    @endif
                                    
                                    @if(!$notificacion->leida)
                                        <form action="{{ route('notificaciones.marcar-leida', $notificacion) }}" 
                                              method="POST" 
                                              class="inline">
                                            @csrf
                                            <button type="submit"
                                                    class="text-gray-600 hover:text-gray-800 text-sm">
                                                Marcar como leída
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="p-6 text-center text-gray-500">
                        No hay notificaciones
                    </div>
                @endforelse
            </div>

            <!-- Paginación -->
            @if($notificaciones->hasPages())
                <div class="px-6 py-4 border-t border-gray-200">
                    {{ $notificaciones->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection