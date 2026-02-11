<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class CreateAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'ravinder@possibilitysolutions.com'],
            [
                'name' => 'Admin',
                'password' => Hash::make('123456'),
                'role' => 'admin',
                'status' => true,
            ]
        );
    }
}
