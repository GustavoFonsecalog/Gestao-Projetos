<html>
<head>
    <meta charset="utf-8">
    <title>Projeto {{ $project->title }}</title>
    <style>
        body { font-family: Arial, sans-serif; color: #222; }
        h2 { color: #2d8a4b; }
        .label { font-weight: bold; }
        .section { margin-bottom: 18px; }
        .box { border: 1px solid #e0e0e0; border-radius: 8px; padding: 12px; margin-bottom: 12px; }
    </style>
</head>
<body>
    <h2>Resumo do Projeto</h2>
    <div class="section box">
        <span class="label">Título:</span> {{ $project->title }}<br>
        <span class="label">Cliente:</span> {{ $project->client_name }}<br>
        <span class="label">Fase:</span> {{ $project->phase }}<br>
        <span class="label">Status:</span> {{ $project->status }}<br>
        <span class="label">Prioridade:</span> {{ $project->prioridade }}<br>
        <span class="label">Data de Início:</span> {{ $project->data_inicio }}<br>
        <span class="label">Data de Fim:</span> {{ $project->data_fim }}<br>
        <span class="label">Orçamento Estimado:</span> R$ {{ $project->orcamento_estimado }}<br>
        <span class="label">Orçamento Real:</span> R$ {{ $project->orcamento_real }}<br>
    </div>
    <div class="section box">
        <span class="label">Descrição:</span><br>
        {{ $project->descricao }}
    </div>
    <div class="section box">
        <span class="label">Equipe:</span><br>
        @foreach($project->users as $user)
            - {{ $user->name }}<br>
        @endforeach
    </div>
    <small>Gerado em {{ date('d/m/Y H:i') }}</small>
</body>
</html> 