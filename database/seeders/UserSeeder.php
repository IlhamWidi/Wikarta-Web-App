<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('users')->insert([
            'id' => 1,
            'user_type' => 'ADMIN',
            'code' => 'ID-00000001',
            'username'  => '081233920913',
            'email'  => 'ridho@gmail.com',
            'password' => bcrypt('1234'),
            'name' => 'Ridho',
            'phone_number' => '081233920913',
            'city_id' => 1,
            'role_id' => 'SUPERADMIN',
            'address' => 'Perumahan Grand Surya Blok C Raya No.43',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ]);
    }
}
