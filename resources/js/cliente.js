document.addEventListener("DOMContentLoaded", function () {
  const clientForm = document.getElementById("clientForm");
  if (!clientForm) return;

  clientForm.addEventListener("submit", async function (e) {
    e.preventDefault();

    showLoading();
    try {
      const id = document.getElementById("clientId").value;
      const formData = new FormData(this);

      // Verificar empresa
      const empresaSelect = document.getElementById("empresa");
      if (empresaSelect.value === "") {
        showToast("Debe seleccionar una empresa.", "error");
        empresaSelect.focus();
        return;
      }

      // Solo en creación: validar y recopilar localidades
      if (!id) {
        const localidadTable = document.querySelector("#localidadTable tbody");
        const rows = localidadTable.querySelectorAll("tr");
        if (rows.length === 0) {
          showToast("Debe agregar al menos una localidad.", "error");
          return;
        }

        const localidades = [];
        for (let row of rows) {
          const inputs = row.getElementsByTagName("input");
          const [nombre, direccion, ubicacion] = [...inputs].map(i => i.value.trim());
          if (!nombre || !direccion || !ubicacion) {
            showToast("Todos los campos de localidad deben estar llenos.", "error");
            return;
          }
          localidades.push({ nombrelocalidad: nombre, direccion, ubicacion });
        }

        formData.append("localidades", JSON.stringify(localidades));
      }

      const url = id
        ? `/api/cliente/actualizar_cliente/${id}`
        : "/api/cliente/crear_cliente";

      const response = await fetch(url, {
        method: "POST",
        body: formData,
        headers: {
          "X-CSRF-TOKEN": document.querySelector('input[name="_token"]').value
        }
      });
      const data = await response.json();

      if (data.ok) {
        showToast("Cliente guardado correctamente", "success");
        // Cerrar modal
        const modal = bootstrap.Modal.getInstance(
          document.getElementById("clientModal")
        );
        modal.hide();
        // Recarga
        location.reload();
      } else {
        showToast(data.error || "Error al guardar el cliente", "error");
      }
    } catch (err) {
      console.error("Error en enviarFormularioCliente:", err);
      showToast("Error en la petición al servidor", "error");
    } finally {
      hideLoading();
    }
  });
});

// Función para editar un cliente
window.editarCliente = function (id_cliente) {
  showLoading();

  $.ajax({
    url: `/api/cliente/${id_cliente}`, // Ruta para obtener el cliente
    method: 'GET',
    beforeSend() {
      // Opcional: deshabilitar botones mientras carga
      $('#submitButton').prop('disabled', true);
    },
    success(response) {
      if (response.success) {
        const cliente = response.data;
        $('#clientId').val(cliente.id);
        $('#razon_social').val(cliente.razon_social);
        $('#ruc').val(cliente.ruc);
        $('#telefono').val(cliente.telefono);
        $('#correo').val(cliente.correo);
        $('#empresa').val(cliente.emp_origen).trigger('change');

        document.getElementById("submitButton").textContent = "Editar";

        showToast('Datos del cliente cargados', 'success');
        new bootstrap.Modal(document.getElementById('clientModal')).show();
      } else {
        showToast('Error al obtener datos del cliente', 'error');
      }
    },
    error() {
      showToast('Error en la petición al servidor', 'error');
    },
    complete() {
      hideLoading();
      $('#submitButton').prop('disabled', false);
    }
  });
};


// Función para mostrar el formulario para crear un nuevo cliente
window.nuevoCliente = function (id_cliente) {
    document.getElementById("clientForm").reset();
    document.getElementById("clientId").value = "";
    document.getElementById("submitButton").textContent = "Guardar";

    // Mostrar el modal vacío para nuevo cliente
    var myModal = new bootstrap.Modal(document.getElementById("clientModal"));
    myModal.show();
}

window.cambiarEstadoCliente = function (clienteId, estadoActual) {
    const accion = estadoActual === 1 ? 'inactivar' : 'restaurar';
    const nuevoEstado = estadoActual === 1 ? 0 : 1;
    const mensaje = estadoActual === 1
        ? "¿Está seguro que desea inactivar el cliente?"
        : "¿Está seguro que desea restaurar el cliente?";

    showConfirm(mensaje, () => {
        showLoading();

        $.ajax({
            url: `/api/cliente/cambiar_estado/${clienteId}`,
            type: 'PUT',
            data: {
                _token: $('meta[name="csrf-token"]').attr('content'),
                estado: nuevoEstado
            },
            success: function (response) {
                hideLoading();
                showToast(response.message, "success", () => location.reload());
            },
            error: function () {
                hideLoading();
                showToast(`Hubo un error al intentar ${accion} el cliente.`, "error");
            }
        });
    });
}



window.irControlExtintorCliente = async function(clienteId, localidadId) {
  showLoading();
  try {

    const res = await fetch(`/api/areas_empresa/existe/${localidadId}`);
    const json = await res.json();
    if (!json.ok) {
      showToast(
        'No existe una relación entre subárea y localidad. Por favor, regístrela primero en el módulo "Área Empresa".',
        'error'
      );
      return;
    }

    window.location.href = `/control_extintor_cliente/${clienteId}?localidad=${localidadId}`;

  } catch (err) {
    console.error('Error consultando áreas:', err);
    showToast('Error al consultar las áreas de la empresa', 'error');
  } finally {
    hideLoading();
  }
};
