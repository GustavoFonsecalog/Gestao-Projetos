<div style="font-family: Arial, sans-serif; color: #222;">
    <h2 style="color: #2d8a4b;">Gestão de Projetos - Notificação</h2>
    <p><strong>Projeto:</strong> {{ $project->title }}</p>
    <p><strong>Cliente:</strong> {{ $project->client_name }}</p>
    <p><strong>Fase:</strong> {{ $project->phase }}</p>
    <p><strong>Status:</strong> {{ $project->status }}</p>
    <p><strong>Mensagem:</strong> {{ $messageText }}</p>
    <hr>
    <small>Enviado automaticamente pelo sistema de gestão de projetos.</small>
</div> 