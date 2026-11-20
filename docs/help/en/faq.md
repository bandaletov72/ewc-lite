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

Drag the ⠿ handle to reorder pages at the same level. Use Move page to move a page and its translations or ↪ beside a folder to move the complete folder. Check links and [include] codes for the old URLs.

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

Open Settings → Updates and choose Update engine. To apply a local archive, run php update.php php-site-update.zip. Put custom PHP layouts in templates/local/. Scheduled updates are available under Maintenance when your host runs php update.php --scheduled.

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
