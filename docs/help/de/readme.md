# Eurowebcart CMS Light

Autor - Бандалетов Павел. [Originalprojekt und Updates](https://github.com/bandaletov72/php-site-updates).

Die Mindestversion ist PHP 7.2. Website und Administration unterstützen PHP 8.6. Verwenden Sie in eigenen Modulen und Vorlagen die Syntax der PHP-Version Ihres Servers.

[Admin-FAQ](faq.md) ist auch im Reiter „Hilfe“ verfügbar.

[Hilfe zur aktuellen Version in zehn Sprachen](../README.md) beschreibt Suche, Site-Bearbeitung, Lokalisierung und Wartung. [Admin FAQ in English](../en/faq.md).

Fügen Sie für geplante Wartungsarbeiten den Befehl `php update.php --scheduled` mit einem Zeitraum von 15 Minuten zum Hosting-Planer hinzu. Im Abschnitt „Wartung“ können Sie die Neuindizierung selbstständig aktivieren und das Prüfintervall für signierte GitHub-Updates festlegen.

## Lokaler Start

Gehen Sie in das Verzeichnis `php-site` und starten Sie den integrierten PHP-Server.

```sh
php -S 127.0.0.1:8000 router.php
```

Die Site wird unter der Adresse `http://127.0.0.1:8000/` geöffnet, das Admin-Panel befindet sich unter `http://127.0.0.1:8000/admin/`.

## Installation und erste Anmeldung

Öffnen Sie auf Ihrem lokalen Server `/admin/` und erstellen Sie ein Konto. Legen Sie im zweiten Schritt den Site-Namen, die Meta-Beschreibung, den Fußzeilentext, die Palette und den Themenmodus fest. Das Logo kann sofort heruntergeladen werden, wenn Sie über PHP GD verfügen. Der Assistent erstellt daraus ein Favicon. Klicken Sie auf „Speichern und Admin öffnen“ oder fahren Sie mit den aktuellen Einstellungen fort. Wenn Sie den Assistenten verlassen, wird er bei Ihrer nächsten Anmeldung fortgesetzt.

Führen Sie auf dem Hosting `php admin/setup.php` aus dem Projektverzeichnis aus und geben Sie im ersten Schritt des Assistenten den Einmalcode ein. Der Code wird eingelöst, wenn Sie ein Konto erstellen. Durch erneutes Ausführen des Befehls vor der Installation wird der vorherige Code ersetzt. Durch die Installation des Updates bleiben das vorherige Konto und die vorherigen Einstellungen erhalten. Wenn die Version vor dem Update `SITE_ADMIN_PASSWORD_HASH` verwendete, wird der Login mit dem Login `admin` und dem bisherigen Passwort gespeichert.

Damit die Anwendung funktioniert, benötigen Sie PHP-Sitzungen und Schreibrechte für `admin/`, `admin/write-lock.php`, `admin/inbox/`, `data/` und Verzeichnisse bearbeitbarer Seiten. Um Seiten und Verzeichnisse im Stammverzeichnis zu erstellen, gewähren Sie dem PHP-Prozess Schreibzugriff auf das Projektstammverzeichnis. Verwenden Sie einen Webserver mit HTTPS und verarbeiten Sie `.php` und `index.php` in der Indexdateiliste. Verzeichniseinträge deaktivieren. Für HTTPS hinter einem Reverse-Proxy übergeben Sie das PHP-Attribut `HTTPS` über die vertrauenswürdige Serverkonfiguration.

## Seiten und Menüs

Im Admin-Bereich wird die Hauptseite als `/` aufgeführt, die restlichen Seiten in den Ordnern werden als `/contacts/` aufgeführt. Um eine Unterseite zu erstellen, geben Sie beispielsweise `/Services-1/Repair/` ein. Das Admin-Panel erstellt die Kataloge und `index.php` selbst. Geben Sie für eine separate Datei eine Adresse wie `/Services-1/About_2.php` ein. `A–Z`, `a–z`, `0–9`, Bindestriche und Unterstriche sind in Datei- und Ordnernamen zulässig. Namen werden ohne Leerzeichen eingegeben. Im Root ist nur die Hauptseite erlaubt. Serviceverzeichnisse einschließlich `data`, `admin`, `templates`, `css` und `js` sind reserviert.

In der Seitenliste können Ordner erweitert werden. Im Menü-Kontrollkästchen werden Ausblenden, Veröffentlichen und Löschen über AJAX gespeichert. Das Ausblenden, Veröffentlichen und Löschen erfordert eine Bestätigung. Ziehen Sie das ⠿-Symbol, um benachbarte Seiten zu sortieren. Die ausgeblendete Seite wird nicht mehr geöffnet und verschwindet vor der Veröffentlichung aus dem Menü.

Öffnen Sie im Seiteneditor den Block „Seite verschieben“ und geben Sie den Zielordner an. Wenn Sie beispielsweise `/contacts/` an `/company/` übertragen, lautet die neue Adresse `/company/contacts/`. Verschachtelte Seiten bleiben an denselben Adressen. Aktualisieren Sie Ihre eigenen Links und Codes `[include]`, die zur migrierten Seite führen.

Ziehen Sie am Griff ⠿, um Seiten derselben Ebene zu sortieren. Eine einzelne Seite verschieben Sie in den Seiteneinstellungen. Das Symbol am Ordner öffnet die Auswahl des übergeordneten Bereichs. Wählen Sie einen Bereich oder die oberste Ebene und klicken Sie auf „Ordner verschieben“. Seiten und Dateien werden mitverschoben. Prüfen Sie alte Links und [include]-Codes.

Inhalte können in einem Texteditor bearbeitet und über FTP oder SFTP hinzugefügt werden. Die Datei muss im UTF-8-Format ohne Stückliste vorliegen. Speichern Sie den PHP-Serviceblock bis zur Zeile `__halt_compiler();`. Fügen Sie in der neuen Auslagerungsdatei diesen Block aus dem Beispiel in der gewünschten Tiefe ein und überprüfen Sie den relativen Pfad zum Stammverzeichnis `index.php`. Als nächstes folgen die Kopfzeilen, eine Leerzeile und ein HTML-Fragment der Seite.

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

`Title` ist erforderlich. Leer `Heading` wird von `Title` übernommen. `Description` füllt die Meta-Beschreibung der Seite aus. `MenuVisible: 1` zeigt den Link im Menü an, `MenuVisible: 0` blendet ihn aus. Für den Ordner `index.php` werden durch Deaktivieren des Kontrollkästchens auch alle Unterseiten aus dem Menü ausgeblendet. Wenn `MenuVisible` fehlt, erbt das Kontrollkästchen die vorherige Regel und gilt als aktiviert, wenn `Menu` gefüllt ist. Bei aktivierter Checkbox wird das leere Feld `Menu` durch den Header `Title` ersetzt. `Order` gibt die Reihenfolge innerhalb einer Ebene an und ist standardmäßig auf 100 eingestellt.

Auf der Registerkarte „CSS und JS“ werden allgemeines CSS und JavaScript für öffentliche Seiten gespeichert. Der Seiteneditor verfügt über einen separaten Block „CSS- und JavaScript-Seiten“. Der Seitencode wird nach dem allgemeinen geladen. Geben Sie nur den Inhalt der Dateien an, ohne die Tags `<style>` und `<script>`. Die zulässige Größe jedes Feldes beträgt bis zu 64 KiB. Um über CSS auf Bilder zu verlinken, können Sie Pfade aus dem Stammverzeichnis der Website verwenden. Die Registerkarte „Hilfe“ enthält Beispiele für verfügbare Vorlagen und Komponenten.

Im Abschnitt „Designvorlagen“ können Sie die Quellen von `header.php`, `footer.php`, `menu.php` und `mobile-menu.php` anzeigen und jeweils gemeinsames HTML festlegen. „Service-Header“ fügen allgemeine Meta-Tags, Zählercodes und Skripte in `<head>` oder vor `</body>` ein. Auf jeder veröffentlichten Vollseite wird `rel="canonical"` aus seiner regulären Adresse ohne Abfrageparameter gebildet.

## Einstellungen, Untervorlagen und Domänen

Im Abschnitt „Einstellungen“ legen Sie den Site-Namen, die Standard-Meta-Beschreibung, den Fußzeilentext und Variablen fest. Im Fußzeilentext zeigt `[year]` automatisch das aktuelle Jahr an. Variablen werden einzeln pro Zeile geschrieben, zum Beispiel `[city] = Москва` und `[spb] = Санкт-Петербург`. Um einen Wert im Site-Titel, Seiteninhalt, Kopf- oder Fußzeile anzuzeigen, fügen Sie `[city]` ein. In PHP-Vorlagen können Sie `site_escape($settings['variables']['city'] ?? '')` verwenden.

Zu den Systemersetzungen gehören `[year]`, `[month]`, `[day]`, `[time]` (Stunden und Minuten), `[dmY]` und `[date]` (Datum TT.MM.JJJJ), `[datetime]` (Datum und Uhrzeit). Es wird die PHP-Zeitzone verwendet.

Unter „Allgemein → Website-Logos und -Symbole“ können Sie PNG, JPEG, GIF oder WebP hochladen. Downloads werden von PHP GD abgewickelt. Mit der Schaltfläche „Favicon erstellen“ wird aus dem ausgewählten oder gespeicherten Logo ein transparentes 64×64-PNG erstellt. Die Bilder werden in `templates/images/branding/` und `templates/icons/branding/` gespeichert.

„Subtemplates“ speichert den Namen, die Beschreibung und die Bedeutung jedes Codes. Erstellen Sie für das Telefon `[tel]`. Um einen Registerkartentitel über eine Untervorlage festzulegen, erstellen Sie `[title]` und geben Sie ihn im Feld `Title` auf der Seite an. Nach dem Tab-Titel wird der Name der Site angezeigt. Ersetzungen funktionieren im Seitentext und in Textmetadaten. Fügen Sie für den Link das gesamte Tag `<a>` als Untervorlagenwert ein und platzieren Sie den Code im Text zwischen den Tags. In Metadaten wird HTML von Wert in Text umgewandelt.

Fügen Sie Subdomains im Abschnitt „Domains“ hinzu. Wählen Sie im Admin-Bereich der gleichnamigen Subdomain „Aktuelle Domain“ oder öffnen Sie die gewünschte Domain aus der allgemeinen Liste. Die lokale Bedeutung hat Vorrang vor der allgemeinen Bedeutung. Die öffentliche Seite wählt Werte nach Domäne in ihrer Adresse aus und interne Links speichern sie. Um eine Subdomain zu verbinden, konfigurieren Sie den DNS- und Webserver auf Ihrem Hosting so, dass die Domain dasselbe Projektverzeichnis bedient.

Der Button „Vorlagen“ neben der Domain öffnet den Editor `[header]`, `[mobile-menu]`, `[menu]` und `[footer]`. Für jeden Block können Sie Ihren eigenen HTML-Code festlegen oder die allgemeine Version verwenden. Allgemeine Vorlagen werden im selben Abschnitt konfiguriert.

## Komponenten in HTML

| Code | Ergebnis |
| --- | --- |
| `[gal]/images/myfotos/[/gal]` | Fotokarussell mit sanftem Scrollen, Pfeilen und Wischen |
| `[fotos]/images/myfotos/[/fotos]` | Fotogalerie mit Links zu Originalen |
| `[files]/files/myfiles[/files]` | Liste der Dateien mit Symbolen nach Erweiterung |
| `[include]/contacts/[/include]` | HTML einer anderen Seite mit ihren Untervorlagen und Komponenten |
| `[form1]` | Im Abschnitt „Formulare“ konfiguriertes Formular |
| `[callback=Вызвать мастера]` | Schaltfläche und Formular für Rückrufanfrage |
| `[iblock=table]/data/myiblock.txt[iblock]` | Informationsblocktabelle |
| `[iblock=phones]/data/myiblock.txt[iblock]` | Liste der Informationsblocktelefone |
| `[phones]`, `[contacts]` | Telefone oder der gesamte konfigurierte Kontaktblock |
| `[soc-icons]` | Icons aller sozialen Netzwerke mit individuellen Links |
| `[tg]`, `[vk]`, `[whatsapp]`, `[youtube]`, `[instagram]`, `[max]` | Link zum sozialen Netzwerk mit Symbol und Signatur |

Platzieren Sie Fotos in `templates/images/myfotos/` oder `images/myfotos/`. Die Dateiliste zeigt `zip`, `pdf`, `xls`, `xlsx`, `doc`, `docx`, `png`, `gif`, `jpg`, `jpeg`, `mp4`, `mov` und `mp3`. Für `[include]` werden bis zu zehn Verschachtelungsebenen unterstützt. Die Seitenwiederholung im aktuellen Thread wird übersprungen. Platzieren Sie Komponentencodes im Hauptteil der Seite, in einer separaten Zeile zwischen HTML-Blöcken.

Legen Sie unter „Einstellungen → Kontakte und soziale Netzwerke“ Links, Nummern, Signaturen und Blocktitel fest. Unter „Einstellungen → Thema und Farben“ stehen System-, Dunkel- und Hellmodus sowie individuelle Paletten zur Verfügung. Unter „Einstellungen → Komponenten“ stellen Sie die allgemeinen Parameter des Karussells und der Fotogalerie ein. Unter den Einstellungen finden Sie Beispiele für das Einfügen mit ausgewählten Werten. Parameter im Seitencode überschreiben allgemeine Werte.

In Theme & Colors können Sie eine Kopie der ausgewählten Einstellungen unter Ihrem eigenen Namen speichern oder den Namen automatisch belassen. „Übernehmen“ ändert das aktuelle Design. „Standard verwenden“ wendet das gespeicherte Design an und speichert es für den Befehl „Design auf Standard zurücksetzen“. Beim Entfernen aus der Liste bleibt das aktuelle Design der Website erhalten.

```text
[gal arrows=off numbers=off effect=fade fit=cover]/images/myfotos/[/gal]
[fotos columns=4 ratio=1x1 fit=cover captions=on links=off]/images/myfotos/[/fotos]
[files icons=on size=on order=desc download=on]/files/myfiles/[/files]
```

Für `[fotos]` kann die Anzahl der Spalten für einen Breitbildschirm manuell eingestellt oder automatisch ausgewählt bleiben. Auf schmalen Bildschirmen reduziert das Raster die Anzahl der Spalten. Verfügbare Seitenverhältnisse sind 4:3, 1:1, 16:9 und die Modi Original, `cover` und `contain`, Signaturen von Dateinamen und Links zu Originalen.

## Formulare und Antispam

Im Abschnitt „Formulare“ können Sie `[form1]` bearbeiten, `[form2]` und die folgenden Formulare erstellen. Für das Formular werden die Adresse des Empfängers, die Schaltflächensignatur und die Felder angegeben. Das Feld kann eine maximale Länge haben. Ein leerer Wert verwendet den Standardgrenzwert für den Feldtyp. Wenn Sie `89031234567` eingeben, wird es im Telefonfeld als `+7(903)123-45-67` formatiert. Der Server prüft Länge, Typ und gültige Auswahlmöglichkeiten und begrenzt die Gesamtgröße der Anfrage. Freie Eingabefelder akzeptieren Nur-Text. HTML in Nachrichten wird bei der Ausgabe maskiert und HTML-Tags in neuen Nachrichten werden abgelehnt.

Das Kontrollkästchen „CAPTCHA verwenden“ aktiviert Google reCAPTCHA v2 mit der Prüfung „Ich bin kein Roboter“ für das ausgewählte Formular. Geben Sie den Site-Schlüssel und den geheimen Schlüssel für die Domäne ein. Das Geheimnis wird in einer geschützten Einstellungsdatei gespeichert und nur zur Serverüberprüfung verwendet. [Dokumentation zur Serververifizierung](https://developers.google.com/recaptcha/docs/verify).

Formulare werden über AJAX übermittelt. Bewerbungen werden im Bereich „Nachrichten“ gespeichert. Wenn ein Empfänger angegeben ist, wird `mail()` für Benachrichtigungen verwendet, was konfigurierte E-Mails auf dem Hosting erfordert. Im Abschnitt „Antispam“ können Sie Listen mit Wörtern, Ausdrücken sowie IPv4- und IPv6-Adressen bearbeiten. Links sind auf dem Server blockiert. Die Absenderadresse wird aus `REMOTE_ADDR` übernommen. Konfigurieren Sie hinter dem Reverse-Proxy die Wiederherstellung der ursprünglichen IP im Webserver für vertrauenswürdige Proxys.

Jedes Formular verfügt über eine „Mail-Vorlage“ mit den Feldern „An“, „Cc (Cc), „E-Mail-Betreff“ und „E-Mail-Text“. Adressen werden durch Kommas getrennt angegeben. Kopien werden versendet, wenn mindestens ein primärer Empfänger angegeben ist. Feldschlüssel werden in geschweiften Klammern in den Betreff und den Text eingefügt, zum Beispiel `Привет {name}!`. Ein leerer Feldwert wird durch einen leeren String ersetzt, der unbekannte Schlüssel bleibt im Buchstaben. Ist der Betreff oder Text leer, wird der entsprechende Teil des Standardbriefes verwendet.

## Rückrufanfragen

Fügen Sie `[callback]` oder `[callback=Вызвать мастера]` in die HTML-Seite ein. Die Schaltfläche öffnet ein Formular mit einer Telefonnummer. Anfragen werden im Bereich „Rückrufe“ gespeichert. Dort werden auch die Tastensignaturen und der Modus zur Übergabe der Anfrage an die Telefonie eingestellt. Standardmäßig ist der Anbieter „Keine“ ausgewählt und Anfragen werden nur gespeichert. Die Telefonie kann für den manuellen Versand aus der Liste oder für den Versand sofort nach Erhalt der Rufnummer aktiviert werden.

- **Zadarma.** Legen Sie die Nummer, SIP oder Betreiberdurchwahl im Admin-Panel fest und übertragen Sie die Schlüssel über `SITE_ZADARMA_KEY` und `SITE_ZADARMA_SECRET` an PHP. [Zadarma-API-Dokumentation](https://zadarma.com/en/support/api/)
- **HTTP-API.** Geben Sie `SITE_CALLBACK_WEBHOOK_URL` mit der HTTPS-Adresse und optional `SITE_CALLBACK_WEBHOOK_TOKEN` an. Die Site sendet einen POST mit den JSON-Feldern `id`, `phone`, `host`, `page`, `requested_at` und dem Header `Idempotency-Key`. Der Token wird als Bearer übertragen. Der Antwortcode 2xx überführt die Anfrage in den Status „Akzeptiert durch Telefonie“.

Erteilen Sie dem PHP-Prozess die Schreibberechtigung für `admin/callbacks/`. Anwendungen verwenden Formular-Antispam-Einstellungen. Die Wiederholung derselben Nummer ist auf eine Minute begrenzt; Über IP werden bis zu drei Anfragen pro 15 Minuten entgegengenommen.

## Informationsblöcke und geschlossener Datenordner

Im Bereich „Infoblöcke“ können Sie Datensätze erstellen, Zeilen bearbeiten und verschieben. `A–Z`, `a–z`, `0–9`, Bindestrich und Unterstrich sind im Infoblocknamen und in den Spaltenschlüsseln zulässig. Namen und Schlüssel werden ohne Leerzeichen eingegeben. Spaltennamen und Zellwerte können auf Russisch geschrieben werden. Die Steuerung entspricht den Spaltentypen und die Speicherung erfolgt über AJAX. Der ☰ „Als Text bearbeiten“-Button schaltet den gesamten Informationsblock auf CSV mit einem Trennzeichen `|` und einem Kopier-Button um. Die ersten drei Zeilen enthalten die Schlüssel, Namen und Typen der Spalten. Dann gibt es Datenleitungen. Das Datum in CSV ist `ГГГГ-ММ-ДД`, Ja/Nein wird als `1` oder `0` gespeichert.

```text
name|phone|is_open
Название|Телефон|Открыто
text|tel|bool
Справочная|+7(903)123-45-67|1
```

Die Vorlage `phones` verwendet Spalten vom Typ `tel` und eine Textspalte mit einem Titel. Sie können Ihre Vorlage in `templates/local/iblock/имя.php` platzieren, wo `$block['columns']` und `$block['rows']` verfügbar sind.

Der Pfad `/data/myiblock.txt` im Code entspricht der Datei `data/myiblock.txt.php`. Dies ist UTF-8-Text mit einem PHP-Stub, der bei direktem Aufruf 404 zurückgibt. `router.php` schließt bei lokaler Ausführung den gesamten Ordner. `data/` verfügt über Regeln für Apache und IIS. Aktivieren Sie für Apache `data/.htaccess`. Konfigurieren Sie unter Nginx den zu schließenden Ordner in der Site-Konfiguration und berücksichtigen Sie dabei das Präfix bei der Installation in einem Unterverzeichnis.

```nginx
location = /data { return 404; }
location ^~ /data/ { return 404; }
```

Überprüfen Sie nach der Installation, ob eine direkte Anfrage an `/data/` und die Infoblock-Datei eine 403 oder 404 erhält.

## Laden von HTML-Fragmenten

Die übliche Adresse `/contacts/` öffnet die ganze Seite. Die Anfrage `?ajax=1` gibt `<main>` mit H1 und verarbeitetem Inhalt zurück, wenn im Header `X-Site-Fragment` ein Sitzungsschlüssel vorhanden ist. Fragmente sind mit dem aktuellen Ursprung verknüpft. Seiten und Fragmente mit Schlüsseln werden ohne Zwischenspeicherung bereitgestellt.

Verwenden Sie in JavaScript auf einer Site-Seite die integrierte Funktion.

```js
const html = await window.siteFetchContent('/contacts/');
document.getElementById('content').outerHTML = html;
```

Übergeben Sie bei der Installation in einem Unterverzeichnis die URL mit dem Site-Präfix. Das Skript und die Stile werden von der Seite übernommen, auf der das Fragment eingefügt wird. Öffentliche Seiten bleiben unter regulären Adressen erreichbar.

## Vorlagen und Ressourcen

Unter „Einstellungen → Bibliotheken und Schriftarten“ sind jQuery, Bootstrap, Font Awesome, Material UI, Alpine.js, htmx, Chart.js und GLightbox separat aktiviert. Font Awesome wird lokal gespeichert. Die übrigen Bibliotheken werden bei Aktivierung vom Browser vom CDN heruntergeladen. Die Material-Benutzeroberfläche ist in den Skripten `type="module"` bis `import { Button } from '@mui/material'` verfügbar. Für Icons eignet sich `[fa-icon=house]` oder das übliche Markup `<i class="fa-solid fa-house"></i>`.

Es stehen 10 Google-Schriftarten mit kyrillischem Alphabet zur Verfügung. Die Auswahl ersetzt die Systemschriftart der Site. Der Wert „System“ gibt das vorherige Design zurück. Unter „Einstellungen → Theme & Farben → Gespeicherte Themes“ gibt es fünf vorgefertigte Paletten mit hellen und dunklen Optionen.

| Weg | Zweck |
| --- | --- |
| `index.php` | Startseite und allgemeiner Einstiegspunkt |
| `contacts/index.php` | Beispiel einer Seite in einem separaten Verzeichnis |
| `admin/` | Login und Seiteneditor |
| `app/site.php` | Lesen von Seiten, Erstellen von Menüs und Website-Titeln in `SITE_NAME` |
| `templates/layout.php` | Allgemeines Layout, Registerkartentitel, Metadaten und Inhaltsbereich |
| `templates/header.php`, `menu.php`, `footer.php` | Kopfzeile, Menü und Fußzeile |
| `templates/css/`, `js/`, `icons/`, `images/` | Stile, Skripte, Symbole und Bilder |
| `templates/components/`, `templates/iblock/` | Komponenten-Markup- und Infoblock-Vorlagen |
| `data/` | Informationsblockdateien, die vor direktem HTTP-Zugriff geschützt sind |

Die Engine setzt eine vollständige Seite aus `[header]`, `[mobile-menu]`, `[menu]` und `[footer]` zusammen. Dies sind die obere Leiste, das mobile Menü, das rechte Menü und die Fußzeile. Sie können durch gleichnamige Untervorlagen im Admin-Panel neu definiert werden, einschließlich separater Optionen für Subdomains. Speichern Sie PHP-Vorlagenüberschreibungen in `templates/local/` mit demselben relativen Pfad, zum Beispiel `templates/local/footer.php`.

## Motor-Update

Für eine Neuinstallation wird das vollständige Archiv benötigt. Für eine Live-Site verwenden Sie `php-site-update.zip`. Es enthält nur die Engine und Standardvorlagen. Gespeicherte Seiten, Informationsblöcke, Einstellungen, Passwörter, Nachrichten und Benutzererweiterungen werden nicht in das Archiv aufgenommen.

Klicken Sie unter „Einstellungen → Updates“ auf „Engine aktualisieren“ oder führen Sie dieselbe Prüfung über den Hosting-Planer durch.

```sh
php update.php --github
```

Die Standardquelle ist `bandaletov72/php-site-updates`. Legen Sie für Ihr eigenes Repository den Wert `SITE_GITHUB_REPO` fest. Für ein öffentliches Repository ist kein Token erforderlich. Übergeben Sie für ein privates Repository die Variable `SITE_GITHUB_TOKEN` an den PHP-Prozess mit Lesezugriff auf den Inhalt. Der Server benötigt die PHP-Erweiterungen Zip und OpenSSL sowie die Möglichkeit, HTTPS-Anfragen an GitHub zu stellen.

Für die manuelle Installation verwenden Sie ZIP.

```sh
php update.php /path/to/php-site-update.zip
```

Entpacken Sie bei der alten Version ohne `update.php` zunächst das Paket über dem Projekt und behalten Sie dabei das vorhandene `.htaccess` und die Dateien von `maintenance/`, falls vorhanden, bei. Wenn Sie Standardvorlagen bearbeitet haben, übertragen Sie die Änderungen vor der Aktualisierung an `templates/local/`. Das Installationsprogramm überprüft die Liste und Prüfsummen der Dateien und für GitHub außerdem die Signatur mit dem vertrauenswürdigen öffentlichen Schlüssel `maintenance/release-public.pem`.

Ändern Sie für die nächste Version die Version in `app/version.php` und erstellen Sie das Paket über `php update.php --build php-site-update.zip`. Signieren Sie die ZIP-Datei mit Ihrem privaten RSA-Schlüssel mit SHA-256 und bewahren Sie den privaten Schlüssel getrennt von der Site auf. Schieben Sie dann die ZIP-Datei und `.sig` auf GitHub Release mit dem Tag `vX.Y.Z` oder auf `updates/vX.Y.Z/` und aktualisieren Sie `updates/latest.json` auf `{"version":"X.Y.Z"}`.

## Erweiterungen

Engine-Module können zusammen mit Updates zu `app/extensions/` hinzugefügt werden. Speichern Sie Ihre eigenen Module in `extensions/`, außerhalb des Update-Pakets. Die PHP-Datei gibt ein Array von Definitionen zurück. Bei einer einzelnen Komponente ist `kind` gleich `single` und bei einer Komponente mit einem Argument ist `wrapped` gleich. Die Engine erkennt neue Codes auf bestehenden Seiten. Eine Komponente kann in `settings` ein Menü mit Optionen deklarieren und jede Option auf `label`, `default` und das Array `options` festlegen. Die Werte werden in `$context['settings']` empfangen.

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

Die Beispielkomponente wird als `[notice]Важное сообщение[/notice]` eingefügt.

## Neu in Version 1.6.3

### Startseite und Datenschutzbestimmungen

Öffnen Sie „Designvorlagen“, um die Startseite und die Seite /privacy.php zu bearbeiten. Der Code [page-content] zeigt den Inhalt der Hauptseite an. In der Richtlinie [site-name] und [site-url] übernehmen Sie den Namen und die öffentliche Adresse aus „Einstellungen → Allgemein“. Vervollständigen Sie den Anfangstext mit Informationen zu Ihrer Website und der Datenverarbeitung.

### Cookie und zurück nach oben

In der Fußzeile stellt [conf-policy] einen Link zur Richtlinie bereit, [cookies] öffnet die Einstellungen und [top] scrollt zum Seitenanfang. Analysen und integrierte CMS-Karten warten auf Zustimmung. Manuell zu Vorlagen oder Service-Headern hinzugefügte Skripte erfordern eine eigene Konfiguration.

### Herausgeber functions.php

Öffnen Sie „Designvorlagen → Funktionen“ nach der Registerkarte „Service-Header“. Fügen Sie dem zurückgegebenen Array Abschlüsse hinzu. Rufen Sie in Ihrer PHP-Vorlage `translit("Москва")` oder `ew_call("translit", "München")` auf. Fügen Sie im HTML [func=translit value="Москва и München"] ein. Das Beispiel erzeugt `Moskva i Muenchen`.

### Bearbeiten von Standardvorlagen

Im Feld „Design-Vorlagen“ ist bereits der PHP- und HTML-Quellcode mit Hervorhebung enthalten. Wählen Sie beim Bearbeiten einer Standardvorlage „Benutzerdefiniertes PHP“ aus. Beim Speichern wird eine benutzerdefinierte Version erstellt. Mit den Schaltflächen neben der Vorlage können Sie die letzte Bearbeitung rückgängig machen oder das Original wiederherstellen.

### PHP-Prüfung vor dem Speichern

Designvorlagen-, Modul- und functions.php-Editoren überprüfen die Syntax mit dem Befehl `php -l`. Im Fehlerfall bleibt die vorherige Version bestehen. Um die PHP-CLI auf dem Server auszuwählen, verwenden Sie die Variable `SITE_PHP_LINT_BINARY`.

### Hintergrundbeleuchtung und kompakte Suche

CodeMirror ist bei Neuinstallationen standardmäßig aktiviert. Die Einstellung befindet sich unter „Bibliotheken und Schriftarten“. Bei der kompakten Suche wird das Feld nach links erweitert, das Kreuz löscht die Zeile und die Eingabetaste startet die Suche.

### Admin-Menü und Bearbeitungsfeld

Im Menü unter der Kopfzeile des Admin-Panels und im Bearbeitungsfeld auf der Website werden Symbole mit Beschriftungen angezeigt. Symbole bleiben auf dem schmalen Bildschirm. Tooltips öffnen sich beim Hover und Tastaturfokus auf einem schmalen Bildschirm. „Ausblenden“ befindet sich neben den restlichen Seitenaktionen. Der Bearbeitungsmodus-Schalter befindet sich auf der rechten Seite.

### Beschreibung und Code des PHP-Moduls

In „PHP-Modulen“ handelt es sich bei der Kurzbeschreibung um Klartext. Fügen Sie den Hauptteil der Funktion in ein separates Feld „Quellcode · PHP“ ein.

## Schaltflächen im Kopfbereich der Verwaltung · 1.6.4

Der Globus öffnet die Website, der Pfeil meldet Sie ab. Auf Mobilgeräten sind nur die Symbole zu sehen. Fahren Sie mit der Maus darüber oder fokussieren Sie die Schaltflächen per Tastatur, um die Beschriftung zu sehen.

Inhalte, Formulare und Anfragen sowie Website und Dienste öffnen sich direkt. Unter dem Hauptmenü erscheinen die Registerkarten des gewählten Bereichs.

Fügen Sie [bread-crumbs] oder [search] in das HTML der Startseite (index.php) oder einer Vorlage ein. Jeder Block erscheint einmal an der gewählten Stelle. Die Einstellungen für Brotkrümelnavigation und Suche gelten weiterhin. [privacy] verweist auf /privacy/. Bearbeiten Sie die Datenschutzerklärung unter Seiten.

```text
[bread-crumbs]
[search]
[privacy]
```

## Konfigurationsdateien, Karten und Vorlagen · 1.6.5

Unter Wartung → Konfigurationsdateien bearbeiten Sie .htaccess, robots.txt und sitemap.xml. Die Sitemap enthält angekreuzte veröffentlichte Seiten. Die vorige .htaccess-Version ist wiederherstellbar.

Geben Sie Breiten- und Längengrad ein. [map] zeigt den Ort der Seite, [maps] sammelt die Orte veröffentlichter Seiten. center und zoom bestimmen den Ausschnitt. Der Kartenanbieter steht unter Integrationen.

[main-menu] ist das Menü unter dem Kopfbereich mit ausklappbaren Unterpunkten. Eigene Vorlagen und die Gerätesichtbarkeit werden unter Layoutvorlagen eingestellt. Beim Hinzufügen einer Domain in den Textbausteinen öffnet sich das Domainformular. Verwenden Sie [privacy] für den Datenschutzlink.

Unter Integrationen können Sie die IDs von JivoSite, Microsoft Clarity und Umami Cloud, den Botfaqtor-Code oder einen Slack-Webhook speichern. Anleitungen stehen neben den Feldern. In Zadarma prüfen Sie den Kontostand oder testen SMS und Anrufe. Tests können kostenpflichtig sein. Für [payment] hinterlegen Sie Links von YooMoney, PayPal und SBP. Den Zahlungsstatus sehen Sie beim Anbieter.

Im HTML- und visuellen Editor finden Sie Symbole für Formatierung, H1–H5 und Listen. Setzen Sie den Cursor und wählen Sie Vorlage, Komponente, Bild oder Symbol. Formulare und Komponentenoptionen stehen zur Auswahl. Font Awesome ist bei aktivierter Bibliothek verfügbar.

Klicken Sie auf einen Ordnernamen, um den Bereich rechts zu verwalten. Wählen Sie einen übergeordneten Bereich oder stellen Sie Desktopmenü, Mobilmenü und Weiterleitungs-URL ein. Eine leere Bereichsseite listet Unterbereiche auf. Unter Wartung bearbeiten Sie Regeln wie /contact.html => /contacts/ und Seiten in /error/. Sprache und Suche haben mobile Symbole.

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

## Komponentendaten direkt bearbeiten · 1.6.6

Im Bearbeitungsmodus bieten Infoblöcke, Formulare [formX], Textbausteine [tel] und andere Komponenten mit Dateneditor die Optionen Daten und Vollbild. Die Ansichtsliste enthält registrierte Vorlagen. Wählen Sie Stammordner für die oberste Ebene. [list-pages] listet veröffentlichte Seiten im Bereich und seinen Unterordnern. Schließen Sie das Sprachmenü durch einen Klick außerhalb oder mit Escape.

## Mobilmenü · 1.6.7

- Im Hauptmenü anzeigen
- Im Mobilmenü anzeigen
- Im Mobilmenü ausblenden

## Seiteneinstellungen · 1.6.8

Erstellen Sie Inhalte unter „Bearbeiten“. Adresse, Menü und Veröffentlichungszeit stehen in den Seiteneinstellungen. SEO enthält Titel, Beschreibung, Schlüsselwörter und die Aufnahme in die Sitemap. Unter „Karten“ stehen Koordinaten und Beispiele.

Leere Datumsfelder bedeuten keine Einschränkung. Es gilt die Zeitzone der Website. Verborgene Seiten müssen separat veröffentlicht werden.

- `[map]` — Karte mit dem Standort der aktuellen Seite.
- `[maps]` — Karte mit den Standorten aller veröffentlichten Seiten.

## Banner und Bildeditor · 1.6.8

Verwalten Sie Sets und Folien unter Komponenten. Verwenden Sie Fotos aus einem Ordner, legen Sie Texte und Schaltflächen für Links, Formulare oder Rückrufe an und planen Sie die Anzeige. Im Bearbeitungsmodus erscheinen Aktionen an der Folie. Die Bildauswahl unterstützt Ordner, Uploads, Löschen und gedrehte oder skalierte Kopien mit GD. Font-Awesome-Symbole sind nach Namen suchbar.

```text
[banners]
[banners set=summer arrows=on numbers=off]
[banners]/images/myfotos/[/banners]
```

## Sprachen · 1.6.9

Der Tab „Sprachen“ listet die Seitensprachen auf. „Öffnen“ führt zur gespeicherten Version. „Übersetzung erstellen“ öffnet einen leeren Editor.

Ein Unterbereich entsteht im aktuellen Bereich, ein gleichrangiger Bereich daneben und ein Hauptbereich auf der obersten Ebene der Website.

## PHP-Kompatibilität und Navigation · 1.6.10

Die Mindestversion ist PHP 7.2. Website und Administration unterstützen PHP 8.6. Verwenden Sie in eigenen Modulen und Vorlagen die Syntax der PHP-Version Ihres Servers.

Der aktuelle Eintrag in [bread-crumbs] zeigt veröffentlichte Bereiche derselben Ebene aus dem Hauptmenü. Die eingestellte Seiten-URL gilt für Menüs, Bereichslisten, Brotkrümelnavigation, Suchergebnisse und Vorschläge.

Der Papierkorb entfernt eine Zeile aus dem Infoblock-Editor. Beim Entfernen einer Spalte gehen alle ihre Werte verloren. Änderungen werden beim Speichern übernommen, auch automatisch nach dem Umsortieren.

### Sicherungen der Website

Das private ZIP enthält Seiten, Medien, Infoblöcke, Einstellungen, eigene Vorlagen und PHP-Module. Administratorkonto und Anfragen werden separat gespeichert. PHP Zip wird benötigt.

Inhalte und Einstellungen ersetzen. Zuerst wird der aktuelle Stand gesichert.

### Ungenutzte Dateien bereinigen

Der Scan sucht Medien ohne Verweise in gespeicherten Inhalten. Prüfen Sie die Liste und wählen Sie Dateien aus. Zuvor wird eine Sicherung erstellt. Beim Entfernen eines Komponentencodes bleiben Ordner und Dateien erhalten.

### Punkt auf der Karte wählen

Erstellen Sie in der Yandex-Konsole einen Schlüssel für „JavaScript API“ und begrenzen Sie ihn per HTTP-Referrer. Wählen Sie in der Karten-Registerkarte der Seite einen Punkt und übernehmen Sie seine Koordinaten.

Markieren Sie Text zum Ausrichten oder zum Entfernen seiner Formatierung. Klicken Sie auf ein Bild, um Eigenschaften zu ändern. Ziehen Sie es zum Verschieben oder ziehen Sie an seiner Ecke. Links unterstützen URL, E-Mail, Telefonnummer und Anker. Schaltflächen öffnen URLs, Formulare, Rückrufe oder Zahlungslinks des Anbieters.

HTML und visueller Editor wechseln den Modus per Tab. SUP, SUB und Absatz fügen die passenden Elemente ein. Komponenten zeigen ihren Einfügecode. Fehlende Medienordner werden beim Einfügen erstellt. Namen nutzen lateinische Buchstaben, Ziffern, Bindestriche und Unterstriche. Der Tab „Sprachen“ führt zu den Spracheinstellungen. Die Einfügeoptionen öffnen sich oberhalb des Bearbeitungsfelds. Mit „Schließen“ oben im Bereich oder mit Esc schließen Sie ihn und deaktivieren die Schaltfläche. Die Pfeile in der Werkzeugleiste machen bis zu 10 Textänderungen über den Server rückgängig oder wiederholen sie. Das Uhrsymbol lädt den zuletzt gespeicherten Text. Beim Verlassen der Seite mit ungespeicherten Änderungen ist eine Bestätigung nötig. Das Speichern-Symbol vor den Pfeilen leuchtet bei ungespeicherten Änderungen. Markieren Sie einen Abschnitt und öffnen Sie „Text formatieren“. Legen Sie Schriftgröße, Text- oder Hintergrundfarbe fest oder wählen Sie eine in den Website-Einstellungen aktivierte Schriftart.

```text
[button action="url" label="Contacts" url="/contacts/"]
[button action="form" label="Write to us" form="form1"]
[button action="callback" label="Call me"]
[button action="payment" label="Pay" amount="1250.00" currency="RUB" yoomoney="410010000000000"]
[map]
[maps]
```

## Verkauf · 1.6.11

Legen Sie Preis und Zahlungsmethode im Verkaufstab fest, optional pro Sprache. Fügen Sie [item-price] und [item-buy-button] ein. Die Bestellung wird vor dem Checkout registriert. Statusmeldungen können per E-Mail und Zadarma-SMS gesendet werden.

Eingegangen · Zahlung angenommen · Versandt · Abgeschlossen · Storniert

```text
[item-price]
[item-buy-button]
```

Wählen Sie eine Datei oder URL für ein Bild aus. Klicken Sie auf das Bild für Größe, Ausrichtung, vier Außenabstände und CSS. Die Bildausrichtung ist unabhängig vom Text. In HTML stehen Blöcke in eigenen Zeilen. [list-pages] steht in der Komponentenliste. Das CMS-Logo verwendet die Farben des aktiven Designs.

## Diagramm aus Datenblock

Öffnen Sie im Seiteneditor die Komponentenauswahl und wählen Sie Tabelle oder Diagramm. Aktivieren Sie Chart.js für Diagramme. Wählen Sie eine Beschriftungsspalte und Spalten vom Typ Zahl oder Preis. Kreis-, Ring- und Polarflächendiagramme verwenden eine nichtnegative Datenreihe. Ohne Bibliothek erscheint die Tabelle.

Beim erneuten Öffnen des Editors erscheinen gespeicherte Formulare, Vorlagen, Textbausteine und registrierte Komponenten. Die ID eines neuen Banner-Sets erlaubt A–Z, a–z, Bindestriche und Unterstriche. Nach dem Hochladen in einen [banners]-Ordner erscheint neben jeder Datei ein Link zur Folienbearbeitung. Im Bearbeitungsmodus öffnet sich nur die gewählte Folie im Dialog. Die Standardeinstellungen sind separat verlinkt. Neben Speichern öffnen Karussell, Galerie und Dateiliste einen Dateimanager zum Hochladen, Löschen, Sortieren und Anlegen von Unterordnern (A–Z, a–z, 0–9, - und _). Folienfarben werden als Quadrate angezeigt.

```text
[iblock=table]/data/myiblock.txt[/iblock]
[iblock=charts type=line labels=month values=sales,orders]/data/myiblock.txt[/iblock]
[banners set=summer-promo]
```

## Kopf- und Fußzeile der Verwaltung · 1.6.12

Auf dem Desktop stehen Eurowebcart CMS und Light mit der installierten Version neben dem Logo. Auf dem Handy stehen Logo und Schaltflächen in einer Zeile. Die Fußzeile verlinkt das Repository. Kumulative Updates lassen sich in den Einstellungen prüfen.

## Kopf- und Fußzeile der Verwaltung · 1.6.13

Auf dem Desktop stehen Eurowebcart CMS und Light mit der installierten Version neben dem Logo. Auf dem Handy stehen Logo und Schaltflächen in einer Zeile. Die Fußzeile verlinkt das Repository. Kumulative Updates lassen sich in den Einstellungen prüfen. Die Standardbezeichnung Startseite in der Seitenliste folgt der Verwaltungssprache. Im Fußzeilenlogo stehen Eurowebcart CMS und LIGHT auf zwei Zeilen.

## PHP-Informationen

Unter Wartung zeigt phpinfo() angemeldeten Administratoren die PHP-Version, Module und Konfiguration. Umgebungs- und Anfragevariablen bleiben verborgen. error_reporting steht in den allgemeinen Einstellungen. Standardmäßig gilt die Servereinstellung.

## Seitenbaum ausblenden · 1.6.14

Am Desktop klappt der Pfeil den Seitenbaum ein und verbreitert den Editor. Die Schaltfläche über dem Editor blendet ihn wieder ein. Der Browser merkt sich die Auswahl.

## Seitenbaum ausblenden · 1.6.15

Am Desktop klappt der Pfeil den Seitenbaum ein und verbreitert den Editor. Die Schaltfläche über dem Editor blendet ihn wieder ein. Der Browser merkt sich die Auswahl.

## Punkt auf der Karte wählen

Erstellen Sie in der Yandex-Konsole einen Schlüssel für „JavaScript API“ und begrenzen Sie ihn per HTTP-Referrer. Wählen Sie in der Karten-Registerkarte der Seite einen Punkt und übernehmen Sie seine Koordinaten. Tragen Sie für lokale Tests localhost ohne Port als HTTP-Referrer ein und öffnen Sie die Verwaltung nach Aktivierung über localhost.

## Suche im Menü · 1.6.16

[search] steht auf dem Desktop neben den Links von [main-menu] und auf dem Handy in [mobile-menu]. Den Suchmodus wählen Sie in den allgemeinen Einstellungen. [search] im Seiteninhalt hat Vorrang.

`[search]`

## Suche im Menü · 1.6.17

[search] steht auf dem Desktop neben den Links von [main-menu] und auf dem Handy in [mobile-menu]. Den Suchmodus wählen Sie in den allgemeinen Einstellungen. [search] im Seiteninhalt hat Vorrang. Im mobilen Menü ist das Suchfeld sofort sichtbar. [lang] zeigt die Sprachauswahl im mobilen Menü und im Desktop-Kopfbereich.

`[search]` · `[lang]`

Unten im mobilen Menü erscheinen die konfigurierten Kontaktwege und sozialen Netzwerke.

## Untertitel der Website

Der Untertitel aus den allgemeinen Einstellungen steht in kleiner Schrift unter dem Namen im Kopfbereich. Auf Handys ist der Name ebenfalls kleiner.

## Städte

Unter Städte können Sie eine registrierte Domain zuordnen und bis zu fünf Telefonnummern hinterlegen. Im Nummernfeld können Sie einen Code wie [tel] verwenden. [cities] zeigt die Stadtauswahl, [phones] die hinterlegten Nummern und [tel], [tel2]–[tel5] einzelne Nummern. Der Browser speichert die Auswahl und aktualisiert die Nummern ohne Neuladen.

`[cities]` · `[phones]` · `[tel]`

## Animation beim Scrollen

Wählen Sie in den allgemeinen Einstellungen den Effekt für Inhaltsblöcke beim Scrollen. Standardmäßig ist die Animation aus und berücksichtigt die Systemeinstellung für reduzierte Bewegung.

## Seitenvorlagen

Wählen Sie beim Erstellen einer Seite eine Vorlage. Sie füllt Inhalt und SEO-Felder aus, die Sie danach bearbeiten können. Eigene Vorlagen fügen Sie unter Seitenvorlagen hinzu. Karten, Formulare und Käufe verwenden ihre jeweiligen Komponenteneinstellungen.

CSS und JavaScript der Vorlage werden in die Seitenfelder kopiert. Eigene HTML-Klassen können Sie im CSS der Vorlage gestalten.

### Wiederverwendbare Klassen

Container, responsives Raster, hervorgehobener Abschnitt, Karte und betonter Preis.

`.page-preset` · `.page-preset-grid` · `.page-preset-hero` · `.page-preset-card` · `.page-preset-price`

Die Layoutvorlagen sind anfangs eingeklappt. Öffnen Sie eine Vorlage über ihre Überschrift oder wählen Sie Alle aufklappen. Erneutes Drücken klappt die Liste ein.

Um die Breite des gesamten Bannerkarussells zu ändern, aktivieren Sie den Bearbeitungsmodus, öffnen Sie Komponente bearbeiten und wählen Sie die Blockbreite.

## Städte · Telefon · 1.6.18

Nur der Stadtname ist erforderlich. Domain und Telefonnummern sind optional. Geben Sie + und die Landesvorwahl an, zum Beispiel +7 (900) 000-00-00. Sie können auch einen Code wie [tel] verwenden.

Zuerst gilt die gespeicherte Stadt, dann die Domain-Stadt und danach der erste Listeneintrag. Ohne Städte werden die allgemeinen Telefonnummern angezeigt.

Formulare, Rückrufe und neue Bestellungen gehen an die Stadtadresse oder an die Standardadresse der Website. Sind beide leer, behalten Formulare ihre eigenen Empfänger. Leere Social-Media-Links werden geerbt.

Im Bearbeitungsmodus erscheint beim Überfahren einer Vorlage ein Bearbeitungslink. Bei allen installierten [php]-Komponenten öffnet Modul bearbeiten den Quellcode und die Einstellungen im Dialog. Der Folien-Editor zeigt Formularname und Code. [banners width=100] füllt als eigenständiger Block ohne Seitenleiste die Fensterbreite. Der Vorlageneditor öffnet sich in einem Dialog auf der Website.

Der Dateimanager prüft vor dem Löschen die Referenzen und sichert nicht leere Ordner. Banner können einen Titel und einen Untertitel haben.

Die Telefonnummer wird bei der Eingabe geprüft. Bei russischen Nummern wird eine führende 8 zu +7. Nach dem Senden wird das Formular ausgeblendet und beim erneuten Öffnen erscheint die Bestätigung.

Bei Nummern ohne + wird die gewählte Ländervorwahl ergänzt. Nummern mit + behalten ihre Vorwahl.

## Design-Themes

Kopieren Sie die Datei in das Theme und speichern Sie sie. Fehlende Dateien kommen aus dem Standardpaket. Änderungen, neue Layoutcodes, Verlauf und Domain-Einstellungen bleiben im aktiven Theme. Zusätzliche Stile und Skripte stehen in css/theme.css und js/theme.js.

`templates/themes/demo/header.php` · `templates/themes/demo/components/banners.php`

Die Sprachen stehen unten im mobilen Menü. Am Desktop steht der Globus neben der Suche im Menü unter dem Kopfbereich.

Galerien, Banner und Dateilisten suchen zuerst im aktiven Theme und verwenden sonst gemeinsame Dateien. Neue Ordner, Uploads, Logos und Favicons landen im Theme. Die Sicherung umfasst Theme-Ordner und Einstellungen.

Wählen Sie Fensterbreite (width=page), Inhaltsbreite (width=content) oder die Breite des übergeordneten Blocks (width=parent). height gilt am Desktop, mobile-height am Handy. auto behält die natürliche Größe.

`[banners width=page height=520 mobile-height=240]` · `[form1 width=parent]`

Das letzte Element der Brotkrumennavigation listet veröffentlichte gleichrangige Bereiche, auch wenn sie im Hauptmenü fehlen. Links verwenden die eingestellte URL.

## Design-Themes · КОНТУР · 1.6.19

Ein vollständiges Website-Theme wechselt Design, Seiten, Datenblöcke und Einstellungen. Andere Themes verwenden die gemeinsamen Seiten.

Ein Häkchen markiert eine Theme-Datei, die Vorrang vor der CMS-Standarddatei hat. Sie kann bearbeitet oder wiederhergestellt werden.

**Werkseinstellung wiederherstellen**

### Portfolio

Öffnen Sie kontur-works in Inhalte → Datenblöcke. Geben Sie Titel, Kategorie, Beschreibung, kommagetrennte Fotos und optional einen Link ein. Das erste Foto ist das Titelbild. Verwenden Sie dieselben Spalten für weitere Sammlungen.

```text
[portfolio]
[iblock=portfolio limit=2]/data/kontur-works.txt[/iblock]
[maps source=kontur-map]
```

### Websitefarben · Schriften

Aktivieren Sie Schriften per Kontrollkästchen und weisen Sie sie Texten, Überschriften oder Bedienelementen zu. Lokale Dateien lädt Ihre Website.

Bibliotheken → Lokal speichern

Seitenvorlagen → Seite erstellen
