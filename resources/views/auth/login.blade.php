@extends('layouts.login')

@section('content')
    <div class="row h-100 justify-content-center align-items-center">

        <div class="col-xl-10 col-lg-12 col-md-9">

            <div class="card o-hidden border-0 shadow-lg my-5">
                <div class="card-body p-0">
                    <!-- Nested Row within Card Body -->
                    <div class="row">
                        <div class="col-lg-6 d-none d-lg-block bg-login-image"></div>
                        <div class="col-lg-6">
                            <div class="p-5">
                                <div class="text-center">
                                    <h1 class="mb-4">
                                        <a href="/" class="logo">
                                            <span><b>AD</b> <span>Manager</span></span>
                                        </a>
                                    </h1>

                                    @error('username')
                                        <p class='text-danger'>
                                            <strong>{{ $message }}</strong>
                                        </p>
                                    @enderror
                                    @error('password')
                                        <p class='text-danger'>
                                            <strong>{{ $message }}</strong>
                                        </p>
                                    @enderror
                                    <br>

                                </div>
                                <form method="post" action="{{ route('login') }}">
                                    @csrf
                                    <div class="input-group mb-3">
                                        <span class="input-group-text" id="basic-addon1"><i class="fa fa-envelope"
                                                aria-hidden="true"></i></span>
                                        <input type="text" class="form-control @error('username') is-invalid @enderror"
                                            placeholder="Identifiant académique" id="username" name="username"
                                            value="{{ old('username') }}" aria-label="Username"
                                            aria-describedby="basic-addon1" autofocus required>
                                    </div>
                                    <div class="input-group mb-3">
                                        <span class="input-group-text" id="basic-addon2"><i class="fa fa-unlock"
                                                aria-hidden="true"></i></span>
                                        <input type="password" class="form-control @error('password') is-invalid @enderror"
                                            placeholder="Mot de passe" id="password" name="password" aria-label="password"
                                            aria-describedby="basic-addon2" required>
                                    </div>

                                    <div class="mb-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="remember"
                                                id="disabledFieldsetCheck" {{ old('remember') ? 'checked' : '' }}>
                                            <label class="form-check-label" for="disabledFieldsetCheck">
                                                {{ __('Se souvenir de moi') }}
                                            </label>
                                        </div>
                                    </div>

                                    <div class="d-grid gap-2">
                                        <input type="submit" class="btn btn-primary btn-user btn-block" value="Connexion"
                                            name="submit" />
                                    </div>

                                </form>
                                <hr>
                                <div class="text-center">
                                    <a class="small" href="mailto:remi.koutchinski@ac-creteil.fr">Un problème ?</a>

                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>

    </div>
@endsection
