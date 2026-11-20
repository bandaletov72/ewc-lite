# Français · Eurowebcart CMS Light

- [Gestion](fr/readme.md)
- [FAQ Administrateur](fr/faq.md)
- [Modèles et composants](fr/templates.md)

## Menu mobile · 1.6.7

- Afficher dans le menu principal
- Afficher dans le menu mobile
- Masquer dans le menu mobile


## Modifier les données sur place · 1.6.6

En mode édition, les blocs de données, les formulaires [formX], les fragments [tel] et les composants associés proposent les actions Données et Plein écran. La liste des vues affiche les modèles enregistrés. Choisissez Dossier racine pour déplacer une rubrique au premier niveau. Utilisez [list-pages] pour lister les pages publiées de la rubrique et de ses sous-dossiers. Cliquez à l’extérieur ou appuyez sur Échap pour fermer le menu des langues.


## Fichiers de configuration, cartes et modèles · 1.6.5

Ouvrez Maintenance → Fichiers de configuration pour modifier .htaccess, robots.txt et sitemap.xml. La génération utilise les pages publiées incluses dans le plan du site. La version précédente de .htaccess reste disponible.

Renseignez la latitude et la longitude. [map] montre le lieu de la page, [maps] réunit les lieux des pages publiées. center et zoom règlent le cadrage. Le fournisseur est défini dans Intégrations.

[main-menu] est le menu sous l’en-tête, avec des sous-rubriques déroulantes. Créez un modèle personnalisé et choisissez sa visibilité selon l’appareil. Dans les fragments, le bouton d’ajout de domaine ouvre le formulaire. Utilisez [privacy] pour le lien de confidentialité.

Dans Intégrations, renseignez les identifiants de JivoSite, Microsoft Clarity et Umami Cloud, le script Botfaqtor ou le webhook entrant Slack. Les liens d’aide sont à côté des champs. Zadarma permet de vérifier le solde et de tester un SMS ou un appel. Les tests peuvent être facturés. Pour [payment], renseignez les liens YooMoney, PayPal et SBP. Vérifiez le statut chez le prestataire.

Les éditeurs HTML et visuel proposent des icônes de mise en forme, les titres H1–H5 et des listes. Placez le curseur puis choisissez un modèle, un composant, une image ou une icône. Les formulaires et les options des composants sont disponibles. Activez Font Awesome pour insérer ses icônes.

Cliquez sur un dossier pour gérer la rubrique à droite. Choisissez un parent, les menus ordinateur et mobile ou une URL de redirection. Une rubrique vide liste ses sous-rubriques. Dans Maintenance, ajoutez des règles comme /contact.html => /contacts/ et modifiez les pages dans /error/. Sur mobile, la langue et la recherche utilisent des icônes.

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

## Paramètres de la page · 1.6.8

Rédigez le contenu dans Édition. L’adresse, le menu et le calendrier de publication figurent dans les paramètres de la page. SEO contient les titres, la description, les mots-clés et l’inclusion dans le sitemap. Les coordonnées et exemples sont dans Cartes.

Laissez les dates vides pour supprimer les limites. Le fuseau horaire du site s’applique. Une page masquée doit être publiée séparément.

- `[map]` — carte avec le lieu de la page actuelle.
- `[maps]` — carte avec les lieux de toutes les pages publiées.

## Bannières et éditeur d’images · 1.6.8

Gérez les ensembles et diapositives dans Composants. Utilisez les photos d’un dossier, ajoutez un texte et un bouton ouvrant un lien, un formulaire ou un rappel, puis planifiez l’affichage. Des actions sont disponibles en mode édition. Le sélecteur d’images permet de parcourir les dossiers, importer, supprimer et créer des copies pivotées ou redimensionnées avec GD. Les icônes Font Awesome sont accessibles par leur nom.

```text
[banners]
[banners set=summer arrows=on numbers=off]
[banners]/images/myfotos/[/banners]
```

## Langues · 1.6.9

L’onglet Langues présente les langues de la page. Ouvrir affiche la version enregistrée. Créer une traduction ouvre un éditeur vide.

Une sous-section est créée dans la section active, une section de même niveau à côté, et une section racine au premier niveau du site.

## Compatibilité PHP et navigation · 1.6.10

La version minimale de PHP est 7.2. Le site et l’administration prennent en charge PHP 8.6. Pour les modules et modèles personnalisés, utilisez la syntaxe de la version installée sur le serveur.

L’élément courant de [bread-crumbs] affiche les sections publiées du même niveau qui figurent dans le menu principal. L’URL configurée est utilisée dans les menus, listes de sections, fils d’Ariane, résultats et suggestions de recherche.

La corbeille retire une ligne de l’éditeur d’infobloc. Supprimer une colonne retire toutes ses valeurs. Les changements sont enregistrés à la sauvegarde, y compris automatiquement après un réagencement.

### Sauvegardes du site

Le ZIP privé contient pages, médias, infoblocs, réglages, modèles et modules PHP. Le compte administrateur et les demandes sont stockés séparément. PHP Zip est requis.

Remplacer le contenu et les réglages. Une copie de l’état actuel sera créée auparavant.

### Nettoyer les fichiers inutilisés

L’analyse cherche les médias sans référence dans le contenu enregistré. Vérifiez la liste et sélectionnez les fichiers. Une sauvegarde est créée au préalable. Supprimer un code de composant conserve son dossier et ses fichiers.

### Choisir un point sur la carte

Dans la console Yandex Maps, créez une clé pour « JavaScript API » et limitez-la par référent HTTP. Dans l’onglet Cartes de la page, choisissez un point puis appliquez ses coordonnées.

Sélectionnez le texte à aligner ou à déformater. Cliquez sur une image pour ses réglages, déplacez-la ou redimensionnez-la depuis son coin. Un lien peut viser une URL, un courriel, un téléphone ou une ancre. Un bouton ouvre un lien, un formulaire, un rappel ou un lien de paiement du prestataire.

Les onglets HTML et Éditeur visuel changent le mode. SUP, SUB et Paragraphe insèrent les éléments correspondants. Les codes figurent à côté des composants. Les dossiers manquants sont créés à l’insertion. Leurs noms acceptent lettres latines, chiffres, traits d’union et tirets bas. L’onglet Langues ouvre les réglages de langue.

```text
[button action="url" label="Contacts" url="/contacts/"]
[button action="form" label="Write to us" form="form1"]
[button action="callback" label="Call me"]
[button action="payment" label="Pay" amount="1250.00" currency="RUB" yoomoney="410010000000000"]
[map]
[maps]
```

## Vente · 1.6.11

Réglez le prix et le moyen de paiement dans Vente, avec des valeurs par langue. Insérez [item-price] et [item-buy-button]. La commande est enregistrée avant le paiement. L’état peut être communiqué par email et SMS Zadarma.

Commande reçue · Paiement accepté · Expédiée · Clôturée · Annulée

```text
[item-price]
[item-buy-button]
```

Sélectionnez un fichier ou une URL pour insérer une image. Cliquez sur l’image pour régler sa taille, son alignement, ses quatre marges et son CSS. Son alignement est indépendant du texte. En HTML, les blocs apparaissent sur des lignes distinctes. [list-pages] est dans les composants. Le logo du CMS reprend les couleurs du thème.

## Graphique du bloc de données

Dans l’éditeur de page, ouvrez l’insertion de composants et choisissez un tableau ou un graphique. Activez Chart.js pour les graphiques et sélectionnez la colonne des libellés et les colonnes de type Nombre ou Prix. Les secteurs, anneaux et aires polaires utilisent une série non négative. Si la bibliothèque est désactivée, le tableau reste visible.

Les formulaires, modèles, fragments enregistrés et composants déclarés apparaissent à la réouverture de l’éditeur. L’ID d’un nouveau groupe de bannières accepte A–Z, a–z, les tirets et les traits de soulignement. Après l’envoi d’images dans un dossier [banners], un lien de modification apparaît près de chaque fichier. En mode édition, seule la diapositive choisie s’ouvre dans une fenêtre. Un lien distinct mène aux réglages par défaut. Le gestionnaire près du bouton Enregistrer du carrousel, de la galerie et de la liste permet les envois, la suppression, le tri et la création de sous-dossiers (A–Z, a–z, 0–9, - et _). Les couleurs sont visibles dans des carrés.

```text
[iblock=table]/data/myiblock.txt[/iblock]
[iblock=charts type=line labels=month values=sales,orders]/data/myiblock.txt[/iblock]
[banners set=summer-promo]
```

## En-tête et pied de page de l’administration · 1.6.12

Sur ordinateur, Eurowebcart CMS et Light accompagnent le logo avec la version installée. Sur téléphone, le symbole s’aligne avec les boutons. Le pied de page affiche l’adresse du dépôt. Les paramètres permettent de vérifier la mise à jour cumulative.

## En-tête et pied de page de l’administration · 1.6.13

Sur ordinateur, Eurowebcart CMS et Light accompagnent le logo avec la version installée. Sur téléphone, le symbole s’aligne avec les boutons. Le pied de page affiche l’adresse du dépôt. Les paramètres permettent de vérifier la mise à jour cumulative. Le libellé Accueil dans la liste suit la langue de l’administration. Le logo du pied de page affiche Eurowebcart CMS et LIGHT sur deux lignes.

## Informations PHP

Dans Maintenance, phpinfo() présente la version, les modules et la configuration PHP aux administrateurs connectés. Les variables d’environnement et de requête sont masquées. Réglez error_reporting dans les paramètres généraux. La valeur du serveur s’applique par défaut.

## Masquer l’arborescence · 1.6.14

Sur ordinateur, la flèche replie l’arborescence et agrandit l’éditeur. Le bouton au-dessus de l’éditeur la rétablit. Le navigateur mémorise votre choix.
