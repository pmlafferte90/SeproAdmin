@extends('layouts.app')
@section('content')
@include('modales.representanteform')

<div class="mt-4 bg-white p-4 rounded shadow-sm">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="h5 font-weight-bold">Representantes</h2>
        <button class="btn btn-danger text-white" onclick="nuevoRepresentante()">+ NUEVO REPRESENTANTE</button>
    </div>

    
    @include('componentes.buscador', ['actionRoute' => route('representante.index')])

    <div class="table-responsive" style="overflow-x: auto;">
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>EMPRESA</th>
                    <th>LOCALIDADES</th>
                    <th>NOMBRE</th>
                    <th>APELLIDO</th>
                    <th>IDENTIFICACIÓN</th>
                    <th>TELÉFONO</th>
                    <th>EMAIL</th>
                    <th>ESTADO</th>
                    <th width="15%">ACCIÓN</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($representantes as $representante)
                <tr>
                    <td>{{ $representante->razon_social }}</td>
                    <td>
                        @if($representante->ver_todas_localidades)
                            <span class="badge bg-primary fs-6">Todas</span>
                        @else
                            {{ $representante->localidades->pluck('nombre')->implode(', ') }}
                        @endif
                    </td>
                    <td>{{ $representante->nombre }}</td>
                    <td>{{ $representante->apellido }}</td>
                    <td>{{ $representante->identificacion }}</td>
                    <td>{{ $representante->telefono }}</td>
                    <td>{{ $representante->correo }}</td>
                    <td>
                        <span class="badge fs-6 {{ $representante->estado == 1 ? 'bg-success' : 'bg-danger' }}">
                            {{ $representante->estado == 1 ? 'Activo' : 'Inactivo' }}
                        </span>
                    </td>
                    <td>
                        <div class="d-flex gap-2">
                            <button class="btn btn-editar btn-sm {{ $representante->estado == 0 ? 'disabled' : '' }}" 
                                    title="Editar" 
                                    onclick="editarRepresentante('{{ $representante->id }}')">
                                <i class="fas fa-pen"></i>
                            </button>

                            <button class="btn btn-sm {{ $representante->estado == 1 ? 'btn-eliminar' : 'btn-success' }}" 
                                    title="{{ $representante->estado == 1 ? 'Eliminar' : 'Restaurar' }}" 
                                    onclick="cambiarEstadoRepresentante('{{ $representante->id }}', '{{ $representante->estado }}')">
                                <i class="fas {{ $representante->estado == 1 ? 'fa-trash' : 'fa-undo' }}"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" class="text-center">No hay representantes disponibles.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @include('componentes.pagination', ['items' => $representantes])

</div>
@endsection

@push('after-scripts')
    @vite('resources/js/representante.js')
@endpush