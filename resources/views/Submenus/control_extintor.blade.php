@extends('layouts.app')
@section('breadcrumbs')
@if($id_cliente != 0)
<li class="breadcrumb-item">
<!-- if breadcrumb is single--><a href="/clientes">Clientes</a>
</li>
@else
<li class="breadcrumb-item">
<!-- if breadcrumb is single--><a href="/dashboard">Inicio</a>
</li>
@endif

<li class="breadcrumb-item active"><span>Control de Extintores</span></li>
@endsection

@section('content')
@include('modales.control_extintorform')

<div class="mt-4 bg-white p-4 rounded shadow-sm">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="h5 font-weight-bold">Control de Extintores</h2>
        <button class="btn btn-danger text-white" onclick="precargarControlExtintor()">+ NUEVO CONTROL</button>
    </div>

    <div class="mb-3">
        <div class="row g-2 align-items-end">
            <!-- Botón de Excel -->
            <div class="col-md-auto">
                <a href="{{ route('control_extintores.export', ['id_cliente' => $id_cliente, 'mes' => request('mes'), 'anio' => request('anio')]) }}" class="btn btn-success">
                    <i class="fa fa-file-excel"></i> Descargar Excel
                </a>
            </div>

            <!-- Filtros -->
            <div class="col-md-auto">
                <form method="GET" action="{{ $id_cliente != 0 ? route('obtener_control_extintor_por_cliente', ['id_cliente' => $id_cliente]) : route('obtener_control_extintores') }}" class="d-flex align-items-center gap-2">
                    <select name="mes" class="form-select w-auto" onchange="this.form.submit()">
                        <option value="">Todos los meses</option>
                        @foreach(range(1, 12) as $m)
                            <option value="{{ $m }}" {{ request('mes') == $m ? 'selected' : '' }}>
                                {{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}
                            </option>
                        @endforeach
                    </select>
                    <select name="anio" class="form-select w-auto" onchange="this.form.submit()">
                        @foreach(range(2023, date('Y')) as $y)
                            <option value="{{ $y }}" {{ request('anio', date('Y')) == $y ? 'selected' : '' }}>
                                {{ $y }}
                            </option>
                        @endforeach
                    </select>
                </form>
            </div>
        </div>
    </div>
    <div class="table-responsive" style="overflow-x: auto; width: 100%;">
        <table class="table table-bordered table-striped" style="min-width: 1800px;">
            <thead>
                <tr>
                    <th class="text-center" style="min-width: 250px;">CLIENTE</th>
                    <th>LOCALIDAD</th>
                    <th>FECHA REVISION</th>
                    <th>ÁREA</th>
                    <th>UBICACIÓN</th>
                    <th>COD</th>
                    <th>CLASE</th>
                    <th>MARCA</th>
                    <th>PESO</th>
                    <th>MANGUERA</th>
                    <th>MANOMETRO</th>
                    <th>SEGURO</th>
                    <th>CILINDRO</th>
                    <th>FECHA RECARGA</th>
                    <th class="text-center" style="min-width: 200px;">FECHA PROX. RECARGA</th>
                    <th>FECHA FABRICACION</th>
                    <th class="text-center" style="min-width: 200px;">FECHA PRUEBA HIDROSTATICA</th>
                    <th>ÁREA DE SEGURIDAD</th>
                    <th>INSTRUCTIVO</th>
                    <th>INSTRUCTIVO CLASIFICACION</th>
                    <th>FLECHA UBICACIÓN</th>
                    <th>SILUETA</th>
                    <th>RECT. INSTALACIÓN</th>
                    <th class="text-center" style="min-width: 250px;">EXTINTOR DENTRO DE HIDRANTE Y/O ARMARIO</th>
                    <th>DIMENSIÓN VIDRIO/ACRILICO</th>
                    <th>OBSERVACIONES MARBETE</th>
                    <th>OBSERVACIONES</th>
                    <th>OBSERVACIONES INTERNAS</th>
                    <th class="sticky-col bg-white border-start" style="z-index: 5; box-shadow: -4px 0 6px -2px rgba(0, 0, 0, 0.2);">
                        ACCIONES
                    </th>
                </tr>
            </thead>
            <tbody>
                @forelse ($controlExtintores as $index => $controlExtintor)
                <tr>
                    <td>{{ $controlExtintor->razon_social }}</td>
                    <td>{{ $controlExtintor->localidad }}</td>
                    @include('componentes.fecha-columna', ['fecha' => $controlExtintor->fecha_revision])
                    <td>{{ $controlExtintor->area_nombre }}</td>
                    <td>{{ $controlExtintor->ubicacion }}</td>
                    <td>{{ $controlExtintor->codigo }}</td>
                    <td>{{ $controlExtintor->clase }}</td>
                    <td>{{ $controlExtintor->marca }}</td>
                    <td>{{ $controlExtintor->peso }} {{ $controlExtintor->unidad }}</td>
                    <td>{{ $controlExtintor->mango }}</td>
                    <td>{{ $controlExtintor->mano }}</td>
                    <td>{{ $controlExtintor->seguro }}</td>
                    <td>{{ $controlExtintor->cilindro }}</td>
                    @include('componentes.fecha-columna', ['fecha' => $controlExtintor->fecha_recarga])
                    @include('componentes.fecha-columna', ['fecha' => $controlExtintor->fecha_prox_recarga])
                    @include('componentes.fecha-columna', ['fecha' => $controlExtintor->fecha_fabricacion])
                    @include('componentes.fecha-columna', ['fecha' => $controlExtintor->fecha_prueba_hidrostatica])
                    <td>{{ $controlExtintor->area_seguridad }}</td>
                    <td>{{ $controlExtintor->instructivo_uso }}</td>
                    <td>{{ $controlExtintor->instructivo_clasificacion }}</td>
                    <td>{{ $controlExtintor->flecha_ubicacion }}</td>
                    <td>{{ $controlExtintor->silueta }}</td>
                    <td>{{ $controlExtintor->rectangulo_instalacion }}</td>
                    <td>{{ $controlExtintor->hidrante }}</td>
                    <td>{{ $controlExtintor->dimension_vidrio }}</td>
                    <td>{{ $controlExtintor->novedad_marbete }}</td>
                    <td>
                        {{ $controlExtintor->observacionesNomenclatura->pluck('abreviatura')->implode(', ') }}
                    </td>
                    <td>{{ $controlExtintor->observaciones }}</td>
                    <!-- <td>
                        <div class="d-flex gap-2">
                            @if($controlExtintor->imagen_observacion)
                                <a href="{{ asset('storage/' . $controlExtintor->imagen_observacion) }}" download class="btn btn-download btn-sm" title="Descargar Imagen">
                                    <i class="fa fa-download"></i>
                                </a>
                            @endif
                        </div>
                    </td> -->
                    <td class="sticky-col bg-white border-start" style="z-index: 4; box-shadow: -4px 0 6px -2px rgba(0, 0, 0, 0.1);">
                        <div class="d-flex gap-2">
                            @if($controlExtintor->imagen_observacion)
                                <button class="btn btn-sm btn-success"
                                    data-bs-toggle="modal"
                                    data-bs-target="#imageModal"
                                    data-image="{{ $controlExtintor->imagen_observacion }}"
                                    data-marca="{{ $controlExtintor->marca }}"
                                    data-fecha="{{ $controlExtintor->fecha_ingreso }}"
                                    title="Visualizar Imagen">
                                    <i class="fa fa-eye"></i>
                                </button>
                            @endif

                            <button class="btn btn-sm btn-editar"
                                title="Editar"
                                onclick='editarControlExtintorCliente(@json($controlExtintor), {{ $id_cliente }})'>
                                <i class="fas fa-pen"></i>
                            </button>

                            <button class="btn btn-sm {{ $controlExtintor->estado == 1 ? 'btn-eliminar' : 'btn-success' }}"
                                title="{{ $controlExtintor->estado == 1 ? 'Eliminar' : 'Restaurar' }}"
                                onclick="cambiarEstadoControl({{ $controlExtintor->id }}, {{ $controlExtintor->estado }})">
                                <i class="fas {{ $controlExtintor->estado == 1 ? 'fa-trash' : 'fa-undo' }}"></i>
                            </button>
                        </div>
                    </td>

                </tr>
                @empty
                <tr>
                    <td colspan="28" class="text-center">No hay datos disponibles.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="d-flex justify-content-center mt-3">
    </div>
</div>
<!-- Modal para mostrar la imagen -->
<div class="modal fade" id="imageModal" tabindex="-1" aria-labelledby="imageModalLabel" >
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header d-flex justify-content-between">
                <h5 class="modal-title" id="imageModalLabel"></h5>
                
                <!-- Botón para descargar la imagen alineado a la derecha -->
                <a 
                    id="downloadImage" 
                    class="btn btn-link" 
                    download 
                    title="Descargar imagen"
                    style="text-decoration: none;">
                    <i class="fa fa-download" style="font-size: 1.5em;"></i>
                </a>
                
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <!-- Imagen cargada dinámicamente aquí -->
                <img id="modalImage" src="" alt="Imagen de Observación" class="img-fluid">
            </div>
        </div>
    </div>
</div>

<script>
    window.ultimoControlExtintor = @json($ultimoControlExtintor);
    window.idCliente = {{ $id_cliente }};
</script>


@endsection

@push('after-scripts')
    @vite('resources/js/extintor.js')
@endpush