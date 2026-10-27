<x-app-layout title="Productos" subtitle="Tu selección, siempre lista. Cambiá un precio acá mismo.">
    <x-slot:actions><a href="{{ route('products.export') }}" class="button secondary"><x-icon name="download"
                :size="16" />Exportar</a><a href="{{ route('products.create') }}" class="button dark"><x-icon
                name="plus" :size="16" />Agregar producto</a></x-slot:actions>
    <div class="tabs"><a href="{{ route('products.index') }}"
            class="{{ request('status') !== 'archived' ? 'active' : '' }}">Activos <span
                class="badge neutral">{{ $activeCount }}</span></a><a
            href="{{ route('products.index', ['status' => 'archived']) }}"
            class="{{ request('status') === 'archived' ? 'active' : '' }}">Archivados</a></div>
    <form class="toolbar" method="get"><input type="hidden" name="status" value="{{ request('status', 'active') }}">
        <div class="search-field"><x-icon name="search" :size="18" /><input name="q" type="search"
                aria-label="Buscar productos" value="{{ request('q') }}" placeholder="Buscar por nombre o SKU"
                maxlength="100"></div><select name="category" aria-label="Filtrar categoría">
            <option value="">Todas las categorías</option>
            @foreach ($categories as $category)
                <option value="{{ $category->id }}" @selected(request('category') == $category->id)>{{ $category->name }}</option>
            @endforeach
        </select>
        <button class="button secondary">Buscar</button><a href="{{ route('categories.index') }}"
            class="button secondary"><x-icon name="tag" :size="16" />Categorías</a>
    </form>
    <div class="card">
        @if ($products->count())
            <table class="product-table">
                <thead>
                    <tr>
                        <th>Producto</th>
                        <th class="table-category">Categoría</th>
                        <th>Precio</th>
                        <th>Disponibilidad</th>
                        <th><span class="sr-only">Acciones</span></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($products as $product)
                        <tr>
                            <td><a class="product-ident" href="{{ route('products.edit', $product->public_id) }}">
                                    @if ($product->media)
                                        <img class="product-thumb" src="{{ $product->media->url(320) }}" alt=""
                                        width="48" height="54" loading="lazy">@else<span
                                            class="product-thumb row"
                                            style="justify-content:center;color:#a5a092"><x-icon
                                                name="image" /></span>
                                    @endif
                                    <div>
                                        <strong>{{ $product->name }}</strong><small>{{ $product->sku ?: 'Sin SKU' }}</small>
                                    </div>
                                </a></td>
                            <td class="table-category">{{ $product->category?->name ?? 'Sin categoría' }}</td>
                            <td class="price-cell">
                                <form method="post" action="{{ route('products.quick', $product->public_id) }}"
                                    x-data="quickPrice(@js(route('products.quick', $product->public_id)), @js(\App\Support\Money::input($product->price_minor)), {{ $product->version }})" @submit.prevent="save">@csrf @method('PATCH')<div
                                        class="quick-price"><label class="sr-only"
                                            for="price-{{ $product->id }}">Precio de
                                            {{ $product->name }}</label><input id="price-{{ $product->id }}"
                                            name="price" x-model="price"
                                            value="{{ \App\Support\Money::input($product->price_minor) }}"
                                            inputmode="decimal" required><input type="hidden" name="version"
                                            value="{{ $product->version }}"><button :disabled="saving"
                                            aria-label="Guardar precio de {{ $product->name }}"><x-icon name="check"
                                                :size="16" /></button></div>
                                    <p class="quick-status" :style="failed ? 'color:#b91c1c' : 'color:#166534'"
                                        role="status" x-text="status"></p>
                                </form>
                            </td>
                            <td class="table-availability"><span
                                    class="badge {{ $product->available ? 'green' : 'neutral' }}">{{ $product->available ? 'Disponible' : 'Agotado' }}</span>
                            </td>
                            <td class="table-actions"><a href="{{ route('products.edit', $product->public_id) }}"
                                    class="icon-button" aria-label="Editar {{ $product->name }}"><x-icon name="edit"
                                        :size="16" /></a>
                                <form action="{{ route('products.archive', $product->public_id) }}" method="post">
                                    @csrf<button class="icon-button"
                                        aria-label="{{ $product->status === 'active' ? 'Archivar' : 'Restaurar' }} {{ $product->name }}"
                                        title="{{ $product->status === 'active' ? 'Archivar' : 'Restaurar' }}"><x-icon
                                            :name="$product->status === 'active' ? 'box' : 'plus'" :size="16" /></button></form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        <div class="pagination">{{ $products->links('components.pagination') }}</div>@else<div class="empty">
                <div class="empty-icon"><x-icon name="box" :size="27" /></div>
                <h2>{{ request('q') ? 'No encontramos esos productos.' : 'Tu selección empieza acá.' }}</h2>
                <p>{{ request('q') ? 'Probá con otra búsqueda o categoría.' : 'Una foto, un nombre y un precio. Agregá tu primer producto y empezá a darle forma a tu catálogo.' }}
                </p><a href="{{ route('products.create') }}" class="button dark">Agregar producto <x-icon
                        name="plus" :size="17" /></a>
            </div>
        @endif
    </div>
    <p class="field-help" style="margin-top:15px">{{ $activeCount }} de {{ $shop->productLimit() }} productos activos
        · Los archivados conservan su información y no aparecen en el catálogo.</p>
</x-app-layout>
