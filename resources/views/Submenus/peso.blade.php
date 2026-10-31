@extends('layouts.app')

@section('content')
    @include('modales.pesoform')
    <div class="mt-4 bg-white p-4 rounded shadow-sm">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2 class="h5 font-weight-bold">Peso</h2>
            <button class="btn btn-danger text-white" data-bs-toggle="modal" data-bs-target="#pesoModal" onclick="crearPeso()">+ NUEVO PESO</button>
        </div>

        @include('componentes.buscador', ['actionRoute' => route('peso.get_peso')]) <!-- Añadir la barra de búsqueda -->

        <div class="table-responsive" style="overflow-x: auto;">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>PESO</th>
                        <th>ESTADO</th>
                        <th width="15%">ACCION</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($pesos as $peso)
                        <tr>
                            <td>{{ $peso->tamanio }}</td>
                            <td>
                                <span class="badge {{ $peso->estado == 1 ? 'bg-success' : 'bg-danger' }}">
                                    {{ $peso->estado == 1 ? 'Activa' : 'Inactiva' }}
                                </span>
                            </td>
                            <td>
                                <div class="d-flex gap-2">
                                    @if($peso->estado == 1) 
                                        <!-- Botón Inactivar -->
                                        <button class="btn btn-danger btn-sm" title="Eliminar" onclick="cambiarEstadoPeso({{ $peso->id }}, {{ $peso->estado }})">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    @else
                                        <!-- Botón Restaurar -->
                                        <button class="btn btn-success btn-sm" title="Restaurar" onclick="cambiarEstadoPeso({{ $peso->id }}, {{ $peso->estado }})">
                                            <i class="fas fa-undo"></i>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="text-center">No hay pesos disponibles.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @include('componentes.pagination', ['items' => $pesos]) <!-- Incluir la paginación -->
    </div>
@endsection

@push('after-scripts')
    @vite('resources/js/extintor.js')
@endpush