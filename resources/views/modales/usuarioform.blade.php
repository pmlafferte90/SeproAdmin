<div class="modal fade" id="usuarioModal" tabindex="-1" aria-labelledby="usuarioModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="usuarioModalLabel">@{{ modalTitle }}</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
          <form id="usuarioForm" onsubmit="event.preventDefault(); enviarFormulariousuario();">
          @csrf
          <input type="hidden" id="usuarioId" name="usuarioId" value="{{ $usuario->id ?? '' }}">

          <div class="modal-body">

            <div class="form-group">
              <label for="rol_id">Rol:</label>
              <select class="form-control" id="rol_id" name="rol_id" required>
                <option value="">Seleccione un rol</option>
                @foreach($roles as $rol)
                  <option value="{{ $rol->id }}">{{ $rol->name }}</option>
                @endforeach
              </select>
            </div>
        
            <div class="form-group mt-2">
              <label for="nombre">Nombre</label>
              <input type="text" class="form-control" id="nombre" name="nombre" required>
            </div>

            <div class="form-group mt-2">
              <label for="apellido">Apellido</label>
              <input type="text" class="form-control" id="apellido" name="apellido" required>
            </div>

            <div class="form-group mt-2">
              <label for="email">Correo electrónico</label>
              <input type="email" class="form-control" id="email" name="email" required>
            </div>

            <div class="form-group mt-2">
              <label for="usuario">Usuario</label>
              <input type="text" class="form-control" id="usuario" name="usuario" required readonly>
            </div>

            <div class="form-check mt-2" id="resetPasswordWrapper" style="display: none;">
              <input type="checkbox" class="form-check-input" id="resetPasswordCheckbox" onclick="togglePasswordField()" />
              <label class="form-check-label" for="resetPasswordCheckbox">Restablecer contraseña</label>
            </div>

            <div class="form-group mt-2">
              <label for="password">Contraseña</label>
              <input type="password" class="form-control" id="password" name="password" required minlength="8">
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
