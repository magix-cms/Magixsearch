<?php
/*
# Copyright (C) 2008 - 2026 Gerits Aurelien (Magix CMS).
# License GPLv3
*/

declare(strict_types=1);

namespace Plugins\Magixsearch\src;

use App\Backend\Controller\BaseController;
use Plugins\Magixsearch\db\MagixsearchDb;
use Magepattern\Component\HTTP\Request;
use Magepattern\Component\Tool\SmartyTool;

class BackendController extends BaseController
{
    public function run(): void
    {
        SmartyTool::addTemplateDir('admin', ROOT_DIR . 'plugins' . DS . 'Magixsearch' . DS . 'views' . DS . 'admin');

        $action = $_GET['action'] ?? 'index';

        if ($action === 'save' && Request::isMethod('POST')) {
            $this->processSave();
            return;
        }

        if (method_exists($this, $action)) {
            $this->$action();
        } else {
            $this->index();
        }
    }

    private function index(): void
    {
        $db = new MagixsearchDb();
        $config = $db->getConfig();

        $this->view->assign([
            'config'    => $config,
            'hashtoken' => $this->session->getToken()
        ]);

        $this->view->display('index.tpl');
    }

    private function processSave(): void
    {
        if (ob_get_length()) ob_clean();

        $token = $_POST['hashtoken'] ?? '';
        if (!$this->session->validateToken($token)) {
            $this->jsonResponse(false, 'Session expirée.');
        }

        $db = new MagixsearchDb();
        $useFulltext = isset($_POST['use_fulltext']) ? 1 : 0;
        // On récupère le design (full ou icon)
        $desktopLayout = $_POST['desktop_layout'] ?? 'full';

        if ($db->saveConfig($useFulltext, $desktopLayout)) {
            $this->jsonResponse(true, 'La configuration de recherche a été mise à jour.', ['type' => 'update']);
        } else {
            $this->jsonResponse(false, 'Erreur lors de la sauvegarde.');
        }
    }
}