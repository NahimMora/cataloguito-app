# Arquitectura propuesta

Estado: arquitectura del MVP implementada y autorizada. Consulta técnica original: 26/10/2026; implementación: 27/10/2026. El conjunto fijado en composer.lock requiere PHP 8.4+. Motor efectivo probado: MariaDB 11.8.9, sin Redis ni procesos permanentes obligatorios. La comparación siguiente conserva el razonamiento original; las integraciones futuras no están implementadas.

## 1. Decisión y comparación

**Laravel 13, PHP soportado compatible (mínimo 8.3), Blade, Alpine.js, Tailwind compilado con Vite y MySQL.** Preferir PHP 8.4/8.5 si extensiones y hosting se verifican. No fijar una versión antigua solo porque aparezca en un autoinstalador. MariaDB es alternativa si se ejecutan migraciones y pruebas sobre su versión real; no asumir equivalencia absoluta con MySQL.

La [guía Laravel 13](https://laravel.com/docs/13.x/deployment) exige PHP ≥8.3 y publicar únicamente el directorio público. [Hostinger documenta Laravel y cron](https://www.hostinger.com/support/which-programming-languages-and-frameworks-are-supported-at-hostinger/), pero parte de esa página tiene referencias atrasadas. Verificar runtime, CLI y extensiones en el plan concreto.

| Criterio | Laravel/Blade | Go/plantillas | Next.js/React |
|---|---|---|---|
| Alojamiento | PHP por solicitud, muy compatible con hosting convencional | Binario/servidor persistente; normalmente VPS o plataforma compatible | Runtime Node administrado o VPS; exportación estática no cubre este SaaS |
| Memoria | Depende del número de workers, ORM e imágenes | Potencial de runtime contenido; medir carga real | Runtime, build y caché consumen recursos; medir renderizado e imágenes |
| Público rápido | SSR sin hidratación de React | SSR con JS mínimo | SSR/Server Components; exige controlar componentes cliente |
| Autenticación/seguridad | Ecosistema integrado, Policies, CSRF, validación | Hay que elegir/integrar más piezas | Depende de librerías/servicios, autorización igualmente propia |
| UI y temas | Blade/componentes/CSS, suficiente para el diseño | Igual capacidad visual; más integración de formularios | Excelente ecosistema de UI; innecesario para la interacción inicial |
| Miles de productos | Índices, paginación, consultas acotadas | Misma necesidad de SQL/índices | Misma necesidad; no enviar catálogo entero al cliente |
| Procesar imágenes | GD/Imagick según hosting; trabajos cortos | Librerías, codecs y binarios según despliegue | Sharp y optimización; vigilar memoria/caché |
| Generar imágenes promocionales | Posible con composición gráfica externa o librería; fuera de MVP | Posible; no ventaja decisiva | Posible; no ventaja decisiva |
| Mercado Pago | HTTP/SDK compatible; monolito apropiado | HTTP oficial; viable | HTTP/SDK compatible; viable |
| Trabajo con agentes | Convenciones claras y menos decisiones propias | Viable; más contratos a diseñar | Viable; más fronteras servidor/cliente y caché |
| Mantenimiento estimado | Mejor ajuste al fundador y alcance | Runtime simple; mayor construcción de aplicación | Más complejidad frontend de la necesaria |

No se inventan MB por framework ni solicitudes por segundo. El ranking es de ajuste al proyecto, no benchmark. [Go](https://go.dev/doc/tutorial/web-service-gin) sirve aplicaciones HTTP como proceso; [Next.js](https://nextjs.org/docs/app/guides/self-hosting) documenta servidor, optimización y caché. [Hostinger admite Next.js en Business/Unlimited y Cloud](https://www.hostinger.com/support/how-to-deploy-a-nodejs-website-in-hostinger/), con [restricciones de workers y servicios](https://www.hostinger.com/support/hostinger-node-js-limits-for-web-and-cloud-plans/). No descartarlo por una supuesta incompatibilidad universal.

No añadir un cuarto stack sin una ventaja objetiva. No usar microservicios, Redis obligatorio, Kubernetes, SPA separada ni motor de búsqueda externo en el MVP.

## 2. Monolito modular

Módulos lógicos: Identidad, Comercios, Catálogo, Apariencia, Solicitudes, Notificaciones y Límites. Facturación SaaS y Pagos de comercios se incorporan después. Una aplicación, una base relacional, una implementación de catálogo compartida.

Presentación: controladores delgados, Form Requests, Blade y componentes. Casos de uso: acciones como guardar precio, publicar apariencia y registrar solicitud. Persistencia: Eloquent, transacciones e índices. Integraciones: adaptadores de almacenamiento, email, enlace WhatsApp y futuro proveedor de pago. No envolver cada modelo en un repositorio vacío; separar allí donde existe una frontera real.

Alpine se reserva para carrito, formularios pequeños, navegación y preview. Node/Vite compilan en desarrollo/CI; no se necesita Vite corriendo en producción. Autenticación de sesiones, componentes de Laravel/Fortify si conviene; no criptografía ni recuperación inventadas.

## 3. Consistencia de catálogo

- Los productos son una única fuente de verdad. Guardar precio incrementa versión del producto y `catalog_version` del negocio en la transacción.
- Actualización pública visible en la siguiente lectura tras guardar. Una página ya abierta se revalida al recuperar foco o revisar el carrito; no prometer push en tiempo real sin implementarlo.
- Inicialmente no cachear HTML con precios en CDN; cachear assets con nombres versionados. Si se añade caché de datos/fragmentos, clave por comercio y versión leída desde origen, no TTL largo a ciegas.
- Evitar caché compartida de sesiones, borradores, pedidos y checkout. Precio de localStorage nunca es autoridad.
- Apariencia usa borrador y versión publicada; publicar reemplaza configuración de forma atómica. Producto y precio no necesitan un flujo editorial complicado.

## 4. Registro de pedido

1. El cliente prepara clave aleatoria de idempotencia para el intento; servidor la vincula a comercio y sesión anónima, con hash del contenido.
2. Resuelve comercio desde la ruta, no desde un `shop_id` confiado del navegador. Solo acepta productos públicos y disponibles de ese comercio.
3. Valida cantidades enteras, importes y tamaño máximo; consulta precios vigentes. Si difieren de la revisión mostrada, retorna una nueva cotización para reconfirmar.
4. En transacción guarda pedido, número atómico por negocio, líneas con snapshots y evento de notificación en outbox. Guarda clave de idempotencia con índice único.
5. Duplicado con mismo contenido y sesión devuelve el mismo pedido; clave repetida con contenido distinto retorna conflicto. Nunca filtrar una respuesta a otra sesión.
6. Tras commit muestra confirmación y enlace `wa.me`. La notificación por correo no interviene en el resultado de la solicitud.

Estados operativos: Pendiente → Confirmado → Completado; Pendiente/Confirmado → Cancelado. Reapertura requiere acción explícita y auditoría. No reserva ni descuenta stock: disponibilidad simple, siempre confirmada por vendedor.

Dinero: enteros en centavos, moneda ARS, cálculos en servidor y límites de overflow. Guardar `unit_price`, cantidad y subtotal de línea. Conservar nombres/precios históricos aunque se edite o archive el producto. El número visible no es credencial para consultar pedidos.

Nombre y teléfono mínimos; sin cuenta comprador. Pedidos visibles solo al dueño en dashboard y al comprador en su sesión de confirmación. No crear una URL pública adivinable con datos personales. Links largos a WhatsApp: número/total y enlace al dashboard autenticado del vendedor; resumen completo continúa visible en el navegador.

## 5. Correo y trabajos cortos

**Outbox en MySQL en la misma transacción del pedido.** Cron toma lotes limitados, con bloqueo/lease que expire si el proceso muere. Puede usarse cola database de Laravel, pero el puente desde outbox debe ser idempotente. Evita el hueco entre commit y envío a un proveedor externo.

Ejecución propuesta cada minuto, acotada a 40–50 segundos, sin solapamiento, pocos trabajos por lote. [Laravel documenta colas](https://laravel.com/docs/13.x/queues) y [planificación](https://laravel.com/docs/13.x/scheduling); [Hostinger permite cron](https://support.hostinger.com/en/articles/1583465-how-to-set-up-a-cron-job-at-hostinger). Intervalo real y CLI deben verificarse. Si el plan solo admite cada 5 minutos, comunicar esa latencia y no vender aviso instantáneo.

Email al destinatario verificado del negocio: comercio, número, fecha, líneas, cantidades, total estimado, contacto y enlace autenticado. Remitente de plataforma con SPF/DKIM/DMARC; no suplantar dominio del comercio. Escapar texto del comprador y limitar longitudes.

Reintentos sugeridos: 1, 5, 15, 60 minutos, máximo 5 intentos; bandeja de fallidos y alerta de antigüedad. Idempotencia por evento/destinatario; SMTP no garantiza exactamente una entrega ante caída posterior a aceptación. Registrar esa limitación, evitar duplicar pedidos aunque un email se repita.

Límites iniciales de notificación por negocio/destinatario, por ejemplo 30/h y 100/día, ajustables con evidencia. Al superar: agrupar avisos y mostrar contador, no perder pedidos. Límites de seguridad separados para recuperación/verificación. Supresión de rebotes permanentes y quejas. Si SES no aprueba producción, usar otro SMTP transaccional cotizado manteniendo el mismo adaptador.

## 6. Imágenes y escala

Aceptar JPEG/PNG/WebP; verificar MIME real, dimensiones, peso y decodificación. Meta inicial de entrada: ≤5 MB y ≤12 megapíxeles por archivo, a validar con memoria real. Reencodificar, orientar y retirar EXIF. Rechazar SVG de usuario y archivos ejecutables; no descargar URLs arbitrarias.

Derivados sugeridos 320/640/1200 px, `srcset`, tamaños declarados y lazy load salvo imagen crítica. No procesar 500 imágenes en una solicitud; lote corto e indicación de pendientes. Conservar imagen anterior hasta finalizar la nueva. Originals temporales privados y eliminación tras procesamiento; límite incluye maestra optimizada/derivados.

Piloto: almacenamiento local si hay respaldo externo y capacidad suficiente; recomendado R2/S3 compatible al abrir adquisición por bajo costo y separación del disco. PHP sirve HTML, objetos sirven imágenes. Una clave de almacenamiento nunca da permiso para editar datos.

Listados paginados de 24–36 productos. Búsqueda SQL acotada por negocio: empezar simple, medir búsquedas por nombre/SKU; usar FULLTEXT solo si la semántica y el motor lo justifican. No afirmar que un B-tree resuelve búsquedas `LIKE %texto%`. Índices compuestos y evitar N+1. Tablas de 100.000 productos totales y tiendas de 500/2.000 son escenarios de prueba, no límites contratados automáticamente.

Objetivos: JS público propio comprimido ≤35 KB, CSS ≤30 KB, fuentes ≤100 KB por tema, carga inicial alrededor de ≤1 MB con imágenes seleccionadas. Metas de ingeniería, sujetas a medición. [Core Web Vitals](https://web.dev/articles/vitals): LCP ≤2,5 s, INP ≤200 ms, CLS ≤0,1 en percentil 75 real cuando exista tráfico. Pruebas de laboratorio no certifican el percentil de campo.

## 7. URLs y dominios

- Ruta pública `/{slug}`; rutas de sistema antes del comodín. Reservar `app`, `admin`, `api`, `login`, `registro`, `precios`, `ayuda`, `legal`, `assets`, `storage`, `robots.txt`, `sitemap.xml`, entre otras.
- Normalizar slug ASCII y minúsculas; unicidad global incluida tabla de alias; no reutilizar aliases de tiendas anteriores.
- Nombre comercial cambia libremente, URL no cambia automáticamente. Cambio de slug excepcional con previsualización; redirect 301 conservando ruta hija. No cadenas de redirecciones.
- Producto `/{tienda}/p/{id-publico}/{slug-legible}`; categoría equivalente. ID estable resuelve cambios de título; canonical a URL vigente. Producto archivado muestra estado o 410 según política, nunca datos privados.
- Open Graph generado en servidor con imagen propia, título y moneda. La caché de redes sociales puede demorar en actualizar aunque el catálogo ya muestre el nuevo precio.
- Subdominios y dominios propios posteriores: prueba de propiedad TXT, CNAME soportado, control de Host permitido, SSL/renovación, monitoreo y retirada segura del dominio. Evitar secuestro de dominios abandonados. No aceptar cualquier Host como comercio.

## 8. Mercado Pago: dos contabilidades separadas

### A. Abono al SaaS

El fundador es cobrador. `SaasBilling` gestiona planes/versiones, acuerdos recurrentes y pagos del abono. La [API de suscripciones](https://www.mercadopago.com.ar/developers/es/docs/subscriptions/overview) admite frecuencia y planes; [gestión](https://www.mercadopago.com.ar/developers/es/docs/subscriptions/subscription-management) permite cambios, pausa/cancelación. Estados del proveedor se conservan como datos externos; nuestro acceso usa trial, activo, gracia, vencido y cancelación programada.

No confundir acuerdo autorizado con cuota aprobada. Acceso pago se activa/extiende por cobro aprobado y verificado de la cuota correcta; prueba gratuita tiene vencimiento independiente. `paid_through` evita extender dos veces por el mismo evento. Rechazo → aviso y gracia definida (propuesta 7 días); conservar panel/exportación. No iniciar otro débito propio mientras el proveedor reintenta.

Cambio de plan inicialmente al próximo ciclo, sin prorrateo complejo. Cancelar renovación conserva acceso hasta fin del período pagado. Antes de ejecutar revisar contrato/provider: cancelar el plan comercial no necesariamente cancela cada suscripción. Conciliación diaria por API y alertas ante estados divergentes.

### B. Venta del comerciante

`MerchantPayments` usa autorización OAuth de cada vendedor y su cuenta receptora. Una orden pertenece a un solo comercio; no carrito multi-vendedor ni agregación de fondos del SaaS. [Mercado Pago documenta OAuth](https://www.mercadopago.com.ar/developers/es/docs/split-payments/additional-content/security/oauth/creation) y [checkout 1:1](https://www.mercadopago.com.ar/developers/en/docs/split-payments/split-1-1/integration-configuration/integrate-marketplace). El término técnico “Marketplace” del proveedor no obliga a construir un marketplace público.

Validar elegibilidad KYC, país, producto, permisos y requisitos de la aplicación antes de ofrecerlo; [1:N tiene restricciones adicionales](https://www.mercadopago.com.ar/developers/es/docs/split-payments/split-1-1/prerequisites) y no se necesita aquí. No prometer universalidad ni métodos no aprobados. Empezar con Checkout Pro si se aprueba: pagos en experiencia del proveedor.

Tokens por negocio cifrados en servidor, estado OAuth y PKCE según flujo, refresh con bloqueo y rotación; revocación de conexión no elimina pedidos. Cero datos de tarjeta en nuestra aplicación. Comisiones del procesador a cargo del vendedor según su contrato; no inventar una tarifa común para ventas y abonos.

### Verificación y webhooks

[Webhooks oficiales](https://www.mercadopago.com.ar/developers/es/docs/checkout-bricks/additional-content/your-integrations/notifications/webhooks): verificar firma según producto, timestamp y tolerancia, persistir evento con identificación única y responder rápido; procesar después. Consultar recurso por API con credenciales correctas y cotejar cuenta receptora, entorno, referencia, monto, moneda y estado.

Separar eventos de suscripción y ventas. Procesar duplicados/fuera de orden sin regresiones; no deduplicar eternamente solo por ID del pago, porque el mismo pago puede reembolsarse después. Conciliar eventos perdidos por cron. Devolución/contracargo cambia estado financiero sin fingir que el producto dejó de haberse entregado.

Ni redirect de navegador, ni comprobante, ni captura de pantalla prueban un pago. La fase 1 no contiene integración de cobros.

### WhatsApp Business Platform futura

Es una integración distinta de wa.me: alta y permisos de la cuenta empresarial, número habilitado, autorización del destinatario conforme a las políticas aplicables y plantillas aprobadas cuando correspondan. No automatizar WhatsApp Web ni usar sesiones personales como sustituto de la API oficial.

La [tarifa oficial](https://whatsappbusiness.com/products/platform-pricing/) se basa en mensajes entregados, mercado y categoría; distingue ventanas gratuitas de servicio. No presupuestar por conversación con una tarifa antigua. Cotizar al implementar: mensajes facturables × tarifa Argentina/categoría vigente + eventual abono/margen del proveedor + soporte. No se verificó una tarifa numérica argentina ni elegibilidad de nuestra futura cuenta; quedan pendientes. Aviso al vendedor y mensaje al comprador son casos diferentes y requieren consentimiento/plantilla adecuados. El email sigue siendo el canal económico inicial.

## 9. Puerta de despliegue

Verificar plan contratado: PHP web/CLI, extensiones (GD/Imagick y codecs incluidos), Composer/build fuera del servidor si conviene, raíz pública segura, MySQL, SSH, cron, timeout, memoria, CPU, procesos, inodos, disco, conexiones SMTP/HTTPS y restauración. No tomar “sitios ilimitados” por CPU o almacenamiento ilimitados.

Staging separado con datos ficticios, pruebas de concurrencia/precios/pedidos, correo sandbox, carga representativa y restauración. Medir antigüedad de outbox, errores, latencia, uso de disco y saturación SQL. Migrar a VPS/Cloud cuando las métricas del recurso lo exijan, no al llegar a una cifra arbitraria de tiendas. Producción requiere autorización expresa del fundador.
