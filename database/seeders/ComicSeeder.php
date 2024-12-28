<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;

class ComicSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        DB::table('comics')->insert([
            ['title' => 'Comic 1', 'description' => 'Description 1', 'thumbnail' => 'url1.jpg', 'marvel_id' => '1'],
            ['title' => 'Comic 2', 'description' => 'Description 2', 'thumbnail' => 'url2.jpg', 'marvel_id' => '2'],
            // Add more entries as needed
        ]);
    }
}
