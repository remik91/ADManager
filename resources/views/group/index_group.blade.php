@extends('layouts.app')
@section('icon', 'fas fa-fw fa-users')
@section('h1', 'Gestion des groupes')

@section('content')

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
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($listgroup as $value)
                                    <tr>
                                        <td>
                                            <a href="{{ url('/group/view/' . $value->getDn()) }}">{{ $value->cn[0] }}</a>
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
                                    <th>UID</th>
                                    <th>Nom complet</th>
                                    <th>Membres</th>
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
