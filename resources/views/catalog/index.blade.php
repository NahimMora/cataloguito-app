<x-catalog-layout :shop="$shop" :appearance="$appearance" :preview="$preview">
    <section class="catalog-intro">
        <div>
            <p class="eyebrow">ELEGIDO CON CUIDADO. HECHO PARA VOS.</p>
            <h1>{{ $appearance['tagline'] ?: $shop->name }}</h1>
            @if ($shop->description)
                <p>{{ $shop->description }}</p>
            @endif
        </div>
        <div class="catalog-note"><x-icon name="bag" :size="15" />Elegí tus favoritos. Consultá tu pedido por
            WhatsApp.</div>
    </section>
    <div class="catalog-main">
        <div class="catalog-toolbar">
            <nav class="category-pills" aria-label="Categorías"><a
                    href="{{ $preview ? route('shop.preview') : $shop->publicUrl() }}"
                    class="{{ !$selected ? 'active' : '' }}">Todo el catálogo</a>
                @foreach ($categories as $category)
                    <a href="{{ $preview ? route('shop.preview') : $category->publicUrl($shop) }}"
                        class="{{ $selected?->id === $category->id ? 'active' : '' }}">{{ $category->name }}</a>
                @endforeach
            </nav>
            <form class="catalog-search search-field" method="get"><x-icon name="search" :size="17" /><input
                    type="search" name="q" value="{{ request('q') }}" placeholder="Buscar algo especial…"
                    aria-label="Buscar en el catálogo" maxlength="100"></form>
        </div>
        <div class="row between" style="margin-bottom:23px">
            <p class="field-help">{{ $products->total() }} productos @if ($selected)
                    en {{ $selected->name }}
                @endif
            </p>
            <form method="get"><input type="hidden" name="q" value="{{ request('q') }}"><label for="sort"
                    class="sr-only">Ordenar productos</label><select id="sort" name="sort"
                    @change="$el.form.submit()"
                    style="font-size:10px;background:transparent;padding:5px;border:0;min-height:32px">
                    <option value="original">Orden original</option>
                    <option value="price_asc" @selected(request('sort') === 'price_asc')>Menor precio</option>
                    <option value="price_desc" @selected(request('sort') === 'price_desc')>Mayor precio</option>
                    <option value="name" @selected(request('sort') === 'name')>Nombre A–Z</option>
                </select><noscript><button>Ordenar</button></noscript></form>
        </div>
        @if ($products->count())
            <div class="catalog-grid">
                @foreach ($products as $product)
                    <x-product-card :priority="$loop->first" :product="$product" :shop="$shop" :preview="$preview" />
                @endforeach
            </div>
            <div class="pagination" style="border:0;margin-top:25px">{{ $products->links('components.pagination') }}
        </div>@else<div class="empty">
                <div class="empty-icon"><x-icon name="search" /></div>
                <h2>{{ request('q') ? 'No encontramos ese producto.' : 'Estamos preparando nuestra selección.' }}</h2>
                <p>{{ request('q') ? 'Probá otro nombre o explorá las categorías.' : 'Volvé pronto para conocer las novedades.' }}
                </p>
            </div>
        @endif
    </div>
</x-catalog-layout>
