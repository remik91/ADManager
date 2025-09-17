@extends('layouts.app')
@section('icon', 'fas fa-fw fa-building-user')
@section('h1', 'Utilisateurs')

@section('content')

    <div class="row">

        <div class="col-md-12">

            <div class="card mb-3">

                <div class="card-header text-end">
                    <div class="ml-auto">

                        <a class="btn btn-outline-secondary my-auto m-lg-1" href="{{ route('user.orphelin') }}"><i
                                class="fa-solid fa-child-reaching"></i> Orphelin</a>
                        <a class="btn btn-outline-secondary my-auto m-lg-1" href="{{ route('user.finfonction') }}"><i
                                class="fa-solid fa-users-slash"></i> Fin de fonction</a>
                        <a class="btn btn-outline-secondary my-auto m-lg-1" href="{{ route('user.trash') }}"><i
                                class="fas fa-trash"></i> Corbeille</a>
                        <a class="btn btn-info my-auto text-white" href="{{ route('user.import') }}"><i
                                class="fas fa-user-plus"></i> Création d'utilisateur</a>

                    </div>
                </div>

            </div>
            <!-- end card -->

        </div>
    </div>
    <div class="row mb-3">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body text-center">

                    <form id="filtre" role="form" action="{{ route('user.index') }}" method="GET"
                        autocomplete="off">
                        @csrf
                        <div class="row">
                            <div class="col-md-12">
                                <div class="row">
                                    <div class="col-2">
                                        <div class="form-group mb-3">
                                            <label>Etat du compte:</label>
                                            <select class="form-select" name="filterStatus" id="filterStatus">
                                                <option value="" selected="">Tout</option>
                                                <option value="66048">Activé</option>
                                                <!--Normal Account / Don't expire password -->
                                                <option value="66050">Désactivé</option>
                                                <!-- Compte verrouiller / don't expire password -->
                                            </select>
                                        </div>
                                    </div>
                                    <!-- Ajout du filtre de date -->
                                    <div class="col-2">
                                        <div class="form-group mb-3">
                                            <label for="dateFilter">Filtrer par date de connexion :</label>
                                            <select class="form-select" name="dateFilter" id="dateFilter">
                                                <option value="" selected="">Toutes</option>
                                                <option value="today">Aujourd'hui</option>
                                                <option value="thisWeek">Cette semaine</option>
                                                <option value="thisMonth">Ce mois-ci</option>
                                                <option value="lastMonth">Le mois dernier</option>
                                                <option value="lastYear">Il y a plus d'un an</option>
                                                <!-- Ajoutez d'autres options de filtrage si nécessaire -->
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-2">
                                        <div class="form-group mb-3">
                                            <label>Synchronisation LDAP:</label>
                                            <select class="form-select" name="FilterSynchro" id="FilterSynchro">
                                                <option value="" selected="">Tout</option>
                                                <option value="TRUE">Synchro LDAP</option>
                                                <option value="FALSE">Non synchro</option>

                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-2">
                                        <div class="form-group mb-3">
                                            <label>Type:</label>
                                            <select class="form-select" name="typeOfAccount">
                                                <option value="" selected="">Tout</option>
                                                <option value="utilisateur">Utilisateur</option>
                                                <option value="fonctionnel">Fonctionnel</option>
                                                <option value="provisoire">Provisoire</option>
                                                <option value="stagiaire">Stagiaire</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div class="form-group mb-3">
                                            <label>Organisation:</label>
                                            <select class="form-select" name="search_ou" style="width: 100%;">
                                                @foreach ($ouList as $ou)
                                                    <option value="{{ $ou->getDn() }}">{{ $ou->getName() }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="input-group mb-3 col-12">
                                    <input type="text" class="form-control" name="search_uid" minlength="2"
                                        value="{{ $searchText }}" placeholder="Recherche d'utilisateur par UID...">
                                    <button class="btn btn-outline-secondary" type="submit"><i
                                            class="fas fa-search"></i></button>

                                </div>

                            </div>

                        </div>
                    </form>

                </div>

            </div>

        </div>

    </div>

    @if (request()->hasAny(['search_ou', 'search_uid', 'typeOfAccount']))
        @if (count($listusers) > 0)
            <div class="row">
                <div class="col-9">
                    <form id="myForm" action="{{ route('user.actionlist') }}" method="POST">
                        @csrf
                        <div class="card">
                            <div class="card-header" id="actionmenu" style="display:none">
                                <div class="ml-auto">
                                    <button class="btn btn-warning my-auto m-lg-1" name="action"
                                        value="desactivate">Désactiver</button>
                                    <button class="btn btn-success my-auto m-lg-1" name="action"
                                        value="activate">Activer</button>
                                    <button class="btn btn-danger my-auto m-lg-1" name="action"
                                        value="delete">Supprimer</button>
                                    <button type="button" class="btn btn-primary my-auto m-lg-1" data-bs-toggle="modal"
                                        data-bs-target="#ChangeOUModal">
                                        Changer d'organisation
                                    </button>
                                    <button type="button" class="btn btn-primary my-auto m-lg-1" data-bs-toggle="modal"
                                        data-bs-target="#AddGroupeModal">
                                        Ajouter à un groupe
                                    </button>
                                </div>
                            </div>
                            <!-- /.card-header -->
                            <div class="card-body">

                                <table id="listusers" class="table table-bordered table-hover display nowrap">
                                    <thead class="table-dark">
                                        <tr>
                                            <th>#</th>
                                            <th>UID</th>
                                            <th>Nom complet</th>
                                            <th>Adresse mail</th>
                                            <th>Service</th>
                                            <th>Organisation</th>
                                            <th>Etat</th>
                                            <th>Dernière connexion</th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                        @csrf
                                        @foreach ($listusers as $value)
                                            <tr>
                                                <td><input type="checkbox" class="rowCheckbox" name="rowCheckbox[]"
                                                        id="rowCheckbox" value="{{ $value->getDn() }}"></td>
                                                <td data-id="{{ $value->getDn() }}">
                                                    <a
                                                        href="{{ url('/user/view/' . $value->getDn()) }}">{{ $value->cn[0] ?? '' }}</a>
                                                </td>
                                                <td> {{ $value->displayName[0] ?? '' }} </td>
                                                <td> {{ $value->mail[0] ?? '' }} </td>
                                                <td> {{ $value->department[0] ?? '' }} </td>
                                                <td> {{ $value->getParentName() }} </td>
                                                <td>
                                                    @if ($value->isEnabled())
                                                        <span class="badge rounded-pill bg-success"><i
                                                                class="fa-solid fa-user-check"></i></span>
                                                    @else
                                                        <span class="badge rounded-pill bg-warning text-dark"><i
                                                                class="fa-solid fa-user-lock"></i></span>
                                                    @endif
                                                </td>
                                                <td> {{ $value->lastlogon ?? '' }} </td>
                                            </tr>
                                        @endforeach

                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th>#</th>
                                            <th>UID</th>
                                            <th>Nom complet</th>
                                            <th>Adresse mail</th>
                                            <th>Service</th>
                                            <th>Organisation</th>
                                            <th>Etat</th>
                                            <th>Dernière connexion</th>
                                        </tr>
                                    </tfoot>
                                </table>

                            </div>
                            <!-- /.card-body -->
                        </div>
                        <!-- /.card -->

                        <!-- Modal Change OU-->
                        <div class="modal fade" id="ChangeOUModal" tabindex="-1" aria-labelledby="ChangeOUModalLabel"
                            aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h1 class="modal-title fs-5" id="exampleModalLabel">Changement d'unité
                                            d'organisation</h1>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="form-group">
                                            <label for="givenname" class="col-sm-12 col-form-label">Selectionner une
                                                nouvelle unité
                                                d'organisation:</label>
                                            <br>
                                            <div class="col-sm-12">
                                                <select name="newou" id="newou" class="form-control" required>
                                                    @foreach ($ouList as $ou)
                                                        <option value="{{ $ou->getDn() }}">{{ $ou->getName() }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary"
                                            data-bs-dismiss="modal">Close</button>
                                        <button type="submit" class="btn btn-primary" name="action"
                                            value="ChangeOU">Déplacer</button>
                                    </div>

                                </div>
                            </div>
                        </div>

                        <!-- Modal AddGroupe -->
                        <div class="modal fade" id="AddGroupeModal" tabindex="-1" aria-labelledby="AddGroupeModalLabel"
                            aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h1 class="modal-title fs-5" id="exampleModalLabel">Ajout dans un groupe </h1>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="form-group">
                                            <label for="givenname" class="col-sm-12 col-form-label">Selectionner un
                                                service pour ajouter les utilisateurs:</label>
                                            <br>
                                            <div class="col-sm-12">
                                                <select name="addgroupe" id="addgroupe" class="form-control" required>
                                                    @foreach ($services ?? '' as $value)
                                                        <option value="{{ $value->getDn() }}">{{ $value->getName() }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary"
                                            data-bs-dismiss="modal">Close</button>
                                        <button type="submit" class="btn btn-primary" name="action"
                                            value="AddGroupe">Ajouter</button>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <!-- /.col -->

                <div class="col-3">
                    <div id="resultats_shortview">
                        @if ($listusers->count() >= 1000)
                            <div class="alert alert-warning" role="alert">
                                Le nombre de résultat est trop élévé (Maximum 1000). Affinez vos résultats à l'aide de la
                                recherche.
                            </div>
                        @endif
                    </div>

                </div>

            </div>
            <!-- /.row -->
        @else
            <div class="alert alert-dark" role="alert">
                Aucun utilisateur trouvé
            </div>
        @endif
    @endif



@stop

@section('scriptjs')
    <script type="module">
        // Fonction pour gérer l'affichage du menu d'actions en fonction des cases cochées
        function handleCheckboxChange() {
            let checkboxes = document.querySelectorAll('.rowCheckbox');
            let checked = Array.from(checkboxes).some(checkbox => checkbox.checked);

            let actionMenu = document.getElementById('actionmenu');
            if (checked) {
                actionMenu.style.display = 'block';
            } else {
                actionMenu.style.display = 'none';
            }
        }

        // Attacher un gestionnaire d'événements aux cases à cocher pour détecter les changements
        let checkboxes = document.querySelectorAll('.rowCheckbox');
        checkboxes.forEach(checkbox => {
            checkbox.addEventListener('change', handleCheckboxChange);
        });

        let table = $("#listusers").DataTable({
            columnDefs: [{
                    targets: 0,
                    orderData: false,
                    orderable: false,
                } // Désactiver le tri pour la première colonne (les cases à cocher)
            ],
            order: [
                [1, 'asc']
            ],
            dom: '<"d-flex justify-content-between align-items-center mb-3"Blf>rt<"d-flex justify-content-between align-items-center mb-3"ip>',
            responsive: true,
            lengthChange: true,
            autoWidth: false,
            select: true,
            buttons: ["copyHtml5", "csvHtml5", "print"]
        });

        table.on('select', function(e, dt, type, indexes) {
            if (indexes.length > 0) {
                // Accède au contenu de la première colonne de la première ligne
                let celluleSelectionnee = table.cell(indexes[0], 1).node();

                // Récupère la valeur de data-id de la cellule
                let dataId = $(celluleSelectionnee).attr('data-id');

                // Effectue une requête Ajax
                $.ajax({
                    url: '/user/shortview/', // Remplacez par votre URL de requête
                    type: 'GET',
                    dataType: 'html',
                    data: {
                        id: dataId
                    },
                    success: function(data) {
                        $('#resultats_shortview').html(
                            data); // Met à jour avec le contenu HTML de la réponse
                    },
                    error: function(error) {
                        console.error('Erreur lors de la requête Ajax :', error);
                    }
                });

            }
        });
    </script>
@stop
