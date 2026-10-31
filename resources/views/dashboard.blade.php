<div class="row">
    @if (auth()->user()->rol == 1)
        <div class="col-sm-6 col-lg-4">
            <div class="card mb-4">
                <div class="card-body">
                    <div class="fs-4 fw-semibold">{{ $usuariosActivos }}</div>
                    <div>Usuarios Activos</div>
                    <div class="progress progress-thin my-2">
                        <div class="progress-bar bg-success" role="progressbar" style="width: 100%" 
                             aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                    <small class="text-medium-emphasis">Usuarios activos en el sistema</small>
                </div>
            </div>
        </div>
        <!-- /.col-->

        <div class="col-sm-6 col-lg-4">
            <div class="card mb-4">
                <div class="card-body">
                    <div class="fs-4 fw-semibold">{{ $clientesActivos }}</div>
                    <div>Clientes Activos</div>
                    <div class="progress progress-thin my-2">
                        <div class="progress-bar bg-info" role="progressbar" style="width: 100%" 
                             aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                    <small class="text-medium-emphasis">Total de clientes registrados</small>
                </div>
            </div>
        </div>
    @endif
    <!-- /.col-->

    @php
        $recargaProxima = $recarga[0] ?? null; // Validamos que exista el primer registro
        $empresa = $recargaProxima['razon_social'] ?? 'N/A';
        $fechaProxRecarga = isset($recargaProxima['fecha_prox_recarga']) && !empty($recargaProxima['fecha_prox_recarga']) 
            ? \Carbon\Carbon::parse($recargaProxima['fecha_prox_recarga'])->format('d-m-Y') 
            : 'No hay recargas futuras por realizar';
        $recargasPendientes = count($recarga) > 1 ? '+1' : ''; // Muestra +1 si hay varias recargas
    @endphp

    <div class="col-sm-6 col-lg-4">
        <div class="card mb-4 text-white bg-danger">
            <div class="card-body">
                <div class="fs-4 fw-semibold">{{ $fechaProxRecarga }}</div>
                <div class="text-uppercase">{{ $empresa }}  {{ $recargasPendientes }}</div>
                <div class="progress progress-white progress-thin my-2">
                    <div class="progress-bar" role="progressbar" style="width: 100%" 
                         aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
                </div>
                <small class="text-medium-emphasis-inverse">Próxima recarga</small>
            </div>
        </div>
    </div>
</div>

<br>

@if (auth()->user()->rol == 1 || auth()->user()->rol == 2)
<div class="row">
    <div class="col-sm-12">
        <div class="card mb-4 bg-light">
            <div class="card-header">
                <h5 class="m-0">Próximas Recargas de Extintores</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover">
                        <thead class="thead-dark">
                            <tr>
                                <th>N°</th>
                                <th>CLIENTE</th>
                                <th>COD</th>
                                <th>CLASE</th>
                                <th>MARCA</th>
                                <th>FECHA PROX. RECARGA</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($recarga as $index => $re)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $re['razon_social'] }}</td>
                                <td>{{ $re['cod'] }}</td>
                                <td>{{ $re['clase'] }}</td>
                                <td>{{ $re['marca'] }}</td>
                                <td style="white-space: nowrap;">
                                    {{ \Carbon\Carbon::parse($re['fecha_prox_recarga'])->format('d-m-Y') }}
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center">No hay próximas recargas.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer text-muted text-center">
                Última actualización: {{ now()->format('d-m-Y H:i') }}
            </div>
        </div>
    </div>
</div>
@endif
