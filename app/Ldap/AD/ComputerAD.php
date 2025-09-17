<?php

namespace App\Ldap\AD;

use LdapRecord\Models\ActiveDirectory\Computer;

class ComputerAD extends Computer
{
    protected ?string $connection = 'AD';
    /**
     * The object classes of the LDAP model.
     *
     * @var array
     */

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


    public function getParentName()
    {
        $parent = $this->getParentDn();

        if ($parent) {
            $parentComponents = explode(',', $parent);
            $parentName = substr($parentComponents[0], 3); // Supprimer "OU=" du début

            return $parentName;
        }

        return null;
    }
}