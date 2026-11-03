@php
\Carbon\Carbon::setLocale('es');
@endphp

<table style="font-family: sans-serif;">
    <!-- Fila para logo + nomenclatura -->
    <thead>
    <tr>
        <th style="text-align: center; font-weight: bold; font-size: 12px;">
        </th>
    </tr>
    </thead>
    <tbody>
    <tr>
        <!-- Columna vacía para separación -->
        <td colspan="2" style="font-weight: bold; width: 150px; height: 300px;">
        </td>
    </tr>
    <tr>

    </tr>
    </tbody>
</table>

<table>
    <thead>
    <tr>
        <th></th>
        <th colspan="8" style="text-align: center; font-weight: bold; font-size: 16px; background-color: #c4bd97">
            RESUMEN CONTROL DE EXTINTORES - {{ $empresa['razon_social'] ?? '' }}
        </th>
    </tr>
    <tr>
        <th></th>
        <th colspan="8" style="text-align:left; font-weight: bold; font-size: 12px; background-color: #D9D9D9; vertical-align: middle;">
            LOCALIDAD : {{ $localidad['nombre'] ?? '' }}
        </th>
    </tr>
    <tr>
        <th></th>
        <th style="width: 150px; text-transform: uppercase; text-align: center; vertical-align: middle; font-weight: bold; background-color: #19346A; color: white;">
            CÓDIGO
        </th>
        <th colspan="2" style="width: 300px; text-transform: uppercase; text-align: center; vertical-align: middle; font-weight: bold; background-color: #19346A; color: white;">
            UBICACIÓN
        </th>
        <th style="width: 200px; text-transform: uppercase; text-align: center; vertical-align: middle; font-weight: bold; background-color: #19346A; color: white;">
            FECHA REVISIÓN
        </th>
        <th style="width: 200px; text-transform: uppercase; text-align: center; vertical-align: middle; font-weight: bold; background-color: #19346A; color: white;">
            FECHA PRÓXIMA RECARGA
        </th>
        <th style="width: 200px; text-transform: uppercase; text-align: center; vertical-align: middle; font-weight: bold; background-color: #19346A; color: white;">
            CLASE
        </th>
        <th style="width: 200px; text-transform: uppercase; text-align: center; vertical-align: middle; font-weight: bold; background-color: #19346A; color: white;">
            PESO
        </th>
        <th style="width: 250px; text-transform: uppercase; text-align: center; vertical-align: middle; font-weight: bold; background-color: #19346A; color: white;">
            OBSERVACIONES
        </th>
    </tr>
    </thead>
    <tbody>
    @foreach ($rows as $row)
    <tr>
        @php
        $rev = \Carbon\Carbon::parse($row->fecha_revision);
        $prox = \Carbon\Carbon::parse($row->fecha_prox_recarga);
        @endphp
        <td></td>
        <td style="text-align:center; vertical-align: middle; word-wrap: break-word;">{{ $row->codigo }}</td>
        <td colspan="2" style="text-align:center; vertical-align: middle; word-wrap: break-word;">{{ $row->nombre_ubicacion }}</td>
        <td style="text-align:center; vertical-align: middle; word-wrap: break-word;">
            {{ ucfirst($rev->translatedFormat('F')) }} del {{ $rev->format('Y') }}
        </td>
        <td style="text-align:center; vertical-align: middle; word-wrap: break-word;">
            {{ ucfirst($prox->translatedFormat('F')) }} del {{ $prox->format('Y') }}
        </td>
        <td style="text-align:center; vertical-align: middle; word-wrap: break-word;">{{ $row->clase }}</td>
        <td style="text-align:center; vertical-align: middle; word-wrap: break-word;">
            {{ $row->peso . ' ' . $row->unidad }}
        </td>
        <td style="text-align:center; vertical-align: middle; word-wrap: break-word;">{{ $row->observaciones }}</td>
    </tr>
    @endforeach
    </tbody>
</table>