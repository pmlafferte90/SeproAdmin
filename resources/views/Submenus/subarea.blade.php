@extends('layouts.app')

@section('content')
@include('modales.subareaform')

<div class="mt-4 bg-white p-4 rounded shadow-sm">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="h5 font-weight-bold">Ubicación e información de extintor</h2>
        <button class="btn btn-danger text-white" onclick="crearSubArea()">+ NUEVA UBICACIÓN</button>
    </div>

    @include('componentes.buscador', ['actionRoute' => route('area.get_sub_area')])

    <div class="table-responsive" style="overflow-x: auto;">
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>ÁREA</th>
                    <th>NOMBRE</th>
                    <th>CÓDIGO</th>
                    <th>CLASE</th>
                    <th>MARCA</th>
                    <th>PESO</th>
                    <th>UNIDAD</th>
                    <th>ESTADO</th>
                    <th width="15%">ACCIÓN</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($subareas as $subarea)
                <tr>
                    <td>{{ $subarea->area }}</td>
                    <td>{{ $subarea->nombre }}</td>
                    <td>{{ $subarea->codigo }}</td>
                    <td>{{ $subarea->clase }}</td>
                    <td>{{ $subarea->marca }}</td>
                    <td>{{ $subarea->peso }}</td>
                    <td>{{ $subarea->unidad }}</td>
                    <td>
                        <span class="badge {{ $subarea->estado == 1 ? 'bg-success' : 'bg-danger' }}">
                            {{ $subarea->estado == 1 ? 'Activa' : 'Inactiva' }}
                        </span>
                    </td>
                    <td>
                        <div class="d-flex gap-2">
                            <!-- Botón Editar -->
                            <button class="btn btn-sm btn-editar {{ $subarea->estado == 0 ? 'disabled' : '' }}" 
                                    title="Editar" 
                                    data-bs-toggle="modal" 
                                    data-bs-target="#subareaModal" 
                                    onclick="editarSubArea({{ json_encode($subarea) }})">
                                <i class="fas fa-pen"></i>
                            </button>

                            @if ($subarea->estado == 1)
                                <!-- Botón Inactivar -->
                                <button class="btn btn-danger btn-sm" 
                                        title="Inactivar" 
                                        onclick="cambiarEstadoSubArea({{ $subarea->id }}, {{ $subarea->estado }})">
                                    <i class="fas fa-trash"></i>
                                </button>
                            @else
                                <!-- Botón Restaurar -->
                                <button class="btn btn-success btn-sm" 
                                        title="Restaurar" 
                                        onclick="cambiarEstadoSubArea({{ $subarea->id }}, {{ $subarea->estado }})">
                                    <i class="fas fa-undo"></i>
                                </button>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" class="text-center">No hay ubicaciones disponibles.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @include('componentes.pagination', ['items' => $subareas])

</div>
@endsection

@push('after-scripts')
    @vite('resources/js/subarea.js')
@endpush