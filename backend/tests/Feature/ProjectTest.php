<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProjectTest extends TestCase
{
    use RefreshDatabase;

    public function test_usuario_autenticado_pode_criar_projeto()
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        $response = $this->post('/projects', [
            'title' => 'Projeto Teste',
            'client_name' => 'Cliente X',
            'phase' => 'Planejamento',
            'users' => [],
        ]);
        $response->assertStatus(201);
        $this->assertDatabaseHas('projects', [
            'title' => 'Projeto Teste',
            'user_id' => $user->id,
        ]);
    }

    public function test_usuario_autenticado_ve_apenas_projetos_proprios_ou_alocado()
    {
        $user = User::factory()->create();
        $outro = User::factory()->create();
        $proprio = Project::factory()->create(['user_id' => $user->id]);
        $alocado = Project::factory()->create();
        $alocado->users()->attach($user->id);
        $outroProjeto = Project::factory()->create(['user_id' => $outro->id]);
        $this->actingAs($user);
        $response = $this->get('/projects');
        $response->assertStatus(200);
        $ids = collect($response->json())->pluck('id');
        $this->assertTrue($ids->contains($proprio->id));
        $this->assertTrue($ids->contains($alocado->id));
        $this->assertFalse($ids->contains($outroProjeto->id));
    }

    public function test_usuario_autenticado_pode_editar_projeto_que_criou()
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        $projeto = Project::factory()->create(['user_id' => $user->id]);
        $response = $this->put("/projects/{$projeto->id}", [
            'title' => 'Novo Título',
            'client_name' => 'Novo Cliente',
            'phase' => 'Execução',
        ]);
        $response->assertStatus(200);
        $this->assertDatabaseHas('projects', [
            'id' => $projeto->id,
            'title' => 'Novo Título',
        ]);
    }

    public function test_usuario_autenticado_nao_pode_editar_projeto_de_outro()
    {
        $user = User::factory()->create();
        $outro = User::factory()->create();
        $projeto = Project::factory()->create(['user_id' => $outro->id]);
        $this->actingAs($user);
        $response = $this->put("/projects/{$projeto->id}", [
            'title' => 'Hackeado',
        ]);
        $response->assertStatus(403);
    }

    public function test_usuario_autenticado_pode_desativar_projeto_que_criou()
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        $projeto = Project::factory()->create(['user_id' => $user->id]);
        $response = $this->delete("/projects/{$projeto->id}");
        $response->assertStatus(200);
        $this->assertDatabaseHas('projects', [
            'id' => $projeto->id,
            'active' => false,
        ]);
    }

    public function test_usuario_autenticado_nao_pode_desativar_projeto_de_outro()
    {
        $user = User::factory()->create();
        $outro = User::factory()->create();
        $projeto = Project::factory()->create(['user_id' => $outro->id]);
        $this->actingAs($user);
        $response = $this->delete("/projects/{$projeto->id}");
        $response->assertStatus(403);
    }
}
