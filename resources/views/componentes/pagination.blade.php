<!-- Paginación -->
<div class="d-flex justify-content-end mt-3">
    {{ $items->appends(['buscar' => request('buscar'), 'perPage' => request('perPage')])->links() }}
</div>
