<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

use Database\Seeders\CategorySeeder;
use Database\Seeders\AuthorPublisherBookSeeder;

class DatabaseSeeder extends Seeder
{
   public function run(): void
{
    $this->call([
        CategorySeeder::class,
        AuthorPublisherBookSeeder::class, // Verifique se isso está escrito corretamente
    ]);
}
}