<?php
declare(strict_types=1);

namespace Plugins\Magixsearch\db;

use App\Frontend\Db\BaseDb;
use Magepattern\Component\Database\QueryBuilder;
use Magepattern\Component\Database\QueryHelper;
use App\Component\Hook\HookManager;

class MagixsearchFrontDb extends BaseDb
{
    public function getConfig(): array
    {
        $qb = new QueryBuilder();
        $qb->select('*')->from('mc_magixsearch_config')->limit(1);
        $res = $this->executeRow($qb);
        return $res ?: ['use_fulltext' => 0, 'desktop_layout' => 'full'];
    }
    /**
     * Récupère la liste des modules core activés dans le CMS
     */
    public function getActiveModules(): array
    {
        $qb = new QueryBuilder();
        $qb->select(['attr_name'])
            ->from('mc_config')
            ->where('status = 1');

        $results = $this->executeAll($qb);
        return $results ? array_column($results, 'attr_name') : [];
    }

    /**
     * @return bool
     */
    public function isFulltextEnabled(): bool
    {
        $qb = new QueryBuilder();
        $qb->select(['use_fulltext'])->from('mc_magixsearch_config')->limit(1);
        $res = $this->executeRow($qb);
        return $res && (int)$res['use_fulltext'] === 1;
    }

    /**
     * Recherche dans le module Pages
     */
    public function searchPages(string $query, int $idLang, bool $fullText): array
    {
        $qb = new QueryBuilder();

        // On récupère p.* et c.* pour que le Presenter puisse formater toutes les données
        $qb->select([
            'p.*',
            'c.*',
            'i.name_img',
            'ic.alt_img',
            'ic.title_img'
        ])
            ->from('mc_cms_page', 'p')
            ->join('mc_cms_page_content', 'c', 'p.id_pages = c.id_pages AND c.id_lang = ' . $idLang)
            ->leftJoin('mc_cms_page_img', 'i', 'p.id_pages = i.id_pages AND i.default_img = 1')
            ->leftJoin('mc_cms_page_img_content', 'ic', 'i.id_img = ic.id_img AND ic.id_lang = ' . $idLang)
            ->where('c.published_pages = 1');

        if ($fullText) {
            $qb->where('MATCH(c.name_pages, c.content_pages) AGAINST(:q IN BOOLEAN MODE)', ['q' => $query . '*']);
        } else {
            $qb->where('(c.name_pages LIKE :q OR c.content_pages LIKE :q)', ['q' => '%' . $query . '%']);
        }

        // 🚀 OVERRIDE : On permet aux autres plugins de modifier la requête
        $overrides = HookManager::triggerFilter('extendPagesList', []);
        if (!empty($overrides)) {
            foreach ($overrides as $pluginOverride) {
                if (isset($pluginOverride['extendQueryParams'])) {
                    QueryHelper::applyExtendParams($qb, $pluginOverride['extendQueryParams']);
                }
            }
        }

        $qb->limit(10);
        return $this->executeAll($qb) ?: [];
    }

    /**
     * Recherche dans le module News en incluant l'image par défaut
     */
    public function searchNews(string $query, int $idLang, bool $fullText): array
    {
        $qb = new QueryBuilder();
        $qb->select([
            'n.*',
            'c.*',
            'i.name_img',
            'ic.alt_img',
            'ic.title_img'
        ])
            ->from('mc_news', 'n')
            ->join('mc_news_content', 'c', 'n.id_news = c.id_news AND c.id_lang = ' . $idLang)
            ->leftJoin('mc_news_img', 'i', 'n.id_news = i.id_news AND i.default_img = 1')
            ->leftJoin('mc_news_img_content', 'ic', 'i.id_img = ic.id_img AND ic.id_lang = ' . $idLang)
            ->where('c.published_news = 1');

        if ($fullText) {
            // CORRECTION: name_news au lieu de title_news
            $qb->where('MATCH(c.name_news, c.content_news) AGAINST(:q IN BOOLEAN MODE)', ['q' => $query . '*']);
        } else {
            $qb->where('(c.name_news LIKE :q OR c.content_news LIKE :q)', ['q' => '%' . $query . '%']);
        }

        // 🚀 OVERRIDE News (Si vous avez un triggerFilter 'extendNewsList')
        $overrides = HookManager::triggerFilter('extendNewsList', []);
        if (!empty($overrides)) {
            foreach ($overrides as $pluginOverride) {
                if (isset($pluginOverride['extendQueryParams'])) {
                    QueryHelper::applyExtendParams($qb, $pluginOverride['extendQueryParams']);
                }
            }
        }

        $qb->limit(10);
        return $this->executeAll($qb) ?: [];
    }

    /**
     * Recherche dans le module Category
     */
    public function searchCategory(string $query, int $idLang, bool $fullText): array
    {
        $qb = new QueryBuilder();
        $qb->select(['c.*', 'cc.*', 'i.name_img', 'ic.alt_img', 'ic.title_img'])
            ->from('mc_catalog_cat', 'c')
            ->join('mc_catalog_cat_content', 'cc', 'c.id_cat = cc.id_cat AND cc.id_lang = ' . $idLang)
            ->leftJoin('mc_catalog_cat_img', 'i', 'c.id_cat = i.id_cat AND i.default_img = 1')
            ->leftJoin('mc_catalog_cat_img_content', 'ic', 'i.id_img = ic.id_img AND ic.id_lang = ' . $idLang)
            ->where('cc.published_cat = 1');

        if ($fullText) {
            $qb->where('MATCH(cc.name_cat, cc.content_cat) AGAINST(:q IN BOOLEAN MODE)', ['q' => $query . '*']);
        } else {
            $qb->where('(cc.name_cat LIKE :q OR cc.content_cat LIKE :q)', ['q' => '%' . $query . '%']);
        }

        //  OVERRIDE Category
        $overrides = HookManager::triggerFilter('extendCategoryList', []);
        if (!empty($overrides)) {
            foreach ($overrides as $pluginOverride) {
                if (isset($pluginOverride['extendQueryParams'])) {
                    QueryHelper::applyExtendParams($qb, $pluginOverride['extendQueryParams']);
                }
            }
        }

        $qb->limit(10);
        return $this->executeAll($qb) ?: [];
    }

    /**
     * Recherche dans le module Product
     */
    public function searchProduct(string $query, int $idLang, bool $fullText): array
    {
        $qb = new QueryBuilder();

        // Structure exacte attendue par ProductPresenter
        $qb->select([
            'p.*', 'pc.*',
            'def_cat.id_cat AS default_id_cat', 'def_cat_c.url_cat AS default_url_cat', 'def_cat_c.name_cat',
            'i.name_img', 'ic.alt_img', 'ic.title_img'
        ])
            ->from('mc_catalog_product', 'p')
            ->join('mc_catalog_product_content', 'pc', 'p.id_product = pc.id_product AND pc.id_lang = ' . $idLang)
            ->leftJoin('mc_catalog', 'def_link', 'p.id_product = def_link.id_product AND def_link.default_c = 1')
            ->leftJoin('mc_catalog_cat', 'def_cat', 'def_link.id_cat = def_cat.id_cat')
            ->leftJoin('mc_catalog_cat_content', 'def_cat_c', 'def_cat.id_cat = def_cat_c.id_cat AND def_cat_c.id_lang = ' . $idLang)
            ->leftJoin('mc_catalog_product_img', 'i', 'p.id_product = i.id_product AND i.default_img = 1')
            ->leftJoin('mc_catalog_product_img_content', 'ic', 'i.id_img = ic.id_img AND ic.id_lang = ' . $idLang)
            ->where('pc.published_p = 1');

        if ($fullText) {
            // CORRECTION: On utilise FULLTEXT pour le nom/contenu, et LIKE pour la référence (car sur une autre table)
            $qb->where('(MATCH(pc.name_p, pc.content_p) AGAINST(:q IN BOOLEAN MODE) OR p.reference_p LIKE :ref)', [
                'q'   => $query . '*',
                'ref' => '%' . $query . '%'
            ]);
        } else {
            $qb->where('(pc.name_p LIKE :q OR pc.content_p LIKE :q OR p.reference_p LIKE :ref)', [
                'q'   => '%' . $query . '%',
                'ref' => '%' . $query . '%'
            ]);
        }

        //  OVERRIDE Product
        $overrides = HookManager::triggerFilter('extendProductList', []);
        if (!empty($overrides)) {
            foreach ($overrides as $pluginOverride) {
                if (isset($pluginOverride['extendQueryParams'])) {
                    QueryHelper::applyExtendParams($qb, $pluginOverride['extendQueryParams']);
                }
            }
        }

        $qb->limit(10);
        return $this->executeAll($qb) ?: [];
    }
}
?>