<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class SeedDatabaseInit extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //Create SUper Admin User
        $user = \App\Models\User::factory()->create([
            'name' => 'Administrateur',
            'username' => 'adminad',
            //'username' => env('LDAP_DEFAULT_USER_SUPERADMIN'),
            'password' => Hash::make('91JmRgkou'),
        ]);
    }
}