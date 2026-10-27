<x-base title="Verificá tu email · Cataloguito">
    <main id="main" class="narrow-page"><x-brand />
        <div class="card card-padding" style="margin-top:40px">
            <div class="empty-icon"><x-icon name="mail" :size="28" /></div>
            <h1>Un paso más y estás.</h1>
            <p class="muted" style="margin:18px 0">Enviamos un enlace a <strong>{{ auth()->user()->email }}</strong>.
                Confirmá tu dirección para publicar tu catálogo. Mientras tanto podés cargar tus productos.</p>
            <x-alerts />
            <form method="post" action="{{ route('verification.send') }}">@csrf<button class="button dark">Reenviar
                    verificación</button></form><a class="text-link" style="display:block;margin-top:20px"
                href="{{ route('dashboard') }}">Seguir preparando mi catálogo →</a>
            @if (app()->environment(['local', 'e2e']))
                <a class="local-mail-link" href="{{ route('local.mail') }}">Abrir correo de desarrollo</a>
            @endif
        </div>
    </main>
</x-base>
