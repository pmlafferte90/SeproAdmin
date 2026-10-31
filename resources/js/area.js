console.log("entro a area js");

function enviarFormularioArea() {
    showLoading();

    try {
        const form = document.getElementById("areaForm");
        const formData = new FormData(form);
        const areaId = formData.get("id");
        const url = areaId ? `/area/actualizar/${areaId}` : "/area/crear";
        const method = "POST";

        console.log("Form Data:");
        formData.forEach((value, key) => {
            console.log(key, value);
        });

        fetch(url, {
            method: method,
            body: formData,
            headers: {
                "X-CSRF-TOKEN": document
                    .querySelector('meta[name="csrf-token"]')
                    .getAttribute("content"),
            },
        })
        .then((response) => {
            console.log("Raw response:", response);
            return response.text();
        })
        .then((data) => {
            console.log("Response text:", data);
            try {
                const jsonData = JSON.parse(data);

                if (jsonData.success) {
                    showToast("Área guardada exitosamente", "success");

                    const modal = bootstrap.Modal.getInstance(document.getElementById("areaModal"));
                    modal.hide();
                    document.querySelectorAll(".modal-backdrop").forEach(el => el.remove());

                    location.reload();
                } else {
                    throw new Error(jsonData.error || "Error al guardar el área");
                }
            } catch (error) {
                console.error("Error parsing JSON:", error);
                showToast("Respuesta inválida del servidor", "error");
            }
        })
        .catch((error) => {
            console.error("Error en enviarFormularioArea:", error);
            showToast(error.message || "Error en la conexión al servidor", "error");
        })
        .finally(() => {
            hideLoading();
        });
    } catch (error) {
        console.error("Error interno en enviarFormularioArea:", error);
        showToast(error.message || "Error interno al procesar el formulario", "error");
        hideLoading();
    }
}

window.enviarFormularioArea = enviarFormularioArea;

window.crearArea = function () {
    try {
        showLoading();

        document.getElementById("areaForm").action = "/area/crear";
        document.getElementById("areaId").value = "";
        document.getElementById("nombre").value = "";

        document.getElementById("areaModalLabel").textContent = "Nuevo área";
        document.getElementById("submitButton").textContent = "Crear área";

        modalHelper.open("areaModal");
    } catch (error) {
        console.error("Error en crearArea:", error);
        showToast("No se pudo abrir el modal de área", "error");
    } finally {
        hideLoading();
    }
};

window.editarArea = function (area) {
    try {
        showLoading();

        const form = document.getElementById("areaForm");
        form.action = `/area/actualizar/${area.id}`;
        form.method = "POST";

        if (!form.querySelector('input[name="_method"]')) {
            const inputMethod = document.createElement("input");
            inputMethod.type = "hidden";
            inputMethod.name = "_method";
            inputMethod.value = "POST";
            form.appendChild(inputMethod);
        }

        document.getElementById("areaId").value = area.id;
        document.getElementById("nombre").value = area.nombre;

        document.getElementById("areaModalLabel").textContent = "Editar área";
        document.getElementById("submitButton").textContent = "Actualizar área";

        modalHelper.open("areaModal");
    } catch (error) {
        console.error("Error en editarArea:", error);
        showToast("No se pudo abrir el modal de edición de área", "error");
    } finally {
        hideLoading();
    }
};

window.cambiarEstadoArea = function (id, estado) {
    const accion = estado === 1 ? "inactivar" : "restaurar";
    const nuevoEstado = estado === 1 ? 0 : 1;
    const mensajeConfirmacion =
        estado === 1
            ? "¿Está seguro que desea inactivar el área?"
            : "¿Está seguro que desea restaurar el área?";

    showConfirm(mensajeConfirmacion, function () {
        showLoading();

        $.ajax({
            url: `/area/cambiarEstado/${id}`,
            type: "PUT",
            data: {
                _token: $('meta[name="csrf-token"]').attr("content"),
                estado: nuevoEstado,
            },
            success: function (response) {
                showToast(response.message || "Área actualizada", "success");
                setTimeout(() => location.reload(), 1000);
            },
            error: function () {
                showToast(`Hubo un error al intentar ${accion} el área.`, "error");
            },
            complete: function () {
                hideLoading();
            }
        });
    });
};