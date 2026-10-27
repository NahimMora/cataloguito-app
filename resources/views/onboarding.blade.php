<x-base title="Creá tu negocio · Vitrina">
    <main id="main" class="narrow-page" style="max-width:600px">
        <div class="row between"><x-brand />
            <form method="post" action="{{ route('logout') }}">@csrf<button class="button ghost small">Salir</button>
            </form>
        </div>
        <div class="card form-section" style="margin-top:35px">
            <p class="eyebrow">PRIMERO, LO TUYO</p>
            <h1>Dale un lugar a tu negocio.</h1>
            <p style="margin:15px 0 25px">Completá estos datos. Después sumamos los productos.</p><x-alerts />
            <form method="post" action="{{ route('onboarding') }}" x-data="{ name: @js(old('name', '')), slug: @js(old('slug', '')), edited: false }">@csrf
                <div class="field"><label for="name">Nombre del negocio</label><input id="name" name="name"
                        x-model="name"
                        @input="if(!edited) slug=name.normalize('NFD').replace(/[\u0300-\u036f]/g,'').toLowerCase().replace(/[^a-z0-9]+/g,'-').replace(/^-|-$/g,'').slice(0,60)"
                        required maxlength="100" placeholder="Por ejemplo, Casa Luma"></div>
                <div class="field"><label for="slug">Tu dirección permanente</label><input id="slug"
                        name="slug" x-model="slug" @input="edited=true" required pattern="[a-z0-9]+(-[a-z0-9]+)*"
                        minlength="3" maxlength="60" autocomplete="off"><small
                        class="field-help">{{ url('/') }}/<span x-text="slug || 'tu-negocio'"></span> · Elegila con
                        cuidado: será tu enlace para compartir.</small></div>
                <div class="field"><label for="whatsapp">WhatsApp del negocio</label><input id="whatsapp"
                        name="whatsapp" value="{{ old('whatsapp') }}" type="tel" required
                        placeholder="5491112345678" autocomplete="tel"><small class="field-help">Código de país + área +
                        número. Para un celular argentino: 549…, sin 0 ni 15.</small></div><button
                    class="button dark full">Crear mi espacio <x-icon name="arrow" :size="17" /></button>
            </form>
        </div>
    </main>
</x-base>
