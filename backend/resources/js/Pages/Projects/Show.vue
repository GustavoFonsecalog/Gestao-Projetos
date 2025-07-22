<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, usePage } from '@inertiajs/vue3';
import { ref, onMounted } from 'vue';

const project = usePage().props.project;

function formatMoney(value) {
    if (value === null || value === undefined || value === '') return '-';
    return Number(value).toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' });
}
function formatDate(date) {
    if (!date) return '-';
    const d = new Date(date);
    if (isNaN(d)) return '-';
    return d.toLocaleDateString('pt-BR');
}
function statusLabel(status) {
    if (status === 'em_andamento') return 'Em andamento';
    if (status === 'finalizado') return 'Finalizado';
    if (status === 'cancelado') return 'Cancelado';
    return status;
}

const suggestions = ref([]);
const newSuggestion = ref('');
const loadingSuggestions = ref(false);
const submitting = ref(false);

async function fetchSuggestions() {
    loadingSuggestions.value = true;
    const res = await fetch(`/projects/${project.id}/suggestions`);
    suggestions.value = await res.json();
    loadingSuggestions.value = false;
}
onMounted(fetchSuggestions);

async function submitSuggestion() {
    if (!newSuggestion.value.trim()) return;
    submitting.value = true;
    const res = await fetch(`/projects/${project.id}/suggestions`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]')?.content
        },
        body: JSON.stringify({ text: newSuggestion.value })
    });
    if (res.ok) {
        newSuggestion.value = '';
        await fetchSuggestions();
    }
    submitting.value = false;
}
function formatDateTime(date) {
    if (!date) return '-';
    const d = new Date(date);
    if (isNaN(d)) return '-';
    return d.toLocaleString('pt-BR');
}
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
                    <div><span class="font-semibold text-grass">Status:</span> <span :class="project.status === 'em_andamento' ? 'text-green-600' : project.status === 'finalizado' ? 'text-gray-600' : 'text-red-600'">{{ statusLabel(project.status) }}</span></div>
                    <div><span class="font-semibold text-grass">Prioridade:</span> {{ project.prioridade }}</div>
                    <div><span class="font-semibold text-grass">Data de Início:</span> {{ formatDate(project.data_inicio) }}</div>
                    <div><span class="font-semibold text-grass">Data de Fim:</span> {{ formatDate(project.data_fim) }}</div>
                    <div><span class="font-semibold text-grass">Orçamento Estimado:</span> {{ formatMoney(project.orcamento_estimado) }}</div>
                    <div><span class="font-semibold text-grass">Orçamento Real:</span> {{ formatMoney(project.orcamento_real) }}</div>
                </div>
                <div class="mt-4">
                    <span class="font-semibold text-grass">Descrição:</span>
                    <div class="bg-gray-50 rounded p-3 mt-1 text-gray-700 min-h-[48px]">{{ project.descricao || 'Sem descrição.' }}</div>
                </div>
            </div>
            <!-- Sugestões -->
            <div class="mx-auto max-w-lg bg-white rounded-xl shadow p-8 mt-8">
                <h3 class="text-lg font-bold text-grass mb-4">Sugestões</h3>
                <form @submit.prevent="submitSuggestion" class="flex gap-2 mb-4">
                    <input v-model="newSuggestion" :disabled="submitting" class="flex-1 rounded border border-gray-300 p-2 focus:ring-2 focus:ring-grass focus:outline-none" placeholder="Deixe uma sugestão para este projeto..." />
                    <button type="submit" :disabled="submitting || !newSuggestion.trim()" class="rounded bg-grass text-white font-bold px-4 py-2 hover:bg-grass-dark disabled:opacity-50">Enviar</button>
                </form>
                <div v-if="loadingSuggestions" class="text-gray-400">Carregando sugestões...</div>
                <div v-else-if="suggestions.length === 0" class="text-gray-400">Nenhuma sugestão ainda.</div>
                <ul v-else class="space-y-4">
                    <li v-for="s in suggestions" :key="s.id" class="border-b pb-2">
                        <div class="text-sm text-gray-700 mb-1">{{ s.user?.name || 'Usuário' }} <span class="text-xs text-gray-400">em {{ formatDateTime(s.created_at) }}</span></div>
                        <div class="text-gray-900">{{ s.text }}</div>
                    </li>
                </ul>
            </div>
        </div>
    </AuthenticatedLayout>
</template> 