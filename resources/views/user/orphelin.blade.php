@extends('layouts.app')
@section('icon', 'fas fa-fw fa-users-slash')
@section('h1', 'Orphelins')

@section('content')

    <div class="row">
        <div class="col-md-12">
            <div class="card mb-3">
                <div class="card-header">
                    <a href="{{ route('user.index') }}" class="btn btn-outline-secondary"><i
                            class="fa-solid fa-chevron-left"></i></a>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-2">
        <div class="row mb-2">
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12">
                <div class="card bg-light mb-3">
                    <div class="card-header bg-info text-white">
                        <i class="fa fa-info-circle"></i> Utilisateurs Orphelins
                    </div>
                    <div class="card-body">
                        <p class="card-text">
                            Cette page vous permet de comparer la base annuaire de l'Active Directory avec celle du LDAP
                            Oracle.
                            La liste ci-dessous présente tous les utilisateurs de l'Active Directory qui n'ont pas/plus de
                            compte
                            LDAP Oracle associé.
                        </p>
                        <p class="card-text">
                            En règle générale, ces utilisateurs devraient être vérifiés et, si nécessaire, avoir des comptes
                            LDAP Oracle créés pour eux afin d'assurer un accès approprié aux ressources nécessaires.
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- end card -->

    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card mb-3">
                <div class="card-body d-flex justify-content-between align-items-center">

                    <button type="button" class="btn btn-warning" disabled>
                        <i class="fa-solid fa-user-lock"></i> Désactiver tout
                    </button>

                    <button type="button" class="btn btn-danger" disabled>
                        <i class="fa-solid fa-user-xmark"></i> Supprimer tout
                    </button>

                    <form role="form" action="{{ route('user.orphelin') }}" method="GET" autocomplete="off">

                        <button id="BoutonActualiser" type="submit" name="actualize" value="oui"
                            class="btn btn-primary"><i class="fa-solid fa-arrows-rotate"></i> Cliquez pour
                            actualiser</button>
                        <div id="spinner" class="d-none">
                            <button class="btn btn-primary" type="button" disabled>
                                <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                Loading...
                            </button>
                        </div>

                    </form>

                </div>
            </div>
        </div>
    </div>



    @if (count($listusers) > 0)
        <div class="row">
            <div class="col-12">

                <div class="card">

                    <!-- /.card-header -->
                    <div class="card-body">

                        <table id="listusers" class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th>UID</th>
                                    <th>Nom complet</th>
                                    <th>Adresse mail</th>
                                    <th>Service</th>
                                    <th>Organisation</th>
                                    <th>Type de compte</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($listusers as $value)
                                    <tr>
                                        <td>
                                            {{ $value->cn[0] }}
                                        </td>
                                        <td> {{ $value->displayName[0] ?? '' }} </td>
                                        <td> {{ $value->mail[0] ?? '' }} </td>
                                        <td> {{ $value->department[0] ?? '' }} </td>
                                        <td> {{ $value->getParentName() }} </td>
                                        <td> {{ $value->typeOfAccount[0] ?? '' }} </td>
                                        <td> <a href='{{ route('user.remove', $value->getDn()) }}'
                                                class='btn btn-danger btn-sm'
                                                onClick='return confirm("Êtes-vous sûr de vouloir supprimer cette utilisateur ? ")'><i
                                                    class='fa fa-trash' aria-hidden='true'></i></a>
                                            <a href="{{ route('user.view', $value->getDn()) }}"
                                                class='btn btn-primary btn-sm'><i
                                                    class="fa-solid fa-magnifying-glass"></i></a>
                                            @if ($value->isEnabled())
                                                <a href="{{ route('user.active', $value->getDn()) }}"
                                                    class='btn btn-warning btn-sm'><i class="fa-solid fa-user-lock"></i></a>
                                            @endif
                                        </td>

                                    </tr>
                                @endforeach
                            </tbody>

                        </table>

                    </div>
                    <!-- /.card-body -->
                </div>
                <!-- /.card -->
            </div>
            <!-- /.col -->

        </div>
        <!-- /.row -->
    @else
        <div class="alert alert-dark" role="alert">
            Aucun utilisateur trouvé en fin de fonction
        </div>
    @endif



@stop

@section('scriptjs')
    <script type="module">
        let table = $("#listusers").DataTable({
            responsive: true,
            lengthChange: false,
            autoWidth: false,
            select: true,
            buttons: ["copy", "csv", "excel", "pdf", "print"]
        });

        table.buttons().container().appendTo('#listusers_wrapper .col-md-6:eq(0)');


        document.getElementById('BoutonActualiser').addEventListener('click', function() {
            // Récupérer l'élément bouton et le spinner
            var bouton = document.getElementById('BoutonActualiser');
            var spinner = document.getElementById('spinner');

            // Cacher le bouton et afficher le spinner
            bouton.style.display = 'none';
            spinner.classList.remove('d-none');


        });
    </script>
@stop
