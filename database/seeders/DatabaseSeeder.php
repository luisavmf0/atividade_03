<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

use Database\Seeders\CategorySeeder;
use Database\Seeders\AuthorPublisherBookSeeder;
use Database\Seeders\AdminUserSeeder; 

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            CategorySeeder::class,
            AuthorPublisherBookSeeder::class,
            AdminUserSeeder::class, 
        ]);
    }
}