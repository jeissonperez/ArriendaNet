@extends('layouts.app')

@section('title', isset($inmueble) ? 'Editar Inmueble' : 'Registrar Inmueble')

@section('content')
    <div class="max-w-2xl mx-auto">
        <div class="bg-white rounded-lg shadow">
            <div class="p-6">
                <h2 class="text-xl font-semibold text-gray-800 mb-6">
                    {{ isset($inmueble) ? 'Editar Inmueble' : 'Registrar Nuevo Inmueble' }}
                </h2>

                @if ($errors->any())
                    <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                        <ul class="list-disc list-inside">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" 
                      action="{{ isset($inmueble) ? route('inmuebles.update', $inmueble) : route('inmuebles.store') }}">
                    @csrf
                    @if(isset($inmueble))
                        @method('PUT')
                    @endif

                    <!-- Dirección -->
                    <div class="mb-4">
                        <label for="direccion" class="block text-sm font-medium text-gray-700">
                            Dirección
                        </label>
                        <input type="text" 
                               name="direccion" 
                               id="direccion"
                               value="{{ old('direccion', $inmueble->direccion ?? '') }}"
                               required
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        @error('direccion')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Tipo de Inmueble -->
                    <div class="mb-4">
                        <label for="tipo_inmueble" class="block text-sm font-medium text-gray-700">
                            Tipo de Inmueble
                        </label>
                        <select name="tipo_inmueble" 
                                id="tipo_inmueble"
                                required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="">Seleccione un tipo...</option>
                            @foreach(['Terreno', 'Casa', 'Apartamento', 'Local', 'Lote', 'Otro'] as $tipo)
                                <option value="{{ $tipo }}" 
                                    {{ old('tipo_inmueble', $inmueble->tipo_inmueble ?? '') == $tipo ? 'selected' : '' }}>
                                    {{ $tipo }}
                                </option>
                            @endforeach
                        </select>
                        @error('tipo_inmueble')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Valor del Arriendo -->
                    <div class="mb-4">
                        <label for="valor_arriendo" class="block text-sm font-medium text-gray-700">
                            Valor del Arriendo
                        </label>
                        <div class="mt-1 relative rounded-md shadow-sm">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <span class="text-gray-500 sm:text-sm">$</span>
                            </div>
                            <input type="number" 
                                   name="valor_arriendo" 
                                   id="valor_arriendo"
                                   value="{{ old('valor_arriendo', $inmueble->valor_arriendo ?? '') }}"
                                   required
                                   min="0"
                                   step="1000"
                                   class="pl-7 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>
                        @error('valor_arriendo')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Estado -->
                    <div class="mb-6">
                        <label for="estado" class="block text-sm font-medium text-gray-700">
                            Estado
                        </label>
                        <select name="estado" 
                                id="estado"
                                required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="">Seleccione un estado...</option>
                            @foreach(['Disponible', 'Arrendado', 'Mantenimiento'] as $estado)
                                <option value="{{ $estado }}"
                                    {{ old('estado', $inmueble->estado ?? 'Disponible') == $estado ? 'selected' : '' }}>
                                    {{ $estado }}
                                </option>
                            @endforeach
                        </select>
                        @error('estado')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Botones -->
                    <div class="flex justify-end space-x-4">
                        <a href="{{ route('inmuebles.index') }}"
                           class="bg-gray-300 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-400">
                            Cancelar
                        </a>
                        <button type="submit"
                                class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">
                            {{ isset($inmueble) ? 'Actualizar' : 'Registrar' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection