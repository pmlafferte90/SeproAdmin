<div class="modal fade" id="pesoModal" tabindex="-1" aria-labelledby="pesoModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="pesoModalLabel">@{{ modalTitle }}</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form id="pesoForm" onsubmit="event.preventDefault(); enviarFormularioPeso();">
      @csrf
        <div class="modal-body">
          <input type="hidden" id="pesoId" name="id"  value="{{ $peso->id ?? '' }}">
          <div class="mb-3">
              <label for="peso" class="form-label">Peso</label>
              <div class="input-group">
              <input type="number" class="form-control" id="peso" name="peso" min="0" inputmode="numeric" required onchange="validaCampoPeso()">
              <span class="input-group-text">Lbs</span>
              </div>
          </div>
          <span id="peso-error" style="color:red; display:none;">Ingrese un peso válido</span>

        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
          <button type="submit" class="btn btn-primary">@{{ modalButton }}</button>
        </div>
      </form>
    </div>
  </div>
</div>
