<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'fernando@maisaqui.com.br'],
            [
                'name' => 'Fernando',
                'empresa' => 'Mais Aqui',
                'password' => bcrypt('123456'),
                'email_verified_at' => now(),
            ]
        );

        $this->command->info('Usuário fernando@maisaqui.com.br criado/atualizado com sucesso.');
    }
}
