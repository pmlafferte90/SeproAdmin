<div class="container-fluid">
    {{-- Título principal --}}
    @if(!empty($titulo))
    <h4 class="mb-0 fw-bold mb-3">{{ $titulo }}</h4>
    @endif

    {{-- Subtítulo --}}
    @if(!empty($subtitulo))
    <h5 class="text-muted mt-1 mb-3">{{ $subtitulo }}</h5>
    @endif

    <!-- Contenido adicional + botón de acción a la derecha -->
    <div class="row mb-3 align-items-center">
        <div class="col-md-9 col-12">
            @hasSection('tabla-extra-content')
            @yield('tabla-extra-content')
            @endif
        </div>
        <div class="col-md-3 col-12 text-md-end mt-2 mt-md-0">
            @hasSection('tabla-create-button')
            @yield('tabla-create-button')
            @endif
        </div>
    </div>
    <!-- Header controls -->
    <div class="row mb-2">
        <!-- Dropdown for selecting records per page + resumen -->
        <div class="col-md-6 col-12 mb-2 mb-md-0 d-flex align-items-center gap-3">
            <div>
                <label for="recordsPerPage" class="me-2">Registros por página:</label>
                <select id="recordsPerPage" class="form-select form-select-sm d-inline-block" style="width: auto;"
                        onchange="updateRecordsPerPage(this.value)">
                    <option value="5" {{ request()->query('perPage') == 5 ? 'selected' : '' }}>5</option>
                    <option value="10" {{ request()->query('perPage') == 10 ? 'selected' : '' }}>10</option>
                    <option value="20" {{ request()->query('perPage') == 20 ? 'selected' : '' }}>20</option>
                    <option value="50" {{ request()->query('perPage') == 50 ? 'selected' : '' }}>50</option>
                </select>
            </div>
            @if(isset($paginator) && $paginator->total() > 0)
            <div class="text-muted small">
                Registros: {{ $paginator->firstItem() }}–{{ $paginator->lastItem() }} de {{ $paginator->total() }}
                registros
            </div>
            @endif
        </div>

        <!-- Search input -->
        <div class="col-md-6 col-12 d-flex justify-content-end align-items-center gap-3">
            <!-- Dropdown Toggle columnas -->
            <div class="btn-group ms-2">
                <button
                        type="button"
                        class="btn btn-outline-secondary dropdown-toggle"
                        data-bs-toggle="dropdown"
                        aria-expanded="false"
                >
                    Mostrar/Ocultar columnas
                </button>
                <div class="dropdown-menu dropdown-menu-end p-3" style="max-height: 300px; overflow-y: auto;">
                    @foreach($headers as $index => $header)
                        @php
                         if (isset($header['noShow']) && $header['noShow'] === true) {
                            $style="display: none;";
                         } else {
                            $style = '';
                         }
                        @endphp
                        <div class="form-check" style="{{ $style }}">
                            <input
                                class="form-check-input toggle-col"
                                type="checkbox"
                                value="{{ $header['key'] }}"
                                checked
                            >
                            <label class="form-check-label" for="colToggle{{ $index + 2 }}">
                                {{ $header['label'] }}
                            </label>
                        </div>
                    @endforeach
                </div>
            </div>
            <!-- Buscador -->
            <div class="input-group" style="width: 250px;">
                <input type="text" id="searchInput" class="form-control" placeholder="Buscar..."/>
                <button class="btn btn-primary" onclick="updateSearchQuery(searchInput.value)">
                    <i class="fa fa-search"></i>
                </button>
            </div>
        </div>
    </div>

    <!-- Scrollable table -->
    <div class="table-container" style="max-height: 400px; overflow-y: auto; border: 1px solid #ddd;">
        <table class="table table-bordered table-striped table-sm align-middle text-center fs-7"
               style="min-width: 1800px;">
            <thead class="align-middle" style="position: sticky; top: 0; background-color: #fff; z-index: 10;">
            <tr>
                <th data-key="row_number" style="width: 50px;">#</th>
                @foreach ($headers as $header)
                <th data-key="{{ $header['key'] }}" class="{{ $header['class'] ?? '' }}" style="{{ $header['style'] ?? '' }}">
                    {{ $header['label'] }}
                </th>
                @endforeach
            </tr>
            </thead>
            <tbody>
            @forelse ($rows as $i => $row)
            <tr>
                <td data-key="row_number">{{ ($paginator->firstItem() ?? 1) + $i }}</td>
                @foreach ($headers as $index => $header)
                <td data-key="{{ $header['key'] }}" class="{{ $header['class'] ?? '' }}" style="{{ $header['style'] ?? '' }}">

                    {!! $row[$index] ?? '-' !!}
                </td>
                @endforeach
            </tr>
            @empty
            <tr>
                <td colspan="{{ count($headers) + 1 }}" class="text-center">No hay datos disponibles.</td>
            </tr>
            @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination links -->
    <div class="d-flex justify-content-center mt-2">
        {!! $paginator->links() !!}
    </div>

    @hasSection('table-footer')
    <div class="mt-3">
        @yield('table-footer')
    </div>
    @endif

</div>

<script>
    // Set the search input value based on the URL query parameter
    document.addEventListener('DOMContentLoaded', () => {
        const searchInput = document.getElementById('searchInput');
        const urlParams = new URLSearchParams(window.location.search);
        const searchValue = urlParams.get('buscar');
        if (searchValue) {
            searchInput.value = searchValue;
        }
        searchInput.addEventListener('keypress', function (e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                updateSearchQuery(searchInput.value);
            }
        });

        // Toggle columnas
        document.querySelectorAll('.toggle-col').forEach(chk => {
          chk.addEventListener('change', () => {
            const key = chk.value;                // ahora obtenemos la key
            const display = chk.checked ? '' : 'none';
            document.querySelectorAll(`[data-key="${key}"]`).forEach(el => {
              el.style.display = display;
            });
          });
        });
    });
    function updateRecordsPerPage(perPage) {
        const url = new URL(window.location.href);
        url.searchParams.set('perPage', perPage);
        window.location.href = url.toString();
    }


    function toggleSearchInput() {
        const searchInput = document.getElementById('searchInput');
        if (searchInput.style.display === 'none') {
            searchInput.style.display = 'inline-block';
            searchInput.focus();
        } else {
            updateSearchQuery(searchInput.value);
        }
    }

    function updateSearchQuery(query) {
        const url = new URL(window.location.href);
        url.searchParams.set('buscar', query);
        window.location.href = url.toString();
    }
</script>
