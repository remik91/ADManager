<?php

namespace App\Http\Controllers;

use DateTime;
use App\Ldap\AD\OrgAD;
use App\Ldap\AD\UserAD;
use App\Ldap\AD\GroupAD;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Ldap\ORACLE\UserLDAP;
use Illuminate\Validation\Rule;
use LdapRecord\LdapRecordException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Spatie\Activitylog\Models\Activity;


class Utilisateur extends Controller
{
    public function index(Request $request)
    {
        // Étape 1 : Vérifier la requête
        $search_ou = $request->input('search_ou');
        $search_uid = $request->input('search_uid');
        $typeOfAccount = $request->input('typeOfAccount');
        $dateFilter = $request->input('dateFilter');
        $filterStatus = $request->input('filterStatus');
        $filterSynchro = $request->input('FilterSynchro');

        $services = GroupAD::in("OU=Services,DC=ad,DC=ac-creteil")->get();

        if (!$search_ou && !$search_uid && !$typeOfAccount) {
            return view('user.index_user', [
                'ouList' => OrgAD::query()->in("OU=Utilisateurs,DC=ad,DC=ac-creteil")->where('objectclass', 'organizationalUnit')->get(['dn', 'name']),
                'listusers' => [],
                'selectedOu' => null,
                'searchText' => null,
                'services' => $services
            ]);
        }

        // Étape 2 : Interroger la base de données
        $users = UserAD::query();

        if ($search_ou) {
            $users->in($search_ou);
        }

        if ($search_uid) {
            $users->where('samaccountname', 'contains', $search_uid);
        }

        if ($typeOfAccount) {
            $users->where('typeOfAccount', '=', $typeOfAccount);
        }

        if ($filterStatus) {
            $users->where('userAccountControl', '=', $filterStatus);
        }

        if ($filterSynchro) {
            $users->where('SyncToLDAP', '=', $filterSynchro);
        }

        if ($dateFilter) {
            $date = new DateTime();

            switch ($dateFilter) {
                case 'today':
                    $timestamp = $date->format('U') * 10000000 + 116444736000000000;
                    $users->where('lastLogon', '>=', $timestamp);
                    break;
                case 'thisWeek':
                    $date->modify('last Monday');
                    $timestamp = $date->format('U') * 10000000 + 116444736000000000;
                    $users->where('lastLogon', '>=', $timestamp);
                    break;
                case 'thisMonth':
                    $date->modify('first day of this month');
                    $timestamp = $date->format('U') * 10000000 + 116444736000000000;
                    $users->where('lastLogon', '>=', $timestamp);
                    break;
                case 'lastMonth':
                    $become = new DateTime('first day of last month');
                    $after = new DateTime('last day of last month');
                    $timestamp = [
                        $become->format('U') * 10000000 + 116444736000000000,
                        $after->format('U') * 10000000 + 116444736000000000
                    ];
                    $users->whereBetween('lastLogon', $timestamp);
                    break;
                case 'lastYear':
                    $date->modify('-1 year');
                    $timestamp = $date->format('U') * 10000000 + 116444736000000000;
                    $users->where('lastLogon', '<=', $timestamp);
                    break;
                default:
                    break;
            }
        }

        $listusers = $users->get();

        // Étape 3 : Renvoyer la vue
        return view('user.index_user', [
            'ouList' => OrgAD::query()->in("OU=Utilisateurs,DC=ad,DC=ac-creteil")->where('objectclass', 'organizationalUnit')->get(['dn', 'name']),
            'listusers' => $listusers,
            'selectedOu' => $search_ou,
            'searchText' => $search_uid,
            'services' => $services
        ]);
    }

    public function corbeille(Request $request)
    {
        // Étape 1 : Interroger la base de données
        $listusers = UserAD::query()->whereDeleted()->get();


        if ($request->dnuser) {
            $restauruser = $request->dnuser;
            $user = UserAD::whereDeleted()
                ->where('distinguishedname', '=', $restauruser)
                ->first();
            try {
                if ($user->isDeleted()) {
                    $user->restore();
                }
                activity()->log("Le compte {$user->getName()} a été restauré.");
                return back()->with('message', "Le compte {$user->getName()} a été restauré.");
            } catch (LdapRecordException $ex) {
                return back()->with('message', "Une erreur est survenue pour {$user->getName()} : " . $ex->getMessage());
            }
        }

        // Étape 3 : Renvoyer la vue
        return view('user.corbeille', [
            'listusers' => $listusers,
        ]);
    }


    public function search(Request $request)
    {
        $ouName = "OU=Utilisateurs,DC=ad,DC=ac-creteil";
        $username = $request->input('searchuid');
        if ($request) {
            $users = UserAD::query()
                ->where('objectclass', '=', 'user')
                ->in($ouName);
            if ($username) {
                $users->where('samaccountname', '=', $username);
            }

            $user = $users->get()->first();

            if (!$user) {
                return redirect()->route('user.index', ['search_id' => $username]);
            }
            return redirect()->route('user.view', ['id' => $user->getDn()]);
        }
    }

    public function view(Request $request)
    {
        $user = UserAD::find($request->id);

        if ($user->exists()) {
            $initial = $user->getInitials();
            $ramdompassword = Str::random(11) . "$";

            $group = GroupAD::in('OU=Groupes,DC=ad,DC=ac-creteil');
            $groups = $group->get();

            $service = GroupAD::in("OU={$user->getParentName()},OU=Services,DC=ad,DC=ac-creteil")->get();


            $ouName = "OU=Utilisateurs,DC=ad,DC=ac-creteil";
            $ouList = OrgAD::in($ouName)->get(['dn', 'name']);

            $groupsuser = $user->groups()->get();

            $SoftphonieGroup = GroupAD::find('CN=OpenTouchSoftphonie,OU=Groupes,DC=ad,DC=ac-creteil');

            $stockmanager = null;
            if (env("ACTIVE_STOCKM")) {
                $response = Http::withoutVerifying()->get(env('URL_STOCKMANAGER') . "/usager/getuserapi?q=" . $user->getName());
                $stockmanager = $response->json();
            }

            return view('user.view_profile', [
                'user' => $user,
                'initial' => $initial,
                'ouList' => $ouList,
                'ramdompass' => $ramdompassword,
                'allGroups' => $groups,
                'allServices' => $service,
                'groupsuser' => $groupsuser,
                'softphonie' => $SoftphonieGroup,
                'stockmanager' => $stockmanager
            ]);
        } else {
            return view('user.view_profile', ['user' => $user]);
        }
    }

    public function view_short(Request $request)
    {
        $user = UserAD::find($request->id);

        return view('user.shortview', ['user' => $user]);
    }

    public function create(Request $request)
    {

        if (UserAD::where('samaccountname', '=', $request->uid)->get()->exists()) {
            return redirect()->route('user.import')->with('error', "L'utilisateur existe déjà");
        }

        $user = (new UserAD)->inside($request->ou);
        // dd($request->synctoldap);
        $user->cn = $request->uid;
        $user->sn = $request->sn;
        $user->unicodePwd = '%Rectorat94*';
        $user->samaccountname = $request->uid;
        $user->userPrincipalName = $request->uid . "@ad.ac-creteil";
        $user->name = $request->uid;
        $user->givenName = $request->givenName;
        $user->displayname = $request->givenName . " " . $request->sn;
        $user->mail = $request->mail;
        $user->department = $request->department;
        $user->division = $request->division;
        $user->title = $request->title;
        $user->physicalDeliveryOfficeName = $request->physicalDeliveryOfficeName;
        $user->SyncToLDAP = $request->synctoldap;
        $user->typeOfAccount = $request->typeOfAccount;
        //$user->initials = $user->initials();

        if ($request->repperso) {
            $user->homeDirectory = "\\\\ad.ac-creteil\\Perso\\home\\" . $request->uid;
            $user->homeDrive = "H:";
        }

        //Création et sauvegarde
        try {
            $user->save();

            // Sync the created users attributes.
            $user->refresh();

            // Enable the user.
            $user->unicodePwd = '%Rectorat94*';
            $user->userAccountControl = 576;

            $message = "L'utilisateur {$user->displayname[0]} ({$user->samaccountname[0]}) a été créer avec succès.";
            try {
                $user->save();
                activity()->log($message);
                return redirect()->route('user.view', $user->getDn())->with('message', $message);
            } catch (\LdapRecord\LdapRecordException $e) {
                return redirect()->route('user.index')->with('error', "#2 Impossible de créer l'utilisateur {$user->displayname[0]}, un problème est survenue: " . $e->getMessage());
            }
        } catch (\LdapRecord\LdapRecordException $e) {
            return redirect()->route('user.index')->with('error', "#1 Impossible de créer l'utilisateur {$user->displayname[0]}, un problème est survenue: " . $e->getMessage());
        }
    }

    public function update(Request $request, $type)
    {
        $user = UserAD::find($request->id);
        if ($request->isMethod('post')) {
            try {
                if ($request->type == "general") {
                    $user->fill([
                        'givenname' => $request->givenname,
                        'sn' => $request->sn,
                        'displayname' => $request->displayname,
                        'initials' => $request->initials,
                        'mail' => $request->mail,
                        'description' => $request->description,
                        'typeOfAccount' => $request->typeOfAccount
                    ]);

                    $user->save();

                    $orgad = OrgAD::find($request->orgunit);
                    if (!$user->isChildOf($orgad)) {
                        $user->move($orgad, $deleteOldRdn = true);
                        activity()->log("Mise à jour de l'utilisateur et changement de l'OU: " . $user->getName());
                        return redirect()->route('user.view', $user->getDn())
                            ->with('message', "Sauvegarde des informations et modification de l'unité d'organisation.");
                    }
                } elseif ($request->type == "contact") {
                    $user->fill([
                        'telephoneNumber' => $request->telephoneNumber,
                        'mobile' => $request->mobile,
                        'physicaldeliveryofficename' => $request->physicaldeliveryofficename,
                        'title' => $request->title,
                        'department' => $request->department,
                        'company' => $request->company,
                        'division' => $request->division
                    ]);

                    $user->save();
                }
            } catch (\LdapRecord\LdapRecordException $e) {
                return back()->with('error', "Une erreur est survenue: " . $e->getMessage());
            }
        }
        activity()->log("Mise à jour de l'utilisateur: " . $user->getName());
        return back()->with('message', "Le/les modification(s) du profil utilisateur ont été enregistrée(s)");
    }


    public function remove(Request $request)
    {
        $user = UserAD::find($request->id);
        if (!$user) {
            return back()->with('error', "Impossible de supprimer l'utilisateur : l'utilisateur n'existe pas");
        }
        try {
            $user->delete($recursive = true);
            activity()->log("Suppression de l'utilisateur: " . $user->getName());
            return back()->with('message', "Suppression de l'utilisateur: " . $user->getName());
        } catch (\LdapRecord\LdapRecordException $e) {
            return back()->with('error', "Impossible de supprimer l'utilisateur $user->displayname, un problème est survenue: " . $e->getMessage());
        }
    }

    public function active($id)
    {
        $user = UserAD::find($id);

        if ($user->isEnabled()) {
            $user->desactiveAccount();
            $message = "Le compte " . $user->getName() . " a été désactivé";
        } else if ($user->isDisabled()) {
            $user->activeAccount();
            $message = "Le compte " . $user->getName() . " a été activé";
        }

        $user->save();
        activity()->log($message);
        return back()->with('message', $message);
    }

    public function password(Request $request)
    {
        $user = UserAD::find($request->id);
        $message = '';

        switch ($request->choixradio) {
            case 'aleatoire':
                $user->unicodepwd = $request->ramdompassword;
                $message = "Le mot de passe AD de l'utilisateur a été changé par un mot de passe aléatoire : $request->ramdompassword";
                break;
            case 'motdepasse':
                if ($request->password == $request->confirm_password) {
                    $user->unicodepwd = $request->password;
                    $message = "Le mot de passe AD de l'utilisateur a été changé.";
                } else {
                    return redirect()->route('user.view', $user->getDn())->with('error', "Erreur: Les mots de passe ne sont pas identiques.");
                }
                break;
            case 'datenaissance':
                $user->unicodepwd = 'RK30121993$';
                $message = "Le mot de passe AD a été changé par un mot de passe préformaté";
                break;
            case 'aucun':
                $user->NoPasswordRequired();
                $message = "Le mot de passe a été défini sur Aucun";
                break;
        }

        try {
            //Sauvegarde de la modification
            $user->save();
            activity()->log("Modification du mot de passe de: " . $user->getName());
            return redirect()->route('user.view', $user->getDn())->with('message', $message);
        } catch (\LdapRecord\Exceptions\InsufficientAccessException $ex) {
            $error = $ex->getDetailedError();
            return redirect()->route('user.view', $user->getDn())->with('error', "Permission: " . $error->getErrorMessage());
        } catch (\LdapRecord\LdapRecordException $ex) {
            $error = $ex->getDetailedError();
            return redirect()->route('user.view', $user->getDn())->with('error', "LDAP: " . $error->getErrorMessage());
        }
    }

    public function import(Request $request)
    {
        $ouName = "OU=Utilisateurs,DC=ad,DC=ac-creteil";
        $ouList = OrgAD::query()->in($ouName)->where('objectclass', 'organizationalUnit')->get(['dn', 'name']);

        $userLDAP = null;
        $userAD = null;

        if ($request->dnldap) {
            $userLDAP = UserLDAP::find($request->dnldap);

            $userAD = UserAD::query()
                ->where([
                    ['objectclass', '=', 'user'],
                    ['samaccountname', '=', $userLDAP->uid[0]],
                ])->first();
        }
        return view('user.import', ['ouList' => $ouList, 'userLDAP' => $userLDAP, 'userAD' => $userAD]);
    }

    public function addgroup(Request $request)
    {
        $group = GroupAD::findOrFail($request->dngroup);
        $user = UserAD::find($request->id);

        if ($user->groups()->exists($group)) {
            return back()->with('error', "L'utilisateur est déjà membre de ce groupe");
        }
        if ($user->groups()->attach($group)) {
            activity()->log("Ajout du groupe  " . $group->getName() . " à l'utilisateur: " . $user->getName());
            return back()->with('message', "Le groupe " . $group->getName() . " a été ajouté.");
        } else return back()->with('error', "Une erreur est survenue.");
    }

    public function removegroup(Request $request)
    {
        $group = GroupAD::findOrFail($request->dngroup);

        $user = UserAD::find($request->id);

        if (!$user->groups()->exists($group)) {
            return back()->with('error', "L'utilisateur n'est pas membre de ce groupe");
        }

        if ($user->groups()->detach($group)) {
            activity()->log("Retrait du groupe  " . $group->getName() . " à l'utilisateur: " . $user->getName());
            return back()->with('message', "Le groupe " . $group->getName() . " a été retiré.");
        } else return back()->with('error', "Une erreur est survenue.");
    }

    public function ToggleRepPerso($id)
    {
        $user = UserAD::find($id);

        if ($user->homeDirectory) {
            $user->homeDirectory = "";
            $user->homeDrive = "";
            $message = "Répertoire personnel de  " . $user->getName() . " a été désactivé.";
        } else {
            $user->homeDirectory = "\\\\ad.ac-creteil\\Perso\\home\\" . $user->samaccountname[0];
            $user->homeDrive = "H:";
            $message = "Répertoire personnel de " . $user->getName() . " a été activé.";
        }

        $user->save();
        activity()->log($message);
        return back()->with('message', $message);
    }


    public function searchldap(Request $request)
    {
        // Validate input
        $searchOU = $request->input('ou');
        $searchCrit = $request->input('crit');
        $searchTerm = $request->input('q');

        if (!$searchOU || !$searchCrit || !$searchTerm) {
            return response()->json(['error' => 'Invalid input'], 400);
        }

        // Perform LDAP search
        $usersldap = UserLDAP::query()
            ->in("ou=$searchOU,ou=ac-creteil,ou=education,o=gouv,c=fr")
            ->where($searchCrit, 'starts_with', $searchTerm)
            ->get();

        return view('user.resultsearch', ['usersldap' => $usersldap]);
    }

    public function searchldapimport(Request $request)
    {
        // Whitelist des bases de recherche (labels → base DN)
        $bases = [
            'personnels EN' => 'ou=personnels EN,ou=ac-creteil,ou=education,o=gouv,c=fr',
            'autres'        => 'ou=autres,ou=ac-creteil,ou=education,o=gouv,c=fr',
        ];

        // Validation entrée
        $request->validate([
            'ou'   => ['required', Rule::in(array_keys($bases))],
            'crit' => ['required', Rule::in(['uid', 'sn', 'mail'])],
            'q'    => ['required', 'string', 'min:2', 'max:64'],
            'cookie' => ['nullable', 'string'],
            'per_page' => ['nullable', 'integer', 'min:5', 'max:100'],
        ]);

        $base   = $bases[$request->ou];
        $crit   = $request->crit;
        $term   = trim($request->q);
        $cookie = $request->get('cookie');
        $perPage = (int) $request->integer('per_page', 25);

        // Attributs utiles pour le rendu + import
        $attrs = [
            'cn',
            'uid',
            'sn',
            'givenname',
            'mail',
            'division',
            'department',
            'datenaissance',
            'service',
            'title',
            'fonction',
            'physicalDeliveryOfficeName',
            'bureau',
        ];

        $q = UserLDAP::query()
            ->in($base)
            ->select($attrs);

        // Filtre souple selon le critère
        switch ($crit) {
            case 'uid':
                $q->where('uid', 'starts_with', $term);
                $q->orderBy('uid');
                break;
            case 'mail':
                $q->wherewhereContains('mail', $term);
                $q->orderBy('mail');
                break;
            case 'sn':
                $q->where('sn', 'starts_with', $term);
                $q->orderBy('sn');
                break;
            default:
                // Nom : on cherche dans sn + givenname
                $q->whereContains('sn', $term)
                    ->orWhereContains('givenname', $term);
                $q->orderBy('sn')->orderBy('givenname');
                break;
        }

        // Pagination LDAP via cookie
        $usersldap = $q->paginate($perPage);

        // Le partial a besoin des paramètres pour le bouton "Plus"
        return view('user.resultsearch', [
            'usersldap'  => $usersldap,
            'q'          => $term,
            'ou'         => $request->ou,
            'crit'       => $crit,
            'perPage'    => $perPage,
        ]);
    }

    public function checkldap(Request $request)
    {
        // Valider l'entrée
        $searchTerm = $request->input('q');

        $userADdn = UserAD::findby('samaccountname', $searchTerm)->getDn();

        if (!$searchTerm) {
            return response()->json(['error' => 'Entrée invalide'], 400);
        }

        // Définir les OUs à rechercher
        $ous = ['ou=Personnels EN,ou=ac-creteil,ou=education,o=gouv,c=fr', 'ou=Autres,ou=ac-creteil,ou=education,o=gouv,c=fr'];

        // Initialiser le tableau de résultats
        $userldap = [];



        // Parcourir les OUs
        foreach ($ous as $ou) {
            $result = UserLDAP::query()
                ->in($ou)
                ->where('uid', '=', $searchTerm)
                ->first();

            if ($result) {
                $userldap = $result;
                break; // Si on trouve des résultats, arrêter la boucle
            }
        }
        return view('user.checkldap', ['usersldap' => $userldap, 'userADdn' => $userADdn]);
    }

    public function resynchro(Request $request)
    {
        $ADdn = $request->input('AD');
        $LDAPdn = $request->input('ldap');

        $userAD = UserAD::find($ADdn);
        $userLDAP = UserLDAP::find($LDAPdn);

        try {
            $userAD->fill([
                'mail' => $userLDAP->mail,
                'telephoneNumber' => $userLDAP->telephonenumber,
                'physicaldeliveryofficename' => $userLDAP->bureau,
                'title' => $userLDAP->fonction,
                'department' => $userLDAP->service,
                'division' => $userLDAP->division
            ]);

            $userAD->SyncToLDAP = "TRUE";

            $userAD->save();
            activity()->log("La resynchronisation LDAP=>AD est terminé pour l'utilisateur " . $userAD->displayname[0]);
            return back()->with('message', "La resynchronisation LDAP=>AD est terminé pour l'utilisateur " . $userAD->displayname[0]);
        } catch (\LdapRecord\LdapRecordException $e) {
            return back()->with('error', "Une erreur est survenue: " . $e->getDetailedError()->getDiagnosticMessage());
        }
    }

    public function ToogleSoftphonie(Request $request)
    {
        $isChecked = $request->input('softphonie'); // Remplacez par le nom de votre checkbox
        $user = UserAD::find($request->dnuser);
        $group = GroupAD::find('CN=OpenTouchSoftphonie,OU=Groupes,DC=ad,DC=ac-creteil');

        try {
            if ($isChecked) {
                $user->groups()->attach($group);
                $message = "Ajout du groupe  " . $group->getName() . " à l'utilisateur: " . $user->getName();
            } else {
                $user->groups()->detach($group);
                $message = "Retrait du groupe  " . $group->getName() . " à l'utilisateur: " . $user->getName();
            }
            activity()->log($message);
            return back()->with('message', $message);
        } catch (\LdapRecord\LdapRecordException $e) {
            return back()->with('error', "Une erreur est survenue: " . $e->getDetailedError()->getDiagnosticMessage());
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function finfonction(Request $request)
    {
        if ($request->actualize === "oui") {
            // Rafraîchir le cache en invalidant la clé de cache actuelle
            Cache::forget('cached_users_ff_attribute');
            return redirect()->route('user.finfonction')->with('message', 'Actualisation terminée !');
        }

        try {
            // Vérifier si les données sont en cache
            $cachedUsers = Cache::get('cached_users_ff_attribute');

            // Si les données sont en cache, les récupérer et renvoyer la vue
            if ($cachedUsers !== null) {
                return view('user.finfonction', [
                    'listusers' => $cachedUsers,
                ]);
            }

            // Les données ne sont pas en cache, effectuer la requête
            $listusers = UserAD::in("OU=Utilisateurs,DC=ad,DC=ac-creteil")->get();
            $usersWithFFAttribute = [];

            foreach ($listusers as $user) {
                $userInOtherLDAP = UserLDAP::query()
                    ->in("ou=Personnels EN,ou=ac-creteil,ou=education,o=gouv,c=fr")
                    ->where('uid', '=', $user->getName())
                    ->first();

                if ($userInOtherLDAP && $userInOtherLDAP->finfonction[0] === 'FF') {
                    $usersWithFFAttribute[] = $user;
                }
            }

            // Mettre en cache les résultats pour une durée donnée (par exemple, 2 heure)
            Cache::put('cached_users_ff_attribute', $usersWithFFAttribute, now()->addHour(2));
        } catch (\Exception $e) {
            // Afficher un message d'erreur générique pour l'utilisateur
            return redirect()->route('user.finfonction')->with('error', 'Une erreur est survenue. Veuillez réessayer:' . $e->getMessage());
        }

        // Renvoyer la vue avec les données
        return view('user.finfonction', [
            'listusers' => $usersWithFFAttribute,
        ]);
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function orphelin(Request $request)
    {
        if ($request->actualize === "oui") {
            // Rafraîchir le cache en invalidant la clé de cache actuelle
            Cache::forget('cached_users_orphelin_attribute');
            return redirect()->route('user.orphelin')->with('message', 'Actualisation terminée !');
        }

        try {
            // Vérifier si les données sont en cache
            $cachedUsers = Cache::get('cached_users_orphelin_attribute');

            // Si les données sont en cache, les récupérer et renvoyer la vue
            if ($cachedUsers !== null) {
                return view('user.orphelin', [
                    'listusers' => $cachedUsers,
                ]);
            }

            // Les données ne sont pas en cache, effectuer la requête
            $listusers = UserAD::in("OU=Utilisateurs,DC=ad,DC=ac-creteil")->get();
            $usersWithOrphAttribute = [];

            foreach ($listusers as $user) {
                $userInOtherLDAP = UserLDAP::query()
                    ->in("ou=Personnels EN,ou=ac-creteil,ou=education,o=gouv,c=fr")
                    ->where('uid', '=', $user->getName())
                    ->first();

                if (!$userInOtherLDAP) {
                    $usersWithOrphAttribute[] = $user;
                }
            }

            // Mettre en cache les résultats pour une durée donnée (par exemple, 2 heures)
            Cache::put('cached_users_orphelin_attribute', $usersWithOrphAttribute, now()->addHours(2));
        } catch (\Exception $e) {
            // Afficher un message d'erreur générique pour l'utilisateur
            return redirect()->route('user.orphelin')->with('error', 'Une erreur est survenue. Veuillez réessayer. Détails : ' . $e->getMessage());
        }

        // Renvoyer la vue avec les données
        return view('user.orphelin', [
            'listusers' => $usersWithOrphAttribute,
        ]);
    }


    public function actionList(Request $request)
    {
        // Récupérer les actions depuis la requête
        $action =  $request->get('action');
        $selectedUsers = $request->get('rowCheckbox');

        // Assurez-vous que des utilisateurs ont été sélectionnés
        if (empty($selectedUsers)) {
            return redirect()->back()->with('error', 'Aucun utilisateur sélectionné.');
        }

        // Traitez les actions en fonction de ce qui a été soumis depuis le formulaire
        switch ($action) {
            case 'desactivate':
                // Désactiver les utilisateurs sélectionnés
                foreach ($selectedUsers as $userId) {
                    $user = UserAD::find($userId);
                    $user->desactiveAccount();
                    $user->save();
                    activity()->log($user->getName() . " a été désactivé");
                    // Vous pouvez ajouter des vérifications et des messages de succès ou d'échec ici
                }
                break;

            case 'activate':
                // Activer les utilisateurs sélectionnés
                foreach ($selectedUsers as $userId) {
                    $user = UserAD::find($userId);
                    $user->activeAccount();
                    $user->save();
                    activity()->log($user->getName() . " a été activé");
                    // Vous pouvez ajouter des vérifications et des messages de succès ou d'échec ici
                }
                break;

            case 'delete':
                // Supprimer les utilisateurs sélectionnés
                foreach ($selectedUsers as $userId) {
                    $user = UserAD::find($userId);
                    activity()->log("Suppression de " . $user->getName());
                    $user->delete();
                }
                break;

            case 'ChangeOU':
                // Change d'organisation la liste d'utilisateur
                $orgad = OrgAD::find($request->newou);
                foreach ($selectedUsers as $userId) {
                    $user = UserAD::find($userId);
                    $user->move($orgad);
                    activity()->log($user->getName() . " a été placé dans l'OU " . $orgad->getName());
                }
                break;

            case 'AddGroupe':
                // Ajoute à un groupe la liste d'utilisateur
                $group = GroupAD::findOrFail($request->addgroupe);
                foreach ($selectedUsers as $userId) {
                    $user = UserAD::find($userId);
                    $group = GroupAD::findOrFail($request->addgroupe);
                    $user->groups()->attach($group);
                    activity()->log($user->getName() . " a été ajouté au service " . $group->getName());
                }
                break;

            default:
                // Action non reconnue, redirigez avec un message d'erreur
                return redirect()->back()->with('error', 'Action non valide.');
        }

        // Redirigez avec un message de succès une fois les actions traitées
        return redirect()->back()->with('message', 'Actions traitées avec succès.');
    }

    public function autocomplete(Request $request)
    {
        $ouName = $request->input('ou') ?? "OU=Utilisateurs,DC=ad,DC=ac-creteil";
        $username = $request->input('search', '');

        $userNames = [];

        if ($username !== '') {
            $users = UserAD::query()
                ->where('objectclass', '=', 'user')
                ->in($ouName)
                ->where('samaccountname', 'contains', $username)
                ->get();

            // Extraction des noms des utilisateurs
            $userNames = $users->pluck('name')->flatten()->toArray(); // Assurez-vous d'adapter cette ligne selon la structure de vos données LDAP
        }

        return response()->json($userNames);
    }



    // public function tri(Request $request)
    // {
    //     $userLDAPs = UserLDAP::query()
    //         ->in("ou=Personnels EN,ou=ac-creteil,ou=education,o=gouv,c=fr")
    //         ->where('uid', '=', 'jboehrer')
    //         ->get();
    //     dd($userLDAPs);

    //     foreach ($userLDAPs as $userLDAP) {

    //         $user = UserAD::query()
    //             ->where('samaccountname', 'contains', $userLDAP->uid[0])
    //             ->first();


    //         $orgad = OrgAD::find("OU=DRAFPIC,OU=Utilisateurs,DC=ad,DC=ac-creteil");

    //         if ($user && !$user->isChildOf($orgad)) {
    //             $user->move($orgad, $deleteOldRdn = true);
    //             $rapport[] = "Mise à jour de l'utilisateur et changement de l'OU: " . $user->getName();
    //         }
    //     }

    //     dd($rapport);
    // }


    // public function testldap(Request $request)
    // {
    //     $userLDAPs = UserLDAP::query()
    //         ->in("ou=Personnels EN,ou=ac-creteil,ou=education,o=gouv,c=fr")
    //         ->where('uid', '=', 'rkoutchinski')
    //         ->get();
    //     dd($userLDAPs);
    // }
}