<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Carbon\Carbon;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('users')->insert([
            [
                'id' => 1,
                'name' => 'Foyez Ahammed',
                'role' => 'user',
                'email' => '5plaban9@gmail.com',
                'email_verified_at' => Carbon::parse('2025-03-09 00:37:10'),
                'password' => '$2y$12$Cx/uPP.zereiWaEON2N47uxChT.PyboNtk/d1Re47Jtz/kRKS5R7i',
                'remember_token' => null,
                'created_at' => Carbon::parse('2025-03-09 00:37:10'),
                'updated_at' => Carbon::parse('2025-03-08 18:39:24'),
            ],
            [
                'id' => 2,
                'name' => 'Admin User',
                'role' => 'admin',
                'email' => 'admin@gmail.com',
                'email_verified_at' => Carbon::parse('2025-03-09 00:38:26'),
                'password' => '$2y$12$mDaN/5z..WTyVkxpdIfc4O/2c.lUj8FahJ1SxVV46kcBh3iPmFok2',
                'remember_token' => null,
                'created_at' => Carbon::parse('2025-03-09 00:38:26'),
                'updated_at' => Carbon::parse('2025-03-08 18:42:27'),
            ],
        ]);
    }
}
