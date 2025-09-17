@extends('layouts.app')
@section('icon', 'fas fa-fw fa-user')
@section('h1', 'Profile')

@section('css')

    <style>
        [data-letters]:before {
            content: attr(data-letters);
            display: inline-block;
            font-size: 2.0em;
            width: 3.0em;
            height: 3.0em;
            line-height: 3.0em;
            text-align: center;
            border-radius: 50%;
            background: #558f98;
            box-shadow: 0 .5rem 1rem rgba(0, 0, 0, .15) !important;
            vertical-align: middle;
            color: white;
        }

        .profile-username {
            font-size: 21px;
            margin-top: 5px;
        }
    </style>

@stop

@section('content')

    <div class="row">
        <div class="col-md-12">
            <div class="card mb-3">
                <div class="card-body d-flex justify-content-between align-items-center ">
                    <div class="col-4">
                        <div class="input-group flex-column">
                            <form action="{{ route('user.search', $user->getDn()) }}" method="POST">
                                @csrf
                                <div class="input-group">
                                    <a href="{{ route('user.index') }}" class="btn btn-outline-secondary"><i
                                            class="fa-solid fa-chevron-left"></i></a>

                                    <input type="text" class="typeahead form-control" id="Searchuser" name="searchuid"
                                        placeholder="Rechercher...">
                                    <button class="btn btn-outline-secondary" type="submit">Rechercher</button>
                                </div>
                            </form>
                        </div>
                    </div>
                    <div class="text-end">
                        <a href="{{ route('user.active', $user->getDn()) }}" class="btn btn-outline-secondary">
                            @if ($user->isEnabled())
                                <i class="fa fa-user-lock"></i>
                                Désactiver l'utilisateur
                            @else
                                <i class="fa fa-user-lock"></i>
                                Activer l'utilisateur
                            @endif
                        </a>


                        <button type="button" class="btn btn-outline-secondary" data-bs-toggle="modal"
                            data-bs-target="#ChangePasswdModal">
                            <i class="fa fa-key"></i> Changer mot de passe
                        </button>


                        <a href="{{ route('user.remove', $user->getDn()) }}" class="btn btn-outline-secondary"
                            role="button" onclick='return confirm("Voulez-vous vraiment supprimer cette utilisateur ?")'><i
                                class="fa fa-trash"></i> Supprimer le compte</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if ($user)
        <div class="row">
            <div class="col-md-3">

                <!-- Profile Image -->
                <div
                    class="card @if ($user->isEnabled()) card-primary @else card-warning @endif card-outline mb-3 shadow-sm">
                    <div class="card-body">
                        <div class="text-center">
                            <p data-letters="{{ $initial }}"></p>
                        </div>

                        <h3 class="profile-username text-center">{{ $user->displayName[0] }}</h3>

                        <p class="text-muted text-center"> <a href="mailto:{{ $user->mail[0] }}">{{ $user->mail[0] }}</a>
                        </p>

                        <ul class="list-group list-group-unbordered mb-3">
                            <li class="list-group-item">
                                <b>UID</b> <a class="float-end">{{ $user->sAMAccountName[0] }}</a>
                            </li>
                            <li class="list-group-item">
                                <b>Téléphone</b> <a href="tel:{{ $user->telephonenumber[0] ?? '' }}"
                                    class="float-end">{{ $user->telephonenumber[0] ?? '' }}</a>
                            </li>
                            <li class="list-group-item">
                                <b>Bureau</b> <a class="float-end">{{ $user->physicaldeliveryofficename[0] ?? '' }}</a>
                            </li>
                            <li class="list-group-item">
                                <b>Créer le</b> <a class="float-end">{{ $user->whencreated }}</a>
                            </li>
                            <li class="list-group-item">
                                <b>Nombres de co.</b> <a class="float-end">{{ $user->logoncount[0] }}</a>
                            </li>
                            <li class="list-group-item">
                                <b>Dernière co.</b> <a class="float-end">{{ $user->lastlogon }}</a>
                            </li>
                            <li class="list-group-item">
                                <b>Dernière modif.</b> <a class="float-end">{{ $user->whenchanged }}</a>
                            </li>
                        </ul>
                    </div>
                    <!-- /.card-body -->
                </div>
                <!-- /.card -->


                <div class="card mb-4">
                    <div class="card-body">
                        <form id="softphonieForm" action="{{ route('user.softphonie', $user->getDn()) }}" method="POST">
                            @csrf
                            <ul class="list-group list-group-flush mx-n2">
                                <li class="list-group-item px-0 d-flex justify-content-between align-items-start">
                                    <div class="ms-2 me-auto">
                                        <h6 class="mb-0">Application Softphonie</h6>
                                        <small>Activer l'application OpenTouchConversation</small>
                                    </div>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" role="switch" name="softphonie"
                                            @if ($user->groups()->exists($softphonie)) checked @endif id="softphonie">
                                    </div>
                                </li>
                            </ul>
                        </form>
                    </div>
                </div>


            </div>
            <!-- Colonne de gauche avec infos -->


            <div class="col-md-9">
                @if (!$user->isEnabled())
                    <div class="alert alert-warning" role="alert">
                        Le compte est désactivé.
                    </div>
                @endif
                <div class="card shadow-sm">

                    <div class="card-header p-2">
                        <div class="float-start">
                            <ul class="nav nav-pills d-flex" id="myTab">
                                <li class="nav-item" role="presentation">
                                    <a class="nav-link active" id="pills-home-tab" data-bs-toggle="pill"
                                        data-bs-target="#pills-home" role="tab" aria-controls="pills-home"
                                        aria-selected="true" href="#">Général</a>
                                </li>

                                <li class="nav-item" role="presentation">
                                    <a class="nav-link" id="pills-contact-tab" data-bs-toggle="pill"
                                        data-bs-target="#pills-contact" role="tab" aria-controls="pills-contact"
                                        aria-selected="false" href="#">Contact</a>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <a class="nav-link" id="pills-groupes-tab" data-bs-toggle="pill"
                                        data-bs-target="#pills-groupes" role="tab" aria-controls="pills-groupes"
                                        aria-selected="false" href="#">Groupes</a>
                                </li>
                                @if (env('ACTIVE_REPPERSO'))
                                    <li class="nav-item" role="presentation">
                                        <a class="nav-link" id="pills-dossier-tab" data-bs-toggle="pill"
                                            data-bs-target="#pills-dossier" role="tab" aria-controls="pills-dossier"
                                            aria-selected="false" href="#">Dossier</a>
                                    </li>
                                @endif

                                <li class="nav-item" role="ldapacad">
                                    <a class="nav-link" id="pills-ldapacad-tab" data-bs-toggle="pill"
                                        data-bs-target="#pills-ldapacad" role="tab" aria-controls="pills-ldapacad"
                                        aria-selected="false" href="#">LDAP
                                        Académique</a>
                                </li>
                                @if (env('ACTIVE_STOCKM'))
                                    <li class="nav-item" role="stockmanager">
                                        <a class="nav-link" id="pills-stockmanager-tab" data-bs-toggle="pill"
                                            data-bs-target="#pills-stockmanager" role="tab"
                                            aria-controls="pills-stockmanager" aria-selected="false"
                                            href="#">StockManager</a>
                                    </li>
                                @endif

                            </ul>
                        </div>


                    </div><!-- /.card-header   Onglet du menu de droite -->

                    <div class="card-body">
                        <div class="tab-content">
                            {{-- Tableau Informations --}}
                            <div class="tab-pane fade show active" id="pills-home" role="tabpanel"
                                aria-labelledby="pills-home-tab">
                                <form class="row g-3" role='form' method='POST'
                                    action='{{ route('user.update', ['id' => $user->getDn(), 'type' => 'general']) }}'>

                                    @csrf

                                    <div class="col-md-4">
                                        <label for="givenname" class="form-label">Prenom*</label>
                                        <input type="text" class="form-control" id="givenname" name="givenname"
                                            placeholder="Prénom" value="{{ $user->givenname[0] ?? '' }}" required="">
                                    </div>
                                    <div class="col-md-4">
                                        <label for="sn" class="form-label">Nom*</label>
                                        <input type="text" class="form-control" id="sn" name="sn"
                                            placeholder="Nom" value="{{ $user->sn[0] ?? '' }}" required="">
                                    </div>
                                    <div class="col-md-4">
                                        <label for="initials" class="form-label">Initials</label>
                                        <input type="text" class="form-control" id="initials" name="initials"
                                            placeholder="Initials" value="{{ $user->initials[0] ?? '' }}">
                                    </div>
                                    <div class="col-md-5">
                                        <label for="samaccountname" class="form-label">Nom d'utilisateur
                                            (UID)</label>
                                        <input type="text" class="form-control" id="samaccountname"
                                            name="samaccountname" placeholder="UID utilisateur"
                                            value="{{ $user->samaccountname[0] ?? '' }}" disabled>
                                    </div>
                                    <div class="col-md-5">
                                        <label for="displayname" class="form-label">Nom
                                            d'affichage</label>
                                        <input type="text" class="form-control" id="displayname" name="displayname"
                                            placeholder="Nom affiché pour la session"
                                            value="{{ $user->displayname[0] ?? '' }}">
                                    </div>
                                    <div class="col-12">
                                        <label for="mail" class="form-label">Adresse Mail</label>

                                        <input type="mail" class="form-control" id="mail" name="mail"
                                            placeholder="Email" autocomplete="off" value="{{ $user->mail[0] ?? '' }}"
                                            required="">
                                    </div>
                                    <div class="col-md-4">
                                        <label for="orgunit" class="form-label">Organisation</label>
                                        <select class="form-select" name="orgunit" id="orgunit" style="width: 100%;">
                                            @foreach ($ouList as $ou)
                                                <option value="{{ $ou->getDn() }}"
                                                    @if ($user->getParentName() == $ou->getName()) selected @endif>
                                                    {{ $ou->getName() }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label for="typeOfAccount" class="form-label">Type de
                                            compte</label>
                                        <select class="form-select" id="typeOfAccount" name="typeOfAccount" required>
                                            <option value=""
                                                {{ empty(optional($user->typeOfAccount)[0]) ? 'selected' : '' }} disabled>
                                                Aucun
                                            </option>
                                            <option value="utilisateur"
                                                {{ optional($user->typeOfAccount)[0] === 'utilisateur' ? 'selected' : '' }}>
                                                Utilisateur
                                            </option>
                                            <option value="fonctionnel"
                                                {{ optional($user->typeOfAccount)[0] === 'fonctionnel' ? 'selected' : '' }}>
                                                Fonctionnel
                                            </option>
                                            <option value="provisoire"
                                                {{ optional($user->typeOfAccount)[0] === 'provisoire' ? 'selected' : '' }}>
                                                Provisoire
                                            </option>
                                            <option value="stagiaire"
                                                {{ optional($user->typeOfAccount)[0] === 'stagiaire' ? 'selected' : '' }}>
                                                Stagiaire
                                            </option>
                                        </select>

                                    </div>
                                    <div class="col-md-4">
                                        <label for="syncToLDAP" class="form-label">Importer du
                                            LDAP</label>
                                        <input type="text" class="form-control" id="syncToLDAP" autocomplete="off"
                                            value="{{ $user->syncToLDAP[0] ?? '' }}" disabled="disabled">
                                    </div>
                                    <div class="col-12">
                                        <label for="description" class="form-label">Description</label>
                                        <textarea type="text" class="form-control" id="description" name="description" placeholder="Description"
                                            autocomplete="off">{{ $user->description[0] ?? '' }}</textarea>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="objectguid" class="">GUID</label>
                                        <input type="text" class="form-control-plaintext" id="objectguid"
                                            value="{{ $user->getConvertedGuid() }}" readonly>
                                    </div>

                                    <div class="col-md-6">
                                        <label for="dn" class="">Distinguished name
                                            (DN)</label>
                                        <input type="text" class="form-control-plaintext" id="dn"
                                            autocomplete="off" value="{{ $user->getDn() }}" readonly>
                                    </div>


                                    <div class="col-12">
                                        <button type="submit"
                                            class="btn btn-danger">{{ __('validation.save') }}</button>
                                    </div>
                                </form>
                            </div>
                            {{-- Tableau LDAP --}}
                            <div class="tab-pane fade" id="pills-ldapacad" role="tabpanel"
                                aria-labelledby="pills-ldapacad-tab">


                                <div class="flex-column align-items-center text-center">
                                    <button class="btn btn-outline-secondary " type="button" id="btncheckldap"
                                        data-id="{{ $user->sAMAccountName[0] }}"><i class="fas fa-user-check"></i> Check
                                        LDAP</button>
                                    <button class="btn btn-outline-secondary " type="button" disabled><i
                                            class="fa-solid fa-people-pulling"></i>
                                        Resynchro (désactivé)</button>
                                </div>
                                <div id="resultats_checkldap" class="mt-3"></div>
                            </div>

                            {{-- Tableau Contact --}}
                            <div class="tab-pane fade" id="pills-contact" role="tabpanel"
                                aria-labelledby="pills-contact-tab">
                                <form class="form-horizontal" role='form' method='POST'
                                    action='{{ route('user.update', ['id' => $user->getDn(), 'type' => 'contact']) }}'>
                                    @csrf

                                    <div class="row mb-3">
                                        <label for="telephonenumber" class="col-sm-2 col-form-label">Téléphone
                                            bureau*</label>
                                        <div class="col-sm-4">
                                            <input type="tel" class="form-control" id="telephoneNumber"
                                                name="telephoneNumber" minlength="6" placeholder="XXXX"
                                                autocomplete="off" value="{{ $user->telephonenumber[0] ?? '' }}">
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <label for="givenname" class="col-sm-2 col-form-label">Téléphone
                                            portable</label>
                                        <div class="col-sm-4">
                                            <input type="text" class="form-control" id="mobile" name="mobile"
                                                placeholder="XXXXXXXXXX" minlength="10"
                                                value="{{ $user->mobile[0] ?? '' }}">
                                        </div>
                                    </div>


                                    <div class="row mb-3">
                                        <label for="physicaldeliveryofficename"
                                            class="col-sm-2 col-form-label">Bureau</label>
                                        <div class="col-sm-8">
                                            <input type="text" class="form-control" id="physicaldeliveryofficename"
                                                name="physicaldeliveryofficename" placeholder="Numéro de bureau"
                                                autocomplete="off"
                                                value="{{ $user->physicaldeliveryofficename[0] ?? '' }}">
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <label for="sn" class="col-sm-2 col-form-label">Fonction</label>
                                        <div class="col-sm-8">
                                            <input type="text" class="form-control" id="title" name="title"
                                                value="{{ $user->title[0] ?? '' }}">
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <label for="telephonenumber" class="col-sm-2 col-form-label">Service</label>
                                        <div class="col-sm-4">
                                            <input type="text" class="form-control" id="department" name="department"
                                                value="{{ $user->department[0] ?? '' }}">
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <label for="telephonenumber" class="col-sm-2 col-form-label">Division</label>
                                        <div class="col-sm-4">
                                            <input type="text" class="form-control" id="division" name="division"
                                                value="{{ $user->division[0] ?? '' }}">
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <div class="float-end">
                                            <button type="submit"
                                                class="btn btn-danger">{{ __('validation.save') }}</button>
                                        </div>
                                    </div>
                                </form>
                                <!-- The timeline -->
                            </div>
                            <!-- Tableau Groupe-->
                            <div class="tab-pane fade" id="pills-groupes" role="tabpanel"
                                aria-labelledby="pills-groupes-tab">

                                <div class="row mb-3">
                                    <div class="col-sm-8">
                                        <div class="card">
                                            <div class="card-body">
                                                <table class="table">
                                                    <thead>
                                                        <tr>
                                                            <th scope="col">Groupe</th>
                                                            <th scope="col">Retirer</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach ($groupsuser as $groupuser)
                                                            <tr>
                                                                <td> <a href="{{ route('group.view', $groupuser->getDn()) }}"
                                                                        class="link-dark">{{ $groupuser->getName() }}</a>
                                                                </td>
                                                                <td>
                                                                    <form
                                                                        action="{{ route('user.rmgroup', $user->getDn()) }}"
                                                                        method="POST">
                                                                        @csrf
                                                                        @method('DELETE')
                                                                        <button type="submit"
                                                                            class="btn btn-warning btn-sm" name="dngroup"
                                                                            value="{{ $groupuser->getDn() }}"
                                                                            onclick='return confirm("Voulez-vous vraiment retirer ce groupe de cette utilisateur ?")'><i
                                                                                class="fa-solid fa-xmark"></i></button>
                                                                    </form>
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-4">
                                        <div class="card mb-3">
                                            <div class="card-header">
                                                <i class="fas fa-fw fa-users"></i> Ajouter un groupe
                                            </div>
                                            <div class="card-body">

                                                <form action="{{ route('user.addgroup', $user->getDn()) }}"
                                                    method="POST">
                                                    @csrf


                                                    <select name="dngroup" class="form-select mb-3 ">
                                                        @foreach ($allGroups as $group)
                                                            <option value="{{ $group->getDn() }}">
                                                                {{ $group->getName() }}
                                                            </option>
                                                        @endforeach
                                                    </select>

                                                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                                        <button class="btn btn-primary" type="submit">Ajouter</button>
                                                    </div>

                                                </form>
                                            </div>
                                        </div>
                                        <div class="card mb-3">
                                            <div class="card-header">
                                                <i class="fa-solid fa-users-between-lines"></i> Ajouter un
                                                service
                                            </div>
                                            <div class="card-body">

                                                <form action="{{ route('user.addgroup', $user->getDn()) }}"
                                                    method="POST">
                                                    @csrf


                                                    <select name="dngroup" class="form-select mb-3 ">
                                                        @foreach ($allServices as $service)
                                                            <option value="{{ $service->getDn() }}">
                                                                {{ $service->getName() }}
                                                            </option>
                                                        @endforeach
                                                    </select>

                                                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                                        <button class="btn btn-primary" type="submit">Ajouter</button>
                                                    </div>

                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>


                            </div>
                            <!-- Tableau répertoire -->
                            <div class="tab-pane fade" id="pills-dossier" role="tabpanel"
                                aria-labelledby="pills-dossier-tab">
                                <div class="alert alert-info" role="alert">
                                    <h5 class="alert-heading">Répertoire personnel</h5>
                                    <p>
                                        Le répertoire personnel sous Windows, également connu sous le nom de dossier
                                        utilisateur ou de profil utilisateur, est un espace dédié à chaque utilisateur sur
                                        le serveur de fichier.<br>
                                        il est à noter que la création initiale du répertoire personnel peut prendre un
                                        certain temps, parfois plus d'une heure.<br>
                                        Ce délai prolongé lors de la première activation est généralement une opération
                                        unique. Une fois que le répertoire personnel est créé, les activations et les
                                        processus suivants seront considérablement plus rapides et efficaces.
                                    <p>
                                </div>
                                <div class="row mb-3">
                                    <label for="objectguid" class="col-sm-2 col-form-label">Répertoire
                                        perso.</label>
                                    <div class="col-sm-8">
                                        @if (optional($user->homedrive)[0])
                                            <div class="input-group mb-3">
                                                <span class="input-group-text"
                                                    id="homedirectory">{{ optional($user->homedrive)[0] }}</span>
                                                <input type="text" class="form-control"
                                                    value="{{ optional($user->homedirectory)[0] }}" readonly>
                                                <a href="{{ route('user.repperso', $user->getDn()) }}"
                                                    class="btn btn-outline-secondary" type="submit">Désactiver</a>
                                            </div>
                                        @else
                                            <a href="{{ route('user.repperso', $user->getDn()) }}"
                                                class="btn btn-outline-secondary" type="submit">Activer le repértoire
                                                personnel</a>
                                        @endif

                                    </div>
                                </div>

                            </div>
                            <!-- Tableau StockManager -->
                            <div class="tab-pane fade" id="pills-stockmanager" role="tabpanel"
                                aria-labelledby="pills-dossier-tab">
                                @if (env('ACTIVE_STOCKM'))
                                    @if (isset($stockmanager))
                                        <div class="alert alert-info" role="alert">
                                            <h5 class="alert-heading">StockManager : Gestion du Stock</h5>
                                            <p>Les données présentées ci-dessous sont issues du StockManager, l'application
                                                de
                                                gestion
                                                de stock du Rectorat de Créteil. StockManager est l'unique moyen d'ajouter
                                                du
                                                matériel
                                                au stock de manière officielle.

                                                Il est impératif de noter que toutes les opérations d'ajout de matériel
                                                doivent
                                                être
                                                effectuées exclusivement via StockManager pour garantir l'intégrité et la
                                                conformité
                                                des
                                                données. Cela assure une traçabilité adéquate et contribue à maintenir le
                                                stock
                                                à
                                                jour
                                                et précis.

                                                La mise à jour régulière du stock est essentielle pour assurer la fiabilité
                                                des
                                                informations et pour répondre aux exigences de conformité en matière de
                                                gestion
                                                des
                                                stocks.
                                            <p>
                                        </div>
                                        <div class="table-responsive">
                                            <table class="table table-bordered table-hover">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th>Voir</th>
                                                        <th>Type</th>
                                                        <th>Marque</th>
                                                        <th>Modèle</th>
                                                        <th>Numéro de série</th>
                                                        <th>Adresse MAC</th>
                                                        <th>Nom Machine</th>
                                                        <th>Date d'installation</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($stockmanager as $dataType => $items)
                                                        @foreach ($items as $item)
                                                            <tr>
                                                                <td>
                                                                    <center><a
                                                                            href='{{ url('http://stockmanager.in.ac-creteil.fr/inventaire/view/' . lcfirst($dataType) . '/' . $item['id']) }}'
                                                                            target="_blank"><i
                                                                                class='fa fa-search-plus fa-fw'></i></a>
                                                                    </center>
                                                                </td>
                                                                <td>{{ $dataType }}</td>
                                                                <td>{{ $item['marque'] ?? '' }}</td>
                                                                <td>{{ $item['modele'] ?? '' }}</td>
                                                                <td>{{ $item['num_serie'] ?? '' }}</td>
                                                                <td>{{ $item['addr_mac'] ?? '' }}</td>
                                                                <td> {{ $item['nom_machine'] ?? '' }}</td>
                                                                <td class="text-center">{{ $item['date_install'] ?? '' }}
                                                                </td>
                                                                <!-- ... Ajoutez d'autres cellules en fonction des données ... -->
                                                            </tr>
                                                        @endforeach
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @else
                                        <div class="alert alert-warning" role="alert">
                                            Aucune donnée disponible.
                                        </div>
                                    @endif
                                @endif
                            </div>
                        </div>
                        <!-- /.tab-content -->
                    </div><!-- /.card-body -->
                </div>
                <!-- /.card -->

            </div>
            <!-- /.col -->
        </div>
        <!-- /.row -->

        <!-- Modal -->
        <div class="modal fade" id="ChangePasswdModal" tabindex="-1" aria-labelledby="ChangePasswdModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="ChangePasswdModalLabel">Changement de mot de passe</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-info" role="alert">
                            Si vous effectuez un changement de mot de passe en utilisant le menu de l'application,
                            veuillez
                            noter que ce nouveau mot de passe ne sera pas automatiquement synchronisé avec le
                            serveur LDAP
                            Académique.<br>
                            Afin de maintenir une cohérence entre le mot de passe AD et le serveur LDAP, une étape
                            de
                            resynchronisation est nécessaire via Identifiant.
                        </div>
                        <form autocomplete="off" action="{{ route('user.password', $user->getDn()) }}"
                            id="ChangePasswd-form" method="POST">
                            @csrf
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="radio" name="choixradio" id="exampleRadios1"
                                    value="aleatoire" checked>
                                <label class="form-check-label" for="exampleRadios1">
                                    Mot de passe aléatoire : {{ $ramdompass }}
                                </label>
                                <input type="text" class="form-control" id="" name="ramdompassword"
                                    autocomplete="off" value="{{ $ramdompass }}" hidden>
                            </div>
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="radio" name="choixradio" id="exampleRadios2"
                                    value="motdepasse">
                                <label class="form-check-label mb-3" for="exampleRadios2">
                                    Saisissez un mot de passe :
                                </label>
                                <div class="form-check-label">
                                    <div class="row mb-3">
                                        <label for="" class="col-sm-4 col-form-label">Mot de
                                            passe</label>
                                        <div class="col-sm-6">
                                            <input type="password" class="form-control" id="password" name="password"
                                                autocomplete="off">
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <label for="" class="col-sm-4 col-form-label">Confirmer le mot de
                                            passe</label>
                                        <div class="col-sm-6">
                                            <input type="password" class="form-control" id="confirm_password"
                                                name="confirm_password" autocomplete="off">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="radio" name="choixradio" id="exampleRadios3"
                                    value="datenaissance" disabled>
                                <label class="form-check-label" for="exampleRadios3">
                                    Mot de passe préformater selon l'utilisateur (RKJJMMAAAA$)<br>
                                    Format: Initial en majuscule, Date de naissance, caractère "$".
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="choixradio" id="exampleRadios4"
                                    value="aucun" disabled>
                                <label class="form-check-label" for="exampleRadios4">
                                    Ne pas renseigner de mot de passe
                                </label>
                            </div>

                        </form>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                        <button type="button" class="btn btn-primary"
                            onclick="event.preventDefault();
                    document.getElementById('ChangePasswd-form').submit();">Appliquer</button>
                    </div>
                </div>
            </div>
        </div>
    @else
        Aucun résultat pour ce profil d'utilisateur.
    @endif

@stop


@section('scriptjs')
    <script type="module">
        var path = "{{ route('user.autocomplete') }}";

        $('#Searchuser').typeahead({

            source: function(query, process) {
                return $.get(path, {
                    search: query
                }, function(data) {
                    return process(data);
                });
            }
        });

        // Stocker l'index de l'onglet actif lorsqu'il est changé
        const navPills = document.querySelector('.nav-pills');
        navPills.addEventListener('click', function(event) {
            if (event.target.classList.contains('nav-link')) {
                const activeIndex = Array.from(navPills.children).indexOf(event.target.parentNode);
                localStorage.setItem('activeTab', activeIndex.toString());
            }
        });

        // Restaurer l'index de l'onglet actif lors du chargement de la page
        window.addEventListener('load', function() {
            const activeIndex = localStorage.getItem('activeTab');
            if (activeIndex !== null) {
                navPills.children[parseInt(activeIndex)].querySelector('a').click();
            }
        });

        $(document).ready(function() {
            $('#btncheckldap').click(function() {
                var dataId = $(this).data('id');
                $.ajax({
                    url: '{{ route('user.checkldap') }}',
                    type: 'GET', // ou 'POST' selon votre besoin
                    dataType: 'html', // ou 'html', 'xml', etc. selon le type de données que vous attendez
                    data: {
                        q: dataId
                    },
                    success: function(data) {
                        $('#resultats_checkldap').html(
                            data); // Met à jour avec le contenu HTML de la réponse
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        console.error('Erreur lors de la requête Ajax :', textStatus);
                    }
                });
            });
        });

        document.getElementById('softphonie').addEventListener('change', function() {
            document.getElementById('softphonieForm').submit();
        });
    </script>
@endsection
