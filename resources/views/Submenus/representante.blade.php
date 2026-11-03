@extends('layouts.app')

@section('content')
@include('modales.representanteform')

<div class="mt-4 bg-white p-4 rounded shadow-sm">
    <div class="table-responsive" style="overflow-x: auto; width: 100%;">
        @section('tabla-extra-content')
            <form
                action="{{ route('representante.import') }}"
                method="POST"
                enctype="multipart/form-data"
                class="d-flex align-items-center"
            >
                @csrf

                <div class="me-2">
                    <label for="archivo" class="form-label visually-hidden">Archivo</label>
                    <input
                        type="file"
                        name="archivo"
                        id="archivo"
                        accept=".csv,.txt,.xlsx"
                        class="form-control form-control-sm"
                        required
                    >
                </div>

                <button type="submit" class="btn btn-sm btn-primary">
                    Importar
                </button>
            </form>
        @endsection

        @section('tabla-create-button')
            <button class='btn btn-sm btn-danger text-white' style='font-size: 13px;' onclick='nuevoRepresentante()'>
                + NUEVO REPRESENTANTE
            </button>
        @endsection

        <x-table
            :headers="[
                ['key' => 'empresa',         'label' => 'EMPRESA'],
                ['key' => 'localidades',     'label' => 'LOCALIDADES'],
                ['key' => 'nombre',          'label' => 'NOMBRE'],
                ['key' => 'apellido',        'label' => 'APELLIDO'],
                ['key' => 'usuario',         'label' => 'USUARIO'],
                ['key' => 'identificacion',  'label' => 'IDENTIFICACIÓN'],
                ['key' => 'telefono',        'label' => 'TELÉFONO'],
                ['key' => 'email',           'label' => 'EMAIL'],
                ['key' => 'estado',          'label' => 'ESTADO'],
                [
                    'key'   => 'acciones',
                    'label' => 'ACCIÓN',
                    'class' => 'sticky-col bg-white border-start',
                    'style' => 'z-index:5;box-shadow:-4px 0 6px -2px rgba(0,0,0,0.2);'
                ],
            ]"

            :rows="$representantes->map(function($r) {
                return [
                    $r->razon_social,
                    $r->ver_todas_localidades
                        ? '<span class=\'badge bg-primary fs-6\'>Todas</span>'
                        : e($r->localidades->pluck('nombre')->implode(', ')),
                    e($r->nombre),
                    e($r->apellido),
                    e($r->usuario->usuario ?? 'Sin usuario'),
                    e($r->identificacion),
                    e($r->telefono),
                    e($r->correo),
                    '<span class=\'badge fs-6 ' . ($r->estado == 1 ? 'bg-success' : 'bg-danger') . '\'>' . ($r->estado == 1 ? 'Activo' : 'Inactivo') . '</span>',
                    view('components.actions', [
                        'actions' => array_filter([
                            [
                                'class'   => 'btn-editar btn-sm' . ($r->estado == 0 ? ' disabled' : ''),
                                'title'   => 'Editar',
                                'icon'    => 'fas fa-pen',
                                'onclick' => $r->estado == 1 ? 'editarRepresentante(\'' . $r->id . '\')' : ''
                            ],
                            [
                                'class'   => 'btn-sm ' . ($r->estado == 1 ? 'btn-eliminar' : 'btn-success'),
                                'title'   => $r->estado == 1 ? 'Eliminar' : 'Restaurar',
                                'icon'    => $r->estado == 1 ? 'fas fa-trash' : 'fas fa-undo',
                                'onclick' => 'cambiarEstadoRepresentante(\'' . $r->id . '\', \'' . $r->estado . '\')'
                            ],
                        ])
                    ])->render()
                ];
            })->toArray()"

            :paginator="$representantes"
            titulo='Representantes'
            subtitulo=''
        ></x-table>

    </div>
</div>
@endsection

@push('after-scripts')
    @vite('resources/js/representante.js')
@endpush
