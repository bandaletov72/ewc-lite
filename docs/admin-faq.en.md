# Admin FAQ

## Installation and sign-in

### How do I start the site and open the admin area?

Run php -S 127.0.0.1:8000 router.php in the project directory and visit http://127.0.0.1:8000/admin/. On a host, run php admin/setup.php first and enter the one-time code in the setup wizard.

### What can I configure during setup?

Create a username and password first. The next step sets the site name, meta description, footer, theme and palette. With PHP GD installed, you can upload a logo and create a favicon. You can skip that step or finish it later.

## Pages and menus

### How do I create a page and choose its address?

Choose Pages → New page and enter an address such as /Services-1/Repair/ or /Services-1/Price_2.php. A folder URL creates the folders and an index.php. Use A–Z, a–z, 0–9, hyphens or underscores. The home page occupies the root. System directories are reserved.

### What is the difference between Show in menu and Hide page?

Turn off Show in menu to remove the page from the default menus while keeping its URL public. On a folder page, this also controls entries for child pages. Hide page makes the public URL unavailable until you publish it again.

### How do I reorder or move pages?

Drag the ⠿ handle to reorder pages at the same level. Use Move page in the page settings to move a single page. The icon beside a folder opens the parent section selector. Choose another section or the root, then select Move folder. The folder moves with its pages and files. Check links and [include] codes for old URLs.

### Why don’t my page changes appear?

Save the page, then follow Open page. Check whether it is published and whether you are on the intended domain.

## Appearance and subdomains

### Where do I change the site name, footer, logo and favicon?

Open Settings → General. Use [year] in the footer if you want the current year. Site logos and icons accepts light and dark logos and creates a favicon from a logo when PHP GD is available.

### How do I switch themes or fonts and restore them later?

Choose your mode and palette under Theme and colors. Save a palette to reapply it later. Use as default saves the palette for restoration. Libraries and fonts controls the website font and shows a preview. Choose System to restore the system font.

### Where do I edit the header or add analytics, CSS or JavaScript?

Appearance templates controls header, mobile-menu, menu and footer. Service headers inserts HTML in head or before closing body. CSS and JS controls site-wide assets. Page-level assets are in the page editor.

### How do I set different details on subdomains?

Add the subdomain under Domains. Edit its values under Snippets or use Templates beside the domain to set a layout override. Your DNS and web server must point the domain at the same installation.

### Where do I edit social links and labels?

Open Settings → Contacts and social media. Add the links, labels and phone numbers. [soc-icons] shows all configured icons. Use [tg] or [vk] to insert one network.

## Forms and callbacks

### How do I add a form?

In Forms, set the fields, required fields, maximum lengths and button label. Save the form and insert its code, such as [form1], into the page.

### Where are submissions if I did not receive an email?

Open Messages. Successful submissions are stored there. For email notifications, check the recipients in the form’s email template and verify that the host supports PHP mail().

### How do I customize the email and add copies?

Open the form’s Email template. Enter comma-separated addresses for recipients and Cc. Use placeholders such as {name} in the subject and body. The default subject or body is used for an empty field.

### Why was my submission rejected or asked to complete CAPTCHA?

Check the required fields, lengths, phone and email. Free-text fields accept plain text. Blocked words and IPs are under Anti-spam. CAPTCHA uses the reCAPTCHA v2 site and secret keys for the domain.

### Where are callback requests?

Open Callbacks. Add a callback form with [callback] or [callback=Call me]. Provider None stores requests locally. A configured provider can dispatch them manually or automatically.

## Data blocks and components

### How do I create and display a data block?

Choose Data blocks → New data block, define columns and add rows. Use Latin letters, numbers, hyphens and underscores in identifiers. Copy the insertion code from the editor. The phones layout renders a phone list.

### How do I copy or replace all rows?

Open Edit as text in the data block. The CSV delimiter is |. The first three rows contain keys, labels and types. Quote fields containing a delimiter, a quote or a line break. Double quotes inside a quoted field.

### Why is my gallery or file list empty?

Check the path against the server folder. [fotos]/images/myfotos/[/fotos] uses images/myfotos/ or templates/images/myfotos/. For [files], check the extension in the component guide.

### Why does ?ajax=1 return 403?

Page fragments require a public session and a token from the same site. Use window.siteFetchContent() within the site to provide the token. Open the regular URL for a full page.

## Updates and backups

### How do I update the engine?

Open Updates and choose Update engine. To apply a local archive, run php update.php php-site-update.zip. Put custom PHP layouts in templates/local/. Scheduled updates are available under Maintenance when your host runs php update.php --scheduled.

### What should I back up before moving or updating?

Copy the whole site directory, including hidden files, settings, pages, data blocks, custom templates, uploads, messages and callbacks. Store the backup outside the public document root because it contains configuration secrets.

### What if the admin area cannot save a change?

Read the error near the form. Reopen the editor after a conflict. For write errors, check PHP permissions on the relevant directory and admin/write-lock.php. For updates, check GitHub connectivity and the PHP Zip and OpenSSL extensions.

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

Use the HTML and Visual Editor tabs to switch modes. SUP, SUB and Paragraph insert the matching elements. Component names show their shortcodes. Missing media folders are created during insertion. Folder names allow letters A–Z, digits, hyphens and underscores. The Languages tab links to the site language settings. Insertion panels open above the editing area. Select Close at the top of the panel or press Esc to close it and clear the active toolbar button. The toolbar arrows undo and redo up to 10 recent text edits through the server. The clock icon loads the last saved text. Leaving the page with unsaved changes requires confirmation. The Save icon before the arrows lights up when there are unsaved changes. Select a fragment and open Text styling. Set the size, text color or background color, or choose a font enabled in the site settings.

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

On desktop, the logo is paired with Eurowebcart CMS and Light, followed by the installed version. On phones, the symbol shares a row with the controls. The footer links to the repository. Check for the latest cumulative release in Updates.

## Admin header and footer · 1.6.13

On desktop, the logo is paired with Eurowebcart CMS and Light, followed by the installed version. On phones, the symbol shares a row with the controls. The footer links to the repository. Check for the latest cumulative release in Updates. The standard Home label in the page list follows the admin language. The footer logo has two lines: Eurowebcart CMS and LIGHT.

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

## Cities · Phone · 1.6.18

Only the city name is required. The domain and phone fields are optional. For a phone number, include + and the country code, for example +7 (900) 000-00-00. A snippet such as [tel] can stand in for a number.

The city selection uses the saved choice, the domain’s assigned city, then the first city in the list. With no cities, the site shows the general phone numbers.

Form messages, callbacks and new orders use the selected city’s email or the site-wide address when blank. If both settings are blank, forms keep their own recipients. Blank city social links inherit site-wide links.

In edit mode, hover over a layout and choose Edit template. For any installed [php] component, Edit module opens its source and settings in a dialog. The slide editor shows the form name and code. Explicit [banners width=100] spans the viewport when inserted as a top-level block on a page with no sidebar. The layout editor opens in a dialog on the site.

The file manager checks a folder and its contents for references before deletion. A backup is created for a non-empty folder. Each banner slide can have a title and subtitle.

Phone numbers are checked as you type. A Russian number starting with 8 changes to +7. After successful submission the form is hidden, and reopening the dialog shows the confirmation.

The selected country code is added to numbers without +. Numbers that start with + keep their international calling code.

## Design themes

Copy a file into the theme and save it. Missing files use the stock distribution. Changes, new layout shortcodes, history and domain-specific layout settings belong to the active theme only. Use css/theme.css and js/theme.js for extra styles and scripts.

`templates/themes/demo/header.php` · `templates/themes/demo/components/banners.php`

Languages sit at the bottom of the mobile drawer. On desktop, the globe switch sits next to search in the menu below the header.

Galleries, banners and file lists look in the active theme first and use shared files if the folder is missing. New folders, uploads, logos and favicons go into the theme. Backups include the theme directory and its settings.

Choose viewport width (width=page), content width (width=content) or parent width (width=parent). Set height for desktop and mobile-height for phones. Use auto to keep the natural size.

`[banners width=page height=520 mobile-height=240]` · `[form1 width=parent]`

The final breadcrumb lists published sibling sections, including sections hidden from the main menu. A configured section URL is used for its link.

## Design themes · КОНТУР · 1.6.19

A complete site theme switches the design, pages, data blocks, and settings. Other themes use the shared site pages.

A check marks a theme file that takes precedence over the stock CMS file. You can edit or restore it.

**Restore factory version**

### Portfolio

Open kontur-works in Content → Data blocks. Set the title, category, description, comma-separated photos and an optional link. The first photo is the cover. Use the same columns for additional collections.

```text
[portfolio]
[iblock=portfolio limit=2]/data/kontur-works.txt[/iblock]
[maps source=kontur-map]
```

### Site colors · Fonts

Enable fonts with checkboxes and assign them to body text, headings or controls. Local files are served by your site.

Libraries → Make local

Page templates → Create page
