<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Borrowing;
use App\Models\Book;

class UserBorrowingSeeder extends Seeder
{
    public function run(): void
    {
        // Criar 10 usuários com empréstimos
        User::factory(10)->create()->each(function ($user) {
            Borrowing::factory(rand(1, 5))->create([
                'user_id' => $user->id,
                'book_id' => Book::inRandomOrder()->first()->id,
            ]);
        });
    }
}
