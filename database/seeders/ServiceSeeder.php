<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
         DB::table('services')->insert([
            [
                'name' => 'ورود',
                'category_id' => 1, 
                'image' => 'storage/services/flowers.jpg',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'هدايا مخصصه',
                'category_id' => 1, 
                'image' => 'storage/services/specialize_gift.jpg',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'حلويات',
                'category_id' => 1,
                'image' => 'storage/services/candy.jpg',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'الديكورات',
                'category_id' => 2,
                'image' => 'storage/services/decore.jpg',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
