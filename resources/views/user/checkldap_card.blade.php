@if (!$userldap)
    <div class="alert alert-danger" role="alert">
        Aucun utilisateur correspondant trouvé sur le LDAP Académique.
    </div>
@else
    <div id="ldapcard" class="card" data-ad-dn="{{ $userADdn }}" data-ldap-dn="{{ $userldap->getDn() }}">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div>
                <strong>{{ $userldap->getFirstAttribute('cn') }}</strong>
                <span class="badge bg-secondary ms-2">UID: {{ $userldap->getFirstAttribute('uid') }}</span>
            </div>
            <span class="text-muted small">Lecture seule</span>
        </div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-6">
                    <div class="card h-100">
                        <div class="card-header">Identité & Contact (LDAP)</div>
                        <div class="card-body">
                            <div class="mb-2"><small class="text-muted">Prénom</small>
                                <div class="fw-semibold">{{ $userldap->getFirstAttribute('givenname') }}</div>
                            </div>
                            <div class="mb-2"><small class="text-muted">Nom</small>
                                <div class="fw-semibold">{{ $userldap->getFirstAttribute('sn') }}</div>
                            </div>
                            <div class="mb-2"><small class="text-muted">Mail</small>
                                <div class="fw-semibold text-break">{{ $userldap->getFirstAttribute('mail') }}</div>
                            </div>
                            <div class="mb-2"><small class="text-muted">Bureau</small>
                                <div class="fw-semibold">{{ $userldap->getFirstAttribute('bureau') }}</div>
                            </div>
                            <div class="mb-2"><small class="text-muted">Naissance</small>
                                <div class="fw-semibold">{{ $userldap->getFirstAttribute('datenaissance') }}</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card h-100">
                        <div class="card-header">Métier (LDAP)</div>
                        <div class="card-body">
                            <div class="mb-2"><small class="text-muted">Division</small>
                                <div class="fw-semibold">{{ $userldap->getFirstAttribute('division') }}</div>
                            </div>
                            <div class="mb-2"><small class="text-muted">Service</small>
                                <div class="fw-semibold">{{ $userldap->getFirstAttribute('service') }}</div>
                            </div>
                            <div class="mb-2"><small class="text-muted">Fonction</small>
                                <div class="fw-semibold">{{ $userldap->getFirstAttribute('fonction') }}</div>
                            </div>
                            <div class="mb-2"><small class="text-muted">Titre</small>
                                <div class="fw-semibold">{{ $userldap->getFirstAttribute('title') }}</div>
                            </div>
                            <div class="mb-2"><small class="text-muted">Fin de fonction</small>
                                <div class="fw-semibold">{{ $userldap->getFirstAttribute('finfonction') }}</div>
                            </div>
                            <div class="mb-2"><small class="text-muted">Date fin</small>
                                <div class="fw-semibold">{{ $userldap->getFirstAttribute('dateff') }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12">
                    <div class="card">
                        <div class="card-header">Références</div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-8">
                                    <small class="text-muted">DN LDAP</small>
                                    <div class="input-group">
                                        <input type="text" class="form-control" value="{{ $userldap->getDn() }}"
                                            readonly>
                                        <button class="btn btn-outline-secondary" type="button"
                                            onclick="navigator.clipboard.writeText('{{ $userldap->getDn() }}'); toastr?.success('DN copié');"><i
                                                class="fa-regular fa-copy"></i></button>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <small class="text-muted">Radius Group</small>
                                    <div class="fw-semibold">{{ $userldap->getFirstAttribute('radiusgroupname') }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Comparatif rapide LDAP ↔ AD --}}
                <div class="col-12">
                    <div class="card border-info">
                        <div class="card-header bg-light"><i class="fa-solid fa-scale-balanced me-2"></i>Comparaison
                            rapide LDAP ↔ AD</div>
                        <div class="card-body table-responsive">
                            <table class="table table-sm align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>Champ</th>
                                        <th>LDAP</th>
                                        <th>AD</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $pairs = [
                                            'givenName' => [
                                                'LDAP' => $userldap->getFirstAttribute('givenname'),
                                                'AD' => $userAD->getFirstAttribute('givenName'),
                                            ],
                                            'sn' => [
                                                'LDAP' => $userldap->getFirstAttribute('sn'),
                                                'AD' => $userAD->getFirstAttribute('sn'),
                                            ],
                                            'mail' => [
                                                'LDAP' => $userldap->getFirstAttribute('mail'),
                                                'AD' => $userAD->getFirstAttribute('mail'),
                                            ],
                                            'division' => [
                                                'LDAP' => $userldap->getFirstAttribute('division'),
                                                'AD' => $userAD->getFirstAttribute('division'),
                                            ],
                                            'department' => [
                                                'LDAP' => $userldap->getFirstAttribute('service'),
                                                'AD' => $userAD->getFirstAttribute('department'),
                                            ],
                                            'title' => [
                                                'LDAP' =>
                                                    $userldap->getFirstAttribute('fonction') ?:
                                                    $userldap->getFirstAttribute('title'),
                                                'AD' => $userAD->getFirstAttribute('title'),
                                            ],
                                            'office' => [
                                                'LDAP' => $userldap->getFirstAttribute('bureau'),
                                                'AD' => $userAD->getFirstAttribute('physicalDeliveryOfficeName'),
                                            ],
                                        ];
                                    @endphp
                                    @foreach ($pairs as $label => $vals)
                                        @php $diff = trim((string)$vals['LDAP']) !== trim((string)$vals['AD']); @endphp
                                        <tr class="{{ $diff ? 'table-warning' : '' }}">
                                            <td class="text-nowrap">{{ $label }}</td>
                                            <td class="text-break">{{ $vals['LDAP'] ?? '—' }}</td>
                                            <td class="text-break">{{ $vals['AD'] ?? '—' }}</td>
                                            <td>
                                                @if ($diff)
                                                <span class="badge bg-warning text-dark">Différent</span>@else<span
                                                        class="badge bg-success">OK</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            <div class="text-end">
                                <button class="btn btn-outline-primary" type="button" data-bs-toggle="modal"
                                    data-bs-target="#syncModal">
                                    <i class="fa-solid fa-people-pulling me-1"></i> Synchroniser ces champs vers AD
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
@endif
