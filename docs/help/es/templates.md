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

