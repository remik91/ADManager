@extends('layouts.app')
@section('icon', 'fas fa-fw fa-user')
@section('h1', 'Corbeille')

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
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12">
            <div class="card bg-light mb-3">
                <div class="card-header bg-info text-white">
                    <i class="fa fa-info-circle"></i> Informations
                </div>
                <div class="card-body">
                    <p class="card-text">
                        Lorsqu'un utilisateur est supprimé d'un Active Directory, ses informations ne disparaissent pas
                        instantanément. Au lieu de cela, ils sont déplacés vers la corbeille de l'Active Directory,
                        également connue sous le nom de conteneur "Corbeille" ou "Recycle Bin".<br>

                        Cette fonctionnalité vous permet de restaurer un utilisateur et de le réintégrer dans la base de
                        données de l'Active Directory.
                    </p>
                </div>
            </div>
        </div>
        <!-- end card -->

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
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($listusers as $value)
                                    <tr>
                                        <td>
                                            {{ $value->cn[0] }}
                                        </td>
                                        <td> {{ $value->displayName[0] }} </td>
                                        <td> {{ $value->mail[0] }} </td>
                                        <td><a href='{{ route('user.trash', $value->getDn()) }}'
                                                class='btn btn-warning btn-sm'
                                                onClick='return confirm("Êtes-vous sûr de vouloir restaurer cette utilisateur ?")'>Restaurer</a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th>UID</th>
                                    <th>Nom complet</th>
                                    <th>Adresse mail</th>
                                    <th>Action</th>
                                </tr>
                            </tfoot>
                        </table>

                    </div>
                    <!-- /.card-body -->
                </div>
                <!-- /.card -->
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
    </script>
@stop
