# English · Eurowebcart CMS Light

- [Management](en/readme.md)
- [Admin FAQ](en/faq.md)
- [Templates and components](en/templates.md)

## Service files, maps and templates · 1.6.5

Open Maintenance → Service files to edit .htaccess, robots.txt and sitemap.xml. Sitemap generation uses published pages with Include in the sitemap checked. The previous .htaccess version is available.

Set the latitude and longitude on a page. [map] shows its location, and [maps] collects all published pages with coordinates. Both tags accept center and zoom. Interactive maps use the provider selected in Integrations.

[main-menu] is the menu below the header, with dropdowns for nested pages. Add a custom template and choose its mobile and desktop visibility under Layout templates. In Snippets, the add-domain button opens the domain form. Use [privacy] for the policy link.

In Integrations enter the IDs for JivoSite, Microsoft Clarity and Umami Cloud, the Botfaqtor script, or a Slack incoming webhook. Setup links are next to the fields. In Zadarma, check the balance or test an SMS or call to an international number. Tests can incur charges. For [payment], enter your YooMoney, PayPal and SBP payment links. Check payment status in the provider dashboard.

The HTML and visual editors have icon buttons for formatting, H1–H5 headings and lists. Place the cursor and choose a template, component, image or icon. Forms are listed by shortcode and components have their own options. Font Awesome insertion is available when the library is enabled.

Click a folder name to manage the section in the right panel. Choose a parent section, set desktop and mobile menu options, or enter a redirect URL. An empty section page lists its children. Under Maintenance, add rules such as /contact.html => /contacts/ and edit error pages in /error/. Mobile search and language use header icons.

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


## Admin header buttons · 1.6.4

The globe opens the site and the arrow signs you out. On mobile, these buttons show icons only. Hover or focus with the keyboard to see their labels.

Content, Forms and Requests, and Site and Services open directly. The selected section shows its own tabs under the main menu.

Insert [bread-crumbs] or [search] in the HTML of the home page (index.php) or a layout template. Each block appears once at its chosen position. The breadcrumb and search settings still apply. [privacy] links to /privacy/. Edit the policy under Pages.

```text
[bread-crumbs]
[search]
[privacy]
```


## New in version 1.6.3

### Home and privacy policy

Open “Design Templates” to edit the home page and /privacy.php page. The code [page-content] displays the contents of the main page. In the policy [site-name] and [site-url] take the name and public address from “Settings → General”. Complete the initial text with information about your site and data processing.

### Cookie and return to top

In the footer, [conf-policy] provides a link to the policy, [cookies] opens the settings, and [top] scrolls to the top of the page. Analytics and built-in CMS maps await agreement. Scripts manually added to templates or service headers require their own configuration.

### Editor functions.php

Open “Design Templates → Functions” after the “Service Headers” tab. Add closures to the returned array. In your PHP template, call `translit("Москва")` or `ew_call("translit", "München")`. In HTML insert [func=translit value="Москва и München"]. The example produces `Moskva i Muenchen`.

### Editing standard templates

In the “Design Templates” field already contains the PHP and HTML source code with highlighting. When editing a standard template, select “Custom PHP”. Saving creates a custom version. Using the buttons next to the template, you can undo the last edit or restore the original.

### PHP check before saving

Design template, module, and functions.php editors check the syntax with the `php -l` command. If there is an error, the previous version remains. To select the PHP CLI on the server, use the `SITE_PHP_LINT_BINARY` variable.

### Backlight and compact search

CodeMirror is enabled by default on new installations. The setting is located in “Libraries and Fonts”. In compact search, the field expands to the left, the cross clears the line, and the Enter key starts the search.

### Admin menu and edit panel

The menu under the admin panel header and the editing panel on the site show icons with captions. Icons remain on the narrow screen. Tooltips open on hover and keyboard focus on a narrow screen. “Hide” is located next to the rest of the page actions. The Edit Mode switch is located on the right.

### Description and code of the PHP module

In "PHP Modules" the short description is for plain text. Paste the body of the function into a separate field “Source code · PHP”.

## New in version 1.6.2

### Language of admin and help

In regional settings, enable the desired languages. The language selection menu in the header changes the interface and help, while maintaining the current section. Page texts and the values ​​of your settings are stored in their original form.

### Basement decoration

The code [author] displays the signature Eurowebcart CMS Light, the author name Бандалетов Павел, and a link to GitHub. To choose where to place your signature in your custom HTML footer, paste [author] into the appropriate line.

### Backlight in edit mode

Hover over the Edit Component link or set keyboard focus to it. The frame will highlight the output of this particular insert.

### Where to change search

In “Settings → General” select the search type. In “Maintenance” you can reindex pages, and the field in the admin header searches by settings.

### Results and suggestions

Search shows 20 numbered results per page. Compact search expands to the left of the magnifying glass, with an Enter icon inside the field. Admin search suggests direct links to settings.

## New in version 1.6.1

### Search

In Settings → General choose Off, Compact with a magnifying glass, or Expanded. The admin header also searches setting names. Admin edits refresh the index automatically; scheduled indexing picks up FTP changes. Text, included pages and data blocks are indexed. The indexer skips arbitrary PHP.

### PHP modules

Expand a module to see its settings and the pages that use it. Create a module with an ID, a file name and a PHP function body. It returns the string inserted by [php]id[/php]. You can rename the file without changing the insertion code. Enable CodeMirror under Libraries for syntax highlighting.

### Templates and translations

Undo up to eight changes per template or restore the original PHP template. History is separate for each domain and the shared templates. Choose Custom HTML to edit in the browser or put a PHP override in templates/local/. The language switcher stays visible while creating a translation. Saved translations move with the page.

### Integrations and maintenance

CAPTCHA, maps, telephony and analytics link to provider consoles or instructions. Enter a callback webhook URL and token in Integrations. A registered subdomain’s snippets follow the visitor to the main domain. Schedule php update.php --scheduled every 15 minutes on the host.

## New in version 1.6.0

### Search and cache

Rebuild the text index in Maintenance after publishing or editing pages in the admin area or over FTP. Suggestions use public pages in the visitor’s language. Parsed-page caching is off by default and can be enabled or cleared in Maintenance. Session-based forms and dynamic blocks run on every request.

### Editing on the site

After signing in, use the toolbar above the header. The switch at the right applies across the site and enables editing links next to inserted components. The page CSS and JS editors open in a lightbox. The admin page editor also offers HTML and visual modes, shortcode insertion, and AJAX bulk uploads and sorting for galleries and file lists.

### Components and navigation

Components without options inherit global settings. Inline options override individual values. Use width=100, 50, 33 or 25, with or without a percent sign. On phones, blocks stack in one column. Set numbers=off to hide the slide count. Swipes and arrows still work. The mobile menu opens from the right; turn breadcrumbs off under Settings → General.

```text
[gal width=50 numbers=off arrows=on fit=cover]/images/myfotos/[/gal]
[fotos width=50% columns=3 captions=on]/images/myfotos/[/fotos]
[files width=33 icons=off size=on download=on]/files/myfiles/[/files]
```

### Languages, appearance and prices

Choose admin and website languages, currency, number separators, date and time formats and timezone under Regional settings. Place index_en.php next to index.php to translate a page; the original content is available when a translation is missing. The Price column uses the chosen currency. Review saved theme colors, preview the theme mode immediately and upload separate light and dark logos. Try Roboto Condensed with a custom preview phrase.

```text
/contacts/index.php → /contacts/index_en.php
```

### Forms, requests and integrations

Settings → CAPTCHA configures shared Google reCAPTCHA v2 keys and can require CAPTCHA for all standard forms. Individual settings remain under Forms. Messages and callbacks list unread items first, 20 per page. Integrations includes Zadarma, Telegram, analytics and Google or Yandex maps. Map points share one map, with optional clustering. The library lightbox tests the active version and shows insertion code. Enable CodeMirror to select Auto, HTML, CSS, JS, PHP or RAW.

```text
[map lat=55.7558 lng=37.6173 title="Office" zoom=13 provider=yandex url="/contacts/" button="Open"]
```

```text
[callback button="Call me" title="Callback" submit="Send" placeholder="+7(900)123-45-67"]
```

### PHP modules

Use PHP modules to edit a registered module’s label, description and availability. Insert its shortcode in the page editor. The included server-date module uses the regional date format. Developers install executable modules using file access in a protected directory.

```text
[php]server-date[/php]
```
