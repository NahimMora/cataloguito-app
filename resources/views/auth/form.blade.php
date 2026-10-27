@php
    $heading = match ($mode) {
        'register' => 'Creá tu Cataloguito.',
        'forgot' => 'Recuperá tu acceso.',
        'reset' => 'Elegí una nueva contraseña.',
        default => 'Qué bueno verte de nuevo.',
    };
    $fieldError = fn ($field) => $errors->has($field) ? 'true' : null;
@endphp
<x-base :title="$heading . ' · Cataloguito'">
    <div class="auth-shell auth-{{ $mode }}">
        <aside class="auth-visual"><x-brand /><div><p class="eyebrow">CERCA DE TU NEGOCIO</p><h2>Un catálogo lindo.<br>Fácil de mantener.</h2><p>Actualizá precios desde el celu y compartí siempre el mismo enlace.</p></div><small>Hecho con cuidado, en Argentina.</small></aside>
        <main id="main" class="auth-main">
            <div class="auth-form">
                <div class="auth-brand-mobile"><x-brand /></div>
                <h1>{{ $heading }}</h1>
                <p>{{ $mode === 'register' ? 'Tu cuenta es gratis. No necesitás tarjeta.' : ($mode === 'forgot' ? 'Te enviaremos un enlace para elegir una nueva contraseña.' : 'Entrá a tu espacio y seguí con lo tuyo.') }}</p>
                @if ($errors->any())
                    <div class="alert error auth-error-summary" role="alert" tabindex="-1" x-data x-init="$el.focus()"><div><strong>Hay datos para revisar</strong><ul>
                        @foreach ($errors->keys() as $field)
                            <li><a href="#{{ $field }}">{{ $errors->first($field) }}</a></li>
                        @endforeach
                    </ul></div></div>
                @else
                    <x-alerts />
                @endif
                <form method="post" x-data="{ submitting: false, reveal: false }" @submit="submitting=true"
                    action="{{ match ($mode) {'register' => route('register'),'forgot' => route('password.email'),'reset' => route('password.update'),default => route('login')} }}">
                    @csrf
                    @if ($mode === 'reset')<input type="hidden" name="token" value="{{ $token }}">@endif
                    @if ($mode === 'register')
                        @if ($selectedStyle ?? null)<input type="hidden" name="style" value="{{ $selectedStyle }}">@endif
                        <div class="field"><label for="name">Tu nombre</label><input id="name" name="name" value="{{ old('name') }}" autocomplete="name" required maxlength="80" placeholder="¿Cómo te llamás?" @if($fieldError('name')) aria-invalid="true" aria-describedby="name-error" @endif><x-field-error field="name" /></div>
                    @endif
                    <div class="field"><label for="email">Email</label><input id="email" name="email" type="email" value="{{ old('email', $email ?? '') }}" autocomplete="email" required placeholder="vos@tunegocio.com" maxlength="254" @if($fieldError('email')) aria-invalid="true" aria-describedby="email-error" @endif><x-field-error field="email" /></div>
                    @unless ($mode === 'forgot')
                        <div class="field"><label for="password">Contraseña</label><div class="password-control"><input id="password" name="password" :type="reveal?'text':'password'" autocomplete="{{ $mode === 'login' ? 'current-password' : 'new-password' }}" required @if ($mode !== 'login') minlength="10" @endif @if($fieldError('password')) aria-invalid="true" aria-describedby="password-error" @endif><button type="button" @click="reveal=!reveal" x-text="reveal?'Ocultar':'Mostrar'"></button></div><x-field-error field="password" />@if ($mode !== 'login' && !$errors->has('password'))<small class="field-help">10 caracteres o más, con letras y números.</small>@endif</div>
                    @endunless
                    @if (in_array($mode, ['register', 'reset']))
                        <div class="field"><label for="password_confirmation">Repetí la contraseña</label><input id="password_confirmation" name="password_confirmation" :type="reveal?'text':'password'" autocomplete="new-password" required></div>
                    @endif
                    @if ($mode === 'register')
                        <div class="field compact-check"><label class="checkbox-label"><input id="terms" type="checkbox" name="terms" value="1" required @checked(old('terms')) @if($fieldError('terms')) aria-invalid="true" aria-describedby="terms-error" @endif><span>Acepto los <a class="text-link" href="{{ route('terms') }}" target="_blank">términos</a> y la <a class="text-link" href="{{ route('privacy') }}" target="_blank">privacidad</a>.</span></label><x-field-error field="terms" /></div>
                    @endif
                    @if ($mode === 'login')
                        <div class="row between field compact-check"><label class="checkbox-label"><input type="checkbox" name="remember" value="1">Recordarme</label><a href="{{ route('password.request') }}" class="text-link">Olvidé mi contraseña</a></div>
                    @endif
                    <button class="button dark full" :disabled="submitting"><span x-text="submitting?'Procesando…':@js(match ($mode) {'register' => 'Crear mi cuenta','forgot' => 'Enviar enlace','reset' => 'Guardar contraseña',default => 'Ingresar a mi espacio'})"></span><x-icon name="arrow" :size="17" /></button>
                </form>
                <div class="auth-form-footer">@if ($mode === 'register')¿Ya tenés cuenta? <a href="{{ route('login') }}">Ingresá</a>@elseif($mode === 'login')¿Todavía no tenés catálogo? <a href="{{ route('register') }}">Empezá gratis</a>@else<a href="{{ route('login') }}">Volver a ingresar</a>@endif</div>
                @if (app()->environment(['local', 'e2e']) && session('mail_preview_email'))<a class="local-mail-link" href="{{ route('local.mail') }}">Abrir correo de desarrollo</a>@endif
            </div>
        </main>
    </div>
</x-base>
