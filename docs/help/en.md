# English · 1.6.1

## Search

In Settings → General choose Off, Compact with a magnifying glass, or Expanded. The admin header also searches setting names. Admin edits refresh the index automatically; scheduled indexing picks up FTP changes. Text, included pages and data blocks are indexed. The indexer skips arbitrary PHP.

## PHP modules

Expand a module to see its settings and the pages that use it. Create a module with an ID, a file name and a PHP function body. It returns the string inserted by [php]id[/php]. You can rename the file without changing the insertion code. Enable CodeMirror under Libraries for syntax highlighting.

## Templates and translations

Undo up to eight changes per template or restore the original PHP template. History is separate for each domain and the shared templates. Choose Custom HTML to edit in the browser or put a PHP override in templates/local/. The language switcher stays visible while creating a translation. Saved translations move with the page.

## Integrations and maintenance

CAPTCHA, maps, telephony and analytics link to provider consoles or instructions. Enter a callback webhook URL and token in Integrations. A registered subdomain’s snippets follow the visitor to the main domain. Schedule php update.php --scheduled every 15 minutes on the host.

---

# English · 1.6.0

## Search and cache

Rebuild the text index in Maintenance after publishing or editing pages in the admin area or over FTP. Suggestions use public pages in the visitor’s language. Parsed-page caching is off by default and can be enabled or cleared in Maintenance. Session-based forms and dynamic blocks run on every request.

## Editing on the site

After signing in, use the toolbar above the header. The switch at the right applies across the site and enables editing links next to inserted components. The page CSS and JS editors open in a lightbox. The admin page editor also offers HTML and visual modes, shortcode insertion, and AJAX bulk uploads and sorting for galleries and file lists.

## Components and navigation

Components without options inherit global settings. Inline options override individual values. Use width=100, 50, 33 or 25, with or without a percent sign. On phones, blocks stack in one column. Set numbers=off to hide the slide count. Swipes and arrows still work. The mobile menu opens from the right; turn breadcrumbs off under Settings → General.

```text
[gal width=50 numbers=off arrows=on fit=cover]/images/myfotos/[/gal]
[fotos width=50% columns=3 captions=on]/images/myfotos/[/fotos]
[files width=33 icons=off size=on download=on]/files/myfiles/[/files]
```

## Languages, appearance and prices

Choose admin and website languages, currency, number separators, date and time formats and timezone under Regional settings. Place index_en.php next to index.php to translate a page; the original content is available when a translation is missing. The Price column uses the chosen currency. Review saved theme colors, preview the theme mode immediately and upload separate light and dark logos. Try Roboto Condensed with a custom preview phrase.

```text
/contacts/index.php → /contacts/index_en.php
```

## Forms, requests and integrations

Settings → CAPTCHA configures shared Google reCAPTCHA v2 keys and can require CAPTCHA for all standard forms. Individual settings remain under Forms. Messages and callbacks list unread items first, 20 per page. Integrations includes Zadarma, Telegram, analytics and Google or Yandex maps. Map points share one map, with optional clustering. The library lightbox tests the active version and shows insertion code. Enable CodeMirror to select Auto, HTML, CSS, JS, PHP or RAW.

```text
[map lat=55.7558 lng=37.6173 title="Office" zoom=13 provider=yandex url="/contacts/" button="Open"]
```

```text
[callback button="Call me" title="Callback" submit="Send" placeholder="+7(900)123-45-67"]
```

## PHP modules

Use PHP modules to edit a registered module’s label, description and availability. Insert its shortcode in the page editor. The included server-date module uses the regional date format. Developers install executable modules using file access in a protected directory.

```text
[php]server-date[/php]
```

