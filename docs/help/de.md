# Deutsch · Eurowebcart CMS Lite

## Erste Schritte mit 1.6.58

- Aktivieren Sie eine vollständige Website unter Designvorlagen → Design-Themes. Farben und Schriften haben eigene Tabs. Mehrere Schriftfamilien lassen sich aktivieren und lokal speichern.
- Aktivieren Sie Bibliotheken unter Einstellungen → Bibliotheken und speichern Sie benötigte Ressourcen lokal. Font Awesome ist enthalten.
- Im Bearbeitungsmodus öffnet ein Doppelklick auf den Inhalt den Editor im Dialog. Ein Doppelklick auf die Standard-H1 öffnet ihr Feld im SEO-Tab.
- Felddefinitionen gelten gemeinsam, Werte je Seite. Beim Anlegen lässt sich der Wert für die geöffnete Seite eingeben. Das Eingabeelement passt sich dem Typ an.
- Erstellen Sie für eine neue Domain eine vollständige ZIP-Datei mit aktivem Theme und testen Sie eine Neuinstallation. Sichern Sie bestehende Websites und verwenden Sie ein signiertes Update.

- [Management](de/readme.md)
- [Admin-FAQ](de/faq.md)
- [Vorlagen und Komponenten](de/templates.md)

## Mobilmenü · 1.6.7

- Im Hauptmenü anzeigen
- Im Mobilmenü anzeigen
- Im Mobilmenü ausblenden


## Komponentendaten direkt bearbeiten · 1.6.6

Im Bearbeitungsmodus bieten Infoblöcke, Formulare [formX], Textbausteine [tel] und andere Komponenten mit Dateneditor die Optionen Daten und Vollbild. Die Ansichtsliste enthält registrierte Vorlagen. Wählen Sie Stammordner für die oberste Ebene. [list-pages] listet veröffentlichte Seiten im Bereich und seinen Unterordnern. Schließen Sie das Sprachmenü durch einen Klick außerhalb oder mit Escape.


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


## Schaltflächen im Kopfbereich der Verwaltung · 1.6.4

Der Globus öffnet die Website, der Pfeil meldet Sie ab. Auf Mobilgeräten sind nur die Symbole zu sehen. Fahren Sie mit der Maus darüber oder fokussieren Sie die Schaltflächen per Tastatur, um die Beschriftung zu sehen.

Inhalte, Formulare und Anfragen sowie Website und Dienste öffnen sich direkt. Unter dem Hauptmenü erscheinen die Registerkarten des gewählten Bereichs.

Fügen Sie [bread-crumbs] oder [search] in das HTML der Startseite (index.php) oder einer Vorlage ein. Jeder Block erscheint einmal an der gewählten Stelle. Die Einstellungen für Brotkrümelnavigation und Suche gelten weiterhin. [privacy] verweist auf /privacy/. Bearbeiten Sie die Datenschutzerklärung unter Seiten.

```text
[bread-crumbs]
[search]
[privacy]
```


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

## Neu in Version 1.6.2

### Sprache der Administration und Hilfe

Aktivieren Sie in den regionalen Einstellungen die gewünschten Sprachen. Das Sprachauswahlmenü in der Kopfzeile ändert die Benutzeroberfläche und die Hilfe, während der aktuelle Abschnitt beibehalten wird. Seitentexte und die Werte Ihrer Einstellungen werden in ihrer ursprünglichen Form gespeichert.

### Kellerdekoration

Der Code [author] zeigt die Signatur Eurowebcart CMS Lite, den Autorennamen Бандалетов Павел und einen Link zu GitHub an. Um auszuwählen, wo Ihre Signatur in Ihrer benutzerdefinierten HTML-Fußzeile platziert werden soll, fügen Sie [author] in die entsprechende Zeile ein.

### Hintergrundbeleuchtung im Bearbeitungsmodus

Bewegen Sie den Mauszeiger über den Link „Komponente bearbeiten“ oder legen Sie den Tastaturfokus darauf fest. Der Rahmen hebt die Ausgabe dieser bestimmten Einfügung hervor.

### Wo kann man die Suche ändern?

Wählen Sie unter „Einstellungen → Allgemein“ den Suchtyp aus. Unter „Wartung“ können Sie Seiten neu indizieren und das Feld im Admin-Header sucht nach Einstellungen.

### Ergebnisse und Vorschläge

Die Suche zeigt 20 nummerierte Ergebnisse je Seite. Die kompakte Suche öffnet sich links neben der Lupe. Das Eingabefeld enthält ein Enter-Symbol. Suchvorschläge in der Verwaltung verlinken direkt auf die Einstellungen.

## Neu in Version 1.6.1

### Suche

Wählen Sie in den Grundeinstellungen Aus, Kompakt mit Lupe oder Erweitert. Die Adminleiste durchsucht Einstellungen. Der Index aktualisiert sich nach Änderungen; für FTP gibt es eine geplante Aktualisierung. Eingebundene Seiten und Datenblöcke werden indexiert, PHP-Module nicht ausgeführt.

### PHP-Module

Klappen Sie ein Modul für Einstellungen und verwendende Seiten auf. Geben Sie Modul-ID, Dateiname und PHP-Funktionsrumpf ein. Beim Umbenennen bleibt [php]id[/php] unverändert. CodeMirror lässt sich für Syntaxhervorhebung aktivieren.

### Vorlagen und Übersetzungen

Pro Vorlage können Sie bis zu acht Änderungen rückgängig machen oder das ursprüngliche PHP wiederherstellen. Die Historie ist je Domain getrennt. Eigenes HTML ändern Sie in der Verwaltung, PHP liegt in templates/local/. Die Sprachwahl bleibt beim Anlegen verfügbar; Übersetzungen werden mit der Seite verschoben.

### Integrationen und Wartung

CAPTCHA, Karten, Telefonie und Analyse verlinken die Anbieter. URL und Token der Rückruf-API sind in der Verwaltung einstellbar. Beim Wechsel auf die Hauptdomain gelten die Platzhalter der registrierten Subdomain weiter. Planen Sie php update.php --scheduled alle 15 Minuten ein.

## Neu in Version 1.6.0

### Suche und Cache

Erstellen Sie den Textindex unter Wartung nach Änderungen im Adminbereich oder per FTP neu. Die Suchvorschläge erfassen öffentliche Seiten in der gewählten Sprache. Der Cache ist zunächst aus und kann dort aktiviert oder geleert werden. Formulare und dynamische Komponenten werden pro Anfrage verarbeitet.

### Bearbeitung auf der Website

Nach der Anmeldung erscheint die Werkzeugleiste. Der Schalter ganz rechts aktiviert die Bearbeitungslinks auf der gesamten Website. CSS und JS der Seite öffnen sich im Dialog. Die Verwaltung bietet HTML- und visuelle Bearbeitung, das Einfügen von Codes sowie AJAX-Sammeluploads und Sortierung.

### Komponenten und Navigation

Fehlende Parameter verwenden die globalen Einstellungen. width akzeptiert 100, 50, 33 oder 25, auch mit %. Auf Smartphones stehen die Blöcke untereinander. numbers=off blendet den Zähler aus. Wischen wechselt das Bild; das mobile Menü kommt von rechts. Die Brotkrumennavigation lässt sich in den Grundeinstellungen deaktivieren.

```text
[gal width=50 numbers=off arrows=on fit=cover]/images/myfotos/[/gal]
[fotos width=50% columns=3 captions=on]/images/myfotos/[/fotos]
[files width=33 icons=off size=on download=on]/files/myfiles/[/files]
```

### Sprachen, Darstellung und Preise

Wählen Sie Sprachen, Währung, Zahlen-, Datums- und Zeitformate sowie Zeitzone in den Regionaleinstellungen. Speichern Sie die Übersetzung als index_en.php neben index.php. Ohne Übersetzung erscheint der Originalinhalt. Preisfelder verwenden die gewählte Währung. Vorschau, getrennte helle und dunkle Logos und eigene Schriftbeispiele für Roboto Condensed sind verfügbar.

```text
/contacts/index.php → /contacts/index_en.php
```

### Formulare, Anfragen und Integrationen

Gemeinsame Google-reCAPTCHA-v2-Schlüssel und Aktivierung stehen in den CAPTCHA-Einstellungen; Formulare bieten eigene Optionen. Nachrichten und Rückrufe stehen ungelesen zuerst mit 20 je Seite. Integrationen umfassen Zadarma, Telegram, Analytik und Google- oder Yandex-Karten mit optionalen Clustern. Der Testdialog prüft die aktive Bibliothek und zeigt Einfügecode. CodeMirror unterstützt Auto, HTML, CSS, JS, PHP und RAW.

```text
[map lat=55.7558 lng=37.6173 title="Office" zoom=13 provider=yandex url="/contacts/" button="Open"]
```

```text
[callback button="Call me" title="Callback" submit="Send" placeholder="+7(900)123-45-67"]
```

### PHP-Module

Unter PHP-Module bearbeiten Sie Namen, Beschreibung und Verfügbarkeit der registrierten Module. server-date zeigt das Datum im regionalen Format. Ausführbare Dateien liegen in einem geschützten Verzeichnis und werden von Entwicklern installiert.

```text
[php]server-date[/php]
```

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

Eine Formularspalte zeigt in konfigurierten Zeilen eine Bestellschaltfläche an. Wählen Sie das Formular nach Namen aus. Verwenden Sie in CSV den Formularcode, etwa form86.

Der Typ „Verstecktes Tag“ fügt Filter oberhalb der Tabelle hinzu. Geben Sie pro Zelle ein oder mehrere durch Kommas getrennte Tags ein. „ALLE“ zeigt alle Zeilen.

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

Wählen Sie „Bearbeiten“, um eine Vorlage zu ändern. „Vorlage speichern“ übernimmt die Änderungen für neue Seiten. „Duplizieren“ erstellt eine separate Vorlage. Im Seiteneditor können Sie die ausgewählte Vorlage öffnen oder aus der gespeicherten Seite eine Vorlage erstellen. Wählen Sie im Vorlagenformular „Symbol einfügen“ und suchen Sie nach dem Namen. Sie können auch einen Code wie `[fa-icon=house]` eingeben.

Die Layoutvorlagen sind anfangs eingeklappt. Öffnen Sie eine Vorlage über ihre Überschrift oder wählen Sie Alle aufklappen. Erneutes Drücken klappt die Liste ein.

Um die Breite des gesamten Bannerkarussells zu ändern, aktivieren Sie den Bearbeitungsmodus, öffnen Sie Komponente bearbeiten und wählen Sie die Blockbreite.

## Städte · Telefon · 1.6.18

Nur der Stadtname ist erforderlich. Domain und Telefonnummern sind optional. Geben Sie + und die Landesvorwahl an, zum Beispiel +7 (900) 000-00-00. Sie können auch einen Code wie [tel] verwenden.

Zuerst gilt die gespeicherte Stadt, dann die Domain-Stadt und danach der erste Listeneintrag. Ohne Städte werden die allgemeinen Telefonnummern angezeigt.

Formulare, Rückrufe und neue Bestellungen gehen an die Stadtadresse oder an die Standardadresse der Website. Sind beide leer, behalten Formulare ihre eigenen Empfänger. Leere Social-Media-Links werden geerbt.

Im Bearbeitungsmodus erscheint beim Überfahren einer Vorlage ein Bearbeitungslink. Bei allen installierten [php]-Komponenten öffnet Modul bearbeiten den Quellcode und die Einstellungen im Dialog. Der Folien-Editor zeigt Formularname und Code. [banners width=100] füllt als eigenständiger Block ohne Seitenleiste die Fensterbreite. Der Vorlageneditor öffnet sich in einem Dialog auf der Website. Doppelklicken Sie im Bearbeitungsmodus auf einen freien Bereich der Vorlage. Der Hinweis erscheint beim Überfahren unter der Schaltfläche. Bei überlappenden Blöcken hat die spätere Vorlage Vorrang. Folie bearbeiten öffnet die gewählte Bannerfolie. Die Website-Leiste zeigt Komponenten, Vorlagen und Bausteine der aktuellen Seite. Im Seiteneditor finden Sie neben Sprachen den Reiter Seitenkomponenten mit den verfügbaren Einstellungen und Daten. Doppelklicken Sie auf den Seiteninhalt, um die aktuelle Seite zu bearbeiten. Der Editor öffnet sich in einem Dialog über der Website. Schließen Sie ihn nach dem Speichern, um die Änderungen auf der Seite zu sehen. Doppelklicken Sie auf die H1-Überschrift, um ihr Feld im SEO-Tab zu öffnen.

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

„Тихий сад“ ist eine vollständige Website für Landschaftsgestaltung. Wählen Sie das Theme unter Layoutvorlagen → Design-Themes und wenden Sie es an. Bearbeiten Sie garden-projects und garden-prices sowie das Banner-Set garden-cover. Hinterlegen Sie Ihre Kontaktdaten und einen Empfänger für [form85].

Das Theme Dezinfektori umfasst 32 Leistungen, die Anfrage [form86] und das Bewertungsformular [form87]. Öffnen Sie die Nachricht. Mit Einwilligung übernehmen Sie Name, Text und Bewertung in dez-reviews und schalten den Eintrag frei. Die Leistungsseiten enthalten Erläuterungen zu Schäden, Vorher-nachher-Bilder und fiktive Einsatzgeschichten. Die Blöcke lassen sich unter Seiten bearbeiten.

```text
[iblock=reviews limit=6]/data/dez-reviews.txt[/iblock]
```

Wenn der Bewertungsfilter leer ist, zeigt die Startseite freigegebene Bewertungen aller Seiten.

Jede Katalogseite hat ein Bewertungsformular. Nach der Freigabe im Bereich Bewertungen erscheint der Beitrag auf seiner Ursprungsseite.

### Websitefarben · Schriften

Aktivieren Sie Schriften per Kontrollkästchen und weisen Sie sie Texten, Überschriften oder Bedienelementen zu. Lokale Dateien lädt Ihre Website.

Bibliotheken → Lokal speichern

Seitenvorlagen → Seite erstellen

## Benutzerdefinierte Felder

Geben Sie in der neuen Zeile den Wert für die aktuelle Seite ein und speichern Sie ihn mit den Feldeinstellungen.

## Bewertungen

Fügen Sie den Code im Seiteneditor ein. Besucher geben einen Namen und eine Bewertung mit 1 bis 5 Sternen ab. Im Bereich Bewertungen können Sie Beiträge freigeben, ablehnen, bearbeiten oder löschen. „Mehr laden“ fügt die nächsten zehn hinzu.

Geben Sie einen Pfad wie /contacts/ ein oder lassen Sie das Feld für alle Bewertungen leer. Das Formular speichert die Seite, von der es gesendet wurde. Nur freigegebene Bewertungen erscheinen auf der Website. CAPTCHA verwendet die allgemeinen CMS-Einstellungen.

```text
[reviews]
```
