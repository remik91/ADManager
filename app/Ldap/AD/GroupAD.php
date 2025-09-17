<?php

namespace App\Ldap\AD;

use LdapRecord\Models\ActiveDirectory\Group;

class GroupAD extends Group
{
    protected ?string $connection = 'AD';
    /**
     * The object classes of the LDAP model.
     *
     * @var array
     */
    public static array $objectClasses = [
        'top',
        'group'
    ];

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

    public function scopeOnlyGl($q)
    {
        return $q->where('cn', 'starts_with', config('admanager.prefix_gl', 'GL'));
    }


    public function scopeOnlyGg($q)
    {
        return $q->where('cn', 'starts_with', config('admanager.prefix_gg', 'GG'));
    }


    public function scopeInEntityGl($q, string $entity)
    {
        if ($ou = data_get(config('admanager.entities'), "$entity.ou_gl")) $q->in($ou);
        return $q;
    }


    public function scopeInEntityGg($q, string $entity)
    {
        if ($ou = data_get(config('admanager.entities'), "$entity.ou_gg")) $q->in($ou);
        return $q;
    }
}