<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;

class SuperAdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $role = Role::where('name', 'superadmin')->first();
        $user = User::firstOrNew(['email' => 'superadmin@gmail.com']);
        $user->name = 'super admin';
        $user->email = 'superadmin@gmail.com';
        $user->password = bcrypt('admin');
        $user->save();
        $user->role()->attach( $role->id);
    }
}
