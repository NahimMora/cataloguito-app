<x-app-layout title="Cuenta y plan" subtitle="Tu espacio, a tu medida.">
    <div class="form-layout">
        <div class="stack">
            <section class="card form-section">
                <h2>Tu cuenta</h2>
                <p>{{ auth()->user()->email }} @if (auth()->user()->hasVerifiedEmail())
                        · Email verificado
                    @endif
                </p>
                <form method="post" action="{{ route('account.update') }}">@csrf @method('PUT')<div class="field">
                        <label for="name">Tu nombre</label><input name="name" id="name"
                            value="{{ old('name', auth()->user()->name) }}" required maxlength="80"></div>
                    <div class="field"><label class="checkbox-label"><input type="checkbox" name="email_orders"
                                value="1" @checked(old('email_orders', $shop->email_orders))>Recibir avisos de nuevas solicitudes por
                            email</label></div>
                    <details style="margin-bottom:22px">
                        <summary class="text-link" style="cursor:pointer">Cambiar contraseña</summary>
                        <div class="field" style="margin-top:18px"><label for="current_password">Contraseña
                                actual</label><input type="password" name="current_password" id="current_password"
                                autocomplete="current-password"></div>
                        <div class="field"><label for="password">Nueva contraseña</label><input type="password"
                                name="password" id="password" autocomplete="new-password" minlength="10"><small
                                class="field-help">Al menos 10 caracteres, con letras y números.</small></div>
                        <div class="field"><label for="password_confirmation">Repetí la nueva contraseña</label><input
                                type="password" name="password_confirmation" id="password_confirmation"
                                autocomplete="new-password"></div>
                    </details><button class="button dark">Guardar preferencias</button>
                </form>
            </section>
            <section class="card form-section">
                <h2>Tus datos, a mano</h2>
                <p>Podés descargar tus productos o pausar el catálogo cuando lo necesites.</p>
                <div class="row gap" style="flex-wrap:wrap"><a href="{{ route('products.export') }}"
                        class="button secondary"><x-icon name="download" :size="16" />Exportar productos</a><a
                        href="{{ route('shop.settings') }}" class="button secondary">Estado del catálogo</a></div>
                @if (app()->environment(['local', 'e2e']))
                    <a class="local-mail-link" href="{{ route('local.mail') }}">Ver correos de desarrollo</a>
                @endif
            </section>
            <section class="card form-section danger-zone">
                <h2>Baja de la cuenta</h2>
                <p>Eliminar tu cuenta también elimina el catálogo, sus fotos y solicitudes. Exportá primero lo que
                    quieras conservar.</p>
                <details>
                    <summary class="text-link" style="color:#9f1239;cursor:pointer">Quiero eliminar mi cuenta</summary>
                    <form method="post" action="{{ route('account.delete') }}" style="margin-top:20px">@csrf
                        @method('DELETE')<div class="field"><label for="delete-password">Tu contraseña</label><input
                                type="password" id="delete-password" name="password" required
                                autocomplete="current-password"></div>
                        <div class="field"><label for="confirmation">Escribí ELIMINAR para confirmar</label><input
                                id="confirmation" name="confirmation" required pattern="ELIMINAR"></div><button
                            class="button danger">Eliminar mi cuenta definitivamente</button>
                    </form>
                </details>
            </section>
        </div>
        <aside class="card card-padding"><span
                class="badge purple">{{ $shop->plan === 'pro' ? 'PRO · PILOTO' : 'PLAN GRATIS' }}</span>
            <h2 style="margin:18px 0 10px">Espacio para lo tuyo.</h2>
            <p class="field-help">Tu catálogo y las solicitudes de pedido están incluidos. Sin comisión de plataforma
                por solicitud.</p>@php($used = $shop->products()->where('status', 'active')->count())<div class="row between"
                style="margin-top:27px;font-size:12px"><strong>Productos activos</strong><span>{{ $used }} /
                    {{ $shop->productLimit() }}</span></div>
            <div class="progress-bar"><span style="width:{{ min(100, ($used / $shop->productLimit()) * 100) }}%"></span></div>
            <div class="row between" style="margin-top:23px;font-size:12px">
                <strong>Imágenes</strong><span>{{ number_format($shop->storage_bytes / 1_000_000, 1, ',', '.') }} /
                    {{ $shop->byteLimit() / 1_000_000 }} MB</span></div>
            <div class="progress-bar"><span
                    style="width:{{ min(100, ($shop->storage_bytes / $shop->byteLimit()) * 100) }}%"></span></div>
            <p class="field-help" style="margin-top:25px">
                {{ $shop->plan === 'pro' ? 'Tu plan piloto se administra con el equipo de Cataloguito. No hay cobros automáticos activos.' : '¿Necesitás más productos? El plan Pro está en piloto. Consultá con el equipo antes de cambiar de plan.' }}
            </p><a href="mailto:{{ config('vitrina.support_email') }}" class="button secondary full"
                style="margin-top:18px">Contactar al equipo <x-icon name="mail" :size="16" /></a>
            <form method="post" action="{{ route('logout') }}" style="margin-top:18px">@csrf<button
                    class="button ghost full"><x-icon name="logout" :size="16" />Cerrar sesión</button></form>
        </aside>
    </div>
</x-app-layout>
