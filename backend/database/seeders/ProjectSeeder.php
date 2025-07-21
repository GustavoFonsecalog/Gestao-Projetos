<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Project;
use App\Models\User;

class ProjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Cria 5 usuários
        $users = User::factory(5)->create();

        // Cria 10 projetos, cada um com um criador e equipe alocada
        Project::factory(10)->create()->each(function ($project) use ($users) {
            // Aloca de 1 a 3 usuários aleatórios em cada projeto
            $project->users()->attach($users->random(rand(1, 3))->pluck('id')->toArray());
        });
    }
}
