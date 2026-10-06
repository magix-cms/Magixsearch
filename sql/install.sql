-- =========================================================
-- CONFIGURATION DU PLUGIN MAGIXSEARCH
-- =========================================================

-- Création de la table de configuration
CREATE TABLE IF NOT EXISTS `mc_magixsearch_config` (
    `id_config` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `use_fulltext` TINYINT(1) DEFAULT 0,
    `desktop_layout` VARCHAR(20) NOT NULL DEFAULT 'full'
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Insertion de la configuration par défaut
-- (Fulltext désactivé par sécurité, affichage barre complète sur desktop)
INSERT INTO `mc_magixsearch_config` (`use_fulltext`, `desktop_layout`) VALUES (0, 'full');

-- =========================================================
-- CRÉATION DES INDEX FULLTEXT SUR LES TABLES CORE
-- =========================================================
-- Ajout des index nécessaires au mode de recherche intelligent de MySQL.

-- 1. Indexation du module Pages
ALTER TABLE `mc_cms_page_content` ADD FULLTEXT `idx_search_pages` (`name_pages`, `content_pages`);

-- 2. Indexation du module News (Utilisation de name_news)
ALTER TABLE `mc_news_content` ADD FULLTEXT `idx_search_news` (`name_news`, `content_news`);

-- 3. Indexation du module Catégories
ALTER TABLE `mc_catalog_cat_content` ADD FULLTEXT `idx_search_cat` (`name_cat`, `content_cat`);

-- 4. Indexation du module Produits
ALTER TABLE `mc_catalog_product_content` ADD FULLTEXT `idx_search_prod` (`name_p`, `content_p`);