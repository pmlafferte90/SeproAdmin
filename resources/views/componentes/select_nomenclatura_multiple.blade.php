@props(['label', 'name', 'tipoFiltro'])

<div>
    <label for="{{ $name }}" class="form-label fw-bold mb-1">{{ $label }}:</label>
    <select name="{{ $name }}[]" id="{{ $name }}" multiple class="form-select shadow-sm tom-select" data-tipo="{{ $tipoFiltro }}">
        @foreach($nomenclaturas as $tipo)
            @if($tipo->descripcion === $tipoFiltro)
                <optgroup>
                    @foreach($tipo->nomenclaturas as $nomenclatura)
                        <option value="{{ $nomenclatura->id }}">{{ $nomenclatura->descripcion }}</option>
                    @endforeach
                </optgroup>
            @endif
        @endforeach
    </select>
</div>
