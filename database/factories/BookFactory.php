<?php

namespace Database\Factories;

use App\Models\Author;
use App\Models\Category;
use App\Models\Publisher;

use App\Models\Book;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Book>
 */
class BookFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
  // database/factories/BookFactory.php
    public function definition(): array
{
       return [
    'title' => fake()->sentence(),
    'published_year' => fake()->year(),
    'pages' => fake()->numberBetween(100, 800),
        ];    
    }
}
