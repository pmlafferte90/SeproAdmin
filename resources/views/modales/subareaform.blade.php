<div class="modal fade" id="subareaModal" tabindex="-1" aria-labelledby="subareaModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="subareaModalLabel">@{{ modalTitle }}</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form id="subareaForm" onsubmit="event.preventDefault(); enviarFormularioSubArea();">
      @csrf
        <div class="modal-body">
        <input type="hidden" id="subareaId" name="subareaId"  value="{{ $subarea->id ?? '' }}">
         <!-- Combo box para áreas activas -->
         <div class="form-group mb-3">
            <label for="area">Área:</label>
            <select class="form-control shadow-sm" id="area_id" name="area_id" required>
                <option value="">Seleccione un área</option>
                @foreach($areas as $area)
                    <option value="{{ $area->id }}">{{ $area->nombre }}</option>
                @endforeach
            </select>
        </div>
        <div class="mb-3">
            <label for="nombre" class="form-label">Nombre</label>
            <input type="text" class="form-control shadow-sm" id="nombre" name="nombre" required>
          </div>
          <div class="mb-3">
            <label for="codigo" class="form-label">Código</label>
            <input type="text" class="form-control shadow-sm" id="codigo" name="codigo" required>
          </div>
          <div class="mb-3">
            <label for="clase" class="form-label">Clase</label>
            <input type="text" class="form-control shadow-sm" id="clase" name="clase" required>
          </div>
          <div class="mb-3">
            <label for="marca" class="form-label">Marca</label>
            <input type="text" class="form-control shadow-sm" id="marca" name="marca" required>
          </div>
          <div class="mb-3">
              <label for="peso_valor" class="form-label fw-bold">Peso:</label>
              <div class="input-group">
                  <input type="number" step="0.01" min="0" class="form-control" id="peso" name="peso" placeholder="Ej: 10" required>
                  <select class="form-select shadow-sm" id="unidad" name="unidad" required>
                    <option value="KG">KG</option>
                    <option value="G">G</option>
                    <option value="L">L</option>
                    <option value="LB">LB</option>
                    <option value="GAL">Gal</option>
                    <option value="M3">m3</option>
                    <option value="OZ">OZ</option>
                  </select>
              </div>
          </div>
        </div>
         @include('componentes.modal-footer', [
                'closeText'  => 'Cerrar',
                'submitText' => 'Guardar', 
                'submitId'   => 'submitButton'
                ])
      </form>
    </div>
  </div>
</div>
