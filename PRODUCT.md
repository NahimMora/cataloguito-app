# Producto y estrategia — propuesta para aprobación

Fecha: 26/10/2026. Las prioridades y precios siguientes son hipótesis propias. La evidencia comercial está en [COMPETITIVE_RESEARCH.md](COMPETITIVE_RESEARCH.md).

## 1. Segmento y promesa

Comercios argentinos que ya reciben consultas por WhatsApp/Instagram, tienen 30–300 productos simples y cambian precio o disponibilidad semanalmente. Empezar con accesorios, regalería y bazar; contrastar con belleza. La disposición a pagar y frecuencia real no están demostradas: se verifican en entrevistas con listas y pedidos reales, con permiso.

Evitar inicialmente moda con matriz de talles, alimentos por peso, mayoristas con precios por cliente y negocios que necesitan stock en tiempo real. No representar variantes complejas como simples notas del pedido.

Promesa a validar: “Publicá una selección de productos, compartí el mismo enlace y mantené los precios al día desde el celular”. Cinco minutos aplica a los primeros tres productos con fotos disponibles, no a cargar cientos de artículos ni a tomar las fotografías.

## 2. Tres ventajas a demostrar

1. **Precios en segundos:** lista móvil editable, teclado decimal, confirmación visible, búsqueda persistente y recuperación de error. Objetivo: precio cambiado en ≤15 segundos desde la lista; propuesta de objetivo, no benchmark obtenido.
2. **Buena presentación sin aprender diseño:** temas con contraste controlado, imágenes consistentes y vista previa. La ventaja sería la facilidad de conseguir el resultado, no simplemente ofrecer plantillas.
3. **Solicitudes que se pueden seguir:** registro duradero, identificación, contacto mínimo y estados comprensibles. El comprador puede reabrir WhatsApp sin duplicar el pedido. No equivale a venta ni a mensaje recibido.

Ninguna constituye por sí sola una barrera defensiva. La especialización, la incorporación y la retención deben sostener el negocio.

## 3. Matriz de alcance

| Función | Prioridad / fase | Valor, esfuerzo y monetización |
|---|---|---|
| Registro, recuperación, verificación de email, negocio | Imprescindible 1 | Base de seguridad y autonomía |
| URL estable, publicación/pausa, copiar enlace | Imprescindible 1 | Distribución permanente |
| Nombre, logo, WhatsApp y descripción | Imprescindible 1 | Identidad mínima |
| Productos, categorías, búsqueda y orden | Imprescindible 1 | Núcleo; una categoría por producto inicialmente |
| Precio editable en lista, disponibilidad, archivo | Imprescindible 1 | Principal hipótesis de ahorro |
| Foto principal; SKU opcional | Imprescindible 1 | Menor complejidad; sin SKU obligatorio |
| Precio anterior | Importante 2 | Validar superior al actual; no prioridad de lanzamiento |
| Dos temas y color controlado | Imprescindible 1 | Minimal y Comercial; componentes compartidos |
| Vista previa de apariencia | Imprescindible 1 | Evita cambios accidentales públicos |
| Carrito sin registro y solicitud persistida | Imprescindible 1 | Pedido útil antes de integrar pagos |
| Reapertura de WhatsApp, copiar resumen, estados | Imprescindible 1 | Recuperación ante fallos y control comercial |
| Email al vendedor | Importante, incluir 1 | No requiere conocer la API de WhatsApp; envío desacoplado |
| Enlaces estables a producto/categoría + Open Graph | Imprescindible estructura 1 | Evita migrar URLs; botón compartir avanzado puede esperar |
| Exportación básica propia CSV | Importante 1 | Portabilidad y soporte, no gancho de pago |
| Límites y permisos de plan | Imprescindible base 1 | Contiene abuso; cobro manual del piloto |
| Métricas de activación y uso | Imprescindible 1 | Validación; panel analítico comercial después |
| CSV con plantilla y revisión | Importante 2 | Prioridad alta si ≥3 pilotos lo necesitan |
| XLSX | Importante 2 posterior a CSV | Validación, memoria y archivos hostiles aumentan coste |
| Porcentaje masivo, redondeo, revisión/deshacer | Importante 2 | Beneficio Pro medible; no cambio instantáneo sin revisión |
| Tercera plantilla, portada, favicon, fuentes | Importante 2 | Personalización Pro |
| Hasta tres fotos por producto | Importante 2 | Útil para rubros visuales; cupo de almacenamiento |
| QR descargable | Importante 2 | Esfuerzo bajo; anticipar si piloto usa ferias/local |
| Compartir producto/categoría con acción nativa | Importante 2 | Esfuerzo bajo, útil en conversaciones |
| Favoritos locales | Posterior 4 | Hipótesis débil; sin cuentas de compradores |
| Ofertas, destacados, métricas y campañas | Posterior 4 | Priorizar según uso, sin inflar dashboard |
| Referidos con crédito | Posterior 4 | Solo después de retención, con control antifraude |
| Suscripción MP SaaS | Posterior 3 | Automatiza algo ya cobrado manualmente |
| Cobro de pedidos, dominios, API WhatsApp | Posterior 5 | Proyectos separados con demanda y costes comprobados |
| OCR/PDF/Drive/Sheets automático | Posterior, sin compromiso | Ya existe en competidores; coste y errores no justifican MVP |
| Estudio de promociones | Posterior 4/5 | Exportación determinista simple antes que IA |
| ERP, facturación de comerciantes, logística, chat, apps nativas, editor libre, IA obligatoria | Descartado | No contribuye al objetivo inicial |

El correo de autenticación sí es imprescindible aunque se decida retirar avisos de pedido por alcance. La facturación fiscal del propio SaaS debe resolverse con herramientas existentes y asesoramiento; no desarrollar un motor fiscal.

## 4. Recorrido del vendedor

1. Registra email/contraseña, crea negocio con nombre y URL disponible. Logo opcional, iniciales como respaldo.
2. Configura WhatsApp con país y prueba el destinatario; puede cargar borradores mientras verifica email. Publicar requiere email verificado.
3. Carga tres productos: nombre, precio, foto. Categoría y SKU no bloquean el alta.
4. Elige entre dos presentaciones; ve preview móvil con sus productos, confirma publicación y copia el enlace.
5. La lista Productos permite buscar y editar el precio en el mismo lugar. Guardar muestra pendiente, guardado o error; nunca anuncia éxito antes del servidor. Un conflicto entre dispositivos solicita revisar.
6. Recibe aviso, abre Pedidos, revisa datos, continúa por WhatsApp y confirma manualmente. “Completado” describe el cierre operativo; no certifica un cobro.
7. Puede pausar, exportar y solicitar baja sin contactar al desarrollador para operaciones cotidianas.

Navegación: Inicio, Productos, Pedidos, Mi catálogo, Cuenta/plan. En móvil: Productos, Pedidos, Catálogo y Más; Inicio accesible desde Más/logo.

## 5. Recorrido del comprador

1. Abre enlace sin registrarse, busca o entra a categoría.
2. Ve precio, disponibilidad y foto; agrega unidades enteras. Carrito separado por comercio y con vencimiento local de 7 días.
3. Revisa resumen y total estimado. Envío y pago se acuerdan con el comercio; no mostrar “envío gratis” si no está incluido.
4. Completa nombre y teléfono para que el comercio pueda responder aunque no envíe WhatsApp. No pedir DNI, dirección ni email en esta etapa.
5. El servidor revalida precios/disponibilidad. Si cambiaron, pide nueva confirmación; no cambia silenciosamente el total.
6. Confirma: se registra una única solicitud, se muestra su número y se ofrece abrir WhatsApp. La apertura puede intentarse en el flujo de confirmación, pero siempre queda un botón explícito por restricciones móviles y bloqueadores.
7. Revisa el texto y lo envía personalmente. Puede volver a abrirlo o copiarlo. Mensaje largo: resumen acotado, número y enlace al panel del vendedor que requiere autenticación.

La [documentación de WhatsApp](https://faq.whatsapp.com/5913398998672934/) describe texto precargado: abrir `wa.me` no envía automáticamente. Nunca usar la etiqueta “enviado” como resultado de un clic.

## 6. Planes propuestos

No abrir un gratuito masivo antes del piloto. Primero 5–10 negocios invitados con términos claros; después Gratis + Pro. El ensayo de abono se cobra externamente por transferencia conciliada en la cuenta del fundador; no implementar pagos online en fase 1.

| | Gratis | Pro propuesto |
|---|---|---|
| Abono | ARS 0 | ARS 7.900/mes, a validar |
| Productos activos | 50 | 500 |
| Imágenes | Una por producto | Hasta tres desde fase 2 |
| Almacenamiento procesado | 50 MB | 1 GB |
| Pedidos, seguridad, velocidad | Completos | Completos |
| Temas | Minimal/Comercial básicos | Boutique y ajustes desde fase 2 |
| Marca de plataforma | Pie discreto | Retirable |
| Productividad | Edición individual | CSV, cambios masivos y recuperación desde fase 2 |
| Soporte | Guías; incidentes de acceso/seguridad atendidos | Asíncrono en horario definido; sin promesa 24/7 |

No comercializar funciones que aún no existen. En fase 1 el piloto paga por el servicio disponible, capacidad y retiro de marca; si ese valor no alcanza, no simular Pro con promesas.

El precio toma como referencia el rango observado, pero no resuelve por sí mismo la presión competitiva. Ensayar una oferta clara por cohortes pequeñas, sin discriminación opaca ni descuentos perpetuos. Un plan superior se estudia solo con ≥5 clientes que exceden los límites y aceptan pagar más. No ofrecer “ilimitado”.

Cuotas se controlan en servidor con operaciones atómicas. Al bajar de plan, conservar datos y permitir exportar/editar/reducir; avisar y dar plazo para elegir los productos visibles. No borrar automáticamente fotos ni pedidos por impago. Ajustes futuros informados con anticipación; política exacta y obligaciones legales antes de lanzamiento. Sin planes vitalicios ni anualidad antes de validar retención.

## 7. Costos y economía

### Fuentes y condiciones

- [Hostinger Argentina](https://www.hostinger.com/ar/hosting-web): Unlimited anunciado ARS 4.999/mes pagando 48 meses (ARS 239.952), renovación anunciada 21.999/mes; 50 GB y respaldos diarios. No es una tarifa mensual sin compromiso; validar impuestos y recursos del contrato. Cloud Startup: 16.099 promocional/52.299 renovación con plazo anunciado. No comprar 48 meses solo por el descuento.
- [R2 Standard](https://developers.cloudflare.com/r2/pricing/): USD 0,015/GB-mes; operaciones A 4,50/millón, B 0,36/millón; egreso a Internet sin cargo. Capa gratis sujeta a cuotas. Usar dominio de assets apropiado, no endpoint de desarrollo para producción.
- [SES](https://aws.amazon.com/ses/pricing/): envío base USD 0,10/1.000 emails, datos y extras aparte. Requiere dominio verificado, salida del sandbox y gestión de rebotes; no basta con tener SMTP.
- [Mercado Pago Suscripciones](https://www.mercadopago.com.ar/herramientas-para-vender/suscripciones): publicación de 6,99%+IVA al instante; 4,49% a 10 días, 3,39% a 18 y 1,49% a 35. Costo final varía con condiciones/impuestos provinciales. Confirmar producto y cuenta antes de integrar.

### Uso modelado, no medición

Comercio Pro medio: 250 productos × 2 fotos × 0,6 MB por conjunto de imagen maestra optimizada y derivados = **300 MB**. Gratuito: 50×1×0,6 = **30 MB**. No almacenar originales de cámara ilimitados.

Con 100 pagos y 400 gratuitos activos: 42 GB de imágenes; 500×500 páginas/mes×1 MB ≈250 GB de transferencia; unas 3 millones de lecturas si son 12 imágenes por página. Con 1.000 pagos y 4.000 gratuitos: 420 GB, 2,5 TB y 30 millones de lecturas. Es un supuesto de uso homogéneo, no capacidad garantizada del servidor.

Antes de descuentos/cuotas gratis: R2 almacenamiento de esos casos ≈USD 0,63 y 6,30/mes; lecturas ≈1,08 y 10,80, más escrituras. El mayor costo de servir tráfico puede ser CPU/SQL, abuso o soporte, aunque el egreso de imágenes no tenga cargo. Backups, archivos temporales y versiones borradas se presupuestan aparte.

50 pedidos/emails por pago y 5 por gratuito al mes: 7.000/70.000 envíos, base SES ≈USD 0,70/7. No usar el límite del correo para impedir registrar pedidos legítimos.

Base de datos: 10.000 pedidos mensuales × estimación de 5 KB incluyendo líneas ≈50 MB/mes; 12 meses ≈600 MB antes de índices, logs y backups. Debe medirse con esquema/datos reales; no incluye catálogo. La BD está incluida en el presupuesto de hosting inicial, no en R2.

Presupuesto operativo inicial: USD 20–40 mensuales equivalentes para hosting a costo sostenible, dominio amortizado, respaldo externo y monitorización básica; correo/objetos aparte. A mayor carga reservar USD 60–120 para un escalón de cómputo. Son asignaciones de presupuesto, no ofertas de proveedores. Herramientas Codex/Claude, contador, impuestos y adquisición son gastos adicionales del fundador.

### Modelo reproducible

Usar tipo de cambio **hipotético** E, no cotización vigente. P=7.900/E USD por cliente. Cobranza modelada con 6,99%×1,21=8,4579%; no incluye retenciones ni todos los impuestos. Coste fijo F=USD 30/mes. Reserva variable =USD 0,15 por pago + USD 0,03 por gratuito; relación gratuita:paga=4:1 → USD 0,27 por pago equivalente. Esa reserva contiene holgura; no se suma otra vez al desglose estimado de fotos/correo.

Contribución antes de soporte, captación e impuestos: C=P×(1−0,084579)−0,27. Resultado operativo parcial=N×C−30. No es ganancia neta.

| E hipotético ARS/USD | P USD | Clientes para facturar USD 400 / 1.000 | Para resultado parcial USD 400 / 1.000 |
|---|---:|---:|---:|
| 1.200 | 6,58 | 61 / 152 | 75 / 179 |
| 1.500 | 5,27 | 76 / 190 | 95 / 227 |
| 1.800 | 4,39 | 92 / 228 | 115 / 275 |

| Pagos, E=1.500 | Facturación USD | Infraestructura/reserva | Comisión modelada | Resultado parcial | Margen antes de soporte |
|---:|---:|---:|---:|---:|---:|
| 10 | 52,67 | 32,70 | 4,45 | 15,51 | 29,5% |
| 50 | 263,33 | 43,50 | 22,27 | 197,56 | 75,0% |
| 100 | 526,67 | 57,00 | 44,54 | 425,12 | 80,7% |
| 250 | 1.316,67 | 97,50 | 111,36 | 1.107,80 | 84,1% |

Los resultados con F=30 son escenarios, no garantía de que ese servidor soporte el tráfico. Si F sube a 80, el resultado cae USD 50.

**Soporte:** 10 minutos/mes por pago valorados a USD 10/h cuestan USD 1,67 por cliente. A E=1.500, se necesitan aproximadamente 150/358 pagos para USD 400/1.000 después de ese soporte pero antes de impuestos/adquisición y otras horas. A 30 minutos/cliente son USD 5: supera la contribución de USD 4,55 y el modelo pierde dinero al crecer. Soporte del gratuito y onboarding deben medirse por separado.

**Cancelaciones:** 5% mensual exige reemplazar unos 5 clientes de cada 100 o 10 de cada 200 solo para sostener la base. Como aproximación estable, vida media 1/churn y LTV de contribución con soporte=2,88/churn: ≈USD 96 al 3%, 58 al 5%, 29 al 10%, antes de costes fijos, impagos y crecimiento. No usar este LTV como evidencia de cohortes. Un CAC de USD 20 tardaría alrededor de siete meses en recuperarse; es demasiado para apostar fuerte antes de conocer la retención.

## 8. Adquisición: 10, 50 y 100

| Hito | Acciones | Qué medir |
|---|---|---|
| Primeros 10 activos | 30–40 prospectos relevantes; 10 entrevistas; 5–10 pilotos; incorporación asistida acotada; demo con permiso y productos propios | Publicación autónoma, uso semanal y al menos tres pagos reales |
| 50 activos | Concentrarse en el rubro con mejor retención; dos casos con tiempos medidos; demostración semanal; alianzas con fotógrafos/diseñadores; importación guiada | Activos/pagos separados, canal de origen, coste de incorporación, renovación |
| 100 activos | Repetir el canal rentable; referido con crédito tras primer pago verificado; biblioteca de tutoriales; ensayos publicitarios pequeños solo con CAC máximo definido | Churn por cohorte, margen tras soporte, recuperación de CAC |

Son hitos, no predicciones de conversión. No se contactó a ningún comercio durante esta investigación. Evitar mensajes masivos no solicitados. La captación inicial es responsabilidad comercial del fundador; los agentes pueden preparar materiales.

Branding: pie pequeño en Gratis, sin popup ni superposición sobre el carrito; se retira en Pro. Medir clic → registro → publicación → pago. Muchos visitantes son compradores, no comerciantes: no asumir un bucle viral rentable.

### SEO

| Intención | Términos a validar | Contenido |
|---|---|---|
| Crear | crear catálogo online; catálogo online para emprendedores | Landing principal y demo |
| WhatsApp | catálogo para WhatsApp; catálogo con carrito WhatsApp | Flujo explicado, límites de wa.me |
| Gratuito | crear catálogo digital gratis | Plan Gratis real, límites visibles |
| Negocio/rubro | catálogo digital para negocios; catálogo para bazar/accesorios | Casos específicos con productos reales |
| Mayorista | catálogo mayorista | Investigar; no prometer reglas mayoristas todavía |

No se obtuvo un export de Keyword Planner ni una base fiable de volúmenes para Argentina. No se asignan búsquedas mensuales, CPC, dificultad ni tráfico inventados. [Google explica](https://support.google.com/google-ads/answer/3022575?hl=es) que las estimaciones dependen de ubicación, período y red. Obtener 12 meses, Argentina, español y variantes; validar después con Search Console. Google Trends sería interés relativo, no volumen absoluto.

SSR, títulos/canonical propios, sitemap solo de contenido público válido, enlaces compartibles, Open Graph, páginas útiles sin duplicación por cada palabra clave. Panel y pedidos privados fuera de indexación y autenticados. Tiendas vacías o abusivas no indexables. [Google Search Central](https://developers.google.com/search/docs/fundamentals/seo-starter-guide) no garantiza posiciones por cumplir estos requisitos. No vender el branding como mecanismo asegurado de backlinks/SEO.

## 9. Validación y decisión

Piloto de 8 comercios como referencia, durante cuatro semanas de uso, con seguimiento de renovación posterior. Registrar si el fundador ayudó: no mezclar publicación asistida y autónoma.

| Métrica | Definición |
|---|---|
| Activación | Publicó ≥3 productos y copió/compartió enlace |
| Tiempo inicial | Desde empezar alta hasta publicación; separar espera de email y falta de fotos |
| Abandono | Inicio de registro sin publicación; por paso |
| Carga | Productos creados/publicados y errores |
| Mantenimiento | Comercios con actualización real de precio/disponibilidad por semana |
| Distribución | Copias/clics de compartir; no prueba que se haya publicado el enlace externamente |
| Tráfico | Vistas y sesiones aproximadas, filtrar robots; no llamarlas personas únicas |
| Contacto | Clics para abrir WhatsApp, sin asumir envío |
| Solicitud | Pedido persistido único y no identificado como spam |
| Venta | Confirmación manual del vendedor, separada de solicitud y de pago verificado |
| Retención | Comercios activos en semanas 2 y 4; renovación a 30/60 días |
| Pro | Uso/interés por función y pagos reales, no solo opinión |

Criterio orientativo para continuar: 6/8 publican sin intervención técnica, mediana ≤10 minutos para 3 productos; 4/8 mantienen actividad útil en semana 4; ≥3 pagan el precio comunicado y ≥2 renuevan; edición mediana de precio ≤15 s; soporte recurrente tendiendo a ≤10 min/pago/mes. La muestra es exploratoria, no validación estadística.

Si no comparten el catálogo, investigar adquisición y hábito antes de agregar funciones. Si actualizan pero no pagan, cambiar paquete/precio o segmento. Si solo pagan cuando se administra por ellos, reconocer que se está validando un servicio, no un SaaS autoservicio.
