<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Link;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
         Link::factory(10)->create();

        $targetUrl = 'https://apple.com';
        $slug = 'apple';

        Link::factory()->create([
            'target_url' => $targetUrl,
            'slug' => $slug
        ]);
    }
}
