# Español · Eurowebcart CMS Lite

## Primeros pasos en 1.6.58

- Activa un sitio completo en Plantillas de diseño → Temas. Los colores y las fuentes tienen pestañas propias. Puedes activar varias familias y guardarlas localmente.
- Activa las bibliotecas en Configuración → Bibliotecas y guarda los recursos que necesites en local. Font Awesome está incluido.
- En modo de edición, un doble clic sobre el contenido abre el editor en una ventana modal. Un doble clic sobre el H1 estándar abre su campo en SEO.
- Las definiciones de campos son comunes y los valores se guardan por página. Puedes asignar un valor a la página abierta al crear el campo. El control se adapta al tipo.
- Para un dominio nuevo, crea un ZIP completo con el tema activo y prueba una instalación limpia. En un sitio existente, haz una copia de seguridad y usa una actualización firmada.

- [Gestión](es/readme.md)
- [Preguntas frecuentes del administrador](es/faq.md)
- [Plantillas y componentes](es/templates.md)

## Menú móvil · 1.6.7

- Mostrar en el menú principal
- Mostrar en el menú móvil
- Ocultar en el menú móvil


## Editar datos desde la página · 1.6.6

En el modo de edición, los bloques, los formularios [formX], los fragmentos [tel] y otros componentes con editor ofrecen Datos y Pantalla completa. La lista de vistas contiene las plantillas registradas. Elige Carpeta raíz para mover una sección al nivel superior. Usa [list-pages] para listar las páginas publicadas de la sección y sus subcarpetas. Cierra el menú de idioma haciendo clic fuera o pulsando Escape.


## Archivos de configuración, mapas y plantillas · 1.6.5

Abre Mantenimiento → Archivos de configuración para editar .htaccess, robots.txt y sitemap.xml. El generador utiliza las páginas publicadas marcadas para el mapa del sitio. Puedes recuperar la versión anterior de .htaccess.

Indica la latitud y la longitud. [map] muestra el punto de la página y [maps] reúne los puntos de las páginas publicadas. center y zoom ajustan la vista. El proveedor de mapas se configura en Integraciones.

[main-menu] es el menú bajo la cabecera, con apartados desplegables. Crea plantillas y define su visibilidad por dispositivo. En fragmentos, el botón de añadir dominio abre el formulario. Usa [privacy] para enlazar a la política.

En Integraciones, introduce los ID de JivoSite, Microsoft Clarity y Umami Cloud, el código de Botfaqtor o un webhook entrante de Slack. Las guías están junto a los campos. En Zadarma, consulta el saldo o prueba SMS y llamadas. Las pruebas pueden tener coste. Para [payment], añade enlaces de YooMoney, PayPal y SBP. Consulta el estado en el panel del proveedor.

Los editores HTML y visual tienen iconos de formato, títulos H1–H5 y listas. Sitúa el cursor y elige plantilla, componente, imagen o icono. Puedes seleccionar formularios y parámetros de componentes. Font Awesome está disponible con la biblioteca activada.

Pulsa el nombre de una carpeta para gestionar la sección a la derecha. Elige la sección superior, ajusta los menús de escritorio y móvil o introduce una URL de redirección. Una sección vacía lista sus subsecciones. En Mantenimiento añade reglas como /contact.html => /contacts/ y edita las páginas de /error/. El idioma y la búsqueda tienen iconos en móvil.

```text
[map]
[map center="55.7558,37.6173" zoom=14]
[maps center="55.7558,37.6173" zoom=11]
[main-menu]
[lang]
[privacy]
[payment]
```

```php
$var = translit("Москва"); // Moskva
```


## Botones de la cabecera de administración · 1.6.4

El globo abre el sitio y la flecha cierra la sesión. En el móvil solo se muestran los iconos. Pasa el cursor o enfoca los botones con el teclado para ver sus etiquetas.

Contenido, Formularios y solicitudes, y Sitio y servicios se abren directamente. Las pestañas de la sección elegida aparecen debajo del menú principal.

Inserta [bread-crumbs] o [search] en el HTML de la página de inicio (index.php) o de una plantilla. Cada bloque aparece una vez donde lo coloques. Se mantienen los ajustes de navegación y búsqueda. [privacy] enlaza a /privacy/. Edita la política en Páginas.

```text
[bread-crumbs]
[search]
[privacy]
```


## Nuevo en la versión 1.6.3

### Inicio y política de privacidad

Abra "Plantillas de diseño" para editar la página de inicio y la página /privacy.php. El código [page-content] muestra el contenido de la página principal. En la política [site-name] y [site-url] toman el nombre y dirección pública de “Configuración → General”. Complete el texto inicial con información sobre su sitio y procesamiento de datos.

### Cookie y volver al principio

En el pie de página, [conf-policy] proporciona un enlace a la política, [cookies] abre la configuración y [top] se desplaza hasta la parte superior de la página. Los análisis y los mapas CMS integrados están a la espera de un acuerdo. Los scripts agregados manualmente a plantillas o encabezados de servicios requieren su propia configuración.

### Editor functions.php

Abra "Plantillas de diseño → Funciones" después de la pestaña "Encabezados de servicio". Agregue cierres a la matriz devuelta. En su plantilla PHP, llame a `translit("Москва")` o `ew_call("translit", "München")`. En HTML inserte [func=translit value="Москва и München"]. El ejemplo produce `Moskva i Muenchen`.

### Editar plantillas estándar

En el campo "Plantillas de diseño" ya contiene el código fuente PHP y HTML resaltado. Al editar una plantilla estándar, seleccione "PHP personalizado". Al guardar se crea una versión personalizada. Usando los botones al lado de la plantilla, puede deshacer la última edición o restaurar el original.

### Verificación de PHP antes de guardar

Los editores de plantillas de diseño, módulos y functions.php verifican la sintaxis con el comando `php -l`. Si hay un error, se mantiene la versión anterior. Para seleccionar la CLI de PHP en el servidor, utilice la variable `SITE_PHP_LINT_BINARY`.

### Retroiluminación y búsqueda compacta.

CodeMirror está habilitado de forma predeterminada en instalaciones nuevas. La configuración se encuentra en "Bibliotecas y fuentes". En la búsqueda compacta, el campo se expande hacia la izquierda, la cruz borra la línea y la tecla Intro inicia la búsqueda.

### Menú de administración y panel de edición

El menú debajo del encabezado del panel de administración y el panel de edición del sitio muestran íconos con subtítulos. Los iconos permanecen en la pantalla estrecha. La información sobre herramientas se abre al pasar el mouse y el teclado se enfoca en una pantalla estrecha. “Ocultar” se encuentra junto al resto de acciones de la página. El interruptor del modo de edición se encuentra a la derecha.

### Descripción y código del módulo PHP.

En "Módulos PHP", la breve descripción es para texto sin formato. Pegue el cuerpo de la función en un campo separado “Código fuente · PHP”.

## Nuevo en la versión 1.6.2

### Idioma de administrador y ayuda.

En la configuración regional, habilite los idiomas deseados. El menú de selección de idioma en el encabezado cambia la interfaz y la ayuda, manteniendo la sección actual. Los textos de las páginas y los valores de su configuración se almacenan en su forma original.

### Decoración del sótano

El código [author] muestra la firma Eurowebcart CMS Lite, el nombre del autor Бандалетов Павел y un enlace a GitHub. Para elegir dónde colocar su firma en su pie de página HTML personalizado, pegue [author] en la línea correspondiente.

### Luz de fondo en modo de edición

Pase el cursor sobre el enlace Editar componente o establezca el foco del teclado en él. El marco resaltará el resultado de este inserto en particular.

### Dónde cambiar la búsqueda

En “Configuración → General” seleccione el tipo de búsqueda. En "Mantenimiento" puede volver a indexar páginas y el campo en el encabezado del administrador busca por configuración.

### Resultados y sugerencias

La búsqueda muestra 20 resultados numerados por página. El campo compacto se abre a la izquierda de la lupa con un icono Intro dentro. Las sugerencias de administración enlazan directamente a los ajustes.

## Novedades de la versión 1.6.1

### Búsqueda

En General puedes desactivar la búsqueda o elegir lupa o campo ampliado. La cabecera de administración busca ajustes. El índice se actualiza tras editar; para FTP se puede programar. Se indexan páginas incluidas y bloques de datos sin ejecutar módulos PHP.

### Módulos PHP

Despliega un módulo para ver ajustes y páginas que lo usan. Indica identificador, nombre del archivo y cuerpo de función. Al renombrar el archivo se mantiene [php]id[/php]. Activa CodeMirror para resaltar sintaxis.

### Plantillas y traducciones

Deshaz hasta ocho cambios por plantilla o restablece el PHP original. Cada dominio tiene su propio historial. Usa HTML personalizado en la administración y templates/local/ para PHP. Puedes cambiar de idioma al crear una traducción; las traducciones guardadas se trasladan con la página.

### Integraciones y mantenimiento

CAPTCHA, mapas, telefonía y analítica enlazan a la documentación de sus proveedores. Configura URL y token HTTP de la devolución de llamada. Los ajustes de un subdominio registrado se mantienen al pasar al dominio principal. Ejecuta php update.php --scheduled cada 15 minutos.

## Novedades de la versión 1.6.0

### Búsqueda y caché

Reconstruye el índice de texto en Mantenimiento después de publicar o editar páginas desde la administración o por FTP. Las sugerencias usan las páginas públicas en el idioma del visitante. La caché está desactivada de forma predeterminada y se administra allí. Los formularios y bloques dinámicos se procesan en cada solicitud.

### Edición en el sitio

Tras iniciar sesión, la barra superior tiene un interruptor a la derecha para editar componentes en todo el sitio. CSS y JS de la página se editan en un cuadro modal. La administración ofrece modos HTML y visual, inserción de códigos y carga masiva y ordenación de archivos mediante AJAX.

### Componentes y navegación

Sin parámetros se usan los ajustes globales. width admite 100, 50, 33 o 25, con o sin %. Los bloques se apilan en móviles. numbers=off oculta el contador. Desliza para cambiar de foto. El menú móvil sale de la derecha y las migas de pan pueden desactivarse en ajustes generales.

```text
[gal width=50 numbers=off arrows=on fit=cover]/images/myfotos/[/gal]
[fotos width=50% columns=3 captions=on]/images/myfotos/[/fotos]
[files width=33 icons=off size=on download=on]/files/myfiles/[/files]
```

### Idiomas, apariencia y precios

Elige idiomas, moneda, formatos y zona horaria en ajustes regionales. Guarda index_en.php junto a index.php para traducir una página; si falta, se muestra el texto original. El campo Precio usa la moneda seleccionada. Previsualiza las paletas y el modo, sube los logotipos claro y oscuro y prueba Roboto Condensed con tu propia frase.

```text
/contacts/index.php → /contacts/index_en.php
```

### Formularios, solicitudes e integraciones

Configura claves de Google reCAPTCHA v2 globales o por formulario. Mensajes y llamadas muestran 20 entradas y priorizan las no leídas. Integraciones incluye Zadarma, Telegram, analítica y mapas Google o Yandex con agrupación opcional. El cuadro de bibliotecas prueba la versión activa y muestra el código. CodeMirror ofrece Auto, HTML, CSS, JS, PHP y RAW.

```text
[map lat=55.7558 lng=37.6173 title="Office" zoom=13 provider=yandex url="/contacts/" button="Open"]
```

```text
[callback button="Call me" title="Callback" submit="Send" placeholder="+7(900)123-45-67"]
```

### Módulos PHP

En Módulos PHP, edita el nombre, la descripción y la disponibilidad. server-date muestra la fecha con el formato regional. Los desarrolladores instalan los archivos ejecutables en una carpeta protegida.

```text
[php]server-date[/php]
```

## Ajustes de la página · 1.6.8

Escribe el contenido en Edición. Configura la dirección, el menú y el calendario de publicación en Ajustes de la página. SEO reúne títulos, descripción, palabras clave e inclusión en sitemap. Mapas contiene coordenadas y ejemplos.

Deja las fechas vacías para eliminar los límites. Se usa la zona horaria del sitio. Las páginas ocultas se publican por separado.

- `[map]` — mapa con el punto de la página actual.
- `[maps]` — mapa con los puntos de todas las páginas publicadas.

## Banners y editor de imágenes · 1.6.8

Administra conjuntos y diapositivas en Componentes. Usa fotos de una carpeta, añade texto y botones para enlaces, formularios o devoluciones de llamada y programa su aparición. El modo de edición muestra acciones para cada diapositiva. El selector permite explorar carpetas, subir, eliminar y crear copias rotadas o redimensionadas con GD. Puedes buscar iconos de Font Awesome por nombre.

```text
[banners]
[banners set=summer arrows=on numbers=off]
[banners]/images/myfotos/[/banners]
```

## Idiomas · 1.6.9

La pestaña Idiomas muestra los idiomas de la página. Abrir lleva a la versión guardada. Crear traducción abre un editor vacío.

Una subsección se crea dentro de la sección actual, una sección hermana a su lado y una sección raíz en el nivel superior del sitio.

## Compatibilidad con PHP y navegación · 1.6.10

La versión mínima de PHP es 7.2. El sitio y su administración admiten PHP 8.6. Usa la sintaxis de la versión instalada en el servidor para tus módulos y plantillas.

El elemento actual de [bread-crumbs] despliega las secciones publicadas del mismo nivel que aparecen en el menú principal. La URL configurada se usa en menús, listas de secciones, rutas de navegación, resultados y sugerencias de búsqueda.

La papelera elimina una fila del editor del bloque de información. Eliminar una columna quita todos sus valores. Los cambios se escriben al guardar, incluso con el guardado automático al reordenar.

### Copias de seguridad

El ZIP privado contiene páginas, medios, bloques, ajustes, plantillas y módulos PHP. La cuenta y las solicitudes se guardan por separado. Se requiere PHP Zip.

Reemplazar contenido y ajustes. Primero se guardará una copia del sitio actual.

### Limpiar archivos sin uso

El análisis busca medios sin referencias en el contenido guardado. Revisa la lista y elige los archivos. Primero se crea una copia de seguridad. Quitar el código del componente conserva su carpeta y archivos.

### Elegir un punto en el mapa

Crea una clave para «JavaScript API» en la consola de Yandex Maps y restríngela por referente HTTP. En la pestaña Mapas de la página, elige un punto y aplica sus coordenadas.

Selecciona texto para alinearlo o quitar el formato. Haz clic en una imagen para editarla y arrastra la imagen o su esquina para moverla o cambiar su tamaño. Los enlaces admiten URL, correo, teléfono y ancla. Los botones abren URL, formularios, devoluciones de llamada o enlaces de pago del proveedor.

Las pestañas HTML y Editor visual cambian el modo. SUP, SUB y Párrafo insertan los elementos correspondientes. Junto a cada componente aparece su código. Las carpetas se crean al insertar. Sus nombres admiten letras latinas, cifras, guiones y guiones bajos. La pestaña Idiomas enlaza con los ajustes de idioma. Los paneles de inserción se abren encima del área de edición. Pulsa «Cerrar» en la parte superior o Esc para cerrar el panel y desactivar el botón. Las flechas de la barra de herramientas deshacen y rehacen hasta diez cambios de texto a través del servidor. El icono del reloj carga el último texto guardado. Si hay cambios pendientes, se pide confirmación al salir. El icono Guardar, antes de las flechas, se resalta cuando hay cambios sin guardar. Selecciona un fragmento y abre Estilo del texto. Ajusta el tamaño, el color del texto o del fondo, o elige una fuente habilitada en los ajustes del sitio.

```text
[button action="url" label="Contacts" url="/contacts/"]
[button action="form" label="Write to us" form="form1"]
[button action="callback" label="Call me"]
[button action="payment" label="Pay" amount="1250.00" currency="RUB" yoomoney="410010000000000"]
[map]
[maps]
```

## Venta · 1.6.11

Configura precio y pago en Venta, con valores opcionales por idioma. Inserta [item-price] y [item-buy-button]. El pedido se registra antes del pago. Los estados se pueden enviar por correo y SMS de Zadarma.

Pedido recibido · Pago aceptado · Enviado · Cerrado · Cancelado

```text
[item-price]
[item-buy-button]
```

Selecciona un archivo o URL para insertar una imagen. Haz clic en la imagen para ajustar tamaño, alineación, cuatro márgenes y CSS. Su alineación es independiente del texto. HTML muestra los bloques en líneas separadas. [list-pages] está en Componentes. El logotipo utiliza los colores del tema.

## Gráfico del bloque de datos

En el editor de páginas, abre el selector de componentes y elige una tabla o un gráfico. Activa Chart.js para los gráficos, elige una columna de etiquetas y selecciona columnas de tipo Número o Precio. Los gráficos circulares, de anillo y de área polar usan una serie no negativa. Si se desactiva la biblioteca, se muestra la tabla.

Los formularios, plantillas, fragmentos guardados y componentes registrados aparecen al volver a abrir el editor. El ID de un conjunto nuevo admite A–Z, a–z, guiones y guiones bajos. Al subir imágenes a una carpeta [banners], aparece un enlace para editar cada diapositiva. En modo de edición, se abre la diapositiva elegida en una ventana con un enlace a los ajustes predeterminados. Junto a Guardar, el gestor de carruseles, galerías y listas permite subir, eliminar, ordenar y crear subcarpetas (A–Z, a–z, 0–9, - y _). Los colores se ven en muestras cuadradas.

```text
[iblock=table]/data/myiblock.txt[/iblock]
[iblock=charts type=line labels=month values=sales,orders]/data/myiblock.txt[/iblock]
[banners set=summer-promo]
```

La columna Botón de formulario muestra un botón Solicitar en cada fila configurada. Elige el formulario por nombre. En CSV, escribe su código, por ejemplo form86.

El tipo «Etiqueta oculta» añade filtros encima de la tabla. Escribe una o varias etiquetas separadas por comas en cada celda. «TODO» muestra todas las filas.

## Encabezado y pie de la administración · 1.6.12

En escritorio, el logotipo aparece junto a Eurowebcart CMS y Light con la versión instalada. En teléfonos, el símbolo comparte fila con los botones. El pie enlaza al repositorio. En Ajustes se puede comprobar la actualización acumulativa.

## Encabezado y pie de la administración · 1.6.13

En escritorio, el logotipo aparece junto a Eurowebcart CMS y Light con la versión instalada. En teléfonos, el símbolo comparte fila con los botones. El pie enlaza al repositorio. En Ajustes se puede comprobar la actualización acumulativa. La etiqueta predeterminada Inicio de la lista sigue el idioma de administración. El logotipo del pie muestra Eurowebcart CMS y LIGHT en dos líneas.

## Información de PHP

En Mantenimiento, phpinfo() muestra la versión, los módulos y la configuración de PHP a administradores autenticados. Oculta variables de entorno y de la solicitud. Ajusta error_reporting en los ajustes generales. Se usa el valor del servidor por defecto.

## Ocultar el árbol de páginas · 1.6.14

En escritorio, la flecha pliega el árbol de páginas y amplía el editor. El botón encima del editor lo restaura. El navegador recuerda la elección.

## Ocultar el árbol de páginas · 1.6.15

En escritorio, la flecha pliega el árbol de páginas y amplía el editor. El botón encima del editor lo restaura. El navegador recuerda la elección.

## Elegir un punto en el mapa

Crea una clave para «JavaScript API» en la consola de Yandex Maps y restríngela por referente HTTP. En la pestaña Mapas de la página, elige un punto y aplica sus coordenadas. Para pruebas locales, configura localhost sin puerto como referente HTTP, espera la activación y abre la administración mediante localhost.

## Búsqueda en el menú · 1.6.16

[search] sitúa la búsqueda junto a los enlaces de [main-menu] en escritorio y dentro de [mobile-menu] en el móvil. El modo se elige en los ajustes generales. Un [search] en el contenido tiene prioridad.

`[search]`

## Búsqueda en el menú · 1.6.17

[search] sitúa la búsqueda junto a los enlaces de [main-menu] en escritorio y dentro de [mobile-menu] en el móvil. El modo se elige en los ajustes generales. Un [search] en el contenido tiene prioridad. El campo de búsqueda aparece al abrir el menú móvil. [lang] muestra el selector de idioma dentro de ese menú y en el encabezado de escritorio.

`[search]` · `[lang]`

La parte inferior del menú móvil muestra los contactos y las redes sociales configurados.

## Subtítulo del sitio

El subtítulo de los ajustes generales se muestra debajo del nombre en el encabezado. En móviles, el nombre también usa un tamaño menor.

## Ciudades

En Ciudades, asigne un dominio registrado y configure hasta cinco teléfonos. Puede usar un código como [tel] en el campo de teléfono. [cities] muestra el selector, [phones] los números configurados y [tel], [tel2]–[tel5] los números individuales. El navegador recuerda la elección y actualiza los números sin recargar la página.

`[cities]` · `[phones]` · `[tel]`

## Animación al desplazarse

En los ajustes generales, elija el efecto de aparición de los bloques al desplazarse. Está desactivado de forma predeterminada y respeta la opción de reducir movimiento del dispositivo.

## Plantillas de página

Elija una plantilla al crear una página. Rellena el contenido y los campos SEO, que después podrá editar. Añada sus propias plantillas en Plantillas de página. Los mapas, formularios y compras usan los ajustes de sus componentes.

El CSS y JavaScript de la plantilla se copian a los campos de la página. Añada clases propias en HTML y defínalas en el CSS de la plantilla.

### Clases reutilizables

Contenedor, cuadrícula adaptable, sección destacada, tarjeta y precio destacado.

`.page-preset` · `.page-preset-grid` · `.page-preset-hero` · `.page-preset-card` · `.page-preset-price`

Elige «Editar» para modificar una plantilla. «Guardar plantilla» aplica los cambios al crear nuevas páginas. «Duplicar» crea una plantilla independiente. En el editor puedes abrir la plantilla seleccionada o crear otra a partir de la página guardada. En el formulario de la plantilla, elige «Insertar icono» y busca por nombre. También puedes introducir un código como `[fa-icon=house]`.

Las plantillas de diseño están contraídas al inicio. Pulse un título o seleccione Expandir todo. Pulse de nuevo para contraer la lista.

Para cambiar el ancho del carrusel, active el modo de edición, pulse Editar componente junto a los banners y elija el ancho del bloque.

## Ciudades · Teléfono · 1.6.18

Solo es obligatorio el nombre de la ciudad. El dominio y los teléfonos son opcionales. Introduzca + y el prefijo de país, por ejemplo +7 (900) 000-00-00. Puede usar un código como [tel] en lugar del número.

Se usa la ciudad guardada, después la del dominio y por último la primera de la lista. Sin ciudades, se muestran los teléfonos generales.

Formularios, solicitudes de llamada y pedidos nuevos usan el correo de la ciudad o el del sitio si queda vacío. Si ambos faltan, los formularios usan sus propios destinatarios. Los enlaces sociales vacíos se heredan del sitio.

En el modo de edición, pase el cursor por una plantilla para editarla. En cualquier componente [php] instalado, Editar módulo abre el código y los ajustes en una ventana. El editor de diapositivas muestra nombre y código del formulario. [banners width=100] ocupa toda la ventana como bloque independiente sin barra lateral. El editor de plantillas se abre en una ventana modal del sitio. En modo de edición, haga doble clic en una zona libre de la plantilla. El aviso aparece debajo del botón al pasar el cursor. Si se superponen dos bloques, prevalece la plantilla posterior. Editar diapositiva abre la diapositiva seleccionada. La barra del sitio muestra los componentes, las plantillas y los fragmentos de la página actual. En el editor de páginas, abra Componentes de la página junto a Idiomas para editar los parámetros y datos disponibles. Haz doble clic en el contenido para abrir el editor de la página actual. El editor se abre en una ventana sobre el sitio. Tras guardar, ciérrala para ver los cambios en la página. Haz doble clic en el encabezado H1 para ir a su campo en la pestaña SEO.

El gestor comprueba las referencias antes de eliminar y hace una copia de las carpetas no vacías. Cada banner puede tener título y subtítulo.

El teléfono se comprueba al escribir. En números rusos, el 8 inicial se convierte en +7. Tras el envío se oculta el formulario y al volver a abrirlo se muestra la confirmación.

El prefijo elegido se añade a los números sin +. Los números que empiezan por + conservan su prefijo.

## Temas de diseño

Copie el archivo al tema y guárdelo. Los archivos ausentes usan la versión incluida. Los cambios, códigos nuevos, historial y ajustes de dominio se guardan en el tema activo. Use css/theme.css y js/theme.js para estilos y scripts adicionales.

`templates/themes/demo/header.php` · `templates/themes/demo/components/banners.php`

Los idiomas están al final del menú móvil. En escritorio, el globo está junto a la búsqueda en el menú bajo el encabezado.

Galerías, banners y archivos buscan primero en el tema activo y usan los archivos comunes si falta la carpeta. Las carpetas nuevas, subidas, logotipos y favicons se guardan en el tema. La copia incluye el tema y sus ajustes.

Elija el ancho de la ventana (width=page), del contenido (width=content) o del bloque padre (width=parent). height ajusta la altura en escritorio y mobile-height en móvil. auto conserva el tamaño natural.

`[banners width=page height=520 mobile-height=240]` · `[form1 width=parent]`

El último elemento de la ruta muestra las secciones hermanas publicadas, incluidas las ocultas en el menú principal. Los enlaces usan la URL configurada.

## Temas de diseño · КОНТУР · 1.6.19

Un tema de sitio completo cambia el diseño, las páginas, los bloques de datos y los ajustes. Los demás temas usan las páginas compartidas.

Una marca indica que un archivo del tema prevalece sobre el archivo estándar del CMS. Puedes editarlo o restaurarlo.

**Restaurar la versión de fábrica**

### Portafolio

Abre kontur-works en Contenido → Bloques de datos. Indica el título, la categoría, la descripción, fotos separadas por comas y un enlace opcional. La primera foto será la portada. Usa las mismas columnas para otras colecciones.

```text
[portfolio]
[iblock=portfolio limit=2]/data/kontur-works.txt[/iblock]
[maps source=kontur-map]
```

«Тихий сад» es un sitio completo para un estudio de paisajismo. Selecciónalo en Plantillas de diseño → Temas de diseño y pulsa Aplicar. Edita los bloques garden-projects y garden-prices y el conjunto de banners garden-cover. Añade tus datos de contacto y el destinatario de [form85].

El tema Dezinfektori incluye 32 servicios, solicitudes [form86] y un formulario de reseña [form87]. Abre la reseña en Mensajes. Con permiso, copia el nombre, texto y puntuación a dez-reviews y activa la publicación. Las páginas de servicios incluyen explicaciones de riesgos, imágenes de antes y después y relatos de ficción. Edita estos bloques en Páginas.

```text
[iblock=reviews limit=6]/data/dez-reviews.txt[/iblock]
```

La página de inicio muestra reseñas aprobadas de todas las páginas si el filtro está vacío.

Cada página del catálogo tiene un formulario de reseñas. Tras aprobarla en Reseñas, aparece en la página desde la que se envió.

### Colores del sitio · Fuentes

Marca las fuentes que quieras activar y asígnalas al texto, títulos o controles. Tu sitio aloja los archivos locales.

Bibliotecas → Alojar localmente

Plantillas de página → Crear página

## Campos personalizados

Introduce el valor de la página actual en la fila nueva y guárdalo junto con los ajustes del campo.

## Reseñas

Inserta el código en el editor de páginas. Los visitantes escriben su nombre, una reseña y una nota de 1 a 5 estrellas. En Reseñas puedes aprobar, rechazar, editar o eliminar reseñas. Cargar más añade las diez siguientes.

Escribe una ruta como /contacts/ o deja el campo vacío para mostrar todas las reseñas. El formulario registra la página desde la que se envió. Solo se publican las reseñas aprobadas. CAPTCHA usa la configuración general del CMS.

```text
[reviews]
```

El botón Dejar una reseña abre el formulario.

## Progreso de la actualización · Progreso de la indexación

Descargando el archivo de actualización. Descargando la firma. Verificando la firma y las sumas de comprobación del archivo. Se conservan las páginas, los ajustes y los archivos de usuario. Preparando los archivos y las copias temporales de restauración. Se usan las plantillas, estilos y scripts incluidos en el CMS. Se conservan las páginas y los ajustes actuales. Se usan el diseño y las páginas incluidos en el tema. Se conservan los ajustes y las demás páginas. Creando una copia de las páginas, los ajustes y los archivos multimedia. Verificando la copia de seguridad. Copia de seguridad guardada en Mantenimiento. Recopilando la lista de páginas. Procesando las páginas y sus traducciones. Guardando el índice de búsqueda.

**Diseño y adición de páginas faltantes**

Se aplica el diseño y se añaden las páginas de navegación faltantes. Se conservan las páginas y los ajustes existentes.

**100% · Listo**

En el modo de edición, pasa el cursor sobre el valor de un fragmento y abre su editor. Guarda y cierra el cuadro de diálogo para ver los cambios en la página.

El botón de edición de [reviews] abre los ajustes y la moderación de reseñas en un cuadro de diálogo.

**Reseñas por lote · Texto del botón «Cargar más»**

El tamaño del lote se aplica a la primera carga y a cada clic. Deja el texto vacío para usar la etiqueta predeterminada en el idioma de la página.

El botón «Datos» abre el editor de [iblock]. Para los demás componentes, usa «Configuración».
