@extends('layouts.app')
@section('icon', 'fas fa-fw fa-computer')
@section('h1', "Vue de l'ordinateur")

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card card-outline mb-3">
                <div class="card-body">
                    <div class="col-12 col-lg-6">
                        <div class="input-group flex-column">
                            <form action="{{ route('computer.search') }}" method="POST"> @csrf
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
                <div class="card card-primary card-outline mb-3">
                    <div class="card-body">
                        <div class="text-center">
                            <h1><i class="fa-solid fa-laptop"></i></h1>
                        </div>
                        <h3 class="profile-username text-center">{{ $computer->cn[0] }}</h3>
                        <ul class="list-group list-group-unbordered mb-3">
                            <li class="list-group-item"><b>Hostname DNS</b> <span
                                    class="float-end">{{ $computer->dNSHostName[0] ?? '—' }}</span></li>
                            <li class="list-group-item"><b>Dernière co.</b> <span
                                    class="float-end">{{ $computer->lastLogon ?? '—' }}</span></li>
                            <li class="list-group-item"><b>Nombre de co.</b> <span
                                    class="float-end">{{ $computer->logonCount[0] ?? '—' }}</span></li>
                            <li class="list-group-item"><b>Système d'exp.</b> <span
                                    class="float-end">{{ $computer->operatingSystem[0] ?? '—' }}</span></li>
                            <li class="list-group-item"><b>Version</b> <span
                                    class="float-end">{{ $computer->operatingSystemVersion[0] ?? '—' }}</span></li>
                            <li class="list-group-item"><b>Créé le</b> <span
                                    class="float-end">{{ $computer->whenCreated ?? '—' }}</span></li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="col-md-9">
                {{-- BitLocker: plusieurs objets --}}
                @if (isset($bitlocks) && $bitlocks->count() > 0)
                    <div class="card mb-3">
                        <div class="card-header d-flex justify-content-between">
                            <span>BitLocker</span>
                            <a class="btn btn-sm btn-outline-secondary"
                                href="{{ route('computer.bitlocker', ['search' => $computer->cn[0]]) }}" target="_blank">
                                <i class="fa-solid fa-up-right-from-square me-1"></i> Voir dans la liste
                            </a>
                            <a class="btn btn-sm btn-outline-secondary"
                                href="{{ route('computer.bitlocker', ['search' => $computer->cn[0]]) }}">
                                Rechercher toutes les clés de cette machine
                            </a>

                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th>DN</th>
                                            <th>GUID</th>
                                            <th>Clé de récupération</th>
                                            <th>Date</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($bitlocks as $b)
                                            @php
                                                $dn = $b->getDn();
                                                preg_match('/\{(.+?)\}/', $b->getName(), $m);
                                                $guid = $m[1] ?? null;
                                                $key = $b['msfve-recoverypassword'][0] ?? null;
                                            @endphp
                                            <tr>
                                                <td class="text-truncate" style="max-width:420px">
                                                    <code>{{ $dn }}</code>
                                                </td>
                                                <td><code>{{ $guid ?? '—' }}</code></td>
                                                <td>
                                                    @if ($key)
                                                        <div class="d-flex align-items-center gap-2">
                                                            <span class="user-select-all">{{ $key }}</span>
                                                            <button class="btn btn-sm btn-outline-secondary"
                                                                onclick="navigator.clipboard.writeText('{{ $key }}'); toastr.success('Clé copiée');"><i
                                                                    class="fa-regular fa-copy"></i></button>
                                                        </div>
                                                    @else
                                                        —
                                                    @endif
                                                </td>
                                                <td>{{ $b->whencreated ?? '—' }}</td>
                                                <td>
                                                    @if ($guid)
                                                        <a class="btn btn-sm btn-outline-primary"
                                                            href="{{ route('computer.bitlocker', ['search' => $guid]) }}">Rechercher
                                                            ce GUID</a>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                @endif

                {{-- StockManager --}}
                @if (!empty($stockmanager))
                    <div class="card mb-3">
                        <div class="card-body">
                            <div class="alert alert-info" role="alert">
                                <h5 class="alert-heading">StockManager : Gestion du Stock</h5>
                                <p class="mb-0">Données informatives issues de StockManager.</p>
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
                                            <td class="text-center"><a
                                                    href='{{ url('https://stockmanager.in.ac-creteil.fr/inventaire/view/ordinateur/' . $stockmanager['id']) }}'
                                                    target="_blank"><i class='fa fa-search-plus fa-fw'></i></a></td>
                                            <td>{{ $stockmanager['benef_uid'] ?? '—' }}</td>
                                            <td>{{ $stockmanager['benef_name'] ?? '—' }}</td>
                                            <td>{{ $stockmanager['service'] ?? '—' }}</td>
                                            <td>{{ $stockmanager['num_serie'] ?? '—' }}</td>
                                            <td>{{ $stockmanager['addr_mac'] ?? '—' }}</td>
                                            <td class="text-center">{{ $stockmanager['date_install'] ?? '—' }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="alert alert-warning" role="alert">Aucune donnée StockManager disponible.</div>
                @endif
            </div>
        </div>
    @else
        Aucun résultat pour ce profil d'ordinateur.
    @endif
@endsection

@section('scriptjs')
    <script type="module">
        const path = "{{ route('computer.autocomplete') }}";
        $('#SearchComputer').typeahead({
            source: function(query, process) {
                return $.get(path, {
                    search: query
                }, function(data) {
                    return process(data);
                });
            },
            delay: 200,
            minLength: 2
        });
    </script>
@endsection
