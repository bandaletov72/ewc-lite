# English · Eurowebcart CMS Lite

## Start here in 1.6.58

- Activate a complete site under Design templates → Design themes. Site colors and fonts have separate tabs. You can enable multiple font families and store local copies.
- Enable libraries under Settings → Libraries and save local copies where needed. Font Awesome is bundled.
- In edit mode, double-click page content to open the editor over the site. Double-click the standard H1 to open its field on the SEO tab.
- Custom field definitions are shared and values are per page. You can set a value for the open page when creating a field. The control adapts to the selected type.
- For a new domain, build a full ZIP with the active theme and verify a clean installation. For an existing site, make a backup and use a signed update.

- [Management](en/readme.md)
- [Admin FAQ](en/faq.md)
- [Templates and components](en/templates.md)

## Mobile menu · 1.6.7

- Show in the main menu
- Show in the mobile menu
- Hide in the mobile menu


## Editing component data in place · 1.6.6

In edit mode, infoblocks, [formX] forms, [tel] snippets and other components with data editors offer Data and Full screen. The infoblock view dropdown lists registered display templates. Choose Root folder to move a section to the top level. Use [list-pages] for published pages in the section and all subfolders. Close the admin language menu by clicking outside or pressing Escape.


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

The code [author] displays the signature Eurowebcart CMS Lite, the author name Бандалетов Павел, and a link to GitHub. To choose where to place your signature in your custom HTML footer, paste [author] into the appropriate line.

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

The Form button column displays an Order button in each configured row. Choose a form by name in the dropdown. For CSV use a form code such as form86.

The Hidden tag type adds filters above the table. Enter one or more comma-separated tags in each cell. ALL shows every row.

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

Choose “Edit” to change a template. “Save template” applies your changes when you create new pages. “Duplicate” creates a separate template. In the page editor you can open the selected template or create one from the saved page. In the template form, choose “Insert icon” and search by name. You can also enter a code such as `[fa-icon=house]`.

Layout templates start collapsed. Open a template by its heading or select Expand all. Press the button again to collapse the list.

To resize the whole banner carousel, turn on edit mode, choose Edit component beside the banners, and select Block width.

## Cities · Phone · 1.6.18

Only the city name is required. The domain and phone fields are optional. For a phone number, include + and the country code, for example +7 (900) 000-00-00. A snippet such as [tel] can stand in for a number.

The city selection uses the saved choice, the domain’s assigned city, then the first city in the list. With no cities, the site shows the general phone numbers.

Form messages, callbacks and new orders use the selected city’s email or the site-wide address when blank. If both settings are blank, forms keep their own recipients. Blank city social links inherit site-wide links.

In edit mode, hover over a layout and choose Edit template. For any installed [php] component, Edit module opens its source and settings in a dialog. The slide editor shows the form name and code. Explicit [banners width=100] spans the viewport when inserted as a top-level block on a page with no sidebar. The layout editor opens in a dialog on the site. In edit mode, double-click a blank area of the template. The hint appears under Edit template on hover. When blocks overlap, the later template takes priority. Edit slide opens the selected banner slide. The site toolbar lists only the current page’s components, layouts and snippets. In the admin page editor, open Page components next to Languages to edit available component parameters and data. Double-click the page content to open the current page editor. The editor opens in a dialog over the site. After saving, close the dialog to see your changes on the page. Double-click the H1 heading to focus its field on the SEO tab.

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

Quiet Garden is a complete landscape design studio site. Select it under Layout templates → Design themes and choose Apply. Edit the garden-projects and garden-prices data blocks, and change the cover in the garden-cover banner set. Add your contact details and set a recipient for [form85].

The Dezinfektori theme includes 32 services, the inquiry form [form86], and the feedback form [form87]. Open the submission in Messages. With consent, copy the name, text, and rating to dez-reviews and enable publication. Service pages include risk explanations, before-and-after illustrations, and fictional field stories. Edit these blocks under Pages.

```text
[iblock=reviews limit=6]/data/dez-reviews.txt[/iblock]
```

The home page lists approved reviews from every page when the review filter is empty.

Every catalog page has a Leave a review form. After approval in Reviews, the review appears on the page it was submitted from.

### Site colors · Fonts

Enable fonts with checkboxes and assign them to body text, headings or controls. Local files are served by your site.

Libraries → Make local

Page templates → Create page

## Custom fields

Enter a value for the current page in the new row and save it with the field settings.

## Reviews

Insert the code in the page editor. Visitors enter a name, write a review and choose a rating from 1 to 5 stars. In Forms and requests → Reviews, approve, reject, edit or delete a review. Load more adds the next ten reviews.

Enter a path such as /contacts/, or leave it blank to show all reviews. The form records the page it was submitted from. Only approved reviews appear on the site. CAPTCHA uses the general CMS settings.

```text
[reviews]
```

The Leave a review button opens the review form.

## Update progress · Indexing progress

Under Updates, the progress bar tracks the update ZIP and signature downloads, verification, and installation. The CMS keeps existing pages and settings and creates temporary rollback copies of the files being replaced.

Under Design Themes → Update theme, the log shows the selected mode. When replacing bundled pages, the CMS creates and verifies a backup of pages, settings, and media. Find the archive named in the log under Maintenance.

Under Maintenance, Rebuild index reports the number of processed pages and shows when the search index is being saved.

**Design and add missing pages**

Applying the design and adding missing navigation pages. Existing pages and settings are kept.

**100% · Done**

In edit mode, hover over a snippet value and select Edit subtemplate. Save and close the dialog to see the updated page.

Edit component on [reviews] opens review settings and moderation in a dialog.

**Reviews per batch · Load more button text**

The batch size applies to the first load and to each button press. Leave the text blank to use the default label in the page language.

Use Data to open the [iblock] editor. Other components use Settings.

If the editor fails to load in the dialog, select Retry or use Full screen to open it separately.

Insert [reviews page=current] to show reviews for the current page first, followed by reviews from other pages, newest first.

Installed theme version · Version bundled with the CMS

Select Design and add missing pages to apply changes to the review shortcodes.

Checking for updates loads the release description from GitHub.

Automatically approve reviews

Reviews containing links or blocked terms stay in moderation. Edit the word list in «Antispam». CMS and spam-filter updates from GitHub add new rules to your list and preserve your saved additions and removals. IPv4 masks allow * for any octet and inclusive ranges, such as 189.10-17.*.*. Exact IPv4 and IPv6 addresses are supported.

Address `[address]` · Public email `[email]` · Opening hours `[hours]`

City contact details appear in the header. Empty fields use the shared snippet values.

The installed and GitHub filter versions appear beside the update button.

## Updates · Backups

The red badge counts available engine, active theme, and spam filter updates. Choose days and a time window for automatic updates. Automatic theme updates apply design files only. A red exclamation mark indicates there is no backup less than a month old. Run the scheduler command every 15 minutes. `php update.php --scheduled`

- Layout templates → Snippets
- Cities and domains
