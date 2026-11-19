# Français · 1.6.1

## Recherche

Dans les réglages généraux, désactivez la recherche ou choisissez une loupe ou un champ visible. La barre d’administration recherche aussi les réglages. L’index se met à jour après les modifications et peut être planifié pour le FTP. Les pages incluses et les blocs de données sont indexés sans exécuter les modules PHP.

## Modules PHP

Développez un module pour voir ses réglages et les pages qui l’utilisent. Saisissez un identifiant, un nom de fichier et le corps de la fonction. Le code [php]id[/php] reste le même après un renommage. CodeMirror active la coloration syntaxique.

## Modèles et traductions

Annulez jusqu’à huit modifications par modèle ou restaurez le PHP d’origine, par domaine ou pour les modèles partagés. Choisissez HTML personnalisé pour modifier un bloc, ou placez le PHP dans templates/local/. Le choix de langue reste visible et les traductions suivent la page déplacée.

## Intégrations et maintenance

Les réglages de CAPTCHA, cartes, téléphonie et statistiques donnent accès aux instructions officielles. Saisissez l’URL et le jeton du rappel HTTP. Les réglages d’un sous-domaine enregistré restent actifs lors du passage au domaine principal. Planifiez php update.php --scheduled toutes les 15 minutes.

---

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

