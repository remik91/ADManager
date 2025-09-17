<?php

namespace App\Http\Controllers;

use App\Ldap\AD\OrgAD;

use App\Ldap\AD\BitlockAD;
use App\Ldap\AD\ComputerAD;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use LdapRecord\Models\ActiveDirectory\Entry;
use Illuminate\Pagination\LengthAwarePaginator;
use LdapRecord\Models\ActiveDirectory\Container;
use LdapRecord\Models\Attributes\AccountControl;

class Ordinateur extends Controller
{
    public function index(Request $request)
    {
        $ouName = "OU=Ordinateurs,DC=ad,DC=ac-creteil";
        $ouList = OrgAD::query()->in($ouName)->get(['dn', 'name']);

        $ouComputers = "CN=Computers,DC=ad,DC=ac-creteil";
        $ouComputersData = Container::query()->in($ouComputers)->first();

        $ouName = $request->input('search_ou');
        $computer = $request->input('search_sAM');
        $sysexp = $request->input('search_sysexp');

        if ($request) {
            $users = ComputerAD::query()
                ->where('objectclass', '=', 'computer')
                ->in($ouName);
            if ($computer) {
                $users->where('name', 'contains', $computer);
            }
            if ($sysexp) {
                $users->where('operatingsystem', 'contains', $sysexp);
            }

            $listcomputers = $users->get();
        }
        // dd($ouList);
        return view('computer.index_computer', [
            'ouList' => $ouList,
            'listcomputers' => $listcomputers,
            'selectedOu' => $ouName,
            'searchText' => $computer,
            'ouComputers' => $ouComputersData,
            'sysexp' => $sysexp
        ]);
    }

    public function view(Request $request)
    {
        $computer = ComputerAD::find($request->id);
        // dd($computer->distinguishedname[0]);
        if ($computer) {

            //Liste OU pour modal changement
            $ouName = "OU=Ordinateurs,DC=ad,DC=ac-creteil";
            // $ouList = OrgAD::query()->in($ouName)->where('objectclass', 'organizationalUnit')->get(['dn', 'name']);
            $uac = new AccountControl(
                $computer->getFirstAttribute('userAccountControl')
            );

            if ($uac->hasFlag(AccountControl::ACCOUNTDISABLE)) {
                //dd("TEST");
            }

            $response = Http::withoutVerifying()->get("http://stockmanager.in.ac-creteil.fr/api/view/" . $computer->getName());
            $stockmanager = $response->json();

            $bitlock = BitlockAD::query()->in($computer->distinguishedname[0])->get();

            return view('computer.view_computer', ['computer' => $computer, 'bitlock' => $bitlock, 'uac' => $uac, 'stockmanager' => $stockmanager]);
        } else return view('computer.view_computer', ['computer' => $computer]);
    }

    public function destroy(Request $request)
    {
        $computer = ComputerAD::find($request->dn);
        try {
            $computer->delete($recursive = true);
            activity()->log('L\'ordinateur ' . $computer->getName() . ' a été supprimé avec succès.');
            return redirect()->route('computer.index')
                ->with('message', 'L\'ordinateur  ' . $computer->getName() . ' a été supprimé avec succès.');
        } catch (\LdapRecord\LdapRecordException $ex) {
            $error = $ex->getDetailedError();

            echo $error->getErrorCode();
            echo $error->getErrorMessage();
            echo $error->getDiagnosticMessage();
            // Failed. Get the last LDAP
            // error to determine the cause of failure.
            return back()->with('error', 'Erreur lors de la suppression: ' . $error->getErrorMessage());
        }
    }

    public function migrate(Request $request)
    {
        // Validation des données d'entrée
        $validated = $request->validate([
            'dncomputer' => 'required',
            'newou' => 'required',
        ]);

        try {
            // Récupération de l'ordinateur et de la nouvelle organisation
            $computer = ComputerAD::findOrFail($validated['dncomputer']);
            $orgad = OrgAD::findOrFail($validated['newou']);

            // Si le formulaire a été soumis
            if ($request->isMethod('post')) {
                if (!$computer->isChildOf($orgad)) {
                    // Déplacement de l'ordinateur
                    $computer->move($orgad, $deleteOldRdn = true);

                    // Enregistrement de l'activité
                    activity()->log('L\'ordinateur ' . $computer->getName() . ' a changé d\'organisation.');

                    // Retour avec message de succès
                    return back()->with('message', "L'Unité d'organisation a bien été changée pour " . $computer->getName());
                } else {
                    // Retour avec message d'erreur
                    return back()->with('error', "Pas de changement détecté pour l'unité d'organisation.");
                }
            }
        } catch (\Exception $e) {
            // Gestion des erreurs et retour avec message d'erreur
            return back()->with('error', 'Une erreur est survenue : ' . $e->getMessage());
        }
    }


    public function search(Request $request)
    {
        $ouName = "OU=Ordinateurs,DC=ad,DC=ac-creteil";
        $computername = $request->input('search');

        if ($request) {
            $computers = ComputerAD::query()
                ->where('objectclass', '=', 'computer')
                ->in($ouName);
            if ($computername) {
                $computers->where('name', '=', $computername);
            }
            $computer = $computers->get()->first();

            if (!$computer) {
                return redirect()->route('computer.index', ['search_sAM' => $computername, 'search_ou' => $ouName]);
            }
            return redirect()->route('computer.view', ['id' => $computer->getDn()]);
        }
    }


    public function autocomplete(Request $request)
    {
        $computername = $request->input('search', '');

        $ComputerNames = [];

        if ($computername !== '') {
            $computer = ComputerAD::query()
                ->where('samaccountname', 'contains', $computername)
                ->get();

            // Extraction des noms des utilisateurs
            $ComputerNames = $computer->pluck('name')->flatten()->toArray(); // Assurez-vous d'adapter cette ligne selon la structure de vos données LDAP
        }

        return response()->json($ComputerNames);
    }

    public function bitlocker(Request $request)
    {
        // Recherche par clé de récupération si un terme est saisi
        $search = $request->input('search');

        // Requête de base pour récupérer les ordinateurs BitLocker
        $query = BitlockAD::in('OU=Ordinateurs,DC=ad,DC=ac-creteil');

        if ($search) {
            // Filtrer par clé de récupération
            $query->whereContains('cn', $search);
        }

        // Récupérer tous les objets correspondant à la requête
        $bitlockerComputers = $query->get();

        // Pagination manuelle
        $perPage = 25; // Nombre d'éléments par page
        $page = $request->input('page', 1); // Récupère le numéro de la page actuelle
        $offset = ($page - 1) * $perPage; // Calcule l'offset

        // Crée une collection paginée
        $paginatedComputers = new LengthAwarePaginator(
            $bitlockerComputers->slice($offset, $perPage), // Découpe la collection
            $bitlockerComputers->count(), // Total des résultats
            $perPage, // Nombre d'éléments par page
            $page, // Page courante
            ['path' => $request->url(), 'query' => $request->query()] // Conserve les paramètres d'URL
        );

        return view('computer.bitlocker', [
            'bitlockerComputers' => $paginatedComputers, // Passe la collection paginée à la vue
            'search' => $search // Recherche actuelle
        ]);
    }
}