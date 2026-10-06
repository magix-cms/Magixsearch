<?php
declare(strict_types=1);

namespace Plugins\Magixsearch;

use App\Component\Hook\HookManager;

class Boot
{
    public function register(): void
    {
        // Accroche pour afficher la barre de recherche dans le header
        HookManager::register('displayHeaderSearch', 'Magixsearch', [$this, 'hookDisplayHeaderSearch']);
    }

    public function hookDisplayHeaderSearch(): string
    {
        return \Plugins\Magixsearch\src\FrontendController::renderWidget();
    }
}