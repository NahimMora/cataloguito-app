@if ($paginator->hasPages())
    <nav aria-label="Paginación">
        <div>
            @if ($paginator->onFirstPage())
            <span>Anterior</span>@else<a href="{{ $paginator->previousPageUrl() }}" rel="prev">← Anterior</a>
            @endif
        </div>
        <p>Página {{ $paginator->currentPage() }} de {{ $paginator->lastPage() }}</p>
        <div>
            @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" rel="next">Siguiente →</a>@else<span>Siguiente</span>
            @endif
        </div>
    </nav>
@endif
