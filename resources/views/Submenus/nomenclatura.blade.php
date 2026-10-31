@extends('layouts.app')

@section('content')
    @include('modales.nomenclaturaform')

    <div class="mt-4 bg-white p-4 rounded shadow-sm">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2 class="h5 font-weight-bold">Nomenclatura</h2>
            <button class="btn btn-danger text-white" data-bs-toggle="modal" data-bs-target="#nomenclaturaModal" onclick="crearNomenclatura()">+ NUEVA NOMENCLATURA</button>
        </div>

        @include('componentes.buscador', ['actionRoute' => route('nomenclatura.index')])

        <div class="table-responsive" style="overflow-x: auto;">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>NOMENCLATURA</th>
                        <th>DESCRIPCIÓN</th>
                        <th>TIPO</th>
                        <th>ESTADO</th>
                        <th width="15%">ACCIÓN</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($nomenclaturas as $nomenclatura)
                        <tr>
                            <td>{{ $nomenclatura->abreviatura }}</td>
                            <td>{{ $nomenclatura->descripcion }}</td>
                            <td>{{ $nomenclatura->tipoNomenclatura->descripcion }}</td>
                            <td>
                                <span class="badge {{ $nomenclatura->estado == 1 ? 'bg-success' : 'bg-danger' }}">
                                    {{ $nomenclatura->estado == 1 ? 'Activa' : 'Inactiva' }}
                                </span>
                            </td>
                            <td>
                                <div class="d-flex gap-2">
                                    <!-- Botón Editar -->
                                    <button class="btn btn-sm btn-editar {{ $nomenclatura->estado == 0 ? 'disabled' : '' }}" 
                                            title="Editar" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#nomenclaturaModal" 
                                            onclick="editarNomenclatura({{ json_encode($nomenclatura) }})">
                                        <i class="fas fa-pen"></i>
                                    </button>

                                    @if ($nomenclatura->estado == 1)
                                        <!-- Botón Inactivar -->
                                        <button class="btn btn-danger btn-sm" 
                                                title="Inactivar" 
                                                onclick="cambiarEstadoNomenclatura({{ $nomenclatura->id }}, {{ $nomenclatura->estado }})">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    @else
                                        <!-- Botón Restaurar -->
                                        <button class="btn btn-success btn-sm" 
                                                title="Restaurar" 
                                                onclick="cambiarEstadoNomenclatura({{ $nomenclatura->id }}, {{ $nomenclatura->estado }})">
                                            <i class="fas fa-undo"></i>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center">No hay nomenclaturas disponibles.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @include('componentes.pagination', ['items' => $nomenclaturas])

    </div>
@endsection

@push('after-scripts')
    @vite('resources/js/nomenclatura.js')
@endpush