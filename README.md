# Vitrina — MVP de catálogos vivos

Estado: **MVP implementado para uso y verificación local**, autorizado por el fundador después de la investigación. Laravel 13, PHP 8.4 portátil, MariaDB 11.8 aislada y assets compilados. Producción no desplegada. Nombre Vitrina provisional; no se afirma disponibilidad marcaria. La investigación original se conserva como antecedente.

## Abrir en esta computadora

Doble clic en **ABRIR_VITRINA.bat**, o ejecutar:

```powershell
powershell -ExecutionPolicy Bypass -File .\scripts\start.ps1
```

Aplicación: http://127.0.0.1:8000 · Catálogo de muestra: http://127.0.0.1:8000/casa-luma . El acceso es local; este enlace no funciona desde otros celulares/computadoras sin desplegar o configurar una red autorizada.

Cuenta ficticia de demostración: **demo@vitrina.test** / **VitrinaDemo2026**. También podés registrarte y crear tu propio comercio. La demo usa teléfono ficticio: no se debe interpretar su enlace WhatsApp como destino real.

El script inicia la base exclusiva del proyecto si hace falta, el servidor y un procesador local de avisos cada 15 segundos, sin ventanas auxiliares. XAMPP no fue modificado. Para detener servidor/avisos: `powershell -ExecutionPolicy Bypass -File .\scripts\stop.ps1`. La base queda disponible para evitar interrumpir pruebas/respaldos.

## Qué podés usar

Registro, login, recuperación y verificación; comercio y WhatsApp; categorías; productos con foto optimizada, precio anterior, SKU opcional, disponibilidad y archivo reversible; edición de precio en lista con conflictos detectados; exportación CSV; publicación/pausa; temas Minimal/Comercial con borrador y vista previa; catálogo responsive, búsqueda, orden y enlaces individuales; carrito por tienda; cotización revalidada; solicitud única; recibo privado; mensaje wa.me revisable; bandeja de pedidos/estados/historial; cuenta, cuotas y baja.

Los emails se capturan en **Correo de desarrollo** (`/dev/mail`) para poder verificar/resetear cuentas sin proveedor externo. Solo muestra correos del usuario/sesión y solo está habilitado en local/e2e desde loopback. Para correo real configurar SMTP; la integración de pedidos no depende de que el correo funcione. Las páginas de cuenta/verificación enlazan al visor local.

La demo no se crea durante instalaciones normales: usar `php artisan db:seed --class=DemoSeeder` solo en local/testing/e2e. No ejecutar en producción.

## Instalar en otro entorno

Requisitos: PHP 8.4+ requerido, Composer 2, MariaDB 11.8 (motor probado), Node 22.12+. Ver extensiones/configuración en [DEPLOYMENT.md](DEPLOYMENT.md). MySQL debe pasar la misma suite antes de adoptarse como motor de producción.

```sh
composer install
cp .env.example .env
# Configurar base de datos exclusiva, credenciales y APP_URL en .env.
php artisan key:generate
php artisan migrate
npm ci
npm run build
php artisan serve
```

En otra terminal: `php artisan schedule:work` para desarrollo, o ejecutar `php artisan orders:notify` después de una solicitud. Producción usa cron `schedule:run`; no requiere un servicio permanente.

No compartir `.env`, `.tools`, bases, backups, sesiones ni credenciales reales en Git. Las herramientas portátiles de esta computadora están ignoradas por Git y no son dependencias que deban subirse al hosting.

## Pruebas y mantenimiento

En esta computadora el wrapper elige PHP portátil; en otros entornos usa `PHP_BINARY` o `php` del PATH:

```sh
npm run test:backend
npm run build
npm run test:e2e
```

La suite backend usa exclusivamente `vitrina_test`, configurada en phpunit.xml. El navegador usa `.env.e2e` y `vitrina_e2e` en puerto 8001. Crear ambas bases con permisos para el usuario de pruebas; copiar .env a .env.e2e y cambiar DB_DATABASE/APP_URL/MAIL_MAILER=array. Antes de la primera prueba de navegador: `php artisan migrate --env=e2e`, `php artisan db:seed --class=DemoSeeder --env=e2e`, `npx playwright install chromium`.

Comprobaciones: aislamiento A/B y FKs, imágenes/archivos hostiles, cuotas, edición concurrente, precios alterados/obsoletos, idempotencia, recibo por sesión, estados y fallo/reintento de email. E2E recorre alta→verificación→foto→publicación→precio→pedido→gestión. Incluye análisis automatizado de accesibilidad del catálogo, teclado y capturas móvil/escritorio; no equivale a auditoría WCAG completa ni Core Web Vitals de campo.

`php artisan catalog:backup` crea ZIP privado con SQL, imágenes y hashes; la restauración local se verifica con `scripts/verify-backup.ps1` sobre `vitrina_restore_test`. Transferencia externa cifrada, cron real, SMTP y retención deben configurarse al desplegar. `php artisan catalog:plan tienda pro` habilita un piloto después de conciliación manual; no registra ni verifica un cobro.

La configuración de CI está en `.github/workflows/checks.yml`; queda pendiente su ejecución en un repositorio remoto. No se creó ni publicó un repositorio externo.

## Dirección de producto

Validar un SaaS de catálogo y solicitudes de pedido para comercios argentinos que venden por conversación y tienen productos simples. Empezar por accesorios, regalería y bazar pequeño; comparar con belleza sin variantes complejas. No empezar como plataforma mayorista completa ni tienda integral.

El enlace permanente, el carrito y la apariencia profesional son requisitos de entrada. La ventaja a demostrar es que mantener precios desde el celular lleva menos tiempo, el comerciante entiende qué solicitudes tiene pendientes y el resultado visual funciona sin contratar diseño.

Propuesta técnica: monolito modular Laravel + Blade + Alpine.js + Tailwind compilado + MySQL. Dos composiciones de catálogo sobre componentes comunes. Servicios externos limitados a correo transaccional y almacenamiento de imágenes cuando convenga. Sin Redis obligatorio ni procesos permanentes propios.

Propuesta comercial: piloto limitado, luego Gratis y Pro. Pro a ARS 7.900/mes como precio de prueba, no decisión aprobada. Validar disposición a pagar desde el piloto mediante cobro externo conciliado por el fundador; automatización de suscripciones en fase 3.

## Documentos

| Documento | Contenido |
|---|---|
| [COMPETITIVE_RESEARCH.md](COMPETITIVE_RESEARCH.md) | Matrices competitivas, precios, evidencia, análisis visual y límites de verificación |
| [PRODUCT.md](PRODUCT.md) | Segmento, diferenciación, prioridades, recorridos, monetización, costos, adquisición y validación |
| [ARCHITECTURE.md](ARCHITECTURE.md) | Comparación técnica, decisión, pedidos, correo, caché y pagos futuros |
| [DESIGN_SYSTEM.md](DESIGN_SYSTEM.md) | Dirección original, tokens, navegación, tres familias y accesibilidad |
| [DATABASE.md](DATABASE.md) | Modelo conceptual, relaciones, aislamiento y entidades futuras |
| [SECURITY.md](SECURITY.md) | Amenazas, controles, privacidad, respaldos y pruebas obligatorias |
| [ROADMAP.md](ROADMAP.md) | Cinco fases, estimaciones, responsables, criterios y riesgos |
| [AGENTS.md](AGENTS.md) | Reglas compartidas para trabajo posterior autorizado |
| [CLAUDE.md](CLAUDE.md) | Importación de las reglas compartidas y orientación de frontend |
| [CATALOGUITO_PLAN.md](CATALOGUITO_PLAN.md) | Plan solicitado de nueva marca, landing móvil, siete estilos, formularios, avisos y SEO; todavía sin ejecutar |

## Qué se verificó

Fuentes oficiales comerciales y técnicas, planes y documentación; navegador público a 390 px para Sucuchito y Catalogá; carrito de la demo de Castock. No se crearon cuentas, no se enviaron pedidos, mensajes ni formularios, ni se probaron pagos. No se ejecutaron benchmarks de competidores ni se accedió a dashboards autenticados.

Las afirmaciones comerciales de los proveedores están identificadas como declaradas, no como resultados de una auditoría funcional. Los campos no publicados permanecen desconocidos. Los escenarios financieros son estimaciones, no cotizaciones ni promesas de rentabilidad.

## Alcance de la autorización

El fundador autorizó implementar el MVP de fase 1. La especificación ejecutada está en [MVP_SPEC.md](MVP_SPEC.md), la operación en [DEPLOYMENT.md](DEPLOYMENT.md) y la continuidad en [IMPLEMENTATION_STATUS.md](IMPLEMENTATION_STATUS.md). Las fases 2–5 siguen siendo propuestas. Producción, compras de servicios e integraciones de pago necesitan instrucciones específicas.
