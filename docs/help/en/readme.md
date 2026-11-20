# Eurowebcart CMS Light

Author - Бандалетов Павел. [Original project and updates](https://github.com/bandaletov72/php-site-updates).

The minimum PHP version is 7.2. The site and admin area support PHP 8.6. Use the PHP syntax available on your server when writing custom modules and templates.

[Admin FAQ](faq.md) is also available in the “Help” tab.

[Help for the current version in ten languages](../README.md) describes search, site editing, localization and maintenance. [Admin FAQ in English](../en/faq.md).

For scheduled maintenance, add the command `php update.php --scheduled` to the hosting scheduler with a period of 15 minutes. In the Maintenance section, you can independently enable reindexing and set the check interval for signed GitHub updates.

## Local launch

Go to the `php-site` directory and launch the built-in PHP server.

```sh
php -S 127.0.0.1:8000 router.php
```

The site will open at the address `http://127.0.0.1:8000/`, the admin panel is located at `http://127.0.0.1:8000/admin/`.

## Installation and first login

On your local server, open `/admin/` and create an account. In the second step, set the site name, meta description, footer text, palette and theme mode. The logo can be downloaded immediately if you have PHP GD. The wizard will create a favicon from it. Click "Save and open admin" or continue with current settings. If you exit the wizard, it will continue the next time you log in.

On the hosting, run `php admin/setup.php` from the project directory and enter the one-time code in the first step of the wizard. The code is redeemed when you create an account. Running the command again before installation replaces the previous code. Installing the update retains the previous account and settings. If the version before the update used `SITE_ADMIN_PASSWORD_HASH`, the login is saved with the login `admin` and the previous password.

For the application to work, you need PHP sessions and write rights to `admin/`, `admin/write-lock.php`, `admin/inbox/`, `data/` and directories of editable pages. To create pages and directories at the root, give the PHP process write access to the project root. Use a web server with HTTPS, processing `.php` and `index.php` in the index file list. Disable directory listings. For HTTPS behind a reverse proxy, pass the PHP attribute `HTTPS` through the trusted server configuration.

## Pages and menus

In the admin panel, the main page is listed as `/`, the rest of the pages in the folders are listed as `/contacts/`. To create a subpage, enter, for example, `/Services-1/Repair/`. The admin panel will create the catalogs and `index.php` itself. For a separate file, enter an address like `/Services-1/About_2.php`. `A–Z`, `a–z`, `0–9`, hyphens and underscores are allowed in file and folder names. Names are entered without spaces. At root, only the main page is allowed. Service directories including `data`, `admin`, `templates`, `css` and `js` are reserved.

In the list of pages, folders can be expanded. In Menu checkbox, hide, publish and delete are saved via AJAX. Hiding, publishing and deleting require confirmation. Drag the ⠿ icon to sort adjacent pages. The hidden page stops opening and disappears from the menu before publishing.

In the page editor, open the “Move Page” block and specify the destination folder. For example, if you transfer `/contacts/` to `/company/`, the new address will be `/company/contacts/`. Nested pages remain at the same addresses. Update your own links and codes `[include]` that lead to the migrated page.

Drag the ⠿ handle to reorder pages at the same level. Use Move page in the page settings to move a single page. The icon beside a folder opens the parent section selector. Choose another section or the root, then select Move folder. The folder moves with its pages and files. Check links and [include] codes for old URLs.

Content can be edited in a text editor and added via FTP or SFTP. The file must be in UTF-8 without BOM. Save the PHP service block up to the line `__halt_compiler();`. In the new page file, insert this block from the example at the desired depth and check the relative path to the root `index.php`. Next come the headers, one empty line, and an HTML fragment of the page.

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

`Title` is required. Empty `Heading` is taken from `Title`. `Description` fills in the meta description of the page. `MenuVisible: 1` shows the link in the menu, `MenuVisible: 0` hides it. For the `index.php` folder, unchecking the checkbox also hides all subpages from the menu. If `MenuVisible` is missing, the checkbox inherits the previous rule and is considered enabled when `Menu` is filled. When the checkbox is enabled, the empty field `Menu` is replaced by the header `Title`. `Order` specifies the sequence within one level and defaults to 100.

The CSS and JS tab stores common CSS and JavaScript for public pages. The page editor has a separate block “CSS and JavaScript pages”. The page code is loaded after the general one. Specify only the contents of the files, without the `<style>` and `<script>` tags. The allowed size of each field is up to 64 KiB. To link to images from CSS, you can use paths from the site root. The Help tab contains examples of available templates and components.

In the “Design Templates” section you can view the sources of `header.php`, `footer.php`, `menu.php` and `mobile-menu.php` and set common HTML for each. "Service Headers" insert common meta tags, counter codes, and scripts into `<head>` or before `</body>`. On each published full page, `rel="canonical"` is formed from its regular address without query parameters.

## Settings, subtemplates and domains

In the “Settings” section you set the site name, default meta description, footer text and variables. In the footer text, `[year]` automatically displays the current year. Variables are written one per line, for example `[city] = Москва` and `[spb] = Санкт-Петербург`. To display a value in the site title, page content, header, or footer, insert `[city]`. In PHP templates you can use `site_escape($settings['variables']['city'] ?? '')`.

System substitutions include `[year]`, `[month]`, `[day]`, `[time]` (hours and minutes), `[dmY]` and `[date]` (date DD.MM.YYYY), `[datetime]` (date and time). The PHP time zone is used.

In “Basic → Site logos and icons” you can upload PNG, JPEG, GIF or WebP. Downloads are handled by PHP GD. The create favicon button makes a transparent 64×64 PNG from the selected or saved logo. The images are stored in `templates/images/branding/` and `templates/icons/branding/`.

"Subtemplates" stores the name, description, and meaning of each code. For the phone, create `[tel]`. To set a tab title via a subtemplate, create `[title]` and specify it in the `Title` field on the page. After the tab title, the name of the site is displayed. Substitutions work in page text and text metadata. For the link, insert the entire `<a>` tag as a subtemplate value and place the code in the text between the tags. In metadata, HTML is converted from value to text.

Add subdomains in the “Domains” section. In the admin panel of the subdomain of the same name, select “Current domain”, or open the desired domain from the general list. Local meaning takes precedence over general meaning. The public page selects values ​​by domain in its address, and internal links save it. To connect a subdomain, configure the DNS and web server on your hosting so that the domain serves the same project directory.

The "Templates" button next to the domain opens the `[header]`, `[mobile-menu]`, `[menu]` and `[footer]` editor. For each block, you can set your own HTML or use the general version. General templates are configured in the same section.

## Components in HTML

| Code | Result |
| --- | --- |
| `[gal]/images/myfotos/[/gal]` | Photo carousel with smooth scrolling, arrows and swipe |
| `[fotos]/images/myfotos/[/fotos]` | Photo gallery with links to originals |
| `[files]/files/myfiles[/files]` | List of files with icons by extension |
| `[include]/contacts/[/include]` | HTML of another page with its subtemplates and components |
| `[form1]` | Form configured in the Forms section |
| `[callback=Вызвать мастера]` | Callback request button and form |
| `[iblock=table]/data/myiblock.txt[iblock]` | Information block table |
| `[iblock=phones]/data/myiblock.txt[iblock]` | List of information block telephones |
| `[phones]`, `[contacts]` | Phones or the entire configured contact block |
| `[soc-icons]` | Icons of all social networks with customized links |
| `[tg]`, `[vk]`, `[whatsapp]`, `[youtube]`, `[instagram]`, `[max]` | Social network link with icon and signature |

Place photos in `templates/images/myfotos/` or `images/myfotos/`. The file list shows `zip`, `pdf`, `xls`, `xlsx`, `doc`, `docx`, `png`, `gif`, `jpg`, `jpeg`, `mp4`, `mov` and `mp3`. For `[include]`, up to ten nesting levels are supported. The page repeat in the current thread is skipped. Place component codes in the body of the page, on a separate line between HTML blocks.

In “Settings → Contacts and social networks” set links, numbers, signatures and block title. In “Settings → Theme and Colors”, system, dark and light modes and individual palettes are available. In “Settings → Components” you set the general parameters of the carousel and photo gallery. Under the settings there are examples of inserting with selected values. Parameters in the page code override common values.

In Theme & Colors, you can save a copy of the selected settings under your own name or leave the name automatic. “Apply” changes the current design. Use Default applies the saved theme and remembers it for the Reset Theme to Default command. Removing from the list preserves the current design of the site.

```text
[gal arrows=off numbers=off effect=fade fit=cover]/images/myfotos/[/gal]
[fotos columns=4 ratio=1x1 fit=cover captions=on links=off]/images/myfotos/[/fotos]
[files icons=on size=on order=desc download=on]/files/myfiles/[/files]
```

For `[fotos]`, the number of columns can be set manually for a wide screen or left automatically selected. On narrow screens, the grid reduces the number of columns. Available aspect ratios are 4:3, 1:1, 16:9 and original, `cover` and `contain` modes, signatures from file names and links to originals.

## Forms and antispam

In the “Forms” section you can edit `[form1]`, create `[form2]` and the following forms. For the form, the recipient's address, button signature and fields are specified. The field can have a maximum length. An empty value uses the default limit for the field type. When you enter `89031234567`, the phone field formats it as `+7(903)123-45-67`. The server checks the length, type, valid choices, and limits the total size of the request. Free entry fields accept plain text. HTML in messages is escaped when output, and HTML tags in new messages are rejected.

The "Use CAPTCHA" checkbox enables Google reCAPTCHA v2 with the "I'm not a robot" check for the selected form. Enter the site key and secret key for the domain. The secret is stored in a protected settings file and is used only for server verification. [Server Verification Documentation](https://developers.google.com/recaptcha/docs/verify).

Forms are submitted via AJAX. Applications are saved in the “Messages” section. If a recipient is specified, `mail()` is used for notifications, which requires configured mail on the hosting. In the Antispam section you can edit lists of words, expressions, IPv4 and IPv6 addresses. Links are blocked on the server. The sender's address is taken from `REMOTE_ADDR`. Behind the reverse proxy, configure the recovery of the original IP in the web server for trusted proxies.

Each form has a “Mail Template” with fields “To,” “Cc (Cc),” “Email Subject,” and “Email Body.” Addresses are indicated separated by commas. Copies are sent if at least one primary recipient is specified. Field keys are inserted into the subject and text in curly braces, for example `Привет {name}!`. An empty field value is substituted as an empty string, the unknown key remains in the letter. If the subject or text is empty, the corresponding part of the standard letter is used.

## Callback requests

Insert `[callback]` or `[callback=Вызвать мастера]` into the HTML page. The button will open a form with a phone number. Requests are saved in the “Callbacks” section. The button signatures and the mode for transferring the request to telephony are also set there. By default, the “None” provider is selected and requests are only saved. Telephony can be enabled for manual sending from the list or for sending immediately after receiving the number.

- **Zadarma.** Set the number, SIP or operator extension in the admin panel, and transfer the keys to PHP via `SITE_ZADARMA_KEY` and `SITE_ZADARMA_SECRET`. [Zadarma API documentation](https://zadarma.com/en/support/api/)
- **HTTP API.** Specify `SITE_CALLBACK_WEBHOOK_URL` with the HTTPS address and optionally `SITE_CALLBACK_WEBHOOK_TOKEN`. The site sends a POST with JSON fields `id`, `phone`, `host`, `page`, `requested_at` and header `Idempotency-Key`. The token is transferred as Bearer. The response code 2xx transfers the request to the “Accepted by telephony” status.

Give the PHP process write permission to `admin/callbacks/`. Applications use form antispam settings. Repetition to the same number is limited to one minute; up to three requests per 15 minutes are accepted via IP.

## Information blocks and closed data folder

In the “Infoblocks” section you can create data sets, edit and drag rows. `A–Z`, `a–z`, `0–9`, hyphen and underscore are allowed in the infoblock name and column keys. Names and keys are entered without spaces. Column names and cell values ​​can be written in Russian. The controls correspond to the column types, and saving is done via AJAX. The ☰ “Edit as text” button switches the entire information block to CSV with a delimiter `|` and a copy button. The first three lines contain the keys, names, and types of the columns. Then there are data lines. The date in CSV is `ГГГГ-ММ-ДД`, yes/no is stored as `1` or `0`.

```text
name|phone|is_open
Название|Телефон|Открыто
text|tel|bool
Справочная|+7(903)123-45-67|1
```

The `phones` template uses columns of type `tel` and a text column with a title. You can place your template in `templates/local/iblock/имя.php`, where `$block['columns']` and `$block['rows']` are available.

The path `/data/myiblock.txt` inside the code corresponds to the file `data/myiblock.txt.php`. This is UTF-8 text with a PHP stub that returns 404 when called directly. `router.php` closes the entire folder when running locally. `data/` has rules for Apache and IIS. For Apache, enable `data/.htaccess`. On Nginx, configure the folder to be closed in the site configuration, taking into account the prefix when installing in a subdirectory.

```nginx
location = /data { return 404; }
location ^~ /data/ { return 404; }
```

After installation, check that a direct request to `/data/` and the infoblock file receives a 403 or 404.

## Loading HTML fragments

The usual address `/contacts/` opens the full page. The request `?ajax=1` returns `<main>` with H1 and processed content if there is a session key in the `X-Site-Fragment` header. Fragments are linked to the current origin. Pages and fragments with keys are served without caching.

In JavaScript on a site page, use the built-in function.

```js
const html = await window.siteFetchContent('/contacts/');
document.getElementById('content').outerHTML = html;
```

When installing in a subdirectory, pass the URL with the site prefix. The script and styles are taken from the page that inserts the fragment. Public pages remain accessible at regular addresses.

## Templates and Resources

In “Settings → Libraries and fonts” jQuery, Bootstrap, Font Awesome, Material UI, Alpine.js, htmx, Chart.js and GLightbox are enabled separately. Font Awesome is stored locally. The remaining libraries are downloaded by the browser from the CDN when enabled. Material UI is available in scripts `type="module"` through `import { Button } from '@mui/material'`. For icons, `[fa-icon=house]` or the usual markup `<i class="fa-solid fa-house"></i>` is suitable.

10 Google fonts with Cyrillic alphabet are available. The selection replaces the site's system font. The value "System" returns the previous design. In “Settings → Theme & Colors → Saved Themes” there are five ready-made palettes with light and dark options.

| Path | Purpose |
| --- | --- |
| `index.php` | Home page and general entry point |
| `contacts/index.php` | Example of a page in a separate directory |
| `admin/` | Login and page editor |
| `app/site.php` | Reading pages, building menus and website titles in `SITE_NAME` |
| `templates/layout.php` | General layout, tab title, metadata and content area |
| `templates/header.php`, `menu.php`, `footer.php` | Header, menu and footer |
| `templates/css/`, `js/`, `icons/`, `images/` | Styles, scripts, icons and images |
| `templates/components/`, `templates/iblock/` | Component markup and infoblock templates |
| `data/` | Information block files protected from direct HTTP access |

The engine assembles a complete page from `[header]`, `[mobile-menu]`, `[menu]` and `[footer]`. These are the top bar, mobile menu, right menu and footer. They can be redefined by subtemplates of the same name in the admin panel, including separate options for subdomains. Store PHP template overrides in `templates/local/` with the same relative path, for example `templates/local/footer.php`.

## Engine update

The full archive is needed for a new installation. For a live site, use `php-site-update.zip`. It only contains the engine and standard templates. Saved pages, information blocks, settings, passwords, messages and user extensions are not included in the archive.

Click "Update Engine" in "Settings → Updates" or run the same check from the hosting scheduler.

```sh
php update.php --github
```

The default source is `bandaletov72/php-site-updates`. For your own repository, set it to `SITE_GITHUB_REPO`. A public repository does not require a token. For a private repository, pass the variable `SITE_GITHUB_TOKEN` to the PHP process with read access to the contents. The server requires the Zip and OpenSSL PHP extensions and the ability to make HTTPS requests to GitHub.

For manual installation, use ZIP.

```sh
php update.php /path/to/php-site-update.zip
```

On the old version without `update.php`, first unzip the package on top of the project, keeping the existing `.htaccess` and the files from `maintenance/`, if any. If you edited standard templates, transfer the edits to `templates/local/` before updating. The installer verifies the list and checksums of the files, and for GitHub it also verifies the signature with the trusted public key `maintenance/release-public.pem`.

For the next release, change the version to `app/version.php` and build the package via `php update.php --build php-site-update.zip`. Sign the ZIP with your RSA private key with SHA‑256, and store the private key separately from the site. Then push the ZIP and `.sig` to GitHub Release with the tag `vX.Y.Z` or to `updates/vX.Y.Z/` and update `updates/latest.json` to `{"version":"X.Y.Z"}`.

## Extensions

Engine modules can be added to `app/extensions/` along with updates. Store your own modules in `extensions/`, outside the update package. The PHP file returns an array of definitions. A single component has `kind` equal to `single`, and a component with an argument equals `wrapped`. The engine recognizes new codes on existing pages. A component can declare a menu of options in `settings`, setting each option to `label`, `default`, and the array `options`. The values ​​are received in `$context['settings']`.

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

The example component is inserted as `[notice]Важное сообщение[/notice]`.

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

## Admin header buttons · 1.6.4

The globe opens the site and the arrow signs you out. On mobile, these buttons show icons only. Hover or focus with the keyboard to see their labels.

Content, Forms and Requests, and Site and Services open directly. The selected section shows its own tabs under the main menu.

Insert [bread-crumbs] or [search] in the HTML of the home page (index.php) or a layout template. Each block appears once at its chosen position. The breadcrumb and search settings still apply. [privacy] links to /privacy/. Edit the policy under Pages.

```text
[bread-crumbs]
[search]
[privacy]
```

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

## Editing component data in place · 1.6.6

In edit mode, infoblocks, [formX] forms, [tel] snippets and other components with data editors offer Data and Full screen. The infoblock view dropdown lists registered display templates. Choose Root folder to move a section to the top level. Use [list-pages] for published pages in the section and all subfolders. Close the admin language menu by clicking outside or pressing Escape.

## Mobile menu · 1.6.7

- Show in the main menu
- Show in the mobile menu
- Hide in the mobile menu

## Page settings · 1.6.8

Write content in Edit. Set the address, menu and publication schedule in Page settings. SEO contains titles, description, keywords and sitemap inclusion. Maps contains coordinates and tag examples.

Leave dates blank for no limits. Dates use the site time zone. A hidden page must be published separately.

- `[map]` — map with the current page’s location.
- `[maps]` — map with all published pages’ locations.

## Banners and image editor · 1.6.8

Manage sets and slides in Components. Use photos from a folder, configure captions and buttons for links, forms or callbacks, and schedule when slides appear. Edit mode shows slide actions. The image picker browses folders, uploads, deletes and makes rotated or resized copies with GD. The Font Awesome picker shows icons and supports searching by name.

```text
[banners]
[banners set=summer arrows=on numbers=off]
[banners]/images/myfotos/[/banners]
```

## Languages · 1.6.9

The Languages tab lists the page languages. Open takes you to a saved version. Create translation opens a blank translation editor.

Choose where to create the section. A subsection goes inside the current section, a sibling goes beside it, and a root section goes at the top level of the site.

## PHP compatibility and navigation · 1.6.10

The minimum PHP version is 7.2. The site and admin area support PHP 8.6. Use the PHP syntax available on your server when writing custom modules and templates.

The current [bread-crumbs] item opens a list of published sections at the same level that appear in the main menu. The page’s configured URL is used in menus, section lists, breadcrumbs, search results and suggestions.

The trash icon removes a row from the infoblock editor. Removing a column removes all its values. Changes are written when the infoblock is saved, including autosave after reordering.

### Site backups

The private ZIP contains pages, media, infoblocks, settings, custom templates and PHP modules. The administrator account and incoming requests are stored separately. Backups require PHP Zip.

Replace content and settings with this backup. A copy of the current site will be created first.

### Unused file cleanup

The scan finds media without references in saved content. Review the list and select the files to remove. A backup is created first. Removing a component code leaves its folder and files in place.

### Pick a point on a map

In the Yandex Maps console create a key for the «JavaScript API» product and restrict it by HTTP Referer. With a provider enabled, open the page’s Maps tab, choose a point and apply its coordinates.

Select text in the visual editor to align it or clear formatting. Click an image to edit its properties, drag its corner to resize, or move the image in the text. Links can target URLs, emails, telephone numbers or anchors. Buttons can open a URL, form, callback or payment. Use a provider link for the displayed amount.

Use the HTML and Visual Editor tabs to switch modes. SUP, SUB and Paragraph insert the matching elements. Component names show their shortcodes. Missing media folders are created during insertion. Folder names allow letters A–Z, digits, hyphens and underscores. The Languages tab links to the site language settings.

```text
[button action="url" label="Contacts" url="/contacts/"]
[button action="form" label="Write to us" form="form1"]
[button action="callback" label="Call me"]
[button action="payment" label="Pay" amount="1250.00" currency="RUB" yoomoney="410010000000000"]
[map]
[maps]
```

## Sale · 1.6.11

Set price and payment method in the Sale tab. You can override them per language. Insert [item-price] and [item-buy-button]. The order is listed before checkout. Status notifications can use email and Zadarma SMS.

Order received · Payment accepted · Shipped · Closed · Cancelled

```text
[item-price]
[item-buy-button]
```

Select a file or enter a URL to insert an image. Click the image to set its size, alignment, four margins and CSS. Image alignment is independent of text alignment. Switching to HTML puts block markup on separate lines. Section page list [list-pages] is in the component list. The CMS logo in the admin header and footer uses the current theme colors.

## Chart from data block

In the page editor, open Insert component and select a table or chart. For charts, enable Chart.js, pick a label column and select Number or Price columns. Pie, doughnut and polar area charts use one non-negative series. When the library is off, visitors see the table.

Saved forms, templates, snippets and registered components appear automatically when the editor is reopened. The ID of a new banner set accepts A–Z, a–z, hyphens and underscores. After uploading images to a [banners] folder, an Edit slide link appears beside each file. In edit mode, Edit slide opens the selected slide in a dialog with a separate link to component defaults. The carousel, gallery and file list have a file manager next to Save for uploads, deletion, sorting and subfolders (A–Z, a–z, 0–9, - and _). Slide colors appear as square swatches.

```text
[iblock=table]/data/myiblock.txt[/iblock]
[iblock=charts type=line labels=month values=sales,orders]/data/myiblock.txt[/iblock]
[banners set=summer-promo]
```

## Admin header and footer · 1.6.12

On desktop, the logo is paired with Eurowebcart CMS and Light, followed by the installed version. On phones, the symbol shares a row with the controls. The footer links to the repository. Check for the latest cumulative release in Settings → Updates.

## Admin header and footer · 1.6.13

On desktop, the logo is paired with Eurowebcart CMS and Light, followed by the installed version. On phones, the symbol shares a row with the controls. The footer links to the repository. Check for the latest cumulative release in Settings → Updates. The standard Home label in the page list follows the admin language. The footer logo has two lines: Eurowebcart CMS and LIGHT.

## PHP information

In Maintenance, phpinfo() shows the PHP version, modules and configuration to signed-in administrators. Environment and request variables are omitted. Set error_reporting in Settings → General. The server default applies initially.

## Hide page tree · 1.6.14

On desktop, the arrow in the page tree collapses it and widens the editor. The button above the editor restores it. The browser remembers your choice.

## Hide page tree · 1.6.15

On desktop, the arrow in the page tree collapses it and widens the editor. The button above the editor restores it. The browser remembers your choice.

## Pick a point on a map

In the Yandex Maps console create a key for the «JavaScript API» product and restrict it by HTTP Referer. With a provider enabled, open the page’s Maps tab, choose a point and apply its coordinates. For local testing, enter localhost without a port in HTTP Referer, wait for the key to be active and open the admin panel using localhost.

## Search in navigation · 1.6.16

[search] places search beside the desktop [main-menu] links and inside [mobile-menu] on phones. Choose the search mode in general settings. An explicit [search] in page content takes priority.

`[search]`

## Search in navigation · 1.6.17

[search] places search beside the desktop [main-menu] links and inside [mobile-menu] on phones. Choose the search mode in general settings. An explicit [search] in page content takes priority. The search field is always visible inside the mobile menu. [lang] provides language links inside that menu and in the desktop header.

`[search]` · `[lang]`

The bottom of the mobile menu shows the channels configured in Contacts and social media.

## Site subtitle

Set the subtitle in General settings. It appears in smaller text below the site name in the header. The site name is smaller on phones.

## Cities

In Cities, assign a registered domain to a city and add up to five phone numbers. A phone field can use a snippet such as [tel]. [cities] shows the city selector, [phones] lists its configured numbers, and [tel], [tel2]–[tel5] show individual numbers. The site remembers your choice and updates the page without reloading.

`[cities]` · `[phones]` · `[tel]`

## Scroll animation

In General settings, choose how content blocks appear when scrolling. Animation is off by default and respects the device’s reduced-motion setting.

## Page templates

Choose a template when creating a page. It fills in the content and SEO fields, which you can then edit. Add your own templates under Page templates. Maps, forms and purchases use their component settings.

Template CSS and JavaScript are copied to the page’s asset fields. Add your own HTML classes and style them in the template’s CSS.

### Reusable classes

Layout wrapper, responsive grid, highlighted section, card, and emphasized price.

`.page-preset` · `.page-preset-grid` · `.page-preset-hero` · `.page-preset-card` · `.page-preset-price`

Layout templates start collapsed. Open a template by its heading or select Expand all. Press the button again to collapse the list.

To resize the whole banner carousel, turn on edit mode, choose Edit component beside the banners, and select Block width.
