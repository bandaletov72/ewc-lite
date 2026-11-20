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
