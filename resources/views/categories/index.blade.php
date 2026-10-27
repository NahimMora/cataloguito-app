<x-app-layout title="Categorías" subtitle="Ayudá a tus clientes a encontrar lo que buscan.">
    <x-slot:actions><a href="{{ route('products.index') }}" class="button secondary">Volver a productos</a></x-slot:actions>
    <div class="form-layout">
        <div class="card">
            <div class="card-header">
                <h2>En tu catálogo</h2><span class="badge neutral">{{ $categories->count() }} categorías</span>
            </div>
            @forelse($categories as $category)
                <div class="category-row">
                    <form method="post" action="{{ route('categories.update', $category->public_id) }}">@csrf
                        @method('PUT')<div class="field"><label for="cat-{{ $category->id }}">Nombre ·
                                {{ $category->products_count }} productos</label><input id="cat-{{ $category->id }}"
                                name="name" value="{{ $category->name }}" required maxlength="80"></div>
                        <div class="field order-field"><label for="order-{{ $category->id }}">Orden</label><input
                                id="order-{{ $category->id }}" name="sort_order" value="{{ $category->sort_order }}"
                                type="number" min="0" max="9999" required></div><button class="icon-button"
                            aria-label="Guardar {{ $category->name }}"><x-icon name="check"
                                :size="18" /></button>
                    </form>
                    <form method="post" action="{{ route('categories.delete', $category->public_id) }}" x-data
                        @submit="if(!confirm('¿Eliminar esta categoría? Los productos se conservan.')) $event.preventDefault()">
                        @csrf @method('DELETE')<button class="icon-button"
                            aria-label="Eliminar {{ $category->name }}"><x-icon name="close"
                                :size="18" /></button></form>
            </div>@empty<div class="empty">
                    <div class="empty-icon"><x-icon name="tag" /></div>
                    <h2>Cada cosa en su lugar.</h2>
                    <p>Creá categorías como Accesorios, Hogar o Regalos. También podés publicar productos sin categoría.
                    </p>
                </div>
            @endforelse
        </div>
        <aside class="card form-section">
            <h2>Nueva categoría</h2>
            <p>Elegí un nombre corto y claro.</p>
            <form method="post" action="{{ route('categories.store') }}">@csrf<div class="field"><label
                        for="name">Nombre</label><input id="name" name="name" required maxlength="80"
                        value="{{ old('name') }}" placeholder="Por ejemplo, Hogar"></div>
                <div class="field"><label for="sort_order">Orden</label><input id="sort_order" name="sort_order"
                        type="number" min="0" max="9999" value="{{ old('sort_order', 0) }}" required></div>
                <button class="button dark full"><x-icon name="plus" :size="16" />Crear categoría</button>
            </form>
        </aside>
    </div>
</x-app-layout>
