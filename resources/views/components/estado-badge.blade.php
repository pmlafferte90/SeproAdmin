@props(['estado'])

@php
    $clase = $estado == 1 ? 'bg-success' : 'bg-danger';
    $texto = $estado == 1 ? 'Activo' : 'Inactivo';
@endphp

<span class='badge {{ $clase }} fs-7'>{{ $texto }}</span>
