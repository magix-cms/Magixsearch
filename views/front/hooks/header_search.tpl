<div class="magix-search-wrapper ms-md-3">

    {* ==========================================
       1. VERSION DESKTOP (Cachée sur mobile)
       ========================================== *}
    <div class="d-none d-md-block">
        {if $search_config.desktop_layout|default:'full' == 'full'}
            {* --- Modèle A : Barre complète --- *}
            <form class="d-flex" action="{$base_url}{$current_lang.iso_lang}/magixsearch/results" method="get" role="search">
                <div class="input-group">
                    <input class="form-control border border-end-0 shadow-none" type="search" name="q" placeholder="Rechercher..." aria-label="Rechercher" required minlength="3">
                    <button class="btn border border-start-0 bg-white text-primary shadow-none" type="submit">
                        <i class="bi bi-search"></i>
                    </button>
                </div>
            </form>
        {else}
            {* --- Modèle B : Icône cliquable (Dropdown) --- *}
            <div class="dropdown">
                <button class="btn btn-link text-dark text-decoration-none p-2 shadow-none" type="button" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false" aria-label="Ouvrir la recherche">
                    <i class="bi bi-search fs-5"></i>
                </button>
                <div class="dropdown-menu dropdown-menu-end p-3 shadow border-0" style="width: 280px;">
                    <form action="{$base_url}{$current_lang.iso_lang}/magixsearch/results" method="get" role="search">
                        <div class="input-group">
                            <input class="form-control border border-end-0 shadow-none" type="search" name="q" placeholder="Rechercher..." aria-label="Rechercher" required minlength="3" autofocus>
                            <button class="btn border border-start-0 bg-white text-primary shadow-none" type="submit">
                                <i class="bi bi-search"></i>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        {/if}
    </div>

    {* ==========================================
       2. VERSION MOBILE (Cachée sur Desktop)
       Toujours sous forme d'icône cliquable
       ========================================== *}
    <div class="d-md-none dropdown">
        <button class="btn btn-link text-dark text-decoration-none p-2 shadow-none" type="button" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false" aria-label="Ouvrir la recherche mobile">
            <i class="bi bi-search fs-5"></i>
        </button>
        <div class="dropdown-menu dropdown-menu-end p-3 shadow border-0" style="width: 280px;">
            <form action="{$base_url}{$current_lang.iso_lang}/magixsearch/results" method="get" role="search">
                <div class="input-group">
                    <input class="form-control border border-end-0 shadow-none" type="search" name="q" placeholder="Rechercher..." aria-label="Rechercher mobile" required minlength="3">
                    <button class="btn border border-start-0 bg-white text-primary shadow-none" type="submit">
                        <i class="bi bi-search"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>