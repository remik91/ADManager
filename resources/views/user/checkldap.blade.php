@if ($usersldap)
    <!-- Profile Image -->
    <div class="card mb-3">
        <div class="card-body">
            <h6 class=" text-center"> <a
                    href="{{ route('user.resynchro', ['AD' => $userADdn, 'ldap' => $usersldap->getDn()]) }}"
                    class="btn btn-outline-secondary  text-center" type="button"
                    onclick='return confirm("Attention : La synchronisation va importer les données utilisateur depuis LDAP vers AD. Il est possible que des données soient écrasées ultérieurement si elles ne sont pas présentes dans LDAP.")'><i
                        class="fa-solid fa-people-pulling"></i>
                    Resynchro</a>
            </h6>

            <h6 class="profile-username text-center">Information LDAP de {{ $usersldap->codecivilite[0] }}
                {{ $usersldap->cn[0] }} </h6>




            <form class="row g-3">
                <div class="col-md-6">
                    <label for="inputEmail4" class="form-label">Prénom</label>
                    <input type="text" id="" class="form-control" value="{{ $usersldap->givenname[0] }}"
                        readonly>
                </div>
                <div class="col-md-6">
                    <label for="inputPassword4" class="form-label">Nom</label>
                    <input type="text" class="form-control" value="{{ $usersldap->sn[0] }}" readonly>
                </div>
                <div class="col-md-6">
                    <label for="inputPassword4" class="form-label">UID</label>
                    <input type="text" class="form-control" value="{{ $usersldap->uid[0] }}" readonly>
                </div>
                <div class="col-md-6">
                    <label for="inputPassword4" class="form-label">Date de naissance</label>
                    <input type="text" class="form-control" value="{{ $usersldap->datenaissance[0] }}" readonly>
                </div>
                <div class="col-12">
                    <label for="inputAddress" class="form-label">Adresse Mail</label>
                    <input type="mail" class="form-control" value="{{ $usersldap->mail[0] }}" readonly>
                </div>
                <div class="col-md-3">
                    <label for="inputPassword4" class="form-label">Division</label>
                    <input type="text" class="form-control" value="{{ $usersldap->division[0] }}" readonly>
                </div>
                <div class="col-md-3">
                    <label for="inputPassword4" class="form-label">Service</label>
                    <input type="text" class="form-control" value="{{ $usersldap->service[0] }}" readonly>
                </div>
                <div class="col-md-3">
                    <label for="inputPassword4" class="form-label">Fonction</label>
                    <input type="text" class="form-control" value="{{ $usersldap->fonction[0] }}" readonly>
                </div>
                <div class="col-md-3">
                    <label for="inputPassword4" class="form-label">Titre</label>
                    <input type="text" class="form-control" value="{{ $usersldap->title[0] }}" readonly>
                </div>
                <div class="col-md-4">
                    <label for="inputPassword4" class="form-label">Fin de fonction</label>
                    <input type="text" class="form-control" value="{{ $usersldap->finfonction[0] }}" readonly>
                </div>
                <div class="col-md-4">
                    <label for="inputPassword4" class="form-label">Date de fin de fonction</label>
                    <input type="text" class="form-control" value="{{ $usersldap->dateff[0] }}" readonly>
                </div>
                <div class="col-12">
                    <label for="inputAddress2" class="form-label">Distinguished Name</label>
                    <input type="text" class="form-control" value="{{ $usersldap->getDn() }}" readonly>
                </div>
                <div class="col-md-4">
                    <label for="inputPassword4" class="form-label">Mot de passe Robuste</label>
                    <input type="text" class="form-control" value="{{ $usersldap->cremdprobust[0] }}" readonly>
                </div>
                <div class="col-md-4">
                    <label for="inputPassword4" class="form-label">Radius Group Name</label>
                    <input type="text" class="form-control" value="{{ $usersldap->radiusgroupname[0] }}" readonly>
                </div>
            </form>

        </div>
        <!-- /.card-body -->
    </div>
    <!-- /.card -->
@else
    <div class="alert alert-danger" role="alert">
        Aucun utilisateur correspondant trouvé sur le LDAP Académique.
    </div>
@endif
