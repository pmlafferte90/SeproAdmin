<div class="modal fade" id="areaEmpresaModal" tabindex="-1" aria-labelledby="areaEmpresaModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="areaEmpresaModalLabel">Asignar Sub-Áreas a Localidades</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      
      <form id="areaEmpresaForm" onsubmit="event.preventDefault(); enviarFormularioAreaEmpresa();">
        @csrf
        <div class="modal-body">
          <input type="hidden" id="areaEmpresaId" name="id" value="{{ $areaEmpresa->id ?? '' }}">

          <!-- 📌 Sección Izquierda: Cliente y Localidad -->
          <div class="row">
            <div class="col-6 mb-3">
              <label for="cliente" class="form-label"><strong>Seleccione un Cliente</strong></label>
              {{-- <select name="cliente" id="cliente" class="form-control shadow-sm" required>
                <option value="" disabled selected>Seleccione un cliente</option>
                @foreach ($clientes as $cliente)
                    <option value="{{ $cliente->id }}">{{ $cliente->razon_social }}</option>
                @endforeach
              </select> --}}

              <select name="cliente" id="cliente" class="form-control shadow-sm" required>
                <option value="" disabled selected>Seleccione un cliente</option>
              </select>

            </div>

            <div class="col-6 mb-3">
              <label for="localidad" class="form-label"><strong>Seleccione una Localidad</strong></label>
              <select name="localidad" id="localidad" class="form-control shadow-sm" required>
                <option value="" disabled selected>Seleccione una localidad</option>
              </select>
            </div>
          </div>

          <div id="contenedorSubareas" class="mt-4" style="display: none;">
          <div class="row">
            <div class="col-md-6">
              <label><strong>Ubicaciones disponibles</strong></label>
              <input type="text" class="form-control mb-1 shadow-sm" id="buscarSubareas" placeholder="Buscar ubicaciones...">
              <ul id="listaDisponibles" class="list-group border rounded shadow-sm" style="max-height: 300px; overflow-y: auto;">
                <!-- JS llenará esta lista -->
              </ul>
            </div>

            <div class="col-md-6">
              <label><strong>Ubicaciones seleccionadas</strong></label>
              <input type="text" class="form-control mb-1 shadow-sm" id="buscarSeleccionadas" placeholder="Buscar ubicaciones seleccionadas...">
              <ul id="listaSeleccionadas" class="list-group border rounded shadow-sm" style="max-height: 300px; overflow-y: auto;">
                <!-- JS llenará esta lista -->
              </ul>
            </div>
          </div>
          </div>

          <input type="hidden" name="subareas[]" id="inputSubareas">

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

{{-- @php
$subareasRaw = $areas->flatMap(function ($area) {
        // Usa la propiedad subAreas (eager‐loaded), nunca subareas()
        return $area->subAreas->map(function ($sub) use ($area) {
            return [
                'id'     => $sub->id,
                'text'   => $sub->nombre,
                'codigo' => $sub->codigo,
                'area'   => $area->nombre,
            ];
        });
    });
@endphp

<script>
    window.subareasRaw = @json($subareasRaw);
</script> --}}
