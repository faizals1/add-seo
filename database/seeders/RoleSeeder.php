<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Role::truncate();
        $role_data = [[
            'name' => 'superadmin',
            'slug' => 'superadmin',
        ],[
            'name' => 'company-admin',
            'slug' => 'companyadmin',
        ],[
            'name' => 'company-user',
            'slug' => 'companyuser',
        ],[
            'name' => 'general',
            'slug' => 'general',
        ]];
        Role::insert($role_data);
    }
}
