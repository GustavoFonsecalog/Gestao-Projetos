<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('client_name');
            $table->text('descricao')->nullable();
            $table->date('data_inicio')->nullable();
            $table->date('data_fim')->nullable();
            $table->enum('status', ['em_andamento', 'finalizado', 'cancelado'])->default('em_andamento');
            $table->enum('prioridade', ['baixa', 'media', 'alta', 'urgente'])->default('media');
            $table->decimal('orcamento_estimado', 15, 2)->nullable();
            $table->decimal('orcamento_real', 15, 2)->nullable();
            $table->string('phase');
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // criador do projeto
            $table->boolean('active')->default(true); // status ativo/desativado
            $table->string('signature_path')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
