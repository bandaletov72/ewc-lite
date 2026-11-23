# Modèles et composants

## Modèles de mise en page

### En-tête, menu et pied de page

La page entière relie automatiquement ces blocs. Les versions générales sont éditées dans « Modèles de conception » et pour un domaine individuel - dans la section « Domaines ».

```text
[header]
[mobile-menu]
[menu]
[footer]
```

### Insérer une autre page

Insère le contenu d'une page publiée. L'imbrication maximale est de dix niveaux. La répétition d'une page dans la même chaîne est ignorée.

```text
[include]/contacts/[/include]
```

### Liens dans le menu

La case à cocher « Afficher dans le menu » ajoute un lien vers les menus standards. Si le nom est vide, le titre est pris. Si vous décochez la case d'une page de dossier, ses sous-pages disparaîtront également du menu.

```text
MenuVisible : 1
Menu : Contacts
```

### CSS et Javascript

Le code général est édité dans l'onglet « CSS et JS ». Le code de la page est édité dans le bloc « Pages CSS et JavaScript » et inclus après le code général. Collez le contenu CSS et JavaScript dans les champs.

```text
.page-content { max-width: 72rem; }
```

```text
document.documentElement.classList.add('custom-script-loaded');
```

### En-têtes de services

Dans la section « Modèles de conception → En-têtes de service », vous pouvez insérer des balises méta générales et des codes de compteur à l'intérieur de l'en-tête ou avant le corps de fermeture. Canonical est ajouté automatiquement sur les pages complètes.

```text
<meta name="theme-color" content="#065f63">
```

### Icônes géniales de police

Font Awesome Free 7.3.1 est connecté. Le code court fonctionne dans les blocs de contenu et de conception HTML. Des styles solides, réguliers et de marque sont disponibles. Ajoutez du texte à côté de l'icône si elle représente une action.

```text
[fa-icon=house] [fa-icon=regular:envelope] [fa-icon=brands:github]
```

```text
<i class="fa-icon fa-solid fa-phone" aria-hidden="true"></i> Contact
```

### Bibliothèques et polices

Les connexions sont gérées dans « Paramètres → Bibliothèques et polices ». Pour Material UI, utilisez JavaScript modulaire. La police Google sélectionnée remplace la police système du site.

```text
<script type="module">import { Button } from "@mui/material";</script>
```

```text
GLightbox({selector: ".glightbox"});
```

## Variables, sous-modèles et date

### Variables du site

Ajoutez des entrées à « Paramètres → Général ». Insérez la clé dans le texte, l'en-tête, le titre du site ou le pied de page.

```text
[city] = Moscou
[spb] = Saint-Pétersbourg
```

```text
Service dans la ville [city]
```

### Fragments de modèle

Dans la section Sous-modèles, spécifiez un nom, une description et une valeur. Pour un domaine, on prend d'abord sa valeur, puis sa valeur générale. Le sous-modèle peut contenir du HTML. Le balisage dans les en-têtes est supprimé.

```text
[tel]
```

```text
[title]
```

### Date et heure

Le fuseau horaire est tiré de PHP. [year] - année, [month] - mois, [day] - jour du mois, [time] - heures et minutes, [dmY] et [date] - date JJ.MM.AAAA, [datetime] - date et heure.

```text
© [year] [city]
Date [dmY], heure [time]
```

### Nom et auteur du CMS

Le code [author] génère Eurowebcart CMS Light, le nom de l'auteur Бандалетов Павел et un lien vers GitHub. Dans un pied de page standard, la signature apparaît automatiquement. Dans votre propre pied de page HTML, collez le code là où vous le souhaitez.

```text
[author]
```

### Modèle et politique d'accueil

[page-content] insère le contenu de l’accueil dans [home]. Ouvrez /privacy/ dans Pages pour modifier la politique. [site-name] et [site-url] proviennent des paramètres généraux.

```text
[page-content]
```

```text
[site-name] · [site-url]
```

### Liens et paramètres dans le pied de page

[privacy] renvoie vers /privacy/, [top] remonte en haut de la page et [cookies] ouvre les paramètres de consentement.

```text
[privacy]
[cookies]
[top]
```

### Fonctions PHP courantes

Dans la section « Modèles de conception → Fonctions », modifiez functions.php. Dans le code PHP, translit() ou ew_call() appelle une fonction enregistrée. Le short code [func=translit] est disponible dans le contenu et dans le champ « HTML personnalisé ».

```text
[func=translit value="Москва и München"]
```

```text
$var = translit("Moscou");
```

```text
echo ew_escape(ew_call("translit", "München"));
```

## Carrousel, galerie de photos et fichiers

### Carrousel [gal]

Affiche les photos d'un dossier. les flèches et les chiffres acceptent l'activation ou la désactivation, l'effet – glisser, fondre ou aucun, ajuster – contenir ou couvrir.

```text
[gal]/images/myfotos/[/gal]
```

```text
[gal arrows=on numbers=off effect=fade fit=cover width=50]/images/myfotos/[/gal]
```

### Galerie de photos [fotos]

Affiche une grille réactive. colonnes — auto ou un nombre de 1 à 6, ratio — 4x3, 1x1, 16x9 ou original, ajustement — couvrir ou contenir. les légendes et les liens sont activés ou désactivés.

```text
[fotos]/images/myfotos/[/fotos]
```

```text
[fotos columns=4 ratio=1x1 fit=cover captions=on links=off width=50%]/images/myfotos/[/fotos]
```

### Liste des fichiers [files]

Répertorie les fichiers pris en charge. icônes, taille et téléchargement acceptés ou désactivés, commande – asc ou desc. Zip, pdf, xls, xlsx, doc, docx, png, gif, jpg, jpeg, mp4, mov et mp3 sont autorisés.

```text
[files]/files/myfiles/[/files]
```

```text
[files icons=on size=on order=desc download=on width=33]/files/myfiles/[/files]
```

## Formulaires et rappel

### Formulaire [formN]

Dans la section « Formulaires », configurez les champs, leur longueur maximale, le texte du bouton et le destinataire. « Utiliser CAPTCHA » active le widget Google reCAPTCHA v2 avec vérification des réponses sur le serveur. Les demandes sont envoyées via AJAX et sont visibles dans la section Messages.

```text
[form1]
[form2]
```

### Modèle de formulaire de courrier

Le sujet et le texte sont stockés séparément pour chaque formulaire. Dans votre modèle d'e-mail, insérez les clés des champs entre accolades. Les listes de destinataires et de copies sont séparées par des virgules.

```text
Bonjour {name} !
Téléphone : {phone}
Message : {message}
```

### Rappelez [callback]

Ouvre un formulaire avec un numéro de téléphone. L'application est enregistrée dans la section « Rappels ». L'étiquette du bouton peut être définie dans le code. Avec le fournisseur « Non », l'envoi vers la téléphonie est désactivé.

```text
[callback]
[callback=Вызвать мастера]
```

## Blocs de données

### Tableau et liste des téléphones

Créez un ensemble de données dans la section "Infoblocs". A à Z, a à z, 0 à 9, les traits d'union et les traits de soulignement sont autorisés dans le nom du bloc d'informations et les clés de colonne. Les espaces ne sont pas autorisés. Le modèle de tableau affiche un tableau, les téléphones affichent des colonnes téléphoniques avec des liens pour appeler. Les codes de fermeture [iblock] et [/iblock] sont pris en charge.

```text
[iblock=table]/data/myiblock.txt[iblock]
[iblock=phones]/data/myiblock.txt[/iblock]
```

## Contacts et réseaux sociaux

### Bloc général et icônes

[contacts] affiche un bloc de contacts, [phones] affiche les numéros de téléphone, [soc-icons] collecte les icônes de tous les réseaux sociaux avec des liens personnalisés.

```text
[contacts]
[phones]
[soc-icons]
```

### Réseaux sociaux individuels

Chaque code affiche un lien vers son réseau social. Les adresses et signatures sont définies dans « Paramètres → Contacts et réseaux sociaux ».

```text
[tg] [vk] [whatsapp] [youtube] [instagram] [max]
```

```text
[wa] [yt] [ig]
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

## Modifier les données sur place · 1.6.6

En mode édition, les blocs de données, les formulaires [formX], les fragments [tel] et les composants associés proposent les actions Données et Plein écran. La liste des vues affiche les modèles enregistrés. Choisissez Dossier racine pour déplacer une rubrique au premier niveau. Utilisez [list-pages] pour lister les pages publiées de la rubrique et de ses sous-dossiers. Cliquez à l’extérieur ou appuyez sur Échap pour fermer le menu des langues.

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

Les modèles sont repliés au départ. Ouvrez un modèle depuis son titre ou utilisez Tout déplier. Appuyez de nouveau pour replier la liste.

Pour changer la largeur du carrousel, activez le mode édition, ouvrez Modifier le composant à côté des bannières et choisissez la largeur du bloc.

## Villes · Téléphone · 1.6.18

Seul le nom de la ville est obligatoire. Le domaine et les téléphones sont facultatifs. Saisissez + et l’indicatif international, par exemple +7 (900) 000-00-00. Un code comme [tel] peut remplacer le numéro.

La ville mémorisée est prioritaire, puis celle du domaine et enfin la première de la liste. Si la liste est vide, les téléphones généraux s’affichent.

Les formulaires, rappels et nouvelles commandes vont à l’adresse de la ville ou à celle du site si elle est vide. Si les deux sont vides, le formulaire garde son propre destinataire. Les liens sociaux vides héritent des réglages du site.

En mode édition, survolez un modèle pour le modifier. Pour tout composant [php] installé, Modifier le module ouvre le code et les réglages dans une fenêtre. L’éditeur de diapositive affiche le nom et le code du formulaire. [banners width=100] occupe toute la fenêtre pour un bloc autonome sans barre latérale. L’éditeur de modèle s’ouvre dans une fenêtre modale sur le site.

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

### Couleurs du site · Polices

Cochez les polices à activer, puis attribuez-les au texte, aux titres ou aux commandes. Votre site héberge les fichiers locaux.

Bibliothèques → Héberger localement

Modèles de page → Créer une page

## Annonce

L’annonce apparaît dans les listes de pages et les résultats de recherche. Les listes privilégient l’image si elle est définie. Dans les menus, l’icône précède le nom de la page.

Dans l’éditeur, cliquez sur « Insérer une liste… ». Choisissez la section actuelle pour `[list-pages]`, ou cliquez sur « Insérer » à côté d’un dossier. `[dir]/sample/[/dir]` liste les pages publiées dans /sample/ et ses sous-sections. `[dir]/[/dir]` commence à la racine.


Saisissez un nom dans le sélecteur d’icônes. Par exemple, home trouve aussi les icônes nommées house.

Ouvrez Composants de la page → [banners] → Diapositives de la bannière pour modifier, ajouter ou réordonner les diapositives du groupe. Modifier le composant ouvre les options du bloc.

Les champs du tableau sont alignés en haut. Faites glisser le coin d’un champ multiligne pour modifier sa largeur et sa hauteur.

Faites glisser le bord droit de l’en-tête ou utilisez les flèches gauche et droite pour redimensionner une colonne.

## Carte des pages de la section · `[maplist]`

Choisissez « Carte des pages de la section » dans les composants ou insérez `[maplist]`. La carte affiche les pages publiées avec des coordonnées dans la section et ses sous-sections. Sur l’accueil, elle couvre tout le site. Chaque fenêtre affiche le titre, l’icône, l’image sur toute la largeur, le texte de l’annonce et un lien. Configurez la carte dans les intégrations.

`[maplist]/sample/[/maplist]` affiche la carte de la section choisie. `[points]/sample/[/points]` présente les pages géolocalisées sous forme de fiches avec titre, image, texte de l’annonce et bouton vers la page.

L’option d’inclusion dans sitemap.xml se trouve dans l’onglet SEO. Activez « Déployer la branche du menu » dans les paramètres de la page. Les sous-pages visibles s’affichent au survol ou après un appui sur la flèche sur mobile.

Insérez `[route]` sur une page avec des coordonnées. Le lien utilise le service de cartes choisi dans les intégrations. provider=yandex ou provider=google force le service. Si les cartes sont désactivées, Yandex est utilisé par défaut.
