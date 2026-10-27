@props(['product', 'shop', 'preview' => false, 'priority' => false])
<article class="catalog-product">
    <div class="catalog-photo"><a href="{{ $preview ? route('shop.preview') : $product->publicUrl($shop) }}"
            @if ($preview ?? false) @click.prevent @endif aria-label="Ver {{ $product->name }}">
            @if ($product->media)
                <img src="{{ $product->media->url() }}" srcset="{{ $product->media->srcset() }}"
                    sizes="(max-width:640px) 45vw, (max-width:1150px) 30vw, 260px" alt="{{ $product->name }}"
                    width="640" height="640" loading="{{ $priority ? 'eager' : 'lazy' }}"
                fetchpriority="{{ $priority ? 'high' : 'auto' }}">@else<div
                    style="height:100%;display:grid;place-items:center;color:#aaa292"><x-icon name="image"
                        :size="45" /></div>
            @endif
        </a>
        @unless ($product->available)
            <span class="stock-badge">SIN DISPONIBILIDAD</span>
        @else
            @if ($product->previous_price_minor)
                <span class="stock-badge">PRECIO ESPECIAL</span>
            @endif @endunless
        </div>
        <div class="product-details">
            <p class="category-name">{{ $product->category?->name ?? 'Nuestra selección' }}</p>
            <h2><a href="{{ $preview ? route('shop.preview') : $product->publicUrl($shop) }}">{{ $product->name }}</a></h2>
            <div class="product-bottom">
                <p class="product-price">{{ \App\Support\Money::format($product->price_minor) }}@if ($product->previous_price_minor)
                        <span class="old-price">{{ \App\Support\Money::format($product->previous_price_minor) }}</span>
                    @endif
                </p>
                @if ($product->available)
                    <button class="add-button" aria-label="Agregar {{ $product->name }}"
                        @click="$dispatch('add-product', @js(['id' => $product->public_id, 'name' => $product->name, 'price' => $product->price_minor]))"><x-icon name="plus"
                            :size="16" /></button>
                @endif
            </div>
        </div>
    </article>
