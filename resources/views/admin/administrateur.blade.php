@extends('layouts.app')
@section('icon', 'fas fa-columns')
@section('h1', 'Gestion des utilisateurs')

@section('content')

    <div class="row">

        <div class="col-md-4">

            <div class="card mb-3 shadow-sm">
                <div class="card-header">
                    <i class="fas fa-user-plus"></i> Ajout d'un utilisateur
                </div>
                <div class="card-body">
                    <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <a class="nav-link active" id="pills-import-tab" data-bs-toggle="pill" href="#pills-import"
                                role="tab" aria-controls="pills-import" aria-selected="true">Importation depuis LDAP</a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a class="nav-link" id="pills-local-tab" data-bs-toggle="pill" href="#pills-local"
                                role="tab" aria-controls="pills-local" aria-selected="false">Utilisateur local</a>
                        </li>
                    </ul>
                    <div class="tab-content" id="pills-tabContent">
                        <div class="tab-pane fade show active" id="pills-import" role="tabpanel"
                            aria-labelledby="pills-import-tab">
                            <form enctype="multipart/form-data" action="{{ route('admin.store') }}" method="POST">
                                @csrf
                                <div class="mb-3">
                                    <label for="username" class="form-label">Nom d'utilisateur (UID LDAP)</label>
                                    <input type="text" class="form-control basicAutoComplete" name="username"
                                        autocomplete="off" id="username" required>
                                </div>
                                <div class="d-flex justify-content-end">
                                    <button type="submit" id="Save" class="btn btn-primary me-2">Ajouter</button>
                                    <button type="reset" class="btn btn-secondary">Annuler</button>
                                </div>
                            </form>
                        </div>
                        <div class="tab-pane fade" id="pills-local" role="tabpanel" aria-labelledby="pills-local-tab">
                            <form enctype="multipart/form-data" action="{{ route('admin.storelocal') }}" method="POST">
                                @csrf
                                <div class="alert alert-primary" role="alert">
                                    Attention, DNSMANAGER priorise les connexions via LDAP. Si le compte local que
                                    vous souhaitez créer contient le même UID qu'un compte LDAP, il sera
                                    automatiquement synchronisé.
                                </div>
                                <div class="mb-3">
                                    <label for="username" class="form-label">Nom d'utilisateur (UID)</label>
                                    <input type="text" class="form-control" name="username" autocomplete="off"
                                        id="username" required>
                                </div>
                                <div class="mb-3">
                                    <label for="name" class="form-label">Prénom et Nom</label>
                                    <input type="text" class="form-control" name="name" autocomplete="off"
                                        id="name" required>
                                </div>
                                <div class="mb-3">
                                    <label for="mail" class="form-label">Adresse mail</label>
                                    <input type="email" class="form-control" name="mail" autocomplete="off"
                                        id="mail" required>
                                </div>
                                <div class="mb-3">
                                    <label for="password" class="form-label">Mot de passe</label>
                                    <input type="password" class="form-control" name="password" autocomplete="off"
                                        id="password" required>
                                </div>
                                <div class="mb-3">
                                    <label for="confirm_password" class="form-label">Confirmer le mot de
                                        passe</label>
                                    <input type="password" class="form-control" name="confirm_password" autocomplete="off"
                                        id="confirm_password" required>
                                </div>
                                <div class="d-flex justify-content-end">
                                    <button type="submit" id="Save" class="btn btn-primary me-2">Créer</button>
                                    <button type="reset" class="btn btn-secondary">Annuler</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <div class="col-md-8">
            <table class="table table-bordered table-light align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>ID.</th>
                        <th>Compte autorisé</th>
                        <th>Type</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody class="">
                    @foreach ($user as $value)
                        <tr>
                            <td>
                                <div class="d-flex justify-content-center"> {{ $value->id }} </div>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <img alt='image' style='max-width:40px; height:auto;'
                                        src='{{ asset('images/avatars/avatar_' . $value->avatar . '.png') }}'>
                                    <div class="ms-2">
                                        <strong>{{ $value->name }} ({{ $value->username }})</strong><br>
                                        <small>{{ $value->email }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="d-flex justify-content-center">
                                    @if ($value->domain == 'AD')
                                        AD
                                    @else
                                        Local
                                    @endif
                                </div>
                            </td>
                            <td class="">
                                <div class="d-flex justify-content-center">
                                    <a href='{{ url('admin/destroy/' . $value->id) }}' class='btn btn-danger btn-sm'
                                        onclick='return confirm("Voulez-vous vraiment supprimer cet utilisateur ?")'>
                                        <i class='fas fa-trash'></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

        </div>

    </div>



@endsection
