@extends('layouts.app')
@section('icon', 'fas fa-columns')
@section('h1', 'Master')

@section('content')

    <div class="card text-center" style="width: 18rem;">
        <div class="card-body">
            <h5 class="card-title">Windows 10 Educ Master 21H2 ADCRETEIL</h5>
            <p class="card-text">(Ver. 17052022 pour PXE ISO)</p>
            <a href="{{ route('admin.download') }}" class="btn btn-primary">Télécharger</a>
        </div>
    </div>

@endsection
