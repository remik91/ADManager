@extends('layouts.app')
@section('icon', 'fas fa-fw fa-computer')
@section('h1', 'Liste de BitLocker')

@section('content')

    <div class="row">
        <div class="col-md-12">
            <div class="card mb-3">
                <div class="card-header">
                    <a href="{{ route('user.index') }}" class="btn btn-outline-secondary">
                        <i class="fa-solid fa-chevron-left"></i></a>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-2">

        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12">
            <div class="card bg-light mb-3">
                <div class="card-header bg-primary text-white">
                    <i class="fa-solid fa-magnifying-glass"></i> Recherche par clé
                </div>
                <div class="card-body">
                    <form method="GET" action="{{ route('computer.bitlocker') }}">
                        <div class="input-group mb-3">
                            <input type="text" name="search" class="form-control"
                                placeholder="Rechercher par clé de récupération" value="{{ request('search') }}">
                            <div class="input-group-append">
                                <button class="btn btn-outline-secondary" type="submit">Rechercher</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>

    <div class="row mb-2">

        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12">
            <div class="card bg-light mb-3">
                <div class="card-header">

                    <h5>Liste des ordinateurs avec BitLocker</h5>

                    @if ($bitlockerComputers->isEmpty())
                        <p>Aucun ordinateur avec BitLocker trouvé.</p>
                    @else
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Clé de bitlock</th>
                                    <th>Ordinateur bitlock</th>
                                    <th>Date de changement</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($bitlockerComputers as $computer)
                                    <tr>
                                        <!-- Extraction de la clé entre {} du CN -->
                                        <td>
                                            @php
                                                $cn = $computer->getName();
                                                preg_match('/\{(.+?)\}/', $cn, $matches);
                                                $recoveryKey = $matches[1] ?? 'Clé introuvable';
                                            @endphp
                                            {{ $recoveryKey }}
                                        </td>
                                        <td>{{ $computer->getParentName() }}</td>

                                        <td>{{ $computer->whenCreated }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>

                        <!-- Pagination personnalisée -->
                        {{ $bitlockerComputers->links() }}
                </div>
            </div>
        </div>
        @endif

    @endsection
