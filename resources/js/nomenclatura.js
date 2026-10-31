console.log("entro a nomenclatura js");

function enviarFormularioNomenclatura() {
    showLoading();

    try {
        const form = document.getElementById('nomenclaturaForm');
        const formData = new FormData(form);

        const nomenclaturaId = formData.get('nomenclaturaId');
        const url = nomenclaturaId ? `/nomenclatura/actualizar/${nomenclaturaId}` : '/nomenclatura/crear';
        const method = 'POST';

        console.log("Form Data:");
        formData.forEach((value, key) => {
            console.log(key, value);
        });

        fetch(url, {
            method: method,
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => {
            console.log('Raw response:', response);
            return response.text();
        })
        .then(data => {
            console.log('Response text:', data);
            try {
                const jsonData = JSON.parse(data);

                if (jsonData.success) {
                    showToast("Nomenclatura guardada exitosamente", "success");

                    const modal = bootstrap.Modal.getInstance(document.getElementById('nomenclaturaModal'));
                    modal.hide();
                    document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());

                    location.reload();
                } else {
                    throw new Error(jsonData.error || 'Error al guardar la nomenclatura');
                }
            } catch (error) {
                console.error('Error parsing JSON:', error);
                showToast("Respuesta inválida del servidor", "error");
            }
        })
        .catch(error => {
            console.error("Error en enviarFormularioNomenclatura:", error);
            showToast(error.message || "Error en la conexión al servidor", "error");
        })
        .finally(() => {
            hideLoading();
        });

    } catch (error) {
        console.error("Error interno en enviarFormularioNomenclatura:", error);
        showToast(error.message || "Error interno al procesar el formulario", "error");
        hideLoading();
    }
}

window.enviarFormularioNomenclatura = enviarFormularioNomenclatura;

window.crearNomenclatura = function() {
    try {
        showLoading();

        const form = document.getElementById('nomenclaturaForm');
        form.action = '/nomenclaturas/crear';
        document.getElementById('nomenclaturaId').value = '';
        document.getElementById('descripcion').value = '';
        document.getElementById('abreviatura').value = '';
        document.getElementById('id_tipo').value = '';

        document.getElementById('nomenclaturaModalLabel').textContent = 'Nueva nomenclatura';
        document.getElementById('submitButton').textContent = 'Crear nomenclatura';

        modalHelper.open('nomenclaturaModal');
    } catch (error) {
        console.error("Error en crearNomenclatura:", error);
        showToast("No se pudo abrir el modal de nomenclatura", "error");
    } finally {
        hideLoading();
    }
}

window.editarNomenclatura = function(nomenclatura) {
    try {
        showLoading();

        const form = document.getElementById('nomenclaturaForm');
        form.action = `/nomenclatura/actualizar/${nomenclatura.id}`;
        form.method = 'POST';

        if (!form.querySelector('input[name="_method"]')) {
            const inputMethod = document.createElement('input');
            inputMethod.type = 'hidden';
            inputMethod.name = '_method';
            inputMethod.value = 'POST';
            form.appendChild(inputMethod);
        }

        document.getElementById('nomenclaturaId').value = nomenclatura.id;
        document.getElementById('descripcion').value = nomenclatura.descripcion;
        document.getElementById('abreviatura').value = nomenclatura.abreviatura;
        document.getElementById('id_tipo').value = nomenclatura.id_tipo;

        document.getElementById('nomenclaturaModalLabel').textContent = 'Editar nomenclatura';
        document.getElementById('submitButton').textContent = 'Actualizar nomenclatura';

        modalHelper.open('nomenclaturaModal');
    } catch (error) {
        console.error("Error en editarNomenclatura:", error);
        showToast("No se pudo abrir el modal de edición de nomenclatura", "error");
    } finally {
        hideLoading();
    }
}

window.cambiarEstadoNomenclatura = function(id, estado) {
    const accion = estado === 1 ? 'inactivar' : 'restaurar';
    const nuevoEstado = estado === 1 ? 0 : 1;
    const mensajeConfirmacion = estado === 1
        ? "¿Está seguro que desea inactivar la nomenclatura?"
        : "¿Está seguro que desea restaurar la nomenclatura?";

    showConfirm(mensajeConfirmacion, () => {
        showLoading();

        $.ajax({
            url: `/nomenclatura/cambiarEstado/${id}`,
            type: 'PUT',
            data: {
                _token: $('meta[name="csrf-token"]').attr('content'),
                estado: nuevoEstado
            },
            success: function(response) {
                showToast(response.message || "Nomenclatura actualizada", "success");
                setTimeout(() => location.reload(), 1000);
            },
            error: function() {
                showToast(`Hubo un error al intentar ${accion} la nomenclatura.`, "error");
            },
            complete: function() {
                hideLoading();
            }
        });
    });
}