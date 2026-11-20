# Vorlagen und Komponenten

## Layoutvorlagen

### Kopfzeile, Menü und Fußzeile

Die ganze Seite verbindet diese Blöcke automatisch. Allgemeine Versionen werden in „Designvorlagen“ und für eine einzelne Domain im Abschnitt „Domains“ bearbeitet.

```text
[header]
[mobile-menu]
[menu]
[footer]
```

### Einfügen einer weiteren Seite

Fügt den Inhalt einer veröffentlichten Seite ein. Die maximale Verschachtelung beträgt zehn Ebenen. Das Wiederholen einer Seite in derselben Kette wird übersprungen.

```text
[include]/contacts/[/include]
```

### Links im Menü

Das Kontrollkästchen „Im Menü anzeigen“ fügt einen Link zu den Standardmenüs hinzu. Wenn der Name leer ist, wird der Titel übernommen. Wenn Sie das Kontrollkästchen für eine Ordnerseite deaktivieren, verschwinden auch deren Unterseiten aus dem Menü.

```text
MenuVisible: 1
Menü: Kontakte
```

### CSS und JavaScript

Der allgemeine Code wird im Reiter „CSS und JS“ bearbeitet. Der Seitencode wird im Block „CSS- und JavaScript-Seiten“ bearbeitet und nach dem allgemeinen Code eingefügt. Fügen Sie den CSS- und JavaScript-Inhalt in die Felder ein.

```text
.page-content { max-width: 72rem; }
```

```text
document.documentElement.classList.add('custom-script-loaded');
```

### Service-Header

Im Abschnitt „Designvorlagen → Service-Header“ können Sie allgemeine Meta-Tags und Zählercodes innerhalb des Headers oder vor dem schließenden Body einfügen. Canonical wird automatisch auf ganzen Seiten hinzugefügt.

```text
<meta name="theme-color" content="#065f63">
```

### Fantastische Schriftsymbole

Font Awesome Free 7.3.1 ist verbunden. Der Kurzcode funktioniert in Inhalts- und HTML-Designblöcken. Es sind einfarbige, reguläre und Markenmodelle erhältlich. Fügen Sie Text neben dem Symbol hinzu, wenn es eine Aktion darstellt.

```text
[fa-icon=house] [fa-icon=regular:envelope] [fa-icon=brands:github]
```

```text
<i class="fa-icon fa-solid fa-phone" aria-hidden="true"></i> Kontakt
```

### Bibliotheken und Schriftarten

Verbindungen werden unter „Einstellungen → Bibliotheken und Schriftarten“ verwaltet. Verwenden Sie für die Material-Benutzeroberfläche modulares JavaScript. Die ausgewählte Google-Schriftart ersetzt die Systemschriftart der Website.

```text
<script type="module">import { Button } from "@mui/material";</script>
```

```text
GLightbox({selector: ".glightbox"});
```

## Variablen, Untervorlagen und Datum

### Site-Variablen

Fügen Sie Einträge zu „Einstellungen → Allgemein“ hinzu. Fügen Sie den Schlüssel in den Text, die Kopfzeile, den Seitentitel oder die Fußzeile ein.

```text
[city] = Moskau
[spb] = St. Petersburg
```

```text
Service in der Stadt [city]
```

### Textbausteine

Geben Sie im Abschnitt „Untervorlagen“ einen Namen, eine Beschreibung und einen Wert an. Für eine Domäne wird zuerst ihr Wert genommen, dann ihr allgemeiner Wert. Die Untervorlage kann HTML enthalten. Das Markup in den Kopfzeilen wird entfernt.

```text
[tel]
```

```text
[title]
```

### Datum und Uhrzeit

Die Zeitzone wird von PHP übernommen. [year] – Jahr, [month] – Monat, [day] – Tag des Monats, [time] – Stunden und Minuten, [dmY] und [date] – Datum TT.MM.JJJJ, [datetime] – Datum und Uhrzeit.

```text
© [year] [city]
Datum [dmY], Uhrzeit [time]
```

### CMS-Name und Autor

Der Code [author] gibt Eurowebcart CMS Light, den Autorennamen Бандалетов Павел und einen Link zu GitHub aus. In einer Standardfußzeile erscheint die Signatur automatisch. Fügen Sie in Ihrer eigenen HTML-Fußzeile den Code an der gewünschten Stelle ein.

```text
[author]
```

### Home-Vorlage und -Richtlinie

[page-content] fügt den Inhalt der Startseite in [home] ein. Öffnen Sie /privacy/ unter Seiten, um den Text zu bearbeiten. [site-name] und [site-url] stammen aus den allgemeinen Einstellungen.

```text
[page-content]
```

```text
[site-name] · [site-url]
```

### Links und Einstellungen im Footer

[privacy] verweist auf /privacy/, [top] scrollt nach oben und [cookies] öffnet die Einwilligungseinstellungen.

```text
[privacy]
[cookies]
[top]
```

### Allgemeine PHP-Funktionen

Bearbeiten Sie im Abschnitt „Designvorlagen → Funktionen“ functions.php. Im PHP-Code ruft translit() oder ew_call() eine registrierte Funktion auf. Der Shortcode [func=translit] ist im Inhalt und im Feld „Custom HTML“ verfügbar.

```text
[func=translit value="Москва и München"]
```

```text
$var = translit("Moskau");
```

```text
echo ew_escape(ew_call("translit", "München"));
```

## Karussell, Fotogalerie und Dateien

### Karussell [gal]

Zeigt Fotos aus einem Ordner an. Pfeile und Zahlen akzeptieren Ein oder Aus, Effekt – Schieben, Ausblenden oder Nichts, Anpassen – Einschließen oder Abdecken.

```text
[gal]/images/myfotos/[/gal]
```

```text
[gal arrows=on numbers=off effect=fade fit=cover width=50]/images/myfotos/[/gal]
```

### Fotogalerie [fotos]

Zeigt ein reaktionsfähiges Raster. Spalten – automatisch oder eine Zahl von 1 bis 6, Verhältnis – 4x3, 1x1, 16x9 oder Original, passen – abdecken oder enthalten. Beschriftungen und Links werden durch Ein oder Aus aktiviert.

```text
[fotos]/images/myfotos/[/fotos]
```

```text
[fotos columns=4 ratio=1x1 fit=cover captions=on links=off width=50%]/images/myfotos/[/fotos]
```

### Liste der Dateien [files]

Listet unterstützte Dateien auf. Symbole, Größe und Download akzeptieren ein- oder ausschalten, Reihenfolge – auf- oder absteigend. Erlaubt sind Zip, PDF, XLS, XLSX, Doc, DocX, PNG, GIF, JPG, JPEG, MP4, MOV und MP3.

```text
[files]/files/myfiles/[/files]
```

```text
[files icons=on size=on order=desc download=on width=33]/files/myfiles/[/files]
```

## Formulare und Rückruf

### Formular [formN]

Konfigurieren Sie im Abschnitt „Formulare“ die Felder, ihre maximale Länge, den Schaltflächentext und den Empfänger. „CAPTCHA verwenden“ aktiviert das Google reCAPTCHA v2-Widget mit Antwortverifizierung auf dem Server. Anfragen werden über AJAX gesendet und sind im Abschnitt „Nachrichten“ sichtbar.

```text
[form1]
[form2]
```

### E-Mail-Formularvorlage

Betreff und Text werden für jedes Formular separat gespeichert. Fügen Sie in Ihrer E-Mail-Vorlage Feldschlüssel in geschweiften Klammern ein. Die Listen der Empfänger und Kopien werden durch Kommas getrennt.

```text
Hallo {name}!
Telefon: {phone}
Nachricht: {message}
```

### Rufen Sie [callback] zurück

Öffnet ein Formular mit einer Telefonnummer. Die Bewerbung wird im Bereich „Rückrufe“ gespeichert. Die Schaltflächenbeschriftung kann im Code festgelegt werden. Beim Anbieter „Nein“ ist das Senden an die Telefonie deaktiviert.

```text
[callback]
[callback=Вызвать мастера]
```

## Datenblöcke

### Tabelle und Liste der Telefone

Erstellen Sie im Abschnitt „Infoblöcke“ einen Datensatz. A–Z, a–z, 0–9, Bindestrich und Unterstrich sind im Infoblocknamen und in den Spaltenschlüsseln zulässig. Leerzeichen sind nicht erlaubt. Die Tabellenvorlage zeigt eine Tabelle an, Telefone zeigen Telefonspalten mit Links zum Anrufen an. Die Abschlusscodes [iblock] und [/iblock] werden unterstützt.

```text
[iblock=table]/data/myiblock.txt[iblock]
[iblock=phones]/data/myiblock.txt[/iblock]
```

## Kontakte und soziale Medien

### Allgemeiner Block und Symbole

[contacts] zeigt einen Kontaktblock an, [phones] zeigt Telefonnummern an, [soc-icons] sammelt Symbole aller sozialen Netzwerke mit benutzerdefinierten Links.

```text
[contacts]
[phones]
[soc-icons]
```

### Einzelne soziale Netzwerke

Jeder Code zeigt einen Link zu seinem sozialen Netzwerk an. Adressen und Signaturen werden unter „Einstellungen → Kontakte und soziale Netzwerke“ eingestellt.

```text
[tg] [vk] [whatsapp] [youtube] [instagram] [max]
```

```text
[wa] [yt] [ig]
```

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
