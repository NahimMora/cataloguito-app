# Especificación de fase 1 autorizada

Nombre de producto: **Cataloguito** (sujeto a verificación marcaria y de dominio). Laravel 13/PHP 8.4+, Blade/Alpine/Tailwind/Vite, MySQL/MariaDB. Una tienda por usuario; ARS y unidades enteras. Minimal, Comercial y Boutique sobre una vista compartida. Gratis: 50 productos y 50 MB; piloto Pro manual: 500 y 1 GB. Sin pagos online.

El usuario verifica email antes de publicar, cargar borradores está permitido. En desarrollo, emails en visor local privado para verificar/resetear sin SMTP; en producción, SMTP obligatorio. La demo solo se crea mediante comando explícito en entorno local/testing y usa datos ficticios.

Producto: nombre, descripción texto, precio, precio anterior opcional, categoría opcional, foto principal opcional, disponibilidad, SKU opcional, orden y estado. Subida segura y derivados; máximo 5 MB/12 MP. Precio individual editable con versión. Archivo reversible.

Catálogo publicado en /{slug}; p/{public_id}/{slug} y c/{public_id}/{slug}. Vista previa privada; nombre comercial no cambia URL. Borrador de colores/tema, publicar apariencia explícitamente. Paletas con contraste validado. Logo y redes. Copiar enlace y exportar CSV propio.

Carrito guest en localStorage por tienda, cantidades 1–999, máximo 50 líneas; caduca 7 días. Servidor cotiza con productos públicos actuales. Orden: nombre/teléfono/contacto, subtotal estimado, número por negocio, snapshots; idempotencia por sesión/comercio/clave y hash. Cotización firmada expira a 15 min y se revalida bajo transacción. Cambio de precios exige revisar. Estados Pendiente→Confirmado→Completado, Pendiente/Confirmado→Cancelado. Sin stock numérico ni reserva.

Al confirmar se muestra recibo privado de sesión y wa.me preparado. Nunca marcar mensaje enviado/pagado. Email al dueño verificado mediante outbox transaccional. Fallos no revierten solicitud. Aviso con totales/contacto y dashboard autenticado. Lotes con lease y reintentos.

Panel: Inicio/Productos/Pedidos/Mi catálogo/Cuenta. Búsqueda/paginación en servidor; edición móvil conserva posición si es posible. CSRF, escape, sesión, rate limiting, control de tenant explícito, headers y límites en servidor. Métricas agregadas por día: visitas aproximadas/clic WhatsApp/solicitudes; sin prometer personas ni ventas.

Pruebas: tenant A/B, fotos/archivos hostiles, cuotas, precio/versiones, cotización obsoleta, idempotencia/reintentos, estado inválido, fallo correo, lifecycle completo. Base MySQL/MariaDB exclusiva. E2E y revisión 390/1440 px. Producción requiere configuración propia, backups y autorización posterior.
