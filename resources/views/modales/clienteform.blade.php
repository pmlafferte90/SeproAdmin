<div class="modal fade" id="clientModal" tabindex="-1" aria-labelledby="clientModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-light">
                <h5 class="modal-title" id="clientModalLabel">Cliente</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="clientForm">
                @csrf
                <input type="hidden" id="clientId" name="id">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="empresa" class="form-label fw-semibold">Empresa</label>
                        <select class="form-select shadow-sm" id="empresa" name="id_empresa" required>
                            <option value="" selected disabled>Seleccione una empresa</option>
                            @foreach($empresas as $empresa)
                            <option value="{{ $empresa->id }}">{{ $empresa->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="razon_social" class="form-label fw-semibold">Nombre o Razón Social</label>
                        <input type="text" class="form-control shadow-sm" id="razon_social" name="razon_social" required>
                    </div>
                    <div class="mb-3">
                        <label for="ruc" class="form-label fw-semibold">RUC</label>
                        <input type="text" class="form-control shadow-sm" id="ruc" name="ruc" required minlength="10" maxlength="13" pattern="[0-9]{10,13}">
                    </div>
                    <div class="mb-3">
                        <label for="telefono" class="form-label fw-semibold">Teléfono</label>
                        <input type="text" class="form-control shadow-sm" id="telefono" name="telefono" required maxlength="10" pattern="[0-9]{10}">
                    </div>
                    <div class="mb-3">
                        <label for="correo" class="form-label fw-semibold">Correo</label>
                        <input type="email" class="form-control shadow-sm" id="correo" name="correo" required>
                    </div>
                    <div class="mb-3" id="localidadesform">
                        <hr>
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span class="fw-semibold">Localidades</span>
                            <button type="button" class="btn btn-success" id="addLocalidadBtn">+</button>
                        </div>

                        <!-- Tabla para las localidades -->
                        <table class="table mt-3" id="localidadTable">
                            <thead>
                                <tr>
                                    <th>Nombre</th>
                                    <th>Dirección</th>
                                    <th>Ubicación</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Las filas se agregarán dinámicamente -->
                            </tbody>
                        </table>
                    </div>
                </div>
                @include('componentes.modal-footer', [
                'closeText'  => 'Cerrar',
                'submitText' => 'Guardar', 
                'submitId'   => 'submitButton'
                ])
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        const localidadSection = document.getElementById('localidadesform');
        const clientIdInput = document.getElementById('clientId');
        const clientModal = document.getElementById('clientModal');

        // Función para verificar si es edición o creación
        function verificarModoFormulario() {
            if (clientIdInput.value) {
                localidadSection.style.display = "none"; // Ocultar en edición
            } else {
                localidadSection.style.display = "block"; // Mostrar en creación
            }
        }

        // Ejecutar la verificación cuando el modal se abre
        clientModal.addEventListener('show.bs.modal', verificarModoFormulario);
    });

    document.getElementById('addLocalidadBtn').addEventListener('click', function () {
        const table = document.querySelector('#localidadTable tbody');
        const newRow = document.createElement('tr');

        newRow.innerHTML = `
            <td><input type="text" class="form-control shadow-sm" name="nombrelocalidad[]" required></td>
            <td><input type="text" class="form-control shadow-sm" name="direccion[]" required></td>
            <td><input type="text" class="form-control shadow-sm" name="ubicacion[]" required></td>
            <td><button type="button" class="btn btn-danger btn-sm deleteLocalidadBtn">Eliminar</button></td>
        `;

        table.appendChild(newRow);

        // Agregar evento al botón eliminar de la fila recién agregada
        newRow.querySelector('.deleteLocalidadBtn').addEventListener('click', function () {
            newRow.remove(); // Elimina la fila correctamente
        });
    });
</script>

