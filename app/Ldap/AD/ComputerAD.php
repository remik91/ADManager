<?php

namespace App\Ldap\AD;


use LdapRecord\Models\ActiveDirectory\Computer;
use Illuminate\Support\Str;


class ComputerAD extends Computer
{
    protected ?string $connection = 'AD';


    public static array $objectClasses = [
        'computer',
        'top',
        'person',
        'organizationalperson',
        'user'
    ];


    protected array $dates = [
        'lastlogon' => 'windows-int',
        'whencreated' => 'windows',
    ];


    protected ?string $baseDn = 'OU=Ordinateurs,DC=ad,DC=ac-creteil';
    protected ?string $dn = 'OU=Ordinateurs,DC=ad,DC=ac-creteil';


    // === Helpers ===
    public function getParentName(): ?string
    {
        $parent = $this->getParentDn();
        if (!$parent) return null;
        $first = explode(',', $parent)[0] ?? '';
        return Str::startsWith($first, 'OU=') || Str::startsWith($first, 'CN=')
            ? substr($first, 3)
            : $first;
    }


    // === Query scopes ===
    public function scopeInOu($query, string $ouDn)
    { // Permet de chaîner $q->inOu($dn)
        return $query->in($ouDn);
    }


    public function scopeNameContains($query, ?string $needle)
    {
        if (!$needle) return $query;
        return $query->whereContains('name', $needle);
    }


    public function scopeOsFilter($query, ?string $major)
    { // major = '10' | '11' | null
        if (!$major) return $query;
        return $query->whereContains('operatingsystem', 'Windows')
            ->whereContains('operatingsystemversion', $major);
    }


    public function scopeEnabledOnly($query)
    { // 4096 = TRUSTED_FOR_DELEGATION? Pour tes écrans, tu l'utilises comme "enrôlé".
        return $query->where('useraccountcontrol', '=', 4096);
    }


    public function scopeSelectLight($query)
    { // Attributs utiles pour la liste
        return $query->select([
            'cn',
            'name',
            'distinguishedname',
            'operatingsystem',
            'operatingsystemversion',
            'useraccountcontrol',
            'whencreated',
            'department'
        ]);
    }


    // Récupère les objets BitLocker (enfants) triés récents d'abord
    public function fetchBitlockers()
    {
        return BitlockAD::query()
            ->in($this->getDn())
            ->orderBy('whencreated', 'desc')
            ->get();
    }
}