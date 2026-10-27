# Dirección visual y sistema de diseño

Sistema implementado. Se revisaron patrones públicos de competidores, sin copiar código, marca ni assets.

## Identidad

Dirección: **cercanía comercial cotidiana**. Cataloguito usa producto/foto/precio como protagonistas y un panel orientado a acciones. El nombre sigue sujeto a comprobar disponibilidad de dominio y antecedentes marcarios.

Paleta de marca: coral profundo `#B93828`, crema `#FFF7EC`, cacao `#321C18` y manteca `#FFD76A`. El dashboard conserva superficies claras para tareas largas. Éxito `#166534` y error `#B91C1C`. Las combinaciones principales se comprobaron con axe/Lighthouse; esto no certifica toda la aplicación. No se adoptaron el verde-crema-mostaza ni la metáfora del toldo de Sucuchito.

Cuerpo: Manrope como candidato; títulos y números de precio de la misma familia para el MVP. Boutique puede sumar Fraunces en fase 2. Evaluar licencia del archivo/fuente concreta antes de distribuir y conservar avisos. WOFF2 local, subconjunto latino con español, pesos necesarios, `font-display: swap` y fallback de métricas semejantes. Sin carga simultánea de todas las fuentes de todos los temas.

## Tokens y componentes

| Variable | Propuesta |
|---|---|
| Espaciado | 4, 8, 12, 16, 24, 32, 48, 64 px |
| Cuerpo/formularios | 16 px, interlineado 1,45–1,6 |
| Ayuda | 14 px legibles; no esconder información esencial en texto tenue |
| Radios | 8 controles, 12 tarjetas, 16 superficies grandes |
| Controles táctiles | Objetivo 44–48 px, suficiente separación |
| Sombras | Una elevación ligera; bordes para separar contenido |
| Movimiento | 120–180 ms, opacity/transform, respeta movimiento reducido |
| Ancho | Contenedor hasta ~1.200 px; gutters 16 móvil/24–32 escritorio |

Componentes comunes: botones primario/secundario/destructivo, campos con etiquetas, precio, disponibilidad, imagen con fallback, tarjeta, fila editable, filtros, categoría, carrito, aviso, tabla responsive, paginación, estados vacíos, confirmación y diálogo accesible.

No usar dashboards con gráficos sin utilidad ni tarjetas de métricas decorativas. Estado vacío conduce a una acción; error dice qué se guardó y qué falta. Indicadores de guardado asociados al producto, no toast ambiguo global.

## Tres familias a evaluar

| Familia | Productos/fotos | Jerarquía y uso | Alcance |
|---|---|---|---|
| Minimal | Ratio 1:1, grilla aireada, foto contenida | Precio claro, pocos adornos; accesorios/tecnología | Fase 1 |
| Comercial | 1:1 o miniatura, densidad mayor, filas/grilla compacta | Búsqueda/categorías primero; precio y cantidad próximos | Fase 1 |
| Boutique | 4:5, fotografía grande, serif de sistema en títulos | Relato breve, producto protagonista; ropa/belleza/pastelería | Implementado |

Un solo motor de catálogo: variantes de layout y tokens de espaciado/color/fuente/imagen. Comportamientos de precio, disponibilidad, carrito y errores idénticos. No duplicar tres páginas completas con lógica distinta. Boutique no debe enterrar buscador y primeras categorías debajo de varias pantallas de portada.

Color del comercio con paletas curadas y selector avanzado acotado; elegir automáticamente texto claro/oscuro o rechazar combinación ilegible. Mantener precio y CTA con contraste adecuado. Preview usa productos propios y estados reales; el botón Publicar diseño aplica solo apariencia.

## Dashboard móvil

Inicio: estado publicado, enlace para compartir, productos activos y últimos pedidos; acciones Agregar producto/Modificar precios. Productos es el destino frecuente al volver.

Fila móvil: miniatura, nombre de hasta dos líneas, precio editable, disponibilidad y menú secundario. Edición abre teclado decimal y acciones Guardar/Cancelar sin perder scroll. No guardar silenciosamente al tocar fuera. Impedir doble envío; permitir reintento conservando valor. Si cambia el precio desde otro dispositivo, mostrar el conflicto.

Pedidos: filtro por estado, número y antigüedad visibles; detalle con líneas, cantidades y total estimado. Confirmar/Completar/Cancelar tienen semántica distinta. WhatsApp abre contacto del comprador proporcionado, sin decir que ya hubo conversación.

Mi catálogo: enlace, vista previa, selector de familia, logo y colores; ajustes menos usados progresivamente. Plan/Cuenta concentra consumo, exportación, recuperación y baja.

## Catálogo comprador

Encabezado compacto, logo/nombre/descripción breve, búsqueda evidente y categorías con scroll horizontal accesible más alternativa Ver todas. Móvil: dos columnas cuando nombre/foto siguen legibles; una en situaciones de accesibilidad o estilo Comercial compacto. Precio no se trunca. Botón Agregar cambia a control de cantidades y confirma accesiblemente.

Barra de carrito solo con productos: cantidad/total estimado y Ver pedido; no tapa navegación ni mensajes del navegador. WhatsApp de consulta secundaria no compite con el CTA del carrito. Cantidades/precios largos en ARS probados, no solo datos perfectos.

## Accesibilidad y evaluación

HTML semántico, etiquetas persistentes, orden de tabulación, foco visible, escape y retorno de foco en diálogos; no color como única señal. [WCAG contraste](https://www.w3.org/WAI/WCAG22/Understanding/contrast-minimum.html): mínimo 4,5:1 texto normal y 3:1 grande. El tamaño táctil propuesto de 44–48 px es objetivo de comodidad, no descripción de todos los mínimos normativos.

Pruebas: 360/390/768/1440 px, zoom 200%, teclado, lector de pantalla en flujos principales, conexiones lentas, sin foto, nombres largos, precio alto, agotado, carrito vacío, error de subida y pérdida de conexión. Screenshots visuales y revisión humana; no declarar accesibilidad completa por pasar un analizador automático.
