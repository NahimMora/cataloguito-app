@php(
    $heading = match ($mode) {
        'register' => 'Tu negocio empieza a mostrarse.',
        'forgot' => 'Recuperá tu acceso.',
        'reset' => 'Una nueva contraseña.',
        'login' => 'Qué bueno verte de nuevo.'
    }
)
<x-base :title="$heading . ' · Vitrina'">
    <div class="auth-shell">
        <aside class="auth-visual"><x-brand />
            <div>
                <p class="eyebrow">SIMPLE DE USAR. LINDO DE COMPARTIR.</p>
                <h2>Un pequeño cambio.<br>Una gran vidriera.</h2>
                <p>Tus productos, tu identidad y un enlace que siempre está al día. Más tiempo para hacer crecer lo
                    tuyo.</p>
            </div><small>Hecho con cuidado, en Argentina.</small>
        </aside>
        <main id="main" class="auth-main">
            <div class="auth-form">
                <h1>{{ $heading }}</h1>
                <p>{{ $mode === 'register' ? 'Creá tu cuenta gratis y dale un lugar propio a lo que hacés.' : ($mode === 'forgot' ? 'Te enviaremos un enlace para elegir una nueva contraseña.' : 'Entrá a tu espacio y seguí con lo tuyo.') }}
                </p><x-alerts />
                <form method="post"
                    action="{{ match ($mode) {'register' => route('register'),'forgot' => route('password.email'),'reset' => route('password.update'),default => route('login')} }}">
                    @csrf
                    @if ($mode === 'reset')
                        <input type="hidden" name="token" value="{{ $token }}">
                    @endif
                    @if ($mode === 'register')
                        <div class="field"><label for="name">Tu nombre</label><input id="name" name="name"
                                value="{{ old('name') }}" autocomplete="name" required maxlength="80"
                                placeholder="¿Cómo te llamás?"></div>
                    @endif
                    <div class="field"><label for="email">Email</label><input id="email" name="email"
                            type="email" value="{{ old('email', $email ?? '') }}" autocomplete="email" required
                            placeholder="vos@tunegocio.com" maxlength="254"></div>
                    @unless ($mode === 'forgot')<div class="field"><label
                                for="password">Contraseña</label><input id="password" name="password" type="password"
                                autocomplete="{{ $mode === 'login' ? 'current-password' : 'new-password' }}" required
                                @if ($mode !== 'login') minlength="10" @endif>
                            @if ($mode !== 'login')
                                <small class="field-help">Al menos 10 caracteres, con letras y números.</small>
                            @endif
                        </div>
                    @endunless
                    @if (in_array($mode, ['register', 'reset']))
                        <div class="field"><label for="password_confirmation">Repetí la contraseña</label><input
                                id="password_confirmation" name="password_confirmation" type="password"
                                autocomplete="new-password" required></div>
                    @endif
                    @if ($mode === 'register')
                        <div class="field"><label class="checkbox-label"><input type="checkbox" name="terms"
                                    value="1" required><span>Acepto los <a class="text-link"
                                        href="{{ route('terms') }}" target="_blank">términos</a> y la <a
                                        class="text-link" href="{{ route('privacy') }}" target="_blank">política de
                                        privacidad</a>.</span></label></div>
                    @endif
                    @if ($mode === 'login')
                        <div class="row between field"><label class="checkbox-label"><input type="checkbox"
                                    name="remember" value="1">Recordarme</label><a
                                href="{{ route('password.request') }}" class="text-link">Olvidé mi contraseña</a></div>
                    @endif
                    <button
                        class="button dark full">{{ match ($mode) {'register' => 'Crear mi cuenta','forgot' => 'Enviar enlace','reset' => 'Guardar contraseña',default => 'Ingresar a mi espacio'} }}
                        <x-icon name="arrow" :size="17" /></button>
                </form>
                <div class="auth-form-footer">
                    @if ($mode === 'register')
                        ¿Ya tenés cuenta?
                        <a href="{{ route('login') }}">Ingresá</a>
                    @elseif($mode === 'login')
                    ¿Todavía no tenés tu catálogo? <a href="{{ route('register') }}">Empezá gratis</a>@else<a
                            href="{{ route('login') }}">Volver a ingresar</a>
                    @endif
                </div>
                @if (app()->environment(['local', 'e2e']) && session('mail_preview_email'))
                    <a class="local-mail-link" href="{{ route('local.mail') }}">Abrir correo de desarrollo</a>
                @endif
            </div>
        </main>
    </div>
</x-base>
