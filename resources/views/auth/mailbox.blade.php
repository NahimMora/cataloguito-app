<x-base title="Correo local · Cataloguito">
    <main id="main" class="narrow-page"><x-brand />
        <div class="page-heading" style="margin-top:35px">
            <div>
                <h1>Correo de desarrollo</h1>
                <p class="muted">Solo disponible en tu entorno local.</p>
            </div><a class="button secondary" href="{{ auth()->check() ? route('dashboard') : route('login') }}">Volver</a>
        </div>
        <div class="card">
            @forelse($messages as $message)
                <article class="mail-message">
                    <h2>{{ $message->subject }}</h2>
                    <p class="field-help">{{ $message->recipient }} · {{ $message->created_at }}</p>
                    @if ($message->action_url)
                        <a class="button dark" style="margin:15px 0" href="{{ $message->action_url }}">Abrir enlace de
                            este correo <x-icon name="arrow" :size="16" /></a>
                    @endif
                    <iframe title="{{ $message->subject }}"
                        sandbox="allow-popups allow-popups-to-escape-sandbox allow-top-navigation-by-user-activation"
                        srcdoc="{{ $message->html }}">
                    </iframe>
            </article>@empty<div class="empty">
                    <p>No hay correos para esta sesión. Solicitá una verificación o recuperación.</p>
                </div>
            @endforelse
        </div>
    </main>
</x-base>
