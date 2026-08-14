# FALM Compare — Comparador de fotos antes/después para WordPress

Shortcode de WordPress que genera un comparador interactivo de imágenes antes/después, pensado para integrarse de forma nativa con Elementor y adaptarse correctamente al tamaño de cualquier contenedor donde se inserte, con opción de expandir a pantalla completa.

## Contexto

Este comparador tomó como punto de partida [JuxtaposeJS](https://github.com/NUKnightLab/juxtapose), la librería de comparación antes/después desarrollada por el Knight Lab de la Universidad Northwestern. Sin embargo, distintos problemas de adaptabilidad al integrarla en un sitio web actual —particularmente en dispositivos móviles y dentro de estructuras de diseño modernas como los containers de Elementor— llevaron a desarrollar una versión propia, construida desde cero, sin dependencias externas.

## Funcionamiento

El snippet está compuesto por tres funciones independientes, cada una responsable de una parte de la experiencia:

### 1. `falm_compare_shortcode`
Es la función base. Registra el shortcode `[falm_compare]` y genera:
- El HTML de la tarjeta: dos fotos superpuestas (antes/después) más la línea divisoria interactiva.
- El cálculo de altura vía JavaScript (en lugar de `aspect-ratio` o `padding-top`), para evitar conflictos con el layout flexbox de Elementor que pueden colapsar la altura del contenedor.
- La lógica de arrastre de la línea divisoria, tanto por mouse como por touch.
- El manejo del `clip-path` para revelar progresivamente la imagen "después" sin que ninguna de las dos imágenes se reescale mientras se arrastra.

### 2. `falm_compare_accessories`
Agrega un ícono animado tipo "Deslízame" sobre la línea divisoria, que:
- Oscila solo de lado a lado cuando nadie está interactuando con el comparador.
- Se desvanece apenas el usuario toca o arrastra la línea.
- Reaparece automáticamente tras un breve período de inactividad.

### 3. `falm_compare_buttons`
Agrega un botón de expandir/contraer que usa la [Fullscreen API](https://developer.mozilla.org/en-US/docs/Web/API/Fullscreen_API) nativa del navegador sobre la tarjeta específica, sin necesidad de simular pantalla completa con CSS. El botón aparece con la interacción y se desvanece tras unos segundos de inactividad.

Las tres funciones se comunican únicamente a través de la clase CSS `.falm-compare` — si se desactiva la función base, las otras dos dejan de tener efecto, ya que dependen de que ese elemento exista en la página.

## Instalación

1. Copiá el contenido de [`falm-compare.php`](./falm-compare.php)
