<div class="modal fade" id="areaModal" tabindex="-1" aria-labelledby="areaModalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="areaModalLabel">@{{ modalTitle }}</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form id="areaForm" onsubmit="event.preventDefault(); enviarFormularioArea();">
      @csrf
        <div class="modal-body">
        <input type="hidden" id="areaId" name="id"  value="{{ $area->id ?? '' }}">
        <div class="mb-3">
            <label for="nombre" class="form-label">Nombre</label>
            <input type="text" class="form-control" id="nombre" name="nombre" required>
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
