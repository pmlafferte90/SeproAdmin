<div class="modal fade" id="localidadModal" tabindex="-1" aria-labelledby="localidadModalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="localidadModalLabel">Nueva Localidad</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="localidadForm">
                    <input type="hidden" id="localidadId" name="localidadId">
                    <input type="hidden" id="id_cliente" name="id_cliente">

                    <div class="mb-3">
                        <label for="nombre_localidad" class="form-label">Nombre</label>
                        <input type="text" class="form-control" id="nombre_localidad" name="nombre_localidad" required>
                    </div>
                    <div class="mb-3">
                        <label for="direccion_localidad" class="form-label">Dirección</label>
                        <input type="text" class="form-control" id="direccion_localidad" name="direccion_localidad" required>
                    </div>
                    <div class="mb-3">
                        <label for="ubicacion_localidad" class="form-label">Ubicación (URL del mapa)</label>
                        <input type="text" class="form-control" id="ubicacion_localidad" name="ubicacion_localidad">
                    </div>

                    <button type="submit" class="btn btn-primary" id="submitLocalidad">Guardar</button>
                </form>
            </div>
        </div>
    </div>
</div>
