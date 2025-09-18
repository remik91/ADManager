@extends('layouts.app')
@section('icon', 'fas fa-fw fa-computer')
@section('h1', 'Clés BitLocker')

@section('content')
    <div class="row mb-3">
        <div class="col-md-12">
            <div class="card mb-3">
                <div class="card-header">
                    <a href="{{ route('computer.index') }}" class="btn btn-outline-secondary"><i
                            class="fa-solid fa-chevron-left"></i></a>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-2">
        <div class="col-12">
            <div class="card bg-light mb-3 shadow-sm">
                <div class="card-header bg-primary text-white"><i class="fa-solid fa-magnifying-glass"></i> Recherche par
                    clé / GUID / nom</div>
                <div class="card-body">
                    <form method="GET" action="{{ route('computer.bitlocker') }}">
                        <div class="input-group">
                            <input type="text" name="search" class="form-control"
                                placeholder="Clé de récupération, GUID, ou nom de machine" value="{{ $search }}">
                            <button class="btn btn-primary" type="submit">Rechercher</button>
                        </div>
                        <div class="form-text">Tu peux coller une clé complète, un GUID ou un morceau de nom d'ordinateur.
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-2">
        <div class="col-12">
            <div class="card bg-light mb-3">
                <div class="card-header">
                    <h5 class="mb-0">Résultats</h5>
                </div>
                <div class="card-body">
                    @if ($bitlockerComputers->isEmpty())
                        <p>Aucune entrée BitLocker trouvée.</p>
                    @else
                        <div class="table-responsive">
                            <table class="table table-bordered align-middle">
                                <thead>
                                    <tr>
                                        <th>Ordinateur</th>
                                        <th>GUID</th>
                                        <th>DN</th>
                                        <th>Date</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($bitlockerComputers as $row)
                                        <tr>
                                            <td>{{ $row['computer'] ?? '—' }}</td>
                                            <td><code>{{ $row['guid'] ?? '—' }}</code></td>
                                            <td class="text-truncate" style="max-width:520px">
                                                <code>{{ $row['dn'] }}</code>
                                            </td>
                                            <td>{{ $row['when'] ?? '—' }}</td>
                                            <td>
                                                @if (!empty($row['computer_dn']))
                                                    <a class="btn btn-sm btn-outline-primary"
                                                        href="{{ url('/computer/view/' . $row['computer_dn']) }}">
                                                        Voir l'ordinateur
                                                    </a>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        {{ $bitlockerComputers->links() }}
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
