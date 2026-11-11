document.addEventListener("DOMContentLoaded", function () {
    // Función para abrir modal de nueva localidad
    window.nuevaLocalidad = function (id_cliente) {
        document.getElementById("localidadForm").reset();
        document.getElementById("localidadId").value = "";
        document.getElementById("id_cliente").value = id_cliente;
        document.getElementById("localidadModalLabel").textContent = "Nueva Localidad";
        document.getElementById("submitLocalidad").textContent = "Guardar";

        const myModal = new bootstrap.Modal(document.getElementById("localidadModal"));
        myModal.show();
    }

    // Función para abrir modal de editar localidad
    window.editarLocalidad = function (id_localidad) {
        showLoading();
        $.ajax({
            url: '/api/localidad/' + id_localidad,
            method: 'GET',
            success: function (response) {
                if (response.success) {
                    let localidad = response.data;
                    $('#localidadId').val(localidad.id);
                    $('#id_cliente').val(localidad.id_cliente);
                    $('#nombre_localidad').val(localidad.nombre);
                    $('#direccion_localidad').val(localidad.direccion);
                    $('#ubicacion_localidad').val(localidad.ubicacion);

                    document.getElementById("localidadModalLabel").textContent = "Editar Localidad";
                    document.getElementById("submitLocalidad").textContent = "Actualizar";

                    const myModal = new bootstrap.Modal(document.getElementById("localidadModal"));
                    myModal.show();
                } else {
                    showToast('Error al obtener datos de la localidad', 'error');
                }
            },
            error: function () {
                showToast('Error en la petición al servidor', 'error');
            },
            complete: function () {
                hideLoading();
            }
        });
    }

    // Función para cambiar el estado de la localidad
    window.eliminarLocalidad = function (localidadId, estadoActual) {
        const nuevoEstado = estadoActual === '1' ? '0' : '1';
        const mensaje = estadoActual === '1'
            ? '¿Está seguro que desea inactivar la localidad?'
            : '¿Está seguro que desea reactivar la localidad?';

        showConfirm(mensaje, async () => {
            showLoading();
            try {
                const response = await fetch(`/api/localidad/cambiar_estado/${localidadId}`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ estado: nuevoEstado })
                });

                const result = await response.json();

                if (result.success) {
                    showToast(result.message, 'success', () => location.reload());
                } else {
                    showToast(result.error || 'Error al cambiar el estado de la localidad', 'error');
                }
            } catch (err) {
                console.error(err);
                showToast('Error de conexión con el servidor', 'error');
            } finally {
                hideLoading();
            }
        });
    }

    // Enviar formulario (crear o actualizar localidad)
    let localidadForm = document.getElementById("localidadForm");
    if (!localidadForm) return;

    localidadForm.addEventListener("submit", function (e) {
        e.preventDefault();

        showLoading();

        const id_localidad = document.getElementById("localidadId").value;
        const formData = new FormData(this);
        const url = id_localidad ? `/api/localidad/actualizar/${id_localidad}` : '/api/localidad/crear';
        const method = 'POST';

        fetch(url, {
            method: method,
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
            .then(response => response.json())
            .then(data => {
                if (data.ok) {
                    showToast(id_localidad ? "Localidad actualizada" : "Localidad creada", 'success', () => location.reload());
                } else {
                    showToast("Hubo un error al guardar la localidad. " + (data.error || ""), 'error');
                }
            })
            .catch(error => {
                console.error("Error:", error);
                showToast("Error en la conexión al servidor", "error");
            })
            .finally(() => {
                hideLoading();
            });
    });
});