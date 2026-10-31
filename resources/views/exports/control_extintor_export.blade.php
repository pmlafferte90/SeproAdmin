<table>
    <thead>
        <!-- Fila combinada para el título principal -->
        <tr>
            <th colspan="17" style="text-align: center; font-weight: bold; font-size: 16px;">
                CONTROL DE EXTINTORES {{ $empresa['razon_social'] ?? '' }}
            </th>
        </tr>
        
        <!-- Fila con encabezados agrupados para "Accesorios" -->
        <tr>
            <th rowspan="2" style="text-align: center; font-weight: bold;">N°</th>
            <th rowspan="2" style="text-align: center; font-weight: bold;">COD</th>
            <th rowspan="2" style="text-align: center; font-weight: bold;">UBICACIÓN</th>
            <th rowspan="2" style="text-align: center; font-weight: bold;">CLASE</th>
            <th rowspan="2" style="text-align: center; font-weight: bold;">MARCA</th>
            <th rowspan="2" style="text-align: center; font-weight: bold;">PESO</th>
            <!-- Celda combinada para "Accesorios" que agrupa MANGO, MANO, SEGURO, CILINDRO -->
            <th colspan="4" style="text-align: center; font-weight: bold;">ACCESORIOS</th>
            <th rowspan="2" style="text-align: center; font-weight: bold;">FECHA RECARGA</th>
            <th rowspan="2" style="text-align: center; font-weight: bold;">FECHA PROX. RECARGA</th>
            <th rowspan="2" style="text-align: center; font-weight: bold;">AREA DE SEGURIDAD</th>
            <th rowspan="2" style="text-align: center; font-weight: bold;">INSTRUCTIVO</th>
            <th rowspan="2" style="text-align: center; font-weight: bold;">RECT. INSTALACION</th>
            <th rowspan="2" style="text-align: center; font-weight: bold;">OBSERVACIONES MARBETE</th>
            <th rowspan="2" style="text-align: center; font-weight: bold;">OBSERVACIONES</th>
        </tr>
        
        <!-- Fila de encabezados de las columnas individuales bajo "Accesorios" -->
        <tr>
            <th style="text-align: center; font-weight: bold;">MANGO</th>
            <th style="text-align: center; font-weight: bold;">MANO</th>
            <th style="text-align: center; font-weight: bold;">SEGURO</th>
            <th style="text-align: center; font-weight: bold;">CILINDRO</th>
        </tr>
    </thead>
    <tbody>
    @foreach ($controlExtintores as $ubicacion => $items)
            <!-- Primera columna combinada usando rowspan -->
            <tr><td colspan="17" style="text-align: center; font-weight: bold; background-color: #f2f2f2;">
                {{ $ubicacion }}
            </td></tr>
            @foreach ($items as $index => $item)
                <tr>
                    
                    <td>{{ $item['razon_social'] }}</td>
                    <td>{{ $item['id'] }}</td>
                    <td>{{ $item['cod'] }}</td>
                    <td>{{ $item['clase'] }}</td>
                    <td>{{ $item['marca'] }}</td>
                    <td>{{ $item['peso'] }}</td>
                    <td>{{ $item['mango'] }}</td>
                    <td>{{ $item['mano'] }}</td>
                    <td>{{ $item['seguro'] }}</td>
                    <td>{{ $item['cilindro'] }}</td>
                    <td>{{ $item['fecha_recarga'] }}</td>
                    <td>{{ $item['fecha_prox_recarga'] }}</td>
                    <td>{{ $item['area_seguridad'] }}</td>
                    <td>{{ $item['instructivo_uso'] }}</td>
                    <td>{{ $item['rectangulo_instalacion'] }}</td>
                    <td>{{ $item['novedad_marbete'] }}</td>
                </tr>
            @endforeach
        @endforeach
    </tbody>
</table>
