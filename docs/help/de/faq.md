# Admin-FAQ

## Installation und Anmeldung

### Wie starte ich eine Website und öffne das Admin-Panel?

Um lokal zu starten, führen Sie den Befehl php -S 127.0.0.1:8000 router.php im Projektverzeichnis aus und öffnen Sie http://127.0.0.1:8000/admin/.. Führen Sie auf dem Hosting zunächst php admin/setup.php aus und geben Sie dann den erhaltenen Einmalcode im ersten Schritt des Assistenten in /admin/. ein

### Was können Sie beim ersten Start konfigurieren?

Erstellen Sie zunächst einen Benutzernamen und ein Passwort. Im zweiten Schritt legen Sie den Site-Namen, die Standard-Meta-Beschreibung, den Fußzeilentext, die Palette und den Themenmodus fest. Wenn Sie über PHP GD verfügen, können Sie das Logo sofort herunterladen und daraus ein Favicon erstellen. Sie können den zweiten Schritt überspringen oder beim nächsten Anmelden fortfahren.

## Seiten und Menüs

### Wie erstelle ich eine Seite und welche Adresse muss eingegeben werden?

Gehen Sie zu Seiten → Neue Seite. Geben Sie die Adresse ein, zum Beispiel /Services-1/Repair/ oder /Services-1/Price_2.php, geben Sie den Titel ein und speichern Sie. Für die Ordneradresse erstellt die Engine selbst Verzeichnisse und index.php. In Namen sind A-Z, a-z, 0-9, Bindestrich und Unterstrich zulässig. Die Hauptseite ist im Stammverzeichnis reserviert und die Erstellung von Seiten in Serviceverzeichnissen ist geschlossen.

### Was ist der Unterschied zwischen dem Kontrollkästchen „Im Menü“ und der Schaltfläche „Ausblenden“?

Durch Deaktivieren des Kontrollkästchens „Im Menü“ wird der Link aus den regulären Menüs entfernt, der Zugriff auf die direkte Adresse bleibt jedoch erhalten. Auf einer Seite mit einer Ordneradresse steuert das Kontrollkästchen auch Elemente auf Unterseiten. Mit der Schaltfläche „Ausblenden“ wird der Link entfernt und die öffentliche Adresse der Seite geschlossen, bis sie erneut veröffentlicht wird.

### Wie kann ich die Reihenfolge der Seiten ändern oder einen ganzen Ordner verschieben?

Ziehen Sie am Griff ⠿, um Seiten derselben Ebene zu sortieren. Eine einzelne Seite verschieben Sie in den Seiteneinstellungen. Das Symbol am Ordner öffnet die Auswahl des übergeordneten Bereichs. Wählen Sie einen Bereich oder die oberste Ebene und klicken Sie auf „Ordner verschieben“. Seiten und Dateien werden mitverschoben. Prüfen Sie alte Links und [include]-Codes.

### Warum sind Seitenänderungen nicht sofort sichtbar?

Klicken Sie im Editor auf „Speichern“. Anschließend nutzen Sie den Link „Seite öffnen“ oder „Los“. Wenn der Link verschwunden ist, prüfen Sie, ob die Seite veröffentlicht ist. Überprüfen Sie bei Änderungen an einer Subdomain die Domain in der Adressleiste.

## Design und Subdomains

### Wo kann ich Titel, Fußzeile, Logo und Favicon ändern?

Öffnen Sie Einstellungen → Allgemein. Das Feld „Fußzeilentext“ unterstützt [year] für das aktuelle Jahr. Im Block „Logos und Site-Symbole“ können Sie Bilder hochladen oder auf „Favicon aus einem Logo erstellen“ klicken. Laden und Konvertieren erfordern PHP GD.

### Wie ändere ich das Thema oder die Schriftart und kehre dann zum alten zurück?

Wählen Sie unter Einstellungen → Thema und Farben einen Modus und eine Palette aus. Speichern Sie die aktuelle Palette in der Liste, damit Sie sie später verwenden können. Die Schaltfläche „Als Standard verwenden“ speichert die Palette für den Befehl „Design auf Standard zurücksetzen“. Die Schriftart kann im Abschnitt „Bibliotheken und Schriftarten“ geändert werden. Die Namen und Beispiele in der Liste werden in den entsprechenden Schriftarten angezeigt. Der Wert „System“ gibt die normale Schriftart zurück.

### Wo ändert man den Header und wo fügt man einen Zähler, CSS oder JavaScript ein?

Die gemeinsamen Blöcke Kopfzeile, Mobilmenü, Menü und Fußzeile werden im Reiter „Designvorlagen“ bearbeitet. An der gleichen Stelle fügen „Service Header“ HTML in den Head oder vor das schließende Body-Tag ein. Für normales CSS und JavaScript gibt es eine separate Registerkarte „CSS und JS“. Der Code für eine Seite wird in ihrem Editor hinzugefügt.

### Wie lege ich unterschiedliche Telefonnummern und Header für Subdomains fest?

Fügen Sie im Abschnitt „Domains“ eine Subdomain hinzu. Legen Sie dann im Abschnitt „Untervorlagen“ einen separaten Untervorlagenwert fest, indem Sie die gewünschte Domäne auswählen. Ändern Sie den Header und andere Blöcke über die Schaltfläche „Vorlagen“ neben der Domain. Die Domänenversion hat Vorrang vor der allgemeinen Version. Der DNS- und Webserver muss die Subdomain auf das Verzeichnis dieser Site verweisen.

### Wo kann ich Links zu sozialen Netzwerken und Bildunterschriften daneben ändern?

Öffnen Sie „Einstellungen → Kontakte und soziale Netzwerke“. Geben Sie Adressen, Unterschriften und Telefonnummern ein. Der Code [soc-icons] zeigt Symbole aller konfigurierten Netzwerke an. Für einen einzelnen Link verwenden Sie beispielsweise [tg] oder [vk].

## Formulare und Anträge

### Wie füge ich ein Formular zu einer Seite hinzu?

Öffnen Sie Formulare, konfigurieren Sie Felder, Pflichtfelder, maximale Länge und Schaltflächentext. Speichern Sie das Formular und fügen Sie seinen Code, zum Beispiel [form1], in den HTML-Code der Seite ein.

### Wo kann ich Bewerbungen einsehen, wenn ich keine Benachrichtigung per E-Mail erhalten habe?

Öffnen Sie Nachrichten. Bewerbungen werden dort nach erfolgreichem Absenden des Formulars gespeichert. Überprüfen Sie für Benachrichtigungen die Adressen im Feld „An“ in der Formular-Mail-Vorlage und die Funktionalität der Funktion mail() auf dem Hosting.

### Wie erstelle ich einen Brief und versende Kopien?

Öffnen Sie das gewünschte Formular und suchen Sie den Block „Mail-Vorlage“. Geben Sie Adressen in die Felder „An“ und „Kopien (Cc)“ ein, getrennt durch Kommas. Verwenden Sie im Betreff und im Text Feldschlüssel in geschweiften Klammern, zum Beispiel Hallo {name}! Die Schaltflächen unterhalb des Textes fügen die ausgewählte Variable ein. Bei leerem Betreff oder Text wird die Standardbenachrichtigung verwendet.

### Warum hat das Formular die Nachricht abgelehnt oder gefragt: „Ich bin kein Roboter“?

Überprüfen Sie die erforderlichen Felder, Länge, Telefon- und E-Mail-Format. Kostenlose Eingabefelder verwenden einfachen Text ohne HTML oder Website-Adressen. Listen mit verbotenen Wörtern und IPs finden Sie im Abschnitt „Antispam“. Das Kontrollkästchen „CAPTCHA verwenden“ erfordert einen Site-Schlüssel und einen geheimen Google reCAPTCHA v2-Schlüssel für die gewünschte Domain.

### Wo kann ich Rückrufwünsche einsehen?

Im Bereich „Rückrufe“. Die Komponente wird der Seite mit dem Code [callback] oder [callback=Вызвать мастера] hinzugefügt. Beim Anbieter „Nein“ werden Bewerbungen im Admin-Panel gespeichert. Bei konfigurierter Telefonie steht die manuelle oder automatische Übertragung zur Verfügung.

## Informationsblöcke und Komponenten

### Wie erstelle ich einen Informationsblock und zeige ihn auf der Seite an?

Klicken Sie im Abschnitt „Infoblöcke“ auf „Neuer Infoblock“, konfigurieren Sie die Spalten und fügen Sie Zeilen hinzu. Verwenden Sie im Infoblocknamen und in den Spaltenschlüsseln A–Z, a–z, 0–9, Bindestrich oder Unterstrich ohne Leerzeichen. Speichern und kopieren Sie den Einbettungscode aus dem Infoblock-Editor. Für eine Liste der Telefone verwenden Sie die Telefonvorlage.

### Wie kopiere oder ersetze ich alle Zeilen eines Infoblocks?

Öffnen Sie den Infoblock und klicken Sie auf „Als Text bearbeiten“. CSV verwendet das | Trennzeichen. Die ersten drei Zeilen enthalten die Schlüssel, Namen und Typen der Spalten. Als nächstes kommen die Daten. Schließen Sie ein Feld mit einem Trennzeichen, einem Anführungszeichen oder einem Zeilenumbruch in doppelte Anführungszeichen und doppelte interne Anführungszeichen ein.

### Was ist zu überprüfen, wenn die Galerie oder Dateiliste leer ist?

Überprüfen Sie den Pfad im Komponentencode mit dem Ordner auf dem Server. Beispielsweise verwendet [fotos]/images/myfotos/[/fotos] Fotos in images/myfotos/ oder templates/images/myfotos/.. Überprüfen Sie bei [files] die Dateierweiterung. Eine Liste der unterstützten Erweiterungen und Parameter finden Sie in der Komponentenhilfe.

### Warum gibt eine Anfrage mit ?ajax=1 403 zurück?

Das Seitenfragment erfordert eine gültige öffentliche Sitzung und ein gültiges Token auf derselben Site. Verwenden Sie zum Laden innerhalb der Site window.siteFetchContent(), das das Token überträgt. Öffnen Sie für einen normalen Übergang die Seitenadresse ohne den Ajax-Parameter.

## Updates und Backup

### Wie aktualisiere ich die Engine einer installierten Site?

Vergleichen Sie unter Einstellungen → Updates die installierte Version mit der neuesten Version von GitHub und klicken Sie dann auf Update Engine. Wenn GitHub nicht verfügbar ist, zeigt das Admin-Panel die zuletzt überprüfte Version an. Um manuell zu aktualisieren, übergeben Sie das Archiv php-site-update.zip an den PHP-Befehl update.php. Das Update-Paket ersetzt die Engine-Dateien und Standardvorlagen. Speichern Sie Ihre PHP-Vorlagen in templates/local/.

### Was sollte ich vor dem Upgrade oder der Migration kopieren?

Erstellen Sie eine Kopie des gesamten Site-Verzeichnisses zusammen mit versteckten Dateien. Seiten befinden sich im Stammverzeichnis und ihre Verzeichnisse, Infoblöcke in Daten/, Einstellungen und Kontodateien in admin/.. Speichern Sie außerdem Ihre Vorlagen, hochgeladenen Bilder, Nachrichten und Anfragen. Die Kopie enthält Konfigurationsgeheimnisse. Bewahren Sie sie daher außerhalb eines öffentlichen Verzeichnisses auf.

### Was ist zu überprüfen, wenn das Admin-Panel die Änderungen nicht speichern kann?

Lesen Sie zunächst die Nachricht neben dem Formular. Wenn Änderungen widersprüchlich sind, kopieren Sie Ihre Änderungen und öffnen Sie den Editor erneut. Wenn ein Schreibfehler auftritt, bitten Sie das Hosting, die Schreibberechtigungen des PHP-Prozesses im gewünschten Ordner und in der gewünschten Datei admin/write-lock.php. zu überprüfen. Wenn das Update fehlschlägt, überprüfen Sie den Zugriff des Servers auf GitHub und die Verfügbarkeit von PHP Zip und OpenSSL.

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
