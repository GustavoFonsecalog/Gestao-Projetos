<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref, onMounted } from 'vue';
import { usePage } from '@inertiajs/vue3';

const users = ref([]);
const project = usePage().props.project;
const form = useForm({
    title: project.title,
    client_name: project.client_name,
    phase: project.phase,
    descricao: project.descricao,
    data_inicio: project.data_inicio,
    data_fim: project.data_fim,
    status: project.status,
    prioridade: project.prioridade,
    orcamento_estimado: project.orcamento_estimado,
    orcamento_real: project.orcamento_real,
    users: project.users.map(u => u.id),
});

onMounted(async () => {
    const response = await fetch('/users');
    if (response.ok) {
        users.value = await response.json();
    }
});

const submit = () => {
    form.put(`/projects/${project.id}`, {
        onSuccess: () => {},
    });
};
</script>
<template>
    <Head title="Editar Projeto" />
    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-2xl font-bold text-grass">Editar Projeto</h2>
                <Link href="/dashboard" class="text-grass hover:underline">Voltar</Link>
            </div>
        </template>
        <div class="py-8">
            <div class="mx-auto max-w-lg bg-white rounded-xl shadow p-8">
                <form @submit.prevent="submit" class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="title" class="block text-grass font-semibold">Título</label>
                            <input id="title" v-model="form.title" required class="mt-1 block w-full rounded-full border border-grass bg-grass-50 focus:border-grass focus:ring focus:ring-grass/30" />
                        </div>
                        <div>
                            <label for="client_name" class="block text-grass font-semibold">Cliente</label>
                            <input id="client_name" v-model="form.client_name" required class="mt-1 block w-full rounded-full border border-grass bg-grass-50 focus:border-grass focus:ring focus:ring-grass/30" />
                        </div>
                        <div>
                            <label for="phase" class="block text-grass font-semibold">Fase</label>
                            <input id="phase" v-model="form.phase" required class="mt-1 block w-full rounded-full border border-grass bg-grass-50 focus:border-grass focus:ring focus:ring-grass/30" />
                        </div>
                        <div>
                            <label for="users" class="block text-grass font-semibold">Equipe</label>
                            <select id="users" v-model="form.users" multiple class="mt-1 block w-full rounded-lg border border-grass bg-grass-50 focus:border-grass focus:ring focus:ring-grass/30">
                                <option v-for="user in users" :key="user.id" :value="user.id">{{ user.name }}</option>
                            </select>
                        </div>
                        <div class="md:col-span-2">
                            <label for="descricao" class="block text-grass font-semibold">Descrição</label>
                            <textarea id="descricao" v-model="form.descricao" rows="3" class="mt-1 block w-full rounded-lg border border-grass bg-grass-50 focus:border-grass focus:ring focus:ring-grass/30"></textarea>
                        </div>
                        <div>
                            <label for="data_inicio" class="block text-grass font-semibold">Data de Início</label>
                            <input id="data_inicio" type="date" v-model="form.data_inicio" class="mt-1 block w-full rounded-full border border-grass bg-grass-50 focus:border-grass focus:ring focus:ring-grass/30" />
                        </div>
                        <div>
                            <label for="data_fim" class="block text-grass font-semibold">Data de Fim</label>
                            <input id="data_fim" type="date" v-model="form.data_fim" class="mt-1 block w-full rounded-full border border-grass bg-grass-50 focus:border-grass focus:ring focus:ring-grass/30" />
                        </div>
                        <div>
                            <label for="status" class="block text-grass font-semibold">Status</label>
                            <select id="status" v-model="form.status" class="mt-1 block w-full rounded-full border border-grass bg-grass-50 focus:border-grass focus:ring focus:ring-grass/30">
                                <option value="em_andamento">Em andamento</option>
                                <option value="finalizado">Finalizado</option>
                                <option value="cancelado">Cancelado</option>
                            </select>
                        </div>
                        <div>
                            <label for="prioridade" class="block text-grass font-semibold">Prioridade</label>
                            <select id="prioridade" v-model="form.prioridade" class="mt-1 block w-full rounded-full border border-grass bg-grass-50 focus:border-grass focus:ring focus:ring-grass/30">
                                <option value="baixa">Baixa</option>
                                <option value="media">Média</option>
                                <option value="alta">Alta</option>
                                <option value="urgente">Urgente</option>
                            </select>
                        </div>
                        <div>
                            <label for="orcamento_estimado" class="block text-grass font-semibold">Orçamento Estimado</label>
                            <input id="orcamento_estimado" type="number" step="0.01" v-model="form.orcamento_estimado" class="mt-1 block w-full rounded-full border border-grass bg-grass-50 focus:border-grass focus:ring focus:ring-grass/30" />
                        </div>
                        <div>
                            <label for="orcamento_real" class="block text-grass font-semibold">Orçamento Real</label>
                            <input id="orcamento_real" type="number" step="0.01" v-model="form.orcamento_real" class="mt-1 block w-full rounded-full border border-grass bg-grass-50 focus:border-grass focus:ring focus:ring-grass/30" />
                        </div>
                    </div>
                    <button type="submit" class="w-full rounded-full bg-grass hover:bg-grass-dark text-white font-bold py-2 px-4">Salvar</button>
                </form>
            </div>
        </div>
    </AuthenticatedLayout>
</template> 