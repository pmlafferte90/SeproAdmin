   function validarEmailsMultiples(inputId) {
    const input = document.getElementById(inputId);
    const emails = input.value.split(',').map(e => e.trim()).filter(e => e !== '');
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

    if (emails.length === 0) return false;
    return emails.every(email => emailRegex.test(email));
  }



// Exportar la función globalmente
window.enviarFormularioRepresentante = async function enviarFormularioRepresentante() {
    const form = document.getElementById('representanteForm');

    /*if (!validarEmailsMultiples('email')) {
      showToast('Por favor, ingresa uno o más correos válidos separados por coma.', 'error');
      return;
    } */

    const formData = new FormData(form);
    const representanteId = formData.get('representanteId');
    const url = representanteId
      ? `/api/representante/actualizar/${representanteId}`
      : '/api/representante/crear';

    try {
      showLoading();

      const response = await fetch(url, {
        method: 'POST',
        body: formData,
        headers: {
          'X-CSRF-TOKEN': document
            .querySelector('meta[name="csrf-token"]')
            .getAttribute('content'),
        },
      });

      const text = await response.text();
      let jsonData;
      try {
        jsonData = JSON.parse(text);
      } catch {
        throw new Error('Respuesta inválida del servidor');
      }

      if (jsonData.ok) {
        showToast('Representante guardado correctamente', 'success');
        const modal = bootstrap.Modal.getInstance(
          document.getElementById('representanteModal')
        );
        modal.hide();
        document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());
        location.reload();
      } else {
        // Verificamos si hay errores específicos
        if (jsonData.errors) {
        const allErrors = Object.values(jsonData.errors).flat();
        allErrors.forEach(msg => showToast(msg, 'error'));
        } else {
            showToast(jsonData.error || 'Error al guardar el representante', 'error');
        }
      }
    } catch (err) {
      console.error(err);
      showToast(err.message || 'Error en la petición', 'error');
    } finally {
      hideLoading();
    }
};

// Función para editar un representante
window.editarRepresentante = function (id_representante) {
  const usuario_div = document.getElementById('usuario_div');
  const usuario_input = document.getElementById('usuario');
  const password_input = document.getElementById('password');
  password_input.removeAttribute('required');
  usuario_input.removeAttribute('required');
  usuario_div.style.display = 'none';

  showLoading();

  $.ajax({
    url: `/api/representante/${id_representante}`,
    method: 'GET',
    success(response) {
      if (response.success) {
        const rep = response.data;

        $('#representanteId').val(rep.id);
        $('#id_cliente').val(rep.id_cliente).prop('disabled', true);
        $('#ver_todas_localidades').prop('checked', !!rep.ver_todas_localidades); // ✅ set checkbox

        const localidadIds = rep.localidades?.map(loc => loc.id) || [];

        cargarLocalidadesPorCliente(rep.id_cliente, rep.ver_todas_localidades ? [] : localidadIds)
          .then(() => {
            // Desactivar selector si ver_todas está activo
            const localidadSelect = document.getElementById('id_localidad');
            localidadSelect.tomselect?.clear();

            if (rep.ver_todas_localidades) {
              localidadSelect.tomselect.disable();
            } else {
              localidadSelect.tomselect.setValue(localidadIds);
              localidadSelect.tomselect.enable();
            }

            // Setear otros campos
            $('#nombre').val(rep.nombre);
            $('#apellido').val(rep.apellido);
            $('#identificacion').val(rep.identificacion);
            $('#telefono').val(rep.telefono);
            //$('#email').val(rep.correo);
            // **Emails con TomSelect**
            const emailSelect = document.getElementById('emails');
            const tsEmails   = emailSelect.tomselect;

            // Limpiar tags existentes
            tsEmails.clearOptions();
            tsEmails.clear();

            // Si la API devuelve un array rep.emails = [{ correo: 'a@x.com' }, ...]
            if (Array.isArray(rep.emails)) {
                // Agregar cada correo como opción y tag
                rep.emails.forEach(e => {
                  tsEmails.addOption({ value: e.email, text: e.email });
                });
                // Seleccionar todos los valores
                tsEmails.setValue(rep.emails.map(e => e.email));
            }

            showToast('Datos del representante cargados', 'success');
            new bootstrap.Modal(document.getElementById('representanteModal')).show();
          })
          .catch(err => {
            console.error('Error cargando localidades:', err);
            showToast('No se pudieron cargar las localidades', 'error');
          })
          .finally(() => {
            hideLoading();
          });
      } else {
        hideLoading();
        showToast('Error al obtener datos', 'error');
      }
    },
    error() {
      hideLoading();
      showToast('Error en la petición', 'error');
    }
  });
};


// Función para mostrar el formulario para crear un nuevo representante
window.nuevoRepresentante = function () {
  const form = document.getElementById("representanteForm");
  form.reset();
  document.getElementById("id_cliente").disabled = false;
  document.getElementById("representanteId").value = "";

  // Mostrar campos de usuario y contraseña nuevamente
  const usuario_div = document.getElementById('usuario_div');
  const usuario_input = document.getElementById('usuario');
  const password_input = document.getElementById('password');
  usuario_input.setAttribute('required', true);
  password_input.setAttribute('required', true);
  usuario_div.style.display = "block";

  // Reiniciar checkbox "ver todas las localidades"
  const verTodasCheckbox = document.getElementById('ver_todas_localidades');
  verTodasCheckbox.checked = false;

  // Reiniciar select de localidades completamente
  const localidadSelect = document.getElementById('id_localidad');
  if (localidadSelect.tomselect) {
    localidadSelect.tomselect.destroy();
  }
  localidadSelect.innerHTML = ''; // quitar opciones anteriores

  const emailSelect = document.getElementById('emails');
  const tsEmails   = emailSelect.tomselect;

  // Limpiar tags existentes
  tsEmails.clearOptions();
  tsEmails.clear();

  // Inicializar vacío
  const newLocalidades = new TomSelect(localidadSelect, {
    placeholder: 'Seleccione una o más localidades',
    options: [],
    items: [],
    plugins: ['remove_button'],
    render: {
      option: (data, escape) => `<div>${escape(data.text)}</div>`,
      item: (data, escape) => `<div>${escape(data.text)}</div>`
    }
  });

  newLocalidades.enable();


  // Eliminar estilos visuales grises
  const wrapper = localidadSelect.tomselect.wrapper;
  wrapper.classList.remove('bg-light', 'text-muted');

  // Mostrar el modal
  const myModal = new bootstrap.Modal(document.getElementById("representanteModal"));
  myModal.show();
};

window.cambiarEstadoRepresentante = function (representanteId, estadoActual) {
  const nuevoEstado = estadoActual == 1 ? 0 : 1;
  const confirmMsg = estadoActual == 1
    ? '¿Desea inactivar este representante?'
    : '¿Desea reactivar este representante?';

  showConfirm(confirmMsg, async () => {
    try {
      showLoading();

      const res = await fetch(
        `/api/representante/actualizar/${representanteId}`,
        {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document
              .querySelector('meta[name="csrf-token"]')
              .getAttribute('content'),
          },
          body: JSON.stringify({ estado: nuevoEstado }),
        }
      );

      const json = await res.json();

      if (json.ok) {
        showToast('Representante actualizado', 'success');
        location.reload();
      } else {
        showToast(json.message || 'Error actualizando estado', 'error');
      }
    } catch (err) {
      console.error(err);
      showToast('Error en la petición', 'error');
    } finally {
      hideLoading();
    }
  });
};



async function cargarLocalidadesPorCliente(clienteId, seleccionadas = []) {
  const localidadSelect = document.getElementById('id_localidad');

  if (localidadSelect.tomselect) {
    localidadSelect.tomselect.destroy();
  }

  localidadSelect.innerHTML = '';
  localidadSelect.style.pointerEvents = 'none';

  try {
    showLoading();

    const res = await fetch(`/api/localidad/cliente/${clienteId}`);
    const json = await res.json();

    const opciones = json.data.map(loc => ({
      value: loc.id,
      text: loc.nombre
    }));

    new TomSelect(localidadSelect, {
      placeholder: 'Seleccione una o más localidades',
      options: opciones,
      items: seleccionadas,
      plugins: ['remove_button'],
      render: {
        option: (data, escape) => `<div>${escape(data.text)}</div>`,
        item: (data, escape) => `<div>${escape(data.text)}</div>`
      },
      onInitialize: () => {
        localidadSelect.style.pointerEvents = 'auto';
      }
    });
  } catch (err) {
    console.error('Error al cargar localidades:', err);
    showToast('No se pudieron cargar las localidades', 'error');
  } finally {
    hideLoading();
  }
}

document.addEventListener('DOMContentLoaded', () => {
  const clienteSelect = document.getElementById('id_cliente');
  if (!clienteSelect) return;

  clienteSelect.addEventListener('change', () => {
    const clienteId = clienteSelect.value;
    if (!clienteId) return;
    cargarLocalidadesPorCliente(clienteId)
      .catch(err => {
        console.error('Error cargando localidades:', err);
        showToast('No se pudieron cargar las localidades', 'error');
      });
  });

  if (clienteSelect.value) {
    cargarLocalidadesPorCliente(clienteSelect.value)
      .catch(() => {});
  }

   inicializarGeneradorUsername();

});

function inicializarGeneradorUsername() {
  const nombreInput = document.getElementById('nombre');
  const apellidoInput = document.getElementById('apellido');
  const usuarioInput = document.getElementById('usuario');

  /*async function generarUsername() {
    const nombre = nombreInput.value.trim().toLowerCase();
    const apellido = apellidoInput.value.trim().toLowerCase();

    if (nombre && apellido) {
      const baseUsername = `${nombre.charAt(0)}.${apellido}`.replace(/\s+/g, '');

      try {
        const res = await fetch(`/usuario/check/username?username=${encodeURIComponent(baseUsername)}`);
        const json = await res.json();

        if (json.success && json.username) {
          usuarioInput.value = json.username;
        } else {
          usuarioInput.value = baseUsername;
        }
      } catch (error) {
        console.error('Error verificando username:', error);
        usuarioInput.value = baseUsername;
      }
    }
  } */

  nombreInput.addEventListener('blur', () => {
      const nombre = nombreInput.value.trim();
      const apellido = apellidoInput.value.trim();
      const usuarioInput = document.getElementById('usuario');

      if (nombre && apellido && usuarioInput) {
        window.generarUsername(nombre, apellido, usuarioInput);
      }
    });

    apellidoInput.addEventListener('blur', () => {
      const nombre = nombreInput.value.trim();
      const apellido = apellidoInput.value.trim();
      const usuarioInput = document.getElementById('usuario');

      if (nombre && apellido && usuarioInput) {
        window.generarUsername(nombre, apellido, usuarioInput);
      }
    });
}

function inicializarSelectVacioLocalidad() {
  const localidadSelect = document.getElementById('id_localidad');

  if (!localidadSelect.tomselect) {
    new TomSelect(localidadSelect, {
      placeholder: 'Seleccione una o más localidades',
      plugins: ['remove_button'],
      items: [],
      options: [],
      render: {
        option: (data, escape) => `<div>${escape(data.text)}</div>`,
        item: (data, escape) => `<div>${escape(data.text)}</div>`
      }
    });
  }
}

document.addEventListener('DOMContentLoaded', () => {
    inicializarSelectVacioLocalidad();

    new TomSelect('#emails', {
      plugins: ['remove_button'],
      persist: false,
      createOnBlur: true,
      create: function(input) {
        // valida formato básico de correo
        const emailRegex = /^\S+@\S+\.\S+$/;
        if (!emailRegex.test(input)) {
          window.showToast(`Formato de correo inválido: ${input}`, 'error');
          return false;  // evita crear el tag
        }
        return { value: input, text: input };
      }
    });
});

document.getElementById('ver_todas_localidades').addEventListener('change', function () {
  const select = document.getElementById('id_localidad');
  const tom = select.tomselect;

  if (!tom) return; // si aún no está inicializado

  const wrapper = tom.wrapper; // acceso seguro al contenedor de Tom Select

  if (this.checked) {
    tom.clear();
    tom.disable();

    wrapper.classList.add('bg-light', 'text-muted');
  } else {
    tom.enable();

    wrapper.classList.remove('bg-light', 'text-muted');
  }
});

