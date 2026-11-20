# Eurowebcart CMS Light

Auteur - Бандалетов Павел. [Projet original et mises à jour](https://github.com/bandaletov72/php-site-updates).

Le projet est conçu pour PHP 7.2. Aucune base de données, Composer ou assemblage frontend requis.

[FAQ Administrateur](faq.md) est également disponible dans l'onglet « Aide ».

[Aide pour la version actuelle en dix langues](../README.md) décrit la recherche, l'édition, la localisation et la maintenance du site. [Admin FAQ in English](../en/faq.md).

Pour une maintenance planifiée, ajoutez la commande `php update.php --scheduled` au planificateur d'hébergement avec une période de 15 minutes. Dans la section Maintenance, vous pouvez activer indépendamment la réindexation et définir l'intervalle de vérification pour les mises à jour GitHub signées.

## Lancement local

Accédez au répertoire `php-site` et lancez le serveur PHP intégré.

```sh
php -S 127.0.0.1:8000 router.php
```

Le site s'ouvrira à l'adresse `http://127.0.0.1:8000/`, le panneau d'administration se trouve à `http://127.0.0.1:8000/admin/`.

## Installation et première connexion

Sur votre serveur local, ouvrez `/admin/` et créez un compte. Dans la deuxième étape, définissez le nom du site, la méta description, le texte du pied de page, la palette et le mode thème. Le logo peut être téléchargé immédiatement si vous disposez de PHP GD. L'assistant créera un favicon à partir de celui-ci. Cliquez sur "Enregistrer et ouvrir l'administrateur" ou continuez avec les paramètres actuels. Si vous quittez l'assistant, il continuera lors de votre prochaine connexion.

Sur l'hébergement, exécutez `php admin/setup.php` depuis le répertoire du projet et saisissez le code à usage unique dans la première étape de l'assistant. Le code est utilisé lorsque vous créez un compte. L’exécution à nouveau de la commande avant l’installation remplace le code précédent. L'installation de la mise à jour conserve le compte et les paramètres précédents. Si la version avant la mise à jour utilisait `SITE_ADMIN_PASSWORD_HASH`, le login est enregistré avec le login `admin` et le mot de passe précédent.

Pour que l'application fonctionne, vous avez besoin de sessions PHP et de droits d'écriture sur `admin/`, `admin/write-lock.php`, `admin/inbox/`, `data/` et sur les répertoires de pages modifiables. Pour créer des pages et des répertoires à la racine, accordez au processus PHP un accès en écriture à la racine du projet. Utilisez un serveur Web avec HTTPS, en traitant `.php` et `index.php` dans la liste des fichiers d'index. Désactivez les listes de répertoires. Pour HTTPS derrière un proxy inverse, transmettez l'attribut PHP `HTTPS` via la configuration du serveur de confiance.

## Pages et menus

Dans le panneau d'administration, la page principale est répertoriée sous le nom `/`, le reste des pages des dossiers est répertorié sous le nom `/contacts/`. Pour créer une sous-page, saisissez par exemple `/Services-1/Repair/`. Le panneau d'administration créera les catalogues et `index.php` lui-même. Pour un fichier séparé, entrez une adresse telle que `/Services-1/About_2.php`. `A–Z`, `a–z`, `0–9`, les tirets et les traits de soulignement sont autorisés dans les noms de fichiers et de dossiers. Les noms sont saisis sans espaces. A la racine, seule la page principale est autorisée. Les répertoires de services comprenant `data`, `admin`, `templates`, `css` et `js` sont réservés.

Dans la liste des pages, les dossiers peuvent être développés. Dans la case Menu, masquer, publier et supprimer sont enregistrés via AJAX. Le masquage, la publication et la suppression nécessitent une confirmation. Faites glisser l'icône ⠿ pour trier les pages adjacentes. La page cachée cesse de s'ouvrir et disparaît du menu avant la publication.

Dans l'éditeur de page, ouvrez le bloc « Déplacer la page » et précisez le dossier de destination. Par exemple, si vous transférez `/contacts/` vers `/company/`, la nouvelle adresse sera `/company/contacts/`. Les pages imbriquées restent aux mêmes adresses. Mettez à jour vos propres liens et codes `[include]` qui mènent à la page migrée.

Le bouton ↪ à côté du nom du dossier transfère l'intégralité du répertoire ainsi que les pages et les fichiers. Vous pouvez sélectionner une autre section ou racine de site pour le dossier. Les adresses des sous-pages changent avec l'adresse du dossier.

Le contenu peut être modifié dans un éditeur de texte et ajouté via FTP ou SFTP. Le fichier doit être en UTF-8 sans BOM. Enregistrez le bloc de service PHP jusqu'à la ligne `__halt_compiler();`. Dans le nouveau fichier d'échange, insérez ce bloc de l'exemple à la profondeur souhaitée et vérifiez le chemin relatif vers la racine `index.php`. Viennent ensuite les en-têtes, une ligne vide et un fragment HTML de la page.

```php
<?php
$pageFile = __FILE__;
require __DIR__ . '/../index.php';
__halt_compiler();
Title: Контакты
Heading: Как с нами связаться
Description: Контактная информация
Menu: Контакты
MenuVisible: 1
Order: 20

<p>Укажите здесь контактную информацию.</p>
```

`Title` est requis. Le `Heading` vide est extrait de `Title`. `Description` remplit la méta description de la page. `MenuVisible: 1` affiche le lien dans le menu, `MenuVisible: 0` le masque. Pour le dossier `index.php`, décocher la case masque également toutes les sous-pages du menu. Si `MenuVisible` est manquant, la case à cocher hérite de la règle précédente et est considérée comme activée lorsque `Menu` est renseignée. Lorsque la case est cochée, le champ vide `Menu` est remplacé par l'en-tête `Title`. `Order` spécifie la séquence dans un niveau et la valeur par défaut est 100.

L'onglet CSS et JS stocke les CSS et JavaScript courants pour les pages publiques. L'éditeur de pages dispose d'un bloc séparé « Pages CSS et JavaScript ». Le code de la page est chargé après le code général. Spécifiez uniquement le contenu des fichiers, sans les balises `<style>` et `<script>`. La taille autorisée de chaque champ peut atteindre 64 Ko. Pour créer un lien vers des images à partir de CSS, vous pouvez utiliser les chemins de la racine du site. L'onglet Aide contient des exemples de modèles et de composants disponibles.

Dans la section « Modèles de conception », vous pouvez visualiser les sources de `header.php`, `footer.php`, `menu.php` et `mobile-menu.php` et définir un code HTML commun pour chacun. Les « en-têtes de service » insèrent des balises méta, des codes de compteur et des scripts courants dans `<head>` ou avant `</body>`. Sur chaque page complète publiée, `rel="canonical"` est formé à partir de son adresse habituelle sans paramètres de requête.

## Paramètres, sous-modèles et domaines

Dans la section « Paramètres », vous définissez le nom du site, la méta description par défaut, le texte du pied de page et les variables. Dans le texte du pied de page, `[year]` affiche automatiquement l'année en cours. Les variables sont écrites une par ligne, par exemple `[city] = Москва` et `[spb] = Санкт-Петербург`. Pour afficher une valeur dans le titre du site, le contenu de la page, l'en-tête ou le pied de page, insérez `[city]`. Dans les modèles PHP, vous pouvez utiliser `site_escape($settings['variables']['city'] ?? '')`.

Les substitutions du système incluent `[year]`, `[month]`, `[day]`, `[time]` (heures et minutes), `[dmY]` et `[date]` (date JJ.MM.AAAA), `[datetime]` (date et heure). Le fuseau horaire PHP est utilisé.

Dans « Basique → Logos et icônes du site », vous pouvez télécharger des fichiers PNG, JPEG, GIF ou WebP. Les téléchargements sont gérés par PHP GD. Le bouton Créer un favicon crée un PNG 64 × 64 transparent à partir du logo sélectionné ou enregistré. Les images sont stockées dans `templates/images/branding/` et `templates/icons/branding/`.

« Sous-modèles » stocke le nom, la description et la signification de chaque code. Pour le téléphone, créez `[tel]`. Pour définir un titre d'onglet via un sous-modèle, créez `[title]` et spécifiez-le dans le champ `Title` de la page. Après le titre de l'onglet, le nom du site est affiché. Les substitutions fonctionnent dans le texte de la page et les métadonnées du texte. Pour le lien, insérez la balise entière `<a>` comme valeur de sous-modèle et placez le code dans le texte entre les balises. Dans les métadonnées, le HTML est converti de valeur en texte.

Ajoutez des sous-domaines dans la section « Domaines ». Dans le panneau d'administration du sous-domaine du même nom, sélectionnez « Domaine actuel » ou ouvrez le domaine souhaité dans la liste générale. Le sens local prime sur le sens général. La page publique sélectionne les valeurs par domaine dans son adresse, et les liens internes l'enregistrent. Pour connecter un sous-domaine, configurez le DNS et le serveur web sur votre hébergement afin que le domaine dessert le même répertoire de projet.

Le bouton « Modèles » à côté du domaine ouvre l'éditeur `[header]`, `[mobile-menu]`, `[menu]` et `[footer]`. Pour chaque bloc, vous pouvez définir votre propre code HTML ou utiliser la version générale. Les modèles généraux sont configurés dans la même section.

## Composants en HTML

| Code | Résultat |
| --- | --- |
| `[gal]/images/myfotos/[/gal]` | Carrousel de photos avec défilement fluide, flèches et balayage |
| `[fotos]/images/myfotos/[/fotos]` | Galerie de photos avec liens vers les originaux |
| `[files]/files/myfiles[/files]` | Liste des fichiers avec icônes par extension |
| `[include]/contacts/[/include]` | HTML d'une autre page avec ses sous-modèles et composants |
| `[form1]` | Formulaire configuré dans la section Formulaires |
| `[callback=Вызвать мастера]` | Bouton et formulaire de demande de rappel |
| `[iblock=table]/data/myiblock.txt[iblock]` | Tableau des blocs d'informations |
| `[iblock=phones]/data/myiblock.txt[iblock]` | Liste des téléphones du bloc d'information |
| `[phones]`, `[contacts]` | Téléphones ou tout le bloc de contacts configuré |
| `[soc-icons]` | Icônes de tous les réseaux sociaux avec liens personnalisés |
| `[tg]`, `[vk]`, `[whatsapp]`, `[youtube]`, `[instagram]`, `[max]` | Lien de réseau social avec icône et signature |

Placez les photos dans `templates/images/myfotos/` ou `images/myfotos/`. La liste des fichiers indique `zip`, `pdf`, `xls`, `xlsx`, `doc`, `docx`, `png`, `gif`, `jpg`, `jpeg`, `mp4`, `mov` et `mp3`. Pour `[include]`, jusqu'à dix niveaux d'imbrication sont pris en charge. La répétition de page dans le fil de discussion actuel est ignorée. Placez les codes des composants dans le corps de la page, sur une ligne distincte entre les blocs HTML.

Dans « Paramètres → Contacts et réseaux sociaux », définissez les liens, les numéros, les signatures et le titre du bloc. Dans « Paramètres → Thème et couleurs », les modes système, sombre et clair et les palettes individuelles sont disponibles. Dans « Paramètres → Composants », vous définissez les paramètres généraux du carrousel et de la galerie de photos. Sous les paramètres, vous trouverez des exemples d'insertion avec des valeurs sélectionnées. Les paramètres du code de la page remplacent les valeurs communes.

Dans Thème et couleurs, vous pouvez enregistrer une copie des paramètres sélectionnés sous votre propre nom ou laisser le nom automatique. « Appliquer » modifie la conception actuelle. Utiliser le thème par défaut applique le thème enregistré et le mémorise pour la commande Réinitialiser le thème par défaut. La suppression de la liste préserve la conception actuelle du site.

```text
[gal arrows=off numbers=off effect=fade fit=cover]/images/myfotos/[/gal]
[fotos columns=4 ratio=1x1 fit=cover captions=on links=off]/images/myfotos/[/fotos]
[files icons=on size=on order=desc download=on]/files/myfiles/[/files]
```

Pour `[fotos]`, le nombre de colonnes peut être défini manuellement pour un écran large ou laissé automatiquement sélectionné. Sur les écrans étroits, la grille réduit le nombre de colonnes. Les formats d'image disponibles sont 4:3, 1:1, 16:9 et original, les modes `cover` et `contain`, les signatures des noms de fichiers et les liens vers les originaux.

## Formulaires et antispam

Dans la section « Formulaires », vous pouvez modifier `[form1]`, créer `[form2]` et les formulaires suivants. Pour le formulaire, l'adresse du destinataire, la signature du bouton et les champs sont précisés. Le champ peut avoir une longueur maximale. Une valeur vide utilise la limite par défaut pour le type de champ. Lorsque vous saisissez `89031234567`, le champ du téléphone le formate en `+7(903)123-45-67`. Le serveur vérifie la longueur, le type, les choix valides et limite la taille totale de la requête. Les champs de saisie gratuits acceptent le texte brut. Le HTML des messages est échappé lors de la sortie et les balises HTML des nouveaux messages sont rejetées.

La case à cocher « Utiliser CAPTCHA » active Google reCAPTCHA v2 avec la vérification « Je ne suis pas un robot » pour le formulaire sélectionné. Entrez la clé du site et la clé secrète du domaine. Le secret est stocké dans un fichier de paramètres protégé et est utilisé uniquement pour la vérification du serveur. [Documentation de vérification du serveur](https://developers.google.com/recaptcha/docs/verify).

Les formulaires sont soumis via AJAX. Les candidatures sont enregistrées dans la section « Messages ». Si un destinataire est spécifié, `mail()` est utilisé pour les notifications, ce qui nécessite un courrier configuré sur l'hébergement. Dans la section Antispam, vous pouvez éditer des listes de mots, d'expressions, d'adresses IPv4 et IPv6. Les liens sont bloqués sur le serveur. L'adresse de l'expéditeur est extraite de `REMOTE_ADDR`. Derrière le proxy inverse, configurez la récupération de l'IP d'origine dans le serveur Web pour les proxys de confiance.

Chaque formulaire comporte un « Modèle de courrier » avec les champs « À », « Cc (Cc) », « Objet de l'e-mail » et « Corps de l'e-mail ». Les adresses sont indiquées séparées par des virgules. Des copies sont envoyées si au moins un destinataire principal est spécifié. Les clés de champ sont insérées dans le sujet et le texte entre accolades, par exemple `Привет {name}!`. Une valeur de champ vide est remplacée par une chaîne vide, la clé inconnue reste dans la lettre. Si le sujet ou le texte est vide, la partie correspondante de la lettre standard est utilisée.

## Demandes de rappel

Insérez `[callback]` ou `[callback=Вызвать мастера]` dans la page HTML. Le bouton ouvrira un formulaire avec un numéro de téléphone. Les demandes sont enregistrées dans la section « Rappels ». Les signatures des boutons et le mode de transfert de la demande vers la téléphonie y sont également définis. Par défaut, le fournisseur « Aucun » est sélectionné et les demandes sont uniquement enregistrées. La téléphonie peut être activée pour l'envoi manuel à partir de la liste ou pour l'envoi immédiatement après réception du numéro.

- **Zadarma.** Définissez le numéro, le SIP ou le poste de l'opérateur dans le panneau d'administration et transférez les clés vers PHP via `SITE_ZADARMA_KEY` et `SITE_ZADARMA_SECRET`. [Documentation API Zadarma](https://zadarma.com/en/support/api/)
- **API HTTP.** Spécifiez `SITE_CALLBACK_WEBHOOK_URL` avec l'adresse HTTPS et éventuellement `SITE_CALLBACK_WEBHOOK_TOKEN`. Le site envoie un POST avec les champs JSON `id`, `phone`, `host`, `page`, `requested_at` et en-tête `Idempotency-Key`. Le jeton est transféré en tant que porteur. Le code réponse 2xx transfère la demande à l'état « Acceptée par téléphonie ».

Donnez au processus PHP l'autorisation d'écriture à `admin/callbacks/`. Les applications utilisent les paramètres antispam du formulaire. La répétition au même numéro est limitée à une minute ; jusqu'à trois demandes toutes les 15 minutes sont acceptées via IP.

## Blocs d'informations et dossier de données fermé

Dans la section « Infoblocks », vous pouvez créer des ensembles de données, modifier et faire glisser des lignes. `A–Z`, `a–z`, `0–9`, trait d'union et trait de soulignement sont autorisés dans le nom du bloc d'informations et les clés de colonne. Les noms et les clés sont saisis sans espaces. Les noms de colonnes et les valeurs de cellules peuvent être écrits en russe. Les champs correspondent aux types de colonnes, et la sauvegarde se fait via AJAX. Le bouton ☰ « Modifier en tant que texte » fait passer l'intégralité du bloc d'informations au format CSV avec un délimiteur `|` et un bouton de copie. Les trois premières lignes contiennent les clés, les noms et les types des colonnes. Ensuite, il y a les lignes de données. La date au format CSV est `ГГГГ-ММ-ДД`, oui/non est stocké sous `1` ou `0`.

```text
name|phone|is_open
Название|Телефон|Открыто
text|tel|bool
Справочная|+7(903)123-45-67|1
```

Le modèle `phones` utilise des colonnes de type `tel` et une colonne de texte avec un titre. Vous pouvez placer votre modèle dans `templates/local/iblock/имя.php`, où `$block['columns']` et `$block['rows']` sont disponibles.

Le chemin `/data/myiblock.txt` à l'intérieur du code correspond au fichier `data/myiblock.txt.php`. Il s'agit d'un texte UTF-8 avec un stub PHP qui renvoie 404 lorsqu'il est appelé directement. `router.php` ferme l'intégralité du dossier lors de l'exécution locale. `data/` a des règles pour Apache et IIS. Pour Apache, activez `data/.htaccess`. Sur Nginx, configurez le dossier à fermer dans la configuration du site en tenant compte du préfixe lors de l'installation dans un sous-répertoire.

```nginx
location = /data { return 404; }
location ^~ /data/ { return 404; }
```

Après l'installation, vérifiez qu'une requête directe vers `/data/` et que le fichier infoblock reçoit un 403 ou 404.

## Chargement de fragments HTML

L'adresse habituelle `/contacts/` ouvre la page pleine. La requête `?ajax=1` renvoie `<main>` avec H1 et le contenu traité s'il existe une clé de session dans l'en-tête `X-Site-Fragment`. Les fragments sont liés à l'origine actuelle. Les pages et fragments avec clés sont servis sans mise en cache.

En JavaScript sur une page de site, utilisez la fonction intégrée.

```js
const html = await window.siteFetchContent('/contacts/');
document.getElementById('content').outerHTML = html;
```

Lors de l'installation dans un sous-répertoire, transmettez l'URL avec le préfixe du site. Le script et les styles sont extraits de la page qui insère le fragment. Les pages publiques restent accessibles aux adresses habituelles.

## Modèles et ressources

Dans « Paramètres → Bibliothèques et polices », jQuery, Bootstrap, Font Awesome, Material UI, Alpine.js, htmx, Chart.js et GLightbox sont activés séparément. Font Awesome est stocké localement. Les bibliothèques restantes sont téléchargées par le navigateur à partir du CDN lorsqu'elles sont activées. L'interface utilisateur matérielle est disponible dans les scripts `type="module"` à `import { Button } from '@mui/material'`. Pour les icônes, `[fa-icon=house]` ou le balisage habituel `<i class="fa-solid fa-house"></i>` convient.

10 polices Google avec alphabet cyrillique sont disponibles. La sélection remplace la police système du site. La valeur "Système" renvoie la conception précédente. Dans « Paramètres → Thème et couleurs → Thèmes enregistrés », vous trouverez cinq palettes prêtes à l'emploi avec des options claires et sombres.

| Chemin | But |
| --- | --- |
| `index.php` | Page d'accueil et point d'entrée général |
| `contacts/index.php` | Exemple de page dans un répertoire séparé |
| `admin/` | Connexion et éditeur de page |
| `app/site.php` | Lecture de pages, création de menus et de titres de sites Web dans `SITE_NAME` |
| `templates/layout.php` | Disposition générale, titre de l'onglet, métadonnées et zone de contenu |
| `templates/header.php`, `menu.php`, `footer.php` | En-tête, menu et pied de page |
| `templates/css/`, `js/`, `icons/`, `images/` | Styles, scripts, icônes et images |
| `templates/components/`, `templates/iblock/` | Modèles de balisage de composants et de blocs d'informations |
| `data/` | Fichiers de bloc d'informations protégés contre l'accès HTTP direct |

Le moteur assemble une page complète de `[header]`, `[mobile-menu]`, `[menu]` et `[footer]`. Il s'agit de la barre supérieure, du menu mobile, du menu de droite et du pied de page. Ils peuvent être redéfinis par des sous-modèles du même nom dans le panneau d'administration, comprenant des options distinctes pour les sous-domaines. Stockez les remplacements de modèles PHP dans `templates/local/` avec le même chemin relatif, par exemple `templates/local/footer.php`.

## Mise à jour du moteur

L'archive complète est nécessaire pour une nouvelle installation. Pour un site en direct, utilisez `php-site-update.zip`. Il contient uniquement le moteur et les modèles standards. Les pages enregistrées, les blocs d'informations, les paramètres, les mots de passe, les messages et les extensions utilisateur ne sont pas inclus dans l'archive.

Cliquez sur « Moteur de mise à jour » dans « Paramètres → Mises à jour » ou exécutez la même vérification depuis le planificateur d'hébergement.

```sh
php update.php --github
```

La source par défaut est `bandaletov72/php-site-updates`. Pour votre propre référentiel, définissez-le sur `SITE_GITHUB_REPO`. Un référentiel public ne nécessite pas de jeton. Pour un dépôt privé, transmettez la variable `SITE_GITHUB_TOKEN` au processus PHP avec un accès en lecture au contenu. Le serveur nécessite les extensions PHP Zip et OpenSSL et la possibilité d'effectuer des requêtes HTTPS vers GitHub.

Pour une installation manuelle, utilisez ZIP.

```sh
php update.php /path/to/php-site-update.zip
```

Sur l'ancienne version sans `update.php`, décompressez d'abord le package au-dessus du projet, en conservant le `.htaccess` existant et les fichiers de `maintenance/`, le cas échéant. Si vous avez modifié des modèles standard, transférez les modifications vers `templates/local/` avant la mise à jour. Le programme d'installation vérifie la liste et les sommes de contrôle des fichiers, et pour GitHub, il vérifie également la signature avec la clé publique de confiance `maintenance/release-public.pem`.

Pour la prochaine version, remplacez la version par `app/version.php` et créez le package via `php update.php --build php-site-update.zip`. Signez le ZIP avec votre clé privée RSA avec SHA‑256 et stockez la clé privée séparément du site. Ensuite, poussez le ZIP et `.sig` vers GitHub Release avec la balise `vX.Y.Z` ou vers `updates/vX.Y.Z/` et mettez à jour `updates/latest.json` vers `{"version":"X.Y.Z"}`.

## Rallonges

Des modules de moteur peuvent être ajoutés à `app/extensions/` avec des mises à jour. Stockez vos propres modules dans `extensions/`, en dehors du package de mise à jour. Le fichier PHP renvoie un tableau de définitions. Un composant unique a `kind` égal à `single` et un composant avec un argument est égal à `wrapped`. Le moteur reconnaît les nouveaux codes sur les pages existantes. Un composant peut déclarer un menu d'options dans `settings`, en définissant chaque option sur `label`, `default` et le tableau `options`. Les valeurs sont reçues dans `$context['settings']`.

```php
<?php
if (!defined('SITE_ROOT')) {
    http_response_code(404);
    exit;
}

return ['notice' => [
    'kind' => 'wrapped',
    'render' => static function (string $text, array $context): string {
        return '<strong>' . site_escape($text) . '</strong>';
    },
]];
```

L'exemple de composant est inséré sous la forme `[notice]Важное сообщение[/notice]`.

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

## Boutons de l’en-tête d’administration · 1.6.4

Le globe ouvre le site et la flèche ferme la session. Sur mobile, seules les icônes sont visibles. Survolez les boutons ou sélectionnez-les au clavier pour afficher leur libellé.

Les rubriques Contenu, Formulaires et demandes, et Site et services s’ouvrent directement. Les onglets de la rubrique choisie apparaissent sous le menu principal.

Insérez [bread-crumbs] ou [search] dans le HTML de la page d’accueil (index.php) ou du modèle. Chaque bloc apparaît une seule fois à l’emplacement choisi. Les réglages du fil d’Ariane et de la recherche s’appliquent. [privacy] renvoie vers /privacy/. Modifiez la politique dans la rubrique Pages.

```text
[bread-crumbs]
[search]
[privacy]
```
