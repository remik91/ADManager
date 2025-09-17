<!-- Modal -->
<div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="exampleModalLabel">Importation d'usager depuis LDAP ACA</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <form role="form" action="" id="search-form-ldap" autocomplete="off">
                                @csrf
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="row">
                                            <div class="col-6">
                                                <div class="form-group mb-3">
                                                    <select class="form-select" name="search_crit" style="width: 100%;">
                                                        <option value="uid" selected>Par UID</option>
                                                        <option value="sn">Par nom</option>
                                                        <option value="mail">Par mail</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <div class="form-group mb-3">
                                                    <select class="form-select" name="search_ou" style="width: 100%;">
                                                        <option value="personnels EN" selected>Personnels EN</option>
                                                        <option value="autres">Autre</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="input-group">
                                            <input type="search" class="form-control" id="search_uid" name="search_uid"
                                                placeholder="Recherche d'utilisateur sur le LDAP"
                                                aria-label="Recherche rapide" autocomplete="off">
                                            <button class="btn btn-outline-secondary" type="submit"><i
                                                    class="fas fa-search"></i></button>
                                        </div>

                                    </div>

                                </div>
                            </form>

                        </div>
                    </div>

                    <div id="search-results"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                </div>
            </div>
        </div>
    </div>

</div>
