@extends('layouts.app')
@section('icon', 'fas fa-fw fa-house')
@section('h1', 'Accueil')
@section('content')


    <div class="row">


        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12">

            <div class="card mb-3">

                <!-- end card-header -->

                <div class="card-header bg-dark text-white">
                    Informations
                </div>
                <!-- end card-body -->
                <div class="card-body">
                    <h5>Bienvenue sur la nouvelle version de ADMANAGER pour l'Académie de Créteil</h5>
                    <p>Nous sommes ravis de vous accueillir sur la toute nouvelle version de ADMANAGER, l'outil de gestion
                        de l'Active Directory dédié à l'Académie de Créteil. Cette mise à jour représente un pas en avant
                        significatif dans notre engagement à vous offrir une expérience utilisateur exceptionnelle et des
                        fonctionnalités encore plus puissantes pour simplifier la gestion de vos comptes et ressources.</p>

                </div>

            </div>
            <!-- end card -->

        </div>

        <div class="col-xl-3 col-md-6">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>{{ $recentUsers }}</h3>
                    <p> Utilisateurs récemment ajoutés</p>
                </div>
                <div class="icon">
                    <i class="fa-solid fa-user-plus"></i>
                </div>
                <a href="{{ route('user.import') }}" class="small-box-footer"> Plus d'info <i
                        class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3>{{ $inactiveUsers }}</h3>
                    <p>Utilisateurs inactifs (depuis 90 jours)</p>
                </div>
                <div class="icon">
                    <i class="fa-solid fa-user-slash"></i>
                </div>
                <a href="{{ route('user.index') }}" class="small-box-footer"> Plus d'info <i
                        class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="small-box bg-primary text-white">
                <div class="inner">
                    <h3>{{ $totalGroups }}</h3>
                    <p>Nombre total de groupes</p>
                </div>
                <div class="icon">
                    <i class="fa-solid fa-people-group"></i>
                </div>
                <a href="{{ route('group.index') }}" class="small-box-footer"> Plus d'info <i
                        class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="small-box bg-danger  text-white">
                <div class="inner">
                    <h3>{{ $totalTrash }}</h3>
                    <p>Utilisateurs dans la corbeille</p>
                </div>
                <div class="icon">
                    <i class="fa-solid fa-trash-can"></i>
                </div>
                <a href="{{ route('user.trash') }}" class="small-box-footer"> Plus d'info <i
                        class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>

    </div>


@endsection
