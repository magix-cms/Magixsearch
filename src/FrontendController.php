<?php
declare(strict_types=1);

namespace Plugins\Magixsearch\src;

use App\Frontend\Controller\BaseController;
use Plugins\Magixsearch\db\MagixsearchFrontDb;
use Magepattern\Component\Tool\FormTool;
use Magepattern\Component\Tool\SmartyTool;
use Magepattern\Component\Tool\StringTool;
use App\Frontend\Db\CompanyDb;
use App\Frontend\Model\PagesPresenter;
use App\Frontend\Model\NewsPresenter;
use App\Frontend\Model\CategoryPresenter;
use App\Frontend\Model\ProductPresenter;
use Magepattern\Component\HTTP\Url;

class FrontendController extends BaseController
{
    public static function renderWidget(): string
    {
        $view = SmartyTool::getInstance('front');

        $db = new \Plugins\Magixsearch\db\MagixsearchFrontDb();
        $view->assign('search_config', $db->getConfig());

        return $view->fetch(ROOT_DIR . 'plugins/Magixsearch/views/front/hooks/header_search.tpl');
    }

    public function run(): void
    {
        SmartyTool::addTemplateDir('front', ROOT_DIR . 'plugins' . DS . 'Magixsearch' . DS . 'views' . DS . 'front');

        $action = $_GET['action'] ?? 'results';

        if ($action === 'results') {
            $this->results();
        } else {
            $this->render404();
        }
    }

    /**
     * @return void
     * @throws \Smarty\Exception
     */
    private function results(): void
    {
        $query = trim(FormTool::simpleClean($_GET['q'] ?? ''));
        $idLang = (int)$this->currentLang['id_lang'];
        $siteUrl = rtrim((string)$this->view->getTemplateVars('site_url'), '/');
        $skinFolder = $this->siteSettings['theme']['value'] ?? 'default';

        $companyDb = new CompanyDb();
        $companyInfo = $companyDb->getCompanyInfo() ?: [];

        $results = [
            'pages'    => [],
            'news'     => [],
            'category' => [],
            'product'  => []
        ];

        $totalResults = 0;

        if (strlen($query) >= 3) {
            $db = new MagixsearchFrontDb();
            $activeModules = $db->getActiveModules();
            $isFulltext = $db->isFulltextEnabled();

            // 1. RECHERCHE DANS LES PAGES
            if (in_array('pages', $activeModules) && class_exists('\App\Frontend\Model\PagesPresenter')) {
                $rawPages = $db->searchPages($query, $idLang, $isFulltext);
                foreach ($rawPages as $row) {
                    $formatted = PagesPresenter::format($row, $this->currentLang, $siteUrl, $companyInfo, $skinFolder);
                    $formatted['search_snippet'] = StringTool::truncate(strip_tags((string)($row['content_pages'] ?? '')), 150);
                    $results['pages'][] = $formatted;
                    $totalResults++;
                }
            }

            // 2. RECHERCHE DANS LES ACTUALITÉS (AJOUT DES TAGS ICI)
            if (in_array('news', $activeModules) && class_exists('\App\Frontend\Model\NewsPresenter')) {
                $rawNews = $db->searchNews($query, $idLang, $isFulltext);

                // On instancie la base de données News pour récupérer les tags
                $newsDb = class_exists('\App\Frontend\Db\NewsDb') ? new \App\Frontend\Db\NewsDb() : null;

                foreach ($rawNews as $row) {
                    $formatted = NewsPresenter::format($row, $this->currentLang, $siteUrl, $companyInfo, $skinFolder);
                    $formatted['search_snippet'] = StringTool::truncate(strip_tags((string)($row['content_news'] ?? '')), 150);

                    // --- AJOUT DES TAGS ---
                    if ($newsDb) {
                        $tags = $newsDb->getNewsTags((int)$row['id_news'], $idLang);
                        if ($tags) {
                            foreach ($tags as &$tag) {
                                // On nettoie le nom pour l'URL comme dans NewsController
                                $tag['slug'] = Url::clean($tag['name_tag']);
                            }
                            unset($tag);
                        }
                        $formatted['tags'] = $tags; // On injecte le tableau de tags dans le résultat formaté
                    } else {
                        $formatted['tags'] = []; // Fallback de sécurité
                    }

                    $results['news'][] = $formatted;
                    $totalResults++;
                }
            }

            // 3. RECHERCHE DANS LES CATÉGORIES
            if (in_array('catalog', $activeModules) && class_exists('\App\Frontend\Model\CategoryPresenter')) {
                $rawCat = $db->searchCategory($query, $idLang, $isFulltext);
                foreach ($rawCat as $row) {
                    $formatted = CategoryPresenter::format($row, $this->currentLang, $siteUrl, $companyInfo, $skinFolder);
                    $formatted['search_snippet'] = StringTool::truncate(strip_tags((string)($row['content_cat'] ?? '')), 150);
                    $results['category'][] = $formatted;
                    $totalResults++;
                }
            }

            // 4. RECHERCHE DANS LES PRODUITS
            if (in_array('catalog', $activeModules) && class_exists('\App\Frontend\Model\ProductPresenter')) {
                $rawProd = $db->searchProduct($query, $idLang, $isFulltext);
                foreach ($rawProd as $row) {
                    $formatted = ProductPresenter::format($row, $this->currentLang, $siteUrl, $companyInfo, $skinFolder, $this->siteSettings);
                    $formatted['search_snippet'] = StringTool::truncate(strip_tags((string)($row['content_p'] ?? '')), 150);
                    $results['product'][] = $formatted;
                    $totalResults++;
                }
            }
        }

        $this->view->assign([
            'search_query'  => $query,
            'results'       => $results,
            'total_results' => $totalResults,
            'seo_title'     => 'Résultats de recherche pour "' . $query . '"'
        ]);

        $this->view->display('results.tpl');
    }
}