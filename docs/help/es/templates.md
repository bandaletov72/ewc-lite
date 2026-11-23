# Plantillas y componentes

## Plantillas de diseño

### Encabezado, menú y pie de página

La página completa conecta estos bloques automáticamente. Las versiones generales se editan en "Plantillas de diseño" y para un dominio individual, en la sección "Dominios".

```text
[header]
[mobile-menu]
[menu]
[footer]
```

### Insertando otra página

Inserta el contenido de una página publicada. El anidamiento máximo es de diez niveles. Se omite la repetición de una página en la misma cadena.

```text
[include]/contacts/[/include]
```

### Enlaces en el menú

La casilla de verificación "Mostrar en el menú" agrega un enlace a los menús estándar. Si el nombre está vacío, se toma el título. Si desmarca la casilla de verificación de una página de carpeta, sus subpáginas también desaparecerán del menú.

```text
MenúVisible: 1
Menú: Contactos
```

### CSS y JavaScript

El código general se edita en la pestaña “CSS y JS”. El código de la página se edita en el bloque “Páginas CSS y JavaScript” y se incluye después del general. Pegue el contenido de CSS y JavaScript en los campos.

```text
.page-content { max-width: 72rem; }
```

```text
document.documentElement.classList.add('custom-script-loaded');
```

### encabezados de servicio

En la sección "Plantillas de diseño → Encabezados de servicio", puede insertar metaetiquetas generales y códigos de contador dentro del encabezado o antes del cuerpo de cierre. Canonical se agrega automáticamente en páginas completas.

```text
<meta name="theme-color" content="#065f63">
```

### Iconos impresionantes de fuentes

Font Awesome Free 7.3.1 está conectado. El código corto funciona en bloques de contenido y diseño HTML. Hay disponibles estilos sólidos, regulares y de marca. Agregue texto junto al ícono si representa una acción.

```text
[fa-icon=house] [fa-icon=regular:envelope] [fa-icon=brands:github]
```

```text
<i class="fa-icon fa-solid fa-phone" aria-hidden="true"></i> Contacto
```

### Bibliotecas y fuentes

Las conexiones se gestionan en “Configuración → Bibliotecas y fuentes”. Para Material UI, utilice JavaScript modular. La fuente de Google seleccionada reemplaza la fuente del sistema del sitio.

```text
<script type="module">import { Button } from "@mui/material";</script>
```

```text
GLightbox({selector: ".glightbox"});
```

## Variables, subplantillas y fecha.

### Variables del sitio

Agregue entradas a "Configuración → General". Inserte la clave en el texto, encabezado, título del sitio o pie de página.

```text
[city] = Moscú
[spb] = San Petersburgo
```

```text
Servicio en la ciudad [city]
```

### Fragmentos

En la sección Subplantillas, especifique un nombre, una descripción y un valor. Para un dominio, primero se toma su valor y luego su valor general. La subplantilla puede contener HTML. Se elimina el marcado en los encabezados.

```text
[tel]
```

```text
[title]
```

### Fecha y hora

La zona horaria está tomada de PHP. [year] - año, [month] - mes, [day] - día del mes, [time] - horas y minutos, [dmY] y [date] - fecha DD.MM.YYYY, [datetime] - fecha y hora.

```text
© [year] [city]
Fecha [dmY], hora [time]
```

### Nombre y autor del CMS

El código [author] genera Eurowebcart CMS Light, el nombre del autor Бандалетов Павел y un enlace a GitHub. En un pie de página estándar, la firma aparece automáticamente. En su propio pie de página HTML, pegue el código donde lo desee.

```text
[author]
```

### Plantilla y política de inicio

[page-content] inserta el contenido de inicio en [home]. Abre /privacy/ en Páginas para editar la política. [site-name] y [site-url] proceden de los ajustes generales.

```text
[page-content]
```

```text
[site-name] · [site-url]
```

### Enlaces y configuraciones en el pie de página

[privacy] enlaza a /privacy/, [top] vuelve al inicio de la página y [cookies] abre los ajustes de consentimiento.

```text
[privacy]
[cookies]
[top]
```

### Funciones PHP comunes

En la sección “Plantillas de diseño → Funciones”, edite functions.php. En código PHP, translit() o ew_call() llama a una función registrada. El código corto [func=translit] está disponible en el contenido y en el campo "HTML personalizado".

```text
[func=translit value="Москва и München"]
```

```text
$var = translit("Moscú");
```

```text
echo ew_escape(ew_call("translit", "München"));
```

## Carrusel, galería de fotos y archivos.

### Carrusel [gal]

Muestra fotografías de una carpeta. las flechas y los números aceptan activado o desactivado, efecto: deslizar, desvanecer o ninguno, ajustar: contener o cubrir.

```text
[gal]/images/myfotos/[/gal]
```

```text
[gal arrows=on numbers=off effect=fade fit=cover width=50]/images/myfotos/[/gal]
```

### Galería de fotos [fotos]

Muestra una cuadrícula receptiva. columnas: automática o un número del 1 al 6, proporción: 4x3, 1x1, 16x9 u original, ajuste: cubrir o contener. Los subtítulos y enlaces se habilitan mediante encendido o apagado.

```text
[fotos]/images/myfotos/[/fotos]
```

```text
[fotos columns=4 ratio=1x1 fit=cover captions=on links=off width=50%]/images/myfotos/[/fotos]
```

### Lista de archivos [files]

Enumera los archivos compatibles. iconos, tamaño y descarga aceptar encendido o apagado, orden – asc o desc. Se permiten zip, pdf, xls, xlsx, doc, docx, png, gif, jpg, jpeg, mp4, mov y mp3.

```text
[files]/files/myfiles/[/files]
```

```text
[files icons=on size=on order=desc download=on width=33]/files/myfiles/[/files]
```

## Formularios y devolución de llamada

### Formulario [formN]

En el apartado “Formularios” configura los campos, su longitud máxima, texto del botón y destinatario. "Usar CAPTCHA" habilita el widget Google reCAPTCHA v2 con verificación de respuesta en el servidor. Las solicitudes se envían a través de AJAX y son visibles en la sección Mensajes.

```text
[form1]
[form2]
```

### Plantilla de formulario de correo

El asunto y el texto se almacenan por separado para cada formulario. En su plantilla de correo electrónico, inserte claves de campo entre llaves. Las listas de destinatarios y copias están separadas por comas.

```text
¡Hola {name}!
Teléfono: {phone}
Mensaje: {message}
```

### Devolver llamada [callback]

Abre un formulario con un número de teléfono. La solicitud se guarda en la sección "Devoluciones de llamada". La etiqueta del botón se puede configurar en código. Con el proveedor “No” se desactiva el envío a telefonía.

```text
[callback]
[callback=Вызвать мастера]
```

## Bloques de datos

### Tabla y listado de teléfonos.

Cree un conjunto de datos en la sección "Bloques de información". Se permiten A–Z, a–z, 0–9, guiones y guiones bajos en el nombre del bloque de información y las claves de columna. No se permiten espacios. La plantilla de tabla muestra una tabla, teléfonos muestra columnas de teléfonos con enlaces para llamar. Se admiten los códigos de cierre [iblock] y [/iblock].

```text
[iblock=table]/data/myiblock.txt[iblock]
[iblock=phones]/data/myiblock.txt[/iblock]
```

## Contactos y redes sociales

### Bloque general e iconos

[contacts] muestra un bloque de contactos, [phones] muestra números de teléfono, [soc-icons] recopila íconos de todas las redes sociales con enlaces personalizados.

```text
[contacts]
[phones]
[soc-icons]
```

### Redes sociales individuales

Cada código muestra un enlace a su red social. Las direcciones y firmas se configuran en “Configuración → Contactos y redes sociales”.

```text
[tg] [vk] [whatsapp] [youtube] [instagram] [max]
```

```text
[wa] [yt] [ig]
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

## Editar datos desde la página · 1.6.6

En el modo de edición, los bloques, los formularios [formX], los fragmentos [tel] y otros componentes con editor ofrecen Datos y Pantalla completa. La lista de vistas contiene las plantillas registradas. Elige Carpeta raíz para mover una sección al nivel superior. Usa [list-pages] para listar las páginas publicadas de la sección y sus subcarpetas. Cierra el menú de idioma haciendo clic fuera o pulsando Escape.

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

Las pestañas HTML y Editor visual cambian el modo. SUP, SUB y Párrafo insertan los elementos correspondientes. Junto a cada componente aparece su código. Las carpetas se crean al insertar. Sus nombres admiten letras latinas, cifras, guiones y guiones bajos. La pestaña Idiomas enlaza con los ajustes de idioma.

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

Las plantillas de diseño están contraídas al inicio. Pulse un título o seleccione Expandir todo. Pulse de nuevo para contraer la lista.

Para cambiar el ancho del carrusel, active el modo de edición, pulse Editar componente junto a los banners y elija el ancho del bloque.

## Ciudades · Teléfono · 1.6.18

Solo es obligatorio el nombre de la ciudad. El dominio y los teléfonos son opcionales. Introduzca + y el prefijo de país, por ejemplo +7 (900) 000-00-00. Puede usar un código como [tel] en lugar del número.

Se usa la ciudad guardada, después la del dominio y por último la primera de la lista. Sin ciudades, se muestran los teléfonos generales.

Formularios, solicitudes de llamada y pedidos nuevos usan el correo de la ciudad o el del sitio si queda vacío. Si ambos faltan, los formularios usan sus propios destinatarios. Los enlaces sociales vacíos se heredan del sitio.

En el modo de edición, pase el cursor por una plantilla para editarla. En cualquier componente [php] instalado, Editar módulo abre el código y los ajustes en una ventana. El editor de diapositivas muestra nombre y código del formulario. [banners width=100] ocupa toda la ventana como bloque independiente sin barra lateral. El editor de plantillas se abre en una ventana modal del sitio.

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

### Colores del sitio · Fuentes

Marca las fuentes que quieras activar y asígnalas al texto, títulos o controles. Tu sitio aloja los archivos locales.

Bibliotecas → Alojar localmente

Plantillas de página → Crear página

## Anuncio

El anuncio aparece en las listas de páginas y en los resultados de búsqueda. En las listas, la imagen tiene prioridad sobre el icono. Los menús muestran el icono antes del nombre de la página.

Pulsa «Insertar lista…» en el editor. Elige la sección actual para `[list-pages]` o pulsa «Insertar» junto a una carpeta. `[dir]/sample/[/dir]` muestra las páginas publicadas en /sample/ y sus subsecciones. `[dir]/[/dir]` empieza en la raíz.


Escribe un nombre en el selector de iconos. Por ejemplo, home también encuentra iconos con house en el nombre.

Abre Componentes de la página → [banners] → Diapositivas del banner para añadir, editar o reordenar las diapositivas del conjunto. Editar componente abre las opciones del bloque.

Los campos de la tabla se alinean arriba. Arrastra la esquina de un campo multilínea para cambiar su ancho y alto.

Para cambiar el ancho de una columna, arrastra el borde derecho de la cabecera o usa las flechas izquierda y derecha.

## Mapa de páginas de la sección · `[maplist]`

Selecciona «Mapa de páginas de la sección» o inserta `[maplist]`. El mapa muestra las páginas publicadas con coordenadas de la sección y sus subsecciones. En la portada abarca todo el sitio. Cada ventana muestra el título, el icono, la imagen a todo lo ancho, el anuncio y un enlace. Configura el mapa en Integraciones.

`[maplist]/sample/[/maplist]` muestra el mapa de la sección elegida. `[points]/sample/[/points]` lista las páginas con coordenadas. Pulsa una fila para ver el mapa del punto debajo. `[points display=modal]/sample/[/points]` lo abre en una ventana modal.
