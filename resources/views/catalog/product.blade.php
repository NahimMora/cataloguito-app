<x-catalog-layout :shop="$shop" :appearance="$appearance" :page-title="$product->name" :image="$product->media?->url(1200)">
    <article class="product-detail">
        <div class="catalog-photo">
            @if ($product->media)
                <img src="{{ $product->media->url(1200) }}" srcset="{{ $product->media->srcset() }}"
                    sizes="(max-width:640px) 90vw, 450px" alt="{{ $product->name }}" width="640"
                height="640">@else<x-icon name="image" :size="60" />
            @endif
        </div>
        <div><a class="text-link" href="{{ $shop->publicUrl() }}">← Volver al catálogo</a>
            <p class="eyebrow" style="margin-top:35px">{{ $product->category?->name ?? $shop->name }}</p>
            <h1>{{ $product->name }}</h1>
            <div class="product-price">{{ \App\Support\Money::format($product->price_minor) }}@if ($product->previous_price_minor)
                    <span class="old-price">{{ \App\Support\Money::format($product->previous_price_minor) }}</span>
                @endif
            </div>
            <p>{{ $product->description }}</p><span
                class="badge {{ $product->available ? 'green' : 'neutral' }}">{{ $product->available ? 'Disponible para consultar' : 'Sin disponibilidad' }}</span>
            @if ($product->sku)
                <small style="display:block;margin-top:15px">Código: {{ $product->sku }}</small>
                @endif @if ($product->available)
                    <button class="button dark full"
                        @click="$dispatch('add-product', @js(['id' => $product->public_id, 'name' => $product->name, 'price' => $product->price_minor]))"><x-icon name="plus"
                            :size="18" />Agregar a mi pedido</button>
                @endif
                <button class="button secondary full" @click="copyText(@js($product->publicUrl($shop)))"><x-icon
                        name="copy" :size="16" />Compartir producto</button>
                <p class="field-help" style="margin-top:20px">Disponibilidad final, forma de pago y entrega a confirmar
                    con {{ $shop->name }}.</p>
        </div>
    </article>
</x-catalog-layout>
