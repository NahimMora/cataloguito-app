# Estado de implementación — 27/10/2026

Autorización: el fundador pidió implementar todo el MVP de fase 1 y dejarlo usable. Producción y compras externas siguen fuera de alcance.

## Criterios de terminación

- Registro/login/reset/verificación; crear y configurar un negocio.
- Productos/categorías, foto segura, disponibilidad, archivo, precio rápido con concurrencia.
- Publicación estable, dos temas y preview; catálogo público responsive, buscador, categorías, enlaces individuales.
- Carrito local por tienda, cotización servidor, contacto mínimo, solicitud idempotente y WhatsApp revisable.
- Panel de pedidos, estados e historial; aislamiento entre negocios.
- Email desacoplado con reintentos; cuotas, exportación propia, cuenta y baja/pausa.
- Demo, comandos de instalación/ejecución, pruebas significativas y comprobación visual.

## Estado actual

**MVP terminado y usable en local.** Todos los criterios anteriores están implementados. Laravel 13, PHP portátil 8.4.27 y MariaDB 11.8.9 exclusiva en puerto 3318. XAMPP no se modificó.

Abrir `ABRIR_VITRINA.bat`. Aplicación: http://127.0.0.1:8000 . Catálogo: http://127.0.0.1:8000/casa-luma . Cuenta ficticia: `demo@vitrina.test` / `VitrinaDemo2026`. El teléfono de la demo es ficticio; configurar uno propio para WhatsApp. Los enlaces locales no funcionan fuera de esta computadora.

También se puede crear una cuenta propia. Verificación y recuperación se completan desde **Correo de desarrollo**, sin SMTP externo. El visor está restringido al usuario/sesión y loopback. Pedido registrado no significa mensaje enviado, venta realizada ni pago aprobado.

Extras incluidos: exportación CSV segura, archivo reversible, historial de pedidos, cuotas Gratis/Pro manual, cambio de contraseña con invalidación de otras sesiones, baja de cuenta, metadatos y sitemap. Sin cobros automáticos.

## Verificación ejecutada

- `npm run test:backend`: **30 pruebas, 166 aserciones**, incluida concurrencia real con dos procesos y aislamiento entre comercios.
- `npm run test:e2e`: **2 recorridos aprobados**, alta/verificación/foto/precio/publicación/pedido/gestión y accesibilidad/teclado/móvil/escritorio.
- `npm run build`: correcto. CSS 40,29 KB / 9,47 KB gzip; JS 58,94 KB / 21,02 KB gzip; fuente local 24,83 KB.
- Laravel Pint aplicado. Backup restaurado en `vitrina_restore_test`, SQL y 24 archivos de imagen verificados con hashes.
- Lighthouse móvil local: rendimiento **87**, accesibilidad **100**, SEO **100**; FCP 2,7 s, LCP 3,4 s, TBT 110 ms y CLS 0. Laboratorio sobre servidor de desarrollo, no Core Web Vitals reales ni garantía de producción. Informe `.tools/lighthouse.json`; repetir con `npm run test:performance`.
- Axe no encontró infracciones en las reglas WCAG evaluadas; no sustituye una auditoría completa.

Bases aisladas: `vitrina` uso local, `vitrina_test` backend, `vitrina_e2e` navegador y `vitrina_restore_test` restauración. Herramientas, secretos, datos e informes locales excluidos de Git. CI preparada, aún sin ejecución remota.

## Antes de un lanzamiento real

Configurar HTTPS, SMTP verificado, cron, respaldo externo cifrado, identidad legal/retención y monitoreo. Ejecutar aceptación sobre el hosting concreto y obtener autorización para producción. Ver `DEPLOYMENT.md`.

Fuera de fase 1: cobros/suscripciones automáticas, importación XLSX, variantes, stock numérico, dominios propios, API de WhatsApp y tercera plantilla. El comando Pro manual no verifica pagos. Nombre Vitrina provisional.

## Continuación ante límite del proveedor

Actualizar este archivo al cerrar cada bloque con lo terminado, comandos/resultados y siguiente acción concreta. No afirmar que una reanudación automática está programada si la aplicación no brinda esa capacidad. Una sesión reanudada debe leer primero este documento y AGENTS.md, revisar git status y continuar sin rehacer trabajo.

No se alcanzó un límite diario ni se programó una reanudación automática durante esta implementación.
