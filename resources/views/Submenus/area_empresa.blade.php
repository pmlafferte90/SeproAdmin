@extends('layouts.app')

@section('breadcrumbs')
@if($id_cliente != 0)
<li class="breadcrumb-item">
    <a href="/clientes">Clientes</a>
</li>
@else
<li class="breadcrumb-item">
    <a href="/dashboard">Inicio</a>
</li>
@endif
<li class="breadcrumb-item active"><span>Áreas de Empresa</span></li>
@endsection

@section('content')
@include('modales.area_empresaform')

<div class="mt-4 bg-white p-4 rounded shadow-sm">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="h5 font-weight-bold">Área Empresa</h2>
        <button class="btn btn-danger text-white" onclick="nuevoAreaEmpresa()">+ NUEVA AREA EMPRESA</button>
    </div>
    @include('componentes.buscador', ['actionRoute' => route('area.area_empresa', ['id_cliente' => $id_cliente])])

    <div class="table-responsive" style="overflow-x: auto;">
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>CLIENTE</th>
                    <th>SUCURSAL</th>
                    <th>AREA</th>
                    <th>UBICACIÓN</th>
                    <th>FECHA CREACIÓN</th>
                    <th>ESTADO</th>
                    <th width="15%">ACCIÓN</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($areaEmpresas as $areaEmpresa)
                    <tr>
                        <td>{{ $areaEmpresa->cliente }}</td>
                        <td>{{ $areaEmpresa->nombre_localidad }}</td>
                        <td>{{ $areaEmpresa->area }}</td>
                        <td>{{ $areaEmpresa->subarea }}</td>
                        <td style="white-space: nowrap;">
                            {{ $areaEmpresa->fecha_creacion && $areaEmpresa->fecha_creacion !== '0000-00-00' 
                                ? \Carbon\Carbon::parse($areaEmpresa->fecha_creacion)->format('d-m-Y') 
                                : '-' }}
                        </td>

                        <td>
                            @if ($areaEmpresa->estado == 1)
                                <span class="badge bg-success">Activa</span>
                            @elseif ($areaEmpresa->estado == 2)
                                <span class="badge bg-warning">Pendiente de Aprobación</span>
                            @elseif ($areaEmpresa->estado == 3)
                                <span class="badge bg-warning">Pendiente de Anulación</span>
                            @else
                                <span class="badge bg-danger">Inactiva</span>
                            @endif
                        </td>
                        <td>
                            @if (auth()->user()->rol == 1 || auth()->user()->rol == 2)
                                @if ($areaEmpresa->estado == 0)
                                    <!-- Botón Restaurar -->
                                    <button class="btn btn-success btn-sm" title="Restaurar" onclick="cambiarEstadoAreaEmpresa({{ $areaEmpresa->id }}, {{ $areaEmpresa->estado }}, 'restaurar')">
                                        <i class="fas fa-undo"></i>
                                    </button>
                                @elseif ($areaEmpresa->estado == 1)
                                    <!-- Botón Inactivar -->
                                    <button class="btn btn-danger btn-sm" title="Inactivar" onclick="cambiarEstadoAreaEmpresa({{ $areaEmpresa->id }}, {{ $areaEmpresa->estado }}, 'inactivar')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                @elseif ($areaEmpresa->estado == 2 || $areaEmpresa->estado == 3)
                                    <!-- Botones Aprobar y Rechazar -->
                                    <button class="btn btn-success btn-sm" title="Aprobar" onclick="cambiarEstadoAreaEmpresa({{ $areaEmpresa->id }}, {{ $areaEmpresa->estado }}, 'aprobar')">
                                        <i class="fas fa-check"></i>
                                    </button>
                                    <button class="btn btn-danger btn-sm" title="Rechazar" onclick="cambiarEstadoAreaEmpresa({{ $areaEmpresa->id }}, {{ $areaEmpresa->estado }}, 'rechazar')">
                                        <i class="fas fa-times"></i>
                                    </button>
                                @endif
                            @else
                                @if ($areaEmpresa->estado == 0)
                                    <!-- Botón Restaurar -->
                                    <button class="btn btn-success btn-sm" title="Restaurar" onclick="cambiarEstadoAreaEmpresa({{ $areaEmpresa->id }}, {{ $areaEmpresa->estado }}, 'restaurar')">
                                        <i class="fas fa-undo"></i>
                                    </button>
                                @elseif ($areaEmpresa->estado == 1)
                                    <!-- Botón Inactivar -->
                                    <button class="btn btn-danger btn-sm" title="Inactivar" onclick="cambiarEstadoAreaEmpresa({{ $areaEmpresa->id }}, {{ $areaEmpresa->estado }}, 'inactivar')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                @endif
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center">No hay áreas disponibles.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @include('componentes.pagination', ['items' => $areaEmpresas])

</div>
@endsection

@push('after-scripts')
    @vite('resources/js/area_empresa.js')
@endpush
