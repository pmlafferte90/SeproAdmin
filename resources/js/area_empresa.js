document.addEventListener("DOMContentLoaded", function () {

    function inicializarTomCliente() {
        if (window.TomSelect && window.clientesData?.length > 0) {
            new TomSelect("#cliente", {
                options: clientesData,
                placeholder: "Seleccione un cliente",
                allowEmptyOption: true,
                create: false,
                sortField: {
                    field: "text",
                    direction: "asc"
                },
            });
        }
    }

    inicializarTomCliente();


    // ─── Variables de estado ──────────────────────────────────────────────
    let pageDisp = 1;
    let moreDisp = true;
    let loadingDisp = false;
    let qDisp = '';
    const perPage = 50;       // ajusta si quieres
    const ulDisp = document.getElementById('listaDisponibles');
    const ulSel = document.getElementById('listaSeleccionadas');
    const inputQDisp = document.getElementById('buscarSubareas');
    const inputQSel = document.getElementById('buscarSeleccionadas');
    const inputHidden = document.getElementById('inputSubareas');
    let selectedIds = [];       // ['12','34',...]
    let selectedItems = [];       // [{id,text,area},...]

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

        } catch (error) {
            console.error('❌ Error al cargar localidades:', error);
            showToast('Error al cargar las localidades', 'error');
        } finally {
            hideLoading(); // ✅ Oculta el loader siempre
        }
    });


    // Evento para cambiar localidad y cargar sub-áreas asignadas
    document.getElementById("localidad").addEventListener("change", async function () {

        selectedIds = [];                    // tu array de IDs seleccionados
        selectedItems = [];                    // tu array de objetos seleccionado
        ulSel.innerHTML = "";                 // UL de “seleccionadas”
        inputQSel.value = "";                // limpia el input de búsqueda seleccionadas
        inputHidden.value = "[]";              // tu hidden que envía el JSON

        const localidadId = this.value;
        if (!localidadId) return;

        showLoading(); // ⏳ Muestra loader global

        try {
            const response = await fetch(`/api/localidad/subareas/${localidadId}`);
            const result = await response.json();

            // ✅ Guardamos solo los IDs (como strings)
            //subareasSeleccionadas = result.data.map(sub => sub.id_subarea.toString());
            result.data.forEach(
                sub => {
                    if (!selectedIds.includes(sub.id_subarea.toString())) {
                        selectedIds.push(sub.id_subarea.toString());
                        selectedItems.push({
                            id: sub.id_subarea,
                            text: sub.nombre,
                            area: sub.nombre_area,
                            codigo: sub.codigo
                        });
                    }
                }
            );

            renderSeleccionadas();
            updateAvailableVisibility();


            // ✅ Mostrar la sección
            document.getElementById("contenedorSubareas").style.display = "block";

        } catch (error) {
            console.error('❌ Error al cargar ubicaciones:', error);
            showToast('Error al cargar las ubicaciones', 'error');
        } finally {
            hideLoading(); // ✅ Ocultar loader siempre
        }
    });

    // Función para mostrar el formulario para asignar subareas
    window.nuevoAreaEmpresa = function () {
        const form = document.getElementById("areaEmpresaForm");
        form.reset();

        // Limpiar selects dinámicos
        document.getElementById("localidad").innerHTML = '<option value="" disabled selected>Seleccione una localidad</option>';
        document.getElementById("listaSeleccionadas").innerHTML = '';
        document.getElementById("inputSubareas").value = '';

        document.getElementById("contenedorSubareas").style.display = "none";

        // Mostrar el modal
        const modal = new bootstrap.Modal(document.getElementById("areaEmpresaModal"));
        modal.show();
    };


    // Función para enviar formulario
    window.enviarFormularioAreaEmpresa = function () {
        showLoading();

        const form = document.getElementById("areaEmpresaForm");
        const formData = new FormData(form);

        const localidadId = document.getElementById("localidad").value;
        const subareas = selectedIds;

        if (subareas.length === 0) {
            hideLoading();
            showToast("Debe seleccionar al menos una ubicacion.", "error");
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

    // Para no re-dibujar cabeceras ya puestas
    const appendedAreas = new Set();

    // ─── Función para cargar página de disponibles ─────────────────────────
    async function loadDisponibles() {
        if (!moreDisp || loadingDisp) return;
        loadingDisp = true;

        try {
            const res = await fetch(`/api/subareas/search?q=${encodeURIComponent(qDisp)}&page=${pageDisp}`);
            const json = await res.json();
            moreDisp = json.pagination.more;

            // por cada subárea retornada
            json.data.forEach(sub => {
                // si no hemos agregado la cabecera de este área
                if (!appendedAreas.has(sub.area)) {
                    const hdr = document.createElement('li');
                    hdr.className = 'list-group-item active btn-sepro';
                    hdr.textContent = sub.area;
                    ulDisp.appendChild(hdr);
                    appendedAreas.add(sub.area);
                }
                // ahora el item
                const li = document.createElement('li');
                li.dataset.id = sub.id.toString(); 
                li.className = 'list-group-item d-flex justify-content-between align-items-center';
                li.innerHTML = `
              <div>${sub.text}</div>
              <button type="button" class="btn btn-sm btn-sepro"><i class="bi bi-plus"></i></button>
            `;
                // click “+”
                li.querySelector('button').addEventListener('click', () => {
                    // evita duplicados
                    if (!selectedIds.includes(sub.id.toString())) {
                        selectedIds.push(sub.id.toString());
                        selectedItems.push(sub);
                        renderSeleccionadas();
                        updateAvailableVisibility();
                    }
                });
                ulDisp.appendChild(li);
            });

        } catch (e) {
            console.error('Error cargando disponibles', e);
        } finally {
            loadingDisp = false;
            updateAvailableVisibility();
        }
    }

    // ─── Detectar scroll hasta fondo para infinite scroll ───────────────────
    ulDisp.addEventListener('scroll', () => {
        const threshold = 50; // px antes del final
        if (ulDisp.scrollTop + ulDisp.clientHeight >= ulDisp.scrollHeight - threshold) {
            if (moreDisp && !loadingDisp) {
                pageDisp++;
                loadDisponibles();
            }
        }
    });

    // ─── Búsqueda con debounce ──────────────────────────────────────────────
    let timerDisp;
    inputQDisp.addEventListener('input', () => {
        clearTimeout(timerDisp);
        timerDisp = setTimeout(() => {
            const term = inputQDisp.value.trim();
            if (term.length >= 3 || term.length === 0) {
                qDisp = inputQDisp.value.trim();
                pageDisp = 1;
                moreDisp = true;
                appendedAreas.clear();
                ulDisp.innerHTML = '';
                loadDisponibles();
            }
        }, 300);
    });

    function renderSeleccionadas() {
        const qSel = inputQSel.value.trim().toLowerCase();

        // 1) Filtrar el array de objetos
        const filtradas = selectedItems.filter(sub => {
            return sub.text.toLowerCase().includes(qSel)
                || (sub.codigo && sub.codigo.toLowerCase().includes(qSel))
                || sub.area.toLowerCase().includes(qSel);
        });

        // 2) Agrupar las filtradas por área
        const grp = filtradas.reduce((acc, sub) => {
            if (!acc[sub.area]) acc[sub.area] = [];
            acc[sub.area].push(sub);
            return acc;
        }, {});

        // 3) Limpiar UL y volver a dibujar sólo las filtradas
        ulSel.innerHTML = '';
        for (const area in grp) {
            const hdr = document.createElement('li');
            hdr.className = 'list-group-item active';
            hdr.textContent = area;
            ulSel.appendChild(hdr);

            grp[area].forEach(sub => {
                const li = document.createElement('li');
                li.className = 'list-group-item d-flex justify-content-between align-items-center';
                li.innerHTML = `
            <div>${sub.text}</div>
            <button type="button" class="btn btn-sm btn-danger">
              <i class="bi bi-x"></i>
            </button>
          `;
                li.querySelector('button').addEventListener('click', () => {
                    // quitar de arrays
                    selectedIds = selectedIds.filter(id => id !== sub.id.toString());
                    selectedItems = selectedItems.filter(s => s.id.toString() !== sub.id.toString());
                    renderSeleccionadas();
                    updateAvailableVisibility();
                });
                ulSel.appendChild(li);
            });
        }

        // 4) Actualizar el hidden con TODOS los IDs seleccionados
        inputHidden.value = JSON.stringify(selectedIds);
    }

    // ─── Filtrar la lista de seleccionadas ─────────────────────────────────
    inputQSel.addEventListener('input', () => {
        const term = inputQSel.value.trim().toLowerCase();
        // solo renderiza cuando ≥3
        if (term.length >= 3 || term.length === 0) {
            renderSeleccionadas();
        }
    });
    // ─── Primer fetch ───────────────────────────────────────────────────────
    loadDisponibles();

    // ─── Bloquear Enter ──────────────────────────────────
    ['buscarSubareas', 'buscarSeleccionadas'].forEach(id => {
        const input = document.getElementById(id);
        if (!input) return;
        input.addEventListener('keydown', e => {
            if (e.key === 'Enter') e.preventDefault();
        });
    });

    function updateAvailableVisibility() {
      // Recorre todos los <li> de listaDisponibles
      Array.from(ulDisp.querySelectorAll('li')).forEach(li => {
        const id = li.dataset.id;    // sólo existe en los sub-área items
        if (!id) return;             // saltar encabezados de área

        if (selectedIds.includes(id)) {
          // OCULTA con !important
          li.style.setProperty('display', 'none', 'important');
        } else {
          // MUESTRA (elimina cualquier display inline)
          li.style.removeProperty('display');
        }
      });
    }

});
