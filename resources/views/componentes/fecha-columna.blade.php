@props(['fecha'])

<td style="white-space: nowrap;">
    {{ $fecha && $fecha !== '0000-00-00'
        ? \Carbon\Carbon::parse($fecha)->format('d-m-Y')
        : '-' }}
</td>
