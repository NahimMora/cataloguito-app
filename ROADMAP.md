# Cinco fases y decisiones de lanzamiento

Estado: propuesta, implementación no autorizada. Estimaciones de esfuerzo del fundador asistido por agentes, no promesas ni suma de horas de dos agentes independientes.

## Antes de la fase 1

Una o dos semanas para entrevistas y selección de 5–10 pilotos, revisar los límites del hosting y aprobar los documentos. No construir una landing o prototipo funcional en esta etapa sin autorización de implementación. Presentar bocetos/documentos y demos propias después de aprobar diseño.

Hipótesis de calendario: **20 horas semanales efectivas** para producto/desarrollo/revisión; ventas y soporte pueden reducirlas. Incluye correcciones y pruebas, excluye esperas externas o cambios mayores. La IA ayuda a producir código, no elimina validación humana ni operación.

| Fase | Esfuerzo / calendario | Entrega y dependencias | Responsabilidad | Aceptación |
|---|---|---|---|---|
| 1. MVP comercial | 180–260 h; 9–13 semanas | Registro/negocio, publicación, productos/categorías/foto, dos temas básicos, precio rápido, carrito, solicitudes/estados, outbox/email, compartir, cuotas simples y exportación | Codex backend/seguridad; Claude UI/temas; fundador producto y piloto | Tienda real opera sola; aislamiento y duplicados probados; precio actualizado; pedido persiste con SMTP caído; restauración y móvil revisados |
| 2. Productividad | 60–100 h; 3–5 semanas | Primero CSV/revisión y porcentaje con recuperación; después XLSX, Boutique, portada/fuentes/fotos/QR según uso | Codex importación/datos; Claude preview/edición; revisión cruzada | Importación sin SKU por ID estable; errores por fila; no sobrescribir ediciones posteriores al deshacer; tres pilotos la usan |
| 3. Cobranza automática | 60–100 h; 3–5 semanas | MP abono SaaS, webhooks/conciliación, planes, vencimiento, cancelación, página de precios definitiva | Codex billing; Claude cuenta/planes; fundador condiciones comerciales | Pago verificado activa una vez; rechazo no duplica débito; cancelación no borra datos; conciliación detecta webhook perdido |
| 4. Conversión/retención | 40–80 h; 2–4 semanas por selección acotada | Métricas útiles, compartir mejorado, destacados/ofertas y un experimento de referidos | Ambos; fundador prioriza con cohortes | Métricas distinguen clic/solicitud/venta; señal de uso y retención; no colección de funciones sin adopción |
| 5. Integraciones | 80–160 h por integración mayor; 4–8 semanas orientativas | MP por comercio, dominios o API WhatsApp, uno por vez | Codex integración/operación; Claude autorización/estados; fundador valida demanda | Cuenta correcta recibe pago; revocación/contracargo probados; o DNS/SSL/retirada seguros; costos y soporte comprobados |

La fase 5 no tiene total comprometido: son proyectos distintos. Las fases 2/3 pueden intercambiarse si la cobranza manual empieza a costar más que la fricción de carga. No asumir que todas deben construirse para facturar.

## Correcciones al plan original

1. **Cobrar antes de automatizar.** Piloto de fase 1 con pago externo manual conciliado; facturación del fundador mediante herramienta existente. No instalar integración online ni aceptar capturas como prueba.
2. **Landing mínima y métricas desde fase 1.** Una página con propuesta, demo y condiciones del piloto; la monetización automatizada sigue en fase 3.
3. **Avisos email en fase 1.** Se construye infraestructura de correo para autenticación de todas formas; alerta de pedido con outbox evita depender de que el vendedor mire el dashboard.
4. **Preview básico desde fase 1.** Es requisito de apariencia; preview por dispositivo y más opciones en fase 2.
5. **URLs individuales desde fase 1.** Su estructura y Open Graph se diseñan ahora; botón compartir más elaborado después.
6. **Una foto inicial, dos temas acotados.** Boutique es la tercera familia conceptual; el comercio no necesita 28 temas para validar.
7. **Importación no se descarta ciegamente.** Si ≥3 pilotos tienen más de 100 productos y no pueden publicar sin CSV, mover un importador mínimo a fase 1 y recalcular alcance (+20–35 h orientativas), retirando un refinamiento visual equivalente. No ampliar silenciosamente.
8. **Gratuito controlado.** Capacidad técnica de límites desde fase 1, adquisición abierta después de validar retención/abuso.

## Descomposición prevista de fase 1

| Entrega pequeña | Responsable principal | Evidencia |
|---|---|---|
| Contratos, esquema y tokens aprobados | Fundador + ambos | Revisión de alcance; ninguna tabla futura obligatoria |
| Autenticación, negocio y aislamiento | Codex | Pruebas negativas entre A/B |
| Catálogo/imagen y edición de precio | Codex; UI Claude en entrega separada | Guardado con errores, cuota y conflicto |
| Render público y dos temas | Claude; revisión Codex | Mismos datos/comportamiento y presupuesto de assets |
| Carrito y cotización/revalidación | Claude UI; Codex servidor | Precio alterado y carrito desactualizado |
| Pedido idempotente/estados | Codex | Concurrencia, snapshots y recuperación |
| Avisos y operación | Codex | SMTP caído, job repetido y alertas |
| Ajustes de UX/accesibilidad | Claude | Móvil/teclado y comercio piloto |
| Staging, carga, backup y ensayo completo | Codex; ambos revisan | Restauración y reporte de mediciones |
| Lanzamiento limitado | Fundador autoriza | 5–10 tiendas y canal de soporte |

Estos son paquetes de trabajo, no instrucciones para implementarlos ahora. Los tickets definitivos tendrán archivos asignados, criterios y comandos reales una vez aprobado el repositorio.

## Riesgos y mitigaciones

| Riesgo | Señal | Respuesta |
|---|---|---|
| Producto poco diferente | Comercios prefieren alternativa gratuita aun con demo | Reducir a tarea/rubro; medir antes de ampliar |
| Precio demasiado bajo | Soporte consume margen | Autoservicio, horarios, paquete/precio revisado; no soporte ilimitado |
| Falta de tráfico de comercios | Catálogo publicado nunca compartido | Incorporación con acción de compartir; enseñar uso, no prometer compradores |
| Catálogo difícil de cargar | Abandono en producto 3–10 | CSV/fotos priorizados por evidencia |
| Necesidades mayoristas prematuras | Variantes/bultos/listas por cliente desde primera entrevista | Elegir otro piloto o replantear segmento/costo |
| Solicitud confundida con venta | Métricas infladas, reclamos | Estados y nombres honestos |
| IDOR multi-tenant | Usuario alcanza datos ajenos | Bloquear lanzamiento; pruebas de aislamiento sistemáticas |
| Precio cacheado o carrito viejo | Total inesperado | Versionado/revalidación y confirmación explícita |
| WhatsApp no se abre | Navegador embebido, app ausente, link extenso | Pedido persistido, botón reabrir/copiar, resumen acotado |
| Hosting se satura | Latencia, procesos, colas retrasadas | Lotes cortos, imágenes fuera, medición y subir capacidad |
| Gratis se convierte en abuso | Coste/registro, spam, cuotas agotadas | Verificación, límites, adquisición gradual y moderación |
| Dependencia de MP | Eventos perdidos, OAuth revocado | Adaptadores, conciliación y estados separados |
| Se borra información por impago | Pérdida de confianza | Downgrade reversible/exportación, retención transparente |
| Cambios concurrentes de agentes | PR pisa contratos/vistas | Worktrees, dueño por archivo, PR pequeño y revisión |
| Incumplimiento de datos/consumo | Políticas ausentes, solicitudes de supresión | Revisión local, minimización y procedimientos antes del piloto público |
| Precios o APIs cambian | Presupuesto/documentación desactualizados | Revalidar antes de contratar e integrar; no precios eternos |

## Decisión de seguir o detener

No continuar a escala por cantidad de registros. Usar los criterios de [PRODUCT.md](PRODUCT.md): actividad de semana 4, pagos y renovaciones, soporte y publicación autónoma. Si la única tracción viene de trabajo manual del fundador, presupuestar explícitamente servicio de incorporación o reducir fricción antes de seguir.
