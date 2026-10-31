console.log("entro a usuario js");

function enviarFormulariousuario() {
    showLoading();

    try {
        const form = document.getElementById('usuarioForm');
        const formData = new FormData(form);
        const usuarioId = formData.get('usuarioId');
        const url = usuarioId ? `usuario/actualizar/${usuarioId}` : 'usuario/crear';
        const method = 'POST';

        console.log("Form Data:");
        formData.forEach((value, key) => console.log(key, value));

        fetch(url, {
            method: method,
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.text())
        .then(data => {
            console.log('Response text:', data);
            let jsonData;

            try {
                jsonData = JSON.parse(data);
            } catch (err) {
                throw new Error("Respuesta inválida del servidor");
            }

            if (jsonData.success) {
                showToast("Usuario guardado exitosamente", "success");

                const modal = bootstrap.Modal.getInstance(document.getElementById('usuarioModal'));
                modal.hide();
                document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());

                location.reload();
            } else if (jsonData.errors) {
                Object.values(jsonData.errors).flat().forEach(msg => showToast(msg, "error"));
            } else {
                throw new Error(jsonData.error || "Error desconocido");
            }
        })
        .catch(error => {
            console.error("Error al enviar formulario:", error);
            showToast(error.message || "Error al enviar datos", "error");
        })
        .finally(() => {
            hideLoading();
        });

    } catch (error) {
        console.error("Error interno en enviarFormulariousuario:", error);
        showToast(error.message || "Error interno en el formulario", "error");
        hideLoading();
    }
}

window.enviarFormulariousuario = enviarFormulariousuario;

window.crearusuario = function() {
    try {
        showLoading();

        const form = document.getElementById('usuarioForm');
        form.action = '/usuarios/crear';
        document.getElementById('usuarioId').value = '';
        document.getElementById('usuario').value = '';
        document.getElementById('usuario').disabled = false;
        document.getElementById('password').value = '';
        document.getElementById('rol_id').value = '';

        document.getElementById('usuarioModalLabel').textContent = 'Nuevo usuario';
        document.getElementById('submitButton').textContent = 'Crear usuario';

        modalHelper.open('usuarioModal');
    } catch (error) {
        console.error("Error en crearusuario:", error);
        showToast("No se pudo abrir el modal de usuario", "error");
    } finally {
        hideLoading();
    }
}

window.editarusuario = function(usuario) {
    try {
        showLoading();

        const form = document.getElementById('usuarioForm');
        form.action = `usuario/actualizar/${usuario.id}`;
        form.method = 'POST';

        if (!form.querySelector('input[name="_method"]')) {
            const inputMethod = document.createElement('input');
            inputMethod.type = 'hidden';
            inputMethod.name = '_method';
            inputMethod.value = 'POST';
            form.appendChild(inputMethod);
        }

        document.getElementById('usuarioId').value = usuario.id;
        document.getElementById('usuario').value = usuario.usuario;
        document.getElementById('usuario').disabled = true;
        document.getElementById('password').value = usuario.password;
        document.getElementById('rol_id').value = usuario.id_rol;

        document.getElementById('usuarioModalLabel').textContent = 'Editar usuario';
        document.getElementById('submitButton').textContent = 'Actualizar usuario';

        modalHelper.open('usuarioModal');
    } catch (error) {
        console.error("Error en editarusuario:", error);
        showToast("No se pudo abrir el modal de edición", "error");
    } finally {
        hideLoading();
    }
}

window.cambiarEstadousuario = function(id, estado) {
    const accion = estado === 1 ? 'inactivar' : 'restaurar';
    const nuevoEstado = estado === 1 ? 0 : 1;
    const mensajeConfirmacion = estado === 1
        ? "¿Está seguro que desea inactivar el usuario?"
        : "¿Está seguro que desea restaurar el usuario?";

    showConfirm(mensajeConfirmacion, () => {
        showLoading();

        $.ajax({
            url: `usuario/cambiarEstado_usuario/${id}`,
            type: 'PUT',
            data: {
                _token: $('meta[name="csrf-token"]').attr('content'),
                estado: nuevoEstado
            },
            success: function(response) {
                showToast(response.message || "Usuario actualizado", "success");
                setTimeout(() => location.reload(), 1000);
            },
            error: function() {
                showToast(`Hubo un error al intentar ${accion} el usuario.`, "error");
            },
            complete: function() {
                hideLoading();
            }
        });
    });
}