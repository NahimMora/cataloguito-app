# Cataloguito: rediseño de marca, landing y experiencia móvil

Fecha: 27/10/2026. Estado: **plan listo para ejecutar; aplicación todavía sin modificar**. Solicitud: marca cercana, color dominante fuerte, landing compacta e interactiva, siete plantillas visibles, mejores formularios, teléfonos y avisos. No incluye pagos ni publicación en producción.

## 1. Diagnóstico comprobado

- Landing actual: mensaje bastante abstracto, una sola muestra ficticia de catálogo y sin galería de plantillas. Predominan blanco/crema y espaciados amplios.
- Registro a 375 × 667: documento de 838 px; botón de crear cuenta termina a 745 px, debajo de la pantalla. A 390 × 844 sí cabe; a 320 × 568 requiere más desplazamiento. La solución debe contemplar alturas reales, teclado, zoom y errores.
- La validación del servidor existe, pero presenta todos los errores en un bloque superior. Faltan mensajes junto al campo, asociación accesible y foco al error.
- Onboarding, configuración del comercio y checkout piden número internacional en una sola entrada. La normalización actual comprueba longitud/formato, sin interpretar países.
- Los avisos breves usan texto y un temporizador único de 3,5 s por evento sin cancelar el anterior. Avisos consecutivos pueden ocultarse demasiado pronto. No se verificó un fallo SMTP real; el outbox ya tiene reintentos y pruebas de fallo.
- Lighthouse móvil de la **landing**, servidor local de desarrollo: rendimiento 98, accesibilidad 94, SEO técnico 100; FCP 1,4 s, LCP 2,4 s, TBT 0 ms y CLS 0. Dos textos del bloque final fallan contraste. Informe: `.tools/lighthouse-landing-baseline.json`. No son métricas de producción ni evidencia de posicionamiento.
- Sucuchito: revisada su landing pública en navegador móvil y su robots.txt. Son aprovechables la demostración del producto, la galería por rubro y la claridad de las llamadas a la acción. Nuestra propuesta usa otra identidad, textos, composiciones y recursos. No se revisaron paneles privados.

## 2. Marca Cataloguito

Promesa: **Tu negocio, lindo y siempre al día.** Tono argentino, directo y cálido: «Armá tu catálogo», «Elegí tu estilo», «Listo para compartir». El diminutivo debe transmitir cercanía; la claridad del producto mantiene la confianza profesional de distribuidores y mayoristas.

Dirección visual propuesta:

| Uso | Propuesta |
|---|---|
| Color dominante | Coral profundo `#B93828`, sobre todo en cabecera y hero |
| Texto oscuro | Cacao `#321C18` |
| Fondo de apoyo | Crema cálido `#FFF7EC` |
| Acento | Amarillo manteca `#FFD76A`, con texto oscuro |
| Tipografía | Manrope local existente, jerarquías más claras y tamaños legibles |
| Componentes | Esquinas de 14–20 px, botones sólidos, sombras leves y fotografía real |
| Logotipo | Wordmark original Cataloguito y símbolo simple de catálogo; favicon propio |

Medir contraste de cada combinación y estado. Usar fondos claros en listas/formularios para lectura prolongada; la landing gana color sin convertir todo el panel en una superficie saturada. Cada comercio conserva su propia paleta.

Inventario del cambio de nombre: logo, títulos, navegación, footer de catálogos, emails, remitente configurado, textos legales, ayuda, demo, documentación y lanzador. Centralizar nombre público en configuración. Incorporar `ABRIR_CATALOGUITO.bat`; mantener el lanzador anterior como acceso compatible durante la transición.

No renombrar bases, credenciales, slugs, sesiones o claves de carrito por sustitución global. Los identificadores internos antiguos pueden mantenerse. Si se cambia una clave de almacenamiento, leer/migrar la anterior para conservar carritos. No regenerar APP_KEY ni borrar comercios. La disponibilidad del dominio y de la marca Cataloguito no está verificada.

## 3. Landing compacta con producto visible

Objetivo: entender qué hace, ver cómo queda y empezar sin recorrer bloques repetidos. Diseñar primero a 375 px; después ampliar a escritorio.

Orden de contenido:

1. **Cabecera de 56–64 px:** marca, Ingresar y una llamada a crear catálogo. Sin menú extenso.
2. **Hero coral:** H1 «Tu catálogo online, listo para compartir por WhatsApp». Apoyo: «Mostrá lo que vendés, actualizá precios desde el celu y recibí pedidos ordenados». Botón principal «Crear mi catálogo gratis», secundario «Ver plantillas». Mostrar límites gratuitos reales en una línea.
3. **Demostración integrada:** vista de catálogo compacta y cambio de precio que se refleja visualmente. En móvil aparece dentro del primer recorrido de pantalla, con una primera muestra visible, sin un gran teléfono decorativo que desplace todo.
4. **Galería protagonista:** «Así puede verse tu negocio», selector de siete rubros, preview activo, «Ver demo» y «Usar este estilo». En móvil, pestañas horizontales con señales de que hay más opciones y controles de teclado; nunca depender solo de deslizar.
5. **Cómo funciona:** tres pasos cortos: cargá productos, elegí diseño, compartí tu enlace. Mostrar la edición de precio como beneficio concreto.
6. **Qué incluye y dudas:** bloque gratuito veraz, cuatro preguntas en acordeón y cierre «Dale un lugar lindo a lo que vendés». No publicar un checkout Pro ni una prueba paga inexistente.

Objetivo inicial de altura a 390 px: aproximadamente 2.000–2.400 px con preguntas cerradas y una sola preview activa. Es un presupuesto de diseño, no una razón para achicar letras. El contenido detallado vive en páginas de plantillas/ayuda. El hero debe mostrar la propuesta y el botón antes del primer desplazamiento a 375 × 667.

### Dinámicas que sí aportan

- Selector de rubro cambia foto, colores y composición de la preview, manteniendo el espacio reservado.
- Una secuencia breve muestra «editás el precio → aparece actualizado». Es una demostración marcada como ejemplo, sin simular un pedido real.
- Transiciones de 150–250 ms y animaciones con transform/opacity; aparición discreta al entrar en pantalla.
- Respetar `prefers-reduced-motion`, pausar fuera de pantalla y evitar carruseles automáticos permanentes. La versión estática debe seguir comunicando el producto.
- Cargar solo recursos del estilo visible y diferir el resto. Sin siete iframes, videos automáticos, librería de animación pesada ni desplazamiento controlado por JavaScript.

No usar testimonios, cifras de comercios, ventas o notificaciones ficticias como prueba social. No copiar el toldo, logotipo, verde/mostaza ni las animaciones distintivas de Sucuchito.

## 4. Siete estilos por rubro, tres familias técnicas

| Rubro | Estilo de trabajo | Familia | Diferencia observable |
|---|---|---|---|
| Tienda de ropa | Hilo | Boutique | Foto protagonista, encabezado editorial y categorías claras |
| Mayoristas y distribuidores | Volumen | Comercial | Más densidad, SKU visible si existe y cantidades fáciles de ajustar |
| Cosmética | Aura | Boutique | Fotografía limpia, tonos suaves y espacio para descripción |
| Bazar y accesorios | Casa | Minimal | Grilla equilibrada, precio claro y foco en fotografía |
| Juguetería | Chispa | Minimal | Acentos alegres, formas amigables y categorías destacadas |
| Petshop | Huellas | Comercial | Categorías por tipo de producto y recompra visualmente sencilla |
| Pastelería | Dulce | Boutique | Fotos grandes y mensaje visible de disponibilidad a confirmar |

Los nombres de estilos son propuestas internas, no marcas verificadas. Minimal y Comercial ya existen; Boutique requiere implementación. Las diferencias deben incluir jerarquía/distribución, no solamente cambiar el color de la misma tarjeta.

Usar un registro permitido de presets con familia, tokens, tipografía, densidad, relación de fotos y datos ficticios. Componentes compartidos para precio, búsqueda, producto y carrito. Los presets no agregan código arbitrario ni duplican siete catálogos.

Cada estilo debe tener demo pública navegable, con aviso «Catálogo de ejemplo», sin solicitudes a comercios reales y sin registros de pedidos de producción. La landing abre una preview liviana; la demo completa se abre por enlace. La galería comercial puede indexarse; las tiendas ficticias de demostración llevan noindex.

«Usar este estilo» conserva la selección durante registro/onboarding y permite verla antes de publicar. Reservar rutas antes del comodín de comercios y comprobar colisiones con slugs existentes.

Límites explícitos: Volumen no promete escalas de precios, compra mínima ni listas privadas; Hilo no promete stock por talle; Dulce no promete agenda, cupos o señas. Son estilos compatibles con los pedidos simples actuales. Fotos propias o con licencia, nunca extraídas del competidor.

## 5. Registro y formularios compactos

- Ruta canónica `/register`. Si se quiere cubrir el error escrito por el usuario, `/regisster` puede redirigir por GET a esa ruta, sin duplicar el formulario.
- Encabezado corto «Creá tu Cataloguito». Marca compacta, descripción de una línea y formulario sin bloque publicitario en móvil.
- Mantener nombre/email/contraseña/confirmación en el primer ajuste. Etiquetas visibles, mostrar/ocultar contraseña, autocompletado y requisitos antes de enviar. No bloquear pegado ni gestores de contraseñas.
- Inputs con texto de 16 px y controles de unos 44–48 px; márgenes de 8–12 px entre grupos. Una sola columna en celular.
- Usar `min-height:100dvh` y flujo natural. Nunca ocultar overflow para forzar una pantalla. Con teclado, zoom, errores o 320 × 568 se permite desplazamiento necesario.
- Objetivo: botón principal y enlace de ingreso visibles sin scroll a 375 × 667, sin errores y con teclado cerrado. También probar 390 × 844, 360 × 640 y escritorio de 768 px de alto.

### Errores y estados

Mensaje al lado del campo con `aria-invalid` y `aria-describedby`; conservar nombre/email y consentimiento cuando corresponda, nunca contraseñas. Tras envío fallido, resumen corto con enlaces a campos y foco en el primer error. No validar agresivamente mientras la persona todavía escribe.

Casos: email inválido/ya registrado, contraseña sin letras o números, confirmación distinta y términos pendientes. El mensaje «Este email ya tiene cuenta. Podés ingresar o recuperar el acceso» evita un callejón sin salida; conservar límites y política de seguridad existente. Recuperación mantiene respuesta genérica para cuentas conocidas/desconocidas.

El servidor conserva la autoridad. La validación del navegador mejora la experiencia, pero no sustituye comprobaciones del backend. Botón en estado «Creando cuenta…» sin doble envío. Sesión vencida, demasiados intentos y caída de red deben dar instrucciones claras, sin asegurar que se guardó algo que falló.

## 6. WhatsApp con país separado en todos los formularios

Componente reutilizable para alta del comercio, configuración y contacto del comprador. Cualquier teléfono nuevo debe usar el mismo contrato.

Presentación inicial: selector **Argentina +54** y campo **Código de área y número**, ejemplo `11 2345 6789`. El país se puede cambiar; no se fija por IP. La selección del usuario prevalece y se conserva al volver con errores.

Para móviles argentinos, normalizar al formato que usa WhatsApp, incluyendo el 9 cuando corresponde. No tratar +549 como código de país: el país es +54. Mostrar una vista previa del número resultante. No quitar cualquier secuencia «15» dentro del número: reconocer prefijos en su posición o pedir corrección si el formato es ambiguo.

Aceptar pegado de un internacional completo sin duplicar prefijos; detectar país y permitir confirmarlo. Probar formatos nacionales e internacionales, número ya normalizado, espacios, Argentina y otros países. Validar con metadatos telefónicos en backend, evaluando una librería mantenida; no descargar metadatos globales pesados al catálogo público.

Mantener el almacenamiento internacional canónico y compatibilidad con teléfonos existentes. `wa.me` recibe solo dígitos y texto codificado. «Probar mi WhatsApp» permite al vendedor comprobar el destino; validar formato no acredita que el número tenga WhatsApp.

## 7. Avisos y notificaciones coherentes

Separar avisos de interfaz, correo transaccional y estados del pedido.

- Éxitos breves: «Precio actualizado», «Enlace copiado». Un gestor cancela temporizadores anteriores, permite cerrar y mantiene visible el aviso mientras tiene foco. No tapar botones, teclado ni navegación inferior. Anunciar con `role=status` sin interrumpir.
- Errores: permanecen junto a la acción; no dependen de un toast que desaparece. Permitir reintentar. Identificar éxito/error también con texto e icono.
- Registro/verificación/reset: asunto y remitente Cataloguito, respuesta coherente ante fallo de envío y reenvío con límites. No afirmar «correo enviado» solo porque fue programado.
- Pedidos: «Solicitud guardada. Ahora podés enviarla por WhatsApp». El aviso del vendedor dice «Nueva solicitud», nunca «Venta cobrada».
- Revisar cron, leases y reintentos existentes. Añadir visibilidad del último aviso de pedido en el panel: pendiente, entregado al transporte, fallido o desactivado. Que SMTP acepte un correo no demuestra lectura ni llegada a bandeja de entrada.
- El vendedor puede desactivar avisos. Los detalles de fallos técnicos quedan para operación, sin credenciales, trazas ni datos personales en pantalla. No sumar push ni WhatsApp API a este bloque.

## 8. SEO: adquisición, no solamente una puntuación

### Base técnica

Título propuesto de inicio: «Catálogo digital para WhatsApp | Cataloguito». H1 descriptivo, descripción propia, Open Graph con marca nueva, favicon, canonical absoluto y HTML renderizado en servidor. El contenido principal y los enlaces a estilos deben existir sin ejecutar la animación.

Revisar mapa de URLs: inicio, `/plantillas`, páginas de estilos y páginas comerciales específicas. Declarar sitemap absoluto del dominio elegido, con páginas canónicas e indexables. El sitemap actual solo incluye inicio/comercios; ampliar cobertura a productos públicos si aportan contenido, con paginación eficiente.

Definir explícitamente qué comercios quieren aparecer en buscadores. No indexar demos, borradores, recibos, búsqueda interna, login/registro ni panel. No confundir robots.txt con privacidad: los pedidos siguen protegidos por autorización/sesión. Para que Google vea un noindex, no bloquear a la vez esa URL por robots.txt. Revisar combinaciones actuales, URLs con parámetros, paginación y canonicals para evitar duplicados o excluir páginas útiles.

Datos estructurados solo cuando describen contenido real: Organization y SoftwareApplication para el servicio; Product únicamente donde precio/disponibilidad/información cumplan las condiciones de la página y del proveedor. Sin reseñas inventadas ni promesas de resultados enriquecidos para preguntas frecuentes.

### Contenido por intención

Prioridad inicial: inicio y plantillas; después una página útil sobre **catálogo para WhatsApp** y otra sobre **crear catálogo digital gratis**. Si repiten la misma intención, consolidarlas. No publicar siete páginas que solo cambien el nombre del rubro.

Las páginas por rubro deben aportar demo propia, ejemplos de categorías/fotos, configuración recomendada y límites honestos. Publicarlas gradualmente al tener contenido suficiente. Priorizar accesorios/bazar y cosmética para captar pilotos; mayoristas requieren validar si el catálogo simple resuelve su necesidad.

Guías iniciales: cómo compartir el catálogo, cómo actualizar precios y cómo recibir solicitudes por WhatsApp. Vincularlas desde preguntas/ayuda, sin agregar bloques extensos al inicio. No asignar volúmenes inventados a palabras clave.

### Medición tras publicar

Configurar Search Console sobre dominio verificado, sitemap e inspección de URLs. Medir impresiones, clics y consultas; analizar indexación y conversión por página. Google usa el contenido móvil para indexación, por lo que móvil/escritorio deben conservar la misma información importante.

Objetivos de experiencia de campo: LCP ≤2,5 s, INP ≤200 ms y CLS ≤0,1 en percentil 75. En local, repetir Lighthouse en condiciones iguales y revisar la mediana de tres ejecuciones; no confundir TBT con INP real. Objetivo de laboratorio ≥90 rendimiento, con contraste y accesibilidad corregidos; no sacrificar la buena base actual por animaciones.

Eventos de conversión: clic en crear catálogo, rubro elegido, demo abierta, registro completado, primer producto y primera publicación. Errores por campo mediante códigos, sin email/teléfono ni contenido ingresado. Medir activación, no solo visitas. Una puntuación SEO 100 no demuestra posicionamiento.

## 9. Orden ejecutable y aceptación

| Bloque | Entrega | Criterio de aceptación | Esfuerzo estimado |
|---|---|---|---|
| 1 | Marca y tokens | Cataloguito en toda la UI/correos; datos y enlaces existentes conservados | 0,5–1 día |
| 2 | Registro, errores, teléfonos y avisos | Registro usable a 375 × 667; errores por campo; país separado; pruebas de normalización y fallo de correo | 2–3 días |
| 3 | Tres familias y siete presets | Siete demos distintas; selección persiste; temas existentes siguen funcionando | 3–5 días |
| 4 | Landing y dinámicas | Hero coral, galería navegable, CTA claro, reduced-motion y sin desbordamiento | 2–3 días |
| 5 | SEO y verificación | Metadatos/sitemap/indexación coherentes; pruebas funcionales, teclado, contraste y mediciones | 1–2 días |

Total orientativo: **8,5–14 días de trabajo**, dependiente de fotografías, ajustes visuales y revisión del fundador. Estimación, no plazo garantizado. Se puede trabajar por entregas verificables; no se necesita rehacer el backend ni usar otro framework.

Para trabajo posterior entre herramientas: Codex, contratos de país/normalización, correo, pruebas y reglas de indexación; Claude Code, tokens, landing, galería y variantes visuales. Un responsable por componente compartido y revisión cruzada de cambios de formularios; no editar los mismos archivos simultáneamente.

Pruebas nuevas enfocadas en riesgos: errores visibles y conservación de campos; país/prefijo sin duplicación; teléfonos guardados siguen funcionando; selección de estilo atraviesa registro; demos no generan solicitudes reales; avisos sucesivos no desaparecen por temporizadores viejos; correos fallidos no pierden pedidos; rutas comerciales no pisan slugs. Reejecutar suite existente y revisión visual móvil/escritorio, teclado/zoom/movimiento reducido. No agregar tests que solo comprueben el texto nuevo de marca.

Resultado esperado: el comerciante reconoce su rubro, ve un catálogo parecido a lo que necesita, entiende el producto y crea su cuenta sin fricción. Las siete plantillas sirven para demostrar valor y activar usuarios, sin ampliar silenciosamente el alcance hacia un ERP o e-commerce completo.

## Fuentes consultadas

Todas consultadas el 27/10/2026; la inspección local y las propuestas se distinguen arriba de lo declarado por terceros.

- [Sucuchito, landing pública](https://sucuchito.app/) y [robots.txt](https://sucuchito.app/robots.txt): demostración, galería y presentación comercial; ninguna copia de assets o pantallas.
- [Google, guía inicial de SEO](https://developers.google.com/search/docs/fundamentals/seo-starter-guide): contenido útil, rastreo, títulos y medición; no garantiza indexación ni posiciones.
- [Google, indexación móvil](https://developers.google.com/search/docs/crawling-indexing/mobile/mobile-sites-mobile-first-indexing): contenido móvil equivalente y accesible al rastreador.
- [Google, noindex](https://developers.google.com/search/docs/crawling-indexing/block-indexing): relación con robots.txt.
- [Web.dev, Web Vitals](https://web.dev/articles/vitals): umbrales y percentil 75.
- [WhatsApp, números internacionales](https://faq.whatsapp.com/640432094208718/?locale=ca_ES) y [Personal, llamadas desde el exterior](https://www.personal.com.ar/ayuda/telefonia/roaming/como-llamar-desde-el-exterior): particularidades del móvil argentino y sus prefijos.
