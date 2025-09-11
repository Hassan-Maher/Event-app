<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use \Illuminate\Support\Facades\DB;

class CitiesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
         $cities = [
            ['name' => 'الرياض'],
            ['name' => 'جدة'],
            ['name' => 'مكة المكرمة'],
            ['name' => 'المدينة المنورة'],
            ['name' => 'الدمام'],
            ['name' => 'الطائف'],
            ['name' => 'تبوك'],
            ['name' => 'بريدة'],
            ['name' => 'خميس مشيط'],
            ['name' => 'الهفوف'],
            ['name' => 'حفر الباطن'],
            ['name' => 'حائل'],
            ['name' => 'نجران'],
            ['name' => 'ينبع'],
            ['name' => 'الخبر'],
            ['name' => 'القطيف'],
            ['name' => 'أبها'],
            ['name' => 'جازان'],
            ['name' => 'سكاكا'],
        ];

        DB::table('cities')->insert($cities);
    }
}
