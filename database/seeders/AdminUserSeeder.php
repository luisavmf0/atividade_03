<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        // Procura por um usuário com este e-mail. Se não achar, cria. Se achar, atualiza os dados.
        User::updateOrCreate(
            ['email' => 'admin@biblioteca.com'], // Coluna para verificar duplicidade
            [
                'name' => 'Administrador',
                'password' => Hash::make('senha123'),
                'role' => 'admin',
            ]
        );
    }
}
