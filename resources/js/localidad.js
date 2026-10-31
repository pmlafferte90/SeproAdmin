document.addEventListener("DOMContentLoaded", function () {
    // Función para abrir modal de nueva localidad
    window.nuevaLocalidad = function (id_cliente) {
        document.getElementById("localidadForm").reset();
        document.getElementById("localidadId").value = "";
        document.getElementById("id_cliente").value = id_cliente;
        document.getElementById("localidadModalLabel").textContent = "Nueva Localidad";
        document.getElementById("submitLocalidad").textContent = "Guardar";

        var myModal = new bootstrap.Modal(document.getElementById("localidadModal"));
        myModal.show();
    }

    // Función para abrir modal de editar localidad
    window.editarLocalidad = function (id_localidad) {
        $.ajax({
            url: '/api/localidad/' + id_localidad, // Asegúrate de tener esta ruta en tu backend
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

                    var myModal = new bootstrap.Modal(document.getElementById("localidadModal"));
                    myModal.show();
                } else {
                    alert('Error al obtener datos de la localidad.');
                }
            },
            error: function () {
                alert('Error en la petición.');
            }
        });
    }

    window.eliminarLocalidad = function(localidadId, estadoActual) {
        // Confirmación antes de proceder
        // Definir el nuevo estado
        let nuevoEstado = estadoActual == 1 ? 0 : 1;
        let confirmacion =  estadoActual == 1 ? '¿Está seguro que desea inactivar la localidad?' : '¿Está seguro que desea reactivar la localidad?';
        if (!confirm(confirmacion)) {
            return;
        }
       // Datos a enviar en la petición
       let data = {
           estado: nuevoEstado
       };
       
       // Realizar la petición fetch
       fetch(`/api/localidad/actualizar/${localidadId}`, {
           method: 'POST',
           headers: {
               'Content-Type': 'application/json',
               'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
           },
           body: JSON.stringify(data)
       })
       .then(response => response.json())
       .then(result => {
           if (result.ok) {
               alert('Localidad actualizado con éxito');
               location.reload(); // Recargar la página para reflejar los cambios
           } else {
               alert('Error al actualizar la localidad: ' + result.message);
           }
       })
       .catch(error => {
           console.error('Error:', error);
           alert('Ocurrió un error inesperado');
       });
   }

    // Evento para el formulario de guardar localidad
    let localidadForm = document.getElementById("localidadForm");
    if (!localidadForm) return; // Si no existe, no ejecuta el código y evita el error.
    
    localidadForm.addEventListener("submit", function (e) {        e.preventDefault();        event.preventDefault();

        let id_localidad = document.getElementById("localidadId").value;
        let formData = new FormData(this);
        let url = id_localidad ? '/api/localidad/actualizar/' + id_localidad : '/api/localidad/crear';
        let method = 'POST';

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
                    alert(id_localidad ? "Localidad actualizada" : "Localidad creada");
                    location.reload(); // Recargar para mostrar cambios
                } else {
                    alert("Hubo un error al guardar la localidad. " + data.error);
                }
            })
            .catch(error => console.error("Error:", error));
    });
});
