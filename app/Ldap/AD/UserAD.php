<?php

namespace App\Ldap\AD;

use LdapRecord\Models\ActiveDirectory\User;
use LdapRecord\Models\Attributes\AccountControl;


class UserAD extends User
{

    protected ?string $connection = 'AD';
    /**
     * The object classes of the LDAP model.
     *
     * @var array
     */
    public static array $objectClasses = [
        'top',
        'person',
        'organizationalperson',
        'user'
    ];

    protected array $dates = [
        'lastlogon' => 'windows-int',
        'whencreated' => 'windows',
    ];

    public function activeAccount()
    {
        $uac = new AccountControl();
        $uac->setAccountIsNormal();
        $uac->setPasswordDoesNotExpire();
        $this->userAccountControl = $uac;
    }
    public function desactiveAccount()
    {
        $uac = new AccountControl();
        $uac->setAccountIsDisabled();
        $uac->setPasswordDoesNotExpire();
        $this->userAccountControl = $uac;
    }

    public function NoPasswordRequired()
    {
        $uac = new AccountControl();
        $uac->setPasswordIsNotRequired();
        $this->userAccountControl = $uac;
    }

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


    public function getInitials()
    {
        $initials = ''; // Initialise la variable pour stocker les initiales

        $words = explode(" ", $this->displayName[0]); // Divise la chaîne de caractères en mots

        foreach ($words as $word) {
            if (!empty($word)) {
                $initials .= strtoupper($word[0]); // Ajoute la première lettre de chaque mot en majuscule
            }
        }

        return $initials;
    }

    public function samaccounttype2()
    {
        if ($this->samaccounttype[0] == '805306368') {
            return "Utilisateur";
        } else if ($this->samaccounttype[0] == '268435456') {
            return "Groupe";
        }
    }
}