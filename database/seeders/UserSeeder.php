<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class UserSeeder extends Seeder
{
    public function run()
    {
        DB::table('users')->insert([
            [
                'name' => 'Vespa',
                'email' => 'vespa@example.com',
                'password' => Hash::make('01'),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Hulk',
                'email' => 'hulk@example.com',
                'password' => Hash::make('02'),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
