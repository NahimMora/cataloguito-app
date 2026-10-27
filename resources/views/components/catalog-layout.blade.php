@props(['shop', 'appearance', 'preview' => false, 'pageTitle' => null, 'image' => null])
<x-base :title="($pageTitle ? $pageTitle . ' · ' : '') . $shop->name">
    @push('head')
        <meta name="description"
            content="{{ $shop->description ?: 'Explorá el catálogo de ' . $shop->name . ' y consultá tu pedido por WhatsApp.' }}">
        <link rel="canonical" href="{{ $preview ? $shop->publicUrl() : url()->current() }}">
        <meta property="og:type" content="website">
        <meta property="og:title" content="{{ $pageTitle ?: $shop->name }}">
        <meta property="og:description" content="{{ $shop->description }}">
        <meta property="og:url" content="{{ $preview ? $shop->publicUrl() : url()->current() }}">
        @if ($image ?? $shop->logoUrl())
            <meta property="og:image" content="{{ $image ?? $shop->logoUrl() }}">
            @endif @if ($preview)
                <meta name="robots" content="noindex,nofollow">
            @endif @endpush
            <div class="catalog-page catalog-{{ $appearance['theme'] }}" style="--accent:{{ $appearance['accent'] }}"
                x-data="catalogCart('{{ $shop->slug }}', {{ $preview ? 'true' : 'false' }})" @add-product.window="add($event.detail)"
                @keydown.escape.window="if(opened) close()">
                <div :inert="opened">
                    @if ($preview)
                        <div class="preview-banner">Vista previa privada · Los pedidos están desactivados.<a
                                href="{{ route('shop.settings') }}">Volver al diseño</a></div>
                    @endif
                    <header class="catalog-header"><a href="{{ $preview ? route('shop.preview') : $shop->publicUrl() }}"
                            class="catalog-identity">
                            @if ($shop->logoUrl())
                                <img src="{{ $shop->logoUrl() }}" alt="" class="catalog-logo" width="45"
                                height="45">@else<span class="catalog-logo">{{ mb_substr($shop->name, 0, 1) }}</span>
                            @endif
                            <div>
                                <strong>{{ $shop->name }}</strong><small>CATÁLOGO ONLINE</small>
                            </div>
                        </a>
                        <div class="catalog-header-actions">
                            @unless ($preview)
                                <a class="button" aria-label="Contactar por WhatsApp"
                                    href="{{ route('catalog.whatsapp', $shop->slug) }}" target="_blank"
                                    rel="noopener noreferrer"><x-icon name="phone"
                                        :size="16" /><span>Contactar</span></a>
                            @endunless
                            <button type="button" class="button cart-trigger" @click="open" aria-label="Abrir mi pedido">
                                <x-icon name="bag" :size="18" /><span>Mi pedido</span><b class="cart-count"
                                    x-show="count" x-cloak x-text="count"></b></button>
                        </div>
                    </header>
                    <main id="main">{{ $slot }}</main>
                    <footer class="catalog-footer">
                        <div><strong>{{ $shop->name }}</strong>
                            <p style="margin-top:7px">Pago, disponibilidad y entrega a confirmar con el comercio.</p>
                        </div>
                        <div class="row gap">
                            @if ($shop->instagram)
                                <a href="https://www.instagram.com/{{ $shop->instagram }}" target="_blank"
                                    rel="noopener noreferrer">Instagram ↗</a>
                                @endif @if ($shop->facebook)
                                    <a href="{{ $shop->facebook }}" target="_blank" rel="noopener noreferrer">Facebook
                                        ↗</a>
                                @endif
                                <a href="{{ route('privacy') }}">
                                    Privacidad</a>
                                @if ($shop->plan !== 'pro')
                                    <a href="{{ route('home') }}">Creado con vitrina.</a>
                                @endif
                        </div>
                    </footer>
                    <div class="cart-bottom" x-show="count>0" x-cloak>
                        <div><strong
                                x-text="count+' '+(count===1?'artículo':'artículos')+' en tu pedido'"></strong><small>Total
                                estimado <span x-text="money(total)"></span></small></div><button class="button"
                            @click="open">Ver pedido <x-icon name="arrow" :size="16" /></button>
                    </div>
                </div>
                <div class="drawer-backdrop" x-show="opened" x-cloak @click="close" aria-hidden="true"></div>
                <section class="cart-drawer" x-show="opened" x-cloak role="dialog" aria-modal="true"
                    aria-labelledby="cart-title" x-ref="drawer" @keydown="trap($event)">
                    <header class="drawer-head">
                        <div>
                            <p class="eyebrow">{{ $shop->name }}</p>
                            <h2 id="cart-title" x-text="step==='checkout'?'Revisá y confirmá':'Tu pedido'"></h2>
                        </div><button class="icon-button" @click="close" x-ref="closeCart"
                            aria-label="Cerrar pedido"><x-icon name="close" /></button>
                    </header>
                    <div class="drawer-content"><template x-if="!items.length">
                            <div class="empty">
                                <div class="empty-icon"><x-icon name="bag" /></div>
                                <h2>Tu próxima elección.</h2>
                                <p>Agregá productos al pedido. Después lo revisamos juntos.</p><button
                                    class="button secondary" @click="close">Seguir mirando</button>
                            </div>
                        </template>
                        <template x-for="item in items" :key="item.id">
                            <div class="cart-line">
                                <div><strong x-text="item.name"></strong><small
                                        x-text="money(item.price)+' por unidad'"></small><button class="text-link"
                                        style="margin-top:7px;font-size:10px"
                                        @click="items=items.filter(i=>i.id!==item.id); changed()"
                                        x-show="step==='cart'">Quitar</button></div>
                                <div>
                                    <div class="quantity-control" x-show="step==='cart'"><button
                                            @click="quantity(item.id,-1)"
                                            :aria-label="'Quitar una unidad de ' + item.name">−</button><span
                                            x-text="item.quantity"></span><button @click="quantity(item.id,1)"
                                            :aria-label="'Agregar una unidad de ' + item.name">+</button></div><small
                                        x-show="step==='checkout'" x-text="item.quantity+' unidades'"></small><strong
                                        style="display:block;text-align:right;margin-top:9px;font-size:12px"
                                        x-text="money(item.price*item.quantity)"></strong>
                                </div>
                            </div>
                        </template>
                        <div x-show="items.length">
                            <div class="cart-total"><span>Total estimado</span><span x-text="money(total)"></span></div>
                            <p class="checkout-note">El comercio confirma disponibilidad, pago y entrega. El envío no está
                                incluido en este total.</p>
                        </div>
                        <p class="cart-error" x-show="error" role="alert" x-text="error"></p>
                        <form x-show="step==='checkout' && items.length" @submit.prevent="confirm" id="checkout-form"
                            class="checkout-fields">
                            <p class="field-help" style="margin-bottom:20px">Precios actualizados. Dejá un contacto para
                                que el comercio pueda responderte.</p>
                            <div class="field"><label for="buyer-name">Tu nombre</label><input id="buyer-name"
                                    name="name" x-model="name" x-ref="buyerName" autocomplete="name" maxlength="100"
                                    required></div>
                            <div class="field"><label for="buyer-phone">Tu WhatsApp</label><input id="buyer-phone"
                                    name="phone" type="tel" x-model="phone" autocomplete="tel" maxlength="25"
                                    placeholder="5491112345678" required><small class="field-help">Número internacional
                                    con código de país.</small></div>
                            <div class="field"><label for="buyer-note">Algo que quieras aclarar
                                    <small>opcional</small></label>
                                <textarea id="buyer-note" name="note" x-model="note" maxlength="500" rows="3"></textarea>
                            </div>
                            <div aria-hidden="true" style="position:absolute;left:-10000px"><label for="website">Sitio
                                    web</label><input id="website" name="website" x-model="website" tabindex="-1"
                                    autocomplete="off"></div>
                            <p class="field-help">Al confirmar, tus datos y la solicitud se comparten con
                                {{ $shop->name }} para responder al pedido. <a href="{{ route('privacy') }}"
                                    class="text-link" target="_blank">Privacidad</a>.</p>
                        </form>
                    </div>
                    <footer class="drawer-foot" x-show="items.length"><button x-show="step==='cart'" @click="review"
                            :disabled="busy" class="button dark full"><span
                                x-text="busy?'Actualizando precios…':'Revisar pedido'"></span><x-icon name="arrow"
                                :size="17" /></button>
                        <div x-show="step==='checkout'"><button type="submit" form="checkout-form"
                                :disabled="busy" class="button dark full"
                                x-text="busy?'Guardando solicitud…':'Confirmar solicitud'"></button><button
                                class="button ghost full" :disabled="busy" @click="step='cart'">Volver al
                                pedido</button></div>
                    </footer>
                </section>
            </div>
    </x-base>
