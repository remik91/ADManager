@extends('layouts.app')
@section('icon', 'fas fa-columns')
@section('h1', "Historique d'administration")

@section('content')
    <div class="row">

        <div class="col-md-12">

            <div class="card mb-3">

                <div class="card-body">
                    <h2>Liste d'activité de l'administration</h2>
                    <table id="activityLogTable" class="table table-striped" data-order='[[ 2, "desc" ]]' data-page-length='25'>
                        <thead>
                            <tr>
                                <th>Utilisateur</th>
                                <th>Activité</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($logactivity as $activity)
                                <tr>
                                    <td order='false'>{{ $activity->causer->name ?? 'Anonyme' }}</td>
                                    <td>{{ $activity->description }}</td>
                                    <td>{{ $activity->created_at }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    @endsection

    @section('scriptjs')
        <script type="module">
            // Assurez-vous d'avoir inclus DataTables et ses dépendances

            $(document).ready(function() {
                $('#activityLogTable').DataTable({
                    dom: '<"d-flex justify-content-between align-items-center mb-3"Blf>rt<"d-flex justify-content-between align-items-center mb-3"ip>',
                    "paging": true, // Activer la pagination
                    "searching": true, // Activer la recherche
                    buttons: [
                        'copyHtml5',
                        'csvHtml5',
                        'print'
                    ]
                    // Vous pouvez ajouter d'autres options selon vos besoins
                });
            });
        </script>
    @endsection
