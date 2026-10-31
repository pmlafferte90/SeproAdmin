<div class="modal fade" id="tipoNomenclaturaModal" tabindex="-1" aria-labelledby="tipoNomenclaturaModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="tipoNomenclaturaModalLabel">@{{ modalTitle }}</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form id="tipoNomenclaturaForm" onsubmit="event.preventDefault(); enviarFormularioTipoNomenclatura();">
      @csrf
        <div class="modal-body">
        <input type="hidden" id="tipoNomenclaturaId" name="id"  value="{{ $tipoNomenclatura->id ?? '' }}">
        <div class="mb-3">
            <label for="descripcion" class="form-label">Descripción</label>
            <input type="text" class="form-control" id="descripcion" name="descripcion" required>
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
