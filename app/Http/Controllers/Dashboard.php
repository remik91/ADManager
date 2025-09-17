<?php

namespace App\Http\Controllers;

use App\Ldap\AD\UserAD;
use App\Ldap\AD\GroupAD;
use Illuminate\Http\Request;

class Dashboard extends Controller
{
    public function index()
    {

        // Utilisateurs récemment ajoutés (dans les 7 derniers jours, par exemple)
        $recentUsers = UserAD::in("OU=Utilisateurs,DC=ad,DC=ac-creteil")
            ->where('whenCreated', '>=', now()->subDays(7)->format('YmdHis'))->get()
            ->count();

        // Utilisateurs inactifs (sans connexion depuis 90 jours, par exemple)
        $inactiveUsers = UserAD::in("OU=Utilisateurs,DC=ad,DC=ac-creteil")
            ->where('lastLogon', '<=', now()->subDays(90)->format('YmdHis'))->get()
            ->whereNotNull('lastLogon')
            ->count();

        // Nombre total de groupes
        $totalGroups = GroupAD::get()->count();

        // Nombre compte supprimer de groupes
        $totalTrash = UserAD::whereDeleted()->get()->count();


        $countuser = UserAD::query()->in("OU=Utilisateurs,DC=ad,DC=ac-creteil")->where('objectclass', 'user')->get()->count();
        return view('dashboard', ['recentUsers' => $recentUsers, 'inactiveUsers' => $inactiveUsers, 'totalGroups' => $totalGroups, 'totalTrash' => $totalTrash]);
    }
}