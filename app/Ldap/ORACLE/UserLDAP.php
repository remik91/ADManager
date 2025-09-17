<?php

namespace App\Ldap\ORACLE;

use LdapRecord\Models\OpenLDAP\User;

class UserLDAP extends User
{
    protected ?string $connection = 'ORACLE';
    /**
     * The object classes of the LDAP model.
     *
     * @var array
     */
}