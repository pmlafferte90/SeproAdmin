<div class="modal fade" id="global-confirm-modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded">
            <!-- Header con color corporativo -->
            <div class="modal-header" style="background-color: var(--sepro); color: #fff;">
                <i class="bi bi-question-circle-fill me-2 fs-4"></i>
                <h5 class="modal-title fw-bold mb-0">Confirmar acción</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>

            <!-- Cuerpo blanco -->
            <div class="modal-body text-center" style="background-color: #ffffff;">
                <p id="global-confirm-message" class="mb-0 fs-5 text-dark">
                    ¿Está seguro que desea realizar esta acción?
                </p>
            </div>

            <!-- Footer con botones personalizados -->
            <div class="modal-footer justify-content-center border-0 pt-0 pb-4" style="background-color: #ffffff;">
                <button type="button" class="btn btn-outline-secondary px-4" id="global-confirm-cancel" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn text-white px-4" id="global-confirm-accept"
                        style="background-color: var(--sepro);">
                    Aceptar
                </button>
            </div>
        </div>
    </div>
</div>
