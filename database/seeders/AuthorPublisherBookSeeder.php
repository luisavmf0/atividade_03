<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\Author;
use App\Models\Publisher;
use App\Models\Book;
use App\Models\Category;

class AuthorPublisherBookSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
    // 1. Verificamos se existem categorias no banco antes de começar
    $categories = \App\Models\Category::all();

    if ($categories->isEmpty()) {
        $this->command->error("ERRO: A tabela 'categories' está vazia! O CategorySeeder não rodou.");
        return; // Para o código para não tentar criar livros sem categorias
    }

    // 2. Criamos os autores
    \App\Models\Author::factory(100)->create()->each(function ($author) use ($categories) {
        $publisher = \App\Models\Publisher::factory()->create();

        // 3. Usamos as categorias que já existem no banco (evita erro de chave estrangeira)
        $category = $categories->random();

        // 4. Criamos os livros
        \App\Models\Book::factory(10)->create([
            'author_id' => $author->id,
            'category_id' => $category->id,
            'publisher_id' => $publisher->id,
            ]);
        });
    }
}