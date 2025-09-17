<?php

namespace App\Ldap\AD;

use LdapRecord\Models\Model;

class OrgAD extends Model
{
    protected ?string $connection = 'AD';
    /**
     * The object classes of the LDAP model.
     *
     * @var array
     */
    public static array $objectClasses = [
        'top',
        'organizationalUnit',
    ];
}