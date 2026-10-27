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
                    <div class="field"><label for="whatsapp">WhatsApp internacional</label><input type="tel"
                            name="whatsapp" id="whatsapp" value="{{ old('whatsapp', $shop->whatsapp) }}" required><a
                            class="field-help text-link" href="https://wa.me/{{ $shop->whatsapp }}" target="_blank"
                            rel="noopener noreferrer">Probar mi número ↗</a></div>
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
        <aside class="card form-section">
            <h2>Un estilo bien tuyo</h2>
            <p>Guardá una vista previa y mirala antes de publicar.</p>
            <form method="post" action="{{ route('shop.appearance') }}" x-data="{ theme: @js(old('theme', $shop->appearance_draft['theme'])) }">@csrf
                @method('PUT')<div class="theme-options">
                    @foreach (['minimal' => 'Minimal', 'commercial' => 'Comercial'] as $key => $name)
                        <label class="theme-option" :class="theme === @js($key) ? 'selected' : ''"><span
                                class="theme-mini {{ $key }}"><i></i><i></i><i></i><i></i></span><input
                                type="radio" name="theme" value="{{ $key }}"
                                x-model="theme">{{ $name }}</label>
                    @endforeach
                </div>
                <div class="field"><span class="field-label">Color de acento</span>
                    <div class="color-options">
                        @foreach (['#4338ca' => 'Índigo', '#0f766e' => 'Petróleo', '#9f1239' => 'Cereza', '#1d4ed8' => 'Azul', '#334155' => 'Grafito'] as $hex => $name)
                            <label class="color-option" title="{{ $name }}"><input type="radio"
                                    name="accent" value="{{ $hex }}" aria-label="{{ $name }}"
                                    @checked(old('accent', $shop->appearance_draft['accent']) === $hex)><span
                                    style="background:{{ $hex }}"></span></label>
                        @endforeach
                    </div>
                </div>
                <div class="field"><label for="tagline">Frase de tu catálogo</label><input name="tagline"
                        id="tagline" maxlength="100"
                        value="{{ old('tagline', $shop->appearance_draft['tagline'] ?? '') }}"></div><button
                    name="action" value="draft" class="button secondary full">Guardar vista previa</button><a
                    class="button secondary full" style="margin-top:9px" href="{{ route('shop.preview') }}"
                    target="_blank" rel="noopener">Ver diseño <x-icon name="external" :size="16" /></a><button
                    name="action" value="publish" class="button dark full" style="margin-top:9px">Publicar este
                    diseño <x-icon name="check" :size="16" /></button>
            </form>
        </aside>
    </div>
</x-app-layout>
