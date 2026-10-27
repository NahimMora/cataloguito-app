# Estado de implementación — 27/10/2026

Autorización: el fundador pidió implementar todo el MVP de fase 1 y dejarlo usable. Producción y compras externas siguen fuera de alcance.

## Criterios de terminación

- Registro/login/reset/verificación; crear y configurar un negocio.
- Productos/categorías, foto segura, disponibilidad, archivo, precio rápido con concurrencia.
- Publicación estable, tres familias visuales y preview; siete muestras por rubro; catálogo público responsive, buscador, categorías y enlaces individuales.
- Carrito local por tienda, cotización servidor, contacto mínimo, solicitud idempotente y WhatsApp revisable.
- Panel de pedidos, estados e historial; aislamiento entre negocios.
- Email desacoplado con reintentos; cuotas, exportación propia, cuenta y baja/pausa.
- Demo, comandos de instalación/ejecución, pruebas significativas y comprobación visual.

## Estado actual

**MVP terminado y usable en local.** Todos los criterios anteriores están implementados. Laravel 13, PHP portátil 8.4.27 y MariaDB 11.8.9 exclusiva en puerto 3318. XAMPP no se modificó.

Abrir `ABRIR_CATALOGUITO.bat`. Aplicación: http://127.0.0.1:8000 . Catálogo: http://127.0.0.1:8000/casa-luma . Cuenta ficticia: `demo@cataloguito.test` / `CataloguitoDemo2026`. El teléfono de la demo es ficticio; configurar uno propio para WhatsApp. Los enlaces locales no funcionan fuera de esta computadora.

También se puede crear una cuenta propia. Verificación y recuperación se completan desde **Correo de desarrollo**, sin SMTP externo. El visor está restringido al usuario/sesión y loopback. Pedido registrado no significa mensaje enviado, venta realizada ni pago aprobado.

Extras incluidos: exportación CSV segura, archivo reversible, historial de pedidos, cuotas Gratis/Pro manual, cambio de contraseña con invalidación de otras sesiones, baja de cuenta, metadatos y sitemap. Sin cobros automáticos.

## Verificación ejecutada

- `npm run test:backend`: **35 pruebas, 205 aserciones**, incluida concurrencia real, aislamiento entre comercios, teléfonos por país, estilos por rubro con color, formato monetario argentino, errores de alta, robots y sitemap.
- `npm run test:e2e`: **4 recorridos aprobados**, alta/verificación/foto/precio/publicación/pedido/gestión, accesibilidad/teclado/móvil/escritorio, landing/registro mobile-first y las siete demos completas con paletas navegables.
- `npm run build`: correcto. CSS 68,28 KB / 15,04 KB gzip; JS 59,11 KB / 21,07 KB gzip; fuente local 24,83 KB.
- Laravel Pint aplicado. Backup restaurado en `vitrina_restore_test`, SQL y 24 archivos de imagen verificados con hashes.
- Lighthouse móvil local de la landing final: rendimiento **97**, accesibilidad **100**, SEO **100**; FCP 1,7 s, LCP 2,4 s, TBT 20 ms y CLS 0. Laboratorio sobre servidor de desarrollo, no Core Web Vitals reales ni garantía de producción. Informe `.tools/lighthouse.json`; repetir con `npm run test:performance`.
- Axe no encontró infracciones en las reglas WCAG evaluadas; no sustituye una auditoría completa.

Bases aisladas: `vitrina` uso local, `vitrina_test` backend, `vitrina_e2e` navegador y `vitrina_restore_test` restauración. Herramientas, secretos, datos e informes locales excluidos de Git. CI preparada, aún sin ejecución remota.

## Antes de un lanzamiento real

Configurar HTTPS, SMTP verificado, cron, respaldo externo cifrado, identidad legal/retención y monitoreo. Ejecutar aceptación sobre el hosting concreto y obtener autorización para producción. Ver `DEPLOYMENT.md`.

Fuera de fase 1: cobros/suscripciones automáticas, importación XLSX, variantes, stock numérico, dominios propios y API de WhatsApp. El comando Pro manual no verifica pagos. El nombre Cataloguito todavía requiere verificación marcaria y de dominio.

## Continuación ante límite del proveedor

Actualizar este archivo al cerrar cada bloque con lo terminado, comandos/resultados y siguiente acción concreta. No afirmar que una reanudación automática está programada si la aplicación no brinda esa capacidad. Una sesión reanudada debe leer primero este documento y AGENTS.md, revisar git status y continuar sin rehacer trabajo.

No se alcanzó un límite diario ni se programó una reanudación automática durante esta implementación.

## Estrategia Cataloguito completada

La estrategia de `CATALOGUITO_PLAN.md` quedó implementada y verificada: marca cálida con coral dominante e ícono propio, landing compacta e interactiva, siete páginas de muestra completas y diferenciadas por rubro, elección de plantilla y color persistente, familia Boutique, panel superior más denso, precios con formato argentino, alta sin desplazamiento inicial innecesario, errores asociados a campos, teléfonos con país separado y Argentina por defecto, avisos corregidos, fotografías originales y base SEO técnica/editorial.
