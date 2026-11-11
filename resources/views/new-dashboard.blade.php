@push('after-styles')
<style>
    .table-responsive-scroll {
        max-height: 200px;
        overflow-y: auto;
    }

    .table-responsive-scroll thead th {
        position: sticky;
        top: 0;
        background: var(--sepro);
        color: #fff;
        z-index: 1;
    }

    .table-responsive-scroll tbody tr:nth-child(even) {
        background-color: #f8f9fa;
    }

    .btn-sepro {
        background-color: var(--sepro);
        color: #fff;
        border: none;
    }

    .btn-sepro:hover,
    .btn-sepro:focus {
        background-color: var(--sepro);
        filter: brightness(0.9);
        box-shadow: 0 0 0 0.2rem rgba(0, 0, 0, 0.1);
    }

    .observacion-alerta {
        width: 37px;
        height: 37px;
        background-color: #fff;
        border-radius: 8px;
        border: 2px solid #dc3545;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.3s ease;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.2);
    }

    .observacion-alerta:hover {
        transform: scale(1.05);
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.3);
    }

    .img-alerta {
        width: 35px;
        height: auto;
    }

    .modal-content {
        border-radius: 0.75rem;
    }

    .table-hover tbody tr:hover {
        background-color: #f1f1f1;
    }

</style>
@endpush

<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card shadow-sm">
                <div class="card-body py-3 px-4 d-block">
                    <div class="mb-3">
                        <h5 class="fw-bold gap-2 mb-2 fs-6" style="color: var(--sepro);" for="localidad">
                            <i class="bi bi-buildings fs-4 text-secondary"></i> LOCALIDAD:
                        </h5>
                        <div class="input-group">
                            <select id="localidad" name="localidad" class="form-control shadow-sm">
                                <option value="">Seleccione una Localidad</option>
                                @foreach($localidades as $localidad)
                                    <option value="{{ $localidad->id }}">{{ $localidad->nombre }}</option>
                                @endforeach
                            </select>
                            <span class="input-group-text bg-light form-select-sm">
                                <i class="bi bi-search text-muted"></i>
                            </span>
                        </div>
                    </div>

                    <div class="mb-3">
                        <h5 class="fw-bold gap-2 mb-2 fs-6" style="color: var(--sepro);" for="fechaRevision">
                            <i class="bi bi-calendar-event fs-4 text-secondary"></i> FECHA DE REVISIÓN
                        </h5>
                        <div class="input-group">
                            <input id="fechaRevision" type="text" class="form-control shadow-sm"
                                   placeholder="Seleccionar fecha..." readonly autocomplete="off">
                            <span class="input-group-text bg-light">
                                <i class="bi bi-search text-muted"></i>
                            </span>
                        </div>
                    </div>

                    <div>
                        <h5 class="fw-bold gap-2 mb-2 fs-6" style="color: var(--sepro);" for="fechaRecarga">
                            <i class="bi bi-calendar-check fs-4 text-secondary"></i> FECHA PRÓXIMA RECARGA
                        </h5>
                        <div class="input-group">
                            <input id="fechaRecarga" type="text" class="form-control shadow-sm"
                                   placeholder="Seleccionar fecha..." readonly autocomplete="off">
                            <span class="input-group-text bg-light">
                            <i class="bi bi-search text-muted"></i>
                            </span>
                        </div>
                    </div>

                    <div class="d-grid mt-4">
                        <button type="button" class="btn btn-sepro" id="btnBuscar">
                            <i class="bi bi-search me-1"></i> Buscar
                        </button>
                    </div>

                </div>
            </div>
        </div>

        <div class="col-md-9">
            <div class="card shadow-sm position-relative">

                <div class="position-absolute top-0 end-0 m-2">
                    <div class="observacion-alerta" onclick="mostrarModalObservaciones()" title="Ver observaciones internas">
                        <img src="{{ asset('img/extintor-advertencia.png') }}" alt="Ver observaciones" class="img-alerta">
                    </div>
                </div>

                <div class="card-body p-4" style="background-color: #E5402B">

                    <div class="d-flex justify-content-center align-items-center text-white">
                        <h5 class="fw-semibold text-uppercase fs-6">Resumen de Extintores por Recargar</h5>
                    </div>

                    <div class="row g-3 h-100">
                        <!-- PQS -->
                        <div class="col-md-3">
                            <div class="card bg-white border border-danger h-100">
                                <div class="card-body p-0 d-flex flex-column h-100">
                                    <div class="row g-0 flex-grow-1 p-1">
                                        <!-- Columna de la imagen -->
                                        <div class="col-5 d-flex align-items-start justify-content-start h-100">
                                            <img
                                                    src="{{ asset('img/extintor_pqs.png') }}"
                                                    alt="Extintor"
                                                    class="img-fluid w-100 me-1" style="height: 120px;"
                                            />
                                        </div>
                                        <!-- Columna del texto -->
                                        <div class="col-6 d-flex flex-column justify-content-start h-100">
                                            <h6 class="fw-semibold mb-2 text-uppercase" style="color: var(--sepro)">
                                                PQS</h6>
                                            <ul id="resumenPQS" class="list-unstyled small mb-0"></ul>
                                                <!-- aquí el JS inyectará: <li>n PQS x LB</li> -->
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- CO₂ -->
                        <div class="col-md-3">
                            <div class="card bg-white border border-secondary h-100">
                                <div class="card-body p-0 d-flex flex-column h-100">
                                    <div class="row g-0 flex-grow-1 p-1">
                                        <div class="col-5 d-flex align-items-start justify-content-start h-100">
                                            <!-- Columna de la imagen -->
                                            <img
                                                    src="{{ asset('img/extintor_co2.png') }}"
                                                    alt="Extintor"
                                                    class="img-fluid w-100 me-1" style="height: 120px;"
                                            />
                                        </div>
                                        <div class="col-6 d-flex flex-column justify-content-start h-100">
                                            <h6 class="fw-semibold mb-2 text-uppercase" style="color: var(--sepro)">
                                                CO₂</h6>
                                            <ul id="resumenCO2" class="list-unstyled small mb-0"></ul>
                                                <!-- aquí el JS inyectará: <li>n CO₂ x LB</li> -->
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- FOAM, H₂O, Tipo K -->
                        <div class="col-md-6 d-grid gap-3">
                            <div class="card bg-white border border-warning">
                                <div class="card-body py-1">
                                    <div class="row g-0 align-items-start h-100">
                                        <div class="col-3 pe-3 h-100">
                                            <img
                                                    src="{{ asset('img/extintor_foam.png') }}"
                                                    alt="Extintor"
                                                    class="img-fluid w-100" style="height: 120px;"
                                            />
                                        </div>
                                        <div class="col-9 h-100">
                                            <h6 class="fw-semibold mb-2 text-uppercase" style="color: var(--sepro)">
                                                FOAM, H₂O, Tipo K</h6>
                                            <ul id="resumenFOAM" class="list-unstyled small mb-0"></ul>
                                                <!-- aquí el JS inyectará: <li>n H₂O x Gal</li>, etc. -->
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="card bg-white border border-warning">
                                <div class="card-body py-1">
                                    <div class="row g-0 align-items-start h-100">
                                        <div class="col-3 pe-4 h-100">
                                            <img
                                                    src="{{ asset('img/extintor_otro.png') }}"
                                                    alt="Extintor"
                                                    class="img-fluid w-100" style="height: 120px;"
                                            />
                                        </div>
                                        <div class="col-9 h-100">
                                            <h6 class="fw-semibold mb-2 text-uppercase" style="color: var(--sepro)">
                                                Otros cilindros</h6>
                                            <ul id="resumenOTROS" class="list-unstyled small mb-0"></ul>
                                                <!-- aquí el JS inyectará: <li>n Nitrógeno</li>, etc. -->
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow mb-4 overflow-hidden">
                <div class="card-body pt-4 pb-1 px-4 d-block">
                    <!-- Título con color y icono -->
                    <h5
                            class="text-center fw-semibold text-uppercase fs-6"
                            style="color: var(--sepro);"
                    >
                        <i class="fas fa-fire-extinguisher  align-middle me-2"></i>
                        Extintores Próximos a Recargar, Caducados, Utilizados, Sin Presión o Sobrepresión
                    </h5>

                    <!-- Tabla con header fijo y scroll en el body -->
                    <div class="table-responsive-scroll">
                        <table class="table table-sm table-bordered mb-0">
                            <thead>
                            <tr>
                                <th><i class="fas fa-hashtag me-1"></i></th>
                                <th><i class="fas fa-calendar-alt me-1"></i>Fecha Revisión</th>
                                <th><i class="fas fa-barcode me-1"></i>Código</th>
                                <th><i class="fas fa-map-marker-alt me-1"></i>Ubicación</th>
                                <th><i class="fas fa-calendar-alt me-1"></i>Fecha Prox. Recarga</th>
                                <th><i class="fas fa-layer-group me-1"></i>Clase</th>
                                <th><i class="fas fa-weight-hanging me-1"></i>Peso</th>
                                <th><i class="fas fa-comment-alt me-1"></i>Observación</th>
                            </tr>
                            </thead>
                            <tbody id="extintorTableBody">
                            <tr>
                                <td colspan="8" class="text-center small text-muted">
                                    No hay datos para mostrar
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Thin bar below the table -->
                    <div
                      class="d-flex flex-nowrap justify-content-between align-items-center px-2 mt-2 bg-light"
                      style="font-size: 14px; white-space: nowrap;"
                    >
                      <!-- Left: Record count -->
                      <div class="d-inline-flex align-items-center me-3">
                        <span class="fw-bold text-uppercase me-1">Cant :</span>
                        <span id="recordCount">0</span>
                      </div>

                      <!-- Right: Download button -->
                      <div class="d-inline-flex align-items-center d-none" id="downloadButtonContainer">
                        <span class="fw-bold text-uppercase me-2">Descargar Resumen:</span>
                        <button
                          class="btn btn-sm btn-success d-flex align-items-center flex-none"
                          onclick="exportarResumenExtintores()"
                        >
                          <i class="fas fa-file-excel me-2"></i>
                          <span>Excel</span>
                        </button>
                        {{-- <div class="observacion-alerta ms-2" onclick="mostrarModalObservaciones()" title="Ver observaciones internas">
                            <img src="{{ asset('img/extintor-advertencia.png') }}" alt="Ver observaciones" class="img-alerta">
                        </div> --}}
                      </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <div class="row mb-4">
        <!-- Otras Novedades -->
        <div class="col-md-3">
            <div class="card h-100 shadow-sm">
                <div class="card-body d-block">
                    <div class="row g-0 flex-grow-1 mb-2">
                        <div class="col-3 p-0 align-items-center justify-content-center d-flex" style="height: 60px;">
                            <img
                                    src="{{ asset('img/ojo.png') }}"
                                    alt="Extintor"
                                    class="img-fluid w-100 h-auto"
                            />
                        </div>
                        <div class="col-9 p-0 align-items-center justify-content-center d-flex">
                            <h5 class="text-center text-danger fw-semibold fs-6 text-uppercase">
                                Otras Novedades
                            </h5>
                        </div>
                    </div>
                    <div class="table-responsive-scroll">
                        <table class="table table-sm table-bordered mb-0">
                            <colgroup>
                                <col style="width: 20%"/>
                                <col/>
                            </colgroup>
                            <thead>
                            <tr>
                                <th>CANT.</th>
                                <th>DESCRIPCIÓN</th>
                            </tr>
                            </thead>
                            <tbody id="otrasNovedadesBody">
                            <tr>
                                <td colspan="2" class="text-center small text-muted">
                                    Sin datos
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Accesorios de Extintores -->
        <div class="col-md-3">
            <div class="card h-100 shadow-sm">
                <div class="card-body d-block">
                    <div class="row g-0 flex-grow-1 mb-2">
                        <div class="col-3 p-0 align-items-center justify-content-center d-flex" style="height: 60px;">
                            <img
                                    src="{{ asset('img/accesorios.png') }}"
                                    alt="Extintor"
                                    class="img-fluid w-100 h-auto"
                            />
                        </div>
                        <div class="col-9 p-0 align-items-center justify-content-center d-flex">
                            <h5 class="text-center text-danger fw-semibold fs-6 text-uppercase">
                                Accesorios de Extintores
                            </h5>
                        </div>
                    </div>
                    <div class="table-responsive-scroll">
                        <table class="table table-sm table-bordered mb-0">
                            <colgroup>
                                <col style="width: 20%"/>
                                <col/>
                            </colgroup>
                            <thead>
                            <tr>
                                <th>CANT.</th>
                                <th>DESCRIPCIÓN</th>
                            </tr>
                            </thead>
                            <tbody id="accesoriosBody">
                            <tr>
                                <td colspan="2" class="text-center small text-muted">
                                    Sin datos
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Señalización -->
        <div class="col-md-3">
            <div class="card h-100 shadow-sm">
                <div class="card-body d-block">
                    <div class="row g-0 flex-grow-1 mb-2">
                        <div class="col-3 p-0 align-items-center justify-content-center d-flex" style="height: 60px;">
                            <img
                                    src="{{ asset('img/extinguidor.png') }}"
                                    alt="Extintor"
                                    class="img-fluid w-100 h-auto"
                            />
                        </div>
                        <div class="col-9 p-0 align-items-center justify-content-center d-flex">
                            <h5 class="text-center text-danger fw-semibold fs-6 text-uppercase">
                                Señalización
                            </h5>
                        </div>
                    </div>
                    <div class="table-responsive-scroll">
                        <table class="table table-sm table-bordered mb-0">
                            <colgroup>
                                <col style="width: 20%"/>
                                <col/>
                            </colgroup>
                            <thead>
                            <tr>
                                <th>CANT.</th>
                                <th>DESCRIPCIÓN</th>
                            </tr>
                            </thead>
                            <tbody id="senalizacionBody">
                            <tr>
                                <td colspan="2" class="text-center small text-muted">
                                    Sin datos
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Novedades Gabinetes -->
        <div class="col-md-3">
            <div class="card h-100 shadow-sm">
                <div class="card-body d-block">
                    <div class="row g-0 flex-grow-1 mb-2">
                        <div class="col-3 p-0 align-items-center justify-content-center d-flex" style="height: 60px;">
                            <img
                                    src="{{ asset('img/manguera.png') }}"
                                    alt="Extintor"
                                    class="img-fluid w-100 h-auto"
                            />
                        </div>
                        <div class="col-9 p-0 align-items-center justify-content-center d-flex">
                            <h5 class="text-center text-danger fw-semibold fs-6 text-uppercase">
                                Novedades Gabinetes
                            </h5>
                        </div>
                    </div>
                    <div class="table-responsive-scroll">
                        <table class="table table-sm table-bordered mb-0">
                            <colgroup>
                                <col style="width: 20%"/>
                                <col/>
                            </colgroup>
                            <thead>
                            <tr>
                                <th>CANT.</th>
                                <th>DESCRIPCIÓN</th>
                            </tr>
                            </thead>
                            <tbody id="gabinetesBody">
                            <tr>
                                <td colspan="2" class="text-center small text-muted">
                                    Sin datos
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalObservacionesInternas" tabindex="-1" aria-labelledby="modalObsLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content border-0 shadow-lg">
            <div class="modal-header text-white" style="background-color: var(--sepro); border-top-left-radius: .75rem; border-top-right-radius: .75rem;">
                <h5 class="modal-title fw-semibold text-uppercase" id="modalObsLabel">
                <i class="fas fa-exclamation-triangle me-2"></i> Observaciones Internas
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body p-0">
                <table class="table table-sm table-hover table-bordered align-middle mb-0">
                <thead class="table-light text-center text-uppercase small">
                    <tr>
                    <th style="width: 20%;">Código</th>
                    <th>Observaciones</th>
                    </tr>
                </thead>
                <tbody id="observacionesInternasBody">
                    <tr>
                    <td colspan="2" class="text-center small text-muted py-3">Sin datos</td>
                    </tr>
                </tbody>
                </table>
            </div>
            </div>
        </div>
    </div>

</div>