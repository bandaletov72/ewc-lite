# FAQ Administrateur

## Installation et connexion

### Comment lancer un site Web et ouvrir le panneau d'administration ?

Pour lancer localement, lancez la commande php -S 127.0.0.1:8000 router.php dans le répertoire du projet et ouvrez http://127.0.0.1:8000/admin/. Sur l'hébergement, lancez d'abord php admin/setup.php, puis saisissez le code à usage unique reçu dans la première étape de l'assistant dans /admin/.

### Que pouvez-vous configurer au premier démarrage ?

Créez d’abord un nom d’utilisateur et un mot de passe. Dans la deuxième étape, définissez le nom du site, la méta description par défaut, le texte du pied de page, la palette et le mode thème. Si vous disposez de PHP GD, vous pouvez immédiatement télécharger le logo et créer un favicon à partir de celui-ci. Vous pouvez ignorer la deuxième étape ou continuer lors de votre prochaine connexion.

## Pages et menus

### Comment créer une page et quelle adresse saisir ?

Accédez à Pages → Nouvelle page. Saisissez l'adresse, par exemple /Services-1/Repair/ ou /Services-1/Price_2.php, remplissez le titre et enregistrez. Pour l'adresse du dossier, le moteur lui-même créera les répertoires et index.php. A-Z, a-z, 0-9, tiret et trait de soulignement sont autorisés dans les noms. La page principale est réservée à la racine, et la création de pages dans les annuaires de services est fermée.

### Quelle est la différence entre la case à cocher « Dans le menu » et le bouton « Masquer » ?

Décocher la case « Dans le menu » supprime le lien des menus habituels, tout en conservant l'accès à l'adresse directe. Sur une page avec une adresse de dossier, la case à cocher contrôle également les éléments des sous-pages. Le bouton « Masquer » supprime le lien et ferme l'adresse publique de la page jusqu'à ce qu'elle soit à nouveau publiée.

### Comment puis-je modifier l’ordre des pages ou déplacer un dossier entier ?

Faites glisser l'icône ⠿ pour réorganiser les pages au sein du même niveau. Pour déplacer une page, ouvrez « Déplacer la page » dans son éditeur. Pour transférer un dossier avec ses pages et fichiers joints, cliquez sur ↪ à côté du nom du dossier. Après le transfert, vérifiez les liens manuels et les codes [include] qui pointaient vers les anciennes adresses.

### Pourquoi les changements de page ne sont-ils pas immédiatement visibles ?

Cliquez sur "Enregistrer" dans l'éditeur. Utilisez ensuite le lien « Ouvrir la page » ou « Aller ». Si le lien a disparu, vérifiez si la page est publiée. Pour les modifications sur un sous-domaine, vérifiez le domaine dans la barre d'adresse.

## Conception et sous-domaines

### Où puis-je modifier le titre, le pied de page, le logo et le favicon ?

Ouvrez Paramètres → Général. Le champ Texte de pied de page prend en charge [year] pour l'année en cours. Dans le bloc « Logos et icônes de site », vous pouvez télécharger des images ou cliquer sur « Créer un favicon à partir d'un logo ». Le chargement et la conversion nécessitent PHP GD.

### Comment changer le thème ou la police puis revenir à l'ancien ?

Dans Paramètres → Thème et couleurs, sélectionnez un mode et une palette. Enregistrez la palette actuelle dans la liste afin de pouvoir l'utiliser plus tard. Le bouton "Utiliser par défaut" mémorise la palette de la commande "Remettre le thème par défaut". La police peut être modifiée dans la section "Bibliothèques et polices". Les noms et échantillons de la liste sont affichés dans les polices appropriées. La valeur "Système" renvoie la police normale.

### Où changer l'en-tête et où insérer un compteur, CSS ou JavaScript ?

L'en-tête des blocs communs, le menu mobile, le menu et le pied de page sont édités dans l'onglet « Modèles de conception ». Au même endroit, « Service Headers » insère du HTML dans l’en-tête ou avant la balise de fermeture body. Pour les CSS et JavaScript classiques, il existe un onglet « CSS et JS » distinct. Le code d'une page est ajouté dans son éditeur.

### Comment définir différents numéros de téléphone et en-têtes pour les sous-domaines ?

Ajoutez un sous-domaine dans la section "Domaines". Définissez ensuite une valeur de sous-modèle distincte dans la section Sous-modèles en sélectionnant le domaine souhaité. Modifiez l'en-tête et les autres blocs à l'aide du bouton « Modèles » à côté du domaine. La version du domaine prévaut sur la version générale. Le DNS et le serveur Web doivent pointer le sous-domaine vers le répertoire de ce site.

### Où puis-je modifier les liens vers les réseaux sociaux et les légendes à côté d'eux ?

Ouvrez « Paramètres → Contacts et réseaux sociaux ». Remplissez les adresses, les signatures et les numéros de téléphone. Le code [soc-icons] affiche les icônes de tous les réseaux configurés. Pour un lien unique, utilisez par exemple [tg] ou [vk].

## Formulaires et demandes

### Comment ajouter un formulaire sur une page ?

Ouvrez les formulaires, configurez les champs, obligatoires, la longueur maximale et le texte du bouton. Enregistrez le formulaire et collez son code, par exemple [form1], dans le HTML de la page.

### Où puis-je consulter les candidatures si je n’ai pas reçu de notification par e-mail ?

Ouvrez Messages. Les candidatures y sont enregistrées après la soumission réussie du formulaire. Pour les notifications, vérifiez les adresses dans le champ « À » du modèle de courrier de formulaire et la fonctionnalité de la fonction mail() sur l'hébergement.

### Comment rédiger une lettre et envoyer des copies ?

Ouvrez le formulaire souhaité et recherchez le bloc « Modèle de courrier ». Saisissez les adresses dans les champs « À » et « Copies (Cc) », séparées par des virgules. Dans le sujet et le corps, utilisez les clés de champ entre accolades, par exemple Bonjour {name} ! Les boutons sous le texte insèrent la variable sélectionnée. Le sujet ou le texte vide utilise la notification standard.

### Pourquoi le formulaire a-t-il rejeté le message ou demandé « Je ne suis pas un robot » ?

Vérifiez les champs obligatoires, la longueur, le format de téléphone et de courrier électronique. Les champs de saisie gratuits utilisent du texte brut sans HTML ni adresses de sites Web. Les listes de mots et d'adresses IP interdits se trouvent dans la section Antispam. La case « Utiliser CAPTCHA » nécessite une clé de site et une clé secrète Google reCAPTCHA v2 pour le domaine souhaité.

### Où puis-je voir les demandes de rappel ?

Dans la section "Rappels". Le composant est ajouté à la page avec le code [callback] ou [callback=Вызвать мастера]. Avec le fournisseur « Non », les candidatures sont enregistrées dans le panneau d'administration. Lorsque la téléphonie est configurée, la transmission manuelle ou automatique est disponible.

## Blocs d'informations et composants

### Comment créer un bloc d'information et l'afficher sur la page ?

Dans la section « Infoblocs », cliquez sur « Nouvel Infobloc », configurez les colonnes et ajoutez des lignes. Dans le nom du bloc d'informations et les clés de colonne, utilisez A à Z, a à z, 0 à 9, un trait d'union ou un trait de soulignement sans espaces. Enregistrez et copiez le code d'intégration depuis l'éditeur d'infobloc. Pour obtenir une liste de téléphones, utilisez le modèle de téléphones.

### Comment copier ou remplacer toutes les lignes d'un infobloc ?

Ouvrez le bloc d'informations et cliquez sur « Modifier en tant que texte ». CSV utilise le | délimiteur. Les trois premières lignes contiennent les clés, les noms et les types des colonnes. Viennent ensuite les données. Placez un champ avec un délimiteur, des guillemets ou un saut de ligne entre guillemets doubles et des guillemets internes doubles.

### Que vérifier si la galerie ou la liste de fichiers est vide ?

Vérifiez le chemin dans le code du composant avec le dossier sur le serveur. Par exemple, [fotos]/images/myfotos/[/fotos] utilise des photos dans images/myfotos/ ou templates/images/myfotos/.. Pour [files], vérifiez l'extension du fichier. Une liste des extensions et paramètres pris en charge peut être trouvée dans l'aide du composant.

### Pourquoi une requête avec ?ajax=1 renvoie-t-elle 403 ?

Le fragment de page nécessite une session publique valide et un jeton sur le même site. Pour charger à l'intérieur du site, utilisez window.siteFetchContent(), qui transfère le jeton. Pour une transition normale, ouvrez l'adresse de la page sans le paramètre ajax.

## Mises à jour et sauvegarde

### Comment mettre à jour le moteur d'un site installé ?

Dans Paramètres → Mises à jour, comparez la version installée avec la dernière version de GitHub, puis cliquez sur Mettre à jour le moteur. Si GitHub n'est pas disponible, le panneau d'administration affiche la version qui a été vérifiée pour la dernière fois. Pour mettre à jour manuellement, transmettez l'archive php-site-update.zip à la commande php update.php. Le package de mise à jour remplace les fichiers du moteur et les modèles standard. Stockez vos modèles PHP dans templates/local/.

### Que dois-je copier avant de procéder à une mise à niveau ou à une migration ?

Faites une copie de l'intégralité du répertoire du site ainsi que des fichiers cachés. Les pages se trouvent à la racine et leurs répertoires, les blocs d'informations dans data/, les paramètres et les fichiers de compte dans admin/.. Enregistrez également vos modèles, images téléchargées, messages et demandes. La copie inclut des secrets de configuration, conservez-la donc en dehors d'un répertoire public.

### Que vérifier si le panneau d'administration ne peut pas enregistrer les modifications ?

Lisez d’abord le message à côté du formulaire. Si les modifications entrent en conflit, copiez vos modifications et ouvrez à nouveau l'éditeur. S'il y a une erreur d'écriture, demandez à l'hébergement de vérifier les autorisations d'écriture du processus PHP dans le dossier et le fichier souhaités admin/write-lock.php.. Si la mise à jour échoue, vérifiez l'accès du serveur à GitHub et la disponibilité de PHP Zip et OpenSSL.

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

## Menu mobile · 1.6.7

- Afficher dans le menu principal
- Afficher dans le menu mobile
- Masquer dans le menu mobile

## Paramètres de la page · 1.6.8

Rédigez le contenu dans Édition. L’adresse, le menu et le calendrier de publication figurent dans les paramètres de la page. SEO contient les titres, la description, les mots-clés et l’inclusion dans le sitemap. Les coordonnées et exemples sont dans Cartes. Choisissez les traductions dans la liste au-dessus de l’éditeur.

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
