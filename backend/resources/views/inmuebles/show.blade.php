@extends('layouts.app')

@section('title', 'Detalle del Inmueble')

@section('content')
    <div class="max-w-3xl mx-auto">
        <div class="bg-white rounded-lg shadow">
            <!-- Encabezado -->
            <div class="px-6 py-4 border-b border-gray-200">
                <div class="flex justify-between items-center">
                    <h2 class="text-xl font-semibold text-gray-800">Detalle del Inmueble</h2>
                    @if(auth()->user()->rol_usuario === 'Arrendador' && $inmueble->id_usuario === auth()->id())
                        <div class="space-x-2">
                            <a href="{{ route('inmuebles.edit', $inmueble) }}" 
                               class="bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700">
                                Editar
                            </a>
                            <form action="{{ route('inmuebles.destroy', $inmueble) }}" 
                                  method="POST" 
                                  class="inline"
                                  onsubmit="return confirm('¿Está seguro de que desea eliminar este inmueble?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" 
                                        class="bg-red-600 text-white px-4 py-2 rounded-md hover:bg-red-700">
                                    Eliminar
                                </button>
                            </form>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Información del Inmueble -->
            <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Detalles básicos -->
                <div>
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Información Básica</h3>
                    <dl class="space-y-4">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Dirección</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $inmueble->direccion }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Tipo de Inmueble</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $inmueble->tipo_inmueble }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Valor del Arriendo</dt>
                            <dd class="mt-1 text-sm text-gray-900">
                                ${{ number_format($inmueble->valor_arriendo, 0, ',', '.') }}
                            </dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Estado</dt>
                            <dd class="mt-1">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                    {{ $inmueble->estado === 'Disponible' ? 'bg-green-100 text-green-800' : '' }}
                                    {{ $inmueble->estado === 'Arrendado' ? 'bg-blue-100 text-blue-800' : '' }}
                                    {{ $inmueble->estado === 'Mantenimiento' ? 'bg-yellow-100 text-yellow-800' : '' }}">
                                    {{ $inmueble->estado }}
                                </span>
                            </dd>
                        </div>
                    </dl>
                </div>

                <!-- Propietario -->
                <div>
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Información del Propietario</h3>
                    <dl class="space-y-4">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Nombre</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $inmueble->propietario->nombre_completo }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Teléfono</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $inmueble->propietario->cel_usuario }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Correo</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $inmueble->propietario->correo_usuario }}</dd>
                        </div>
                    </dl>
                </div>
            </div>

            <!-- Contrato Activo -->
            @if($inmueble->contratoActivo)
            <div class="px-6 py-4 border-t border-gray-200">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Contrato Activo</h3>
                <div class="bg-gray-50 rounded-lg p-4">
                    <dl class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Arrendatario</dt>
                            <dd class="mt-1 text-sm text-gray-900">
                                {{ $inmueble->contratoActivo->arrendatario->nombre_completo }}
                            </dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Fecha Inicio</dt>
                            <dd class="mt-1 text-sm text-gray-900">
                                {{ $inmueble->contratoActivo->fecha_inicio->format('d/m/Y') }}
                            </dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Fecha Fin</dt>
                            <dd class="mt-1 text-sm text-gray-900">
                                {{ $inmueble->contratoActivo->fecha_fin ? $inmueble->contratoActivo->fecha_fin->format('d/m/Y') : 'No especificada' }}
                            </dd>
                        </div>
                    </dl>
                </div>
            </div>
            @endif

            <!-- Solicitudes de Mantenimiento Recientes -->
            <div class="px-6 py-4 border-t border-gray-200">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Solicitudes de Mantenimiento Recientes</h3>
                @if($inmueble->solicitudesMantenimiento->count() > 0)
                    <div class="space-y-4">
                        @foreach($inmueble->solicitudesMantenimiento->take(3) as $solicitud)
                            <div class="bg-gray-50 rounded-lg p-4">
                                <div class="flex justify-between">
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">
                                            {{ $solicitud->tipo_mantenimiento }}
                                        </p>
                                        <p class="text-sm text-gray-500">{{ $solicitud->descripcion }}</p>
                                    </div>
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                        {{ $solicitud->estado_solicitud === 'Pendiente' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                        {{ $solicitud->estado_solicitud === 'En proceso' ? 'bg-blue-100 text-blue-800' : '' }}
                                        {{ $solicitud->estado_solicitud === 'Finalizado' ? 'bg-green-100 text-green-800' : '' }}
                                        {{ $solicitud->estado_solicitud === 'Rechazado' ? 'bg-red-100 text-red-800' : '' }}">
                                        {{ $solicitud->estado_solicitud }}
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-sm text-gray-500">No hay solicitudes de mantenimiento registradas</p>
                @endif
            </div>

            <!-- Botones de Acción -->
            <div class="px-6 py-4 border-t border-gray-200 flex justify-end space-x-4">
                <a href="{{ route('inmuebles.index') }}" 
                   class="bg-gray-300 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-400">
                    Volver
                </a>
                @if(auth()->user()->rol_usuario === 'Arrendador' && $inmueble->estado === 'Disponible')
                    <a href="{{ route('contratos.create', ['inmueble' => $inmueble->id_inmueble]) }}"
                       class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">
                        Crear Contrato
                    </a>
                @endif
            </div>
        </div>
    </div>
@endsection