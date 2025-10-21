<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleAccessMenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ambil semua ID menu yang ada
        $allMenuIds = DB::table('menus')->pluck('id')->toArray();
        
        // Menu untuk SUPERADMIN & SUPERUSER (semua akses)
        $superuserMenus = $allMenuIds;
        
        // Menu untuk MARKETING (terbatas)
        $marketingMenus = [
            1,  // Dashboard
            41, // Invoice
            43, // Omzet
            44, // Omzet Statistic
            51, // Pelanggan
            52, // Pemasangan
            53, // GiveAway
            54, // Kurir GiveAway
            61, // Support Ticket
            62, // Resolve Ticket
        ];
        
        // Menu untuk TEKNISI (terbatas)
        $teknisiMenus = [
            1,  // Dashboard
            51, // Pelanggan
            52, // Pemasangan
            53, // GiveAway
            54, // Kurir GiveAway
            55, // Data Asset
            61, // Support Ticket
            62, // Resolve Ticket
            71, // Attendance Monitor
        ];
        
        // Ambil semua user dan berikan akses menu sesuai role mereka
        $users = DB::table('users')->get();
        
        foreach ($users as $user) {
            $menuAccess = [];
            
            // Tentukan menu berdasarkan role
            switch ($user->role_id) {
                case 'SUPERADMIN':
                case 'SUPERUSER':
                    $menuAccess = $superuserMenus;
                    break;
                case 'MARKETING':
                    $menuAccess = $marketingMenus;
                    break;
                case 'TEKNISI':
                    $menuAccess = $teknisiMenus;
                    break;
                default:
                    $menuAccess = [1]; // Hanya dashboard untuk role lain
                    break;
            }
            
            // Insert atau update menu access untuk user ini
            DB::table('role_access_menus')->updateOrInsert(
                ['user_id' => $user->id],
                [
                    'menu_access' => json_encode($menuAccess),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }
}
