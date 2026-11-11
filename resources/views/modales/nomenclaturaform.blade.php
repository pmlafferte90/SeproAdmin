<div class="modal fade" id="nomenclaturaModal" tabindex="-1" aria-labelledby="nomenclaturaModalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="nomenclaturaModalLabel">@{{ modalTitle }}</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form id="nomenclaturaForm" onsubmit="event.preventDefault(); enviarFormularioNomenclatura();">
      @csrf
        <div class="modal-body">
        <input type="hidden" id="nomenclaturaId" name="nomenclaturaId"  value="{{ $nomenclatura->id ?? '' }}">
         <!-- Combo box para áreas activas -->
         <div class="form-group mb-3">
            <label for="tipo" class="form-label">Tipo:</label>
            <select class="form-control" id="id_tipo" name="id_tipo" required>
                <option value="">Seleccione un tipo</option>
                @foreach($tipoNomenclatura as $tipo)
                    <option value="{{ $tipo->id }}">{{ $tipo->descripcion }}</option>
                @endforeach
            </select>
        </div>
        <div class="mb-3">
            <label for="descripcion" class="form-label">Descripción</label>
            <input type="text" class="form-control" id="descripcion" name="descripcion" required>
          </div>
          <div class="mb-3">
            <label for="abreviatura" class="form-label">Abreviatura</label>
            <input type="text" class="form-control" id="abreviatura" name="abreviatura" required>
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
