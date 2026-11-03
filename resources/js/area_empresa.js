//let todasLasSubareas = window.subareasRaw || [];
let todasLasSubareas = [];
let subareasSeleccionadas = [];

document.addEventListener("DOMContentLoaded", function() {

    function renderSubareas() {
        const disponiblesUl = document.getElementById("listaDisponibles");
        const seleccionadasUl = document.getElementById("listaSeleccionadas");
        const buscarDisponibles = document.getElementById("buscarSubareas").value.toLowerCase();
        const buscarSeleccionadas = document.getElementById("buscarSeleccionadas")?.value.toLowerCase() || '';

        disponiblesUl.innerHTML = '';
        seleccionadasUl.innerHTML = '';

        // Agrupar disponibles por área
        const disponiblesAgrupadas = {};
        todasLasSubareas.forEach(sub => {
            if (
                !subareasSeleccionadas.includes(sub.id.toString()) &&
                (
                    sub.text.toLowerCase().includes(buscarDisponibles) ||
                    (sub.codigo && sub.codigo.toLowerCase().includes(buscarDisponibles))
                )
                ){
                if (!disponiblesAgrupadas[sub.area]) disponiblesAgrupadas[sub.area] = [];
                disponiblesAgrupadas[sub.area].push(sub);
            }
        });

        for (const area in disponiblesAgrupadas) {
            const header = document.createElement("li");
            header.className = "list-group-item active btn-sepro";
            header.textContent = area;
            disponiblesUl.appendChild(header);

            disponiblesAgrupadas[area]
                .sort((a, b) => a.text.localeCompare(b.text))
                .forEach(sub => {
                    const li = document.createElement("li");
                    li.className = "list-group-item d-flex justify-content-between align-items-center";
                    li.innerHTML = `<div>${sub.text}</div>
                                    <button class="btn btn-sm btn-sepro">
                                        <i class="bi bi-plus"></i>
                                    </button>`;
                    li.querySelector("button").addEventListener("click", () => {
                        subareasSeleccionadas.push(sub.id.toString());
                        renderSubareas();
                    });
                    disponiblesUl.appendChild(li);
                });
        }

        // Agrupar seleccionadas por área
        const seleccionadasAgrupadas = {};
        subareasSeleccionadas.forEach(id => {
            const sub = todasLasSubareas.find(s => s.id.toString() === id);
            if (!sub || !sub.text.toLowerCase().includes(buscarSeleccionadas)) return;
            if (!seleccionadasAgrupadas[sub.area]) seleccionadasAgrupadas[sub.area] = [];
            seleccionadasAgrupadas[sub.area].push(sub);
        });

        for (const area in seleccionadasAgrupadas) {
            const header = document.createElement("li");
            header.className = "list-group-item active";
            header.textContent = area;
            seleccionadasUl.appendChild(header);

            seleccionadasAgrupadas[area]
                .sort((a, b) => a.text.localeCompare(b.text))
                .forEach(sub => {
                    const li = document.createElement("li");
                    li.className = "list-group-item d-flex justify-content-between align-items-center";
                    li.innerHTML = `<div>${sub.text}</div>
                                    <button class="btn btn-sm btn-danger">
                                        <i class="bi bi-x"></i>
                                    </button>`;
                    li.querySelector("button").addEventListener("click", () => {
                        subareasSeleccionadas = subareasSeleccionadas.filter(x => x !== sub.id.toString());
                        renderSubareas();
                    });
                    seleccionadasUl.appendChild(li);
                });
        }

        // Actualizar input oculto
        document.getElementById("inputSubareas").value = JSON.stringify(subareasSeleccionadas);
    }

    renderSubareas();

    // Buscar
    document.getElementById("buscarSubareas").addEventListener("input", renderSubareas);
    document.getElementById("buscarSeleccionadas").addEventListener("input", renderSubareas);

    // Evento para cambiar cliente y cargar localidades
    document.getElementById("cliente").addEventListener("change", async function () {
        const clienteId = this.value;

        // Ocultar lista hasta que seleccione localidad
        document.getElementById("contenedorSubareas").style.display = "none";

        if (!clienteId) return;

        showLoading(); // ⏳ Muestra el loader global

        try {
            const response = await fetch(`/api/localidad/cliente/${clienteId}`);
            const result = await response.json();

            const localidadSelect = document.getElementById("localidad");
            localidadSelect.innerHTML = '<option value="" disabled selected>Seleccione una localidad</option>';

            result.data.forEach(localidad => {
                const option = document.createElement("option");
                option.value = localidad.id;
                option.textContent = localidad.nombre;
                localidadSelect.appendChild(option);
            });

            // Limpiar subáreas seleccionadas visualmente
            subareasSeleccionadas = [];
            renderSubareas();

        } catch (error) {
            console.error('❌ Error al cargar localidades:', error);
            showToast('Error al cargar las localidades', 'error');
        } finally {
            hideLoading(); // ✅ Oculta el loader siempre
        }
    });


    // Evento para cambiar localidad y cargar sub-áreas asignadas
    document.getElementById("localidad").addEventListener("change", async function () {
        const localidadId = this.value;
        if (!localidadId) return;

        showLoading(); // ⏳ Muestra loader global

        try {
            const response = await fetch(`/api/localidad/subareas/${localidadId}`);
            const result = await response.json();

            // ✅ Guardamos solo los IDs (como strings)
            subareasSeleccionadas = result.data.map(sub => sub.id_subarea.toString());

            // ✅ Mostrar la sección
            document.getElementById("contenedorSubareas").style.display = "block";

            console.log("✅ Sub-áreas seleccionadas:", subareasSeleccionadas);
            renderSubareas();
        } catch (error) {
            console.error('❌ Error al cargar sub-áreas:', error);
            showToast('Error al cargar las sub-áreas', 'error');
        } finally {
            hideLoading(); // ✅ Ocultar loader siempre
        }
    });



    // Función para mostrar el formulario para asignar subareas
    // window.nuevoAreaEmpresa = function () {
    //     document.getElementById("areaEmpresaForm").reset();
    //     document.getElementById("areaEmpresaId").value = "";
        
    //     var myModal = new bootstrap.Modal(document.getElementById("areaEmpresaModal"));
    //     myModal.show();
    // }

    // Función para mostrar el formulario para asignar subareas
    /*window.nuevoAreaEmpresa = function () {
        const form = document.getElementById("areaEmpresaForm");
        form.reset();

        // Limpiar selects dinámicos
        document.getElementById("localidad").innerHTML = '<option value="" disabled selected>Seleccione una localidad</option>';
        document.getElementById("listaDisponibles").innerHTML = '';
        document.getElementById("listaSeleccionadas").innerHTML = '';
        document.getElementById("inputSubareas").value = '';

        // Si estás usando TomSelect u otra librería en algún campo, destrúyelo aquí también
        if (window.tomSubareas) {
            window.tomSubareas.clear();
            window.tomSubareas.destroy();
            window.tomSubareas = null;
        }

        document.getElementById("contenedorSubareas").style.display = "none";

        // Mostrar el modal
        const modal = new bootstrap.Modal(document.getElementById("areaEmpresaModal"));
        modal.show();
    };*/

    window.nuevoAreaEmpresa = async function () {
        const form = document.getElementById("areaEmpresaForm");
        form.reset();

        // Limpiar selects y listas
        document.getElementById("cliente").innerHTML = '<option value="" disabled selected>Seleccione un cliente</option>';
        document.getElementById("localidad").innerHTML = '<option value="" disabled selected>Seleccione una localidad</option>';
        document.getElementById("listaDisponibles").innerHTML = '';
        document.getElementById("listaSeleccionadas").innerHTML = '';
        document.getElementById("inputSubareas").value = '';
        document.getElementById("contenedorSubareas").style.display = "none";

        if (window.tomSubareas) {
            window.tomSubareas.clear();
            window.tomSubareas.destroy();
            window.tomSubareas = null;
        }

        showLoading();

        try {
            const responseClientes = await fetch('/api/clientes_activos');
            const resultClientes = await responseClientes.json();

            const clienteSelect = document.getElementById("cliente");
            resultClientes.data.forEach(cliente => {
                const option = document.createElement("option");
                option.value = cliente.id;
                option.textContent = cliente.razon_social;
                clienteSelect.appendChild(option);
            });

            const responseSubareas = await fetch('/api/subareas_activas');
            const resultSubareas = await responseSubareas.json();

            todasLasSubareas = resultSubareas.data || [];
            subareasSeleccionadas = [];

            renderSubareas();

            const modal = new bootstrap.Modal(document.getElementById("areaEmpresaModal"));
            modal.show();
        } catch (error) {
            console.error('❌ Error al cargar datos del formulario:', error);
            showToast('Error al cargar los datos del formulario.', 'error');
        } finally {
            hideLoading();
        }
    };

    // Función para enviar formulario
    window.enviarFormularioAreaEmpresa = function () {
        showLoading();

        const form = document.getElementById("areaEmpresaForm");
        const formData = new FormData(form);

        const localidadId = document.getElementById("localidad").value;
        const subareas = subareasSeleccionadas;

        if (subareas.length === 0) {
            hideLoading();
            showToast("Debe seleccionar al menos una sub-área.", "error");
            return;
        }

        const data = {
            localidad: localidadId,
            subareas: subareas
        };

        fetch('/api/localidad/crear_subareas', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': formData.get('_token'),
            },
            body: JSON.stringify(data)
        })
        .then(response => response.json())
        .then(data => {
            hideLoading();
            showToast(data.message, data.ok ? 'success' : 'error');
            if (data.ok) {
                location.reload();
            }
        })
        .catch(error => {
            hideLoading();
            console.error(error);
            showToast('Error en la operación', 'error');
        });
    };

    window.cambiarEstadoAreaEmpresa = function (id, estado, accion) {
        let mensajeConfirmacion = '';

        switch (accion) {
            case 'restaurar':
                mensajeConfirmacion = "¿Está seguro que desea restaurar el Área?";
                break;
            case 'inactivar':
                mensajeConfirmacion = "¿Está seguro que desea inactivar el Área?";
                break;
            case 'aprobar':
                if (estado == 2) {
                    mensajeConfirmacion = "¿Está seguro que desea aprobar la creación del Área?";
                } else if (estado == 3) {
                    mensajeConfirmacion = "¿Está seguro que desea aprobar la inactivación del Área?";
                }
                break;
            case 'rechazar':
                if (estado == 2) {
                    mensajeConfirmacion = "¿Está seguro que desea rechazar la creación del Área?";
                } else if (estado == 3) {
                    mensajeConfirmacion = "¿Está seguro que desea rechazar la inactivación del Área?";
                }
                break;
            default:
                mensajeConfirmacion = "¿Está seguro que desea realizar esta acción?";
        }

        showConfirm(mensajeConfirmacion, () => {
            showLoading();

            $.ajax({
                url: `/area_empresa/cambiarEstado_area_empresa/${id}`,
                type: 'PUT',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    accion: accion
                },
                success: function (response) {
                    hideLoading();
                    showToast(response.message, 'success', () => location.reload());
                },
                error: function (xhr) {
                    hideLoading();
                    console.error("❌ Error al cambiar estado:", xhr);
                    showToast('Hubo un error al intentar cambiar el estado del área.', 'error');
                }
            });
        });
    }

});
