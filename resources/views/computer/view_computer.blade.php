@extends('layouts.app')
@section('icon', 'fas fa-fw fa-computer')
@section('h1', 'Vue de l\'ordinateur')

@section('css')

@stop

@section('content')

    <div class="row">
        <div class="col-md-12">
            <div class="card card-outline mb-3">
                <div class="card-body">
                    <div class="col-4">
                        <div class="input-group flex-column">
                            <form action="{{ route('computer.search', $computer->getDn()) }}" method="POST">
                                @csrf
                                <div class="input-group">
                                    <a href="{{ route('computer.index') }}" class="btn btn-outline-secondary"><i
                                            class="fa-solid fa-chevron-left"></i></a>

                                    <input type="text" class="typeahead form-control" id="SearchComputer" name="search"
                                        placeholder="Rechercher...">
                                    <button class="btn btn-outline-secondary" type="submit">Rechercher</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    @if ($computer)
        <div class="row">
            <div class="col-md-3">

                <!-- Profile Image -->
                <div class="card card-primary card-outline mb-3">
                    <div class="card-body">
                        <div class="text-center">
                            <h1> <i class="fa-solid fa-laptop"></i></h1>
                        </div>

                        <h3 class="profile-username text-center">{{ $computer->cn[0] }}</h3>

                        <ul class="list-group list-group-unbordered mb-3">
                            <li class="list-group-item">
                                <b>Hostname DNS</b> <a class="float-end">{{ $computer->dNSHostName[0] }}</a>
                            </li>
                            <li class="list-group-item">
                                <b>Dernière co.</b> <a class="float-end">{{ $computer->lastLogon }}</a>
                            </li>
                            <li class="list-group-item">
                                <b>Nombre de co.</b> <a class="float-end">{{ $computer->logonCount[0] }}</a>
                            </li>
                            <li class="list-group-item">
                                <b>Système d'exp.</b> <a class="float-end">{{ $computer->operatingSystem[0] }}</a>
                            </li>
                            <li class="list-group-item">
                                <b>Version</b> <a class="float-end">{{ $computer->operatingSystemVersion[0] }}</a>
                            </li>
                            <li class="list-group-item">
                                <b>Crée le</b> <a class="float-end">{{ $computer->whenCreated }}</a>
                            </li>
                        </ul>
                    </div>
                    <!-- /.card-body -->
                </div>
                <!-- /.card -->
            </div>



            <div class="col-md-9">
                @if ($bitlock->exists())
                    <div class="card mb-3">
                        <div class="card-header">
                            Bitlock
                        </div>
                        <div class="card-body">

                            <table class="table">
                                <tbody>
                                    <tr>
                                        <th scope="row" style=" vertical-align: middle;">Date & DN : </th>
                                        <td><input type="text" class="form-control" name="givenName" id="givenName"
                                                value="{{ $bitlock[0]['distinguishedname'][0] }}" autocomplete="off"
                                                readonly="readonly">
                                        </td>
                                    </tr>
                                    <tr>
                                        <th scope="row" style=" vertical-align: middle;">ID de mot de passe : </th>
                                        <td><input type="text" class="form-control" name="uid" id="uid"
                                                autocomplete="off"
                                                value="{{ bin2hex($bitlock[0]['msfve-recoveryguid'][0]) }}"
                                                readonly="readonly"></td>
                                    </tr>
                                    <tr>
                                        <th scope="row" style=" vertical-align: middle;">Mot de passe de récupération
                                        </th>
                                        <td>
                                            <textarea class="form-control" name="mail" id="mail" readonly="readonly">{{ $bitlock[0]['msfve-recoverypassword'][0] }}</textarea>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif
                @if (isset($stockmanager))
                    <div class="card mb-3">
                        <div class="card-body">
                            <div class="alert alert-info" role="alert">
                                <h5 class="alert-heading">StockManager : Gestion du Stock</h5>
                                <p>Les données présentées ci-dessous sont issues du StockManager, l'application de
                                    gestion
                                    de stock du Rectorat de Créteil. StockManager est l'unique moyen d'ajouter du
                                    matériel
                                    au stock de manière officielle.

                                    Il est impératif de noter que toutes les opérations d'ajout de matériel doivent
                                    être
                                    effectuées exclusivement via StockManager pour garantir l'intégrité et la
                                    conformité
                                    des
                                    données. Cela assure une traçabilité adéquate et contribue à maintenir le stock
                                    à
                                    jour
                                    et précis.

                                    La mise à jour régulière du stock est essentielle pour assurer la fiabilité des
                                    informations et pour répondre aux exigences de conformité en matière de gestion
                                    des
                                    stocks.
                                <p>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Voir</th>
                                            <th>UID</th>
                                            <th>Nom Prénom</th>
                                            <th>Service</th>
                                            <th>Numéro de série</th>
                                            <th>Adresse MAC</th>
                                            <th>Date d'installation</th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                        <tr>
                                            <td>
                                                <center><a
                                                        href='{{ url('http://stockmanager.in.ac-creteil.fr/inventaire/view/ordinateur/' . $stockmanager['id']) }}'
                                                        target="_blank"><i class='fa fa-search-plus fa-fw'></i></a>
                                                </center>
                                            </td>
                                            <td>{{ $stockmanager['benef_uid'] }}</td>
                                            <td>{{ $stockmanager['benef_name'] }}</td>
                                            <td>{{ $stockmanager['service'] }}</td>
                                            <td>{{ $stockmanager['num_serie'] }}</td>
                                            <td>{{ $stockmanager['addr_mac'] }}</td>
                                            <td class="text-center">{{ $stockmanager['date_install'] }}</td>
                                            <!-- ... Ajoutez d'autres cellules en fonction des données ... -->
                                        </tr>

                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="alert alert-warning" role="alert">
                        Aucune donnée disponible.
                    </div>
                @endif
            </div>
        </div>
    @else
        Aucun résultat pour ce profil d'ordinateur.
    @endif

@stop


@section('scriptjs')

    <script type="module">
        var path = "{{ route('computer.autocomplete') }}";

        $('#SearchComputer').typeahead({

            source: function(query, process) {
                return $.get(path, {
                    search: query
                }, function(data) {
                    return process(data);
                });
            }
        });
    </script>

@endsection
