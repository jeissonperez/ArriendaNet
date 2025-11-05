@extends('layouts.app')

@section('title', 'Inmuebles')

@section('content')
    <div class="bg-white rounded-lg shadow">
        <!-- Encabezado -->
        <div class="p-6 border-b border-gray-200">
            <div class="flex justify-between items-center">
                <h2 class="text-xl font-semibold text-gray-800">Inmuebles</h2>
                @if(auth()->user()->rol_usuario === 'Arrendador')
                    <a href="{{ route('inmuebles.create') }}" 
                       class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">
                        Registrar Inmueble
                    </a>
                @endif
            </div>

            <!-- Filtros -->
            <form method="GET" action="{{ route('inmuebles.index') }}" class="mt-4 grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label for="tipo" class="block text-sm font-medium text-gray-700">Tipo de Inmueble</label>
                    <select name="tipo" id="tipo" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">Todos</option>
                        <option value="Terreno" {{ request('tipo') == 'Terreno' ? 'selected' : '' }}>Terreno</option>
                        <option value="Casa" {{ request('tipo') == 'Casa' ? 'selected' : '' }}>Casa</option>
                        <option value="Apartamento" {{ request('tipo') == 'Apartamento' ? 'selected' : '' }}>Apartamento</option>
                        <option value="Local" {{ request('tipo') == 'Local' ? 'selected' : '' }}>Local</option>
                        <option value="Lote" {{ request('tipo') == 'Lote' ? 'selected' : '' }}>Lote</option>
                        <option value="Otro" {{ request('tipo') == 'Otro' ? 'selected' : '' }}>Otro</option>
                    </select>
                </div>

                <div>
                    <label for="estado" class="block text-sm font-medium text-gray-700">Estado</label>
                    <select name="estado" id="estado" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">Todos</option>
                        <option value="Disponible" {{ request('estado') == 'Disponible' ? 'selected' : '' }}>Disponible</option>
                        <option value="Arrendado" {{ request('estado') == 'Arrendado' ? 'selected' : '' }}>Arrendado</option>
                        <option value="Mantenimiento" {{ request('estado') == 'Mantenimiento' ? 'selected' : '' }}>Mantenimiento</option>
                    </select>
                </div>

                <div>
                    <label for="precio_min" class="block text-sm font-medium text-gray-700">Precio Mínimo</label>
                    <input type="number" name="precio_min" id="precio_min" 
                           value="{{ request('precio_min') }}"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                           min="0" step="1000">
                </div>

                <div>
                    <label for="precio_max" class="block text-sm font-medium text-gray-700">Precio Máximo</label>
                    <input type="number" name="precio_max" id="precio_max" 
                           value="{{ request('precio_max') }}"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                           min="0" step="1000">
                </div>

                <div class="md:col-span-4 flex justify-end">
                    <button type="submit" class="bg-gray-800 text-white px-4 py-2 rounded-md hover:bg-gray-700">
                        Filtrar
                    </button>
                </div>
            </form>
        </div>

        <!-- Lista de Inmuebles -->
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Dirección
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Tipo
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Valor Arriendo
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Estado
                        </th>
                        @if(auth()->user()->rol_usuario === 'Arrendador')
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Acciones
                            </th>
                        @endif
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($inmuebles as $inmueble)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $inmueble->direccion }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $inmueble->tipo_inmueble }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">
                                    ${{ number_format($inmueble->valor_arriendo, 0, ',', '.') }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                    {{ $inmueble->estado === 'Disponible' ? 'bg-green-100 text-green-800' : '' }}
                                    {{ $inmueble->estado === 'Arrendado' ? 'bg-blue-100 text-blue-800' : '' }}
                                    {{ $inmueble->estado === 'Mantenimiento' ? 'bg-yellow-100 text-yellow-800' : '' }}">
                                    {{ $inmueble->estado }}
                                </span>
                            </td>
                            @if(auth()->user()->rol_usuario === 'Arrendador')
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <a href="{{ route('inmuebles.show', $inmueble) }}" 
                                       class="text-blue-600 hover:text-blue-900 mr-3">Ver</a>
                                    <a href="{{ route('inmuebles.edit', $inmueble) }}" 
                                       class="text-green-600 hover:text-green-900 mr-3">Editar</a>
                                    <form action="{{ route('inmuebles.destroy', $inmueble) }}" 
                                          method="POST" 
                                          class="inline"
                                          onsubmit="return confirm('¿Está seguro de que desea eliminar este inmueble?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                class="text-red-600 hover:text-red-900">
                                            Eliminar
                                        </button>
                                    </form>
                                </td>
                            @endif
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                                No se encontraron inmuebles
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Paginación -->
        <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
            {{ $inmuebles->links() }}
        </div>
    </div>
@endsection