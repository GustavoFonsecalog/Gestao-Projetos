<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, usePage } from '@inertiajs/vue3';

const project = usePage().props.project;
</script>
<template>
    <Head title="Detalhes do Projeto" />
    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-2xl font-bold text-grass">Detalhes do Projeto</h2>
                <Link href="/dashboard" class="text-grass hover:underline">Voltar</Link>
            </div>
        </template>
        <div class="py-8">
            <div class="mx-auto max-w-lg bg-white rounded-xl shadow p-8 flex flex-col gap-4">
                <h3 class="text-2xl font-bold text-grass mb-2">{{ project.title }}</h3>
                <div class="grid grid-cols-1 gap-2">
                    <div><span class="font-semibold text-grass">Cliente:</span> {{ project.client_name }}</div>
                    <div><span class="font-semibold text-grass">Fase:</span> {{ project.phase }}</div>
                    <div><span class="font-semibold text-grass">Equipe:</span> {{ project.users.map(u => u.name).join(', ') }}</div>
                    <div><span class="font-semibold text-grass">Status:</span> <span :class="project.status === 'em_andamento' ? 'text-green-600' : project.status === 'finalizado' ? 'text-gray-600' : 'text-red-600'">{{ project.status }}</span></div>
                    <div><span class="font-semibold text-grass">Prioridade:</span> {{ project.prioridade }}</div>
                    <div><span class="font-semibold text-grass">Data de Início:</span> {{ project.data_inicio || '-' }}</div>
                    <div><span class="font-semibold text-grass">Data de Fim:</span> {{ project.data_fim || '-' }}</div>
                    <div><span class="font-semibold text-grass">Orçamento Estimado:</span> R$ {{ project.orcamento_estimado || '-' }}</div>
                    <div><span class="font-semibold text-grass">Orçamento Real:</span> R$ {{ project.orcamento_real || '-' }}</div>
                </div>
                <div class="mt-4">
                    <span class="font-semibold text-grass">Descrição:</span>
                    <div class="bg-gray-50 rounded p-3 mt-1 text-gray-700 min-h-[48px]">{{ project.descricao || 'Sem descrição.' }}</div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template> 