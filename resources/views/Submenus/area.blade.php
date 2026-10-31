@extends('layouts.app')

@section('content')
@include('modales.areaform')

<div class="mt-4 bg-white p-4 rounded shadow-sm">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="h5 font-weight-bold">Áreas</h2>
        <button class="btn btn-danger text-white" onclick="crearArea()">+ NUEVA ÁREA</button>
    </div>

    @include('componentes.buscador', ['actionRoute' => route('area.index')])

    <div class="table-responsive" style="overflow-x: auto;">
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>NOMBRE</th>
                    <th>ESTADO</th>
                    <th width="15%">ACCIÓN</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($areas as $area)
                <tr>
                    <td>{{ $area->nombre }}</td>
                    <td>
                        <span class="badge {{ $area->estado == 1 ? 'bg-success' : 'bg-danger' }}">
                            {{ $area->estado == 1 ? 'Activa' : 'Inactiva' }}
                        </span>
                    </td>
                    <td>
                        <div class="d-flex gap-2">
                            <!-- Botón Editar -->
                            <button class="btn btn-sm btn-editar {{ $area->estado == 0 ? 'disabled' : '' }}" 
                                    title="Editar" 
                                    onclick="editarArea({{ json_encode($area) }})">
                                <i class="fas fa-pen"></i>
                            </button>

                            @if ($area->estado == 1)
                                <!-- Botón Inactivar -->
                                <button class="btn btn-danger btn-sm" 
                                        title="Inactivar" 
                                        onclick="cambiarEstadoArea({{ $area->id }}, {{ $area->estado }})">
                                    <i class="fas fa-trash"></i>
                                </button>
                            @else
                                <!-- Botón Restaurar -->
                                <button class="btn btn-success btn-sm" 
                                        title="Restaurar" 
                                        onclick="cambiarEstadoArea({{ $area->id }}, {{ $area->estado }})">
                                    <i class="fas fa-undo"></i>
                                </button>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="3" class="text-center">No hay áreas disponibles.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @include('componentes.pagination', ['items' => $areas])

</div>
@endsection

@push('after-scripts')
    @vite('resources/js/area.js')
@endpush
