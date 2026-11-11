console.log("entro a subarea js");

function enviarFormularioSubArea() {
    showLoading();
    try {
        let form = document.getElementById("subareaForm");
        let formData = new FormData(form);

        // Verifica si es un subarea nuevo o existente
        let subareaId = formData.get("subareaId");
        let url = subareaId ? `subarea/actualizar/${subareaId}` : "subarea/crear";
        let method = "POST";

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
        .then(response => {
            console.log("Raw response:", response);
            return response.text();
        })
        .then(data => {
            console.log("Response text:", data);
            let jsonData;
            try {
                jsonData = JSON.parse(data);
            } catch (err) {
                throw new Error("Respuesta inválida del servidor");
            }
            if (jsonData.success) {
                showToast("Operación exitosa", "success");
                const modal = bootstrap.Modal.getInstance(
                    document.getElementById("subareaModal")
                );
                modal.hide();
                document
                    .querySelectorAll(".modal-backdrop")
                    .forEach(el => el.remove());
                hideLoading();
                location.reload();
            } else {
                throw new Error(jsonData.error || "Error al actualizar el área");
            }
        })
        .catch(error => {
            console.error("Error en enviarFormularioSubArea:", error);
            showToast(error.message || "Error en la conexión al servidor", "error");
            hideLoading();
        });
    } catch (error) {
        console.error("Error interno en enviarFormularioSubArea:", error);
        showToast(error.message || "Error interno al procesar el formulario", "error");
        hideLoading();
    }
}

// Exportar la función globalmente
window.enviarFormularioSubArea = enviarFormularioSubArea;

window.crearSubArea = function () {
    showLoading();
    try {
        document.getElementById("subareaForm").reset();
        document.getElementById("subareaModalLabel").textContent = "Nueva Ubicación";
        document.getElementById("submitButton").textContent = "Crear Ubicación";
        modalHelper.open('subareaModal');
        inicializarTomAreaSelect();
        hideLoading();
    } catch (error) {
        console.error("Error en crearSubArea:", error);
        showToast("No se pudo abrir el modal de nueva ubicación", "error");
        hideLoading();
    }
};

window.editarSubArea = function (subarea) {
    showLoading();
    try {
        document.getElementById("subareaForm").action = `subarea/actualizar/${subarea.id}`;
        document.getElementById("subareaForm").method = "POST";
        if (!document.querySelector('input[name="_method"]')) {
            var inputMethod = document.createElement("input");
            inputMethod.setAttribute("type", "hidden");
            inputMethod.setAttribute("name", "_method");
            inputMethod.setAttribute("value", "POST");
            subareaForm.appendChild(inputMethod);
        }
        document.getElementById("subareaId").value = subarea.id;
        document.getElementById("nombre").value = subarea.nombre;
        document.getElementById("codigo").value = subarea.codigo;
        document.getElementById("clase").value = subarea.clase;
        document.getElementById("marca").value = subarea.marca;
        document.getElementById("peso").value = subarea.peso;
        document.getElementById("unidad").value = subarea.unidad;
        document.getElementById("area_id").value = subarea.id_area;
        document.getElementById("subareaModalLabel").textContent = "Editar subarea";
        document.getElementById("submitButton").textContent = "Actualizar subarea";
        modalHelper.open('subareaModal');
        inicializarTomAreaSelect();
        hideLoading();
    } catch (error) {
        console.error("Error en editarSubArea:", error);
        showToast("No se pudo abrir el modal de edición de subarea", "error");
        hideLoading();
    }
};

window.cambiarEstadoSubArea = function(id, estado) {
    const accion = estado === 1 ? 'inactivar' : 'restaurar';
    const nuevoEstado = estado === 1 ? 0 : 1;
    const mensajeConfirmacion = estado === 1 
        ? "¿Está seguro que desea inactivar el subárea?" 
        : "¿Está seguro que desea restaurar el subárea?";

    showConfirm(mensajeConfirmacion, function () {
        showLoading(); // Mostrar indicador de carga

        $.ajax({
            url: `subarea/cambiarEstado_subarea/${id}`,
            type: 'PUT',
            data: {
                _token: $('meta[name="csrf-token"]').attr('content'),
                estado: nuevoEstado
            },
            success: function(response) {
                hideLoading(); 
                showToast(response.message, 'success');
                setTimeout(() => location.reload(), 1000);
            },
            error: function(xhr) {
                hideLoading(); 
                showToast(`Hubo un error al intentar ${accion} el subárea.`, 'error');
            }
        });
    });
};

function inicializarTomAreaSelect() {
    const select = document.getElementById('area_id');
    if (!select) return;

    if (select.tomselect) {
        select.tomselect.destroy();
    }

    const areas = JSON.parse(document.getElementById('areaOptionsJson').textContent || '[]');

    const tom = new TomSelect(select, {
        placeholder: 'Buscar área...',
        options: areas.sort((a, b) => a.nombre.localeCompare(b.nombre)),
        valueField: 'id',
        labelField: 'nombre',
        searchField: ['nombre'],
        render: {
            option: (data, escape) => `<div>${escape(data.nombre)}</div>`,
            item: (data, escape) => `<div>${escape(data.nombre)}</div>`
        }
    });
}

document.getElementById('peso').addEventListener('input', function (e) {
    const value = e.target.value;
    const decimalIndex = value.indexOf(".");
    if (decimalIndex >= 0 && value.length - decimalIndex > 3) {
        e.target.value = parseFloat(value).toFixed(2);
    }
});