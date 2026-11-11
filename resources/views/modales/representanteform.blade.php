<div class="modal fade" id="representanteModal" tabindex="-1" aria-labelledby="representanteModalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="representanteModalLabel">Representante</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form id="representanteForm" enctype="multipart/form-data" onsubmit="event.preventDefault(); enviarFormularioRepresentante();">
      @csrf
        <div class="modal-body">
          <input type="hidden" id="representanteId" name="representanteId" value="{{ $representante->id ?? '' }}">

          <!-- Combo box para empresas activas -->
          <div class="form-group mb-3">
            <label for="cliente">Empresa:</label>
            <select class="form-control shadow-sm" id="id_cliente" name="id_cliente" required>
              <option value="">Seleccione un Cliente</option>
              @foreach($clientes as $cliente)
                <option value="{{ $cliente->id }}">{{ $cliente->razon_social }}</option>
              @endforeach
            </select>
          </div>

          <div class="form-group mb-3">
            <div class="form-check mb-3">
              <input class="form-check-input" type="checkbox" value="1" id="ver_todas_localidades" name="ver_todas_localidades">
              <label class="form-check-label" for="ver_todas_localidades">
                Asignar todas las localidades
              </label>
            </div>
            <label for="id_localidad">Localidad:</label>
            <select class="form-select shadow-sm tom-select" id="id_localidad" name="id_localidad[]" multiple required></select>
          </div>

          <div class="mb-3">
            <label for="nombre" class="form-label">Nombre</label>
            <input type="text" class="form-control shadow-sm" id="nombre" name="nombre" required>
          </div>
          <div class="mb-3">
            <label for="apellido" class="form-label">Apellido</label>
            <input type="text" class="form-control shadow-sm" id="apellido" name="apellido" required>
          </div>
          <div class="mb-3">
            <label for="identificacion" class="form-label">Identificación</label>
            <input type="text" class="form-control shadow-sm" id="identificacion" name="identificacion" required minlength="10" maxlength="10"
              inputmode="numeric" pattern="\d{10}" title="El número de identificación debe contener exactamente 10 dígitos."
              oninput="this.value = this.value.replace(/[^0-9]/g, '');">
          </div>
          <div class="mb-3">
            <label for="telefono" class="form-label">Teléfono</label>
            <input type="text" class="form-control shadow-sm" id="telefono" name="telefono" required maxlength="10"
              inputmode="numeric" pattern="[0-9]{10}" title="El teléfono debe contener 10 dígitos."
              oninput="this.value = this.value.replace(/[^0-9]/g, '');">
          </div>
          <!--div class="mb-3">
            <label for="correo" class="form-label">Correo</label>
            <input type="text" class="form-control shadow-sm" id="email" name="email" required
              placeholder="ej: correo1@ejemplo.com, correo2@ejemplo.com"
              title="Ingrese uno o más correos electrónicos separados por coma.">
          </div-->
          <div class="mb-3">
              <label for="emails" class="form-label">Correos</label>
              <select
                id="emails"
                name="emails[]"
                multiple
                class="form-control shadow-sm"
                placeholder="Escribe un correo y presiona Enter"
              ></select>
            </div>
          <div class="mb-3" id="usuario_div">
            <label for="usuario" class="form-label">Nombre de Usuario</label>
            <input
              type="text"
              class="form-control shadow-sm"
              id="usuario"
              name="usuario"
              readonly
              required
            >

            <label for="password" class="form-label mt-3">Contraseña</label>
            <input
              type="password"
              class="form-control shadow-sm"
              id="password"
              name="password"
              required
            >
          </div>
        </div>
         @include('componentes.modal-footer', [
                'closeText'  => 'Cerrar',
                'submitText' => 'Guardar', 
                'submitId'   => 'submit_control'
                ])
      </form>
    </div>
  </div>
</div>
