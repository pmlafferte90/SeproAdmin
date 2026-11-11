import monthSelectPlugin from "flatpickr/dist/plugins/monthSelect";
import { Spanish } from "flatpickr/dist/l10n/es.js";

let usuarioCambioSubarea = false;

document.addEventListener('DOMContentLoaded', function () {

function enviarFormularioMarca() {
    let form = document.getElementById('marcaForm');
    let formData = new FormData(form);
    
    // Verifica si es un marca nuevo o existente
    let marcaId = formData.get('id');  // Supongamos que el campo 'id' existe en el formulario para marcas existentes
    let url = marcaId ? `api/marca/${marcaId}/actualizar` : 'api/marca/crear';
    // let method = marcaId ? 'PUT' : 'POST';
    let method = 'POST';

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
        return response.text();  // Cambiamos a text() para ver el contenido completo
    })
    .then(data => {
        console.log('Response text:', data);
        try {
            let jsonData = JSON.parse(data);  // Intentamos convertir a JSON manualmente
            if (jsonData.success) {
                alert("Operación exitosa.");
                    // Cerrar modal
                const modal = bootstrap.Modal.getInstance(document.getElementById('marcaModal'));
                modal.hide();
                document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());

                // Recargar contenido de main-content
                location.reload();
                // loadContent('/marca');
            } else {
                throw new Error(jsonData.error || 'Error al actualizar el área');
            }
        } catch (error) {
            console.error('Error parsing JSON:', error);
        }
    })
}
// Exportar la función globalmente
window.enviarFormularioMarca = enviarFormularioMarca;

const importForm = document.getElementById('importControlesForm');
if (importForm) {
  importForm.addEventListener('submit', async (e) => {
    e.preventDefault();

    const fd = new FormData(importForm);
    const url = importForm.action;
    const csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    try {
      window.showLoading();

      const res = await fetch(url, {
        method: 'POST',
        headers: {
          'X-CSRF-TOKEN': csrf,
          'X-Requested-With': 'XMLHttpRequest',
        },
        body: fd,
        credentials: 'include',
      });

      const data = await res.json().catch(() => null);

      if (!res.ok) {
        const msg = data?.message || 'Error al importar.';
        window.showToast(msg, 'error');
        return;
      }

      if (data.status === 'success') {
        window.showToast(data.message, 'success', () => {
          location.reload();
        });
      } else if (data.status === 'warning') {
        // Puedes mostrar el detalle en consola o armar un modal
        console.warn('Advertencias de importación:', data.warnings);
        window.showToast(`${data.message} (${data.warnings.length} advertencias)`, 'warning', () => {
          location.reload();
        });
      } else {
        window.showToast(data?.message || 'Error al importar.', 'error');
      }
    } catch (err) {
      console.error(err);
      window.showToast('Error de conexión con el servidor.', 'error');
    } finally {
      window.hideLoading();
      importForm.reset();
    }
  });
}

function ubicacionesxRevisar(clienteId, mes, anio) {
  const url = `/api/control_extintor_cliente/${clienteId}/ubicaciones?mes=${mes}&anio=${anio}`;
  window.showLoading();

  fetch(url)
    .then(res => res.ok ? res.json() : Promise.reject(res.statusText))
    .then(json => {
      const data = json.data;
      let html;

        if (data.length) {
            html = `
            <div class="tabla-scroll">
                <table class="table table-sm table-striped table-hover mb-0">
                <thead>
                    <tr>
                    <th style="width:50px;">#</th>
                    <th style="width:200px;">Área</th>
                    <th style="width:200px;">Ubicación</th>
                    <th style="width:100px;">Código</th>
                    <th style="width:100px;">Clase</th>
                    </tr>
                </thead>
                <tbody>
                    ${data.map((item, i) => `
                    <tr>
                    <td style="width:50px;">${i+1}</td>
                    <td style="width:200px;">${item.area}</td>
                    <td style="width:200px;">${item.nombre}</td>
                    <td style="width:100px;">${item.codigo}</td>
                    <td style="width:100px;">${item.clase}</td>
                    </tr>
                    `).join('')}
                </tbody>
                </table>
            </div>
            `;

        } else {
            html = `
                <div class="p-4 text-center text-muted">
                Ya has realizado el control de todas las ubicaciones para este mes.
                </div>`;
        }

      document.getElementById('ubicacionesModalBody').innerHTML = html;
      new bootstrap.Modal(document.getElementById('ubicacionesModal')).show();
    })
    .catch(err => {
      console.error(err);
      window.showToast('Error al cargar ubicaciones.', 'error');
    })
    .finally(() => {
      window.hideLoading();
    });
}

window.ubicacionesxRevisar = ubicacionesxRevisar;

    window.crearMarca = function() {
        // Configuración para "Nuevo marca"
        document.getElementById('marcaForm').action = 'api/marca/crear';
        document.getElementById('marcaId').value = '';
        document.getElementById('marca').value = '';
        
        // Cambiar título y texto del botón
        document.getElementById('marcaModalLabel').textContent = 'Nuevo marca';
        document.querySelector('.modal-footer .btn-primary').textContent = 'Crear marca';
    }
    
    window.editarMarca = function(marca) {

        // Setea los valores del formulario con los datos del marca pasado desde la vista
        document.getElementById('marcaForm').action = `api/marca/${marca.id}/actualizar`;
        // Establece el método del formulario como POST (esto sigue siendo necesario)
        document.getElementById('marcaForm').method = 'POST';
        if (!document.querySelector('input[name="_method"]')) {
            var inputMethod = document.createElement('input');
            inputMethod.setAttribute('type', 'hidden');
            inputMethod.setAttribute('name', '_method');
            inputMethod.setAttribute('value', 'POST');
            marcaForm.appendChild(inputMethod);
        }
        document.getElementById('marcaId').value = marca['id'];
        document.getElementById('marca').value = marca.marca;
        
        // Cambia el título y el texto del botón del modal
        document.getElementById('marcaModalLabel').textContent = 'Editar marca';
        document.querySelector('.modal-footer .btn-primary').textContent = 'Actualizar marca';
    }

    window.cambiarEstadoMarca = function(id, estado) {
        let accion = estado === 1 ? 'inactivar' : 'restaurar'; // Definir la acción basada en el estado actual
        let nuevoEstado = estado === 1 ? 0 : 1; // Si está activo, inactivarlo; si está inactivo, restaurarlo
        let mensajeConfirmacion = estado === 1 ? "¿Está seguro que desea inactivar el marca?" : "¿Está seguro que desea restaurar el marca?";
    
        if (confirm(mensajeConfirmacion)) {
            // Enviar petición AJAX para cambiar el estado del marca
            $.ajax({
                url: `api/marca/cambiarEstado_marca/${id}`, // Ruta al controlador que maneja el cambio de estado
                type: 'PUT',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content'), // Token CSRF
                    estado: nuevoEstado // Cambiar el estado a 0 o 1
                },
                success: function(response) {
                    // Mostrar mensaje de éxito
                    alert(response.message);
                    // Recargar la página o actualizar la lista de marcas
                    location.reload();
                    // loadContent('/marca');
                },
                error: function(xhr) {
                    // Mostrar mensaje de error
                    alert('Hubo un error al intentar ' + accion + ' el marca.');
                }
            });
        }
    }
    
//-------------------------- PESO

function enviarFormularioPeso() {
    let form = document.getElementById('pesoForm');
    let formData = new FormData(form);
    
    // Verifica si es un peso nuevo o existente
    let pesoId = formData.get('id');  // Supongamos que el campo 'id' existe en el formulario para pesos existentes
    let url = pesoId ? `api/peso/${pesoId}/actualizar` : 'api/peso/crear';
    // let method = pesoId ? 'PUT' : 'POST';
    let method = 'POST';

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
        return response.text();  // Cambiamos a text() para ver el contenido completo
    })
    .then(data => {
        console.log('Response text:', data);
        try {
            let jsonData = JSON.parse(data);  // Intentamos convertir a JSON manualmente
            if (jsonData.success) {
                alert("Operación exitosa.");
                    // Cerrar modal
                const modal = bootstrap.Modal.getInstance(document.getElementById('pesoModal'));
                modal.hide();
                document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());

                // Recargar contenido de main-content
                location.reload();
                // loadContent('/peso');
            } else {
                throw new Error(jsonData.error || 'Error al actualizar el área');
            }
        } catch (error) {
            console.error('Error parsing JSON:', error);
        }
    })
}
// Exportar la función globalmente
window.enviarFormularioPeso = enviarFormularioPeso;

    window.crearPeso = function() {
        // Configuración para "Nuevo peso"
        document.getElementById('pesoForm').action = 'api/peso/crear';
        document.getElementById('pesoId').value = '';
        document.getElementById('peso').value = '';
        
        // Cambiar título y texto del botón
        document.getElementById('pesoModalLabel').textContent = 'Nuevo peso';
        document.querySelector('.modal-footer .btn-primary').textContent = 'Crear peso';
    }
    
    window.editarPeso = function(peso) {

        // Setea los valores del formulario con los datos del peso pasado desde la vista
        document.getElementById('pesoForm').action = `api/peso/${peso.id}/actualizar`;
        // Establece el método del formulario como POST (esto sigue siendo necesario)
        document.getElementById('pesoForm').method = 'POST';
        if (!document.querySelector('input[name="_method"]')) {
            var inputMethod = document.createElement('input');
            inputMethod.setAttribute('type', 'hidden');
            inputMethod.setAttribute('name', '_method');
            inputMethod.setAttribute('value', 'POST');
            pesoForm.appendChild(inputMethod);
        }
        document.getElementById('pesoId').value = peso['id'];
        document.getElementById('peso').value = peso.tamanio;
        
        // Cambia el título y el texto del botón del modal
        document.getElementById('pesoModalLabel').textContent = 'Editar peso';
        document.querySelector('.modal-footer .btn-primary').textContent = 'Actualizar peso';
    }

    window.cambiarEstadoPeso = function(id, estado) {
        let accion = estado === 1 ? 'inactivar' : 'restaurar'; // Definir la acción basada en el estado actual
        let nuevoEstado = estado === 1 ? 0 : 1; // Si está activo, inactivarlo; si está inactivo, restaurarlo
        let mensajeConfirmacion = estado === 1 ? "¿Está seguro que desea inactivar el peso?" : "¿Está seguro que desea restaurar el peso?";
    
        if (confirm(mensajeConfirmacion)) {
            // Enviar petición AJAX para cambiar el estado del peso
            $.ajax({
                url: `api/peso/cambiarEstado_peso/${id}`, // Ruta al controlador que maneja el cambio de estado
                type: 'PUT',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content'), // Token CSRF
                    estado: nuevoEstado // Cambiar el estado a 0 o 1
                },
                success: function(response) {
                    // Mostrar mensaje de éxito
                    alert(response.message);
                    // Recargar la página o actualizar la lista de pesos
                    location.reload();
                    // loadContent('/peso');
                },
                error: function(xhr) {
                    // Mostrar mensaje de error
                    alert('Hubo un error al intentar ' + accion + ' el peso.');
                }
            });
        }
    }
    
    
// ---------------------CLASE

function enviarFormularioClase() {
    let form = document.getElementById('claseForm');
    let formData = new FormData(form);
    
    // Verifica si es un clase nuevo o existente
    let claseId = formData.get('id');  // Supongamos que el campo 'id' existe en el formulario para clases existentes
    let url = claseId ? `api/clase/${claseId}/actualizar` : 'api/clase/crear';
    // let method = claseId ? 'PUT' : 'POST';
    let method = 'POST';

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
        return response.text();  // Cambiamos a text() para ver el contenido completo
    })
    .then(data => {
        console.log('Response text:', data);
        try {
            let jsonData = JSON.parse(data);  // Intentamos convertir a JSON manualmente
            if (jsonData.success) {
                alert("Operación exitosa.");
                    // Cerrar modal
                const modal = bootstrap.Modal.getInstance(document.getElementById('claseModal'));
                modal.hide();
                document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());

                // Recargar contenido de main-content
                location.reload();
                // loadContent('/clase');
            } else {
                throw new Error(jsonData.error || 'Error al actualizar el área');
            }
        } catch (error) {
            console.error('Error parsing JSON:', error);
        }
    })
}
// Exportar la función globalmente
window.enviarFormularioClase = enviarFormularioClase;

    window.crearClase = function() {
        // Configuración para "Nuevo clase"
        document.getElementById('claseForm').action = 'api/clase/crear';
        document.getElementById('claseId').value = '';
        document.getElementById('clase').value = '';
        
        // Cambiar título y texto del botón
        document.getElementById('claseModalLabel').textContent = 'Nuevo clase';
        document.querySelector('.modal-footer .btn-primary').textContent = 'Crear clase';
    }
    
    window.editarClase = function(clase) {

        // Setea los valores del formulario con los datos del clase pasado desde la vista
        console,log("clase en editar");
        console.log(clase);
        document.getElementById('claseForm').action = `api/clase/${clase.id}/actualizar`;
        // Establece el método del formulario como POST (esto sigue siendo necesario)
        document.getElementById('claseForm').method = 'POST';
        if (!document.querySelector('input[name="_method"]')) {
            var inputMethod = document.createElement('input');
            inputMethod.setAttribute('type', 'hidden');
            inputMethod.setAttribute('name', '_method');
            inputMethod.setAttribute('value', 'POST');
            claseForm.appendChild(inputMethod);
        }
        document.getElementById('claseId').value = clase['id'];
        document.getElementById('clase').value = clase.clase;
        
        // Cambia el título y el texto del botón del modal
        document.getElementById('claseModalLabel').textContent = 'Editar clase';
        document.querySelector('.modal-footer .btn-primary').textContent = 'Actualizar clase';
    }

    window.cambiarEstadoClase = function(id, estado) {
        let accion = estado === 1 ? 'inactivar' : 'restaurar'; // Definir la acción basada en el estado actual
        let nuevoEstado = estado === 1 ? 0 : 1; // Si está activo, inactivarlo; si está inactivo, restaurarlo
        let mensajeConfirmacion = estado === 1 ? "¿Está seguro que desea inactivar el clase?" : "¿Está seguro que desea restaurar el clase?";
    
        if (confirm(mensajeConfirmacion)) {
            // Enviar petición AJAX para cambiar el estado del clase
            $.ajax({
                url: `api/clase/cambiarEstado_clase/${id}`, // Ruta al controlador que maneja el cambio de estado
                type: 'PUT',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content'), // Token CSRF
                    estado: nuevoEstado // Cambiar el estado a 0 o 1
                },
                success: function(response) {
                    // Mostrar mensaje de éxito
                    alert(response.message);
                    // Recargar la página o actualizar la lista de clases
                    location.reload();
                    // loadContent('/clase');
                },
                error: function(xhr) {
                    // Mostrar mensaje de error
                    alert('Hubo un error al intentar ' + accion + ' el clase.');
                }
            });
        }
    }
    
// -------------------CONTROL EXTINTOR

function enviarFormularioControlExtintor() {
    let form = document.getElementById('controlExtintorForm');
    let formData = new FormData(form);

    showLoading();

    formData.forEach((value, key) => {
        console.log(key, value);
    });

    let controlExtintorId = formData.get('controlExtintorId');
    let url = controlExtintorId ? `/api/control-extintores/${controlExtintorId}/actualizar` : '/api/controlExtintor/crear';
    let method = 'POST';


    fetch(url, {
        method: method,
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.text())
    .then(data => {
        let jsonData = JSON.parse(data);

        if (jsonData.ok) {
            showToast("Control guardado exitosamente", "success", () => {
                const modal = bootstrap.Modal.getInstance(document.getElementById('controlExtintorModal'));
                modal.hide();
                document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());
                location.reload();
            });
        } else {
            showToast("Error al guardar control: " + (jsonData.error || "Ocurrió un error"), "error");
        }
    })
    .catch(error => {
        console.error('Error en la petición:', error);
        showToast("Error en la conexión al servidor", "error");
    })
    .finally(() => {
        hideLoading();
    });
}

// Exportar la función globalmente
window.enviarFormularioControlExtintor = enviarFormularioControlExtintor;

window.crearControlExtintor = async function () {
    showLoading();

    try {
        document.getElementById("controlExtintorForm").reset();
        document.getElementById('controlExtintorId').value = '';

        const clienteId = window.idCliente;
        document.getElementById('cliente_control').value = clienteId;
        mostrarClienteComoTexto(clienteId);

        const urlParams = new URLSearchParams(window.location.search);
        const localidadId = urlParams.get('localidad');

        await cargarLocalidadesYSubareas(clienteId, localidadId, null);

        const myModal = new bootstrap.Modal(document.getElementById('controlExtintorModal'));
        document.getElementById('controlExtintorModalLabel').textContent = 'Nuevo Control';
        document.querySelector('.modal-footer #submit_control').textContent = 'Guardar Control';
        myModal.show();
    } catch (error) {
        console.error("Error al cargar el formulario:", error);
        showToast("Error al cargar el formulario.", "error");
    } finally {
        hideLoading();
    }
};

window.setValues = function(selectId, value) {
    console.log("value input "+value);
    
    console.log("select input");
    let select = document.getElementById(selectId);
    console.log(select);
    if (select) {
        let option = select.querySelector(`option[value="${value}"]`);
        if (option) {
            select.value = value;
        } else {
            console.warn(`Valor "${value}" no encontrado en el select ${selectId}`);
        }
    }
};

/*const subareaSelect = document.getElementById("subarea_control");
const areaInput = document.getElementById("area_control");

subareaSelect.addEventListener('change', function () {
    const selectedOption = this.options[this.selectedIndex];
    const nombreArea = selectedOption.getAttribute('data-nombre-area');
    areaInput.value = nombreArea || '';
});*/

function setFlatpickrMonthValue(selector, fecha) {
    const input = document.querySelector(selector);
    if (!input || !input._flatpickr) return;

    const instancia = input._flatpickr;

    const valor = fecha ? fecha.slice(0, 7) : new Date().toISOString().slice(0, 7);
    instancia.setDate(valor + "-01");
}

async function cargarLocalidadesYSubareas(clienteId, localidadId = null, subareaId = null) {
    const localidadSelect = document.getElementById("localidad_control");
    const subareaSelect = document.getElementById("subarea_control");
    const areaSelect = document.getElementById("area_control");

    const localidades = await fetch(`/api/localidad/cliente/${clienteId}`).then(res => res.json());
    localidadSelect.innerHTML = '<option value="" disabled selected>Seleccione una localidad</option>';
    localidades.data.forEach(localidad => {
        const option = document.createElement("option");
        option.value = localidad.id;
        option.textContent = localidad.nombre;
        localidadSelect.appendChild(option);
    });
    if (localidadId) localidadSelect.value = localidadId;

    if (!localidadId) return;

    const subareas = await fetch(`/api/localidad/subareas/${localidadId}`).then(res => res.json());

    const areaMap = new Map();
    const subareasPorArea = {};

    subareas.data.forEach(subarea => {
        const idArea = String(subarea.id_area);
        const nombreArea = subarea.nombre_area.toUpperCase();

        areaMap.set(idArea, nombreArea);
        if (!subareasPorArea[idArea]) {
            subareasPorArea[idArea] = [];
        }

        subareasPorArea[idArea].push({
            value: subarea.id_subarea,
            id_area: idArea,
            nombreSubarea: subarea.nombre,
            nombreArea: nombreArea,
            codigo: subarea.codigo?.toUpperCase() || '',
            clase: subarea.clase?.toUpperCase() || '',
            marca: subarea.marca?.toUpperCase() || '',
            peso: subarea.peso?.toUpperCase() || '',
            unidad: subarea.unidad?.toUpperCase() || ''
        });
    });

    areaSelect.innerHTML = '<option value="" disabled selected>Seleccione un área</option>';
    [...areaMap.entries()]
    .sort((a, b) => a[1].localeCompare(b[1]))
    .forEach(([id, nombre]) => {
        const option = document.createElement("option");
        option.value = id;
        option.textContent = nombre;
        areaSelect.appendChild(option);
    });

    if (areaSelect.tomselect) {
        areaSelect.tomselect.destroy();
    }

    const tomAreaSelect = new TomSelect(areaSelect, {
                placeholder: 'Seleccione un área',
                allowEmptyOption: true,
            });

    areaSelect.onchange = function () {
        const selectedAreaId = this.value;
        let subareaOptions = subareasPorArea[selectedAreaId] || [];
        subareaOptions = subareaOptions.sort((a, b) =>
            a.nombreSubarea.localeCompare(b.nombreSubarea)
        );

        document.getElementById("codigo_control").value = '';
        document.getElementById("clase").value = '';
        document.getElementById("marca").value = '';
        document.getElementById("peso_unidad").value = '';
        document.getElementById("peso").value = '';
        document.getElementById("unidad").value = '';

        if (subareaSelect.tomselect) subareaSelect.tomselect.destroy();
        subareaSelect.innerHTML = '';


        const tomSubareaSelect = new TomSelect(subareaSelect, {
            placeholder: 'Buscar y seleccionar ubicación',
            options: subareaOptions,
            valueField: 'value',
            labelField: 'nombreSubarea',
            searchField: ['nombreSubarea', 'codigo'],
            render: {
                option: function (data, escape) {
                    return `<div>${escape(data.nombreSubarea)}</div>`;
                },
                item: function (data, escape) {
                    return `<div>${escape(data.nombreSubarea)}</div>`;
                }
            },
            onChange: async function (value) {
                const selected = this.options[value];
                if (selected) {
                    const peso = selected.peso != null ? parseFloat(selected.peso).toString() : '';

                    document.getElementById("codigo_control").value = selected.codigo || '';
                    document.getElementById("clase").value = selected.clase || '';
                    document.getElementById("marca").value = selected.marca || '';
                    document.getElementById("peso_unidad").value = `${peso} ${selected.unidad || ''}`.trim();
                    document.getElementById("peso").value = peso;
                    document.getElementById("unidad").value = selected.unidad || '';
                    document.getElementById("area_control").value = selected.id_area || '';

                    if (usuarioCambioSubarea && selected.codigo) {
                        try {
                            showLoading();
                            const idCliente = window.idCliente;
                            const idLocalidad = document.getElementById('id_localidad')?.value || new URLSearchParams(window.location.search).get('localidad') || '';

                            const res = await fetch(`/api/control-extintor/ultimo?cliente=${idCliente}&localidad=${idLocalidad}&codigo=${selected.codigo}`);
                            const data = await res.json();

                            const idControlExtintor = $('#controlExtintorId').val();
                            if (data && data.id) {
                                await setValoresFormulario(data, idControlExtintor ? 'editar' :'nuevo');
                            }
                        } catch (error) {
                            console.error('Error cargando último control:', error);
                        } finally {
                            hideLoading();
                        }
                    }
                }
            }
        });

        //Se setea el subarea
        if (subareaId && subareaOptions.some(opt => opt.value == subareaId)) {
            usuarioCambioSubarea = false;
            tomSubareaSelect.setValue(subareaId);
            usuarioCambioSubarea = true;
        }
    };

    //Se setea el área
    if (subareaId) {
        for (const [areaId, subList] of Object.entries(subareasPorArea)) {
            if (subList.some(s => s.value == subareaId)) {
                //areaSelect.value = areaId;
                tomAreaSelect.setValue(areaId);
                areaSelect.dispatchEvent(new Event('change'));
                break;
            }
        }
    }
}

    function setValoresFormulario(control_extintores, modo = 'nuevo') {

        if (control_extintores.fecha_revision) {
            const input = document.querySelector('#fecha_revision');
            if (input && input._flatpickr) {
                input._flatpickr.setDate(control_extintores.fecha_revision);
            } else {
                input.value = control_extintores.fecha_revision;
            }
        }

        if (modo !== 'nuevo') {
            if (control_extintores.fecha_recarga) setFlatpickrMonthValue('#fecha_recarga', control_extintores.fecha_recarga);
            if (control_extintores.fecha_prox_recarga) setFlatpickrMonthValue('#fecha_proxima_recarga', control_extintores.fecha_prox_recarga);
            if (control_extintores.id) $('#controlExtintorId').val(control_extintores.id);
        }else{
            $('#controlExtintorId').val('');
        }

        if (control_extintores.codigo) $('#codigo_control').val(control_extintores.codigo);
        if (control_extintores.clase) $('#clase').val(control_extintores.clase);
        if (control_extintores.marca) $('#marca').val(control_extintores.marca);
        if (control_extintores.peso && control_extintores.unidad) {
            const pesoFormateado = parseFloat(control_extintores.peso).toString();
            $('#peso_unidad').val(`${pesoFormateado} ${control_extintores.unidad}`);
        }
        if (control_extintores.peso) $('#peso').val(control_extintores.peso);
        if (control_extintores.unidad) $('#unidad').val(control_extintores.unidad);
        if (control_extintores.id_mango) $('#mango').val(control_extintores.id_mango);
        if (control_extintores.id_mano) $('#mano').val(control_extintores.id_mano);
        if (control_extintores.id_seguro) $('#seguro').val(control_extintores.id_seguro);
        if (control_extintores.id_cilindro) $('#cilindro').val(control_extintores.id_cilindro);
        //if (control_extintores.fecha_recarga) $('#fecha_recarga').val(control_extintores.fecha_recarga);
        //if (control_extintores.fecha_prox_recarga) $('#fecha_proxima_recarga').val(control_extintores.fecha_prox_recarga);
        if (control_extintores.fecha_fabricacion) $('#fecha_fabricacion').val(control_extintores.fecha_fabricacion);
        if (control_extintores.fecha_prueba_hidrostatica) $('#fecha_prueba_hidrostatica').val(control_extintores.fecha_prueba_hidrostatica);
        if (control_extintores.id_area_seguridad) $('#area_seguridad').val(control_extintores.id_area_seguridad);
        if (control_extintores.id_instructivo_uso) $('#instructivo').val(control_extintores.id_instructivo_uso);
        if (control_extintores.id_instructivo_clasificacion) $('#instructivo_clasificacion').val(control_extintores.id_instructivo_clasificacion);
        if (control_extintores.id_flecha_ubicacion) $('#flecha_ubicacion').val(control_extintores.id_flecha_ubicacion);
        if (control_extintores.id_silueta) $('#silueta').val(control_extintores.id_silueta);
        if (control_extintores.id_rectangulo_instalacion) $('#rectangulo_instalacion').val(control_extintores.id_rectangulo_instalacion);
        if (control_extintores.id_hidrante) $('#hidrante').val(control_extintores.id_hidrante);
        if (control_extintores.dimension_vidrio) $('#dimension_vidrio').val(control_extintores.dimension_vidrio);
        if (control_extintores.novedad_marbete) $('#observaciones_marbete').val(control_extintores.novedad_marbete);
        if (control_extintores.observaciones) $('#observaciones').val(control_extintores.observaciones);

        // Observaciones generales (multi-select TomSelect)
        const tom = document.getElementById('observaciones_generales')?.tomselect;
        if (tom) {
            tom.clear(true);
            if (control_extintores.observaciones_nomenclatura?.length > 0){
                tom.setValue(control_extintores.observaciones_nomenclatura.map(item => String(item.id)));
            }
        }

        // Activar checkbox si fecha_fabricacion viene vacía o nula
        if (!control_extintores.fecha_fabricacion) {
        document.getElementById('omit_fecha_fabricacion').checked = true;
        }

        // Activar checkbox si fecha_prueba_hidrostatica viene vacía o nula
        if (!control_extintores.fecha_prueba_hidrostatica) {
            document.getElementById('omit_fecha_prueba_hidrostatica').checked = true;
        }


        actualizarEstadoDimensionVidrio();
        actualizarEstadoinput('omit_fecha_fabricacion', 'fecha_fabricacion');
        actualizarEstadoinput('omit_fecha_prueba_hidrostatica', 'fecha_prueba_hidrostatica');
    }

    async function mostrarFormularioControlExtintor(control_extintores, clienteId, modo = 'nuevo') {
        showLoading();

        try {
            document.getElementById("controlExtintorForm").reset();

            document.getElementById('cliente_control').value = clienteId;
            mostrarClienteComoTexto(clienteId);

            await cargarLocalidadesYSubareas(clienteId, control_extintores.id_cliente_localidad, control_extintores.id_subarea_empresa);

            setValoresFormulario(control_extintores, modo);

            const myModal = new bootstrap.Modal(document.getElementById('controlExtintorModal'));
            document.getElementById('controlExtintorModalLabel').textContent = (modo === 'editar') ? 'Editar Control' : 'Nuevo Control';
            document.querySelector('.modal-footer #submit_control').textContent = (modo === 'editar') ? 'Actualizar Control' : 'Guardar Control';
            myModal.show();
        } catch (error) {
            console.error("Error mostrando el formulario:", error);
            showToast("Ocurrió un error al cargar el formulario", "error");
        } finally {
            hideLoading();
        }
    }


    window.precargarControlExtintor = async function () {
        const control_extintores = window.ultimoControlExtintor;
        if (!control_extintores) return await crearControlExtintor();
        await mostrarFormularioControlExtintor(control_extintores, window.idCliente, 'nuevo');
    };

    window.editarControlExtintorCliente = async function (control_extintores, id_cliente) {
        await mostrarFormularioControlExtintor(control_extintores, id_cliente, 'editar');
    };

    /*function viewImage(image, marca, fecha) {
        // Get modal elements
        const modalImage = document.getElementById('modalImage');
        const modalTitle = document.getElementById('imageModalLabel');
        const downloadLink = document.getElementById('downloadImage');

        // Las imágenes viven en R2: llega una URL; se mantiene el base64 por compatibilidad
        const imageSrc = image.startsWith('http') ? image : `data:image/jpg;base64,${image}`;

        // Update modal content
        modalImage.src = imageSrc; // Set the image source
        modalTitle.textContent = `Marca: ${marca} | Fecha: ${fecha}`; // Update the modal title
        downloadLink.href = imageSrc; // Set the download link
        downloadLink.download = `imagen_${marca}_${fecha}.jpg`; // <- aquí defines nombre y extensión

        // Show the modal
        const imageModal = new bootstrap.Modal(document.getElementById('imageModal'));
        imageModal.show();
    }

    window.viewImage = viewImage;*/

    window.viewImageById = function(id) {
        showLoading();

        $.ajax({
            url: `/api/control-extintor/${id}/imagen`,
            type: 'GET',
            success: function(data) {
                // La imagen viene como URL del bucket R2; se mantiene base64 por compatibilidad
                const imageSrc = data.url ? data.url : `data:image/jpg;base64,${data.image}`;

                $('#modalImage').attr('src', imageSrc);
                $('#imageModalLabel').text(`Marca: ${data.marca} | Fecha: ${data.fecha}`);
                $('#downloadImage')
                    .attr('href', imageSrc)
                    .attr('download', `imagen_${data.marca}_${data.fecha}.jpg`);

                const imageModal = new bootstrap.Modal(document.getElementById('imageModal'));
                imageModal.show();
            },
            error: function(xhr) {
                showToast('No se pudo cargar la imagen del extintor', 'error');
            },
            complete: function() {
                hideLoading();
            }
        });
    };



    flatpickr(".flatpickr-month", {
        locale: Spanish,
        altInput: true,
        disableMobile: true,
        altFormat: "F Y",
        dateFormat: "Y-m",
        plugins: [
            new monthSelectPlugin({
                shorthand: false,
                dateFormat: "Y-m",
                altFormat: "F Y"
            })
        ],
        onChange: function (selectedDates, dateStr, instance) {
            if (dateStr.length === 7) {
                instance.input.value = dateStr + "-01";
            }
        }
    });

    flatpickr("#fecha_revision", {
        locale: Spanish,
        disableMobile: true,
        altInput: true,
        altFormat: "d F Y",
        dateFormat: "Y-m-d",
        defaultDate: new Date()
    });

    function mostrarClienteComoTexto(idCliente) {
        const select = document.getElementById('cliente_control');
        const input = document.getElementById('cliente_readonly');

        const selected = select.querySelector(`option[value="${idCliente}"]`);
        if (selected) {
            input.value = selected.textContent;
            input.classList.remove('d-none');
            select.classList.add('d-none');
        }
    }

    window.cambiarEstadoControl = function(id, estado) {
        let accion = estado === 1 ? 'inactivar' : 'restaurar';
        let nuevoEstado = estado === 1 ? 0 : 1;
        let mensaje = estado === 1 
            ? "¿Está seguro que desea inactivar el control?" 
            : "¿Está seguro que desea restaurar el control?";

        showConfirm(mensaje, () => {
            showLoading();
            $.ajax({
                url: `/api/control-extintores/cambiarEstado_control_extintores/${id}`,
                type: 'PUT',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    estado: nuevoEstado
                },
                success: function(response) {
                    hideLoading();
                    showToast(response.message, "success", () => location.reload());
                },
                error: function(xhr) {
                    hideLoading();
                    showToast('Hubo un error al intentar ' + accion + ' el control.', "error");
                }
            });
        });
    };


    window.cambiarEstadoClase = function(id, estado) {
        let accion = estado === 1 ? 'inactivar' : 'restaurar'; // Definir la acción basada en el estado actual
        let nuevoEstado = estado === 1 ? 0 : 1; // Si está activo, inactivarlo; si está inactivo, restaurarlo
        let mensajeConfirmacion = estado === 1 ? "¿Está seguro que desea inactivar el clase?" : "¿Está seguro que desea restaurar el clase?";

        if (confirm(mensajeConfirmacion)) {
            // Enviar petición AJAX para cambiar el estado del clase
            $.ajax({
                url: `api/clase/cambiarEstado_clase/${id}`, // Ruta al controlador que maneja el cambio de estado
                type: 'PUT',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content'), // Token CSRF
                    estado: nuevoEstado // Cambiar el estado a 0 o 1
                },
                success: function(response) {
                    // Mostrar mensaje de éxito
                    alert(response.message);
                    // Recargar la página o actualizar la lista de clases
                    location.reload();
                    // loadContent('/clase');
                },
                error: function(xhr) {
                    // Mostrar mensaje de error
                    alert('Hubo un error al intentar ' + accion + ' el clase.');
                }
            });
        }
    }

    // Seleccionar el modal, la imagen del modal y el enlace de descarga
    const hidranteSelect = document.getElementById("hidrante");

    function actualizarEstadoDimensionVidrio() {
        const hidranteSelect = document.getElementById("hidrante");
        const dimensionInput = document.getElementById("dimension_vidrio");

        if (!hidranteSelect || !dimensionInput) return;

        const texto = hidranteSelect.options[hidranteSelect.selectedIndex].text.trim().toLowerCase();
        if (texto === "no") {
            dimensionInput.readOnly = true;
            dimensionInput.required = false;
            dimensionInput.value = "";
            dimensionInput.classList.add('bg-light', 'text-muted');
        } else {
            dimensionInput.readOnly = false;
            dimensionInput.required = true;
            dimensionInput.classList.remove('bg-light', 'text-muted');
        }
    }


    hidranteSelect.addEventListener("change",actualizarEstadoDimensionVidrio);

    actualizarEstadoDimensionVidrio();

    // Evento para cambiar cliente y cargar localidades
    document.getElementById("cliente_control").addEventListener("change", function() {
        let clienteId = this.value;
        if (clienteId) {
            fetch(`/api/localidad/cliente/${clienteId}`)
                .then(response => response.json())
                .then(response => {
                    let localidadSelect = document.getElementById("localidad_control");
                    localidadSelect.innerHTML = '<option value="" disabled selected>Seleccione una localidad</option>';
                    response.data.forEach(localidad => {
                        let option = document.createElement("option");
                        option.value = localidad.id;
                        option.textContent = localidad.nombre;
                        localidadSelect.appendChild(option);
                    });
                })
                .catch(error => console.error('Error al cargar localidades:', error));
        }
    });

    // Evento para cambiar localidades y cargar subareas
    document.getElementById("localidad_control").addEventListener("change", function() {
        let localidadId = this.value;
        if (localidadId) {
            fetch(`/api/localidad/subareas/${localidadId}`)
            .then(response => response.json())
            .then(response => {
                let localidadSelect = document.getElementById("subarea_control");
                localidadSelect.innerHTML = '<option value="" disabled selected>Seleccione una sub-area</option>';
                response.data.forEach(subarea => {
                    let option = document.createElement("option");
                    option.value = subarea.id;
                    option.textContent = subarea.nombre;
                    option.setAttribute("data-abreviatura", subarea.codigo.toUpperCase()); // Agregar abreviatura
                    localidadSelect.appendChild(option);
                });
            })
            .catch(error => console.error('Error al cargar subareas:', error));
        }
    });

    const actualizarEstadoinput = (checkboxId, inputId) => {
        const checkbox = document.getElementById(checkboxId);
        const input = document.getElementById(inputId);

        if (!checkbox.checked) {
            input.classList.remove('bg-light', 'text-muted');
            input.removeAttribute('readonly');
            input.setAttribute('required', 'required');
        } else {
            input.value = '';
            input.classList.add('bg-light', 'text-muted');
            input.setAttribute('readonly', 'readonly');
            input.removeAttribute('required');
        }
    }


    const toggleInput = (checkboxId, inputId) => {
        const checkbox = document.getElementById(checkboxId);
        const input = document.getElementById(inputId);

        checkbox.addEventListener('change', () => {
            actualizarEstadoinput(checkboxId, inputId);
        });

        if (input.value) {
            checkbox.checked = true;
        } else {
            checkbox.checked = false;
        }

        actualizarEstadoinput(checkboxId, inputId);
    };

    toggleInput('omit_fecha_fabricacion', 'fecha_fabricacion');
    toggleInput('omit_fecha_prueba_hidrostatica', 'fecha_prueba_hidrostatica');


    document.querySelectorAll('.tom-select').forEach(select => {
        const tomSelect = new TomSelect(select, {
            plugins: ['remove_button'],
            placeholder: 'Seleccione una o más opciones',
        });
        tomSelect.wrapper.classList.remove('multi');
    });

    window.updateQueryParam = function (param, value) {
        const url = new URL(window.location.href);
        if (value) {
            url.searchParams.set(param, value);
        } else {
            url.searchParams.delete(param);
        }
        // Mostrar loading antes de redirigir
        window.showLoading();

        // Pequeña demora opcional para que se vea el loading
        setTimeout(() => {
            window.location.href = url.toString();
        }, 100);
    };

    window.exportarExcel = async function (idCliente) {
          window.showLoading();

          // 1. Construir URL con params
          const urlActual = new URL(window.location.href);
          const currentParams = urlActual.searchParams;
          const exportUrl = new URL('/api/control-extintores/export', window.location.origin);
          const exportParams = new URLSearchParams();

          currentParams.forEach((value, key) => {
            exportParams.set(key, value);
          });
          if (idCliente) {
            exportParams.set('id_cliente', idCliente);
          }
          document.querySelectorAll('.toggle-col:checked').forEach(chk => {
            if (chk.value === 'acciones') return;
            exportParams.append('columns[]', chk.value);
          });
          exportUrl.search = exportParams.toString();

          try {
            // 2. Petición y obtención de blob
            const response = await fetch(exportUrl.toString(), { credentials: 'include' });
            if (!response.ok) {
              throw new Error(`Error en la exportación: ${response.status} ${response.statusText}`);
            }
            const blob = await response.blob();

            // 3. Extraer filename del header
            let filename = 'export.xlsx';
            const disposition = response.headers.get('Content-Disposition') || '';
            const filenameMatch = disposition.match(/filename\*?=(?:UTF-8'')?["']?([^;"']+)["']?/i);
            if (filenameMatch) {
              filename = decodeURIComponent(filenameMatch[1]);
            }

            // 4. Descargar usando blob URL
            const blobUrl = URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = blobUrl;
            a.download = filename;
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
            URL.revokeObjectURL(blobUrl);

          } catch (err) {
            console.error(err);
            alert('Ocurrió un error al exportar.');
          } finally {
            // 5. Ocultar loading
            window.hideLoading();
          }
    };

    document.getElementById('formEnviarCorreo').addEventListener('submit', function (e) {
        e.preventDefault();

        const emailsRaw = document.getElementById('emails').value;

        const emails = emailsRaw
            .split(',')
            .map(email => email.trim())
            .filter(email => email !== '');

        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

        // Validar que todos los correos sean válidos
        const invalidEmails = emails.filter(email => !emailRegex.test(email));

        if (emails.length === 0 || invalidEmails.length > 0) {
            const message = invalidEmails.length > 0
                ? `Correo inválido: ${invalidEmails.join(', ')}`
                : 'Debe ingresar al menos un correo válido.';
            window.showToast(message, 'error');
            return;
        }

        modalHelper.close('modalEnviarCorreo');

        window.showConfirm(
            '¿Desea enviar el reporte de extintores por correo?',
            () => {
                window.showLoading();

                const form = document.getElementById('formEnviarCorreo');
                const url = `/api/control-extintores/enviar-reporte`;
                const method = 'POST';

                const payload = {
                    emails: emails
                };

                // hidden inputs
                const hiddenInputs = form.querySelectorAll('input[type="hidden"]');
                hiddenInputs.forEach(input => {
                    if (input.id) {
                        payload[input.id] = input.value;
                    }
                });

                // parámetros de la URL
                const urlParams = new URLSearchParams(window.location.search);
                urlParams.forEach((value, key) => {
                    payload[key] = value;
                });

                // columnas seleccionadas
                const columns = [];
                document.querySelectorAll('.toggle-col:checked').forEach(chk => {
                    if (chk.value === 'acciones') return;
                    columns.push(chk.value);
                });
                payload.columns = columns;

                fetch(url, {
                    method: method,
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify(payload)
                })
                    .then(res => res.json())
                    .then(data => {
                        window.showToast(data.message, 'success');
                    })
                    .catch(err => {
                        console.error(err);
                        window.showToast('Error al enviar el correo.', 'error');
                    })
                    .finally(() => {
                        window.hideLoading();
                    });
            },
            () => {
                modalHelper.open('modalEnviarCorreo');
            }
        );
    });

    const fechaRecargaInput = document.getElementById('fecha_recarga');
    const fechaProximaRecargaInput = document.getElementById('fecha_proxima_recarga');

    const proxRecargaPicker = flatpickr(fechaProximaRecargaInput, {
        plugins: [new monthSelectPlugin({
            shorthand: false,
            dateFormat: "Y-m-d",   // formato a enviar
            altFormat: "F Y",      // formato visible
            theme: "light"
        })],
        disableMobile: true,
        altInput: true,
        locale: 'es'
    });

    flatpickr(fechaRecargaInput, {
        plugins: [new monthSelectPlugin({
            shorthand: false,
            dateFormat: "Y-m-d",
            altFormat: "F Y",
            theme: "light"
        })],
        disableMobile: true,
        altInput: true,
        locale: 'es',
        onChange: function (selectedDates) {
            if (selectedDates.length > 0) {
                const fecha = selectedDates[0];
                const fechaSumada = new Date(fecha.getFullYear() + 1, fecha.getMonth(), 1);
                proxRecargaPicker.setDate(fechaSumada, true);
            }
        }
    });
});