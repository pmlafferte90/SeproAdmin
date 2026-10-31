@extends('layouts.app')

@section('content')
@include('modales.clienteform')
@include('modales.localidadform')


<div class="mt-4 bg-white p-4 rounded shadow-sm">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="h5 font-weight-bold">Clientes</h2>
        <button class="btn btn-danger text-white" onclick="nuevoCliente()">+ NUEVO CLIENTE</button>
    </div>

    @include('componentes.buscador', ['actionRoute' => route('clientes.index')])

    <div class="table-responsive" style="overflow-x: auto;">
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th></th>
                    @foreach (['NOMBRE', 'IDENTIFICACIÓN', 'TELÉFONO', 'EMAIL', 'FECHA INICIO', 'ESTADO'] as $index => $columna)
                        <th>
                            <div class="d-flex justify-content-between align-items-center position-relative">
                                <span>{{ $columna }}</span>
                                <button id="search_column_btn_{{ $index + 1 }}" 
                                    class="btn btn-transparent search_column-btn" 
                                    data-column="{{ $index + 1 }}">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                        </th>
                    @endforeach
                    <th width="15%">ACCIÓN</th>
                </tr>
            </thead>

            <tbody>
                @forelse ($clientes as $cliente)
                <tr>
                    <td>
                        <button class="btn btn-sm toggle-localidades" data-id="{{ $cliente->id }}" title="Localidades">+</button>
                    </td>
                    <td>{{ $cliente->razon_social }}</td>
                    <td>{{ $cliente->ruc }}</td>
                    <td>{{ $cliente->telefono }}</td>
                    <td>{{ $cliente->correo }}</td>
                    <td style="white-space: nowrap;">
                        {{ $cliente->fecha_creacion && $cliente->fecha_creacion !== '0000-00-00' 
                            ? \Carbon\Carbon::parse($cliente->fecha_creacion)->format('d-m-Y') 
                            : '-' }}
                    </td>
                    <!-- <td style="white-space: nowrap;">
                        {{ $cliente->fecha_fin && $cliente->fecha_fin !== '0000-00-00' 
                            ? \Carbon\Carbon::parse($cliente->fecha_fin)->format('d-m-Y') 
                            : '-' }}
                    </td> -->
                    <td>
                        <span class="badge {{ $cliente->estado == 1 ? 'bg-success' : 'bg-danger' }}">
                            {{ $cliente->estado == 1 ? 'Activo' : 'Inactivo' }}
                        </span>
                    </td>
                    <td>
                        <div class="d-flex gap-2">
                            <button class="btn btn-editar btn-sm {{ $cliente->estado == 0 ? 'disabled' : '' }}"
                                title="Editar"
                                onclick="editarCliente('{{ $cliente->id }}')">
                                <i class="fas fa-pen"></i>
                            </button>

                            <a href="{{ url('area_empresa/' . $cliente->id) }}"
                                class="btn btn-area-empresa btn-sm {{ $cliente->estado == 0 ? 'disabled' : '' }}"
                                title="Áreas Empresa">
                                <i class="fas fa-plus"></i>
                            </a>
                            <!-- <a href="{{ url('control_extintor_cliente/' . $cliente->id) }}"
                                class="btn btn-control-extintores btn-sm {{ $cliente->estado == 0 ? 'disabled' : '' }}"
                                title="Control Extintores">
                                <i class="fas fa-list"></i>
                            </a> -->
                            <button class="btn btn-sm {{ $cliente->estado == 1 ? 'btn-eliminar' : 'btn-success' }}"
                                title="{{ $cliente->estado == 1 ? 'Eliminar' : 'Restaurar' }}"
                                onclick="cambiarEstadoCliente('{{ $cliente->id }}', '{{ $cliente->estado }}')">
                                <i class="fas {{ $cliente->estado == 1 ? 'fa-trash' : 'fa-undo' }}"></i>
                            </button>
                        </div>
                    </td>
                </tr>

                <!-- Fila oculta con las localidades -->
                <tr class="localidades-row d-none" id="localidades-{{ $cliente->id }}">
                    <td colspan="11">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>
                                        <button class="btn btn-sm agregar-localidad border"
                                            title="Nueva localidad"
                                            data-id-cliente="{{ $cliente->id }}"
                                            onclick="nuevaLocalidad('{{ $cliente->id }}')">
                                            +
                                        </button>
                                    </th>
                                    <th>Nombre</th>
                                    <th>Dirección</th>
                                    <th>Ubicación</th>
                                    <th>Fecha Creación</th>
                                    <th width="15%">Acciones</th>

                                </tr>
                            </thead>
                            <tbody>
                                @if($cliente->localidades && $cliente->localidades->isNotEmpty())
                                @foreach($cliente->localidades as $key => $localidad)
                                <tr>
                                    <td>{{ $key+1 }}</td>
                                    <td>{{ $localidad->nombre }}</td>
                                    <td>{{ $localidad->direccion }}</td>
                                    <td class="text-primary">
                                        @if($localidad->ubicacion)
                                        <a href="{{ $localidad->ubicacion }}" target="_blank" rel="noopener noreferrer">Ver en mapa</a>
                                        @endif
                                    </td>
                                    <td>
                                        {{ $localidad->fecha_creacion && $localidad->fecha_creacion !== '0000-00-00' 
                                        ? \Carbon\Carbon::parse($localidad->fecha_creacion)->format('d-m-Y') 
                                        : '-' }}
                                    </td>
                                    <td>
                                        <div class="d-flex gap-2">

                                            <button class="btn btn-editar btn-sm {{ $localidad->estado == 0 ? 'disabled' : '' }} editar-localidad"
                                                title="Editar"
                                                data-id-localidad="{{ $localidad->id }}"
                                                onclick="editarLocalidad('{{ $localidad->id }}')">
                                                <i class="fas fa-pen"></i>
                                            </button>
                                            <button
                                                type="button"
                                                onclick="irControlExtintorCliente({{ $cliente->id }}, {{ $localidad->id }})"
                                                class="btn btn-control-extintores btn-sm"
                                                title="Control Extintores"
                                                {{ $cliente->estado == 0 ? 'disabled' : '' }}
                                                >
                                                <i class="fas fa-list"></i>
                                            </button>
                                            <button class="btn btn-sm {{ $localidad->estado == 1 ? 'btn-eliminar' : 'btn-success' }} eliminar-localidad"
                                                title="{{ $localidad->estado == 1 ? 'Eliminar' : 'Restaurar' }}"
                                                data-id-localidad="{{ $localidad->id }}"
                                                onclick="eliminarLocalidad('{{ $localidad->id }}', '{{ $localidad->estado }}')">
                                                <i class="fas {{ $localidad->estado == 1 ? 'fa-trash' : 'fa-undo' }}"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                                @else
                                <tr>
                                    <td colspan="6" class="text-center">No hay localidades</td>
                                </tr>
                                @endif
                            </tbody>
                        </table>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="11" class="text-center">No hay clientes registrados</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @include('componentes.pagination', ['items' => $clientes])
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        document.querySelectorAll(".toggle-localidades").forEach(button => {
            button.addEventListener("click", function() {
                let id = this.getAttribute("data-id");
                let row = document.getElementById(`localidades-${id}`);

                if (row.classList.contains("d-none")) {
                    row.classList.remove("d-none");
                    this.textContent = "-";
                } else {
                    row.classList.add("d-none");
                    this.textContent = "+";
                }
            });
        });
    });
</script>

@endsection

@push('after-scripts')
    @vite('resources/js/cliente.js')
    @vite('resources/js/localidad.js')
@endpush