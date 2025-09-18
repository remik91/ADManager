<?php

namespace App\Http\Controllers;

use App\Ldap\AD\OrgAD;
use App\Ldap\AD\BitlockAD;
use App\Ldap\AD\ComputerAD;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use LdapRecord\Models\ActiveDirectory\Container;
use LdapRecord\Models\Attributes\AccountControl;

class Ordinateur extends Controller
{
    /** Liste + filtres + pagination LDAP */
    public function index(Request $request)
    {
        // OU par défaut + liste des OU autorisées (whitelist)
        $rootComputersOu = 'OU=Ordinateurs,DC=ad,DC=ac-creteil';
        $ouList = OrgAD::query()->in($rootComputersOu)->get(['dn', 'name']);

        $wellKnownComputers = 'CN=Computers,DC=ad,DC=ac-creteil';
        $ouComputersData = Container::query()->in($wellKnownComputers)->first();

        // Build whitelist from available OU + CN=Computers
        $allowedOuDns = $ouList->pluck('dn')->push($wellKnownComputers)->all();

        $request->validate([
            'search_ou'  => ['nullable', Rule::in($allowedOuDns)],
            'search_sAM' => ['nullable', 'string', 'max:64'],
            'search_sysexp' => ['nullable', Rule::in(['', '10', '11'])],
            'per_page'   => ['nullable', 'integer', 'min:10', 'max:200'],
            'cookie'     => ['nullable', 'string']
        ]);

        $selectedOu = $request->input('search_ou', $rootComputersOu);
        $needle     = $request->input('search_sAM');
        $sysexp     = $request->input('search_sysexp');
        $perPage    = (int) $request->integer('per_page', 50);

        $q = ComputerAD::query()
            ->where('objectclass', '=', 'computer')
            ->in($selectedOu)
            ->selectLight()
            ->nameContains($needle)
            ->osFilter($sysexp)
            ->orderBy('cn');

        // Pagination LDAP (avec cookie)
        $listcomputers = $q->paginate($perPage);

        return view('computer.index_computer', [
            'ouList'        => $ouList,
            'listcomputers' => $listcomputers,
            'selectedOu'    => $selectedOu,
            'searchText'    => $needle,
            'ouComputers'   => $ouComputersData,
            'sysexp'        => $sysexp,
            'perPage'       => $perPage,
        ]);
    }

    /** Vue d'un ordinateur + multi BitLocker */
    public function view(Request $request)
    {
        $computer = ComputerAD::find($request->id);
        if (!$computer) return view('computer.view_computer', ['computer' => null]);

        $uac = new AccountControl($computer->getFirstAttribute('userAccountControl'));

        // StockManager: sécuriser l'appel HTTP
        $stockBase = config('services.stockmanager.base_url', 'https://stockmanager.in.ac-creteil.fr/api');
        $stockmanager = null;
        try {
            $response = Http::timeout(5)->retry(1, 500)->acceptJson()
                ->get(rtrim($stockBase, '/') . '/view/' . $computer->getName());
            if ($response->successful()) $stockmanager = $response->json();
        } catch (\Throwable $e) {
            // log mais ne bloque pas l'affichage
            Log::warning('[StockManager] ' . $e->getMessage());
        }

        // Tous les objets BitLocker sous cet ordinateur (triés desc)
        $bitlocks = BitlockAD::query()->in($computer->getDn())->orderBy('whencreated', 'desc')->get();

        return view('computer.view_computer', [
            'computer'    => $computer,
            'bitlocks'    => $bitlocks,
            'uac'         => $uac,
            'stockmanager' => $stockmanager,
        ]);
    }

    /** Migration d'OU — sécurisée */
    public function migrate(Request $request)
    {
        $validated = $request->validate([
            'dncomputer' => ['required', 'string'],
            'newou'      => ['required', 'string'],
        ]);

        $computer = ComputerAD::findOrFail($validated['dncomputer']);
        $orgad    = OrgAD::findOrFail($validated['newou']);

        if (!$computer->isChildOf($orgad)) {
            $computer->move($orgad, $deleteOldRdn = true);
            activity()->log("Ordinateur {$computer->getName()} déplacé dans {$orgad->getName()}");
            return back()->with('message', "OU changée pour {$computer->getName()}");
        }
        return back()->with('error', "Pas de changement détecté pour l'OU.");
    }

    /** Recherche barre haute : redirige vers index ou view */
    public function search(Request $request)
    {
        $root = 'OU=Ordinateurs,DC=ad,DC=ac-creteil';
        $needle = trim($request->input('search', ''));
        $q = ComputerAD::query()->where('objectclass', '=', 'computer')->in($root);
        if ($needle !== '') $q->where('name', '=', $needle);
        $computer = $q->first();

        if (!$computer) {
            return redirect()->route('computer.index', ['search_sAM' => $needle, 'search_ou' => $root]);
        }
        return redirect()->route('computer.view', ['id' => $computer->getDn()]);
    }

    /** Autocomplete — limiter + durcir */
    public function autocomplete(Request $request)
    {
        $term = trim($request->input('search', ''));
        $names = [];
        if ($term !== '') {
            $names = ComputerAD::query()
                ->where('samaccountname', 'contains', $term)
                ->select(['name'])
                ->limit(10)
                ->get()
                ->pluck('name')
                ->flatten()
                ->toArray();
        }
        return response()->json($names);
    }

    /** Liste BitLocker globale – pagination Laravel conservée mais on dé-duplique par ordi */
    public function bitlocker(Request $request)
    {
        $search = trim($request->input('search', ''));
        $root   = 'OU=Ordinateurs,DC=ad,DC=ac-creteil';

        // Cas 1 : GUID ou clé -> recherche directe sur msFVE-RecoveryInformation
        $guidRegex = '/^\{?[0-9a-fA-F]{8}(?:-[0-9a-fA-F]{4}){3}-[0-9a-fA-F]{12}\}?$/';
        $looksLikeRecoveryKey = (bool) preg_match('/[0-9]{6,}(-[0-9]{6,})+/', $search);

        $items = collect();

        if ($search !== '' && (preg_match($guidRegex, $search) || $looksLikeRecoveryKey)) {
            $q = \App\Ldap\AD\BitlockAD::in($root)
                ->select(['cn', 'whencreated', 'distinguishedname', 'msfve-recoverypassword'])
                ->orderBy('whencreated', 'desc');

            if (preg_match($guidRegex, $search)) {
                $q->whereContains('cn', trim($search, '{}'));
            } else {
                $q->whereContains('msfve-recoverypassword', $search);
            }

            $items = $items->merge($q->get());
        } else {
            // Cas 2 : recherche par NOM de machine -> 2 étapes

            // On tolère que l’utilisateur tape "PC-NAME$" (sAMAccountName) : on retire le $
            $needle = rtrim($search, '$');

            // 2.1 Trouver les ordinateurs candidats
            $computers = \App\Ldap\AD\ComputerAD::query()
                ->in($root)
                ->select(['cn', 'distinguishedname', 'samaccountname', 'name'])
                ->whereContains('cn', $needle)
                ->orWhere('samaccountname', '=', $needle . '$')
                ->orWhereContains('name', $needle)
                ->limit(50) // évite de charger trop de PCs
                ->get();

            // 2.2 Pour chacun, récupérer les objets BitLocker situés DESSOUS
            foreach ($computers as $c) {
                $itemsForC = \App\Ldap\AD\BitlockAD::query()
                    ->in($c->getDn())
                    ->select(['cn', 'whencreated', 'distinguishedname', 'msfve-recoverypassword'])
                    ->orderBy('whencreated', 'desc')
                    ->get();

                $items = $items->merge($itemsForC);
            }
        }

        // Normaliser pour la vue
        $rows = $items->map(function ($b) {
            $dn = $b->getDn(); // CN={GUID},CN=PC-NAME,OU=...,DC=...
            $computerDn = \Illuminate\Support\Str::after($dn, ','); // CN=PC-NAME,OU=...,DC=...
            $computerCn = substr(explode(',', $computerDn)[0] ?? '', 3);
            preg_match('/\{(.+?)\}/', $b->getName(), $m);
            $guid = $m[1] ?? null;

            return [
                'computer'     => $computerCn ?: null,
                'computer_dn'  => $computerDn ?: null,                // DN complet côté machine (pour ton lien)
                'guid'         => $guid,
                'key'          => $b['msfve-recoverypassword'][0] ?? null,
                'when'         => $b->whencreated,
                'dn'           => $dn,
            ];
        });

        // Pagination manuelle Laravel
        $perPage = (int) $request->integer('per_page', 25);
        $page    = (int) $request->integer('page', 1);
        $total   = $rows->count();
        $slice   = $rows->slice(($page - 1) * $perPage, $perPage);

        $paginator = new \Illuminate\Pagination\LengthAwarePaginator(
            $slice,
            $total,
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        return view('computer.bitlocker', [
            'bitlockerComputers' => $paginator,
            'search' => $search,
        ]);
    }
}