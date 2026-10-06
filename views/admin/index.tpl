{extends file="layout.tpl"}

{block name='head:title'}MagixSearch - Configuration{/block}

{block name='article'}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="bi bi-search me-2"></i> Configuration du moteur de recherche
        </h1>
    </div>

    <div class="row">
        <div class="col-12 col-xl-8">
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white py-3 border-bottom">
                    <h6 class="m-0 fw-bold text-primary">Paramètres généraux</h6>
                </div>
                <div class="card-body p-4">
                    <form action="index.php?controller=Magixsearch&action=save" method="post" class="validate_form">
                        <input type="hidden" name="hashtoken" value="{$hashtoken|default:''}">

                        <div class="alert alert-info mb-4 d-flex align-items-center">
                            <i class="bi bi-info-circle-fill fs-4 me-3"></i>
                            <div>
                                <strong>Information :</strong> Le moteur de recherche analyse automatiquement les modules activés sur votre site (Pages, Actualités, Catalogue, etc.).
                                Aucune configuration supplémentaire n'est requise pour l'indexation standard.
                            </div>
                        </div>

                        <div class="bg-light p-4 rounded border">
                            <h5 class="fw-bold mb-3"><i class="bi bi-cpu me-2"></i> Mode de recherche avancé (FULLTEXT)</h5>

                            <p class="text-muted small mb-4">
                                L'activation du mode <strong>FULLTEXT</strong> offre des résultats plus pertinents (recherche par pertinence, mots partiels avec astérisque, mode booléen). <br>
                                <span class="text-danger fw-bold"><i class="bi bi-exclamation-triangle"></i> Attention :</span> Pour que ce mode fonctionne, vos tables MySQL doivent impérativement posséder des index de type <code>FULLTEXT</code> sur les colonnes recherchées. Dans le cas contraire, la recherche SQL échouera.
                            </p>

                            <div class="form-check form-switch fs-5 mb-0">
                                <input class="form-check-input ms-0 me-3" type="checkbox" role="switch" id="use_fulltext" name="use_fulltext" value="1" {if isset($config.use_fulltext) && $config.use_fulltext == 1}checked{/if}>
                                <label class="form-check-label fw-medium mt-1" for="use_fulltext">
                                    Activer la recherche intelligente (Mode MySQL FULLTEXT)
                                </label>
                            </div>
                        </div>

                        <hr class="my-4">

                        <h5 class="fw-bold mb-3"><i class="bi bi-brush me-2"></i> Apparence sur Ordinateur (Desktop)</h5>
                        <p class="text-muted small mb-4">Choisissez comment la barre de recherche s'affiche sur les grands écrans (sur mobile, l'icône cliquable est toujours utilisée pour gagner de l'espace).</p>

                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label class="border rounded p-3 d-block position-relative bg-light cursor-pointer h-100">
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="radio" name="desktop_layout" value="full" id="layout_full" {if !isset($config.desktop_layout) || $config.desktop_layout == 'full'}checked{/if}>
                                        <label class="form-check-label fw-bold" for="layout_full">Barre de recherche complète</label>
                                    </div>
                                    <div class="input-group input-group-sm mt-3 opacity-50">
                                        <input type="text" class="form-control" placeholder="Rechercher..." disabled>
                                        <span class="input-group-text"><i class="bi bi-search"></i></span>
                                    </div>
                                </label>
                            </div>
                            <div class="col-md-6">
                                <label class="border rounded p-3 d-block position-relative bg-light cursor-pointer h-100">
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="radio" name="desktop_layout" value="icon" id="layout_icon" {if isset($config.desktop_layout) && $config.desktop_layout == 'icon'}checked{/if}>
                                        <label class="form-check-label fw-bold" for="layout_icon">Icône cliquable (Gain de place)</label>
                                    </div>
                                    <div class="mt-3 opacity-50 text-end pe-2">
                                        <i class="bi bi-search fs-5"></i>
                                    </div>
                                </label>
                            </div>
                        </div>

                        <div class="text-end">
                            <button type="submit" class="btn btn-primary px-4">
                                <i class="bi bi-save me-2"></i> Sauvegarder la configuration
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-12 col-xl-4">
            <div class="card shadow-sm border-0 mb-4 bg-light">
                <div class="card-body p-4 text-center">
                    <i class="bi bi-code-slash display-4 text-secondary mb-3"></i>
                    <h5 class="fw-bold">Intégration Frontend</h5>
                    <p class="text-muted small">
                        Le formulaire de recherche est automatiquement injecté dans l'en-tête de votre site via le hook :
                    </p>
                    <code class="d-block bg-white border p-2 rounded text-primary mb-3">
                        &#123;event name="displayHeaderSearch"&#125;
                    </code>
                    <p class="text-muted small mb-0">
                        Assurez-vous que cette balise est présente dans votre fichier de thème <code>header.tpl</code>.
                    </p>
                </div>
            </div>
        </div>
    </div>
{/block}

{block name="javascripts" append}
    <script src="templates/js/MagixFormTools.min.js?v={$smarty.now}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            new MagixFormTools();
        });
    </script>
{/block}