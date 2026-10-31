<div class="modal fade" id="claseModal" tabindex="-1" aria-labelledby="claseModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="claseModalLabel">@{{ modalTitle }}</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form id="claseForm" onsubmit="event.preventDefault(); enviarFormularioClase();">
      @csrf
        <div class="modal-body">
        <input type="hidden" id="claseId" name="id"  value="{{ $clase->id ?? '' }}">
        <div class="mb-3">
            <label for="clase" class="form-label">Clase</label>
            <input type="text" class="form-control" id="clase" name="clase" required>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
          <button type="submit" class="btn btn-primary">@{{ modalButton }}</button>
        </div>
      </form>
    </div>
  </div>
</div>
