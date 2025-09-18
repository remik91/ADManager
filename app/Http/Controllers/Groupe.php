<?php

namespace App\Http\Controllers;

use App\Ldap\AD\OrgAD;
use App\Ldap\AD\UserAD;
use App\Ldap\AD\GroupAD;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;
use LdapRecord\LdapRecordException;
use LdapRecord\Models\ActiveDirectory\Group;

class Groupe extends Controller
{
    public function index(Request $request)
    {
        $listgroup = GroupAD::query()
            ->where('objectclass', '=', 'group')
            ->in("OU=Groupes,DC=ad,DC=ac-creteil")
            ->get();

        return view('group.index_group', ['listgroup' => $listgroup]);
    }

    public function indexByOu(Request $request, $ouType)
    {
        $search_ou = $request->input('search_ou') ?? session($ouType);

        if ($request->has('search_ou')) {
            session([$ouType => $search_ou]);
        }

        $query = GroupAD::query()
            ->where('objectclass', '=', 'group');

        if ($search_ou) {
            $query->in($search_ou);
        } else {
            $query->in("OU=$ouType,DC=ad,DC=ac-creteil");
        }

        $listgroup = $query->get();

        $url = "/group/view/";

        return view("group.index_$ouType", [
            'ouList' => OrgAD::query()->in("OU=$ouType,DC=ad,DC=ac-creteil")->where('objectclass', 'organizationalUnit')->get(['dn', 'name']),
            'listgroup' => $listgroup,
            'selectedOu' => $search_ou,
            'url' => $url
        ]);
    }

    public function index_services(Request $request)
    {
        return $this->indexByOu($request, 'services');
    }

    public function index_partages(Request $request)
    {
        return $this->indexByOu($request, 'partages');
    }

    public function view($type, Request $request)
    {
        $group = GroupAD::find($request->dn);

        $members = $group->members()->get();

        $membrede = $group->groups()->recursive()->get();

        return view("group.view_group", ['group' => $group, 'members' => $members, 'membrede' => $membrede]);
    }

    public function create_service(Request $request)
    {
        $ouservice = $request->input('OuGroupe');

        $group = (new GroupAD)->inside("OU=$ouservice,OU=Services,DC=ad,DC=ac-creteil");

        $group->cn =  $request->input('nomGroupe');
        $group->samaccountname =  $request->input('nomGroupe');
        $group->description =  $request->input('description');

        try {
            $group->save();
            $group->refresh();
            return redirect()->route('group.view', ['dn' => $group->getDn()]);
        } catch (\LdapRecord\LdapRecordException $e) {

            return back()->with('error', "Une erreur est survenue: " . $e->getDetailedError()->getDiagnosticMessage());
        }
    }

    public function create_partage(Request $request)
    {
        $request->validate([
            'entity'      => ['required', Rule::in(array_keys(config('admanager.entities')))],
            'segments'    => ['required', 'array', 'min:1'],
            'segments.*'  => ['nullable', 'string', 'max:64', 'regex:/^[A-Za-z0-9._\\- ]+$/'],
            'access'      => [Rule::requiredIf(!$request->boolean('create_both')), Rule::in(['RW', 'RO'])],
            'create_both' => ['sometimes', 'boolean'],
        ]);

        $entity   = $request->entity;
        $ouGl     = config("admanager.entities.$entity.ou_gl");
        $segments = array_values(array_filter(array_map('trim', $request->segments ?? [])));
        $createBoth = $request->boolean('create_both');

        if (count($segments) < 1) {
            return back()->with('warning', 'Ajoute au moins un segment.')->withInput();
        }

        $accesses = $createBoth ? ['RW', 'RO'] : [$request->access];

        $created = [];
        $skipped = [];
        $errors  = [];

        foreach ($accesses as $acc) {
            $cn = $this->buildGlCn($entity, $segments, $acc);   // GL<code>_seg1_seg2_..._<RW|RO>
            try {
                // DN cible
                $dn = "CN={$cn},{$ouGl}";

                // Si existe déjà, on skip
                if (GroupAD::find($dn)) {
                    $skipped[] = $cn;
                    continue;
                }

                $gl = (new GroupAD)->inside($ouGl);
                $gl->cn = $cn;
                $gl->samaccountname = $cn;
                // Global Security Group
                $gl->groupType = -2147483646;
                $gl->save();

                $created[] = $cn;
                activity()->log('GL_CREATE :' . $gl->getDn());
            } catch (\Throwable $e) {
                $errors[] = "{$cn} : " . $e->getMessage();
                Log::error('[ADManager][GL_CREATE] ' . $e->getMessage(), ['cn' => $cn, 'ou' => $ouGl]);
            }
        }

        // Feedback utilisateur
        if ($errors) {
            return redirect()->route('gl.index')->with(
                'warning',
                "Créés: " . implode(', ', $created) . ". Déjà présents: " . implode(', ', $skipped) . ". Erreurs: " . implode(' | ', $errors)
            );
        }

        if ($createBoth) {
            return redirect()->route('gl.index')->with('message', "Groupes créés : " . implode(' & ', $created));
        }

        return redirect()->route('gl.index')->with('message', "Groupe {$created[0]} créé.");
    }

    private function buildGlCn(string $entity, array $segments, string $access): string
    {
        $prefix = config('admanager.prefix_gl', 'GL');
        $code   = data_get(config('admanager.entity_codes'), $entity, $entity);
        $clean  = array_map(fn($s) => Str::of($s)->trim()->replace(' ', '_'), $segments);
        return $prefix . $code . '_' . implode('_', $clean) . '_' . $access;
    }


    public function edit(Request $request)
    {
        // Récupérer les données du formulaire
        // Rechercher le groupe par son DN
        $group = GroupAD::find($request->dn);

        if (!$group) {
            return back()->with('error', 'Groupe non trouvé');
        }

        // Vérifier si les champs requis sont présents dans la requête
        if (!$request->has(['cn', 'description'])) {
            return back()->with('error', 'Tous les champs sont requis');
        }

        // Mettre à jour les attributs du groupe
        $group->setAttribute('cn', $request->cn);
        $group->setAttribute('description', $request->description);
        $group->save();

        activity()->log('Mise à jour du groupe :' . $group->getName());

        return back()->with('message', 'Groupe Mise à jour avec succès');
    }

    public function search(Request $request)
    {
        $namegroup = $request->input('SearchGroup');
        if ($request) {
            $groups = GroupAD::query()
                ->where('objectclass', '=', 'group');
            if ($namegroup) {
                $groups->where('cn', '=', $namegroup);
            }

            $group = $groups->get()->first();

            if (!$group) {
                return redirect()->route('group.index');
            }
            return redirect()->route('group.view', ['dn' => $group->getDn()]);
        }
    }

    public function destroy(Request $request)
    {
        $group = GroupAD::find($request->dn);

        if (!$group) {
            return "Le groupe n'a pas été trouvé.";
        }

        $nbrmember = $group->members()->get()->count();
        if ($nbrmember > 0) {
            return back()->with('error', "Suppression impossible: Le groupe n'est pas vide !");
        }

        try {
            $group->delete();
            activity()->log('Suppression du groupe :' . $group->getName());
            return redirect()->route('group.index')->with('message', 'Le groupe a été supprimé avec succès.');
        } catch (LdapRecordException $e) {
            return back()->with('message', "Une erreur s'est produite lors de la suppression du groupe :" . $e->getMessage());
        }
    }

    public function remove_user(Request $request)
    {
        $group = GroupAD::find($request->dn);
        $user = $group->members()->where('distinguishedName', '=', $request->dnuser)->first();
        $group->members()->detach($user);
        activity()->log("Retrait de {$user->getName()} du groupe {$group->getName()}");
        return back()->with('message', "L'utilisateur " . $user->displayname[0] . " a été retiré du groupe.");
    }

    public function add_user(Request $request)
    {
        $group = GroupAD::find($request->dn);

        $users = UserAD::where('samaccountname', '=', $request->SearchAdduser);

        if ($users->exists()) {
            $user = UserAD::find($users->get()->first()->getDn());
            $group->members()->attach($user);
            activity()->log("Ajout de {$user->getName()} au groupe {$group->getName()}");
            return back()->with('message', "L'utilisateur " . $user->displayname[0] . " a été ajouté au groupe.");
        }
        return back()->with('error', 'Utilisateur introuvable');
    }

    public function add_group(Request $request)
    {
        $group = GroupAD::where('cn', '=', $request->SearchAddgroup)->first();

        $addgroup = GroupAD::find($request->dn);
        if ($addgroup->exists()) {
            $group->groups()->attach($addgroup);
            activity()->log("Ajout de {$addgroup->getName()} au groupe {$group->getName()}");
            return back()->with('message', "Groupe " . $group->cn[0] . " ajouté");
        }
        return back()->with('error', 'Groupe introuvable');
    }


    public function attach_group(Request $request)
    {
        $addgroup = GroupAD::where('cn', '=', $request->SearchAttachgroup)->first();

        $group = GroupAD::find($request->dn);
        if ($addgroup->exists()) {
            $group->groups()->attach($addgroup);
            activity()->log("Ajout de {$addgroup->getName()} au groupe {$group->getName()}");
            return back()->with('message', "Ajout de {$addgroup->getName()} au groupe {$group->getName()}");
        }
        return back()->with('error', 'Groupe introuvable');
    }

    public function remove_group(Request $request)
    {
        $member = Group::find($request->dnuser);

        $officeGroup = $member->groups()->where('distinguishedName', '=', $request->dn)->first();

        $member->groups()->detach($officeGroup);

        activity()->log("Retrait de {$member->getName()} du groupe {$officeGroup->getName()}");

        return back()->with('message', "Groupe " . $member->cn[0] . " retiré");
    }

    public function autocomplete(Request $request)
    {
        $ouName = $request->input('ou', '');
        $username = $request->input('search', '');

        $userNames = [];

        if ($username !== '') {
            $users = GroupAD::query()
                ->in($ouName)
                ->where('cn', 'contains', $username)
                ->get();

            // Extraction des noms des utilisateurs
            $userNames = $users->pluck('name')->flatten()->toArray(); // Assurez-vous d'adapter cette ligne selon la structure de vos données LDAP
        }

        return response()->json($userNames);
    }
}