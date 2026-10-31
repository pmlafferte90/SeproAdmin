<div class="modal fade" id="usuarioModal" tabindex="-1" aria-labelledby="usuarioModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="usuarioModalLabel">@{{ modalTitle }}</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form id="usuarioForm" onsubmit="event.preventDefault(); enviarFormulariousuario();">
      @csrf
        <div class="modal-body">
        <input type="hidden" id="usuarioId" name="usuarioId"  value="{{ $usuario->id ?? '' }}">
         <!-- Combo box para áreas activas -->
         <div class="form-group">
            <label for="rol">Rol:</label>
            <select class="form-control" id="rol_id" name="rol_id">
                <option value="">Seleccione un rol</option>
                @foreach($roles as $rol)
                    <option value="{{ $rol->id }}">{{ $rol->rol }}</option>
                @endforeach
            </select>
        </div>
        <div class="mb-3">
            <label for="usuario" class="form-label">Usuario</label>
            <input type="text" class="form-control" id="usuario" name="usuario" required>
          </div>
          <div class="mb-3">
            <label for="password" class="form-label">Contraseña</label>
            <input type="text" class="form-control" id="password" name="password" required>
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
