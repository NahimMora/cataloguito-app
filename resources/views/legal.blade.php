<x-base :title="($kind === 'privacy' ? 'Privacidad' : 'Términos de uso') . ' · Vitrina'">
    <main id="main" class="narrow-page"><x-brand />
        <article class="card card-padding legal-content" style="margin-top:35px">
            <h1>{{ $kind === 'privacy' ? 'Tus datos y tu privacidad' : 'Términos de uso' }}</h1>
            <p style="margin-top:20px">Responsable de la plataforma: {{ config('vitrina.operator_name') }}. Domicilio:
                {{ config('vitrina.operator_address') }}. Contacto: <a
                    href="mailto:{{ config('vitrina.support_email') }}">{{ config('vitrina.support_email') }}</a>.</p>
            @if ($kind === 'privacy')
                <h2>Para qué usamos los datos</h2>
                <p>Usamos nombre, email y contraseña protegida para administrar tu cuenta de comerciante. Al confirmar
                    una solicitud, el comprador comparte nombre, teléfono y los productos elegidos con el comercio
                    correspondiente para que pueda responderle. El teléfono informado no se verifica automáticamente.
                </p>
                <h2>Acceso y conservación</h2>
                <p>El comerciante accede a sus propios productos y solicitudes. Los datos se conservan durante el tiempo
                    necesario para prestar el servicio y cumplir obligaciones aplicables. No usamos las solicitudes para
                    campañas publicitarias ni vendemos datos personales. Las fotografías publicadas y la información del
                    catálogo son públicas.</p>
                <h2>Tus derechos</h2>
                <p>Podés solicitar acceso, rectificación o supresión mediante el contacto indicado. El comerciante puede
                    exportar sus productos y eliminar su cuenta desde el panel. La eliminación de respaldos sigue su
                    ciclo de conservación; el operador debe informar su política efectiva antes de operar
                    comercialmente.</p>
                <h2>Sesiones y servicios externos</h2>
                <p>Usamos cookies de sesión para mantener acceso, proteger formularios y recuperar solicitudes. El
                    carrito se conserva en este navegador hasta siete días. WhatsApp se abre únicamente al seguir un
                    enlace y aplica sus propias condiciones. El correo transaccional y el alojamiento dependen de los
                proveedores configurados por el operador.</p>@else<h2>Qué ofrece Vitrina</h2>
                <p>Una plataforma de catálogos y solicitudes de pedido. El comercio es responsable por sus productos,
                    precios, fotografías, condiciones de venta, atención y cumplimiento de las obligaciones que le
                    correspondan.</p>
                <h2>Solicitudes y ventas</h2>
                <p>Una solicitud registrada no constituye confirmación de disponibilidad ni prueba de pago. Abrir
                    WhatsApp prepara un mensaje que el comprador debe revisar y enviar. El comercio y el comprador
                    coordinan pago, entrega y condiciones finales.</p>
                <h2>Uso responsable</h2>
                <p>Publicá contenido propio o autorizado, mantené los datos correctos y no uses la plataforma para
                    fraude, spam o productos ilícitos. El operador puede suspender catálogos abusivos con un
                    procedimiento documentado de revisión.</p>
                <h2>Plan y baja</h2>
                <p>El plan Gratis permite hasta 50 productos activos y 50 MB de imágenes procesadas. El piloto Pro se
                    administra mediante acuerdo expreso; esta versión no realiza cobros automáticos. Podés pausar el
                    catálogo, exportar productos o eliminar tu cuenta desde el panel.</p>
                @endif @if (app()->environment(['local', 'e2e']))
                    <div class="alert info">Versión local de demostración. La identidad legal, proveedores, plazos de
                        conservación y condiciones comerciales deben completarse antes del lanzamiento público.</div>
                @endif
                <a class="text-link" href="{{ route('home') }}">Volver al inicio →</a>
        </article>
    </main>
</x-base>
