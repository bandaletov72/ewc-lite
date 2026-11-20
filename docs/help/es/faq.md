# Preguntas frecuentes del administrador

## Instalación e inicio de sesión

### ¿Cómo iniciar un sitio web y abrir el panel de administración?

Para iniciar localmente, ejecute el comando php -S 127.0.0.1:8000 router.php en el directorio del proyecto y abra http://127.0.0.1:8000/admin/.. En el hosting, primero ejecute php admin/setup.php, luego ingrese el código de un solo uso recibido en el primer paso del asistente en /admin/..

### ¿Qué puedes configurar cuando empiezas por primera vez?

Primero cree un nombre de usuario y contraseña. En el segundo paso, configure el nombre del sitio, la meta descripción predeterminada, el texto del pie de página, la paleta y el modo del tema. Si tiene PHP GD, puede descargar inmediatamente el logotipo y crear un favicon a partir de él. Puede omitir el segundo paso o continuar la próxima vez que inicie sesión.

## Páginas y menús

### ¿Cómo crear una página y qué dirección ingresar?

Vaya a Páginas → Nueva página. Ingrese la dirección, por ejemplo /Servicios-1/Reparación/ o /Servicios-1/Price_2.php, complete el título y guarde. Para la dirección de la carpeta, el propio motor creará directorios y index.php. Se permiten A-Z, a-z, 0-9, guión y guión bajo en los nombres. La página principal está reservada en la raíz y se cierra la creación de páginas en directorios de servicios.

### ¿Cuál es la diferencia entre la casilla de verificación "En menú" y el botón "Ocultar"?

Al desmarcar la casilla "En el menú", se elimina el enlace de los menús habituales, manteniendo el acceso a la dirección directa. En una página con una dirección de carpeta, la casilla de verificación también controla los elementos de las subpáginas. El botón “Ocultar” elimina el enlace y cierra la dirección pública de la página hasta que se vuelva a publicar.

### ¿Cómo puedo cambiar el orden de las páginas o mover una carpeta completa?

Arrastra el control ⠿ para ordenar páginas del mismo nivel. Mueve una página desde sus ajustes. El icono de la carpeta abre el selector de sección superior. Elige otra sección o la raíz y pulsa Mover carpeta. Las páginas y archivos se mueven con ella. Revisa los enlaces antiguos y los códigos [include].

### ¿Por qué los cambios de página no son visibles inmediatamente?

Haga clic en "Guardar" en el editor. Luego utilice el enlace "Abrir página" o "Ir". Si el enlace ha desaparecido, comprueba si la página está publicada. Para cambios en un subdominio, verifique el dominio en la barra de direcciones.

## Diseño y subdominios

### ¿Dónde puedo cambiar el título, pie de página, logotipo y favicon?

Abra Configuración → General. El campo Texto de pie de página admite [year] para el año actual. En el bloque "Logotipos e íconos del sitio", puede cargar imágenes o hacer clic en "Crear un favicon a partir de un logotipo". La carga y conversión requieren PHP GD.

### ¿Cómo cambiar el tema o fuente y luego volver al anterior?

En Configuración → Tema y colores, seleccione un modo y una paleta. Guarde la paleta actual en la lista para poder usarla más tarde. El botón "Usar como predeterminado" recuerda la paleta del comando "Volver el tema al valor predeterminado". La fuente se puede cambiar en la sección "Bibliotecas y fuentes". Los nombres y ejemplos de la lista se muestran en las fuentes apropiadas. El valor "Sistema" devuelve la fuente normal.

### ¿Dónde cambiar el encabezado y dónde insertar un contador, CSS o JavaScript?

El encabezado, el menú móvil, el menú y el pie de página de los bloques comunes se editan en la pestaña "Plantillas de diseño". En el mismo lugar, los "Encabezados de servicio" insertan HTML en el encabezado o antes de la etiqueta del cuerpo de cierre. Para CSS y JavaScript normales, hay una pestaña separada "CSS y JS". El código de una página se agrega en su editor.

### ¿Cómo configurar diferentes números de teléfono y encabezados para subdominios?

Agregue un subdominio en la sección "Dominios". Luego, establezca un valor de subplantilla separado en la sección Subplantillas seleccionando el dominio que desee. Cambie el encabezado y otros bloques usando el botón "Plantillas" al lado del dominio. La versión del dominio tiene prioridad sobre la versión general. El DNS y el servidor web deben apuntar el subdominio al directorio de ese sitio.

### ¿Dónde puedo cambiar los enlaces a las redes sociales y los títulos junto a ellas?

Abra “Configuración → Contactos y redes sociales”. Complete direcciones, firmas y números de teléfono. El código [soc-icons] muestra iconos de todas las redes configuradas. Para un enlace único, utilice, por ejemplo, [tg] o [vk].

## Formularios y aplicaciones

### ¿Cómo agregar un formulario a una página?

Abrir Formularios, configurar campos, obligatorios, longitud máxima y texto del botón. Guarde el formulario y pegue su código, por ejemplo [form1], en el HTML de la página.

### ¿Dónde puedo ver las solicitudes si no he recibido una notificación por correo electrónico?

Abra Mensajes. Las solicitudes se guardan allí después de enviar correctamente el formulario. Para notificaciones, verifique las direcciones en el campo "Para" en la plantilla de correo del formulario y la funcionalidad de la función mail() en el hosting.

### ¿Cómo configurar una carta y enviar copias?

Abra el formulario deseado y busque el bloque "Plantilla de correo". Ingrese las direcciones en los campos "Para" y "Copias (Cc)", separadas por comas. En el asunto y el cuerpo, utilice claves de campo entre llaves, por ejemplo ¡Hola {name}! Los botones debajo del texto insertan la variable seleccionada. El asunto o el texto en blanco utiliza la notificación estándar.

### ¿Por qué el formulario rechaza el mensaje o pregunta "No soy un robot"?

Verifique los campos obligatorios, la longitud, el teléfono y el formato del correo electrónico. Los campos de entrada gratuitos utilizan texto sin formato sin HTML ni direcciones de sitios web. Las listas de palabras e IP prohibidas se encuentran en la sección Antispam. La casilla de verificación "Usar CAPTCHA" requiere una clave de sitio y una clave secreta de Google reCAPTCHA v2 para el dominio deseado.

### ¿Dónde puedo ver las solicitudes de devolución de llamadas?

En la sección "Devoluciones de llamada". El componente se agrega a la página con el código [callback] o [callback=Вызвать мастера]. Con el proveedor "No", las aplicaciones se guardan en el panel de administración. Cuando la telefonía está configurada, la transmisión manual o automática está disponible.

## Bloques de información y componentes.

### ¿Cómo crear un bloque de información y mostrarlo en la página?

En la sección “Bloques de información”, haga clic en “Nuevo bloque de información”, configure las columnas y agregue filas. En el nombre del bloque de información y las claves de columna, utilice A–Z, a–z, 0–9, guión o guión bajo sin espacios. Guarde y copie el código de inserción del editor de bloques de información. Para obtener una lista de teléfonos, utilice la plantilla de teléfonos.

### ¿Cómo copiar o reemplazar todas las filas de un bloque de información?

Abra el bloque de información y haga clic en "Editar como texto". CSV utiliza el | delimitador. Las primeras tres líneas contienen las claves, los nombres y los tipos de las columnas. Luego vienen los datos. Encierre un campo con un delimitador, comillas o salto de línea entre comillas dobles y comillas dobles internas.

### ¿Qué comprobar si la galería o lista de archivos está vacía?

Verifique la ruta en el código del componente con la carpeta en el servidor. Por ejemplo, [fotos]/images/myfotos/[/fotos] usa fotos en images/myfotos/ o templates/images/myfotos/.. Para [files], verifique la extensión del archivo. Puede encontrar una lista de extensiones y parámetros compatibles en la ayuda del componente.

### ¿Por qué una solicitud con ?ajax=1 devuelve 403?

El fragmento de página requiere una sesión pública válida y un token en el mismo sitio. Para cargar dentro del sitio, use window.siteFetchContent(), que transfiere el token. Para una transición normal, abra la dirección de la página sin el parámetro ajax.

## Actualizaciones y copia de seguridad

### ¿Cómo actualizar el motor de un sitio instalado?

En Configuración → Actualizaciones, compare la versión instalada con la última versión de GitHub, luego haga clic en Actualizar motor. Si GitHub no está disponible, el panel de administración muestra la versión que se verificó por última vez. Para actualizar manualmente, pase el archivo php-site-update.zip al comando php update.php. El paquete de actualización reemplaza los archivos del motor y las plantillas estándar. Guarde sus plantillas PHP en templates/local/.

### ¿Qué debo copiar antes de actualizar o migrar?

Haga una copia de todo el directorio del sitio junto con los archivos ocultos. Las páginas están ubicadas en la raíz y sus directorios, bloques de información en datos/, configuraciones y archivos de cuenta en admin/.. Guarde también sus plantillas, imágenes cargadas, mensajes y solicitudes. La copia incluye secretos de configuración, así que manténgala fuera de un directorio público.

### ¿Qué comprobar si el panel de administración no puede guardar los cambios?

Primero lea el mensaje al lado del formulario. Si los cambios entran en conflicto, copie las ediciones y abra el editor nuevamente. Si hay un error de escritura, solicite al hosting que verifique los permisos de escritura del proceso PHP en la carpeta y archivo deseado admin/write-lock.php.. Si la actualización falla, verifique el acceso del servidor a GitHub y la disponibilidad de PHP Zip y OpenSSL.

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
