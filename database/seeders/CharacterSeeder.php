<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;

class CharacterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        DB::table('characters')->insert([
            ['name' => 'Character 1', 'description' => 'Description 1', 'thumbnail' => 'url1.jpg', 'marvel_id' => '101'],
            ['name' => 'Character 2', 'description' => 'Description 2', 'thumbnail' => 'url2.jpg', 'marvel_id' => '102'],
            // Add more entries as needed
        ]);
    }
}
