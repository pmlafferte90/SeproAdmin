@extends('layouts.app')

@section('content')
    @include('modales.marcaform') <!-- Modal para crear/editar marca -->

    <div class="mt-4 bg-white p-4 rounded shadow-sm">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2 class="h5 font-weight-bold">Marcas</h2>
            <button class="btn btn-danger text-white" data-bs-toggle="modal" data-bs-target="#marcaModal" onclick="crearMarca()">+ NUEVA MARCA</button>
        </div>

        @include('componentes.buscador', ['actionRoute' => route('marca.get_marca')]) <!-- Añadir la barra de búsqueda -->

        <div class="table-responsive" style="overflow-x: auto;">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>MARCA</th>
                        <th>ESTADO</th>
                        <th width="15%">ACCION</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($marcas as $marca)
                        <tr>
                            <td>{{ $marca->marca }}</td>
                            <td>
                                <span class="badge {{ $marca->estado == 1 ? 'bg-success' : 'bg-danger' }}">
                                    {{ $marca->estado == 1 ? 'Activa' : 'Inactiva' }}
                                </span>
                            </td>
                            <td>
                                <div class="d-flex gap-2">
                                    @if($marca->estado == 1) 
                                        <!-- Botón Inactivar -->
                                        <button class="btn btn-danger btn-sm" title="Eliminar" onclick="cambiarEstadoMarca({{ $marca->id }}, {{ $marca->estado }})">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    @else
                                        <!-- Botón Restaurar -->
                                        <button class="btn btn-success btn-sm" title="Restaurar" onclick="cambiarEstadoMarca({{ $marca->id }}, {{ $marca->estado }})">
                                            <i class="fas fa-undo"></i>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="text-center">No hay marcas disponibles.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @include('componentes.pagination', ['items' => $marcas]) <!-- Incluir la paginación -->
    </div>
@endsection

@push('after-scripts')
    @vite('resources/js/extintor.js')
@endpush