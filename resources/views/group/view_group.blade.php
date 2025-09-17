@extends('layouts.app')
@section('h1', 'Vue du groupe')

@section('content')


    <div class="row">
        <div class="col-md-12">
            <div class="card mb-3">
                <div class="card-body d-flex justify-content-between align-items-center ">
                    <div class="col-4">
                        <div class="input-group flex-column">
                            <form action="{{ route('group.search') }}" method="POST">
                                @csrf
                                <div class="input-group">
                                    <a href="{{ url()->previous() }}" class="btn btn-outline-secondary"><i
                                            class="fa-solid fa-chevron-left"></i></a>

                                    <input type="text" class="typeahead form-control" id="SearchGroup" name="SearchGroup"
                                        placeholder="Rechercher un groupe...">
                                    <button class="btn btn-outline-secondary" type="submit">Rechercher</button>
                                </div>
                            </form>
                        </div>
                    </div>
                    <div class="text-end">
                        <form action="{{ route('group.remove', ['dn' => $group->getDn()]) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-outline-secondary"><i class="fa-solid fa-trash-alt"
                                    onclick='return confirm("Attention : Voulez-vous vraiment supprimer ce groupe ? Cette opération est irréversible.")'></i>
                                Supprimer le groupe</button>
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-3">

            <!-- Profile Image -->
            <div class="card card-outline mb-3 shadow-sm">
                <div class="card-body">
                    <div class="text-center mb-3">
                        <i class="fa-solid fa-users fa-2xl"></i>
                    </div>

                    <h3 class="profile-username text-center">{{ $group->cn[0] ?? '' }}</h3>

                    <p class="text-center"> {{ $group->description[0] ?? '' }} </a>
                    </p>

                    <ul class="list-group list-group-unbordered mb-3">
                        <li class="list-group-item">
                            <b>Groupe de</b> <a class="float-end">{{ $group->getParentName() }}</a>
                        </li>
                        <li class="list-group-item">
                            <b>Membres</b> <a class="float-end">{{ count($members) }}</a>
                        </li>
                        <li class="list-group-item">
                            <b>Créer le</b> <a class="float-end">{{ $group->whencreated }}</a>
                        </li>
                        <li class="list-group-item">
                            <b>Dernière modif.</b> <a class="float-end">{{ $group->whenchanged }}</a>
                        </li>
                    </ul>
                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#EditGroup">
                            Editer
                        </button>
                    </div>
                </div>
                <!-- /.card-body -->
            </div>
            <!-- /.card -->
            <div class="card mb-3 shadow-sm">
                <div class="card-header">
                    <i class="fa-solid fa-users-viewfinder"></i> Appartenance du groupe
                </div>
                <div class="card-body">

                    <form enctype="multipart/form-data" action="{{ route('group.attachgroup', $group->getDn()) }}"
                        method="POST" class="row mb-2">
                        @csrf
                        <div class="col-9">
                            <input type="text" class="typeahead form-control" id="SearchAttachgroup"
                                name="SearchAttachgroup" placeholder="Nom du groupe..." required>
                        </div>
                        <div class="col-1">
                            <button type="submit" id="Save" class="btn btn-primary m-l-5">Ajouter</button>
                        </div>
                    </form>

                    @if ($membrede->count())
                        <table class="table table-bordered">
                            <thead class="thead-dark">
                                <tr>
                                    <th>Nom</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($membrede as $value)
                                    <tr>
                                        <td> <a href="{{ route('group.view', $value->getDn()) }}"
                                                class="link-dark">{{ $value->name[0] }}</a> </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        Aucune appartenance à un groupe
                    @endif
                </div>
            </div>
        </div>

        <div class="col-xs-9 col-sm-9 col-md-9 col-lg-9 col-xl-9">
            <div class="row">
                <div class="col-6">
                    <div class="card mb-3 shadow-sm">
                        <div class="card-header">
                            <i class="fa-solid fa-user-plus"></i> Ajout d'utilisateur
                        </div>
                        <div class="card-body">
                            <form enctype="multipart/form-data" action="{{ route('group.adduser', $group->getDn()) }}"
                                method="POST" class="row">
                                @csrf
                                <div class="col-9">
                                    <input type="text" class="typeahead form-control" id="SearchAdduser"
                                        name="SearchAdduser" aria-describedby="SearchAdduserHelp"
                                        placeholder="UID ou Nom d'utilisateur">
                                    <div id="SearchAdduserHelp" class="form-text">Si l'utilisateur n'apparait pas dans
                                        l'autocompletion, il n'existe pas.</div>
                                </div>
                                <div class="col-1">
                                    <button type="submit" id="Save" class="btn btn-primary m-l-5">Ajouter</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="col-6">
                    <div class="card mb-3 shadow-sm">
                        <div class="card-header">
                            <i class="fa-solid fa-user-group"></i> Ajout de groupe
                        </div>
                        <div class="card-body">
                            <form enctype="multipart/form-data" action="{{ route('group.addgroup', $group->getDn()) }}"
                                method="POST" class="row ">
                                @csrf
                                <div class="col-9">
                                    <input type="text" class="typeahead form-control" id="SearchAddgroup"
                                        name="SearchAddgroup" aria-describedby="SearchAddgroupHelp"
                                        placeholder="Nom du groupe de service...">
                                    <div id="SearchAddgroupHelp" class="form-text">Si le groupe n'apparait pas dans
                                        l'autocompletion, il n'existe pas.</div>
                                </div>
                                <div class="col-1">
                                    <button type="submit" id="Save" class="btn btn-primary m-l-5">Ajouter</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card mb-3 shadow-sm">
                <!-- end card-header -->

                <div class="card-header">
                    <i class="fa-solid fa-table-list"></i> Liste des membres du groupe
                </div>
                <div class="card-body">

                    <form action="{{ route('group.removeuser', $group->getDn()) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <div class="table-responsive">
                            <table id="listmembers" class="table table-bordered table-light">
                                <thead class="thead-dark">
                                    <tr>
                                        <th style="width: 50px;">Voir</th>
                                        <th>UID</th>
                                        <th>Informations</th>
                                        <th class="align-middle">
                                            <div class="text-center">Retirer</div>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($members as $value)
                                        <tr>
                                            <td>
                                                <center>
                                                    @if ($value->samaccounttype[0] == '805306368')
                                                        {{-- NORMAL ACCOUNT  --}}
                                                        <a href="{{ route('user.view', $value->getDn()) }}"><i
                                                                class="fa fa-search"></i></a>
                                                    @elseif($value->samaccounttype[0] == '268435456')
                                                        {{--  GROUP ACCOUNT --}}
                                                        <a href="{{ route('group.view', $value->getDn()) }}"><i
                                                                class="fa fa-search"></i></a>
                                                    @endif
                                                </center>
                                            </td>
                                            <td> {{ $value->cn[0] }} </td>
                                            <td>
                                                @if ($value->samaccounttype[0] == '805306368')
                                                    {{-- NORMAL ACCOUNT  --}}
                                                    {{ $value->displayName[0] }}
                                                @elseif($value->samaccounttype[0] == '268435456')
                                                    {{--  GROUP ACCOUNT --}}
                                                    {{ $value->description[0] }}
                                                @endif
                                            </td>
                                            <td>
                                                <div class="text-center">
                                                    <button type="submit" class="btn btn-warning btn-sm" name="dnuser"
                                                        value="{{ $value->getDn() }}"><i
                                                            class="fa-solid fa-xmark"></i></button>
                                                </div>


                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </form>

                </div>
            </div>

        </div>

        <!-- end row -->
    </div>


    <!-- Modal -->
    <div class="modal fade" id="EditGroup" tabindex="-1" aria-labelledby="EditGroupModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="EditGroupModalLabel">Modification du groupe</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form role="form" action="{{ route('group.edit', $group->getDn()) }}" id="formeditgroup"
                        class="row g-3" autocomplete="off">
                        @csrf

                        <div class="col-12">
                            <label for="cnInput" class="form-label">Nom du groupe</label>

                            <input type="text" class="form-control" id="cnInput" name="cn"
                                value="{{ $group->cn[0] ?? '' }}" required>

                        </div>
                        <div class="col-12">
                            <label for="descriptionInput" class="form-label">Description</label>

                            <input type="text" class="form-control" id="descriptionInput" name="description"
                                value="{{ $group->description[0] ?? '' }}">

                        </div>
                        <div class="col-12 d-grid gap-2 d-md-flex justify-content-md-end">
                            <button type="submit" class="btn btn-primary">Modifier</button>
                        </div>
                    </form>

                </div>

            </div>

        </div>
    </div>


@endsection

@section('scriptjs')
    <script type="module">
        const initializeTypeahead = (element, routeName, ouName = "") => {
            const path = routeName;

            $(element).typeahead({
                source: (query, process) => {
                    return $.get(path, {
                        search: query,
                        ou: ouName
                    }, data => process(data));
                }
            });
        };

        initializeTypeahead('#SearchAdduser', '{{ route('user.autocomplete') }}');
        initializeTypeahead('#SearchGroup', '{{ route('group.autocomplete') }}',
            'OU=Services,DC=ad,DC=ac-creteil');
        initializeTypeahead('#SearchAddgroup', '{{ route('group.autocomplete') }}', 'OU=Services,DC=ad,DC=ac-creteil');
        initializeTypeahead('#SearchAttachgroup', '{{ route('group.autocomplete') }}', 'OU=Partages,DC=ad,DC=ac-creteil');


        let table = $("#listmembers").DataTable({
            responsive: true,
            lengthChange: true,
            autoWidth: true,
            buttons: ["copy", "csv", "excel", "pdf", "print"]
        });
    </script>
@endsection
