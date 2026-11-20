# Eurowebcart CMS Light

Autor - Бандалетов Павел. [Proyecto original y actualizaciones.](https://github.com/bandaletov72/php-site-updates).

La versión mínima de PHP es 7.2. El sitio y su administración admiten PHP 8.6. Usa la sintaxis de la versión instalada en el servidor para tus módulos y plantillas.

[Preguntas frecuentes del administrador](faq.md) también está disponible en la pestaña "Ayuda".

[Ayuda para la versión actual en diez idiomas](../README.md) describe la búsqueda, edición, localización y mantenimiento del sitio. [Admin FAQ in English](../en/faq.md).

Para mantenimiento programado, agregue el comando `php update.php --scheduled` al programador de hosting con un período de 15 minutos. En la sección Mantenimiento, puede habilitar la reindexación de forma independiente y establecer el intervalo de verificación para las actualizaciones firmadas de GitHub.

## Lanzamiento local

Vaya al directorio `php-site` e inicie el servidor PHP integrado.

```sh
php -S 127.0.0.1:8000 router.php
```

El sitio se abrirá en la dirección `http://127.0.0.1:8000/`, el panel de administración está ubicado en `http://127.0.0.1:8000/admin/`.

## Instalación y primer inicio de sesión

En su servidor local, abra `/admin/` y cree una cuenta. En el segundo paso, configure el nombre del sitio, la meta descripción, el texto del pie de página, la paleta y el modo del tema. El logotipo se puede descargar inmediatamente si tiene PHP GD. El asistente creará un favicon a partir de él. Haga clic en "Guardar y abrir administrador" o continúe con la configuración actual. Si sale del asistente, continuará la próxima vez que inicie sesión.

En el alojamiento, ejecute `php admin/setup.php` desde el directorio del proyecto e ingrese el código de un solo uso en el primer paso del asistente. El código se canjea cuando creas una cuenta. Ejecutar el comando nuevamente antes de la instalación reemplaza el código anterior. La instalación de la actualización conserva la cuenta y la configuración anteriores. Si la versión anterior a la actualización usaba `SITE_ADMIN_PASSWORD_HASH`, el inicio de sesión se guarda con el inicio de sesión `admin` y la contraseña anterior.

Para que la aplicación funcione, necesita sesiones PHP y derechos de escritura para `admin/`, `admin/write-lock.php`, `admin/inbox/`, `data/` y directorios de páginas editables. Para crear páginas y directorios en la raíz, otorgue al proceso PHP acceso de escritura a la raíz del proyecto. Utilice un servidor web con HTTPS, procesando `.php` y `index.php` en la lista de archivos de índice. Deshabilitar listados de directorios. Para HTTPS detrás de un proxy inverso, pase el atributo PHP `HTTPS` a través de la configuración del servidor confiable.

## Páginas y menús

En el panel de administración, la página principal aparece como `/`, el resto de las páginas de las carpetas aparecen como `/contacts/`. Para crear una subpágina, ingrese, por ejemplo, `/Services-1/Repair/`. El panel de administración creará los catálogos y el propio `index.php`. Para un archivo separado, ingrese una dirección como `/Services-1/About_2.php`. `A–Z`, `a–z`, `0–9`, se permiten guiones y guiones bajos en los nombres de archivos y carpetas. Los nombres se ingresan sin espacios. En la raíz, sólo se permite la página principal. Los directorios de servicios que incluyen `data`, `admin`, `templates`, `css` y `js` están reservados.

En la lista de páginas, se pueden ampliar las carpetas. En la casilla de verificación Menú, ocultar, publicar y eliminar se guardan mediante AJAX. Ocultar, publicar y eliminar requieren confirmación. Arrastra el ícono ⠿ para ordenar las páginas adyacentes. La página oculta deja de abrirse y desaparece del menú antes de publicarse.

En el editor de páginas, abra el bloque "Mover página" y especifique la carpeta de destino. Por ejemplo, si transfiere `/contacts/` a `/company/`, la nueva dirección será `/company/contacts/`. Las páginas anidadas permanecen en las mismas direcciones. Actualice sus propios enlaces y códigos `[include]` que conducen a la página migrada.

Arrastra el control ⠿ para ordenar páginas del mismo nivel. Mueve una página desde sus ajustes. El icono de la carpeta abre el selector de sección superior. Elige otra sección o la raíz y pulsa Mover carpeta. Las páginas y archivos se mueven con ella. Revisa los enlaces antiguos y los códigos [include].

El contenido se puede editar en un editor de texto y agregar a través de FTP o SFTP. El archivo debe estar en UTF-8 sin BOM. Guarde el bloque de servicio PHP hasta la línea `__halt_compiler();`. En el archivo de la nueva página, inserte este bloque del ejemplo a la profundidad deseada y verifique la ruta relativa a la raíz `index.php`. Luego vienen los encabezados, una línea vacía y un fragmento HTML de la página.

```php
<?php
$pageFile = __FILE__;
require __DIR__ . '/../index.php';
__halt_compiler();
Title: Контакты
Heading: Как с нами связаться
Description: Контактная информация
Menu: Контакты
MenuVisible: 1
Order: 20

<p>Укажите здесь контактную информацию.</p>
```

Se requiere `Title`. El `Heading` vacío se toma de `Title`. `Description` completa la meta descripción de la página. `MenuVisible: 1` muestra el enlace en el menú, `MenuVisible: 0` lo oculta. Para la carpeta `index.php`, al desmarcar la casilla de verificación también se ocultan todas las subpáginas del menú. Si falta `MenuVisible`, la casilla de verificación hereda la regla anterior y se considera habilitada cuando se completa `Menu`. Cuando la casilla de verificación está habilitada, el campo vacío `Menu` se reemplaza por el encabezado `Title`. `Order` especifica la secuencia dentro de un nivel y el valor predeterminado es 100.

La pestaña CSS y JS almacena CSS y JavaScript comunes para páginas públicas. El editor de páginas tiene un bloque separado "Páginas CSS y JavaScript". El código de la página se carga después del general. Especifique sólo el contenido de los archivos, sin las etiquetas `<style>` y `<script>`. El tamaño permitido de cada campo es de hasta 64 KiB. Para vincular imágenes desde CSS, puede utilizar rutas desde la raíz del sitio. La pestaña Ayuda contiene ejemplos de plantillas y componentes disponibles.

En la sección "Plantillas de diseño" puede ver las fuentes de `header.php`, `footer.php`, `menu.php` y `mobile-menu.php` y configurar HTML común para cada uno. Los "encabezados de servicio" insertan metaetiquetas, códigos de contador y scripts comunes en `<head>` o antes de `</body>`. En cada página completa publicada, `rel="canonical"` se forma a partir de su dirección habitual sin parámetros de consulta.

## Configuraciones, subplantillas y dominios

En la sección "Configuración", configura el nombre del sitio, la meta descripción predeterminada, el texto del pie de página y las variables. En el texto del pie de página, `[year]` muestra automáticamente el año actual. Las variables se escriben una por línea, por ejemplo `[city] = Москва` y `[spb] = Санкт-Петербург`. Para mostrar un valor en el título del sitio, contenido de la página, encabezado o pie de página, inserte `[city]`. En plantillas PHP puedes usar `site_escape($settings['variables']['city'] ?? '')`.

Las sustituciones del sistema incluyen `[year]`, `[month]`, `[day]`, `[time]` (horas y minutos), `[dmY]` y `[date]` (fecha DD.MM.AAAA), `[datetime]` (fecha y hora). Se utiliza la zona horaria PHP.

En “Básico → Logotipos e íconos del sitio” puede cargar PNG, JPEG, GIF o WebP. Las descargas son manejadas por PHP GD. El botón Crear favicon crea un PNG transparente de 64 × 64 a partir del logotipo seleccionado o guardado. Las imágenes se almacenan en `templates/images/branding/` y `templates/icons/branding/`.

Las "Subplantillas" almacenan el nombre, la descripción y el significado de cada código. Para el teléfono, cree `[tel]`. Para establecer un título de pestaña a través de una subplantilla, cree `[title]` y especifíquelo en el campo `Title` de la página. Después del título de la pestaña, se muestra el nombre del sitio. Las sustituciones funcionan en el texto de la página y en los metadatos de texto. Para el enlace, inserte la etiqueta `<a>` completa como valor de subplantilla y coloque el código en el texto entre las etiquetas. En los metadatos, HTML se convierte de valor a texto.

Agregue subdominios en la sección "Dominios". En el panel de administración del subdominio del mismo nombre, seleccione "Dominio actual" o abra el dominio deseado de la lista general. El significado local tiene prioridad sobre el significado general. La página pública selecciona valores por dominio en su dirección y los enlaces internos la guardan. Para conectar un subdominio, configure el DNS y el servidor web en su hosting para que el dominio sirva al mismo directorio del proyecto.

El botón "Plantillas" al lado del dominio abre el editor `[header]`, `[mobile-menu]`, `[menu]` y `[footer]`. Para cada bloque, puedes configurar tu propio HTML o utilizar la versión general. Las plantillas generales se configuran en el mismo apartado.

## Componentes en HTML

| Código | Resultado |
| --- | --- |
| `[gal]/images/myfotos/[/gal]` | Carrusel de fotos con desplazamiento suave, flechas y deslizamiento |
| `[fotos]/images/myfotos/[/fotos]` | Galería de fotos con enlaces a originales. |
| `[files]/files/myfiles[/files]` | Lista de archivos con iconos por extensión |
| `[include]/contacts/[/include]` | HTML de otra página con sus subplantillas y componentes |
| `[form1]` | Formulario configurado en la sección Formularios |
| `[callback=Вызвать мастера]` | Botón y formulario de solicitud de devolución de llamada |
| `[iblock=table]/data/myiblock.txt[iblock]` | Tabla de bloques de información |
| `[iblock=phones]/data/myiblock.txt[iblock]` | Lista de teléfonos del bloque de información |
| `[phones]`, `[contacts]` | Teléfonos o todo el bloque de contactos configurado |
| `[soc-icons]` | Iconos de todas las redes sociales con enlaces personalizados. |
| `[tg]`, `[vk]`, `[whatsapp]`, `[youtube]`, `[instagram]`, `[max]` | Enlace de red social con icono y firma. |

Coloque fotos en `templates/images/myfotos/` o `images/myfotos/`. La lista de archivos muestra `zip`, `pdf`, `xls`, `xlsx`, `doc`, `docx`, `png`, `gif`, `jpg`, `jpeg`, `mp4`, `mov` y `mp3`. Para `[include]`, se admiten hasta diez niveles de anidamiento. Se omite la repetición de página en el hilo actual. Coloque los códigos de los componentes en el cuerpo de la página, en una línea separada entre los bloques HTML.

En “Configuración → Contactos y redes sociales” establezca enlaces, números, firmas y título del bloque. En “Configuración → Tema y colores”, están disponibles el sistema, los modos oscuro y claro y las paletas individuales. En “Configuración → Componentes” configuras los parámetros generales del carrusel y la galería de fotos. Debajo de la configuración hay ejemplos de inserción con valores seleccionados. Los parámetros en el código de la página anulan los valores comunes.

En Tema y colores, puede guardar una copia de la configuración seleccionada con su propio nombre o dejar el nombre automático. “Aplicar” cambia el diseño actual. Usar predeterminado aplica el tema guardado y lo recuerda para el comando Restablecer tema a predeterminado. Al eliminarlo de la lista se conserva el diseño actual del sitio.

```text
[gal arrows=off numbers=off effect=fade fit=cover]/images/myfotos/[/gal]
[fotos columns=4 ratio=1x1 fit=cover captions=on links=off]/images/myfotos/[/fotos]
[files icons=on size=on order=desc download=on]/files/myfiles/[/files]
```

Para `[fotos]`, el número de columnas se puede configurar manualmente para una pantalla ancha o dejarse seleccionado automáticamente. En pantallas estrechas, la cuadrícula reduce el número de columnas. Las relaciones de aspecto disponibles son 4:3, 1:1, 16:9 y original, modos `cover` y `contain`, firmas de nombres de archivos y enlaces a originales.

## Formularios y antispam

En la sección “Formularios” puedes editar `[form1]`, crear `[form2]` y los siguientes formularios. Para el formulario, se especifican la dirección del destinatario, la firma del botón y los campos. El campo puede tener una longitud máxima. Un valor vacío utiliza el límite predeterminado para el tipo de campo. Cuando ingresa `89031234567`, el campo del teléfono lo formatea como `+7(903)123-45-67`. El servidor verifica la longitud, el tipo, las opciones válidas y limita el tamaño total de la solicitud. Los campos de entrada gratuitos aceptan texto sin formato. El HTML de los mensajes se escapa cuando se generan y las etiquetas HTML de los mensajes nuevos se rechazan.

La casilla de verificación "Usar CAPTCHA" habilita Google reCAPTCHA v2 con la marca "No soy un robot" para el formulario seleccionado. Ingrese la clave del sitio y la clave secreta del dominio. El secreto se almacena en un archivo de configuración protegido y se utiliza únicamente para la verificación del servidor. [Documentación de verificación del servidor](https://developers.google.com/recaptcha/docs/verify).

Los formularios se envían a través de AJAX. Las solicitudes se guardan en la sección "Mensajes". Si se especifica un destinatario, `mail()` se utiliza para las notificaciones, lo que requiere correo configurado en el hosting. En la sección Antispam puedes editar listas de palabras, expresiones, direcciones IPv4 e IPv6. Los enlaces están bloqueados en el servidor. La dirección del remitente se toma de `REMOTE_ADDR`. Detrás del proxy inverso, configure la recuperación de la IP original en el servidor web para servidores proxy confiables.

Cada formulario tiene una "Plantilla de correo" con los campos "Para", "Cc (Cc)", "Asunto del correo electrónico" y "Cuerpo del correo electrónico". Las direcciones se indican separadas por comas. Se envían copias si se especifica al menos un destinatario principal. Las claves de campo se insertan en el asunto y el texto entre llaves, por ejemplo `Привет {name}!`. Un valor de campo vacío se sustituye por una cadena vacía, la clave desconocida permanece en la letra. Si el asunto o el texto está vacío, se utiliza la parte correspondiente de la carta estándar.

## Solicitudes de llamada

Inserte `[callback]` o `[callback=Вызвать мастера]` en la página HTML. El botón abrirá un formulario con un número de teléfono. Las solicitudes se guardan en la sección "Devoluciones de llamada". Allí también se configuran las firmas de los botones y el modo de transferencia de la solicitud a telefonía. De forma predeterminada, se selecciona el proveedor "Ninguno" y las solicitudes solo se guardan. La telefonía se puede habilitar para envío manual desde la lista o para envío inmediatamente después de recibir el número.

- **Zadarma.** Establece el número, SIP o extensión del operador en el panel de administración y transfiere las claves a PHP a través de `SITE_ZADARMA_KEY` y `SITE_ZADARMA_SECRET`. [Documentación de la API de Zadarma](https://zadarma.com/en/support/api/)
- **HTTP API.** Especifique `SITE_CALLBACK_WEBHOOK_URL` con la dirección HTTPS y, opcionalmente, `SITE_CALLBACK_WEBHOOK_TOKEN`. El sitio envía un POST con los campos JSON `id`, `phone`, `host`, `page`, `requested_at` y el encabezado `Idempotency-Key`. El token se transfiere como portador. El código de respuesta 2xx transfiere la solicitud al estado "Aceptada por telefonía".

Otorgue permiso de escritura al proceso PHP para `admin/callbacks/`. Las aplicaciones utilizan configuraciones antispam. La repetición del mismo número está limitada a un minuto; Se aceptan hasta tres solicitudes cada 15 minutos vía IP.

## Bloques de información y carpeta de datos cerrada.

En la sección "Bloques de información" puede crear conjuntos de datos, editar y arrastrar filas. `A–Z`, `a–z`, `0–9`, se permiten guiones y guiones bajos en el nombre del bloque de información y las claves de columna. Los nombres y claves se ingresan sin espacios. Los nombres de las columnas y los valores de las celdas se pueden escribir en ruso. Los controles corresponden a los tipos de columnas y el guardado se realiza mediante AJAX. El botón ☰ “Editar como texto” cambia todo el bloque de información a CSV con un delimitador `|` y un botón de copiar. Las primeras tres líneas contienen las claves, los nombres y los tipos de las columnas. Luego están las líneas de datos. La fecha en CSV es `ГГГГ-ММ-ДД`, sí/no se almacena como `1` o `0`.

```text
name|phone|is_open
Название|Телефон|Открыто
text|tel|bool
Справочная|+7(903)123-45-67|1
```

La plantilla `phones` utiliza columnas de tipo `tel` y una columna de texto con un título. Puede colocar su plantilla en `templates/local/iblock/имя.php`, donde están disponibles `$block['columns']` y `$block['rows']`.

La ruta `/data/myiblock.txt` dentro del código corresponde al archivo `data/myiblock.txt.php`. Este es texto UTF-8 con un código auxiliar PHP que devuelve 404 cuando se llama directamente. `router.php` cierra toda la carpeta cuando se ejecuta localmente. `data/` tiene reglas para Apache e IIS. Para Apache, habilite `data/.htaccess`. En Nginx, configure la carpeta a cerrar en la configuración del sitio, teniendo en cuenta el prefijo al instalar en un subdirectorio.

```nginx
location = /data { return 404; }
location ^~ /data/ { return 404; }
```

Después de la instalación, verifique que una solicitud directa a `/data/` y el archivo infoblock reciba un 403 o 404.

## Cargando fragmentos HTML

La dirección habitual `/contacts/` abre la página completa. La solicitud `?ajax=1` devuelve `<main>` con H1 y contenido procesado si hay una clave de sesión en el encabezado `X-Site-Fragment`. Los fragmentos están vinculados al origen actual. Las páginas y fragmentos con claves se entregan sin almacenamiento en caché.

En JavaScript en la página de un sitio, utilice la función incorporada.

```js
const html = await window.siteFetchContent('/contacts/');
document.getElementById('content').outerHTML = html;
```

Al instalar en un subdirectorio, pase la URL con el prefijo del sitio. El guión y los estilos se toman de la página que inserta el fragmento. Las páginas públicas siguen siendo accesibles en direcciones habituales.

## Plantillas y recursos

En “Configuración → Bibliotecas y fuentes”, jQuery, Bootstrap, Font Awesome, Material UI, Alpine.js, htmx, Chart.js y GLightbox se habilitan por separado. Font Awesome se almacena localmente. El navegador descarga las bibliotecas restantes desde la CDN cuando está habilitada. La interfaz de usuario del material está disponible en los scripts `type="module"` a `import { Button } from '@mui/material'`. Para iconos, es adecuado `[fa-icon=house]` o el marcado habitual `<i class="fa-solid fa-house"></i>`.

Están disponibles 10 fuentes de Google con alfabeto cirílico. La selección reemplaza la fuente del sistema del sitio. El valor "Sistema" devuelve el diseño anterior. En “Configuración → Tema y colores → Temas guardados” hay cinco paletas listas para usar con opciones claras y oscuras.

| Camino | Objetivo |
| --- | --- |
| `index.php` | Página de inicio y punto de entrada general |
| `contacts/index.php` | Ejemplo de una página en un directorio separado |
| `admin/` | Iniciar sesión y editar páginas |
| `app/site.php` | Lectura de páginas, creación de menús y títulos de sitios web en `SITE_NAME` |
| `templates/layout.php` | Diseño general, título de pestaña, metadatos y área de contenido |
| `templates/header.php`, `menu.php`, `footer.php` | Encabezado, menú y pie de página |
| `templates/css/`, `js/`, `icons/`, `images/` | Estilos, guiones, iconos e imágenes. |
| `templates/components/`, `templates/iblock/` | Plantillas de marcado de componentes y bloques de información |
| `data/` | Archivos de bloques de información protegidos del acceso HTTP directo |

El motor ensambla una página completa de `[header]`, `[mobile-menu]`, `[menu]` y `[footer]`. Estos son la barra superior, el menú móvil, el menú derecho y el pie de página. Se pueden redefinir mediante subplantillas del mismo nombre en el panel de administración, incluidas opciones separadas para subdominios. Almacene las anulaciones de plantillas PHP en `templates/local/` con la misma ruta relativa, por ejemplo `templates/local/footer.php`.

## Actualización del motor

Se necesita el archivo completo para una nueva instalación. Para un sitio activo, utilice `php-site-update.zip`. Solo contiene el motor y las plantillas estándar. Las páginas guardadas, bloques de información, configuraciones, contraseñas, mensajes y extensiones de usuario no se incluyen en el archivo.

Haga clic en "Actualizar motor" en "Configuración → Actualizaciones" o ejecute la misma verificación desde el programador de alojamiento.

```sh
php update.php --github
```

La fuente predeterminada es `bandaletov72/php-site-updates`. Para su propio repositorio, configúrelo en `SITE_GITHUB_REPO`. Un repositorio público no requiere un token. Para un repositorio privado, pase la variable `SITE_GITHUB_TOKEN` al proceso PHP con acceso de lectura a los contenidos. El servidor requiere las extensiones PHP Zip y OpenSSL y la capacidad de realizar solicitudes HTTPS a GitHub.

Para la instalación manual, utilice ZIP.

```sh
php update.php /path/to/php-site-update.zip
```

En la versión anterior sin `update.php`, primero descomprima el paquete encima del proyecto, conservando el `.htaccess` existente y los archivos de `maintenance/`, si los hubiera. Si editó plantillas estándar, transfiera las ediciones a `templates/local/` antes de actualizar. El instalador verifica la lista y las sumas de verificación de los archivos y, para GitHub, también verifica la firma con la clave pública confiable `maintenance/release-public.pem`.

Para la próxima versión, cambie la versión a `app/version.php` y cree el paquete mediante `php update.php --build php-site-update.zip`. Firme el ZIP con su clave privada RSA con SHA‑256 y almacene la clave privada por separado del sitio. Luego envíe el ZIP y `.sig` a GitHub Release con la etiqueta `vX.Y.Z` o `updates/vX.Y.Z/` y actualice `updates/latest.json` a `{"version":"X.Y.Z"}`.

## Extensiones

Se pueden agregar módulos de motor a `app/extensions/` junto con las actualizaciones. Guarde sus propios módulos en `extensions/`, fuera del paquete de actualización. El archivo PHP devuelve una serie de definiciones. Un solo componente tiene `kind` igual a `single` y un componente con un argumento es igual a `wrapped`. El motor reconoce nuevos códigos en páginas existentes. Un componente puede declarar un menú de opciones en `settings`, configurando cada opción en `label`, `default` y la matriz `options`. Los valores se reciben en `$context['settings']`.

```php
<?php
if (!defined('SITE_ROOT')) {
    http_response_code(404);
    exit;
}

return ['notice' => [
    'kind' => 'wrapped',
    'render' => static function (string $text, array $context): string {
        return '<strong>' . site_escape($text) . '</strong>';
    },
]];
```

El componente de ejemplo se inserta como `[notice]Важное сообщение[/notice]`.

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

## Menú móvil · 1.6.7

- Mostrar en el menú principal
- Mostrar en el menú móvil
- Ocultar en el menú móvil

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
