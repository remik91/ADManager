@extends('layouts.app')
@section('icon', 'fas fa-fw fa-user')
@section('h1', 'Création d\'utilisateur')

@section('content')
    <div class="row">
        <div class="col-md-12">

            <div class="card mb-3">

                <div class="card-header">
                    <a href="{{ route('user.index') }}" class="btn btn-light "><i class="fa-solid fa-chevron-left"></i></a>
                </div>

            </div>
            <!-- end card -->

        </div>
    </div>



    @isset($userAD)
        <div class="alert alert-warning" role="alert">
            Le compte existe déjà sur l'Active Directory
        </div>
    @endisset

    <form class="" action="{{ route('user.create') }}" method="post">
        @csrf

        <input type="text" name="synctoldap" id="synctoldap"
            value="@if (isset($userLDAP->uid[0])) TRUE @else FALSE @endif" hidden>
        <div class="row">

            <!-- Left side -->
            <div class="col-lg-10">

                <div class="card mb-4">
                    <div class="card-header">
                        Renseigner les informations de l'utilisateur
                    </div>
                    <div class="card-body">

                        <div class="row">
                            <!-- Left side -->
                            <div class="col-lg-8">
                                <!-- Basic information -->
                                <div class="card mb-4">
                                    <div class="card-body">
                                        <h3 class="h6 mb-4">Informations d'identification</h3>
                                        <div class="row">
                                            <div class="col-lg-6">
                                                <div class="mb-3">
                                                    <label class="form-label">Prénom*</label>
                                                    <input type="text" class="form-control" name="givenName"
                                                        id="givenName" value="{{ $userLDAP->givenname[0] ?? '' }}"
                                                        autocomplete="off" required>
                                                </div>
                                            </div>
                                            <div class="col-lg-6">
                                                <div class="mb-3">
                                                    <label class="form-label">Nom*</label>
                                                    <input type="text" class="form-control" name="sn" id="sn"
                                                        value="{{ $userLDAP->sn[0] ?? '' }}" autocomplete="off" required>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-lg-6">
                                                <div class="mb-3">
                                                    <label class="form-label">UID*</label>
                                                    <input type="text" class="form-control" name="uid" id="uid"
                                                        autocomplete="off" value="{{ $userLDAP->uid[0] ?? '' }}" required>
                                                </div>
                                            </div>
                                            <div class="col-lg-6">
                                                <div class="mb-3">
                                                    <label class="form-label">Mail*</label>
                                                    <input type="mail" class="form-control" name="mail" id="mail"
                                                        value="{{ $userLDAP->mail[0] ?? '' }}" autocomplete="off" required>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- Address -->
                                <div class="card mb-4">
                                    <div class="card-body">
                                        <h3 class="h6 mb-4">Informations complémentaire</h3>
                                        <div class="row">
                                            <div class="col-lg-6">
                                                <div class="mb-3">
                                                    <label class="form-label">Unité d'organisation*</label>
                                                    <select class=" form-control" name="ou" id="ou" required>
                                                        <option value selected disabled>Choisir une unité d'organisation
                                                        </option>
                                                        @foreach ($ouList as $ou)
                                                            <option value="{{ $ou->getDn() }}">{{ $ou->getName() }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-lg-6">
                                                <div class="mb-3">
                                                    <label class="form-label">Bureau</label>
                                                    <input type="mail" class="form-control"
                                                        name="physicalDeliveryOfficeName" id="physicalDeliveryOfficeName"
                                                        value="{{ $userLDAP->bureau[0] ?? '' }}" autocomplete="off">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-lg-3">
                                                <div class="mb-3">
                                                    <label class="form-label">Division</label>
                                                    <input type="mail" class="form-control" name="division"
                                                        id="division" value="{{ $userLDAP->division[0] ?? '' }}"
                                                        autocomplete="off">
                                                </div>
                                            </div>
                                            <div class="col-lg-5">
                                                <div class="mb-3">
                                                    <label class="form-label">Service</label>
                                                    <input type="mail" class="form-control" name="department"
                                                        id="department" value="{{ $userLDAP->service[0] ?? '' }}"
                                                        autocomplete="off">
                                                </div>
                                            </div>
                                            <div class="col-lg-4">
                                                <div class="mb-3">
                                                    <label class="form-label">Fonction</label>
                                                    <input type="text" class="form-control" name="title"
                                                        id="title" autocomplete="off"
                                                        value="{{ $userLDAP->fonction[0] ?? '' }}">
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- Right side -->
                            <div class="col-lg-4">
                                <!-- Status -->
                                <div class="card mb-4">
                                    <div class="card-body">
                                        <h3 class="h6">Type de compte</h3>
                                        <select class="form-select" name="typeOfAccount" required>
                                            <option value="utilisateur" selected="">Utilisateur</option>
                                            <option value="fonctionnel">Fonctionnel</option>
                                            <option value="provisoire">Provisoire</option>
                                            <option value="stagiaire">Stagiaire</option>
                                        </select>
                                    </div>
                                </div>
                                <!-- Notes -->
                                <div class="card mb-4">
                                    <div class="card-body">
                                        <h3 class="h6">Description</h3>
                                        <textarea class="form-control" rows="3"></textarea>
                                    </div>
                                </div>
                                <!-- Notification settings -->
                                <div class="card mb-4">
                                    <div class="card-body">
                                        <h3 class="h6">Paramètres</h3>
                                        <ul class="list-group list-group-flush mx-n2">
                                            <li
                                                class="list-group-item px-0 d-flex justify-content-between align-items-start">
                                                <div class="ms-2 me-auto">
                                                    <h6 class="mb-0">Repertoire personnel</h6>
                                                    <small>Activer le repertoire personnel.</small>
                                                </div>
                                                <div class="form-check form-switch">
                                                    <input class="form-check-input" type="checkbox" role="switch"
                                                        name="repperso">
                                                </div>
                                            </li>
                                            <li
                                                class="list-group-item px-0 d-flex justify-content-between align-items-start">
                                                <div class="ms-2 me-auto">
                                                    <h6 class="mb-0">Envoie Email</h6>
                                                    <small>Envoyer un email de confirmation à l'utilisateur</small>
                                                </div>
                                                <div class="form-check form-switch">
                                                    <input class="form-check-input" type="checkbox" role="switch"
                                                        disabled>
                                                </div>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>

                            <small>En cliquant sur Créer utilisateur, un mot de passe par défaut est généré pour
                                l'utilisateur.
                                <br>L'utilisateur doit changer son mot de passe via Identifiant pour se connecter à son
                                compte.<br>Dans le cas d'un compte non importé du LDAP, le mot de passe par défaut est :
                                %Rectorat94*</small>
                        </div>

                    </div>
                </div>
            </div>
            <div class="col-lg-2">
                <div class="card mb-4">
                    <div class="card-header">Actions</div>
                    <div class="card-body">
                        <div class="list-group mb-3">
                            <button type="button" class="btn btn-info text-white" data-bs-toggle="modal"
                                data-bs-target="#exampleModal">
                                <i class="fas fa-user-tag"></i> Importer utilisateur
                            </button>
                        </div>
                        <div class="list-group mb-3">
                            <button type="submit" class="btn btn-block btn-primary" name="CreateUser"><i
                                    class="fas fa-user-plus"></i> Créer
                                utilisateur</button>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </form>
@stop

@include('user.modal_importldap')


@section('scriptjs')
    <script type="module">
        $(document).ready(function() {
            $('#search-form-ldap').submit(function(e) {
                e.preventDefault(); // Empêche la soumission du formulaire

                var searchCrit = $('select[name="search_crit"]').val();
                var searchOU = $('select[name="search_ou"]').val();
                var searchTerm = $('input[name="search_uid"]').val();

                $.ajax({
                    url: '{{ route('user.searchldap') }}',
                    type: 'GET',
                    data: {
                        q: searchTerm,
                        ou: searchOU,
                        crit: searchCrit
                    },
                    success: function(response) {

                        $('#search-results').html(response);
                        // $('#ChangePasswdModal').modal('show');
                    },
                    error: function(xhr, status, error) {
                        console.error(error);
                    }
                });
            });
        });
    </script>
@endsection
