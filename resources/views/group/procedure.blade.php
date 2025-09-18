@extends('layouts.app')
@section('icon', 'fa-solid fa-users-between-lines')
@section('h1', 'Procédure — Mise en place des GL sur un répertoire')

@section('content')
    <div class="row g-4">
        <div class="col-12 col-lg-3">
            <div class="card sticky-top" style="top: 88px;">
                <div class="card-body">
                    <h6 class="text-uppercase text-muted">Sommaire</h6>
                    <div class="list-group small" id="toc">
                        <a class="list-group-item list-group-item-action" href="#agdpl">Rappel AGDLP</a>
                        <a class="list-group-item list-group-item-action" href="#contexte">Contexte & arborescence</a>
                        <a class="list-group-item list-group-item-action" href="#concepts">Droits de partage vs NTFS</a>
                        <a class="list-group-item list-group-item-action" href="#procedure">Procédure pas‑à‑pas</a>
                        <a class="list-group-item list-group-item-action" href="#etape2">Étape 2 — Cas de figure NTFS</a>
                        <a class="list-group-item list-group-item-action" href="#checklist">Checklist & pièges</a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-lg-9">

            <div id="agdpl" class="card border-info mb-3">
                <div class="card-header bg-info text-white">Rappel : la méthode AGDLP (et son objectif)</div>
                <div class="card-body">
                    <p class="mb-2"><strong>AGDLP</strong> = <em>Accounts → Global → Domain Local → Permissions</em>.</p>
                    <ul class="mb-2">
                        <li><strong>Accounts</strong> (comptes utilisateurs) deviennent membres de <strong>GG</strong>
                            (Groupes <em>Globaux</em>) par <em>rôle</em> / équipe / service.</li>
                        <li>Ces <strong>GG</strong> sont ajoutés comme membres des <strong>GL</strong> (Groupes <em>Locaux
                                au domaine</em>) porteurs des droits sur les <em>ressources</em> (dossiers, partages).</li>
                        <li>Les <strong>GL</strong> reçoivent les <strong>permissions NTFS</strong> sur les répertoires
                            cibles.</li>
                    </ul>
                    <p class="mb-0"><strong>Objectif :</strong> séparer <em>qui</em> (GG) de <em>quoi</em> (GL), appliquer
                        le <em>moindre privilège</em>, faciliter la maintenance (ajouter/retirer des personnes = changer les
                        GG, <em>sans toucher</em> aux ACL), et garder une nomenclature claire (_RO / _RW).</p>
                </div>
            </div>

            <h2 id="contexte" class="mt-3">Contexte & arborescence</h2>
            <p>Chaque entité (ex. <code>DSDEN77</code>) possède un répertoire racine. On <strong>n’y écrit pas</strong> (pas
                de création/suppression) ; il expose les dossiers « Racine » : <em>Divisions</em>, <em>Services</em> (sans
                division) et <em>Missions</em>.</p>
            <pre class="bg-light p-3 rounded small mb-3"><code>
\\Srv-Fichiers\Partages\DSDEN77\
├─ DFSM\        ← Répertoire de division = navigation uniquement (pas d’écriture à la racine)
│  ├─ DFSM1\      ← <b>répertoire de service</b> (droits effectifs pour les agents de DFSM1)
│  ├─ DFSM2\     ← <b>répertoire de service</b> (droits effectifs pour les agents de DFSM2)
│  ├─ Cadre\     ← <b>répertoire de service</b> (droits effectifs pour les cadres)
│  └─ ...
├─ Service RH\            ← <b>répertoire de service</b>
└─ Mission Numérique\     ← <b>répertoire de service</b>
</code></pre>
            <ul>
                <li><strong>Division</strong> : dossier « contenant » ; les agents <em>voient</em> les sous‑dossiers mais ne
                    peuvent pas écrire à la racine.</li>
                <li><strong>Service / Mission</strong> (dans une division ou à la racine) : c’est <em>ici</em> que l’on
                    applique les GL et que les agents travaillent.</li>
            </ul>

            <h2 id="concepts" class="mt-4">Droits de partage vs NTFS</h2>
            <div class="row g-3">
                <div class="col-md-6">
                    <div class="card h-100">
                        <div class="card-header">Droits de <strong>partage</strong> SMB</div>
                        <div class="card-body">
                            <ul class="mb-0">
                                <li>Portent sur l’<em>accès réseau</em> (SMB).</li>
                                <li>Niveaux : <code>Lecture</code>, <code>Modification</code> (Change), <code>Contrôle
                                        total</code>.</li>
                                <li>Les droits effectifs = <strong>min(partage, NTFS)</strong>.</li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card h-100">
                        <div class="card-header">Droits <strong>NTFS</strong></div>
                        <div class="card-body">
                            <ul class="mb-0">
                                <li>Portent sur le <em>système de fichiers</em> (local).</li>
                                <li>Granularité fine (héritage, portée, permissions détaillées).</li>
                                <li><strong>Recommandation :</strong> partager de façon permissive et gouverner via NTFS.
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <div class="alert alert-secondary mt-3">
                <strong>Politique conseillée :</strong> Partage <em>permissif</em> (Admins : Contrôle total ; Authenticated
                Users : Change/Full) et <strong>contrôle effectif via NTFS</strong> avec les GL. Ainsi, pas de double goulot
                d’étranglement.
            </div>

            <h2 id="procedure" class="mt-4">Procédure pas‑à‑pas</h2>

            <div class="card mb-3">
                <div class="card-header">Étape 0 — Pré‑requis</div>
                <div class="card-body">
                    <ul class="mb-0">
                        <li>Identifier le <strong>chemin</strong> exact (ex.
                            <code>D:\\Partages\\DSDEN77\\CP1D\\Maternelle</code>).
                        </li>
                        <li>Créer la paire de <strong>GL</strong> correspondante (<code>..._RO</code>/<code>..._RW</code>).
                            <em>Les membres des GL : uniquement des GG.</em>
                        </li>
                        <li>Vérifier l’appartenance des utilisateurs dans les <strong>GG</strong> adéquats.</li>
                    </ul>
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-header">Étape 1 — Créer le répertoire de service</div>
                <div class="card-body">
                    <p>À l’intérieur de la <em>Division</em> (ou directement à la racine entité pour un service/mission),
                        créer le dossier cible. Les dossiers de <strong>Division</strong> restent en <em>lecture seule</em>
                        pour les agents.</p>
                    <p class="mb-0 text-muted small">Astuce : nommer le dossier comme le segment final de la paire GL (ex.
                        <code>Maternelle</code> pour <code>..._Maternelle_RO/RW</code>).
                    </p>
                </div>
            </div>

            <h2 id="etape2" class="mt-4">Étape 2 — Appliquer les ACL <strong>NTFS</strong> (cas de figure)</h2>

            <div class="alert alert-light border">
                <strong>Chemin d’accès dans l’interface :</strong> <em>Clic droit sur le dossier → Propriétés → Sécurité →
                    Avancé</em>.<br>
                <strong>Action initiale :</strong> cliquer sur <em>« Désactiver l’héritage »</em> puis <em>« Convertir les
                    autorisations héritées en autorisations explicites »</em>.
            </div>

            <div class="row g-3">
                <div class="col-12">
                    <div class="card h-100 border-success">
                        <div class="card-header bg-success text-white">Cas A — Répertoire de <strong>Service</strong>
                            (standard)</div>
                        <div class="card-body">
                            <p><em>Lieu où les agents travaillent.</em> Appliquer les droits suivants :</p>
                            <ol class="mb-2">
                                <li>Conserver <code>SYSTEM</code> et <code>Domain Admins</code> → <strong>Contrôle
                                        total</strong> (<em>Ce dossier, sous-dossiers et fichiers</em>).</li>
                                <li>Ajouter <code>CREATOR OWNER</code> → <strong>Modification</strong> (<em>Sous-dossiers et
                                        fichiers uniquement</em>).</li>
                                <li>Ajouter <code>GL..._RW</code> → <strong>Modification</strong> (<em>Ce dossier,
                                        sous-dossiers et fichiers</em>).</li>
                                <li>Ajouter <code>GL..._RO</code> → <strong>Lecture & Exécution</strong> (<em>Ce dossier,
                                        sous-dossiers et fichiers</em>).</li>
                                <li>Supprimer <code>Users</code>/<code>Everyone</code> si présents.</li>
                            </ol>
                            <p class="small text-muted mb-0"><strong>Où cliquer :</strong> Propriétés → Sécurité → Avancé →
                                <em>Ajouter</em> → <em>Sélectionner un principal</em> (saisir le groupe) → <em>Afficher les
                                    autorisations de base</em> (ou <em>Avancé</em> pour les portées) → <em>Appliquer à</em>
                                : choisir la portée (<em>Ce dossier, sous-dossiers et fichiers</em> ou <em>Sous-dossiers et
                                    fichiers uniquement</em>), puis <em>OK</em>.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="col-12">
                    <div class="card h-100 border-warning">
                        <div class="card-header bg-warning">Cas B — Dossier de <strong>Division</strong> (conteneur
                            « browse‑only »)</div>
                        <div class="card-body">
                            <p><em>Les agents ne doivent pas écrire ici, mais doivent voir les sous‑dossiers
                                    (services).</em></p>
                            <ol class="mb-2">
                                <li>Conserver <code>SYSTEM</code> et <code>Domain Admins</code> → <strong>Contrôle
                                        total</strong> (<em>Ce dossier, sous-dossiers et fichiers</em>).</li>
                                <li>(Option recommandé) Ajouter un groupe de navigation (ex. <code>Authenticated
                                        Users</code> ou un <code>GL..._BROWSE</code>) → <strong>Lecture & Exécution</strong>
                                    avec <strong>portée</strong> : <em>Ce dossier uniquement</em> <span
                                        class="text-muted">(ou « Cette clé uniquement » selon version)</span>.</li>
                                <li>Vérifier qu’<strong>aucune</strong> ACE n’accorde d’écriture à ce niveau. <em>Ne pas
                                        utiliser de « Refuser »</em> ; préférer la <strong>portée</strong> adéquate.</li>
                            </ol>
                            <p class="small text-muted mb-0"><strong>Où cliquer :</strong> Propriétés → Sécurité → Avancé →
                                <em>Ajouter</em> → groupe de navigation → cocher <em>Lecture & Exécution</em> →
                                <em>Appliquer à</em> : <strong>Ce dossier uniquement</strong> → <em>OK</em>.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="col-12">
                    <div class="card h-100 border-primary">
                        <div class="card-header bg-primary text-white">Cas C — <strong>Service/Mission à la racine</strong>
                            de l’entité</div>
                        <div class="card-body">
                            <p><em>Comportement identique à un service dans une division.</em> Appliquer le <strong>Cas
                                    A</strong> sur ce dossier.</p>
                            <ul class="mb-0 small">
                                <li>Assurer que le dossier racine d’entité (<code>\\...\\DSDEN77</code>) reste en navigation
                                    uniquement (cf. Cas B).</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="col-12">
                    <div class="card h-100 border-secondary">
                        <div class="card-header bg-light">Cas D — Dossier existant « hérité » à nettoyer</div>
                        <div class="card-body">
                            <ol class="mb-2">
                                <li><strong>Désactiver l’héritage</strong> → <em>Convertir</em>.</li>
                                <li>Retirer les ACE larges (<code>Users</code>/<code>Everyone</code>).</li>
                                <li>Appliquer le <strong>Cas A</strong> (ou B selon le niveau).</li>
                            </ol>
                            <p class="small text-muted mb-0">Toujours tester après nettoyage (un compte RO et un compte RW).
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- <div class="card my-4">
                <div class="card-header">Étape 3 — Partage SMB (si partage dédié requis)</div>
                <div class="card-body">
                    <p><strong>Chemin UI :</strong> <em>Propriétés → Partage → Partage avancé…</em> → cocher <em>Partager ce
                            dossier</em> → <em>Autorisations</em>.</p>
                    <ul class="mb-0">
                        <li><strong>Recommandé :</strong> Admins → Contrôle total ; <em>Authenticated Users</em> →
                            <strong>Modification</strong> (ou <strong>Contrôle total</strong>). Les GL pilotent via NTFS.
                        </li>
                        <li>Nom de partage : optionnellement masqué (ex. <code>Maternelle$</code>).</li>
                    </ul>
                </div>
            </div> --}}

            <div class="card my-4">
                <div class="card-header">Étape 3 — Vérifications</div>
                <div class="card-body">
                    <ul class="mb-0">
                        <li><strong>Compte RO</strong> : lister/ouvrir OK ; création/suppression refusées.</li>
                        <li><strong>Compte RW</strong> : création, modification, suppression OK.</li>
                        <li><strong>Division</strong> : à la racine, impossible d’écrire ; les sous‑dossiers service sont
                            visibles.</li>
                    </ul>
                </div>
            </div>

            <h2 id="checklist" class="mt-4">Checklist & pièges</h2>
            <ul>
                <li><strong>Pas de « Refuser »</strong> sauf cas exceptionnel ; utiliser la <strong>portée</strong> correcte
                    (Ce dossier / Sous‑dossiers & fichiers…).</li>
                <li>Vérifier systématiquement l’onglet <strong>Avancé</strong> → <em>Héritage</em> et <em>Appliquer à</em>.
                </li>
                <li>Créer <strong>toujours</strong> la paire GL <code>_RO</code>/<code>_RW</code> et n’y mettre que des
                    <strong>GG</strong>.
                </li>
                <li>Documenter le lien dossier ⇄ GL dans ADManager (description, champs dédiés si dispo).</li>
            </ul>

        </div>
    </div>
@endsection

@section('scriptjs')
    <script type="module">
        // Rien de spécial ici pour l’instant
    </script>
@endsection
