<?php

namespace App\Http\Controllers;

use App\Ldap\AD\UserAD;
use Illuminate\Http\Request;
use App\Models\User as ModelsUser;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Spatie\Activitylog\Models\Activity;
use LdapRecord\Laravel\Import\Synchronizer;

class Admin extends Controller
{
    public function index()
    {
        $user = ModelsUser::all();
        return view('admin.administrateur', compact(['user']));
    }

    public function store(Request $request)
    {
        if (ModelsUser::withTrashed()->where('username', $request->username)->restore()) {
            return back()->with('message', 'L\'utilisateur ' . $request->username . ' a été ajouté avec succès.');
        }

        $validated = $request->validate([
            'username' => 'required|unique:users|max:255',
        ]);

        $users = new UserAD();
        $record = $users->query()->findBy('samaccountname', $request->username);

        if ($record) {
            // Create the synchronizer.
            $synchronizer = new Synchronizer(ModelsUser::class, $config = [
                'sync_passwords' => true,
                'sync_attributes' => [
                    'name' => 'displayname',
                    'email' => 'mail',
                    'username' => 'samaccountname',
                ],
            ]);
            $synchronizer->run($record)->save();
            $usercreated = ModelsUser::where('username', $request->username)->get()->first();
            if ($usercreated) {
                //dd($user);

                // $usercreated->assignRole("Lecteur");

                activity()->log('Ajout de l\'administrateur :' .  $request->username);

                return back()->with('message', 'L\'utilisateur ' . $request->username . ' a été ajouté avec succès.');
            }
        } else {
            return back()->withErrors('L\'utilisateur ' . $request->username . ' n\'existe pas ou n\'a pas été trouvé.');
        }
    }

    public function storelocal(Request $request)
    {
        if (ModelsUser::withTrashed()->where('username', $request->username)->restore()) {
            return back()->with('message', 'L\'utilisateur ' . $request->username . ' a été ajouté avec succès.');
        }

        $validated = $request->validate([
            'username' => 'required|unique:users|max:255',
        ]);

        $user = new ModelsUser();
        $user->password = Hash::make($request->password);
        $user->email =  $request->mail;
        $user->name =  $request->name;
        $user->username =  $request->username;
        $user->save();

        $usercreated = ModelsUser::where('username', $request->username)->get()->first();

        if ($usercreated) {

            //  $usercreated->assignRole("Lecteur");
            activity()->log('Ajout de l\'administrateur local :' . $request->username);

            return back()->with('message', 'L\'utilisateur ' . $request->username . ' a été ajouté avec succès.');
        }
    }

    public function update_profil(Request $request)
    {
        $user = ModelsUser::find($request->id);
        $user->name = $request->name;
        $user->avatar = $request->avatar;

        if ($request->password) {
            $user->password = Hash::make($request->password);
        }
        $user->save();

        activity()->log('Modification de l\'administrateur :' . $user->name);
        //LogActivity::addToLog('[Utilisateur] Modification', "Utilisateur $user->name à modifié son profil.");
        return back()->with('message', 'Profil ' . $user->name . ' modifié avec succès.');
    }

    public function destroy($id)
    {
        $user = ModelsUser::find($id);
        if (($user->username === "adminad") && ($user->id == 1)) {
            return back()->withErrors('Pour des raisons de sécurité, l\'utilisateur ' . $user->name . ' ne peut être supprimé de l\'application DNSManager.');
        }
        //$user->syncRoles([]);
        $user->delete();
        activity()->log('Suppression de l\'administrateur :' . $user->name);

        return back()->with('message', 'L\'utilisateur ' . $user->name . ' a été supprimé.');
    }

    public function profil()
    {
        $user = Auth::user();
        //  $roles = Role::get();

        return view('admin.profil_admin', compact(['user']));
    }

    public function master()
    {
        return view('admin.master');
    }

    public function download()
    {
        return Storage::download("MasterRectorat.iso");
    }

    public function history()
    {
        $logactivity = Activity::all();
        return view('admin.history', ['logactivity' => $logactivity]);
    }

    public function outil()
    {
        return view('admin.outil',);
    }
}