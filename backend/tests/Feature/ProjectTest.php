<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProjectTest extends TestCase
{
    use RefreshDatabase;

    // Testa se um usuário autenticado consegue criar um projeto normalmente
    public function test_usuario_autenticado_pode_criar_projeto()
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        $response = $this->post('/projects', [
            'title' => 'Projeto Teste',
            'client_name' => 'Cliente X',
            'phase' => 'Planejamento',
            'status' => 'em_andamento',
            'prioridade' => 'media',
            'users' => [],
        ]);
        $response->assertRedirect('/dashboard');
        $this->assertDatabaseHas('projects', [
            'title' => 'Projeto Teste',
            'user_id' => $user->id,
        ]);
    }

    // Testa se o usuário só vê projetos próprios ou em que está alocado
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

    // Testa se o usuário pode editar um projeto que ele mesmo criou
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
        $response->assertRedirect();
        $this->assertDatabaseHas('projects', [
            'id' => $projeto->id,
            'title' => 'Novo Título',
        ]);
    }

    // Testa se o usuário não pode editar projeto de outro usuário
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

    // Testa se o usuário pode desativar (soft delete) um projeto que criou
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

    // Testa se o usuário não pode desativar projeto de outro usuário
    public function test_usuario_autenticado_nao_pode_desativar_projeto_de_outro()
    {
        $user = User::factory()->create();
        $outro = User::factory()->create();
        $projeto = Project::factory()->create(['user_id' => $outro->id]);
        $this->actingAs($user);
        $response = $this->delete("/projects/{$projeto->id}");
        $response->assertStatus(403);
    }

    // Testa criação de projeto preenchendo todos os campos opcionais e obrigatórios
    public function test_usuario_pode_criar_projeto_com_todos_os_campos()
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        $response = $this->post('/projects', [
            'title' => 'Projeto Completo',
            'client_name' => 'Cliente Completo',
            'phase' => 'Execução',
            'descricao' => 'Descrição detalhada',
            'data_inicio' => '2025-07-20',
            'data_fim' => '2025-07-30',
            'status' => 'em_andamento',
            'prioridade' => 'alta',
            'orcamento_estimado' => 10000,
            'orcamento_real' => 8000,
            'users' => [],
        ]);
        $response->assertRedirect('/dashboard');
        $this->assertDatabaseHas('projects', [
            'title' => 'Projeto Completo',
            'descricao' => 'Descrição detalhada',
            'data_inicio' => '2025-07-20 00:00:00',
            'data_fim' => '2025-07-30 00:00:00',
            'status' => 'em_andamento',
            'prioridade' => 'alta',
            'orcamento_estimado' => 10000,
            'orcamento_real' => 8000,
        ]);
    }

    // Testa se o usuário pode excluir (soft delete) um projeto
    public function test_usuario_pode_excluir_projeto_soft_delete()
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        $projeto = Project::factory()->create(['user_id' => $user->id]);
        $response = $this->post("/projects/{$projeto->id}/archive");
        $response->assertStatus(200);
        $this->assertSoftDeleted('projects', ['id' => $projeto->id]);
    }

    // Testa duplicação de projeto
    public function test_usuario_pode_duplicar_projeto()
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        $projeto = Project::factory()->create(['user_id' => $user->id, 'title' => 'Original']);
        $response = $this->post("/projects/{$projeto->id}/duplicate");
        $response->assertStatus(200);
        $this->assertDatabaseHas('projects', ['title' => 'Original (Cópia)']);
    }

    // Testa se o usuário pode baixar o PDF do projeto
    public function test_usuario_pode_baixar_pdf_do_projeto()
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        $projeto = Project::factory()->create(['user_id' => $user->id]);
        $response = $this->get("/projects/{$projeto->id}/pdf");
        $response->assertStatus(200);
        $response->assertHeader('content-type', 'application/pdf');
    }

    // Testa envio de e-mail com informações do projeto
    public function test_envio_de_email_do_projeto()
    {
        \Mail::fake();
        $user = User::factory()->create();
        $this->actingAs($user);
        $projeto = Project::factory()->create(['user_id' => $user->id]);
        $response = $this->post("/projects/{$projeto->id}/send-email", [
            'to' => 'destino@teste.com',
            'message' => 'Mensagem teste',
        ]);
        $response->assertStatus(200);
        \Mail::assertSent(\App\Mail\ProjectActionMail::class);
    }

    // Testa validação dos campos obrigatórios ao criar projeto
    public function test_validacao_campos_obrigatorios()
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        $response = $this->post('/projects', []);
        $response->assertSessionHasErrors(['title', 'client_name', 'phase', 'status', 'prioridade']);
    }

    // Testa edição de todos os campos do projeto
    public function test_usuario_pode_editar_todos_os_campos_do_projeto()
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        $projeto = Project::factory()->create(['user_id' => $user->id]);
        $response = $this->put("/projects/{$projeto->id}", [
            'title' => 'Editado',
            'client_name' => 'Editado',
            'phase' => 'Editado',
            'descricao' => 'Nova descrição',
            'data_inicio' => '2025-07-21',
            'data_fim' => '2025-07-31',
            'status' => 'finalizado',
            'prioridade' => 'baixa',
            'orcamento_estimado' => 5000,
            'orcamento_real' => 4000,
            'users' => [],
        ]);
        $response->assertRedirect();
        $this->assertDatabaseHas('projects', [
            'id' => $projeto->id,
            'title' => 'Editado',
            'descricao' => 'Nova descrição',
            'data_inicio' => '2025-07-21 00:00:00',
            'data_fim' => '2025-07-31 00:00:00',
            'status' => 'finalizado',
            'prioridade' => 'baixa',
            'orcamento_estimado' => 5000,
            'orcamento_real' => 4000,
        ]);
    }

    // Testa todo o fluxo de tarefas e subtarefas: criar, editar, mover, concluir, excluir
    public function test_usuario_pode_criar_editar_excluir_tarefa_e_subtarefa()
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        $projeto = Project::factory()->create(['user_id' => $user->id]);
        $response = $this->post("/projects/{$projeto->id}/tasks", [
            'title' => 'Tarefa Kanban',
            'status' => 'a_fazer',
            'prioridade' => 'media',
        ]);
        $response->assertStatus(201);
        $taskId = $response->json('id');
        $this->assertDatabaseHas('tasks', ['title' => 'Tarefa Kanban', 'project_id' => $projeto->id]);
        $this->put("/tasks/{$taskId}", ['title' => 'Tarefa Editada', 'status' => 'em_andamento', 'prioridade' => 'alta'])
            ->assertStatus(200);
        $this->assertDatabaseHas('tasks', ['title' => 'Tarefa Editada', 'status' => 'em_andamento', 'prioridade' => 'alta']);
        $this->put("/tasks/{$taskId}", ['status' => 'concluida'])->assertStatus(200);
        $this->assertDatabaseHas('tasks', ['id' => $taskId, 'status' => 'concluida']);
        $response = $this->post("/tasks/{$taskId}/subtasks", [
            'title' => 'Subtarefa 1',
            'status' => 'a_fazer',
        ]);
        $response->assertStatus(201);
        $subId = $response->json('id');
        $this->assertDatabaseHas('subtasks', ['title' => 'Subtarefa 1', 'task_id' => $taskId]);
        $this->put("/subtasks/{$subId}", ['is_checked' => true])->assertStatus(200);
        $this->assertDatabaseHas('subtasks', ['id' => $subId, 'is_checked' => 1]);
        $this->delete("/subtasks/{$subId}")->assertStatus(200);
        $this->assertDatabaseMissing('subtasks', ['id' => $subId]);
        $this->delete("/tasks/{$taskId}")->assertStatus(200);
        $this->assertDatabaseMissing('tasks', ['id' => $taskId]);
    }
}
