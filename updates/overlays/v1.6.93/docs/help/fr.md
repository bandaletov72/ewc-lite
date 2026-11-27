# Français · Eurowebcart CMS Lite

## Premiers pas avec la version 1.6.58

- Activez un site complet dans Modèles de design → Thèmes. Les couleurs et les polices ont leurs propres onglets. Plusieurs familles peuvent être activées et stockées localement.
- Activez les bibliothèques dans Paramètres → Bibliothèques et enregistrez localement les ressources nécessaires. Font Awesome est inclus.
- En mode édition, double-cliquez sur le contenu pour ouvrir l’éditeur en fenêtre modale. Double-cliquez sur le H1 standard pour accéder à son champ dans SEO.
- Les définitions des champs sont communes et les valeurs propres à chaque page. La valeur de la page ouverte peut être enregistrée à la création du champ. La saisie s’adapte au type choisi.
- Pour un nouveau domaine, créez un ZIP complet avec le thème actif et testez une installation vierge. Pour un site existant, effectuez une sauvegarde et utilisez une mise à jour signée.

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

Le code [author] affiche la signature Eurowebcart CMS Lite, le nom de l'auteur Бандалетов Павел et un lien vers GitHub. Pour choisir où placer votre signature dans votre pied de page HTML personnalisé, collez [author] dans la ligne appropriée.

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

L’analyse inclut la médiathèque Marketplace. Les photos de produits et de variantes, les logos et les fichiers référencés par les sauvegardes du catalogue sont protégés. La case sélectionne les fichiers de la page en cours. Chaque ligne affiche la taille du fichier. Cliquez sur une vignette pour ouvrir la visionneuse.

### Choisir un point sur la carte

Dans la console Yandex Maps, créez une clé pour « JavaScript API » et limitez-la par référent HTTP. Dans l’onglet Cartes de la page, choisissez un point puis appliquez ses coordonnées.

Sélectionnez le texte à aligner ou à déformater. Cliquez sur une image pour ses réglages, déplacez-la ou redimensionnez-la depuis son coin. Un lien peut viser une URL, un courriel, un téléphone ou une ancre. Un bouton ouvre un lien, un formulaire, un rappel ou un lien de paiement du prestataire.

Les onglets HTML et Éditeur visuel changent le mode. SUP, SUB et Paragraphe insèrent les éléments correspondants. Les codes figurent à côté des composants. Les dossiers manquants sont créés à l’insertion. Leurs noms acceptent lettres latines, chiffres, traits d’union et tirets bas. L’onglet Langues ouvre les réglages de langue. Les panneaux d’insertion s’ouvrent au-dessus de la zone d’édition. Cliquez sur « Fermer » en haut du panneau ou appuyez sur Échap pour le fermer et désactiver le bouton. Les flèches de la barre d’outils annulent et rétablissent jusqu’à 10 modifications de texte via le serveur. L’icône d’horloge charge le dernier texte enregistré. Confirmez avant de quitter la page si des modifications restent à enregistrer. L’icône Enregistrer avant les flèches s’allume en présence de modifications non enregistrées. Sélectionnez un passage et ouvrez Style du texte. Choisissez la taille, la couleur du texte, la couleur du fond ou une police activée dans les paramètres du site.

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

La colonne Bouton de formulaire affiche un bouton Commander dans chaque ligne configurée. Choisissez le formulaire par son nom. En CSV, indiquez son code, par exemple form86.

Le type « Tag masqué » ajoute des filtres au-dessus du tableau. Saisissez un ou plusieurs tags séparés par des virgules dans une cellule. « TOUT » affiche toutes les lignes.

## En-tête et pied de page de l’administration · 1.6.12

Sur ordinateur, Eurowebcart CMS et Light accompagnent le logo avec la version installée. Sur téléphone, le symbole s’aligne avec les boutons. Le pied de page affiche l’adresse du dépôt. Les paramètres permettent de vérifier la mise à jour cumulative.

## En-tête et pied de page de l’administration · 1.6.13

Sur ordinateur, Eurowebcart CMS et Light accompagnent le logo avec la version installée. Sur téléphone, le symbole s’aligne avec les boutons. Le pied de page affiche l’adresse du dépôt. Les paramètres permettent de vérifier la mise à jour cumulative. Le libellé Accueil dans la liste suit la langue de l’administration. Le logo du pied de page affiche Eurowebcart CMS et LIGHT sur deux lignes.

## Informations PHP

Dans Maintenance, phpinfo() présente la version, les modules et la configuration PHP aux administrateurs connectés. Les variables d’environnement et de requête sont masquées. Réglez error_reporting dans les paramètres généraux. La valeur du serveur s’applique par défaut.

## Masquer l’arborescence · 1.6.14

Sur ordinateur, la flèche replie l’arborescence et agrandit l’éditeur. Le bouton au-dessus de l’éditeur la rétablit. Le navigateur mémorise votre choix.

## Masquer l’arborescence · 1.6.15

Sur ordinateur, la flèche replie l’arborescence et agrandit l’éditeur. Le bouton au-dessus de l’éditeur la rétablit. Le navigateur mémorise votre choix.

## Choisir un point sur la carte

Dans la console Yandex Maps, créez une clé pour « JavaScript API » et limitez-la par référent HTTP. Dans l’onglet Cartes de la page, choisissez un point puis appliquez ses coordonnées. Pour un test local, saisissez localhost sans port comme référent HTTP, attendez l’activation puis ouvrez l’administration via localhost.

## Recherche dans le menu · 1.6.16

[search] affiche la recherche à côté des liens [main-menu] sur ordinateur et dans [mobile-menu] sur téléphone. Le mode se choisit dans les réglages généraux. Un [search] dans la page est prioritaire.

`[search]`

## Recherche dans le menu · 1.6.17

[search] affiche la recherche à côté des liens [main-menu] sur ordinateur et dans [mobile-menu] sur téléphone. Le mode se choisit dans les réglages généraux. Un [search] dans la page est prioritaire. Le champ de recherche est visible dès l’ouverture du menu mobile. [lang] affiche les langues dans ce menu et dans l’en-tête sur ordinateur.

`[search]` · `[lang]`

Le bas du menu mobile affiche les coordonnées et les réseaux sociaux configurés.

## Sous-titre du site

Dans les réglages généraux, renseignez le sous-titre. Il apparaît sous le nom dans l’en-tête. Sur téléphone, le nom est affiché en plus petit.

## Villes

Dans Villes, associez un domaine enregistré et ajoutez jusqu’à cinq numéros. Un champ de téléphone accepte un code comme [tel]. [cities] affiche le sélecteur, [phones] la liste des numéros renseignés et [tel], [tel2]–[tel5] les numéros individuels. Le navigateur mémorise le choix et actualise les numéros sans recharger la page.

`[cities]` · `[phones]` · `[tel]`

## Animation au défilement

Dans les réglages généraux, choisissez l’effet d’apparition des blocs au défilement. L’animation est désactivée par défaut et respecte la préférence de réduction des mouvements.

## Modèles de page

Sélectionnez un modèle en créant une page. Il remplit le contenu et les champs SEO, que vous pourrez modifier. Ajoutez vos propres modèles dans Modèles de page. Les cartes, les formulaires et les achats utilisent les réglages de leurs composants.

Le CSS et le JavaScript du modèle sont copiés dans la page. Ajoutez vos classes dans le HTML et définissez leur style dans le CSS du modèle.

### Classes réutilisables

Conteneur, grille adaptative, section mise en avant, carte et prix mis en valeur.

`.page-preset` · `.page-preset-grid` · `.page-preset-hero` · `.page-preset-card` · `.page-preset-price`

Choisissez « Modifier » pour modifier un modèle. « Enregistrer le modèle » applique les modifications aux nouvelles pages. « Dupliquer » crée un modèle distinct. Dans l’éditeur de page, vous pouvez ouvrir le modèle sélectionné ou en créer un à partir de la page enregistrée. Dans le formulaire du modèle, choisissez « Insérer une icône » et recherchez-la par nom. Vous pouvez aussi saisir un code comme `[fa-icon=house]`.

Les modèles sont repliés au départ. Ouvrez un modèle depuis son titre ou utilisez Tout déplier. Appuyez de nouveau pour replier la liste.

Pour changer la largeur du carrousel, activez le mode édition, ouvrez Modifier le composant à côté des bannières et choisissez la largeur du bloc.

## Villes · Téléphone · 1.6.18

Seul le nom de la ville est obligatoire. Le domaine et les téléphones sont facultatifs. Saisissez + et l’indicatif international, par exemple +7 (900) 000-00-00. Un code comme [tel] peut remplacer le numéro.

La ville mémorisée est prioritaire, puis celle du domaine et enfin la première de la liste. Si la liste est vide, les téléphones généraux s’affichent.

Les formulaires, rappels et nouvelles commandes vont à l’adresse de la ville ou à celle du site si elle est vide. Si les deux sont vides, le formulaire garde son propre destinataire. Les liens sociaux vides héritent des réglages du site.

En mode édition, survolez un modèle pour le modifier. Pour tout composant [php] installé, Modifier le module ouvre le code et les réglages dans une fenêtre. L’éditeur de diapositive affiche le nom et le code du formulaire. [banners width=100] occupe toute la fenêtre pour un bloc autonome sans barre latérale. L’éditeur de modèle s’ouvre dans une fenêtre modale sur le site. En mode édition, double-cliquez dans une zone libre du modèle. Cette indication apparaît sous le bouton de modification au survol. En cas de chevauchement, le modèle suivant est prioritaire. Le bouton de modification de la diapositive ouvre la diapositive choisie. La barre du site affiche les composants, modèles et sous-modèles de la page active. Dans l’administration, ouvrez Composants de la page à côté de Langues pour modifier leurs paramètres et données. Double-cliquez sur le contenu pour ouvrir l’éditeur de la page en cours. L’éditeur s’ouvre dans une fenêtre au-dessus du site. Après l’enregistrement, fermez-la pour voir les modifications sur la page. Double-cliquez sur le titre H1 pour accéder directement à son champ dans l’onglet SEO.

Le gestionnaire vérifie les références avant la suppression et sauvegarde les dossiers non vides. Une bannière peut avoir un titre et un sous-titre.

Le numéro est vérifié pendant la saisie. Un numéro russe commençant par 8 passe à +7. Le formulaire disparaît après l’envoi et la confirmation reste visible en rouvrant la fenêtre.

L’indicatif du pays choisi est ajouté aux numéros sans +. Les numéros commençant par + gardent leur indicatif.

## Thèmes de design

Copiez le fichier dans le thème, puis enregistrez-le. Les fichiers absents utilisent la version fournie. Les modifications, nouveaux codes, historiques et réglages de domaine restent dans le thème actif. Ajoutez styles et scripts dans css/theme.css et js/theme.js.

`templates/themes/demo/header.php` · `templates/themes/demo/components/banners.php`

Les langues figurent au bas du menu mobile. Sur ordinateur, le globe est à côté de la recherche dans le menu sous l’en-tête.

Les galeries, bannières et listes cherchent d’abord dans le thème actif, puis dans les fichiers communs. Les nouveaux dossiers, fichiers envoyés, logos et favicons vont dans le thème. La sauvegarde conserve le thème et ses réglages.

Choisissez la largeur de la fenêtre (width=page), du contenu (width=content) ou du bloc parent (width=parent). height règle la hauteur sur ordinateur et mobile-height sur mobile. auto conserve la taille naturelle.

`[banners width=page height=520 mobile-height=240]` · `[form1 width=parent]`

Le dernier élément du fil d’Ariane liste les rubriques de même niveau publiées, y compris celles absentes du menu principal. Les liens utilisent les URL configurées.

## Thèmes de design · КОНТУР · 1.6.19

Un thème de site complet change le design, les pages, les blocs de données et les réglages. Les autres thèmes utilisent les pages communes.

Une coche marque un fichier du thème prioritaire sur le fichier standard du CMS. Vous pouvez le modifier ou le restaurer.

**Restaurer la version d’origine**

### Portfolio

Ouvrez kontur-works dans Contenu → Blocs de données. Saisissez le titre, la catégorie, la description, les photos séparées par des virgules et un lien facultatif. La première photo sert de couverture. Conservez les mêmes colonnes pour d’autres collections.

```text
[portfolio]
[iblock=portfolio limit=2]/data/kontur-works.txt[/iblock]
[maps source=kontur-map]
```

« Тихий сад » est un site complet pour un studio de design paysager. Choisissez-le dans Modèles de mise en page → Thèmes de design, puis appliquez-le. Modifiez les blocs garden-projects et garden-prices et le groupe de bannières garden-cover. Ajoutez vos coordonnées et le destinataire du formulaire [form85].

Le thème Dezinfektori comprend 32 services, le formulaire [form86] et le formulaire d’avis [form87]. Ouvrez l’avis dans Messages. Avec l’accord de l’auteur, copiez le nom, le texte et la note dans dez-reviews, puis activez la publication. Les pages de services présentent les risques, des illustrations avant-après et des récits de fiction. Modifiez ces blocs dans Pages.

```text
[iblock=reviews limit=6]/data/dez-reviews.txt[/iblock]
```

La page d’accueil affiche les avis approuvés de toutes les pages si le filtre d’avis est vide.

Chaque page du catalogue propose un formulaire d’avis. Après approbation dans Avis, il apparaît sur sa page d’origine.

### Couleurs du site · Polices

Cochez les polices à activer, puis attribuez-les au texte, aux titres ou aux commandes. Votre site héberge les fichiers locaux.

Bibliothèques → Héberger localement

Modèles de page → Créer une page

## Champs personnalisés

Saisissez dans la nouvelle ligne la valeur pour la page en cours et enregistrez-la avec les réglages du champ.

## Avis

Insérez le code dans l’éditeur de page. Les visiteurs saisissent leur nom, rédigent un avis et attribuent de 1 à 5 étoiles. La rubrique Avis permet d’approuver, de refuser, de modifier ou de supprimer un avis. Le bouton de chargement ajoute les dix suivants.

Saisissez un chemin comme /contacts/ ou laissez le champ vide pour afficher tous les avis. Le formulaire enregistre sa page d’origine. Seuls les avis approuvés sont visibles. Le CAPTCHA utilise les réglages généraux du CMS.

```text
[reviews]
```

Le bouton Laisser un avis ouvre le formulaire.

## Progression de la mise à jour · Progression de l’indexation

Téléchargement de l’archive de mise à jour. Téléchargement de la signature. Vérification de la signature et des sommes de contrôle de l’archive. Les pages, les paramètres et les fichiers utilisateur sont conservés. Préparation des fichiers et des copies temporaires de restauration. Utilisation des modèles, styles et scripts fournis avec le CMS. Les pages et paramètres actuels sont conservés. Utilisation du design et des pages fournis avec le thème. Les paramètres et les autres pages sont conservés. Sauvegarde des pages, paramètres et médias. Vérification de la sauvegarde. Sauvegarde enregistrée dans Maintenance. Collecte des pages. Traitement des pages et de leurs traductions. Enregistrement de l’index de recherche.

**Design et ajout des pages manquantes**

Application du design et ajout des pages de navigation manquantes. Les pages et réglages existants sont conservés.

**100% · Terminé**

En mode édition, survolez la valeur du sous-modèle et ouvrez son éditeur. Enregistrez et fermez la fenêtre pour voir la page mise à jour.

Le bouton de modification de [reviews] ouvre les réglages et la modération des avis dans une fenêtre.

**Avis par lot · Texte du bouton « Charger plus »**

La taille du lot vaut pour le premier chargement et pour chaque clic. Laissez le texte vide pour utiliser le libellé par défaut dans la langue de la page.

Le bouton « Données » ouvre l’éditeur de [iblock]. Pour les autres composants, utilisez « Paramètres ».

Si l’éditeur ne se charge pas dans la fenêtre, cliquez sur « Réessayer » ou ouvrez-le en plein écran.

Insérez [reviews page=current] pour afficher d’abord les avis de la page ouverte, puis ceux des autres pages, du plus récent au plus ancien.

Version du thème installée · Version du thème incluse avec le CMS

Choisissez Design et ajout des pages manquantes pour mettre à jour les codes des avis.

La vérification des mises à jour charge la description de la version depuis GitHub.

Approuver automatiquement les avis

Les avis avec des liens ou des mots bloqués restent en modération. La liste se modifie dans «Antispam». Les mises à jour GitHub ajoutent les nouvelles règles et conservent vos modifications. Les masques IPv4 acceptent * et les plages inclusives comme 189.10-17.*.*. Les adresses IPv4 et IPv6 exactes sont acceptées.

Adresse `[address]` · E-mail public `[email]` · Horaires `[hours]`

Les coordonnées de la ville apparaissent dans l’en-tête. Les champs vides utilisent les valeurs communes.

Les versions installée et GitHub des filtres figurent près du bouton de mise à jour.

## Mises à jour · Sauvegardes

Le badge rouge compte les mises à jour du CMS, du thème actif et des filtres antispam. Choisissez les jours et la plage horaire des mises à jour automatiques. La mise à jour automatique d’un thème applique uniquement son apparence. Le point d’exclamation indique qu’aucune sauvegarde ne date de moins d’un mois. Exécutez la commande toutes les 15 minutes. `php update.php --scheduled`

- Modèles de mise en page → Fragments
- Villes et domaines

## Marketplace

La place de marché est désactivée à l’installation. Ouvrez sa rubrique dans l’administration pour configurer la vitrine et importer du CSV ou du YML.

`[shop-categories]` · `[shop-items category=drills]` · `[shop-basket]` · `[register]` · `[account]`

Dans **Marketplace → Catalogue → Produits**, filtrez par nom, référence, code-barres, catégorie ou statut. Affichez d’autres champs avec **Colonnes**. Modifiez le statut, le prix, la référence, le code-barres et les stocks dans la fiche produit. Ajoutez des entrepôts dans **Entrepôts**.

Triez avec les flèches des en-têtes et enregistrez les prix dans la liste. Pour les produits avec variantes, ouvrez la fourchette de prix.

Insérez `[shop]` dans une page pour afficher la boutique avec le thème du site. Dans **Marketplace → Filtres**, réglez la position, le prix, les attributs et le filtrage en direct. Gérez les courriels dans **Modèles de design → Modèles de courriel**. Utilisez les icônes Grille et Liste pour changer la vue. Le tri s’applique immédiatement.

Les prix, codes-barres, stocks et attributs numériques sont enregistrés en quittant le champ. Utilisez Colonnes pour choisir les champs visibles et déplacez les en-têtes ou leurs bords pour ajuster leur ordre ou leur largeur. Sélectionnez les produits pour les actions groupées. Réglez l’arrondi des prix dans les paramètres de la boutique. La boutique de thèmes GitHub propose des pages et des produits de démonstration à sélectionner.

Ajouter au panier ouvre une fenêtre. Les quantités et les suppressions sont mises à jour sans rechargement.

Dans Marques, ajoutez une description, un site et un logo. Les cartes ouvrent les produits de la marque.

[Marketplace](../marketplace.md)

Dans le catalogue, Variantes ouvre l’option correspondante. Caractéristiques définit les champs des fiches et filtres. Plus contient la galerie, les prix supplémentaires, les lots et les redirections. Sélectionnez produits et catégories par nom, et photos dans le gestionnaire. Le panneau CSV permet l’édition en lot.

Les commandes s’affichent par pages de 20. Ajoutez des statuts et utilisez Imprimer / PDF pour le bon de livraison avec adresse, destinataire et références. Les statistiques affichent graphiques et tableau. Configurez les clés et installez les profils GitHub signés sous Intégrations. Vérifiez le paiement chez le prestataire.

Dans la boutique de thèmes, sélectionnez les produits de démo et confirmez le remplacement. L’index est mis à jour automatiquement. Restaurez le catalogue et les marques depuis les sauvegardes du catalogue. Les commandes sont conservées. Dans Couleurs du site, appliquez la palette au site, à l’administration ou aux deux. Dans Marketplace → Exporter → Vérifier le flux YML, ouvrez un produit pour corriger les champs signalés.

Dans l’éditeur HTML, choisissez [items] ou [carousel] sous Composant. Sélectionnez une catégorie, un terme de recherche, un tri et des filtres. [items] affiche une liste ou une grille. [carousel] propose des colonnes distinctes pour mobile et ordinateur. Chaque bloc a sa propre pagination.

Choisissez le dossier du catalogue dans les réglages de la marketplace. Sous /test/, le catalogue se trouve à /test/catalog/. Activez les URL permanentes pour conserver /test/product/article/ lors du déplacement du produit. Une URL de catégorie bénéficie d'une redirection 301 après le déplacement. Les modèles de la boutique s’ouvrent dans une fenêtre. Utilisez les codes entre crochets. Le balisage Product est ajouté automatiquement. [categories] affiche les sous-catégories en liste ou en grille. Une photo récente sert d’image de remplacement. L’indexation du site actualise aussi le catalogue.

### Modèles de la marketplace

Modifiez les pages de la boutique et les fragments de mise en page dans des fenêtres.

### Sections à importer

Ajouter au catalogue actuel. Les produits correspondants sont mis à jour. Le reste est conservé. Remplacer le catalogue. Seules les sections choisies sont importées après sauvegarde.

Renseignez le prix précédent de chaque variante. Ajoutez des photos avec leurs légendes. Le champ video ouvre la vidéo en plein écran.

Activez la commande rapide à retirer dans la fiche produit. Le bouton Commander ouvre un formulaire avec un seul champ pour le téléphone.
