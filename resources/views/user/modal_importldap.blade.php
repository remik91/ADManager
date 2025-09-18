<!-- Modal: Import LDAP -->
<div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="ldapModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <div>
                    <h1 class="modal-title fs-5" id="ldapModalLabel">Importer un usager depuis le LDAP ACA</h1>
                    <div class="text-muted small">Recherche ciblée par UID, nom ou e-mail – puis import direct dans le
                        formulaire.</div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
            </div>

            <div class="modal-body">
                <div class="card shadow-sm border-0 mb-3">
                    <div class="card-body">
                        <form id="search-form-ldap" class="row g-3 align-items-end" role="form" autocomplete="off"
                            novalidate>
                            @csrf
                            <div class="col-md-3">
                                <label class="form-label">Critère</label>
                                <select class="form-select" name="search_crit" id="ldap-crit">
                                    <option value="uid" selected>Par UID</option>
                                    <option value="sn">Par nom</option>
                                    <option value="mail">Par e-mail</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Population</label>
                                <select class="form-select" name="search_ou" id="ldap-ou">
                                    <option value="personnels EN" selected>Personnels EN</option>
                                    <option value="autres">Autre</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Requête</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-search"></i></span>
                                    <input type="search" class="form-control" id="search_uid" name="search_uid"
                                        placeholder="UID, nom, e-mail…" aria-label="Recherche LDAP" autocomplete="off">
                                    <button class="btn btn-primary d-inline-flex align-items-center" type="submit"
                                        id="ldap-search-btn">
                                        <span class="spinner-border spinner-border-sm me-2 d-none" id="ldap-spinner"
                                            role="status" aria-hidden="true"></span>
                                        Rechercher
                                    </button>
                                </div>
                                {{-- <div class="form-text">Appuie sur Entrée pour lancer la recherche.</div> --}}
                            </div>
                        </form>
                    </div>
                </div>

                <!-- States -->
                <div id="ldap-empty" class="text-center text-muted py-5">
                    <i class="fa-regular fa-folder-open fa-2xl mb-3"></i>
                    <div>Aucun résultat pour l’instant. Lance une recherche ci-dessus.</div>
                </div>

                <div id="ldap-loading" class="placeholder-glow d-none">
                    <div class="card mb-2">
                        <div class="card-body"><span class="placeholder col-4"></span><span
                                class="placeholder col-8 d-block mt-2"></span></div>
                    </div>
                    <div class="card mb-2">
                        <div class="card-body"><span class="placeholder col-6"></span><span
                                class="placeholder col-7 d-block mt-2"></span></div>
                    </div>
                    <div class="card mb-2">
                        <div class="card-body"><span class="placeholder col-5"></span><span
                                class="placeholder col-9 d-block mt-2"></span></div>
                    </div>
                </div>

                <!-- Résultats (HTML rendu par la route user.searchldap) -->
                <div id="search-results" class="d-none"></div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
            </div>
        </div>
    </div>
</div>
