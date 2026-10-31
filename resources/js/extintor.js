import monthSelectPlugin from "flatpickr/dist/plugins/monthSelect";
import { Spanish } from "flatpickr/dist/l10n/es.js";

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

/*async function cargarLocalidadesYSubareas(clienteId, localidadId = null, subareaId = null) {
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

    if (localidadId) {
        const subareas = await fetch(`/api/localidad/subareas/${localidadId}`).then(res => res.json());

        const areaMap = new Map();
        areaSelect.innerHTML = '<option value="" disabled selected>Seleccione una área</option>';

        const optgroups = {}; // clave: id_area
        const optgroupLabels = {}; // para mostrar nombre_area

        subareas.data.forEach(subarea => {
            const idArea = String(subarea.id_area);
            const nombreArea = subarea.nombre_area.toUpperCase();

            if (!optgroups[idArea]) {
                optgroups[idArea] = [];
                optgroupLabels[idArea] = nombreArea;

                const areaOption = document.createElement("option");
                areaOption.value = idArea;
                areaOption.textContent = nombreArea;
                areaSelect.appendChild(areaOption);
            }

            optgroups[idArea].push({
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

        const subareaOptions = Object.values(optgroups).flat();

        if (subareaSelect.tomselect) subareaSelect.tomselect.destroy();
        subareaSelect.innerHTML = '';

        new TomSelect(subareaSelect, {
            placeholder: 'Buscar y seleccionar sub-área',
            options: subareaOptions,
            optgroups: Object.keys(optgroups).map(idArea => ({
                value: idArea,
                label: optgroupLabels[idArea]
            })),
            optgroupField: 'nombreArea',
            valueField: 'value',
            labelField: 'nombreSubarea',
            //searchField: ['nombreSubarea', 'codigo', 'clase', 'marca', 'nombreArea'],
            searchField: ['nombreSubarea', 'codigo', 'nombreArea'],
            render: {
                option: function (data, escape) {
                    return `<div>${escape(data.nombreSubarea)}</div>`;
                },
                item: function (data, escape) {
                    return `<div>${escape(data.nombreSubarea)}</div>`;
                }
            },
            onChange: function (value) {
                const selected = this.options[value];
                if (selected) {
                    document.getElementById("codigo_control").value = selected.codigo || '';
                    document.getElementById("clase").value = selected.clase || '';
                    document.getElementById("marca").value = selected.marca || '';
                    document.getElementById("peso_unidad").value = `${selected.peso || ''} ${selected.unidad || ''}`;
                    document.getElementById("peso").value = selected.peso || '';
                    document.getElementById("unidad").value = selected.unidad || '';
                    document.getElementById("area_control").value = selected.id_area || '';
                }
            }
        });

        if (subareaId) {
            const tom = subareaSelect.tomselect;
            tom.setValue(subareaId);
        }
    } else {
        areaSelect.innerHTML = '<option value="" disabled selected>Seleccione una área</option>';
        if (subareaSelect.tomselect) subareaSelect.tomselect.destroy();
        subareaSelect.innerHTML = '<option value="" disabled selected>Seleccione una sub-área</option>';
    }
}*/

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
    areaMap.forEach((nombre, id) => {
        const option = document.createElement("option");
        option.value = id;
        option.textContent = nombre;
        areaSelect.appendChild(option);
    });

    areaSelect.onchange = function () {
        const selectedAreaId = this.value;
        const subareaOptions = subareasPorArea[selectedAreaId] || [];

        document.getElementById("codigo_control").value = '';
        document.getElementById("clase").value = '';
        document.getElementById("marca").value = '';
        document.getElementById("peso_unidad").value = '';
        document.getElementById("peso").value = '';
        document.getElementById("unidad").value = '';

        if (subareaSelect.tomselect) subareaSelect.tomselect.destroy();
        subareaSelect.innerHTML = '';

        const tomSubareaSelect = new TomSelect(subareaSelect, {
            placeholder: 'Buscar y seleccionar sub-área',
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
            onChange: function (value) {
                const selected = this.options[value];
                if (selected) {
                    document.getElementById("codigo_control").value = selected.codigo || '';
                    document.getElementById("clase").value = selected.clase || '';
                    document.getElementById("marca").value = selected.marca || '';
                    document.getElementById("peso_unidad").value = `${selected.peso || ''} ${selected.unidad || ''}`;
                    document.getElementById("peso").value = selected.peso || '';
                    document.getElementById("unidad").value = selected.unidad || '';
                    document.getElementById("area_control").value = selected.id_area || '';
                }
            }
        });

        //Se setea el subarea
        if (subareaId && subareaOptions.some(opt => opt.value == subareaId)) {
            tomSubareaSelect.setValue(subareaId);
        }
    };

    //Se setea el área
    if (subareaId) {
        for (const [areaId, subList] of Object.entries(subareasPorArea)) {
            if (subList.some(s => s.value == subareaId)) {
                areaSelect.value = areaId;
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

        if (control_extintores.fecha_fabricacion) setFlatpickrMonthValue('#fecha_recarga', control_extintores.fecha_fabricacion);
        if (control_extintores.fecha_prueba_hidrostatica) setFlatpickrMonthValue('#fecha_proxima_recarga', control_extintores.fecha_prueba_hidrostatica);

        if (modo !== 'nuevo') {
            if (control_extintores.id) $('#controlExtintorId').val(control_extintores.id);
        }else{
            $('#controlExtintorId').val('');
        }

        if (control_extintores.codigo) $('#codigo_control').val(control_extintores.codigo);
        if (control_extintores.clase) $('#clase').val(control_extintores.clase);
        if (control_extintores.marca) $('#marca').val(control_extintores.marca);
        if (control_extintores.peso && control_extintores.unidad) $('#peso_unidad').val(`${control_extintores.peso} ${control_extintores.unidad}`);
        if (control_extintores.peso) $('#peso').val(control_extintores.peso);
        if (control_extintores.unidad) $('#unidad').val(control_extintores.unidad);
        if (control_extintores.id_mango) $('#mango').val(control_extintores.id_mango);
        if (control_extintores.id_mano) $('#mano').val(control_extintores.id_mano);
        if (control_extintores.id_seguro) $('#seguro').val(control_extintores.id_seguro);
        if (control_extintores.id_cilindro) $('#cilindro').val(control_extintores.id_cilindro);
        if (control_extintores.fecha_recarga) $('#fecha_recarga').val(control_extintores.fecha_recarga);
        if (control_extintores.fecha_prox_recarga) $('#fecha_proxima_recarga').val(control_extintores.fecha_prox_recarga);
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
        if (control_extintores.observaciones_nomenclatura?.length > 0) {
            const tom = document.getElementById('observaciones_generales')?.tomselect;
            if (tom) {
                tom.clear(true);
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


    flatpickr(".flatpickr-month", {
        locale: Spanish,
        altInput: true,
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
    const imageModal = document.getElementById('imageModal');
    const modalImage = document.getElementById('modalImage');
    const downloadLink = document.getElementById('downloadImage');
    const modalTitle = document.getElementById('imageModalLabel'); // Seleccionar el título del modal
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


    // Evento al abrir el modal
    if(!imageModal) return;
    imageModal.addEventListener('show.bs.modal', function (event) {
        // Botón que activó el modal
        const button = event.relatedTarget;

        // Obtener la imagen en base64 y los datos adicionales
        const imageBase64 = button.getAttribute('data-image');
        const marca = button.getAttribute('data-marca'); // Asegúrate de tener este dato en el botón
        const fecha = button.getAttribute('data-fecha'); // Asegúrate de tener este dato en el botón

        // Configurar el nombre del archivo para descargar
        const nombreArchivo = `Control_extintor_${marca} - ${fecha}.jpg`; // Cambia la extensión si es necesario
        modalTitle.textContent = `Control_extintor ${marca} - ${fecha}`;

        // Mostrar la imagen en el src del <img>
        modalImage.src = `data:image/jpeg;base64,${imageBase64}`; // Ajustar MIME si es necesario

        // Configurar el enlace de descarga
        downloadLink.href = `data:image/jpeg;base64,${imageBase64}`; // Ajustar MIME si es necesario
        downloadLink.download = nombreArchivo;
    });

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

        if (checkbox.checked) {
            input.value = '';
            input.classList.add('bg-light', 'text-muted');
            input.setAttribute('readonly', 'readonly');
            input.removeAttribute('required');
        } else {
            input.classList.remove('bg-light', 'text-muted');
            input.removeAttribute('readonly'); 
            input.setAttribute('required', 'required');
        }
    }

    const toggleInput = (checkboxId, inputId) => {
        const checkbox = document.getElementById(checkboxId);
        const input = document.getElementById(inputId);

        checkbox.addEventListener('change', function () {
            actualizarEstadoinput(checkboxId, inputId);
        });

        // Ejecutar inicialmente según el estado del checkbox
        actualizarEstadoinput(checkboxId, inputId);
    };

    toggleInput('omit_fecha_fabricacion', 'fecha_fabricacion');
    toggleInput('omit_fecha_prueba_hidrostatica', 'fecha_prueba_hidrostatica');


    document.querySelectorAll('.tom-select').forEach(select => {
        new TomSelect(select, {
            plugins: ['remove_button'],
            placeholder: 'Seleccione una o más opciones',
        });
    });

});