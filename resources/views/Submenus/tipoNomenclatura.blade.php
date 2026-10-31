@extends('layouts.app')

@section('content')
    @include('modales.tipoNomenclaturaform')

    <div class="mt-4 bg-white p-4 rounded shadow-sm">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2 class="h5 font-weight-bold">Tipo Nomenclaturas</h2>
            <button class="btn btn-danger text-white" data-bs-toggle="modal" data-bs-target="#tipoNomenclaturaModal" onclick="crearTipoNomenclatura()">+ NUEVO TIPO</button>
        </div>

        @include('componentes.buscador', ['actionRoute' => route('tipoNomenclatura.get_tipo_nomenclatura')]) <!-- Añadir la barra de búsqueda -->

        <div class="table-responsive" style="overflow-x: auto;">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>DESCRIPCION</th>
                        <th>ESTADO</th>
                        <th width="15%">ACCION</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($tipoNomenclaturas as $tipoNomenclatura)
                        <tr>
                            <td>{{ $tipoNomenclatura->descripcion }}</td>
                            <td>
                                <span class="badge {{ $tipoNomenclatura->estado == 1 ? 'bg-success' : 'bg-danger' }}">
                                    {{ $tipoNomenclatura->estado == 1 ? 'Activa' : 'Inactiva' }}
                                </span>
                            </td>
                            <td>
                                <div class="d-flex gap-2">
                                    @if($tipoNomenclatura->estado == 1) 
                                        <!-- Botón Editar -->
                                        <button class="btn btn-sm btn-editar" title="Editar" data-bs-toggle="modal" data-bs-target="#tipoNomenclaturaModal" onclick="editarTipoNomenclatura({{ json_encode($tipoNomenclatura) }})">
                                            <i class="fas fa-pen"></i>
                                        </button>

                                        <!-- Botón Inactivar -->
                                        <button class="btn btn-danger btn-sm" title="Inactivar" onclick="cambiarEstadoTipoNomenclatura({{ $tipoNomenclatura->id }}, {{ $tipoNomenclatura->estado }})">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    @else
                                        <!-- Botón Editar Deshabilitado -->
                                        <button class="btn btn-sm btn-editar disabled" title="Editar" disabled>
                                            <i class="fas fa-pen"></i>
                                        </button>

                                        <!-- Botón Restaurar -->
                                        <button class="btn btn-success btn-sm" title="Restaurar" onclick="cambiarEstadoTipoNomenclatura({{ $tipoNomenclatura->id }}, {{ $tipoNomenclatura->estado }})">
                                            <i class="fas fa-undo"></i>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="text-center">No hay tipos de Nomenclatura disponibles.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @include('componentes.pagination', ['items' => $tipoNomenclaturas]) <!-- Incluir la paginación -->
    </div>
@endsection

@push('after-scripts')
    @vite('resources/js/tipoNomenclatura.js')
@endpush