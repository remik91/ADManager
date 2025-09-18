@extends('layouts.app')
@section('icon', 'fas fa-fw fa-user')
@section('h1', 'Création d\'utilisateur')

@section('content')
    <div class="row g-3 mb-3">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-body d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <div class="d-flex align-items-center gap-2">
                        <a href="{{ route('user.index') }}" class="btn btn-light"><i class="fa-solid fa-chevron-left"></i></a>
                        <div>
                            <div class="fw-bold">Créer un compte</div>
                            <div class="text-muted small">Saisir manuellement les informations ou les importer depuis
                                l'annuaire</div>
                        </div>
                    </div>
                    <div class="btn-group" role="group" aria-label="Modes de création">
                        <button type="button" class="btn btn-outline-primary active" disabled>
                            <i class="fa-solid fa-keyboard me-1"></i> Manuel
                        </button>
                        <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal"
                            data-bs-target="#exampleModal">
                            <i class="fa-solid fa-magnifying-glass me-1"></i> Importer depuis l'annuaire
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @isset($userAD)
        <div class="alert alert-warning d-flex align-items-center" role="alert">
            <i class="fa-solid fa-triangle-exclamation me-2"></i>
            Le compte existe déjà dans l'Active Directory.
        </div>
    @endisset

    <form action="{{ route('user.create') }}" method="post" novalidate>
        @csrf
        <input type="text" name="synctoldap" id="synctoldap"
            value="@if (isset($userLDAP->uid[0])) TRUE @else FALSE @endif" hidden>

        <div class="row g-3">
            <!-- Colonne principale -->
            <div class="col-lg-8">

                {{-- Identité & connexion --}}
                <div class="card mb-3 shadow-sm">
                    <div class="card-body">
                        <h3 class="h6 mb-3">Identité & identifiants</h3>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Prénom *</label>
                                <input type="text" class="form-control" name="givenName" id="givenName"
                                    value="{{ $userLDAP->givenname[0] ?? '' }}" autocomplete="off" required>
                                <div class="invalid-feedback">Champ obligatoire.</div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Nom *</label>
                                <input type="text" class="form-control" name="sn" id="sn"
                                    value="{{ $userLDAP->sn[0] ?? '' }}" autocomplete="off" required>
                                <div class="invalid-feedback">Champ obligatoire.</div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">UID (sAMAccountName) *</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fa-solid fa-id-badge"></i></span>
                                    <input type="text" class="form-control" name="uid" id="uid"
                                        value="{{ $userLDAP->uid[0] ?? '' }}" autocomplete="off" required>
                                </div>
                                <div class="form-text">Proposition automatique basée sur prénom + nom (modifiable).</div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">E‑mail *</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fa-solid fa-envelope"></i></span>
                                    <input type="email" class="form-control" name="mail" id="mail"
                                        value="{{ $userLDAP->mail[0] ?? '' }}" autocomplete="off" required>
                                </div>
                                <div class="form-text">Format conseillé : prenom.nom@domaine</div>
                            </div>
                        </div>

                        <div class="mt-3 p-2 bg-light rounded small">
                            <div><span class="text-muted">UPN :</span> <code id="upnPreview">—</code></div>
                        </div>
                    </div>
                </div>

                {{-- Organisation & informations complémentaires --}}
                <div class="card mb-3 shadow-sm">
                    <div class="card-body">
                        <h3 class="h6 mb-3">Organisation & informations</h3>
                        <div class="row g-3">
                            <div class="col-lg-6">
                                <label class="form-label">Unité d'organisation *</label>
                                <select class="form-select" name="ou" id="ou" required>
                                    <option value selected disabled>Choisir une unité d'organisation</option>
                                    @foreach ($ouList as $ou)
                                        <option value="{{ $ou->getDn() }}">{{ $ou->getName() }}</option>
                                    @endforeach
                                </select>
                                <div class="invalid-feedback">Sélectionne une OU.</div>
                                <div class="form-text small text-truncate"><span class="text-muted">DN :</span> <span
                                        id="ouDnPreview">—</span></div>
                            </div>
                            <div class="col-lg-6">
                                <label class="form-label">Bureau</label>
                                <input type="text" class="form-control" name="physicalDeliveryOfficeName"
                                    id="physicalDeliveryOfficeName" value="{{ $userLDAP->bureau[0] ?? '' }}"
                                    autocomplete="off">
                            </div>

                            <div class="col-lg-3">
                                <label class="form-label">Division</label>
                                <input type="text" class="form-control" name="division" id="division"
                                    value="{{ $userLDAP->division[0] ?? '' }}" autocomplete="off">
                            </div>
                            <div class="col-lg-5">
                                <label class="form-label">Service</label>
                                <input type="text" class="form-control" name="department" id="department"
                                    value="{{ $userLDAP->service[0] ?? '' }}" autocomplete="off">
                            </div>
                            <div class="col-lg-4">
                                <label class="form-label">Fonction</label>
                                <input type="text" class="form-control" name="title" id="title"
                                    value="{{ $userLDAP->fonction[0] ?? '' }}" autocomplete="off">
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Colonne latérale -->
            <div class="col-lg-4">

                <div class="card mb-3 shadow-sm">
                    <div class="card-body">
                        <h3 class="h6">Type de compte</h3>
                        <select class="form-select" name="typeOfAccount" required>
                            <option value="utilisateur" selected>Utilisateur</option>
                            <option value="fonctionnel">Fonctionnel</option>
                            <option value="provisoire">Provisoire</option>
                            <option value="stagiaire">Stagiaire</option>
                        </select>
                    </div>
                </div>

                <div class="card mb-3 shadow-sm">
                    <div class="card-body">
                        <h3 class="h6">Options</h3>
                        <ul class="list-group list-group-flush mx-n2">
                            <li class="list-group-item px-0 d-flex justify-content-between align-items-start">
                                <div class="ms-2 me-auto">
                                    <h6 class="mb-0">Répertoire personnel</h6>
                                    <small>Active la création du lecteur personnel (H:).</small>
                                    <div class="small mt-1 text-muted">Chemin : <code id="homeDirPreview">—</code></div>
                                </div>
                                <div class="form-check form-switch mt-1">
                                    <input class="form-check-input" type="checkbox" role="switch" name="repperso"
                                        id="repperso">
                                </div>
                            </li>
                            <li class="list-group-item px-0 d-flex justify-content-between align-items-start">
                                <div class="ms-2 me-auto">
                                    <h6 class="mb-0">Envoi d'email</h6>
                                    <small>Envoyer un email de confirmation à l'utilisateur</small>
                                </div>
                                <div class="form-check form-switch mt-1">
                                    <input class="form-check-input" type="checkbox" role="switch" disabled>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>

                <div class="card mb-3 shadow-sm">
                    <div class="card-body">
                        <h3 class="h6">Description</h3>
                        <textarea class="form-control" rows="3" name="description" placeholder="Notes internes (facultatif)"></textarea>
                    </div>
                </div>

                <div class="alert alert-info">
                    <div class="d-flex align-items-center mb-1"><i class="fa-solid fa-lock me-2"></i> Mot de passe initial
                    </div>
                    <div class="small">Un mot de passe par défaut sera appliqué : <code
                            id="defaultPwd">%Rectorat94*</code><br>Il devra être changé à la première connexion.</div>
                    <button type="button" class="btn btn-sm btn-outline-secondary mt-2" id="copyPwd"><i
                            class="fa-regular fa-copy me-1"></i> Copier</button>
                </div>

                <div class="card sticky-top" style="top: 90px;">
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary" id="submitBtn">
                                <span class="spinner-border spinner-border-sm me-2 d-none" id="submitSpinner"
                                    role="status" aria-hidden="true"></span>
                                <i class="fas fa-user-plus me-1"></i> Créer l'utilisateur
                            </button>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </form>
@endsection

@include('user.modal_importldap')

@section('scriptjs')
    <script type="module">
        // Helpers pour nettoyer et proposer UID + aperçu UPN
        const $given = document.getElementById('givenName');
        const $sn = document.getElementById('sn');
        const $uid = document.getElementById('uid');
        const $mail = document.getElementById('mail');
        const $upn = document.getElementById('upnPreview');
        const $ou = document.getElementById('ou');
        const $ouDnPreview = document.getElementById('ouDnPreview');
        const $rep = document.getElementById('repperso');
        const $home = document.getElementById('homeDirPreview');
        const $submitBtn = document.getElementById('submitBtn');
        const $spinner = document.getElementById('submitSpinner');

        // Activation Select2 si présent
        try {
            if (window.$ && $('#ou').select2) {
                $('#ou').select2({
                    width: '100%'
                });
            }
        } catch (e) {}

        // Remove diacritics
        const deburr = (s) => (s || '').normalize('NFD').replace(/[\u0300-\u036f]/g, '');
        // Sanitize samAccountName (basic)
        const toUid = (g, n) => {
            const base = (deburr(g).trim().charAt(0) + deburr(n).trim()).toLowerCase();
            return base.replace(/[^a-z0-9._-]/g, '');
        };

        let uidTouched = false;
        $uid?.addEventListener('input', () => {
            uidTouched = true;
            updateUpn();
            updateHome();
        });

        function updateUidSuggestion() {
            if (uidTouched) return;
            const g = $given?.value || '';
            const n = $sn?.value || '';
            const suggestion = toUid(g, n);
            if (suggestion) {
                $uid.value = suggestion;
            }
            updateUpn();
            updateHome();
        }

        function updateUpn() {
            const u = ($uid?.value || '').trim();
            $upn.textContent = u ? (u + '@ad.ac-creteil') : '—';
        }

        function updateHome() {
            if (!$home) return;
            const u = ($uid?.value || '').trim();
            $home.textContent = ($rep?.checked && u) ? `\\\\ad.ac-creteil\\Perso\\home\\${u}` : '—';
        }

        $given?.addEventListener('input', updateUidSuggestion);
        $sn?.addEventListener('input', updateUidSuggestion);
        $rep?.addEventListener('change', updateHome);
        $ou?.addEventListener('change', () => {
            const opt = $ou.options[$ou.selectedIndex];
            $ouDnPreview.textContent = opt ? opt.value : '—';
        });

        // Copier mot de passe par défaut
        document.getElementById('copyPwd')?.addEventListener('click', async () => {
            try {
                await navigator.clipboard.writeText(document.getElementById('defaultPwd').textContent.trim());
                toastr.success('Mot de passe copié');
            } catch (e) {
                toastr.error('Copie impossible');
            }
        });

        // Submit UX
        $submitBtn?.addEventListener('click', () => {
            $spinner.classList.remove('d-none');
            setTimeout(() => {
                $spinner.classList.add('d-none');
            }, 3000);
        });

        // Init
        updateUidSuggestion();
        updateUpn();
        updateHome();

        (() => {
            const $form = document.getElementById('search-form-ldap');
            const $btn = document.getElementById('ldap-search-btn');
            const $spin = document.getElementById('ldap-spinner');
            const $results = document.getElementById('search-results');
            const $loading = document.getElementById('ldap-loading');
            const $empty = document.getElementById('ldap-empty');
            const $modal = document.getElementById('exampleModal');
            const $crit = document.getElementById('ldap-crit');
            const $ou = document.getElementById('ldap-ou');
            const $q = document.getElementById('search_uid');

            // Mémoriser le dernier choix
            const k = {
                crit: 'ldap_crit',
                ou: 'ldap_ou',
                q: 'ldap_q'
            };
            const ls = window.localStorage;

            function setLoading(on) {
                if (on) {
                    $spin.classList.remove('d-none');
                    $btn.setAttribute('disabled', 'disabled');
                    $loading.classList.remove('d-none');
                    $results.classList.add('d-none');
                    $empty.classList.add('d-none');
                } else {
                    $spin.classList.add('d-none');
                    $btn.removeAttribute('disabled');
                    $loading.classList.add('d-none');
                }
            }

            function showEmpty() {
                $empty.classList.remove('d-none');
                $results.classList.add('d-none');
            }

            // Restore derniers critères
            try {
                const vCrit = ls.getItem(k.crit);
                if (vCrit) $crit.value = vCrit;
                const vOu = ls.getItem(k.ou);
                if (vOu) $ou.value = vOu;
                const vQ = ls.getItem(k.q);
                if (vQ) $q.value = vQ;
            } catch (e) {}

            // Soumission AJAX
            $form?.addEventListener('submit', function(e) {
                e.preventDefault();
                const crit = $crit.value;
                const ou = $ou.value;
                const q = $q.value.trim();
                if (!q) {
                    toastr.info('Saisis un terme de recherche.');
                    showEmpty();
                    return;
                }

                // Save critères
                try {
                    ls.setItem(k.crit, crit);
                    ls.setItem(k.ou, ou);
                    ls.setItem(k.q, q);
                } catch (e) {}

                setLoading(true);
                $.ajax({
                    url: '{{ route('user.searchldapimport') }}',
                    type: 'GET',
                    data: {
                        q,
                        ou,
                        crit
                    },
                    success: function(html) {
                        $results.innerHTML = html ||
                            '<div class="alert alert-light m-0">Aucun résultat.</div>';
                        $results.classList.remove('d-none');
                        $empty.classList.add('d-none');
                    },
                    error: function(xhr) {
                        console.error(xhr);
                        $results.innerHTML =
                            '<div class="alert alert-danger m-0">Erreur lors de la recherche LDAP.</div>';
                        $results.classList.remove('d-none');
                        $empty.classList.add('d-none');
                    },
                    complete: function() {
                        setLoading(false);
                    }
                });
            });

            // Entrée déclenche la recherche
            $q?.addEventListener('keydown', (e) => {
                if (e.key === 'Enter') $form.requestSubmit();
            });

            // Délégation: bouton d'import dans les résultats
            document.addEventListener('click', function(e) {
                const btn = e.target.closest('.btn-import-ldap');
                if (!btn) return;

                // On attend ces data-attrs sur le bouton dans le partial results côté serveur :
                // data-given, data-sn, data-uid, data-mail, data-division, data-department, data-title, data-bureau
                const map = {
                    givenName: btn.dataset.given || '',
                    sn: btn.dataset.sn || '',
                    uid: btn.dataset.uid || '',
                    mail: btn.dataset.mail || '',
                    division: btn.dataset.division || '',
                    department: btn.dataset.department || '',
                    title: btn.dataset.title || '',
                    bureau: btn.dataset.bureau || ''
                };

                // Injecte dans le formulaire principal (si dispo)
                const setVal = (id, val) => {
                    const el = document.getElementById(id);
                    if (!el || val === undefined) return;
                    el.value = val;
                    el.dispatchEvent(new Event('input', {
                        bubbles: true
                    })); // pour rafraîchir les aperçus (UPN, home, etc.)
                    el.dispatchEvent(new Event('change', {
                        bubbles: true
                    }));
                };

                setVal('givenName', map.givenName);
                setVal('sn', map.sn);
                setVal('uid', map.uid);
                setVal('mail', map.mail);
                setVal('division', map.division);
                setVal('department', map.department);
                setVal('title', map.title);
                setVal('physicalDeliveryOfficeName', map.bureau);

                const modalEl = document.getElementById('exampleModal');
                if (modalEl) {
                    if (window.bootstrap?.Modal) {
                        window.bootstrap.Modal.getOrCreateInstance(modalEl).hide();
                    } else if (typeof $ !== 'undefined' && typeof $('#exampleModal').modal === 'function') {
                        // fallback Bootstrap 4 (si jamais)
                        $('#exampleModal').modal('hide');
                    } else {
                        // dernier recours : clique sur le bouton close
                        modalEl.querySelector('[data-bs-dismiss="modal"]')?.click();
                    }
                }
            });

            // Réinitialiser l'état à l’ouverture
            $modal?.addEventListener('show.bs.modal', () => {
                $results.classList.add('d-none');
                $empty.classList.remove('d-none');
                $loading.classList.add('d-none');
                document.getElementById('search_uid')?.focus({
                    preventScroll: true
                });
            });

        })();
    </script>
@endsection
