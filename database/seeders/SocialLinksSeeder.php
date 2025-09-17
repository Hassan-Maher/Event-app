<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SocialLinksSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    
        public function run(): void
    {
        DB::table('social_links')->insert([
            [
                'name' => 'TikTok',
                'link'  => 'https://www.tiktok.com',
            ],
            [
                'name' => 'Instagram',
                'link'  => 'https://www.instagram.com',
            ],
            [
                'name' => 'Facebook',
                'link'  => 'https://www.facebook.com',
            ],
            [
                'name' => 'LinkedIn',
                'link'  => 'https://www.linkedin.com',
            ],
            [
                'name' => 'X',
                'link'  => 'https://www.x.com', // أو https://twitter.com
            ],
        ]);
    
    }
}
