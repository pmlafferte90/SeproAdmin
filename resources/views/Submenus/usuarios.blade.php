@extends('layouts.app')

@section('content')
@include('modales.usuarioform')

<div class="mt-4 bg-white p-4 rounded shadow-sm">
    <!--div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="h5 font-weight-bold">Usuario</h2>
        <button class="btn btn-danger text-white" data-bs-toggle="modal" data-bs-target="#usuarioModal" onclick="crearusuario()">+ NUEVO USUARIO</button>
    </div-->

    <!--div class="table-responsive" style="overflow-x: auto;">
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
                            <button class="btn btn-sm btn-editar {{ $usuario->estado == 0 ? 'disabled' : '' }}"
                                    title="Editar" 
                                    data-bs-toggle="modal" 
                                    data-bs-target="#usuarioModal" 
                                    onclick="editarusuario({{ json_encode($usuario) }})">
                                <i class="fas fa-pen"></i>
                            </button>

                            @if ($usuario->estado == 1)
                                <button class="btn btn-danger btn-sm"
                                        title="Inactivar" 
                                        onclick="cambiarEstadousuario({{ $usuario->id }}, {{ $usuario->estado }})">
                                    <i class="fas fa-trash"></i>
                                </button>
                            @else
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
        </table-->

        @section('tabla-create-button')
            <button class="btn btn-danger text-white" onclick="crearusuario()">+ NUEVO USUARIO</button>
        @endsection

        <x-table
            :titulo="'Usuarios del Sistema'"
            :headers="[
                ['key' => 'usuario',         'label' => 'USUARIO',          'style' => 'width: 150px;'],
                ['key' => 'rol',             'label' => 'ROL',              'style' => 'width: 100px;'],
                ['key' => 'nombre',          'label' => 'NOMBRE',           'style' => 'width: 150px;'],
                ['key' => 'apellido',        'label' => 'APELLIDO',         'style' => 'width: 150px;'],
                ['key' => 'email',           'label' => 'EMAIL',            'style' => 'width: 250px;'],
                ['key' => 'estado',          'label' => 'ESTADO',           'style' => 'width: 150px;'],
                ['key' => 'empresa',         'label' => 'EMPRESA',          'style' => 'width: 350px;'],
                ['key' => 'fecha_creacion',  'label' => 'FECHA CREACIÓN',   'style' => 'width: 150px;'],
                ['key' => 'fecha_fin',       'label' => 'FECHA FIN',        'style' => 'width: 150px;'],
                ['key' => 'accion',          'label' => 'ACCIÓN',           'class' => 'sticky-col bg-white border-start text-center', 'style' => 'width: 150px;']
            ]"
            :rows="$usuarios->map(function($usuario) {
                return [
                    $usuario->usuario,
                    $usuario->rol,
                    $usuario->nombre,
                    $usuario->apellido,
                    $usuario->email,
                    view('components.estado-badge', ['estado' => $usuario->estado])->render(),
                    $usuario->razon_social,
                    $usuario->fecha_creacion && $usuario->fecha_creacion !== '0000-00-00'
                        ? \Carbon\Carbon::parse($usuario->fecha_creacion)->format('d-m-Y')
                        : '-',
                    $usuario->fecha_fin && $usuario->fecha_fin !== '0000-00-00'
                        ? \Carbon\Carbon::parse($usuario->fecha_fin)->format('d-m-Y')
                        : '-',
                    view('components.actions', [
                        'actions' => [
                            [
                                'title' => 'Editar',
                                'icon' => 'fas fa-pen',
                                'class' => 'btn-editar ' . ($usuario->estado == 0 ? 'disabled' : ''),
                                'onclick' => 'editarusuario(' . json_encode($usuario) . ')'
                            ],
                            [
                                'title' => $usuario->estado == 1 ? 'Inactivar' : 'Restaurar',
                                'icon' => $usuario->estado == 1 ? 'fas fa-trash' : 'fas fa-undo',
                                'class' => $usuario->estado == 1 ? 'btn-danger' : 'btn-success',
                                'onclick' => 'cambiarEstadousuario(' . $usuario->id . ',' . $usuario->estado .')'
                            ]
                        ]
                    ])->render()
                ];
            })->toArray()"
        :paginator="$usuarios"></x-table>
    </div>

</div>
@endsection

@push('after-scripts')
    @vite('resources/js/usuarios.js')
@endpush