@if ($usersldap->count() === 0)
    <div class="alert alert-light m-0">Aucun résultat.</div>
@else
    <div class="ldap-results">
        @foreach ($usersldap as $u)
            @php
                $uid = $u->uid[0] ?? '';
                $sn = $u->sn[0] ?? '';
                $given = $u->givenname[0] ?? '';
                $mail = $u->mail[0] ?? '';
                $datenaissance = $u->datenaissance[0] ?? '';
                $division = $u->division[0] ?? '';
                $department = $u->department[0] ?? ($u->service[0] ?? '');
                $title = $u->title[0] ?? ($u->fonction[0] ?? '');
                $bureau = $u->physicalDeliveryOfficeName[0] ?? ($u->bureau[0] ?? '');
            @endphp

            <div class="card mb-2">
                <div class="card-body d-flex justify-content-between align-items-start gap-3">
                    <div>
                        <div class="fw-bold">
                            <i class="fa-solid fa-user me-2"></i>{{ $given }} {{ $sn }}
                            @if ($title)
                                <span class="badge text-bg-light ms-2">{{ $title }}</span>
                            @endif
                        </div>
                        <div class="small text-muted">
                            UID: <code>{{ $uid }}</code> ·
                            @if ($mail)
                                <i class="fa-solid fa-envelope"></i> {{ $mail }} ·
                            @endif
                            @if ($mail)
                                <i class="fa-solid fa-cake-candles"></i> {{ $datenaissance }} ·
                            @endif
                            @if ($department)
                                <i class="fa-solid fa-building-user"></i> {{ $department }}
                            @endif
                            @if ($division)
                                · {{ $division }}
                            @endif
                            @if ($bureau)
                                · <i class="fa-solid fa-location-dot"></i> {{ $bureau }}
                            @endif
                        </div>
                    </div>
                    <div class="text-nowrap">
                        <button type="button" class="btn btn-sm btn-outline-primary btn-import-ldap"
                            title="Importer ces informations" data-given="{{ $given }}"
                            data-sn="{{ $sn }}" data-uid="{{ $uid }}"
                            data-mail="{{ $mail }}" data-division="{{ $division }}"
                            data-department="{{ $department }}" data-title="{{ $title }}"
                            data-bureau="{{ $bureau }}">
                            <i class="fa-solid fa-download me-1"></i> Importer
                        </button>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    @if (!empty($hasMore) && $hasMore && !empty($nextCookie))
        <div class="text-center mt-3">
            <button class="btn btn-outline-primary" id="ldap-load-more" data-q="{{ $q }}"
                data-ou="{{ $ou }}" data-crit="{{ $crit }}" data-per-page="{{ $perPage }}">
                <i class="fa-solid fa-angles-down me-1"></i> Plus de résultats
            </button>
        </div>
    @endif
@endif
