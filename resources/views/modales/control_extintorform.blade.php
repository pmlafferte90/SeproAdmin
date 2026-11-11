<div class="modal fade"
    id="controlExtintorModal"
    tabindex="-1"
    data-bs-focus="false"
    aria-labelledby="controlExtintorModalLabel"
    aria-hidden="true"
    data-bs-backdrop="static"
    data-bs-keyboard="false">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="controlExtintorModalLabel">@{{ modalTitle }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="controlExtintorForm" enctype="multipart/form-data" onsubmit="event.preventDefault(); enviarFormularioControlExtintor();">
                @csrf

                <div class="modal-body">
                    <input type="hidden" id="controlExtintorId" name="controlExtintorId" value="{{ $controlExtintor->id ?? '' }}">
                   <input type="hidden"
                         id="id_usuario"
                         name="id_usuario"
                         value="{{ auth()->id() }}">
                    <!-- Cliente, Localidad, Fecha -->
                    <fieldset class="border p-3 rounded-2 mb-3">
                        <div class="row mb-3 g-3">
                            <div class="col-md-12">
                                <div class="d-flex align-items-center">
                                    <label for="cliente_control" class="form-label fw-bold me-1 mb-0" style="min-width: 55px;">Cliente:</label>
                                    <input style="background-color: #e6f0dd;" type="text" class="form-control-plaintext text-center fw-bold rounded py-0 mb-1 border shadow-sm" id="cliente_readonly" readonly>
                                    <select class="form-control d-none" name="cliente_control" id="cliente_control" required style="flex: 1;">
                                        <option value="" selected disabled>Seleccione un cliente</option>
                                        @foreach($clientes as $cliente)
                                            <option value="{{ $cliente->id }}">{{ $cliente->razon_social }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label for="localidad_control" class="form-label fw-bold mb-1">Localidad:</label>
                                <select name="localidad_control" id="localidad_control" class="form-control bg-light text-muted shadow-sm" required style="pointer-events: none;">
                                    <option value="" disabled selected>Seleccione una localidad</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="fecha_revision" class="form-label fw-bold mb-1">Fecha de revisión:</label>
                                <input type="text" id="fecha_revision" name="fecha_revision" class="form-control shadow-sm flatpickr" placeholder="Selecciona una fecha" required>
                            </div>
                        </div>
                    </fieldset>

                    <!-- Área, Ubicación, Código, Clase, Marca, Peso -->
                    <fieldset class="border p-3 rounded mb-3">
                        <div class="row mb-3 g-3">
                            <div class="col-md-6">
                                <label for="area_control" class="form-label fw-bold mb-1">Área:</label>
                                <!-- <input type="text"
                                    class="form-control bg-light text-muted shadow-sm"
                                    id="area_control"
                                    name="area_control"
                                    readonly
                                    placeholder="Área" required> -->
                                <select class="form-control shadow-sm" name="area_control" id="area_control" required>
                                    <option value="" disabled selected>Seleccione un área</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="subarea_control" class="form-label fw-bold mb-1">Ubicación:</label>
                                <select class="form-control shadow-sm" name="subarea_control" id="subarea_control" required>
                                    <option value="" selected disabled>Seleccione una subárea</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-bold mb-1">Código:</label>
                                <input type="text" class="form-control bg-light text-muted shadow-sm" id="codigo_control" name="codigo_control" required readonly placeholder="Código">
                            </div>
                            <div class="col-md-3">
                                <label for="clase" class="form-label fw-bold mb-1">Clase:</label>
                                <input type="text" class="form-control bg-light text-muted shadow-sm" name="clase" id="clase" required readonly placeholder="Clase">
                            </div>
                            <div class="col-md-3">
                                <label for="marca" class="form-label fw-bold mb-1">Marca:</label>
                                <input type="text" class="form-control bg-light text-muted shadow-sm" name="marca" id="marca" required readonly placeholder="Marca">
                            </div>
                            <div class="col-md-3">
                                <label for="peso_unidad" class="form-label fw-bold mb-1">Peso:</label>
                                <input type="text" class="form-control bg-light text-muted shadow-sm" name="peso_unidad" id="peso_unidad" required readonly placeholder="Peso">
                            </div>
                            <input type="hidden" class="form-control bg-light text-muted shadow-sm" name="peso" id="peso" readonly placeholder="Peso">
                            <input type="hidden" class="form-control bg-light text-muted shadow-sm" name="unidad" id="unidad" readonly placeholder="Unidad">
                        </div>
                    </fieldset>

                    <!-- Revisión del extintor -->
                    <div style="background-color: #fff3cd;" class="text-center fw-bold py-0 mb-1 rounded">
                        REVISIÓN DEL EXTINTOR
                    </div>
                    <fieldset class="border p-3 rounded mb-3">
                        <div class="row mb-3 g-3">
                            <div class="col-md-3">@include('componentes.select_nomenclatura', ['label' => 'Manguera', 'name' => 'mango', 'tipoFiltro' => 'MANGUERA', 'required' => true])</div>
                            <div class="col-md-3">@include('componentes.select_nomenclatura', ['label' => 'Manómetro', 'name' => 'mano', 'tipoFiltro' => 'MANOMETRO', 'required' => true])</div>
                            <div class="col-md-3">@include('componentes.select_nomenclatura', ['label' => 'Seguro', 'name' => 'seguro', 'tipoFiltro' => 'SEGURO', 'required' => true])</div>
                            <div class="col-md-3">@include('componentes.select_nomenclatura', ['label' => 'Cilindro', 'name' => 'cilindro', 'tipoFiltro' => 'CILINDRO', 'required' => true])</div>
                        </div>
                        <div class="row mb-3 g-3">
                            <div class="col-md-6">
                                <label for="fecha_recarga" class="form-label fw-bold mb-1">Fecha de Recarga:</label>
                                <input type="text" id="fecha_recarga" name="fecha_recarga" class="form-control shadow-sm flatpickr-month" placeholder="Selecciona mes y año" required>
                            </div>
                            <div class="col-md-6">
                                <label for="fecha_proxima_recarga" class="form-label fw-bold mb-1">Fecha de Próxima Recarga:</label>
                                <input type="text" id="fecha_proxima_recarga" name="fecha_proxima_recarga" class="form-control shadow-sm flatpickr-month" placeholder="Selecciona mes y año" required>
                            </div>
                        </div>
                    </fieldset>

                    <fieldset class="border p-3 rounded mb-3">
                        <div class="row mb-3 g-3">
                            <div class="col-md-6">
                                <label for="fecha_fabricacion" class="form-label fw-bold mb-1">
                                    Fecha de Fabricación:
                                    <input type="checkbox" id="omit_fecha_fabricacion" class="form-check-input ms-2" title="Omitir este campo">
                                </label>
                                <input type="date" class="form-control shadow-sm" id="fecha_fabricacion" name="fecha_fabricacion" required>
                            </div>
                            <div class="col-md-6">
                                <label for="fecha_prueba_hidrostatica" class="form-label fw-bold mb-1">
                                    Fecha de Prueba Hidrostática:
                                    <input type="checkbox" id="omit_fecha_prueba_hidrostatica" class="form-check-input ms-2" title="Omitir este campo">
                                </label>
                                <input type="date" class="form-control shadow-sm" id="fecha_prueba_hidrostatica" name="fecha_prueba_hidrostatica" required>
                            </div>
                        </div>
                    </fieldset>

                    <!-- Revisión de señalización -->
                    <div style="background-color: #d9e2f3;" class="text-center fw-bold py-0 mb-1 rounded">
                        REVISIÓN DE SEÑALIZACIÓN
                    </div>
                    <fieldset class="border p-3 rounded mb-3">
                        <div class="row mb-3 g-3">
                            <div class="col-md-4">@include('componentes.select_nomenclatura', ['label' => 'Área de Seguridad', 'name' => 'area_seguridad', 'tipoFiltro' => 'AREA-SEGURIDAD'])</div>
                            <div class="col-md-4">@include('componentes.select_nomenclatura', ['label' => 'Instructivo de Uso', 'name' => 'instructivo', 'tipoFiltro' => 'INSTRUCTIVO-USO'])</div>
                            <div class="col-md-4">@include('componentes.select_nomenclatura', ['label' => 'Instructivo de Clasificación', 'name' => 'instructivo_clasificacion', 'tipoFiltro' => 'INSTRUCTIVO-CLASIFICACIÓN'])</div>
                        </div>
                        <div class="row mb-3 g-3">
                            <div class="col-md-4">@include('componentes.select_nomenclatura', ['label' => 'Flecha de Ubicación', 'name' => 'flecha_ubicacion', 'tipoFiltro' => 'FLECHA-UBICACIÓN'])</div>
                            <div class="col-md-4">@include('componentes.select_nomenclatura', ['label' => 'Silueta de Extintor', 'name' => 'silueta', 'tipoFiltro' => 'SILUETA-EXTINTOR'])</div>
                            <div class="col-md-4">@include('componentes.select_nomenclatura', ['label' => 'Rectángulo de Instalación', 'name' => 'rectangulo_instalacion', 'tipoFiltro' => 'RECTANGULO'])</div>
                        </div>
                    </fieldset>

                    <fieldset class="border p-3 rounded mb-3">
                        <div class="row mb-3 g-3">
                            <div class="col-md-6">@include('componentes.select_nomenclatura', ['label' => 'Extintor En Gabinete De Hidrante', 'name' => 'hidrante', 'tipoFiltro' => 'HIDRANTE', 'required' => true])</div>
                            <div class="col-md-6">
                                <label for="dimension_vidrio" class="form-label fw-bold mb-1">Dimensión Vidrio/Acrílico:</label>
                                <input type="text" class="form-control shadow-sm" id="dimension_vidrio" name="dimension_vidrio">
                            </div>
                        </div>
                    </fieldset>

                    <!-- Observaciones finales -->
                    <div style="background-color: #fce4e4;" class="text-center fw-bold py-0 mb-1 rounded">
                        OBSERVACIONES FINALES
                    </div>
                    <fieldset class="border p-3 rounded mb-3">
                        <div class="row mb-3 g-3">
                            <div class="col-md-6">
                                <div class="row g-3">
                                    <div class="col-md-12">
                                        <label for="observaciones_marbete" class="form-label fw-bold mb-1">Novedad del Marbete:</label>
                                        <input type="text" class="form-control shadow-sm" id="observaciones_marbete" name="observaciones_marbete" required>
                                    </div>
                                    <div class="col-md-12">
                                       @include('componentes.select_nomenclatura_multiple', [
                                            'label' => 'Observaciones',
                                            'name' => 'observaciones_generales',
                                            'tipoFiltro' => 'MARBETE'
                                        ])
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="row g-3">
                                    <div class="col-md-12">
                                        <label for="imagen" class="form-label fw-bold mb-1">Subir Imagen:</label>
                                        <input type="file" class="form-control shadow-sm" id="imagen" name="imagen" accept="image/*">
                                    </div>
                                    <div class="col-md-12">
                                        <label for="observaciones" class="form-label fw-bold mb-1">Novedades:</label>
                                        <input type="text" class="form-control shadow-sm" id="observaciones" name="observaciones">
                                    </div>
                                </div> 
                            </div>
                        </div>
                    </fieldset>
                </div>

                <!-- Footer del Modal -->
                @include('componentes.modal-footer', [
                'closeText'  => 'Cerrar',
                'submitText' => '', 
                'submitId'   => 'submit_control'
                ])

            </form>
        </div>
    </div>
</div>

<script>
    // Actualizar la abreviatura del extintor al seleccionar una subárea
    /*document.getElementById('subarea_control').addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];

        const abreviatura = selectedOption.getAttribute('data-abreviatura');
        const clase = selectedOption.getAttribute('data-clase');
        const marca = selectedOption.getAttribute('data-marca');
        const peso = selectedOption.getAttribute('data-peso');
        const unidad = selectedOption.getAttribute('data-unidad');
        const nombreArea = selectedOption.getAttribute('data-nombre-area');

        document.getElementById('codigo_control').value = abreviatura ? abreviatura : '';
        document.getElementById('clase').value = clase || '';
        document.getElementById('marca').value = marca || '';
        document.getElementById('peso_unidad').value = `${peso} ${unidad}`;
        document.getElementById('peso').value = peso || '';
        document.getElementById('unidad').value = unidad || '';
        document.getElementById('area_control').value = nombreArea || '';

    });*/
</script>