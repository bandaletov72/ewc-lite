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
