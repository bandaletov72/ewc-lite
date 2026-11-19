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

Ziehen Sie das ⠿-Symbol, um Seiten innerhalb derselben Ebene neu anzuordnen. Um eine Seite zu verschieben, öffnen Sie „Seite verschieben“ in ihrem Editor. Um einen Ordner zusammen mit den angehängten Seiten und Dateien zu übertragen, klicken Sie auf ↪ neben dem Ordnernamen. Überprüfen Sie nach der Übertragung die manuellen Links und [include]-Codes, die auf die alten Adressen verweisen.

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

