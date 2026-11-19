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

Arrastra el ícono ⠿ para reorganizar las páginas dentro del mismo nivel. Para mover una página, abra "Mover página" en su editor. Para transferir una carpeta junto con sus páginas y archivos adjuntos, haga clic en ↪ junto al nombre de la carpeta. Después de la transferencia, verifique los enlaces del manual y los códigos [include] que apuntaban a las direcciones anteriores.

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

