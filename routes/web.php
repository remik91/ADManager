<?php

use App\Http\Controllers\Admin;
use App\Http\Controllers\Groupe;
use App\Http\Controllers\Dashboard;
use App\Http\Controllers\Ordinateur;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Utilisateur;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('testldap', [Utilisateur::class, 'testldap']);


Route::middleware(['auth'])->group(function () {

    Route::get('/', [Dashboard::class, 'index']);

    Route::get('/user', [Utilisateur::class, 'index'])->name('user.index');
    Route::post('/user/{id}/update/{type}', [Utilisateur::class, 'update'])->name('user.update');
    Route::get('/user/shortview/{id?}', [Utilisateur::class, 'view_short'])->name('user.shortview');
    Route::get('/user/view/{id?}', [Utilisateur::class, 'view'])->name('user.view');
    Route::get('/user/view/{id}/active', [Utilisateur::class, 'active'])->name('user.active');
    Route::post('/user/view/{id}/password', [Utilisateur::class, 'password'])->name('user.password');
    Route::get('/user/view/{id}/remove', [Utilisateur::class, 'remove'])->name('user.remove');
    Route::post('/user/view/{id}/addgroup', [Utilisateur::class, 'addgroup'])->name('user.addgroup');
    Route::delete('/user/view/{id}/rmgroup', [Utilisateur::class, 'removegroup'])->name('user.rmgroup');
    Route::get('/user/view/{id}/repperso', [Utilisateur::class, 'ToggleRepPerso'])->name('user.repperso');
    Route::get('/user/autocomplete', [Utilisateur::class, 'autocomplete'])->name('user.autocomplete');
    Route::post('/user/search', [Utilisateur::class, 'search'])->name('user.search');
    Route::get('/user/checkldap', [Utilisateur::class, 'checkldap'])->name('user.checkldap');
    Route::get('/user/resynchro', [Utilisateur::class, 'resynchro'])->name('user.resynchro');
    Route::get('/user/trash/{dnuser?}', [Utilisateur::class, 'corbeille'])->name('user.trash');
    Route::post('/user/{dnuser?}/softphonie', [Utilisateur::class, 'ToogleSoftphonie'])->name('user.softphonie');
    Route::get('/user/finfonction', [Utilisateur::class, 'finfonction'])->name('user.finfonction');
    Route::get('/user/orphelin', [Utilisateur::class, 'orphelin'])->name('user.orphelin');
    Route::post('/user/actionlist', [Utilisateur::class, 'actionList'])->name('user.actionlist');

    Route::get('/user/add', [Utilisateur::class, 'import'])->name('user.add');
    Route::get('/user/add/{dnldap?}', [Utilisateur::class, 'import'])->name('user.import');
    Route::post('/user/create', [Utilisateur::class, 'create'])->name('user.create');

    Route::get('/user/import/searchldap', [Utilisateur::class, 'searchldap'])->name('user.searchldap');
    Route::get('/user/import/searchldapimport', [Utilisateur::class, 'searchldapimport'])->name('user.searchldapimport');

    Route::get('/user/tri', [Utilisateur::class, 'tri'])->name('user.tri');


    Route::get('/computer', [Ordinateur::class, 'index'])->name('computer.index');
    Route::get('/computer/view/{id?}', [Ordinateur::class, 'view'])->name('computer.view');
    Route::get('/computer/{dn?}/remove', [Ordinateur::class, 'destroy'])->name('computer.remove');
    Route::post('/computer/migrate', [Ordinateur::class, 'migrate'])->name('computer.migrate');
    Route::post('/computer/search', [Ordinateur::class, 'search'])->name('computer.search');
    Route::get('/computer/bitlocker', [Ordinateur::class, 'bitlocker'])->name('computer.bitlocker');
    Route::get('/computer/autocomplete', [Ordinateur::class, 'autocomplete'])->name('computer.autocomplete');

    Route::get('/group', [Groupe::class, 'index'])->name('group.index');
    Route::get('/group/view/{dn?}', [Groupe::class, 'view'])->name('group.view');
    Route::get('/group/{dn?}/edit', [Groupe::class, 'edit'])->name('group.edit');
    Route::post('/group/create', [Groupe::class, 'create'])->name('group.create');
    Route::post('/group/create_gl', [Groupe::class, 'create_partage'])->name('group.creategl');
    Route::delete('/group/{dn?}/remove', [Groupe::class, 'destroy'])->name('group.remove');
    Route::delete('/group/{dn?}/removeuser', [Groupe::class, 'remove_user'])->name('group.removeuser');
    Route::post('/group/{dn?}/adduser', [Groupe::class, 'add_user'])->name('group.adduser');
    Route::post('/group/{dn?}/addgroup', [Groupe::class, 'add_group'])->name('group.addgroup');
    Route::post('/group/{dn?}/attachgroup', [Groupe::class, 'attach_group'])->name('group.attachgroup');
    Route::delete('/group/{dn?}/removegroup', [Groupe::class, 'remove_group'])->name('group.removegroup');
    Route::post('/group/search', [Groupe::class, 'search'])->name('group.search');
    Route::get('/group/autocomplete', [Groupe::class, 'autocomplete'])->name('group.autocomplete');

    Route::get('/service', [Groupe::class, 'index_services'])->name('service.index');
    Route::post('/service/create', [Groupe::class, 'create_service'])->name('service.create');

    Route::get('/partages', [Groupe::class, 'index_partages'])->name('partage.index');


    Route::get('admin', [Admin::class, 'index'])->name('admin.index');
    Route::get('admin/destroy/{id}', [Admin::class, 'destroy'])->name('admin.destroy');
    Route::post('admin/store', [Admin::class, 'store'])->name('admin.store');
    Route::post('admin/storelocal', [Admin::class, 'storelocal'])->name('admin.storelocal');
    Route::get('admin/profil', [Admin::class, 'profil'])->name('admin.profil');
    Route::post('admin/updateprofil', [Admin::class, 'update_profil'])->name('admin.updateprofil');
    Route::get('admin/download', [Admin::class, 'download'])->name('admin.download');

    Route::get('history', [Admin::class, 'history'])->name('admin.history');

    Route::get('master', [Admin::class, 'master'])->name('master');

    Route::get('outil', [Admin::class, 'outil'])->name('outil');
});
Auth::routes();