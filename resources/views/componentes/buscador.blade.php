<!-- Selector de items por página, campo de búsqueda y botón de nuevo cliente -->
<div class="row align-items-center mb-3">
    <!-- Selector de items por página -->
    <div class="col-md-6 mb-2 mb-md-0">
        <form action="{{ $actionRoute }}" method="GET" class="d-flex align-items-center">
            <label for="perPage" class="mr-2 mb-0">Items por página:</label>
            <select name="perPage" id="perPage" class="form-control" style="width: auto;" onchange="this.form.submit()">
                <option value="5" {{ request('perPage') == 5 ? 'selected' : '' }}>5</option>
                <option value="10" {{ request('perPage') == 10 ? 'selected' : '' }}>10</option>
                <option value="15" {{ request('perPage') == 15 ? 'selected' : '' }}>15</option>
                <option value="20" {{ request('perPage') == 20 ? 'selected' : '' }}>20</option>
            </select>
        </form>
    </div>

    <!-- Campo de búsqueda -->
    <div class="col-md-6 d-flex justify-content-end">
        <form action="{{ $actionRoute }}" method="GET" class="d-flex align-items-center">
            <input type="hidden" name="perPage" value="{{ request('perPage', 5) }}">
            <input type="text" name="buscar" class="form-control" placeholder="Buscar..." value="{{ request('buscar') }}" style="width: auto;">
            <button type="submit" class="btn btn-secondary ml-2" title="Buscar">
                <i class="fas fa-search"></i>
            </button>
        </form>
    </div>
</div>
