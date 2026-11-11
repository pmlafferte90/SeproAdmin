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
        <td colspan="2" style="font-weight: bold; width: 150px; height: 330px;">
        </td>
    </tr>
    <tr>

    </tr>
    </tbody>
</table>

<table>
    @php
        $filtered = array_diff($selected, ['area_nombre', 'observaciones', 'auditor']);
        $colspan = count($filtered) + 1;
    @endphp
    <thead>
    <tr>
        <th colspan="{{ $colspan }}"
            style="text-align: center;
            vertical-align: middle;
            word-wrap: break-word;
            font-weight: bold;
            font-size: 16px;
            background-color: #c4bd97;
            @if($colspan <= 9)
            height: 100px;
            @elseif($colspan >= 10 && $colspan <= 13)
                height: 60px;
            @endif
            "
             >
            CONTROL DE EXTINTORES - {{ $empresa['razon_social'] ?? '' }}
        </th>
    </tr>

    <tr style="background-color: #F2F2F2;">
        <th rowspan="2" style="width: 50px; text-align: center; vertical-align: middle; font-weight: bold; background-color: #F2F2F2;">N°</th>
        @php
            $accesoriosKeys = ['manguera', 'manometro', 'seguro', 'cilindro'];
            $showAccesorios = count(array_intersect($filtered, $accesoriosKeys)) > 0;
            $alreadyShownAccesorios = false;
        @endphp
        @foreach($filtered as $key)
            @php
                if (in_array($key, $accesoriosKeys)) {
                    if ($alreadyShownAccesorios) continue;
                    $alreadyShownAccesorios = true;
                    $label = 'ACCESORIOS';
                    $colspanHeader = count($accesoriosKeys);
                    $rowspan = 1;
                    $style = 'text-align: center; vertical-align: middle; font-weight: bold; background-color: #F2F2F2;';
                } else {
                    $label = $headersMap[$key]['label'] ?? $key;
                    $colspanHeader = 1;
                    $rowspan = 2;
                    $style = $headersMap[$key]['style'] ?? '';
                }
            @endphp
            <th style="{{ $style }}" colspan="{{ $colspanHeader }}" rowspan="{{ $rowspan }}">
                {{ $label }}
            </th>
        @endforeach
    </tr>
    <tr>
        <th style="height: 35px; text-align: center; vertical-align: middle; font-weight: bold; background-color: #F2F2F2; width: 65px; word-wrap: break-word;">MANGUERA</th>
        <th style="height: 35px; text-align: center; vertical-align: middle; font-weight: bold; background-color: #F2F2F2; width: 65px; word-wrap: break-word;">MANÓMETRO</th>
        <th style="height: 35px; text-align: center; vertical-align: middle; font-weight: bold; background-color: #F2F2F2; width: 65px; word-wrap: break-word;">SEGURO</th>
        <th style="height: 35px; text-align: center; vertical-align: middle; font-weight: bold; background-color: #F2F2F2; width: 70px; word-wrap: break-word;">CILINDRO</th>
    </tr>
    </thead>
    <tbody>
    @php $itemNumber = 1; @endphp
    @foreach ($controlExtintores as $area => $items)
    <tr>
        <td colspan="1" style="background-color: #D9D9D9;"></td>
        <td colspan="{{ $colspan - 1 }}" style="text-align:left; font-weight: bold; font-size: 12px; background-color: #D9D9D9; vertical-align: middle;">
            {{ $area }}
        </td>
    </tr>
    @foreach ($items as $index => $item)
    <tr>
        <td style="text-align:center; vertical-align: middle;">{{ $itemNumber++ }}</td>
        @foreach($filtered as $key)

            @php
                // Caso especial para la nomenclatura
                if ($key === 'observaciones_nomen') {
                    // Pluck de la relación y implode, o guión si viene vacío
                    $value = $item->observacionesNomenclatura
                        ->pluck('abreviatura')
                        ->implode(', ') ?: '-';
                } elseif ($key === 'peso') {
                    $value = intval($item->peso) .' '. $item->unidad;
                } elseif ($key === 'manguera') {
                    $value = $item->mango;
                } elseif ($key === 'manometro') {
                    $value = $item->mano;
                } elseif ($key === 'instructivo_clasif') {
                    $value = $item->instructivo_clasificacion;
                } elseif ($key === 'rect_instalacion') {
                    $value = $item->rectangulo_instalacion;
                } elseif ($key === 'fecha_prueba_hidros') {
                    $value = $item->fecha_prueba_hidrostatica;
                } else {
                    // Por defecto tomamos la propiedad o '-'
                    $value = data_get($item, $key, '-');
                    // Si es fecha, aplicamos formateo
                    if (
                        in_array($key, ['fecha_recarga','fecha_prox_recarga']) &&
                        $value !== '-'
                    ) {
                        try {
                            \Carbon\Carbon::setLocale('es');
                            $fecha = \Carbon\Carbon::parse($value);
                            $value = ucfirst($fecha->translatedFormat('F'))
                                   . ' - '
                                   . $fecha->format('Y');
                        } catch (\Exception $e) {
                            // leave as is
                        }
                    }
                }
            @endphp

            <td style="text-align:center; vertical-align: middle; word-wrap: break-word;">
                {{ $value }}
            </td>
        @endforeach
    </tr>
    @endforeach
    @endforeach
    </tbody>
</table>