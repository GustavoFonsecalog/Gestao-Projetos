<template>
    <div class="min-h-screen flex flex-col items-center justify-center bg-gradient-to-br from-blue-700 to-grass p-8">
        <div class="bg-white bg-opacity-90 rounded-2xl shadow-xl max-w-3xl w-full p-10">
            <h1 class="text-3xl font-bold text-grass mb-4">Funcionalidades</h1>
            <ul class="space-y-6">
                <li class="flex items-start gap-4">
                    <span class="bg-grass text-white rounded-full p-2"><svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 17v-6a2 2 0 012-2h2a2 2 0 012 2v6m-6 0h6" /></svg></span>
                    <div>
                        <h2 class="text-lg font-semibold">Gestão de Projetos</h2>
                        <p class="text-gray-700">Crie, edite, duplique, exclua e acompanhe projetos com facilidade.</p>
                    </div>
                </li>
                <li class="flex items-start gap-4">
                    <span class="bg-grass text-white rounded-full p-2"><svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 9V7a5 5 0 00-10 0v2a5 5 0 0010 0z" /></svg></span>
                    <div>
                        <h2 class="text-lg font-semibold">Equipe e Colaboração</h2>
                        <p class="text-gray-700">Atribua membros, compartilhe responsabilidades e receba sugestões em cada projeto.</p>
                    </div>
                </li>
                <li class="flex items-start gap-4">
                    <span class="bg-grass text-white rounded-full p-2"><svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3" /></svg></span>
                    <div>
                        <h2 class="text-lg font-semibold">Acompanhamento de Prazos</h2>
                        <p class="text-gray-700">Visualize datas de início, fim e progresso dos projetos em tempo real.</p>
                    </div>
                </li>
                <li class="flex items-start gap-4">
                    <span class="bg-grass text-white rounded-full p-2"><svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12h.01M12 12h.01M9 12h.01M21 12c0 4.418-4.03 8-9 8s-9-3.582-9-8 4.03-8 9-8 9 3.582 9 8z" /></svg></span>
                    <div>
                        <h2 class="text-lg font-semibold">Orçamento e Status</h2>
                        <p class="text-gray-700">Controle orçamentos estimados e reais, status e prioridades de cada projeto.</p>
                    </div>
                </li>
                <li class="flex items-start gap-4">
                    <span class="bg-grass text-white rounded-full p-2"><svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" /></svg></span>
                    <div>
                        <h2 class="text-lg font-semibold">Upload de Arquivos e Assinatura Digital</h2>
                        <p class="text-gray-700">Anexe documentos, imagens e assine digitalmente projetos.</p>
                    </div>
                </li>
                <li class="flex items-start gap-4">
                    <span class="bg-grass text-white rounded-full p-2"><svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 12v1a4 4 0 01-8 0v-1" /></svg></span>
                    <div>
                        <h2 class="text-lg font-semibold">Envio de E-mails e PDF</h2>
                        <p class="text-gray-700">Envie detalhes do projeto por e-mail e gere relatórios em PDF com um clique.</p>
                    </div>
                </li>
            </ul>
            <Link href="/login" class="inline-block mt-8 text-grass font-bold hover:underline">Voltar para o início</Link>
        </div>
    </div>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';
</script> 