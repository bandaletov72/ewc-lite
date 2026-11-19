# Español · 1.6.1

## Búsqueda

En General puedes desactivar la búsqueda o elegir lupa o campo ampliado. La cabecera de administración busca ajustes. El índice se actualiza tras editar; para FTP se puede programar. Se indexan páginas incluidas y bloques de datos sin ejecutar módulos PHP.

## Módulos PHP

Despliega un módulo para ver ajustes y páginas que lo usan. Indica identificador, nombre del archivo y cuerpo de función. Al renombrar el archivo se mantiene [php]id[/php]. Activa CodeMirror para resaltar sintaxis.

## Plantillas y traducciones

Deshaz hasta ocho cambios por plantilla o restablece el PHP original. Cada dominio tiene su propio historial. Usa HTML personalizado en la administración y templates/local/ para PHP. Puedes cambiar de idioma al crear una traducción; las traducciones guardadas se trasladan con la página.

## Integraciones y mantenimiento

CAPTCHA, mapas, telefonía y analítica enlazan a la documentación de sus proveedores. Configura URL y token HTTP de la devolución de llamada. Los ajustes de un subdominio registrado se mantienen al pasar al dominio principal. Ejecuta php update.php --scheduled cada 15 minutos.

---

# Español · 1.6.0

## Búsqueda y caché

Reconstruye el índice de texto en Mantenimiento después de publicar o editar páginas desde la administración o por FTP. Las sugerencias usan las páginas públicas en el idioma del visitante. La caché está desactivada de forma predeterminada y se administra allí. Los formularios y bloques dinámicos se procesan en cada solicitud.

## Edición en el sitio

Tras iniciar sesión, la barra superior tiene un interruptor a la derecha para editar componentes en todo el sitio. CSS y JS de la página se editan en un cuadro modal. La administración ofrece modos HTML y visual, inserción de códigos y carga masiva y ordenación de archivos mediante AJAX.

## Componentes y navegación

Sin parámetros se usan los ajustes globales. width admite 100, 50, 33 o 25, con o sin %. Los bloques se apilan en móviles. numbers=off oculta el contador. Desliza para cambiar de foto. El menú móvil sale de la derecha y las migas de pan pueden desactivarse en ajustes generales.

```text
[gal width=50 numbers=off arrows=on fit=cover]/images/myfotos/[/gal]
[fotos width=50% columns=3 captions=on]/images/myfotos/[/fotos]
[files width=33 icons=off size=on download=on]/files/myfiles/[/files]
```

## Idiomas, apariencia y precios

Elige idiomas, moneda, formatos y zona horaria en ajustes regionales. Guarda index_en.php junto a index.php para traducir una página; si falta, se muestra el texto original. El campo Precio usa la moneda seleccionada. Previsualiza las paletas y el modo, sube los logotipos claro y oscuro y prueba Roboto Condensed con tu propia frase.

```text
/contacts/index.php → /contacts/index_en.php
```

## Formularios, solicitudes e integraciones

Configura claves de Google reCAPTCHA v2 globales o por formulario. Mensajes y llamadas muestran 20 entradas y priorizan las no leídas. Integraciones incluye Zadarma, Telegram, analítica y mapas Google o Yandex con agrupación opcional. El cuadro de bibliotecas prueba la versión activa y muestra el código. CodeMirror ofrece Auto, HTML, CSS, JS, PHP y RAW.

```text
[map lat=55.7558 lng=37.6173 title="Office" zoom=13 provider=yandex url="/contacts/" button="Open"]
```

```text
[callback button="Call me" title="Callback" submit="Send" placeholder="+7(900)123-45-67"]
```

## Módulos PHP

En Módulos PHP, edita el nombre, la descripción y la disponibilidad. server-date muestra la fecha con el formato regional. Los desarrolladores instalan los archivos ejecutables en una carpeta protegida.

```text
[php]server-date[/php]
```

