@extends('layouts.app')

@section('content')
@include('modales.usuarioform')

<div class="mt-4 bg-white p-4 rounded shadow-sm">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="h5 font-weight-bold">Usuario</h2>
        <button class="btn btn-danger text-white" data-bs-toggle="modal" data-bs-target="#usuarioModal" onclick="crearusuario()">+ NUEVO USUARIO</button>
    </div>

    @include('componentes.buscador', ['actionRoute' => route('usuario.index')])

    <div class="table-responsive" style="overflow-x: auto;">
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>USUARIO</th>
                    <th>ROL</th>
                    <th>EMPRESA</th>
                    <th>FECHA CREACIÓN</th>
                    <th>FECHA FIN</th>
                    <th>ESTADO</th>
                    <th width="15%">ACCIÓN</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($usuarios as $usuario)
                <tr>
                    <td>{{ $usuario->usuario }}</td>
                    <td>{{ $usuario->rol }}</td>
                    <td>{{ $usuario->razon_social }}</td>
                    <td style="white-space: nowrap;">
                        {{ $usuario->fecha_creacion && $usuario->fecha_creacion !== '0000-00-00' 
                            ? \Carbon\Carbon::parse($usuario->fecha_creacion)->format('d-m-Y') 
                            : '-' }}
                    </td>
                    <td style="white-space: nowrap;">
                        {{ $usuario->fecha_fin && $usuario->fecha_fin !== '0000-00-00' 
                            ? \Carbon\Carbon::parse($usuario->fecha_fin)->format('d-m-Y') 
                            : '-' }}
                    </td>
                    <td>
                        <span class="badge {{ $usuario->estado == 1 ? 'bg-success' : 'bg-danger' }}">
                            {{ $usuario->estado == 1 ? 'Activo' : 'Inactivo' }}
                        </span>
                    </td>
                    <td>
                        <div class="d-flex gap-2">
                            <!-- Botón Editar -->
                            <button class="btn btn-sm btn-editar {{ $usuario->estado == 0 ? 'disabled' : '' }}" 
                                    title="Editar" 
                                    data-bs-toggle="modal" 
                                    data-bs-target="#usuarioModal" 
                                    onclick="editarusuario({{ json_encode($usuario) }})">
                                <i class="fas fa-pen"></i>
                            </button>

                            @if ($usuario->estado == 1)
                                <!-- Botón Inactivar -->
                                <button class="btn btn-danger btn-sm" 
                                        title="Inactivar" 
                                        onclick="cambiarEstadousuario({{ $usuario->id }}, {{ $usuario->estado }})">
                                    <i class="fas fa-trash"></i>
                                </button>
                            @else
                                <!-- Botón Restaurar -->
                                <button class="btn btn-success btn-sm" 
                                        title="Restaurar" 
                                        onclick="cambiarEstadousuario({{ $usuario->id }}, {{ $usuario->estado }})">
                                    <i class="fas fa-undo"></i>
                                </button>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center">No hay usuario disponibles.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    @include('componentes.pagination', ['items' => $usuarios])

</div>
@endsection

@push('after-scripts')
    @vite('resources/js/usuarios.js')
@endpush