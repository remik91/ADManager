@extends('layouts.app')
@section('icon', 'fas fa-fw fa-computer')
@section('h1', 'Ordinateurs')

@section('content')
    <div class="row mb-3">
        <div class="col-md-12">
            <div class="card mb-3 shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div class="fw-bold">Filtres</div>
                    <div class="ml-auto">
                        <a class="btn btn-outline-secondary my-auto m-lg-1" href="{{ route('computer.bitlocker') }}">
                            <i class="fa-solid fa-key"></i> BitLocker
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <form role="form" action="{{ route('computer.index') }}" method="GET" autocomplete="off"
                        class="row g-3">
                        @csrf
                        <div class="col-12 col-lg-3">
                            <label class="form-label">Organisation</label>
                            <select class="form-select" name="search_ou">
                                @foreach ($ouList as $ou)
                                    <option value="{{ $ou->getDn() }}" @selected($selectedOu === $ou->getDn())>{{ $ou->getName() }}
                                    </option>
                                @endforeach
                                @if ($ouComputers)
                                    <option value="{{ $ouComputers->distinguishedname[0] }}" @selected($selectedOu === $ouComputers->distinguishedname[0])>
                                        {{ $ouComputers->name[0] }}
                                    </option>
                                @endif
                            </select>
                        </div>
                        <div class="col-6 col-lg-3">
                            <label class="form-label">Système d'exploitation</label>
                            <select class="form-select" name="search_sysexp">
                                <option value="" @selected($sysexp === '')>Tout</option>
                                <option value="10" @selected($sysexp === '10')>Windows 10</option>
                                <option value="11" @selected($sysexp === '11')>Windows 11</option>
                            </select>
                        </div>
                        <div class="col-6 col-lg-3">
                            <label class="form-label">Affichage</label>
                            <select class="form-select" name="per_page">
                                @foreach ([25, 50, 100, 150, 200] as $pp)
                                    <option value="{{ $pp }}" @selected(($perPage ?? 50) == $pp)>{{ $pp }} par
                                        page</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12 col-lg-3">
                            <label class="form-label">Recherche</label>
                            <div class="input-group">
                                <input type="text" class="form-control" name="search_sAM" minlength="2"
                                    value="{{ $searchText }}" placeholder="Nom d'ordinateur">
                                <button class="btn btn-primary" type="submit"><i class="fas fa-search"></i></button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @if ($listcomputers->count() > 0)
        <div class="row mb-3">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="listcomputers" class="table table-bordered table-hover align-middle">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Nom</th>
                                        <th>OS</th>
                                        <th>Appartenance</th>
                                        <th>Description</th>
                                        <th>Statut</th>
                                        <th>Créé le</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($listcomputers as $value)
                                        <tr>
                                            <td>
                                                <a href="{{ url('/computer/view/' . $value->getDn()) }}">
                                                    <i class="fa-solid fa-computer"></i> {{ $value->cn[0] }}
                                                </a>
                                            </td>
                                            <td>{{ $value->operatingsystem[0] ?? '' }} @if (!empty($value->operatingSystemVersion[0]))
                                                    | {{ $value->operatingSystemVersion[0] }}
                                                @endif
                                            </td>
                                            <td>{{ $value->getParentName() }}</td>
                                            <td>{{ $value->department[0] ?? '' }}</td>
                                            <td>
                                                @if (($value->userAccountControl[0] ?? null) == 4096)
                                                    <span class="badge bg-success">Enrôlé</span>
                                                @else
                                                    <span class="badge bg-warning text-dark">Vérifier</span>
                                                @endif
                                            </td>
                                            <td>{{ $value->whencreated }}</td>
                                            <td class="text-nowrap">
                                                <a href='{{ route('computer.remove', ['dn' => $value->getDn()]) }}'
                                                    class='btn btn-danger btn-sm'
                                                    onClick='return confirm("Supprimer cet ordinateur ?")'><i
                                                        class='fa fa-trash'></i></a>
                                                <button type="button" class="btn btn-warning btn-sm" data-bs-toggle="modal"
                                                    data-bs-target="#exampleModal"
                                                    onclick="MoveComputer('{{ $value->cn[0] }}','{{ $value->getDn() }}')">
                                                    <i class='fa fa-dolly'></i>
                                                </button>
                                                <a href="{{ url('/computer/view/' . $value->getDn()) }}"
                                                    class='btn btn-primary btn-sm'><i
                                                        class="fa-solid fa-magnifying-glass"></i></a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Modal déplacement --}}
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
                            <label class="col-sm-12 col-form-label">Nouvelle unité d'organisation :</label>
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
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                        <button type="submit" class="btn btn-primary">Déplacer</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('scriptjs')
    <script type="module">
        function MoveComputer(name, dncomput) {
            document.getElementById('dncomputer').value = dncomput;
            document.getElementById('namecomputer').innerHTML = `<h6 class="text-center">${name}</h6>`;
        }

        // DataTables simple (tu peux réactiver responsive si importé)
        $('#listcomputers').DataTable({
            // responsive: true,
            lengthChange: false,
            autoWidth: false,
        });
    </script>
@endsection
