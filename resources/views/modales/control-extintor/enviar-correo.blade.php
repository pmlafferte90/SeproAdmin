<div class="modal fade"
    id="modalEnviarCorreo"
    tabindex="-1"
    aria-labelledby="modalEnviarCorreoLabel"
    aria-hidden="true"
    data-bs-backdrop="static"
    data-bs-keyboard="false"
    >
  <div class="modal-dialog">
    <div class="modal-content border-0 shadow-lg">
      <form id="formEnviarCorreo">
        <div class="modal-header bg-warning text-white">
          <h5 class="modal-title" id="modalEnviarCorreoLabel">Enviar Reporte por Correo</h5>
          <button type="button" class="btn-close" onclick="modalHelper.close('modalEnviarCorreo')"></button>
        </div>
        <div class="modal-body">
          <input type="hidden" id="id_cliente" value="{{ $id_cliente }}">
          <input type="hidden" id="nombreCliente" value="{{ $nombreCliente }}">
          <input type="hidden" id="nombreLocalidad" value="{{ $nombreLocalidad }}">
          <label class="form-label fw-semibold">Correos electrónicos (separados por coma):</label>
          <textarea class="form-control" name="emails" id="emails" rows="3" placeholder="ejemplo@correo.com, otro@correo.com">{{ implode(', ', $emails) }}</textarea>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" onclick="modalHelper.close('modalEnviarCorreo')">Cancelar</button>
          <button type="submit" class="btn btn-success">
            <i class="fa fa-paper-plane me-1"></i> Enviar
          </button>
        </div>
      </form>
    </div>
  </div>
</div>
