@extends('layouts.app')

@section('content')
    @include('modales.claseform')
    <div class="mt-4 bg-white p-4 rounded shadow-sm">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2 class="h5 font-weight-bold">Clase</h2>
            <button class="btn btn-danger text-white" onclick="crearClase()">+ NUEVA CLASE</button>
        </div>

        @include('componentes.buscador', ['actionRoute' => route('clase.get_clase')]) <!-- Añadir la barra de búsqueda -->

        <div class="table-responsive" style="overflow-x: auto;">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>CLASE</th>
                        <th>ESTADO</th>
                        <th width="15%">ACCION</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($clases as $clase)
                        <tr>
                            <td>{{ $clase->clase }}</td>
                            <td>
                                <span class="badge {{ $clase->estado == 1 ? 'bg-success' : 'bg-danger' }}">
                                    {{ $clase->estado == 1 ? 'Activa' : 'Inactiva' }}
                                </span>
                            </td>
                            <td>
                                <div class="d-flex gap-2">
                                    @if($clase->estado == 1) 
                                        <!-- Botón Inactivar -->
                                        <button class="btn btn-danger btn-sm" title="Eliminar" onclick="cambiarEstadoClase({{ $clase->id }}, {{ $clase->estado }})">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    @else
                                        <!-- Botón Restaurar -->
                                        <button class="btn btn-success btn-sm" title="Restaurar" onclick="cambiarEstadoClase({{ $clase->id }}, {{ $clase->estado }})">
                                            <i class="fas fa-undo"></i>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="text-center">No hay clases disponibles.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @include('componentes.pagination', ['items' => $clases]) <!-- Incluir la paginación -->
    </div>
@endsection

@push('after-scripts')
    @vite('resources/js/extintor.js')
@endpush