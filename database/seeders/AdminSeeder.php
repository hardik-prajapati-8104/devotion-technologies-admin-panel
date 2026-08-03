<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Admin;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run()
    {
        $superAdmin = Admin::firstOrCreate(
            ['email' => 'admin@devotiontechnology.com'],
            [
                'first_name' => 'Devotion',
                'last_name' => 'Admin',
                'username' => 'devotionadmin',
                'mobile_number' => '1234567890',
                'password' => Hash::make('password'), // Change before production
                'status' => 1,
                'login' => 1,
                'is_assign_super_admin' => 1,
            ]
        );

        $superAdmin->assignRole('Super Admin');
    }
}