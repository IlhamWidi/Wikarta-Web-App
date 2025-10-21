<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            [
                'id' => 'SUPERADMIN',
                'name' => 'Super Admin',
                'description' => 'Super Administrator dengan akses penuh ke semua fitur',
                'active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 'SUPERUSER',
                'name' => 'Super User',
                'description' => 'User dengan akses penuh ke sebagian besar fitur',
                'active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 'MARKETING',
                'name' => 'Marketing',
                'description' => 'Marketing team dengan akses ke pelanggan dan invoice',
                'active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 'TEKNISI',
                'name' => 'Teknisi',
                'description' => 'Teknisi dengan akses ke pemasangan dan asset',
                'active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 'ADMIN',
                'name' => 'Admin',
                'description' => 'Administrator dengan akses terbatas',
                'active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($roles as $role) {
            DB::table('roles')->updateOrInsert(
                ['id' => $role['id']],
                $role
            );
        }
    }
}
