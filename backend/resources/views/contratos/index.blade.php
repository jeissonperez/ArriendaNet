@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h2>Contratos</h2>
                    <a href="{{ route('contratos.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Nuevo Contrato
                    </a>
                </div>

                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success" role="alert">
                            {{ session('success') }}
                        </div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Inmueble</th>
                                    <th>Arrendatario</th>
                                    <th>Fecha Inicio</th>
                                    <th>Fecha Fin</th>
                                    <th>Valor Mensual</th>
                                    <th>Estado</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($contratos as $contrato)
                                    <tr>
                                        <td>{{ $contrato->id_contrato }}</td>
                                        <td>{{ $contrato->inmueble->direccion }}</td>
                                        <td>{{ $contrato->arrendatario->pri_nombre }} {{ $contrato->arrendatario->pri_apellido }}</td>
                                        <td>{{ \Carbon\Carbon::parse($contrato->fecha_inicio)->format('d/m/Y') }}</td>
                                        <td>{{ \Carbon\Carbon::parse($contrato->fecha_fin)->format('d/m/Y') }}</td>
                                        <td>${{ number_format($contrato->valor_mensual, 0, ',', '.') }}</td>
                                        <td>
                                            <span class="badge {{ $contrato->estado === 'Activo' ? 'bg-success' : ($contrato->estado === 'Finalizado' ? 'bg-danger' : 'bg-warning') }}">
                                                {{ $contrato->estado }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('contratos.show', $contrato->id_contrato) }}" 
                                                   class="btn btn-info btn-sm" 
                                                   title="Ver detalles">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('contratos.edit', $contrato->id_contrato) }}" 
                                                   class="btn btn-warning btn-sm" 
                                                   title="Editar">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form action="{{ route('contratos.destroy', $contrato->id_contrato) }}" 
                                                      method="POST" 
                                                      class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" 
                                                            class="btn btn-danger btn-sm" 
                                                            title="Eliminar"
                                                            onclick="return confirm('¿Está seguro de que desea eliminar este contrato?')">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center">No hay contratos registrados.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection