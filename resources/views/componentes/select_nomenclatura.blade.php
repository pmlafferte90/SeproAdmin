@props(['label', 'name', 'tipoFiltro'])

<div>
    <label for="{{ $name }}" class="form-label fw-bold mb-1">{{ $label }}:</label>
    <select name="{{ $name }}" id="{{ $name }}" class="form-select shadow-sm" {{ isset($required) && $required ? 'required' : '' }}>
        <option value="" selected >Seleccione una opción</option>
        @foreach($nomenclaturas as $tipo)
            @if($tipo->descripcion === $tipoFiltro)
                <!--optgroup label="{{ $tipo->descripcion }}"-->
                    @foreach($tipo->nomenclaturas as $nomenclatura)
                        <option value="{{ $nomenclatura->id }}">{{ $nomenclatura->descripcion }}</option>
                    @endforeach
                <!--/optgroup-->
            @endif
        @endforeach
    </select>
</div>
