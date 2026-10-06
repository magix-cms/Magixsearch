{extends file="layout.tpl"}

{block name='head:title'}{$seo_title}{/block}

{block name="article:content"}
    {$breadcrumbs = [['label' => 'Recherche']]}
    {include file="components/breadcrumbs.tpl" breadcrumbs=$breadcrumbs}

    <header class="page-header mb-5 mt-3">
        <h1 class="display-5 fw-bold text-primary">Résultats de recherche</h1>
        <p class="lead text-muted">
            {if $total_results > 0}
                {$total_results} résultat(s) trouvé(s) pour la recherche "<strong>{$search_query|escape}</strong>".
            {else}
                Aucun résultat trouvé pour "<strong>{$search_query|escape}</strong>".
            {/if}
        </p>
    </header>

    <section class="page-body mb-5">
        {if $total_results > 0}

            {* AFFICHAGE PAGES *}
            {if isset($results.pages) && count($results.pages) > 0}
                <h3 class="h4 border-bottom pb-2 mb-4"><i class="bi bi-file-earmark-text me-2"></i> Pages</h3>
                {*  Appel direct à votre composant natif *}
                {include file="pages/loop/pages-grid.tpl" data=$results.pages classType="normal" animate=false animType="fade-up"}
            {/if}

            {* AFFICHAGE ACTUALITÉS *}
            {if isset($results.news) && count($results.news) > 0}
                <h3 class="h4 border-bottom pb-2 mb-4 mt-5"><i class="bi bi-newspaper me-2"></i> Actualités</h3>
                {* Assurez-vous que ce fichier existe dans votre thème *}
                {include file="news/loop/news-grid.tpl" data=$results.news classType="normal" animate=false}
            {/if}

            {* AFFICHAGE CATÉGORIES *}
            {if isset($results.category) && count($results.category) > 0}
                <h3 class="h4 border-bottom pb-2 mb-4 mt-5"><i class="bi bi-folder2-open me-2"></i> Catégories</h3>
                {* Assurez-vous que ce fichier existe dans votre thème *}
                {include file="catalog/loop/category-grid.tpl" data=$results.category classType="normal" animate=false}
            {/if}

            {* AFFICHAGE PRODUITS *}
            {if isset($results.product) && count($results.product) > 0}
                <h3 class="h4 border-bottom pb-2 mb-4 mt-5"><i class="bi bi-box-seam me-2"></i> Produits</h3>
                {* Assurez-vous que ce fichier existe dans votre thème *}
                {include file="catalog/loop/product-grid.tpl" data=$results.product classType="normal" animate=false}
            {/if}

        {else}
            <div class="alert alert-info shadow-sm border-0 rounded-4 p-4 mb-5">
                <h4 class="alert-heading fw-bold"><i class="bi bi-search me-2"></i> Aucun résultat</h4>
                <p class="mb-0">Nous n'avons trouvé aucun résultat pour "<strong>{$search_query|escape}</strong>". Essayez de modifier vos mots-clés ou de vérifier l'orthographe (3 caractères minimum).</p>
            </div>

            {* Formulaire de secours centré et plus grand *}
            <div class="card border-0 bg-light rounded-4 p-4 text-center mx-auto" style="max-width: 600px;">
                <h5 class="fw-bold mb-3">Nouvelle recherche</h5>
                <form action="{$base_url}{$current_lang.iso_lang}/magixsearch/results" method="get" role="search">
                    <div class="input-group input-group-lg">
                        <input class="form-control" type="search" name="q" placeholder="Tapez votre recherche ici..." value="{$search_query|escape}" required minlength="3">
                        <button class="btn btn-primary px-4" type="submit">
                            <i class="bi bi-search"></i>
                        </button>
                    </div>
                </form>
            </div>
        {/if}
    </section>
{/block}