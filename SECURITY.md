# Seguridad y operación

Propuesta para fase 1. “Seguridad básica completa” significa controles mínimos probados frente a amenazas concretas, no garantía de ausencia de vulnerabilidades.

| Amenaza | Control | Verificación previa a lanzamiento |
|---|---|---|
| IDOR/lectura de otro comercio | Contexto autenticado, Policies, filtros y relaciones compuestas | Usuario A intenta leer/editar/exportar pedidos, fotos, categorías y productos B |
| Consulta pública de borradores | Scope explícito publicado/disponible; preview privado | URLs e IDs conocidos no revelan drafts |
| Acceso roto | Hash de contraseña estándar, regeneración de sesión, cookies Secure/HttpOnly/SameSite, TLS | Login, logout, reset expirado/reutilizado, verificación email y rate limits |
| CSRF | Protección de mutaciones y validación de origen donde aplique | Peticiones sin token; no confiar en CORS como autorización |
| XSS | Escape Blade, descripción texto plano, tokens CSS permitidos, CSP | Nombres/descr./emails/formularios con payloads HTML/JS |
| Inyección SQL | Query builder/bindings; allowlist de campos de orden | Filtros y búsqueda maliciosos |
| Subidas maliciosas | MIME real, reencodificación, cuota, límites de píxeles/bytes, originales privados | Archivo falso, SVG, polyglot, dimensiones extremas y cuota concurrente |
| Precio/cantidad manipulado | Cálculo servidor, productos del mismo negocio | Importe falso, negativo, cantidad excesiva y producto ajeno |
| Duplicados | Idempotencia y transacción, número único | Doble toque, timeout/reintento y requests concurrentes |
| Spam de solicitudes | Límite por sesión/IP/comercio, honeypot, límites de payload | Ráfagas sin bloquear automáticamente todas las redes compartidas |
| Agotamiento de recursos | Límites globales, jobs cortos, cuotas, monitoreo | Carga de imágenes/importación paralela |
| Fuga de caché | Claves por shop/version; excluir datos privados | Visitar dos tiendas y sesiones distintas |
| Falsificación de pagos futura | Firma, consulta API, cuenta/monto/moneda y conciliación | Firma falsa, evento duplicado/fuera de orden, cuenta equivocada |
| Fallo de email | Outbox y reintentos sin revertir pedido | SMTP caído después del commit |
| Pérdida operativa | Backup externo cifrado y restauración ensayada | Restaurar negocio/BD/objetos y claves necesarias |

## Reglas de aislamiento

Resolver shop desde membresía/propiedad comprobada; en público, desde slug válido. No aceptar shop_id del cuerpo como autoridad. El scope global es defensa adicional, no sustituto de autorización. Jobs y comandos también llevan contexto; evitar consultas sin tenant en tareas normales. Las operaciones de mantenimiento cross-tenant deben ser explícitas, auditadas y no expuestas a usuarios.

Objetos publicados son públicos por intención, pero nunca conceden permiso para modificar. Logos/fotos borrador, originales y documentos privados no quedan expuestos por conocer una URL. No permitir que un comercio borre un objeto cambiando su key.

## Antispam

Límites iniciales orientativos: 3 solicitudes/10 min por sesión, umbral de riesgo por IP y máximo de líneas/unidades por solicitud. Afinar con pilotos; no declarar un IP como comprador único. Límite global de abuso por negocio con alertas. Desafío CAPTCHA solo al subir riesgo, respetando accesibilidad; no como barrera universal.

Teléfono proporcionado no equivale a teléfono verificado. Identificarlo así en panel; no permitir que la plataforma envíe campañas a contactos no verificados. Avisos iniciales solo al vendedor con email verificado. Desactivar notificaciones por rebotes/abuso sin borrar solicitudes válidas.

## Privacidad y datos personales

Minimizar: nombre/teléfono para pedido; no DNI, domicilio ni datos de tarjeta en MVP. Informar quién recibe el dato y para qué. No condicionar un pedido al consentimiento de marketing. No añadir rastreadores publicitarios antes de resolver consentimiento y necesidad.

La [AAIP](https://www.argentina.gob.ar/aaip/datospersonales/responsables/obligaciones) y la [Ley 25.326](https://www.argentina.gob.ar/normativa/nacional/64790/actualizacion) requieren revisar información al titular, seguridad, derechos y transferencias. Antes de lanzamiento: definir rol SaaS/comercio, política de privacidad, acuerdo de tratamiento, proveedores/regiones, mecanismo de acceso/rectificación/supresión y eventual registro correspondiente. No asumir que una política genérica resuelve la obligación. Facturación y normativa de consumo del SaaS se revisan con asesoramiento local, sin construir un ERP.

Propuesta de retención a validar legalmente: datos de solicitudes 12 meses o menos si el propósito termina; logs técnicos 30 días; señales antispam efímeras; outbox enviada 30 días; auditoría de seguridad 90 días. Suscripciones/tributación pueden necesitar plazos distintos. Supresión incluye proceso de expiración de backups y listado de exclusiones para no reintroducir datos al restaurar.

IP solo cuando es necesaria para seguridad, con retención breve; analítica agregada y sin fingerprint. Hash de identificador no lo convierte automáticamente en anónimo. No PII en URL, métricas, logs ni herramientas de agentes.

## Operación

- Secretos en entorno, nunca Git ni frontend; backup separado de claves, acceso mínimo, rotación y revocación. `APP_DEBUG=false`; publicar solo `public/`.
- Cuenta administrativa de plataforma separada, MFA y registros de acciones. No impersonación silenciosa.
- Backup externo diario de BD y objetos; objetivo inicial propuesto RPO ≤24 h/RTO ≤4 h validado en restauración. Evaluar BD horaria para pedidos si el hosting lo permite; comunicar el RPO real, no el deseado.
- Alertas de 5xx, latencia, antigüedad de outbox, cuota SMTP, espacio, caducidad SSL y fallos de backup. Probar alerta fuera del proveedor de correo que se está monitoreando.
- Restauración mensual del piloto y antes de migraciones relevantes; prueba de integridad de fotos/pedidos.
- Canal Reportar catálogo, reglas contra contenido ilícito/phishing y suspensión auditada. No convertirlo en plataforma de reseñas desde el MVP.
- Dependencias actualizadas, lockfiles, revisión de vulnerabilidades y licencias. Cron con bloqueo y limpieza acotada.

## Pruebas que importan

Feature/integración en MySQL real para aislamiento, claves compuestas, cuotas y concurrencia; no limitarlo a SQLite. E2E: alta→publicación→precio nuevo→carrito→solicitud única→panel; email caído; cambio de precio durante compra; reintento tras timeout. No tests de bajo valor que solo repitan getters.

Pagos futuros requieren pruebas negativas propias antes de habilitarse. No lanzar con un fallo conocido de aislamiento, autenticación o cálculo de importes, aunque el diseño esté terminado.
