# Deutsch · 1.6.0

## Suche und Cache

Erstellen Sie den Textindex unter Wartung nach Änderungen im Adminbereich oder per FTP neu. Die Suchvorschläge erfassen öffentliche Seiten in der gewählten Sprache. Der Cache ist zunächst aus und kann dort aktiviert oder geleert werden. Formulare und dynamische Komponenten werden pro Anfrage verarbeitet.

## Bearbeitung auf der Website

Nach der Anmeldung erscheint die Werkzeugleiste. Der Schalter ganz rechts aktiviert die Bearbeitungslinks auf der gesamten Website. CSS und JS der Seite öffnen sich im Dialog. Die Verwaltung bietet HTML- und visuelle Bearbeitung, das Einfügen von Codes sowie AJAX-Sammeluploads und Sortierung.

## Komponenten und Navigation

Fehlende Parameter verwenden die globalen Einstellungen. width akzeptiert 100, 50, 33 oder 25, auch mit %. Auf Smartphones stehen die Blöcke untereinander. numbers=off blendet den Zähler aus. Wischen wechselt das Bild; das mobile Menü kommt von rechts. Die Brotkrumennavigation lässt sich in den Grundeinstellungen deaktivieren.

```text
[gal width=50 numbers=off arrows=on fit=cover]/images/myfotos/[/gal]
[fotos width=50% columns=3 captions=on]/images/myfotos/[/fotos]
[files width=33 icons=off size=on download=on]/files/myfiles/[/files]
```

## Sprachen, Darstellung und Preise

Wählen Sie Sprachen, Währung, Zahlen-, Datums- und Zeitformate sowie Zeitzone in den Regionaleinstellungen. Speichern Sie die Übersetzung als index_en.php neben index.php. Ohne Übersetzung erscheint der Originalinhalt. Preisfelder verwenden die gewählte Währung. Vorschau, getrennte helle und dunkle Logos und eigene Schriftbeispiele für Roboto Condensed sind verfügbar.

```text
/contacts/index.php → /contacts/index_en.php
```

## Formulare, Anfragen und Integrationen

Gemeinsame Google-reCAPTCHA-v2-Schlüssel und Aktivierung stehen in den CAPTCHA-Einstellungen; Formulare bieten eigene Optionen. Nachrichten und Rückrufe stehen ungelesen zuerst mit 20 je Seite. Integrationen umfassen Zadarma, Telegram, Analytik und Google- oder Yandex-Karten mit optionalen Clustern. Der Testdialog prüft die aktive Bibliothek und zeigt Einfügecode. CodeMirror unterstützt Auto, HTML, CSS, JS, PHP und RAW.

```text
[map lat=55.7558 lng=37.6173 title="Office" zoom=13 provider=yandex url="/contacts/" button="Open"]
```

```text
[callback button="Call me" title="Callback" submit="Send" placeholder="+7(900)123-45-67"]
```

## PHP-Module

Unter PHP-Module bearbeiten Sie Namen, Beschreibung und Verfügbarkeit der registrierten Module. server-date zeigt das Datum im regionalen Format. Ausführbare Dateien liegen in einem geschützten Verzeichnis und werden von Entwicklern installiert.

```text
[php]server-date[/php]
```

