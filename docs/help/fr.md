# Français · Eurowebcart CMS Light

- [Gestion](fr/readme.md)
- [FAQ Administrateur](fr/faq.md)
- [Modèles et composants](fr/templates.md)

## Boutons de l’en-tête d’administration · 1.6.4

Le globe ouvre le site et la flèche ferme la session. Sur mobile, seules les icônes sont visibles. Survolez les boutons ou sélectionnez-les au clavier pour afficher leur libellé.

Les rubriques Contenu, Formulaires et demandes, et Site et services s’ouvrent directement. Les onglets de la rubrique choisie apparaissent sous le menu principal.

Insérez [bread-crumbs] ou [search] dans le HTML de la page d’accueil (index.php) ou du modèle. Chaque bloc apparaît une seule fois à l’emplacement choisi. Les réglages du fil d’Ariane et de la recherche s’appliquent. [privacy] renvoie vers /privacy/. Modifiez la politique dans la rubrique Pages.

```text
[bread-crumbs]
[search]
[privacy]
```


## Nouveau dans la version 1.6.3

### Politique d'accueil et de confidentialité

Ouvrez « Modèles de conception » pour modifier la page d'accueil et la page /privacy.php. Le code [page-content] affiche le contenu de la page principale. Dans la politique [site-name] et [site-url] prenez le nom et l'adresse publique de « Paramètres → Général ». Complétez le texte initial avec des informations sur votre site et le traitement des données.

### Cookie et retour en haut

Dans le pied de page, [conf-policy] fournit un lien vers la stratégie, [cookies] ouvre les paramètres et [top] fait défiler vers le haut de la page. Les analyses et les cartes CMS intégrées attendent un accord. Les scripts ajoutés manuellement aux modèles ou aux en-têtes de service nécessitent leur propre configuration.

### Éditeur functions.php

Ouvrez « Modèles de conception → Fonctions » après l'onglet « En-têtes de service ». Ajoutez des fermetures au tableau renvoyé. Dans votre modèle PHP, appelez `translit("Москва")` ou `ew_call("translit", "München")`. En HTML, insérez [func=translit value="Москва и München"]. L'exemple produit `Moskva i Muenchen`.

### Modification des modèles standards

Le champ « Modèles de conception » contient déjà le code source PHP et HTML avec surbrillance. Lors de la modification d'un modèle standard, sélectionnez « PHP personnalisé ». L'enregistrement crée une version personnalisée. À l'aide des boutons à côté du modèle, vous pouvez annuler la dernière modification ou restaurer l'original.

### Vérification PHP avant d'enregistrer

Les éditeurs de modèles de conception, de modules et functions.php vérifient la syntaxe avec la commande `php -l`. En cas d'erreur, la version précédente demeure. Pour sélectionner la CLI PHP sur le serveur, utilisez la variable `SITE_PHP_LINT_BINARY`.

### Rétroéclairage et recherche compacte

CodeMirror est activé par défaut sur les nouvelles installations. Le paramètre se trouve dans « Bibliothèques et polices ». En recherche compacte, le champ s'agrandit vers la gauche, la croix efface la ligne et la touche Entrée lance la recherche.

### Menu d'administration et panneau d'édition

Le menu sous l'en-tête du panneau d'administration et le panneau d'édition sur le site affichent des icônes avec des légendes. Les icônes restent sur l'écran étroit. Les info-bulles s'ouvrent au survol et le clavier se concentre sur un écran étroit. « Masquer » est situé à côté du reste des actions de la page. Le commutateur de mode d'édition est situé à droite.

### Description et code du module PHP

Dans "Modules PHP", la brève description concerne le texte brut. Collez le corps de la fonction dans un champ séparé « Code source · PHP ».

## Nouveau dans la version 1.6.2

### Langue de l'administration et de l'aide

Dans les paramètres régionaux, activez les langues souhaitées. Le menu de sélection de la langue dans l'en-tête modifie l'interface et l'aide, tout en conservant la section actuelle. Les textes des pages et les valeurs de vos paramètres sont stockés sous leur forme originale.

### Décoration du sous-sol

Le code [author] affiche la signature Eurowebcart CMS Light, le nom de l'auteur Бандалетов Павел et un lien vers GitHub. Pour choisir où placer votre signature dans votre pied de page HTML personnalisé, collez [author] dans la ligne appropriée.

### Rétroéclairage en mode édition

Passez la souris sur le lien Modifier le composant ou définissez-y le focus clavier. Le cadre mettra en évidence la sortie de cet insert particulier.

### Où modifier la recherche

Dans « Paramètres → Général », sélectionnez le type de recherche. Dans « Maintenance », vous pouvez réindexer les pages et le champ dans l'en-tête d'administration recherche par paramètres.

### Résultats et suggestions

La recherche affiche 20 résultats numérotés par page. Le champ compact se déploie à gauche de la loupe et contient une icône Entrée. Les suggestions de la recherche administrative mènent directement aux réglages.

## Nouveautés de la version 1.6.1

### Recherche

Dans les réglages généraux, désactivez la recherche ou choisissez une loupe ou un champ visible. La barre d’administration recherche aussi les réglages. L’index se met à jour après les modifications et peut être planifié pour le FTP. Les pages incluses et les blocs de données sont indexés sans exécuter les modules PHP.

### Modules PHP

Développez un module pour voir ses réglages et les pages qui l’utilisent. Saisissez un identifiant, un nom de fichier et le corps de la fonction. Le code [php]id[/php] reste le même après un renommage. CodeMirror active la coloration syntaxique.

### Modèles et traductions

Annulez jusqu’à huit modifications par modèle ou restaurez le PHP d’origine, par domaine ou pour les modèles partagés. Choisissez HTML personnalisé pour modifier un bloc, ou placez le PHP dans templates/local/. Le choix de langue reste visible et les traductions suivent la page déplacée.

### Intégrations et maintenance

Les réglages de CAPTCHA, cartes, téléphonie et statistiques donnent accès aux instructions officielles. Saisissez l’URL et le jeton du rappel HTTP. Les réglages d’un sous-domaine enregistré restent actifs lors du passage au domaine principal. Planifiez php update.php --scheduled toutes les 15 minutes.

## Nouveautés de la version 1.6.0

### Recherche et cache

Dans Maintenance, reconstruisez l’index texte après publication ou modification via l’administration ou FTP. Les suggestions tiennent compte des pages publiques et de la langue. Le cache des pages analysées est désactivé par défaut. Les formulaires et blocs dynamiques restent traités à chaque requête.

### Modification sur le site

La barre apparaît après connexion. L’interrupteur à droite active les liens de modification des composants sur tout le site. Éditez le CSS ou le JS de la page dans une fenêtre modale. L’éditeur propose les modes HTML et visuel, l’insertion de codes et le transfert groupé avec tri AJAX.

### Composants et navigation

Les paramètres globaux servent de valeurs par défaut. width accepte 100, 50, 33 et 25 avec ou sans %. Les blocs se placent sur une colonne sur téléphone. numbers=off masque le compteur; le balayage change de photo. Le menu mobile arrive de droite. Le fil d’Ariane peut être désactivé dans les réglages généraux.

```text
[gal width=50 numbers=off arrows=on fit=cover]/images/myfotos/[/gal]
[fotos width=50% columns=3 captions=on]/images/myfotos/[/fotos]
[files width=33 icons=off size=on download=on]/files/myfiles/[/files]
```

### Langues, apparence et prix

Les paramètres régionaux définissent les langues, la devise, le fuseau et les formats. Placez index_en.php à côté d’index.php pour traduire une page; le contenu original reste disponible. Le type Prix utilise la devise choisie. Prévisualisez les couleurs, changez le mode immédiatement, importez les logos clair et sombre et testez Roboto Condensed avec votre texte.

```text
/contacts/index.php → /contacts/index_en.php
```

### Formulaires, demandes et intégrations

Configurez Google reCAPTCHA v2 dans les réglages CAPTCHA, globalement ou par formulaire. Messages et rappels affichent 20 entrées par page, les non lus en premier. Intégrations propose Zadarma, Telegram, les statistiques et les cartes Google ou Yandex avec regroupement facultatif. La fenêtre des bibliothèques teste la version active et affiche le code. CodeMirror propose Auto, HTML, CSS, JS, PHP et RAW.

```text
[map lat=55.7558 lng=37.6173 title="Office" zoom=13 provider=yandex url="/contacts/" button="Open"]
```

```text
[callback button="Call me" title="Callback" submit="Send" placeholder="+7(900)123-45-67"]
```

### Modules PHP

Dans Modules PHP, choisissez le nom, la description et la disponibilité. server-date suit le format de date régional. Les développeurs installent les fichiers exécutables dans un répertoire protégé.

```text
[php]server-date[/php]
```
