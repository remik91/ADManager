@extends('layouts.app')
@section('icon', 'fa-solid fa-users-between-lines')
@section('h1', 'Gestion des services')




@section('content')

    <div class="row mb-2">
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12">
            <div class="card bg-light mb-3">
                <div class="card-header bg-info text-white">
                    <i class="fa fa-info-circle"></i> Informations
                </div>
                <div class="card-body">
                    <p class="card-text">
                        Dans la méthode AGDLP, les groupes de service (ou groupes globaux) jouent un rôle crucial dans la
                        gestion des droits d'accès au sein d'un environnement Windows Active Directory.<br>

                        Les groupes de service sont créés dans le but de regrouper des utilisateurs ou d'autres groupes qui
                        ont des besoins d'accès similaires à des ressources spécifiques au sein de l'entreprise. Ces groupes
                        permettent d'appliquer des autorisations de manière cohérente et efficace.
                    </p>
                </div>
            </div>
        </div>
        <!-- end card -->

    </div>

    <div class="row mb-3">
        <div class="col-md-5">
            <div class="card">
                <div class="card-body text-center">

                    <form role="form" action="{{ route('service.index') }}" method="GET" autocomplete="off">
                        @csrf

                        <div class="row">
                            <div class="col-8">
                                <div class="form-group mb-3">
                                    <label class="form-label">Organisation:</label>
                                    <select class="form-select" name="search_ou" style="width: 100%;">
                                        @foreach ($ouList as $ou)
                                            <option value="{{ $ou->getDn() }}"
                                                {{ $ou->getDn() == $selectedOu ? 'selected' : '' }}>
                                                {{ $ou->getName() }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-auto">
                                <label class="form-label">Action</label>
                                <button class="btn btn-outline-secondary form-control" type="submit"
                                    id="button-addon2">Filtre</button>
                            </div>
                        </div>

                    </form>

                </div>

            </div>

        </div>

        <div class="col-md-7">
            <div class="card">
                <div class="card-body mb-3">
                    <form role="form" action="{{ route('service.create') }}" method="POST" autocomplete="off"
                        class="row g-3">
                        @csrf
                        <div class="col-md-4">
                            <label for="nomGroupe" class="form-label">Création du service</label>
                            <input type="text" class="form-control" id="nomGroupe" name="nomGroupe"
                                placeholder="Nom du service (GGdpt_)" required>
                        </div>
                        <div class="col-md-4">
                            <label for="description" class="form-label">Description du service</label>
                            <input class="form-control" id="description" name="description" placeholder="Description">
                        </div>
                        <div class="col-md-2">
                            <label for="description" class="form-label">Organisation</label>
                            <select class="form-select" name="OuGroupe" style="width: 100%;">
                                @foreach ($ouList as $ou)
                                    <option value="{{ $ou->getName() }}" {{ $ou->getDn() == $selectedOu ? 'selected' : '' }}
                                        @if ($ou->getName() == 'Services') disabled @endif>
                                        {{ $ou->getName() }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary">Créer</button>
                        </div>

                    </form>

                </div>

            </div>

        </div>

    </div>

    @if ($listgroup->count() > 0)
        <div class="row">
            <div class="col-12">

                <div class="card">

                    <!-- /.card-header -->
                    <div class="card-body">

                        <table id="listusers" class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th>Nom</th>
                                    <th>Description</th>
                                    <th>Membres</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($listgroup as $value)
                                    <tr>
                                        <td>
                                            <a href="{{ url($url . $value->getDn()) }}">{{ $value->cn[0] }}</a>
                                        </td>
                                        <td> {{ $value->description[0] ?? '' }} </td>
                                        <td> {{ $value->members()->get()->count() }} </td>
                                        <td>
                                            <div class="text-center">
                                                <form action="{{ route('group.remove', ['dn' => $value->getDn()]) }}"
                                                    method="POST">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger"><i
                                                            class="fa-solid fa-trash-alt"
                                                            onclick='return confirm("Attention : Voulez-vous vraiment supprimer ce groupe ? Cette opération est irréversible.")'></i></button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th>Nom</th>
                                    <th>Description</th>
                                    <th>Membres</th>
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


                <div id="resultats_shortview"></div>

            </div>


        </div>
        <!-- /.row -->
    @else
        {{-- <p>Aucun utilisateur trouvé.</p> --}}
    @endif


@endsection

@section('scriptjs')
    <script type="module">
        let table = $("#listusers").DataTable({
            responsive: true,
            lengthChange: true,
            autoWidth: false,
            buttons: ["copy", "csv", "excel", "pdf", "print"]
        });
    </script>
@stop
