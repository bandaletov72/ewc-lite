# Français · 1.6.0

## Recherche et cache

Dans Maintenance, reconstruisez l’index texte après publication ou modification via l’administration ou FTP. Les suggestions tiennent compte des pages publiques et de la langue. Le cache des pages analysées est désactivé par défaut. Les formulaires et blocs dynamiques restent traités à chaque requête.

## Modification sur le site

La barre apparaît après connexion. L’interrupteur à droite active les liens de modification des composants sur tout le site. Éditez le CSS ou le JS de la page dans une fenêtre modale. L’éditeur propose les modes HTML et visuel, l’insertion de codes et le transfert groupé avec tri AJAX.

## Composants et navigation

Les paramètres globaux servent de valeurs par défaut. width accepte 100, 50, 33 et 25 avec ou sans %. Les blocs se placent sur une colonne sur téléphone. numbers=off masque le compteur; le balayage change de photo. Le menu mobile arrive de droite. Le fil d’Ariane peut être désactivé dans les réglages généraux.

```text
[gal width=50 numbers=off arrows=on fit=cover]/images/myfotos/[/gal]
[fotos width=50% columns=3 captions=on]/images/myfotos/[/fotos]
[files width=33 icons=off size=on download=on]/files/myfiles/[/files]
```

## Langues, apparence et prix

Les paramètres régionaux définissent les langues, la devise, le fuseau et les formats. Placez index_en.php à côté d’index.php pour traduire une page; le contenu original reste disponible. Le type Prix utilise la devise choisie. Prévisualisez les couleurs, changez le mode immédiatement, importez les logos clair et sombre et testez Roboto Condensed avec votre texte.

```text
/contacts/index.php → /contacts/index_en.php
```

## Formulaires, demandes et intégrations

Configurez Google reCAPTCHA v2 dans les réglages CAPTCHA, globalement ou par formulaire. Messages et rappels affichent 20 entrées par page, les non lus en premier. Intégrations propose Zadarma, Telegram, les statistiques et les cartes Google ou Yandex avec regroupement facultatif. La fenêtre des bibliothèques teste la version active et affiche le code. CodeMirror propose Auto, HTML, CSS, JS, PHP et RAW.

```text
[map lat=55.7558 lng=37.6173 title="Office" zoom=13 provider=yandex url="/contacts/" button="Open"]
```

```text
[callback button="Call me" title="Callback" submit="Send" placeholder="+7(900)123-45-67"]
```

## Modules PHP

Dans Modules PHP, choisissez le nom, la description et la disponibilité. server-date suit le format de date régional. Les développeurs installent les fichiers exécutables dans un répertoire protégé.

```text
[php]server-date[/php]
```

