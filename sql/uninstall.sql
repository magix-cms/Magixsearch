-- 1. Suppression de la table de configuration du plugin
DROP TABLE IF EXISTS `mc_magixsearch_config`;

-- 2. Suppression des index FULLTEXT sur les tables Core
-- On utilise ALTER TABLE ... DROP INDEX ...
-- (Si l'index n'existe pas car le mode n'a jamais été activé, MySQL l'ignorera ou génèrera un warning non bloquant selon la version)

ALTER TABLE `mc_cms_page_content` DROP INDEX `idx_search_pages`;
ALTER TABLE `mc_news_content` DROP INDEX `idx_search_news`;
ALTER TABLE `mc_catalog_cat_content` DROP INDEX `idx_search_cat`;
ALTER TABLE `mc_catalog_product_content` DROP INDEX `idx_search_prod`;