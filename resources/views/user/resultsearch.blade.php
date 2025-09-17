<table id="resultats-utilisateurs-table" class="table">
    <thead>
        <tr>
            <th>Nom Prénom</th>
            <th>UID</th>
            <th>Mail</th>
            <th>Date de naissance</th>
        </tr>
    </thead>
    <tbody>

        @foreach ($usersldap as $users)
            <tr>
                <td><a href="{{ route('user.import', $users->getDn()) }}">{{ $users->cn[0] ?? '' }}</a></td>
                <td>{{ $users->uid[0] ?? '' }}</td>
                <td>{{ $users->mail[0] ?? '' }}</td>
                <td>{{ $users->datenaissance[0] ?? '' }}</td>
            </tr>
        @endforeach

    </tbody>
</table>
