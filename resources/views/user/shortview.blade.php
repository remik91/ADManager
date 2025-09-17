<!-- Profile Image -->
<div class="card @if ($user->isEnabled()) card-primary @else card-warning @endif card-outline mb-3">
    <div class="card-body">

        <h3 class="profile-username text-center">{{ $user->displayName[0] ?? '' }}</h3>

        <p class="text-muted text-center"><a href="mailto:{{ $user->mail[0] ?? '' }}">{{ $user->mail[0] ?? '' }}</a></p>

        <ul class="list-group list-group-unbordered mb-3">
            <li class="list-group-item">
                <b>UID</b> <a class="float-end">{{ $user->sAMAccountName[0] ?? '' }}</a>
            </li>
            <li class="list-group-item">
                <b>Téléphone</b> <a href="tel:{{ $user->telephonenumber[0] ?? '' }}"
                    class="float-end">{{ $user->telephonenumber[0] ?? '' }}</a>
            </li>
            <li class="list-group-item">
                <b>Bureau</b> <a class="float-end">{{ $user->physicaldeliveryofficename[0] ?? '' }}</a>
            </li>
            <li class="list-group-item">
                <b>Créer le</b> <a class="float-end">{{ $user->whencreated }}</a>
            </li>
            <li class="list-group-item">
                <b>Nombres de co.</b> <a class="float-end">{{ $user->logoncount[0] }}</a>
            </li>
            <li class="list-group-item">
                <b>Dernière co.</b> <a class="float-end">{{ $user->lastlogon }}</a>
            </li>
        </ul>
    </div>
    <!-- /.card-body -->
    <div class="card-footer d-flex justify-content-center">
        <a href="{{ url('/user/view/' . $user->getDn()) }}" class="btn btn-primary">Consulter le profil</a>
    </div>
</div>
<!-- /.card -->
