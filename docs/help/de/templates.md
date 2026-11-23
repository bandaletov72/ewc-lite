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

HTML und visueller Editor wechseln den Modus per Tab. SUP, SUB und Absatz fügen die passenden Elemente ein. Komponenten zeigen ihren Einfügecode. Fehlende Medienordner werden beim Einfügen erstellt. Namen nutzen lateinische Buchstaben, Ziffern, Bindestriche und Unterstriche. Der Tab „Sprachen“ führt zu den Spracheinstellungen.

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

## Ankündigung

Die Ankündigung erscheint in Seitenlisten und Suchergebnissen. Listen zeigen das Bild, falls vorhanden, sonst das Symbol. Im Menü steht das Symbol vor dem Seitennamen.

Klicken Sie im Editor auf „Seitenliste einfügen…“. Wählen Sie den aktuellen Abschnitt für `[list-pages]` oder klicken Sie neben einem Ordner auf „Einfügen“. `[dir]/sample/[/dir]` listet veröffentlichte Seiten in /sample/ und seinen Unterabschnitten auf. `[dir]/[/dir]` beginnt im Stammverzeichnis.


Geben Sie einen Namen in die Symbolsuche ein. Home findet auch Symbole mit house im Namen.

Öffnen Sie Seitenkomponenten → [banners] → Bannerfolien, um Folien zu bearbeiten, hinzuzufügen oder umzusortieren. Komponente bearbeiten öffnet die Blockoptionen.

Die Felder einer Datenzeile sind oben ausgerichtet. Ziehen Sie an der Ecke eines mehrzeiligen Feldes, um Breite und Höhe zu ändern.

Ziehen Sie den rechten Rand der Überschrift oder verwenden Sie die Pfeiltasten links und rechts, um die Spaltenbreite zu ändern.

## Karte der Abschnittsseiten · `[maplist]`

Wählen Sie „Karte der Abschnittsseiten“ oder fügen Sie `[maplist]` ein. Die Karte zeigt veröffentlichte Seiten mit Koordinaten im aktuellen Abschnitt und seinen Unterabschnitten. Auf der Startseite umfasst sie die ganze Website. Das Kartenfenster zeigt Titel, Symbol, ein Bild über die ganze Breite, Ankündigung und Link. Richten Sie die Karte unter Integrationen ein.

`[maplist]/sample/[/maplist]` zeigt die Karte des gewählten Abschnitts. `[points]/sample/[/points]` listet Seiten mit Koordinaten als Karten mit Titel, Bild, Ankündigung und Link zur Seite auf. Die Schaltfläche „Route öffnen“ verwendet die Koordinaten des Punkts und den unter Integrationen gewählten Kartendienst.

Die Einstellung zur Sitemap befindet sich im SEO-Tab und gilt bei der Erzeugung von sitemap.xml. Aktivieren Sie in den Seiteneinstellungen „Menüzweig ausklappen“. Im Menü sichtbare Unterseiten öffnen sich beim Überfahren oder durch Tippen auf den Pfeil.

Fügen Sie `[route]` auf einer Seite mit Koordinaten ein. Der Link verwendet den Anbieter aus den Integrationen. Mit provider=yandex oder provider=google wählen Sie ihn direkt. Sind Karten deaktiviert, wird standardmäßig Yandex verwendet.

## Benutzerdefinierte Felder

Verwenden Sie 1–64 lateinische Buchstaben, Ziffern, Bindestriche oder Unterstriche. Füllen Sie die neue Zeile aus und speichern Sie die Einstellungen.

Felder gelten für alle Seiten. Werte werden pro Seite gespeichert. Fügen Sie [u-ID] ein oder wählen Sie ein Feld im Editor.

## Vorlage der Punktliste

Wählen Sie eigenes PHP. Die Punkte stehen in $context['pointItems'], benutzerdefinierte Felder in $point['customFields'].

Unter Seitenkomponenten können Sie den schreibgeschützten Shortcode kopieren. Ändern Sie die Einstellungen in den Feldern der Komponente. Beim Speichern der Seite werden auch die Komponentenänderungen gespeichert. Für [route] können Sie Anbieter und Schaltflächentext wählen. Ist der Text leer, wird „Route planen“ angezeigt. Für [map] können Sie Anbieter und Kartenmodus wählen. Standard übernimmt die Karteneinstellungen.
