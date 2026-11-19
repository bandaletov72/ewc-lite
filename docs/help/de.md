# Deutsch · Eurowebcart CMS Light

- [Management](de/readme.md)
- [Admin-FAQ](de/faq.md)
- [Vorlagen und Komponenten](de/templates.md)

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

Der Code [author] zeigt die Signatur Eurowebcart CMS Light, den Autorennamen Бандалетов Павел und einen Link zu GitHub an. Um auszuwählen, wo Ihre Signatur in Ihrer benutzerdefinierten HTML-Fußzeile platziert werden soll, fügen Sie [author] in die entsprechende Zeile ein.

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

