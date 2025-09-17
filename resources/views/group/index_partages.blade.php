@extends('layouts.app')
@section('icon', 'fa-solid fa-users-between-lines')
@section('h1', 'Gestion des partages')

@section('content')

    {{-- Info card --}}
    <div class="row mb-2">
        <div class="col-12">
            <div class="card bg-light mb-3">
                <div class="card-header bg-info text-white">
                    <i class="fa fa-info-circle"></i> Informations
                </div>
                <div class="card-body">
                    <p class="card-text">
                        Les groupes de droits sont au cœur de l’AGDLP (ou AGUDLP). En gérant les droits via des groupes
                        (plutôt que par utilisateur), on simplifie l’administration, on renforce la sécurité et on garde des
                        accès cohérents dans le temps.
                    </p>
                </div>
            </div>
        </div>
    </div>

    {{-- Barre d’actions + filtre OU --}}
    <div class="row mb-3 g-3">
        <div class="col-md-8">
            <div class="card h-100">
                <div class="card-body">
                    <form role="form" action="{{ route('partage.index') }}" method="GET" autocomplete="off">
                        @csrf
                        <div class="row g-2 align-items-end">
                            <div class="col">
                                <label class="form-label">Organisation</label>
                                <div class="input-group">
                                    <label class="input-group-text" for="inputGroupSelect01">OU</label>
                                    <select class="form-select" name="search_ou" id="inputGroupSelect01">
                                        @foreach ($ouList as $ou)
                                            <option value="{{ $ou->getDn() }}"
                                                {{ $ou->getDn() == $selectedOu ? 'selected' : '' }}>
                                                {{ $ou->getName() }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-auto">
                                <button class="btn btn-outline-secondary" type="submit" id="button-addon2">
                                    Filtrer
                                </button>
                            </div>
                        </div>
                    </form>
                </div> <!-- /card-body -->
            </div>
        </div>

        <div class="col-md-4 d-flex">
            <div class="card flex-fill">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div>
                        <div class="fw-bold mb-1">Actions rapides</div>
                        <div class="text-muted small">Créer un groupe de droit GL (RW/RO)</div>
                    </div>
                    <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#createGlModal">
                        <i class="fa-solid fa-plus me-1"></i> Nouveau GL
                    </button>
                </div>
            </div>
        </div>
    </div>

    @if ($listgroup->count() > 0)
        <div class="row">
            <div class="col-12">
                <div class="card">

                    <div class="card-body">
                        <table id="listgroups" class="table table-bordered table-hover align-middle">
                            <thead>
                                <tr>
                                    <th>Nom (CN)</th>
                                    <th>Description</th>
                                    <th>Membres</th>
                                    <th>Créé le</th>
                                    <th class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($listgroup as $value)
                                    @php
                                        $dn = $value->getDn();
                                        $dnB64 = rtrim(strtr(base64_encode($dn), '+/', '-_'), '=');
                                    @endphp
                                    <tr>
                                        <td>
                                            <i class="fa-solid fa-users-between-lines me-1"></i>
                                            <a href="{{ url($url . $dn) }}">{{ $value->cn[0] }}</a>
                                        </td>
                                        <td>{{ $value->description[0] ?? '' }}</td>
                                        <td>{{ $value->members()->get()->count() }}</td>
                                        <td>{{ $value->whencreated }}</td>
                                        <td class="text-end">
                                            <a href="{{ route('group.view', $dn) }}"
                                                class="btn btn-sm btn-outline-secondary">
                                                <i class="fa-solid fa-pen-to-square me-1"></i> Modifier
                                            </a>
                                            <form action="{{ route('group.remove', $dn) }}" method="POST" class="d-inline"
                                                onsubmit="return confirm('Supprimer ce GL ?');">
                                                @csrf @method('DELETE')
                                                <button class="btn btn-outline-danger btn-sm">Supprimer</button>
                                            </form>

                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th>Nom (CN)</th>
                                    <th>Description</th>
                                    <th>Membres</th>
                                    <th>Créé le</th>
                                    <th class="text-end">Actions</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>

            <div class="col-3">
                <div id="resultats_shortview"></div>
            </div>
        </div>
    @endif


    {{-- MODAL CRÉATION GL (segments dynamiques + toggle RW/RO) --}}
    @php
        $defaultEntity = str_contains($selectedOu, 'OU=DSDEN77')
            ? 'DSDEN77'
            : (str_contains($selectedOu, 'OU=DSDEN93')
                ? 'DSDEN93'
                : (str_contains($selectedOu, 'OU=DSDEN94')
                    ? 'DSDEN94'
                    : 'RECTORAT'));
        $entities = config('admanager.entities');
        $entityCodes = config('admanager.entity_codes');
        $glPrefix = config('admanager.prefix_gl', 'GL');
    @endphp

    <div class="modal fade" id="createGlModal" tabindex="-1" aria-labelledby="createGlLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <form method="POST" action="{{ route('group.creategl') }}" id="gl-create-form">
                    @csrf

                    <div class="modal-header">
                        <div>
                            <h5 class="modal-title" id="createGlLabel">
                                <i class="fa-solid fa-folder-tree me-2"></i> Nouveau groupe GL
                            </h5>
                            <div class="text-muted small">Construit le CN à partir de l’entité, des segments d’arborescence
                                et du mode d’accès.</div>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
                    </div>

                    <div class="modal-body">
                        <div class="row g-3">
                            {{-- Colonne gauche : Entité + Accès / Toggle --}}
                            <div class="col-lg-5">
                                <div class="card border-0 shadow-sm h-100">
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <label class="form-label">Entité</label>
                                            <select class="form-select" name="entity" id="gl-entity" required>
                                                @foreach ($entities as $code => $info)
                                                    <option value="{{ $code }}" @selected($code === $defaultEntity)>
                                                        {{ $info['label'] }}</option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="mb-2">
                                            <label class="form-label">Accès</label>
                                            <select class="form-select" name="access" id="gl-access" required>
                                                <option value="RW">RW (Lecture/Écriture)</option>
                                                <option value="RO">RO (Lecture seule)</option>
                                            </select>
                                        </div>

                                        <div class="form-check form-switch d-flex align-items-center mt-3">
                                            <input class="form-check-input me-2" type="checkbox" id="gl-create-both"
                                                name="create_both" value="1">
                                            <label class="form-check-label" for="gl-create-both">
                                                Créer <strong>les deux groupes</strong> (RW & RO)
                                            </label>
                                        </div>
                                        <div class="form-text">
                                            Si activé, les deux groupes seront créés avec les mêmes segments (suffixes
                                            <code>_RW</code> et <code>_RO</code>).
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Colonne droite : Segments + Aperçu --}}
                            <div class="col-lg-7">
                                <div class="card border-0 shadow-sm h-100">
                                    <div class="card-body">
                                        <label class="form-label d-flex justify-content-between align-items-center">
                                            <span>Segments (arborescence des dossiers)</span>
                                            <button class="btn btn-sm btn-outline-secondary" type="button"
                                                id="add-seg">
                                                <i class="fa-solid fa-plus me-1"></i> Ajouter un dossier
                                            </button>
                                        </label>

                                        <div id="seg-list" class="d-grid gap-2 mb-3">
                                            {{-- ligne initiale --}}
                                            <div class="input-group seg-row">
                                                <span class="input-group-text"><i class="fa-solid fa-folder"></i></span>
                                                <input type="text" name="segments[]" class="form-control seg-input"
                                                    placeholder="Ex : CP1D">
                                                <button class="btn btn-outline-danger remove-seg" type="button"
                                                    title="Supprimer">
                                                    <i class="fa-solid fa-xmark"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <div class="form-text mb-3">
                                            Ajoute autant de niveaux que nécessaire (ex. <code>CP1D</code> → <code>MI</code>
                                            → <code>Maternelle</code>).
                                            Au moins un segment est requis.
                                        </div>

                                        {{-- Aperçu CN élégant --}}
                                        <div class="card bg-light border-0">
                                            <div class="card-body py-2">
                                                <div class="small text-muted">Aperçu du/des CN généré(s)</div>
                                                <div id="gl-cn-preview">
                                                    <div class="placeholder-glow">
                                                        <span class="placeholder col-8"></span>
                                                    </div>
                                                </div>
                                                <div class="mt-2 d-flex gap-2">
                                                    <button type="button" class="btn btn-sm btn-outline-secondary"
                                                        id="copy-cn">
                                                        <i class="fa-regular fa-copy me-1"></i> Copier
                                                    </button>
                                                    <button type="button" class="btn btn-sm btn-outline-secondary"
                                                        id="reset-segs">
                                                        <i class="fa-solid fa-rotate-left me-1"></i> Réinitialiser les
                                                        segments
                                                    </button>
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div> {{-- /row --}}
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Annuler</button>
                        <button type="submit" class="btn btn-success" id="gl-submit-btn">
                            <i class="fa-solid fa-floppy-disk me-1"></i> Créer
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>



@endsection

@section('scriptjs')
    <script type="module">
        // === DataTables (si déjà présent, garde tel quel) ===
        let table = $("#listgroups").DataTable({
            responsive: true,
            lengthChange: true,
            autoWidth: false,
            buttons: ["copy", "csv", "excel", "pdf", "print"]
        });
        table.buttons().container().appendTo('#listgroups_wrapper .col-md-6:eq(0)');

        // === Segments dynamiques + Aperçu CN + Toggle "Créer les deux" ===
        const entityCodes = @json($entityCodes);
        const glPrefix = @json($glPrefix);

        const $entity = document.getElementById('gl-entity');
        const $access = document.getElementById('gl-access');
        const $createBoth = document.getElementById('gl-create-both');
        const $segList = document.getElementById('seg-list');
        const $addSeg = document.getElementById('add-seg');
        const $preview = document.getElementById('gl-cn-preview');
        const $copyBtn = document.getElementById('copy-cn');
        const $resetBtn = document.getElementById('reset-segs');
        const $form = document.getElementById('gl-create-form');
        const $submit = document.getElementById('gl-submit-btn');

        function cleanSeg(v) {
            return (v || '').trim().replace(/\s+/g, '_');
        }

        function getSegments() {
            return Array.from($segList.querySelectorAll('.seg-input'))
                .map(i => cleanSeg(i.value))
                .filter(v => v.length > 0);
        }

        function ensureAtLeastOneRow() {
            if ($segList.querySelectorAll('.seg-row').length === 0) {
                addSegRow('');
            }
        }

        function addSegRow(value = '') {
            const row = document.createElement('div');
            row.className = 'input-group seg-row';
            row.innerHTML = `
      <span class="input-group-text"><i class="fa-solid fa-folder"></i></span>
      <input type="text" name="segments[]" class="form-control seg-input" placeholder="Ex : MI" value="${value}">
      <button class="btn btn-outline-danger remove-seg" type="button" title="Supprimer">
        <i class="fa-solid fa-xmark"></i>
      </button>`;
            $segList.appendChild(row);
            row.querySelector('.seg-input').addEventListener('input', updatePreview);
            row.querySelector('.remove-seg').addEventListener('click', () => {
                row.remove();
                ensureAtLeastOneRow();
                updatePreview();
            });
        }

        function buildCNs() {
            const code = entityCodes[$entity.value] ?? $entity.value;
            const segs = getSegments();
            if (segs.length === 0) return [];
            const base = glPrefix + code + '_' + segs.join('_') + '_';
            if ($createBoth.checked) {
                return [base + 'RW', base + 'RO'];
            }
            return [base + $access.value];
        }

        function updatePreview() {
            const cns = buildCNs();
            let html = '';
            if (cns.length === 0) {
                html = `<span class="text-muted">— Renseigne au moins un segment</span>`;
            } else if (cns.length === 1) {
                html = `<span class="badge text-bg-secondary me-2">sera créé</span><code>${cns[0]}</code>`;
            } else {
                html = cns.map((cn, idx) => `
        <div class="mb-1">
          <span class="badge ${idx===0 ? 'text-bg-success' : 'text-bg-info'} me-2">sera créé</span>
          <code>${cn}</code>
        </div>
      `).join('');
            }
            $preview.innerHTML = html;

            // Bouton submit : libellé dynamique
            if ($createBoth.checked) {
                $submit.innerHTML = `<i class="fa-solid fa-floppy-disk me-1"></i> Créer les 2 groupes`;
                // Désactive le select "Accès" pour éviter la confusion
                $access.setAttribute('disabled', 'disabled');
            } else {
                $submit.innerHTML = `<i class="fa-solid fa-floppy-disk me-1"></i> Créer`;
                $access.removeAttribute('disabled');
            }
        }

        $addSeg?.addEventListener('click', () => {
            addSegRow('');
            updatePreview();
        });
        $entity?.addEventListener('change', updatePreview);
        $access?.addEventListener('change', updatePreview);
        $createBoth?.addEventListener('change', updatePreview);

        // init pour la ligne initiale
        $segList.querySelectorAll('.seg-input').forEach(i => i.addEventListener('input', updatePreview));
        $segList.querySelectorAll('.remove-seg').forEach(b => b.addEventListener('click', (e) => {
            e.currentTarget.closest('.seg-row').remove();
            ensureAtLeastOneRow();
            updatePreview();
        }));

        // Copier l’aperçu
        $copyBtn?.addEventListener('click', async () => {
            const cns = buildCNs();
            if (cns.length === 0) return;
            await navigator.clipboard.writeText(cns.join('\n'));
            toastr.success('CN copié dans le presse-papiers.');
        });

        // Reset segments
        $resetBtn?.addEventListener('click', () => {
            $segList.innerHTML = '';
            addSegRow('');
            updatePreview();
        });

        // Validation simple : au moins 1 segment
        $form?.addEventListener('submit', (e) => {
            if (getSegments().length < 1) {
                e.preventDefault();
                toastr.warning("Ajoute au moins un segment (ex: CP1D).");
            }
        });

        // Init à l’ouverture du modal
        const createModal = document.getElementById('createGlModal');
        if (createModal) {
            createModal.addEventListener('shown.bs.modal', updatePreview);
        }
    </script>


@stop
