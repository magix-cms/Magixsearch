<?php
/*
# Copyright (C) 2008 - 2026 Gerits Aurelien (Magix CMS).
# License GPLv3
*/

declare(strict_types=1);

namespace Plugins\Magixsearch\db;

use App\Backend\Db\BaseDb;
use Magepattern\Component\Database\QueryBuilder;

class MagixsearchDb extends BaseDb
{
    /**
     * Récupère la configuration actuelle du moteur de recherche
     */
    public function getConfig(): array
    {
        $qb = new QueryBuilder();
        $qb->select('*')
            ->from('mc_magixsearch_config')
            ->limit(1);

        $result = $this->executeRow($qb);

        // Valeur par défaut si la table est vide
        return $result ?: ['use_fulltext' => 0];
    }

    /**
     * Met à jour la configuration (Activation/Désactivation du Full-Text)
     */
    public function saveConfig(int $useFulltext, string $desktopLayout): bool
    {
        $config = $this->getConfig();
        $qb = new QueryBuilder();
        $data = [
            'use_fulltext'   => $useFulltext,
            'desktop_layout' => $desktopLayout
        ];

        if (isset($config['id_config'])) {
            $qb->update('mc_magixsearch_config', $data);
            return $this->executeUpdate($qb) !== false;
        } else {
            $qb->insert('mc_magixsearch_config', $data);
            return $this->executeInsert($qb);
        }
    }
}