<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Create Superuser
        $superuser = User::create([
            'name' => 'Super Admin',
            'email' => 'admin@agusprovider.com',
            'phone' => '081234567890',
            'password' => Hash::make('password'),
            'is_active' => true,
        ]);
        $superuser->assignRole('superuser');

        // Create Keuangan User
        $keuangan = User::create([
            'name' => 'Finance Manager',
            'email' => 'finance@agusprovider.com',
            'phone' => '081234567891',
            'password' => Hash::make('password'),
            'is_active' => true,
        ]);
        $keuangan->assignRole('keuangan');

        // Create Marketing User
        $marketing = User::create([
            'name' => 'Marketing Manager',
            'email' => 'marketing@agusprovider.com',
            'phone' => '081234567892',
            'password' => Hash::make('password'),
            'is_active' => true,
        ]);
        $marketing->assignRole('marketing');

        // Create Teknisi User
        $teknisi = User::create([
            'name' => 'Technician',
            'email' => 'teknisi@agusprovider.com',
            'phone' => '081234567893',
            'password' => Hash::make('password'),
            'is_active' => true,
        ]);
        $teknisi->assignRole('teknisi');
    }
}
