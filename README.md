# MagixSearch - Moteur de recherche pour Magix CMS 4

[![Release](https://img.shields.io/github/release/magix-cms/Magixsearch.svg)](https://github.com/magix-cms/Magixsearch/releases/latest)
[![License](https://img.shields.io/github/license/magix-cms/Magixsearch.svg)](LICENSE)
[![PHP Version](https://img.shields.io/badge/php-%3E%3D%208.2-blue.svg)](https://php.net/)
[![Magix CMS](https://img.shields.io/badge/Magix%20CMS-4.x-success.svg)](https://www.magix-cms.com/)

MagixSearch est un plugin officiel pour Magix CMS 4 permettant d'ajouter un moteur de recherche transversal sur votre site. Il scanne automatiquement les modules natifs activés (Pages, Actualités, Catégories, Produits) et affiche des résultats pertinents et unifiés.

## 🚀 Fonctionnalités

- **Détection automatique** des modules actifs via `mc_config`.
- **Mode Standard (`LIKE`)** : Fonctionne par défaut sur n'importe quel environnement.
- **Mode Avancé (`FULLTEXT`)** : Recherche intelligente avec pertinence, via un switch dans l'administration.
- **Intégration transparente** : Utilise les `Presenters` natifs de Magix CMS pour formater les images et les URLs des résultats.
- **Hook dédié** : Injection propre du formulaire sans modifier lourdement le thème.

## 📦 Installation

1. Placez le dossier `Magixsearch` dans le répertoire `plugins/` de votre installation Magix CMS.
2. Connectez-vous à votre administration Magix CMS.
3. Rendez-vous dans le gestionnaire de plugins et installez **Magixsearch**.
4. (Optionnel) Cliquez sur l'icône de configuration du plugin pour activer le mode **FULLTEXT**.

## ⚙️ Configuration

Depuis l'administration du plugin, vous pouvez paramétrer deux éléments :

1. **L'apparence (Desktop)** : Choisissez d'afficher une barre de recherche complète ou une simple icône cliquable (pour alléger les menus d'en-tête). *Sur mobile, l'icône cliquable est toujours privilégiée pour le gain d'espace.*
2. **Le mode FULLTEXT** : Activez la recherche avancée intelligente de MySQL.

**Note sur le mode FULLTEXT :**
Si vous activez cette option, assurez-vous que le script d'installation a bien créé les index `FULLTEXT` sur vos tables. En cas d'erreur SQL lors de la recherche, vous pouvez exécuter manuellement ces requêtes dans votre base de données :

```sql
ALTER TABLE `mc_cms_page_content` ADD FULLTEXT `idx_search_pages` (`name_pages`, `content_pages`);
ALTER TABLE `mc_news_content` ADD FULLTEXT `idx_search_news` (`name_news`, `content_news`);
ALTER TABLE `mc_catalog_cat_content` ADD FULLTEXT `idx_search_cat` (`name_cat`, `content_cat`);
ALTER TABLE `mc_catalog_product_content` ADD FULLTEXT `idx_search_prod` (`name_p`, `content_p`);
```

## 🎨 Intégration sur le site (Frontend)

Pour afficher la barre de recherche dans l'en-tête de votre site, vous devez utiliser le système de Hook (Event) natif de Magix CMS.

Ouvrez le fichier de votre thème correspondant à l'en-tête, généralement situé ici :
`skin/votre_theme/layout/header.tpl`.

Placez la balise Smarty suivante à l'endroit exact où vous souhaitez voir apparaître le champ de recherche :

```smarty
{event name="displayHeaderSearch"}
```

Le plugin se chargera d'injecter automatiquement le formulaire HTML de recherche à cet emplacement. S'il n'y a aucun résultat, la page de recherche affichera d'elle-même un formulaire alternatif centré plus large.

## 🛠️ Structure requise (Thème)

La page de résultats s'appuie sur les boucles natives de votre thème. Assurez-vous que votre skin possède bien les fichiers suivants pour un affichage optimal :
- `pages/loop/pages-grid.tpl`
- `news/loop/news-grid.tpl`
- `catalog/loop/category-grid.tpl`
- `catalog/loop/product-grid.tpl`

---
*Plugin développé pour Magix CMS v4.x*