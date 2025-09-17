@extends('layouts.app')
@section('icon', 'fas fa-fw fa-computer')
@section('h1', 'Ordinateurs')

@section('content')

    <div class="row">

        <div class="col-md-12">
            <div class="card mb-3">
                <div class="card-header text-end">
                    <div class="ml-auto">
                        <a class="btn btn-outline-secondary my-auto m-lg-1" href="{{ route('computer.bitlocker') }}"><i
                                class="fa-solid fa-key"></i> Bitlocker</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row mb-3">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body text-center">

                    <form role="form" action="{{ route('computer.index') }}" method="GET" autocomplete="off">
                        @csrf
                        <div class="row">
                            <div class="col-md-12">
                                <div class="row">
                                    <div class="col-3">
                                        <div class="form-group mb-3">
                                            <label>Service:</label>
                                            <select class="form-select" data-placeholder="Any" style="width: 100%;"
                                                disabled>
                                                <option>Tout</option>
                                                <option>Images</option>
                                                <option>Video</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="form-group mb-3">
                                            <label>Groupe:</label>
                                            <select class="form-select" style="width: 100%;" disabled>
                                                <option selected>Tout</option>
                                                <option>DESC</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="form-group mb-3">
                                            <label>Système d'exploitation:</label>
                                            <select class="form-select" style="width: 100%;" name="search_sysexp">
                                                <option value="" selected="">Tout</option>
                                                <option value="10" @if ($sysexp == '10') selected @endif>
                                                    Windows 10</option>
                                                <option value="11" @if ($sysexp == '11') selected @endif>
                                                    Windows 11</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="form-group mb-3">
                                            <label>Organisation:</label>
                                            <select class="form-select" name="search_ou" style="width: 100%;">
                                                @foreach ($ouList as $ou)
                                                    <option value="{{ $ou->getDn() }}">{{ $ou->getName() }}</option>
                                                @endforeach
                                                <option value="{{ $ouComputers->distinguishedname[0] }}">
                                                    {{ $ouComputers->name[0] }}
                                                </option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="input-group mb-3">
                                    <input type="text" class="form-control" name="search_sAM" minlength="2"
                                        value="{{ $searchText }}" placeholder="Recherche d'ordinateur">
                                    <button class="btn btn-outline-secondary" type="submit" id="button-addon2"><i
                                            class="fas fa-search"></i></button>

                                </div>


                            </div>

                        </div>
                    </form>

                </div>

            </div>

        </div>

    </div>
    @if ($listcomputers->count() >= 1000)
        <div class="alert alert-warning" role="alert">
            Le nombre de résultat est trop élévé (Maximum 1000). Affinez vos résultats à l'aide de la
            recherche.
        </div>
    @endif
    @if ($listcomputers->count() > 0)
        <div class="row mb-3">
            <div class="col-12">

                <div class="card">

                    <!-- /.card-header -->
                    <div class="card-body">

                        <table id="listcomputers" class="table table-bordered table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th>Nom de l'ordinateur</th>
                                    <th>Système d'exploitation</th>
                                    <th>Appartenance</th>
                                    <th>Description</th>
                                    <th>Statut</th>
                                    <th>Crée le</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($listcomputers as $value)
                                    <tr>
                                        <td>
                                            <a href="{{ url('/computer/view/' . $value->getDn()) }}"><i
                                                    class="fa-solid fa-computer"></i> {{ $value->cn[0] }}</a>
                                        </td>
                                        <td> {{ $value->operatingsystem[0] }} | {{ $value->operatingSystemVersion[0] }}
                                        </td>
                                        <td> {{ $value->getParentName() }} </td>
                                        <td> {{ $value->department[0] ?? '' }} </td>
                                        <td>
                                            @if ($value->userAccountControl[0] == 4096)
                                                <span class="badge bg-success">Enrôlé</span>
                                            @else
                                                <span class="badge bg-warning text-dark">Problème</span>
                                            @endif
                                        </td>
                                        <td> {{ $value->whencreated }} </td>
                                        <td> <a href='{{ route('computer.remove', ['dn' => $value->getDn()]) }}'
                                                class='btn btn-danger btn-sm'
                                                onClick='return confirm("Êtes-vous sûr de vouloir supprimer cette ordinateur ?")'><i
                                                    class='fa fa-trash' aria-hidden='true'></i></a>
                                            <button type="button" class="btn btn-warning btn-sm" data-bs-toggle="modal"
                                                data-bs-target="#exampleModal"
                                                onclick="MoveComputer('{{ $value->cn[0] }}','{{ $value->getDn() }}')">
                                                <i class='fa fa-dolly' aria-hidden='true'></i></button>
                                            <a href="{{ url('/computer/view/' . $value->getDn()) }}"
                                                class='btn btn-primary btn-sm'><i
                                                    class="fa-solid fa-magnifying-glass"></i></a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th>Nom de l'ordinateur</th>
                                    <th>Système d'exploitation</th>
                                    <th>Appartenance</th>
                                    <th>Description</th>
                                    <th>Statut</th>
                                    <th>Crée le</th>
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
        </div>
        <!-- /.row -->
    @else
        {{-- <p>Aucun ordinateur trouvé.</p> --}}
    @endif

    <!-- Modal -->
    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form autocomplete="off" action="{{ route('computer.migrate') }}" id="FormChangeOU" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="exampleModalLabel">Changement d'unité d'organisation</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div id="namecomputer"></div>
                        <div class="form-group">
                            <input type="text" name="dncomputer" id="dncomputer" value="" hidden>
                            <label for="givenname" class="col-sm-12 col-form-label">Selectionner la nouvelle unité
                                d'organisation pour l'ordinateur:</label>
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
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Déplacer</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="accordion" id="versionsCollapse">
        <div class="card">
            <div class="card-header bg-info text-white" id="versionsHeading">
                <h3 class="mb-0">
                    <button class="btn text-white" type="button" data-bs-toggle="collapse"
                        data-bs-target="#versionsContent" aria-expanded="true" aria-controls="versionsContent"> <i
                            class="fa fa-info-circle"></i>
                        Versions de Windows 10
                    </button>
                </h3>
            </div>

            <div id="versionsContent" class="collapse" aria-labelledby="versionsHeading"
                data-bs-parent="#versionsCollapse">
                <div class="card-body bg-info">
                    <div class="alert alert-info">
                        <h4>Versions de Windows 10</h4>
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Numéro de version</th>
                                    <th>Nom de version</th>
                                    <th>Nom de code</th>
                                    <th>Date de sortie</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>10.0.18362</td>
                                    <td>May 2019 Update</td>
                                    <td>19H1</td>
                                    <td>21 mai 2019</td>
                                </tr>
                                <tr>
                                    <td>10.0.18363</td>
                                    <td>November 2019 Update</td>
                                    <td>19H2</td>
                                    <td>12 novembre 2019</td>
                                </tr>
                                <tr>
                                    <td>10.0.19041</td>
                                    <td>May 2020 Update</td>
                                    <td>20H1</td>
                                    <td>27 mai 2020</td>
                                </tr>
                                <tr>
                                    <td>10.0.19042</td>
                                    <td>October 2020 Update</td>
                                    <td>20H2</td>
                                    <td>20 octobre 2020</td>
                                </tr>
                                <tr>
                                    <td>10.0.19043</td>
                                    <td>May 2021 Update</td>
                                    <td>21H1</td>
                                    <td>18 mai 2021</td>
                                </tr>
                                <tr>
                                    <td>10.0.19044</td>
                                    <td>November 2021 Update</td>
                                    <td>21H2</td>
                                    <td>16 novembre 2021</td>
                                </tr>
                                <tr>
                                    <td>10.0.19045</td>
                                    <td>October 2022 Update</td>
                                    <td>22H2</td>
                                    <td>À venir</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div>
    </div>

@stop

@section('scriptjs')
    <script>
        function MoveComputer(name, dncomput) {
            document.getElementById("dncomputer").value = dncomput;
            document.getElementById("namecomputer").innerHTML = "<h6><p class='text-center'>" + name + "</p></h6>";

        }

        let table = $("#listcomputers").DataTable({
            responsive: true,
            lengthChange: false,
            autoWidth: false,
        });
    </script>
@stop
