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

