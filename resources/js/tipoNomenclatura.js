console.log("entro a tipoNomenclatura js");

function enviarFormularioTipoNomenclatura() {
    showLoading();

    try {
        const form = document.getElementById('tipoNomenclaturaForm');
        const formData = new FormData(form);
        const tipoNomenclaturaId = formData.get('id');
        const url = tipoNomenclaturaId ? `/tipoNomenclatura/actualizar/${tipoNomenclaturaId}` : '/tipoNomenclatura/crear';
        const method = 'POST';

        console.log("URL:", url);
        console.log("Method:", method);
        console.log("FormData:", formData);

        fetch(url, {
            method: method,
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showToast("Tipo de nomenclatura guardado exitosamente", "success");

                const modal = bootstrap.Modal.getInstance(document.getElementById('tipoNomenclaturaModal'));
                modal.hide();
                document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());

                location.reload();
            } else {
                console.error('Error:', data.error);
                showToast(data.error || "Error al guardar el tipo de nomenclatura", "error");
            }
        })
        .catch(error => {
            console.error('Error al enviar formulario:', error);
            showToast("Error de red al enviar el formulario", "error");
        })
        .finally(() => {
            hideLoading();
        });
    } catch (error) {
        console.error("Error interno en enviarFormularioTipoNomenclatura:", error);
        showToast("Error interno al procesar el formulario", "error");
        hideLoading();
    }
}

window.enviarFormularioTipoNomenclatura = enviarFormularioTipoNomenclatura;

window.crearTipoNomenclatura = function() {
    try {
        showLoading();

        const form = document.getElementById('tipoNomenclaturaForm');
        form.action = '/tipoNomenclatura/crear';
        document.getElementById('tipoNomenclaturaId').value = '';
        document.getElementById('descripcion').value = '';

        document.getElementById('tipoNomenclaturaModalLabel').textContent = 'Nuevo tipo';
        document.getElementById('submitButton').textContent = 'Crear tipo';

        modalHelper.open('tipoNomenclaturaModal');
    } catch (error) {
        console.error("Error en crearTipoNomenclatura:", error);
        showToast("No se pudo abrir el modal de tipo de nomenclatura", "error");
    } finally {
        hideLoading();
    }
}

window.editarTipoNomenclatura = function(tipoNomenclatura) {
    try {
        showLoading();

        const form = document.getElementById('tipoNomenclaturaForm');
        form.action = `/tipoNomenclatura/actualizar/${tipoNomenclatura.id}`;
        form.method = 'POST';

        if (!form.querySelector('input[name="_method"]')) {
            const inputMethod = document.createElement('input');
            inputMethod.type = 'hidden';
            inputMethod.name = '_method';
            inputMethod.value = 'PUT';
            form.appendChild(inputMethod);
        }

        document.getElementById('tipoNomenclaturaId').value = tipoNomenclatura.id;
        document.getElementById('descripcion').value = tipoNomenclatura.descripcion;

        document.getElementById('tipoNomenclaturaModalLabel').textContent = 'Editar Tipo';
        document.getElementById('submitButton').textContent = 'Actualizar Tipo';

        modalHelper.open('tipoNomenclaturaModal');
    } catch (error) {
        console.error("Error en editarTipoNomenclatura:", error);
        showToast("No se pudo abrir el modal de edición", "error");
    } finally {
        hideLoading();
    }
}

window.cambiarEstadoTipoNomenclatura = function(id, estado) {
    const accion = estado === 1 ? 'inactivar' : 'restaurar';
    const nuevoEstado = estado === 1 ? 0 : 1;
    const mensajeConfirmacion = estado === 1
        ? "¿Está seguro que desea inactivar el tipo de nomenclatura?"
        : "¿Está seguro que desea restaurar el tipo de nomenclatura?";

    showConfirm(mensajeConfirmacion, () => {
        showLoading();

        $.ajax({
            url: `/tipoNomenclatura/cambiarEstado/${id}`,
            type: 'PUT',
            data: {
                _token: $('meta[name="csrf-token"]').attr('content'),
                estado: nuevoEstado
            },
            success: function(response) {
                showToast(response.message || "Estado actualizado", "success");
                setTimeout(() => location.reload(), 1000);
            },
            error: function() {
                showToast(`Hubo un error al intentar ${accion} el tipo de nomenclatura.`, "error");
            },
            complete: function() {
                hideLoading();
            }
        });
    });
}