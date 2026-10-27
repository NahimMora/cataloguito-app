<x-app-layout title="Mi catálogo" subtitle="Un espacio con tu identidad. Un enlace que siempre funciona.">
    <x-slot:actions><a href="{{ route('shop.preview') }}" target="_blank" rel="noopener"
            class="button secondary"><x-icon name="external" :size="16" />Vista previa</a>
        <form method="post" action="{{ route('shop.publish') }}">@csrf<button class="button dark"><x-icon
                    :name="$shop->status === 'published' ? 'clock' : 'check'"
                    :size="16" />{{ $shop->status === 'published' ? 'Pausar catálogo' : 'Publicar catálogo' }}</button>
        </form>
    </x-slot:actions>
    <div class="card card-padding" style="margin-bottom:24px">
        <div class="row between gap">
            <div>
                <h2>Tu enlace permanente</h2>
                <p class="field-help">El nombre del negocio puede cambiar. Esta dirección se conserva.</p>
            </div><span
                class="badge {{ $shop->status === 'published' ? 'green' : 'neutral' }}">{{ $shop->status === 'published' ? 'Publicado' : 'Sin publicar' }}</span>
        </div>
        <div class="url-box" style="margin-bottom:0"><span>{{ $shop->publicUrl() }}</span><button
                aria-label="Copiar enlace" @click="copyText(@js($shop->publicUrl()))"><x-icon
                    name="copy" /></button></div>
    </div>
    <div class="form-layout">
        <div class="card">
            <form method="post" action="{{ route('shop.update') }}" enctype="multipart/form-data">@csrf
                @method('PUT')<section class="form-section">
                    <h2>La identidad de tu negocio</h2>
                    <p>Los detalles que hacen que te reconozcan.</p>
                    <div class="field"><label for="name">Nombre comercial</label><input name="name"
                            id="name" value="{{ old('name', $shop->name) }}" required maxlength="100"></div>
                    <div class="field"><label for="description">Descripción breve</label>
                        <textarea name="description" id="description" maxlength="300" rows="3">{{ old('description', $shop->description) }}</textarea>
                    </div>
                    <div class="field"><label for="image">Logo</label>
                        <div class="row gap">
                            @if ($shop->logoUrl())
                                <img src="{{ $shop->logoUrl() }}" alt="Logo actual" width="55" height="55"
                                    style="border-radius:8px">
                            @endif
                            <input type="file" name="image" id="image"
                                accept="image/jpeg,image/png,image/webp">
                        </div><small class="field-help">JPG, PNG o WebP, hasta 5 MB. Se verá con fondo blanco.</small>
                    </div>
                    @if ($shop->logoUrl())
                        <label class="checkbox-label"><input type="checkbox" name="remove_logo" value="1">Quitar
                            logo</label>
                    @endif
                </section>
                <section class="form-section">
                    <h2>Dónde encontrarte</h2>
                    <p>La conversación sigue en tus canales habituales.</p>
                    <x-phone-input label="WhatsApp del negocio" :value="$shop->whatsapp" />
                    <a class="field-help text-link" href="https://wa.me/{{ $shop->whatsapp }}" target="_blank"
                        rel="noopener noreferrer">Probar mi número actual ↗</a>
                    <div class="field"><label for="instagram">Usuario de Instagram</label><input name="instagram"
                            id="instagram" value="{{ old('instagram', $shop->instagram) }}"
                            placeholder="tunegocio, sin @"></div>
                    <div class="field"><label for="facebook">Enlace de Facebook</label><input type="url"
                            name="facebook" id="facebook" value="{{ old('facebook', $shop->facebook) }}"
                            placeholder="https://www.facebook.com/tunegocio"></div><button class="button dark">Guardar
                        datos del negocio <x-icon name="check" :size="16" /></button>
                </section>
            </form>
        </div>
        <aside class="card form-section appearance-studio" id="estilo">
            <div class="appearance-heading"><div><span class="eyebrow">TU VIDRIERA</span><h2>Elegí una plantilla por rubro</h2><p>Cada estilo cambia la composición, la tipografía y la forma de mostrar tus productos.</p></div><a href="{{ route('templates.index') }}" target="_blank" class="text-link">Ver galería completa <x-icon name="external" :size="14" /></a></div>
            <form method="post" action="{{ route('shop.appearance') }}" x-data="{ style: @js(old('style', $shop->currentStyle())), accent: @js(old('accent', $shop->appearance_draft['accent'])) }">@csrf
                @method('PUT')<div class="style-picker-grid">
                    @foreach ($styles as $key => $template)
                        <label class="style-picker" :class="style === @js($key) ? 'selected' : ''" style="--picker-accent:{{ $template['accent'] }};--picker-surface:{{ $template['surface'] }}"><input type="radio" name="style" value="{{ $key }}" x-model="style" @change="accent=@js($template['accent'])"><span class="style-picker-art"><i></i><i></i><i></i></span><span><strong>{{ $template['name'] }}</strong><small>{{ $template['sector'] }}</small></span><b aria-hidden="true">✓</b></label>
                    @endforeach
                </div>
                <div class="field appearance-color"><span class="field-label">Ahora elegí el color principal</span><small class="field-help">Las opciones conservan buen contraste en botones y precios.</small>
                    <div class="color-options">
                        @foreach (['#b93828' => 'Coral', '#a53f35' => 'Terracota', '#154b45' => 'Bosque', '#8c4661' => 'Malva', '#b34b35' => 'Arcilla', '#c74f32' => 'Mandarina', '#36684c' => 'Verde hoja', '#9c493d' => 'Cacao rosa', '#4338ca' => 'Índigo', '#0f766e' => 'Petróleo', '#9f1239' => 'Cereza', '#1d4ed8' => 'Azul', '#334155' => 'Grafito'] as $hex => $name)
                            <label class="color-option" title="{{ $name }}"><input type="radio"
                                    name="accent" value="{{ $hex }}" aria-label="{{ $name }}"
                                    x-model="accent"><span
                                    style="background:{{ $hex }}"></span></label>
                        @endforeach
                    </div>
                </div>
                <div class="field"><label for="tagline">Frase principal del catálogo</label><input name="tagline"
                        id="tagline" maxlength="100"
                        value="{{ old('tagline', $shop->appearance_draft['tagline'] ?? '') }}" placeholder="Una selección hecha para vos…"></div><div class="appearance-actions"><button name="action" value="draft" class="button secondary">Guardar borrador</button><a class="button secondary" href="{{ route('shop.preview') }}" target="_blank" rel="noopener">Ver mi catálogo <x-icon name="external" :size="16" /></a><button name="action" value="publish" class="button dark">Publicar diseño <x-icon name="check" :size="16" /></button></div>
            </form>
        </aside>
    </div>
</x-app-layout>
