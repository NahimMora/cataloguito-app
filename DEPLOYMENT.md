# Despliegue y operación

El MVP funciona localmente. Producción requiere autorización del fundador y configuración de su infraestructura. No se conectaron cuentas de pago ni SMTP externo.

## Requisitos

PHP 8.4+ (versión mínima del conjunto fijado en composer.lock), Composer 2 y MariaDB 11.8 (motor probado); extensiones PDO MySQL, GD con WebP, mbstring, fileinfo, openssl, intl, XML, EXIF y ZIP. MySQL 8.4 es alternativa sujeta a ejecutar la misma suite antes de producción. Node 22.12+ solo para compilar. Raíz web exclusivamente `public/`. Configurar `upload_max_filesize=5M`, `post_max_size=8M` y memoria suficiente para reencodificar hasta 12 MP.

El hosting debe permitir cron y escritura en `storage/` y `bootstrap/cache/`. No necesita Redis ni workers permanentes. Las fotos se sirven desde almacenamiento privado mediante control de publicación/propietario; un catálogo pausado no expone nuevas lecturas de fotos a terceros.

## Instalar una versión

1. Crear BD y usuario exclusivo; configurar `.env` desde `.env.example`, `APP_ENV=production`, `APP_DEBUG=false`, `APP_URL=https://dominio`, cookies seguras, identidad del operador y correo SMTP.
2. Instalar `composer install --no-dev --prefer-dist --optimize-autoloader`. Compilar `npm ci` y `npm run build` en CI/local; subir `public/build` con la versión.
3. Generar `APP_KEY` una sola vez para la instalación inicial; conservarla cifrada fuera de la aplicación. No regenerarla en cada despliegue.
4. Ejecutar migraciones después de backup y revisión: `php artisan migrate --force`. No ejecutar `migrate:fresh` en producción.
5. Ejecutar `php artisan config:cache`, `php artisan route:cache`, `php artisan view:cache`. Rutas y templates comprobados por tests antes de publicar.
6. Instalar cron cada minuto: `php /ruta/artisan schedule:run`. El outbox entrega lotes cortos, con lease, reintentos y timeout de SMTP. Confirmar intervalo permitido por el hosting y observar retrasos.
7. Verificar HTTPS, alta real, verificación, recuperación, carga de imagen, pedido único, panel y SMTP. Datos de prueba propios; no enviar solicitudes a negocios reales.

No ejecutar DemoSeeder en producción. Antes de abrir registros completar OPERATOR_NAME, OPERATOR_ADDRESS, SUPPORT_EMAIL, proveedor/región de datos, política efectiva de retención y textos legales revisados. La página legal incluida es una base funcional; no reemplaza esa configuración/revisión.

## Correo

SMTP transaccional con remitente verificado, SPF/DKIM/DMARC; no Gmail API. Producción no admite `array` ni `log` como comprobación de entrega. La verificación/reset envían correo desde el flujo de autenticación; las notificaciones de pedido quedan en outbox. Revisar `failed` y la antigüedad de `pending`; corregir credenciales y reprogramar fallidos después de resolver la causa. SMTP puede duplicar una entrega si se pierde la confirmación posterior al envío: nunca duplica el pedido.

Configurar rebotes/quejas en el proveedor y suspender un destinatario cuando corresponda; la integración automática de webhooks de rebote específica del proveedor no está incluida. La versión limita avisos por negocio a 30/h y 100/día; el pedido no se descarta al superar el límite. Los avisos excedentes se posponen y no se convierten en un digest.

## Backups y restauración

Antes del backup consistente: poner mantenimiento (`php artisan down`), esperar que terminen peticiones de escritura y detener trabajos que alteren archivos. Ejecutar `php artisan catalog:backup`; se crea ZIP privado con SQL, imágenes y manifiesto. `DB_DUMP_BINARY` puede indicar mariadb-dump/mysqldump. Enviar el archivo a almacenamiento externo cifrado y conservar la APP_KEY aparte. Luego `php artisan up`.

El ZIP contiene datos personales y no está cifrado por sí mismo. Nunca guardarlo en `public/`, Git ni compartirlo como archivo público. La creación del ZIP no equivale a tener un backup externo.

Restaurar primero sobre una base vacía y un almacenamiento aislado: importar `database.sql` con cliente compatible, copiar `images/shops` al disco privado configurado y verificar filas/fotos/pedidos, sin enviar emails antiguos. Descartar o revisar sesiones y outbox antes de volver a habilitar cron. Restaurar claves/configuración desde su respaldo separado. Ensayar mensualmente y medir RPO/RTO reales.

`scripts/verify-backup.ps1 -Archive ...` permite comprobar un backup local sobre la BD fija `vitrina_restore_test`, nunca sobre la BD de uso. La prueba deja evidencia en `.tools/restore-check*`; no modifica datos originales.

## Actualizaciones y reversión

Staging primero; PR acotado; backup; migraciones compatibles hacia adelante; cambio de versión/artefactos. Si falla, volver al código/artefactos anteriores cuando el esquema siga siendo compatible. No hacer rollback destructivo de tablas con pedidos reales para solucionar una falla de interfaz.

Observar 5xx, tiempo de respuesta, disco, conexiones SQL, outbox y vencimiento SSL. No publicar mensajes de excepción ni PII en herramientas de monitoreo. La app incluye headers básicos; Alpine estándar requiere `unsafe-eval` en CSP. Mantener escape de HTML y tokens de apariencia permitidos; una CSP sin eval requeriría adaptar el bundle a Alpine CSP y probar sus expresiones.
