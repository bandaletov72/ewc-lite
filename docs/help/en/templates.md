# Templates and components

## Layout templates

### Header, menu and footer

The full page connects these blocks automatically. General versions are edited in “Design Templates”, and for an individual domain - in the “Domains” section.

```text
[header]
[mobile-menu]
[menu]
[footer]
```

### Inserting another page

Inserts the content of a published page. The maximum nesting is ten levels. Repeating a page in the same chain is skipped.

```text
[include]/contacts/[/include]
```

### Links in the menu

The “Show in menu” checkbox adds a link to the standard menus. If the name is empty, the title is taken. If you uncheck the checkbox for a folder page, its subpages will also disappear from the menu.

```text
MenuVisible: 1
Menu: Contacts
```

### CSS and JavaScript

The general code is edited in the “CSS and JS” tab. The page code is edited in the “CSS and JavaScript pages” block and included after the general one. Paste the CSS and JavaScript content into the fields.

```text
.page-content { max-width: 72rem; }
```

```text
document.documentElement.classList.add('custom-script-loaded');
```

### Service headers

In the “Design templates → Service headers” section, you can insert general meta tags and counter codes inside the head or before the closing body. Canonical is added automatically on full pages.

```text
<meta name="theme-color" content="#065f63">
```

### Font Awesome Icons

Font Awesome Free 7.3.1 is connected. The short code works in content and HTML design blocks. Solid, regular and brands styles are available. Add text next to the icon if it represents an action.

```text
[fa-icon=house] [fa-icon=regular:envelope] [fa-icon=brands:github]
```

```text
<i class="fa-icon fa-solid fa-phone" aria-hidden="true"></i> Contact
```

### Libraries and fonts

Connections are managed in “Settings → Libraries and fonts”. For Material UI, use modular JavaScript. The selected Google font replaces the site's system font.

```text
<script type="module">import { Button } from "@mui/material";</script>
```

```text
GLightbox({selector: ".glightbox"});
```

## Variables, subtemplates and date

### Site Variables

Add entries to “Settings → General”. Insert the key into the text, header, site title or footer.

```text
[city] = Moscow
[spb] = St. Petersburg
```

```text
Service in the city [city]
```

### Snippets

In the Subtemplates section, specify a name, description, and value. For a domain, its value is taken first, then its general value. The subtemplate may contain HTML. The markup in headers is removed.

```text
[tel]
```

```text
[title]
```

### Date and time

The time zone is taken from PHP. [year] - year, [month] - month, [day] - day of the month, [time] - hours and minutes, [dmY] and [date] - date DD.MM.YYYY, [datetime] - date and time.

```text
© [year] [city]
Date [dmY], time [time]
```

### CMS name and author

The code [author] outputs Eurowebcart CMS Light, the author name Бандалетов Павел, and a link to GitHub. In a standard footer, the signature appears automatically. In your own HTML footer, paste the code where you want it.

```text
[author]
```

### Home template and policy

[page-content] inserts the home page content into [home]. Open /privacy/ under Pages to edit the policy. [site-name] and [site-url] come from the general settings.

```text
[page-content]
```

```text
[site-name] · [site-url]
```

### Links and settings in the footer

[privacy] links to /privacy/, [top] scrolls to the top, and [cookies] opens consent settings.

```text
[privacy]
[cookies]
[top]
```

### Common PHP Functions

In the “Design Templates → Functions” section, edit functions.php. In PHP code, translit() or ew_call() calls a registered function. The short code [func=translit] is available in the content and in the “Custom HTML” field.

```text
[func=translit value="Москва и München"]
```

```text
$var = translit("Moscow");
```

```text
echo ew_escape(ew_call("translit", "München"));
```

## Carousel, photo gallery and files

### Carousel [gal]

Shows photos from a folder. arrows and numbers accept on or off, effect – slide, fade or none, fit – contain or cover.

```text
[gal]/images/myfotos/[/gal]
```

```text
[gal arrows=on numbers=off effect=fade fit=cover width=50]/images/myfotos/[/gal]
```

### Photo gallery [fotos]

Shows a responsive grid. columns — auto or a number from 1 to 6, ratio — 4x3, 1x1, 16x9 or original, fit — cover or contain. captions and links are enabled via on or off.

```text
[fotos]/images/myfotos/[/fotos]
```

```text
[fotos columns=4 ratio=1x1 fit=cover captions=on links=off width=50%]/images/myfotos/[/fotos]
```

### List of files [files]

Lists supported files. icons, size and download accept on or off, order – asc or desc. Zip, pdf, xls, xlsx, doc, docx, png, gif, jpg, jpeg, mp4, mov and mp3 are allowed.

```text
[files]/files/myfiles/[/files]
```

```text
[files icons=on size=on order=desc download=on width=33]/files/myfiles/[/files]
```

## Forms and call back

### Form [formN]

In the “Forms” section, configure the fields, their maximum length, button text and recipient. “Use CAPTCHA” enables the Google reCAPTCHA v2 widget with response verification on the server. Requests are sent via AJAX and are visible in the Messages section.

```text
[form1]
[form2]
```

### Mail form template

The subject and text are stored separately for each form. In your email template, insert field keys in curly braces. The lists of recipients and copies are separated by commas.

```text
Hello {name}!
Phone: {phone}
Message: {message}
```

### Call back [callback]

Opens a form with a phone number. The application is saved in the “Callbacks” section. The button label can be set in code. With the “No” provider, sending to telephony is disabled.

```text
[callback]
[callback=Вызвать мастера]
```

## Data blocks

### Table and list of telephones

Create a data set in the "Infoblocks" section. A–Z, a–z, 0–9, hyphen and underscore are allowed in the infoblock name and column keys. Spaces are not allowed. The table template displays a table, phones displays telephone columns with links to call. Closing codes [iblock] and [/iblock] are supported.

```text
[iblock=table]/data/myiblock.txt[iblock]
[iblock=phones]/data/myiblock.txt[/iblock]
```

## Contacts and social media

### General block and icons

[contacts] displays a block of contacts, [phones] shows phone numbers, [soc-icons] collects icons of all social networks with customized links.

```text
[contacts]
[phones]
[soc-icons]
```

### Individual social networks

Each code displays a link to its social network. Addresses and signatures are set in “Settings → Contacts and social networks”.

```text
[tg] [vk] [whatsapp] [youtube] [instagram] [max]
```

```text
[wa] [yt] [ig]
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
