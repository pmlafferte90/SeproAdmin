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
@include('modales.control-extintor.enviar-correo')


<div class="mt-4 bg-white p-4 rounded shadow-sm">
    <div class="table-responsive" style="overflow-x: auto; width: 100%;">
        @section('tabla-extra-content')
            <div class="d-flex align-items-center gap-2">
                <select id="mesSelect" class="form-select" style="width: 120px; font-size: 13px;" onchange="updateQueryParam('mes', this.value)">
                    <option value="">Todos los meses</option>
                    @foreach(range(1, 12) as $m)
                        <option value="{{ $m }}" {{ request('mes') == $m ? 'selected' : '' }}>
                            {{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}
                        </option>
                    @endforeach
                </select>

                <select id="anioSelect" class="form-select" style="width: 120px; font-size: 13px;" onchange="updateQueryParam('anio', this.value)">
                    @foreach(range(2023, date('Y')) as $y)
                        <option value="{{ $y }}" {{ request('anio', date('Y')) == $y ? 'selected' : '' }}>
                            {{ $y }}
                        </option>
                    @endforeach
                </select>
                <button class="btn btn-success" style="font-size: 13px;" onclick="exportarExcel({{ $id_cliente }})">
                    <i class="fa fa-file-excel"></i> Exportar Excel
                </button>

                <form
                id="importControlesForm"
                action="{{ route('extintores.controles.import') }}"
                method="POST"
                enctype="multipart/form-data"
                class="d-flex align-items-center gap-2"
                >
                @csrf
                <input type="hidden" name="id_localidad" value="{{ $id_localidad }}">

                <input
                    type="file"
                    name="archivo"
                    id="archivoControles"
                    accept=".csv,.txt,.xlsx"
                    class="form-control form-control-sm"
                    required
                />

                <button type="submit" class="btn btn-sm btn-primary">
                    Importar
                </button>
                </form>


                @hasanyrole('Admin')
                @if(request('mes') && request('anio'))
                <button type="button" class="btn btn-warning" style="font-size: 13px;" onclick="modalHelper.open('modalEnviarCorreo')">
                    <i class="fa fa-envelope"></i> Enviar Reporte por Correo
                </button>
                @endif
                @endhasanyrole
                @hasanyrole('Admin|Revisor')
                @if(request('mes') && request('anio'))
                <button
                    class="btn btn-success"
                    style="font-size: 13px;"
                    onclick="ubicacionesxRevisar({{ $id_localidad }}, {{ request('mes') }}, {{ request('anio') }})"
                >
                    <i class="fas fa-map-marker-alt"></i>
                    Ubicaciones por revisar
                </button>
                @endif
                @endhasanyrole
            </div>
        @endsection

        @section('tabla-create-button')
            @hasanyrole('Admin|Revisor')
            <button class="btn btn-danger text-white" style="font-size: 13px;" onclick="precargarControlExtintor()">+ NUEVO CONTROL</button>
            @endhasanyrole
        @endsection

        @section('table-footer')
            <strong>Total de extintores registrados para esta localidad:</strong>
            {{ $totalExtintoresxLocalidad }}
        @endsection

        @php
          $headers = [
            ['key' => 'fecha_revision',              'label' => 'FECHA REVISIÓN',                    'style' => 'min-width: 150px;'],
            ['key' => 'codigo',                      'label' => 'COD',                               'style' => 'min-width: 80px;'],
            ['key' => 'area_nombre',                 'label' => 'ÁREA',                              'style' => 'min-width: 100px;'],
            ['key' => 'ubicacion',                   'label' => 'UBICACIÓN',                         'style' => 'min-width: 200px;'],
            ['key' => 'clase',                       'label' => 'CLASE',                             'style' => 'min-width: 80px;'],
            ['key' => 'marca',                       'label' => 'MARCA',                             'style' => 'min-width: 80px;'],
            ['key' => 'peso',                        'label' => 'PESO',                              'style' => 'min-width: 80px;'],
            ['key' => 'manguera',                    'label' => 'MANGUERA', 'noShow' => true],
            ['key' => 'manometro',                   'label' => 'MANÓMETRO', 'noShow' => true],
            ['key' => 'seguro',                      'label' => 'SEGURO', 'noShow' => true],
            ['key' => 'cilindro',                    'label' => 'CILINDRO', 'noShow' => true],
            ['key' => 'fecha_recarga',               'label' => 'FECHA RECARGA',                     'style' => 'min-width: 150px;'],
            ['key' => 'fecha_prox_recarga',          'label' => 'FECHA PROX. RECARGA',               'style' => 'min-width: 150px;'],
            ['key' => 'area_seguridad',              'label' => 'ÁREA DE SEGURIDAD',                 'style' => 'min-width: 100px;'],
            ['key' => 'instructivo_uso',             'label' => 'INSTRUCTIVO DE USO',                'style' => 'min-width: 100px;'],
            ['key' => 'instructivo_clasif',          'label' => 'INSTRUCTIVO DE CLASIFICACIÓN',      'style' => 'min-width: 100px;'],
            ['key' => 'flecha_ubicacion',            'label' => 'FLECHA UBICACIÓN',                  'style' => 'min-width: 100px;'],
            ['key' => 'silueta',                     'label' => 'SILUETA DE EXTINTOR',               'style' => 'min-width: 100px;'],
            ['key' => 'rect_instalacion',            'label' => 'RECT. INSTALACIÓN'],
            ['key' => 'hidrante',                    'label' => 'EXTINTOR DENTRO DE HIDRANTE Y/O ARMARIO','style'=>'min-width:200px;'],
            ['key' => 'dimension_vidrio',            'label' => 'DIMENSIÓN VIDRIO/ACRILICO'],
            ['key' => 'fecha_fabricacion',           'label' => 'FECHA FABRICACIÓN',                 'style' => 'min-width: 150px;'],
            ['key' => 'fecha_prueba_hidros',         'label' => 'FECHA PRUEBA HIDROSTÁTICA',         'style' => 'min-width: 150px;'],
            ['key' => 'novedad_marbete',             'label' => 'MARBETE',                           'style' => 'min-width: 150px;'],
            ['key' => 'observaciones_nomen',         'label' => 'OBSERVACIONES', 'style' => 'min-width: 150px;']
          ];

           if (auth()->user()->hasRole(['Admin', 'Revisor'])) {
              $headers[] = ['key' => 'observaciones', 'label' => 'OBSERVACIONES INTERNAS'];
           }

           if (auth()->user()->hasRole('Admin')) {
                $headers[] = [
                  'key'    => 'auditor',
                  'label'  => 'AUDITOR',
                  'style'  => 'min-width:150px;',
                  'noShow' => true
                ];
           }

          $headers[] = [
              'key'   => 'acciones',
              'label' => 'ACCIONES',
              'class' => 'sticky-col bg-white border-start',
              'style' => 'z-index:5;box-shadow:-4px 0 6px -2px rgba(0,0,0,0.2);'
          ];


          $rows = $controlExtintores->map(function($controlExtintor) use ($id_cliente) {
            // columnas comunes
            $row = [
                    $controlExtintor->fecha_revision,
                    $controlExtintor->codigo,
                    $controlExtintor->area_nombre,
                    $controlExtintor->ubicacion,
                    $controlExtintor->clase,
                    $controlExtintor->marca,
                    rtrim(rtrim($controlExtintor->peso, '0'), '.') . ' ' . $controlExtintor->unidad,
                    $controlExtintor->mango,
                    $controlExtintor->mano,
                    $controlExtintor->seguro,
                    $controlExtintor->cilindro,
                    ucfirst(\Carbon\Carbon::parse($controlExtintor->fecha_recarga)->translatedFormat('F Y')),
                    ucfirst(\Carbon\Carbon::parse($controlExtintor->fecha_prox_recarga)->translatedFormat('F Y')),
                    $controlExtintor->area_seguridad,
                    $controlExtintor->instructivo_uso,
                    $controlExtintor->instructivo_clasificacion,
                    $controlExtintor->flecha_ubicacion,
                    $controlExtintor->silueta,
                    $controlExtintor->rectangulo_instalacion,
                    $controlExtintor->hidrante,
                    $controlExtintor->dimension_vidrio,
                    $controlExtintor->fecha_fabricacion,
                    $controlExtintor->fecha_prueba_hidrostatica,
                    $controlExtintor->novedad_marbete,
                    $controlExtintor->observacionesNomenclatura->pluck('abreviatura')->implode(', ') ?: '-'
                ];

            if (auth()->user()->hasRole(['Admin', 'Revisor'])) {
              $row[] = $controlExtintor->observaciones ?? '-';
            }

            if (auth()->user()->hasRole('Admin')) {
               $row[] = $controlExtintor->auditor_full_name ?? '-';
            }

            // Acciones
            $row[] = view('components.actions', [
                'actions' => array_filter([
                            [
                                'class' => 'btn-success',
                                'title' => 'Visualizar Imagen',
                                'icon' => 'fa fa-fire-extinguisher',
                                'onclick' => $controlExtintor->tiene_imagen
                                    ? 'viewImageById(' . $controlExtintor->id . ')'
                                    : ''
                            ],
                            auth()->user()->hasAnyRole(['Admin', 'Revisor']) ? [
                                'class' => 'btn-editar',
                                'title' => 'Editar',
                                'icon' => 'fas fa-pen',
                                'onclick' => 'editarControlExtintorCliente(' . json_encode($controlExtintor) . ', ' . $id_cliente . ')'
                            ] : null,
                            auth()->user()->hasAnyRole(['Admin', 'Revisor']) ? [
                                'class' => $controlExtintor->estado == 1 ? 'btn-eliminar' : 'btn-success',
                                'title' => $controlExtintor->estado == 1 ? 'Eliminar' : 'Restaurar',
                                'icon' => $controlExtintor->estado == 1 ? 'fas fa-trash' : 'fas fa-undo',
                                'onclick' => 'cambiarEstadoControl(' . $controlExtintor->id . ', ' . $controlExtintor->estado . ')'
                            ] : null,
                        ])
              ])->render();
            return $row;
          })->toArray();
        @endphp

        <x-table
          :headers="$headers"
          :rows="$rows"
          :paginator="$controlExtintores"
          :titulo="$nombreCliente"
          :subtitulo="$nombreLocalidad"
        ></x-table>
   
       </div>
</div>
<!-- Modal para mostrar la imagen -->
<div class="modal fade"
    id="imageModal"
    tabindex="-1"
    aria-labelledby="imageModalLabel"
    data-bs-backdrop="static"
    data-bs-keyboard="false"
  >
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


@push('after-scripts')
<style>
    :root { --sepro: #19346A; }

    /* Modal más serio */
    #ubicacionesModal .modal-content {
        border-radius: .5rem;
        box-shadow: 0 .75rem 1.5rem rgba(0,0,0,.1);
    }
    #ubicacionesModal .modal-header {
        background: var(--sepro);
        color: #fff;
        border-bottom: 1px solid rgba(255,255,255,.2);
    }
    #ubicacionesModal .modal-header .modal-title {
        font-weight: 600;
        letter-spacing: .5px;
    }
    #ubicacionesModal .modal-footer {
        border-top: none;
        padding: 1rem 1.5rem;
    }
    #ubicacionesModal .tabla-scroll {
        max-height: 55vh;
        overflow-y: auto;
    }

    #ubicacionesModal table {
        width: 100%;
        table-layout: fixed;
        border-collapse: collapse;
    }

    #ubicacionesModal thead th,
    #ubicacionesModal tbody td {
        padding: 0.75rem 1rem;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    #ubicacionesModal thead th {
        position: sticky;
        top: 0;
        z-index: 2;
        background: #f8f9fa;
        font-weight: 600;
        border-bottom: 2px solid #e9ecef;
    }
    
     /* Botón aceptar sepro */
    #ubicacionesModal .btn-aceptar {
        background: var(--sepro);
        border-color: var(--sepro);
        color: #fff;
        font-weight: 500;
        padding: .5rem 1.25rem;
    }
</style>
@endpush


{{-- Modal para mostrar ubicaciones por revisar --}}
<div class="modal fade"
    id="ubicacionesModal"
    tabindex="-1"
    aria-hidden="true"
    data-bs-backdrop="static"
    data-bs-keyboard="false"
    >
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content">

      <div class="modal-header">
        <h5 class="modal-title">Ubicaciones por Revisar</h5>
        {{-- se elimina la “X” --}}
      </div>

      <div class="modal-body" id="ubicacionesModalBody">
        {{-- contenido inyectado por JS --}}
      </div>

      <div class="modal-footer">
        <button 
          type="button" 
          class="btn btn-aceptar" 
          data-bs-dismiss="modal"
        >
          Aceptar
        </button>
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