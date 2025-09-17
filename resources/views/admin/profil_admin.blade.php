@extends('layouts.app')
@section('icon', 'fas fa-columns')
@section('h1', 'Profil utilisateur')

@section('content')

    <div class="row g-4">
        <div class="col-xl-3 col-lg-3 col-md-12 col-sm-12 col-12">
            <div class="card h-100">
                <div class="card-body">
                    <div class="account-settings">
                        <div class="user-profile text-center">
                            <div class="user-avatar">
                                <img src="{{ asset('images/avatars/avatar_' . $user->avatar . '.png') }}"
                                    class="img-fluid rounded-circle" alt="Avatar">
                            </div>
                            <h5 class="user-name">{{ $user->name }}</h5>
                            <h6 class="user-email">{{ $user->email }}</h6>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-9 col-lg-9 col-md-12 col-sm-12 col-12">

            <div class="card h-100">
                <div class="card-body">

                    @if ($user->domain == 'AD')
                        <div class="alert alert-primary" role="alert">
                            <p>
                                Attention: Votre compte est synchronisé depuis un LDAP. Toutes modifications des
                                informations personnelles ou du mot de passe seront écrasées à chaque connexion.</p>
                        </div>
                    @endif
                    <form class="row g-3" enctype="multipart/form-data" action="{{ route('admin.updateprofil') }}"
                        method="post">
                        @csrf
                        <div class="col-12">
                            <h6 class="mb-2 text-primary">Détails personnels</h6>
                        </div>
                        <input type="text" id="id" name="id" value="{{ $user->id }}" hidden>
                        <div class="col-md-6">
                            <label for="name" class="form-label">Nom complet</label>
                            <input type="text" class="form-control" id="name" name="name"
                                value="{{ $user->name }}" placeholder="Entrer un nom complet">
                        </div>
                        <div class="col-md-6">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" value="{{ $user->email }}"
                                placeholder="Entrer une adresse mail">
                        </div>
                        <div class="col-md-6">
                            <label for="username" class="form-label">Nom d'utilisateur (UID)</label>
                            <input type="text" class="form-control" id="username" name="username"
                                value="{{ $user->username }}" disabled>
                        </div>

                        <div class="col-12">
                            <h6 class="mt-3 mb-2 text-primary">Rôles & Avatar</h6>
                        </div>

                        <div class="col-md-6">
                            <label for="edit_avatar" class="form-label">Avatar</label>
                            <select name="avatar" id="edit_avatar" class="form-select" required>
                                <option value='1'>Avatar 1</option>
                                <option value='2'>Avatar 2</option>
                                <option value='3'>Avatar 3</option>
                                <option value='4'>Avatar 4</option>
                            </select>
                        </div>

                        <div class="col-12">
                            <h6 class="mt-3 mb-2 text-primary">Sécurité</h6>
                        </div>

                        <div class="col-md-6">
                            <label for="password" class="form-label">Mot de passe</label>
                            <input type="password" class="form-control" id="password" name="password">
                        </div>
                        <div class="col-md-6">
                            <label for="confirm_password" class="form-label">Confirmer le mot de passe</label>
                            <input type="password" class="form-control" id="confirm_password">
                        </div>
                        <div class="col-12 text-end">
                            <button type="submit" id="submit" class="btn btn-primary">Mettre à jour</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

@endsection
