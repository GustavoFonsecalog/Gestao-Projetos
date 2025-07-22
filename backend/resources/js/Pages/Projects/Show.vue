<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, usePage } from '@inertiajs/vue3';
import { ref, onMounted } from 'vue';
import TasksKanban from './TasksKanban.vue';

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
        await fetchActivities();
    }
    submitting.value = false;
}
function formatDateTime(date) {
    if (!date) return '-';
    const d = new Date(date);
    if (isNaN(d)) return '-';
    return d.toLocaleString('pt-BR');
}

const activities = ref([]);
const loadingActivities = ref(false);

async function fetchActivities() {
    loadingActivities.value = true;
    const res = await fetch(`/projects/${project.id}/activities`);
    if (res.ok) {
        activities.value = await res.json();
    }
    loadingActivities.value = false;
}
onMounted(fetchActivities);

const showKanban = ref(false);
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
        <div class="py-12">
            <div class="mx-auto max-w-7xl px-4 mb-8 flex justify-end">
                <button @click="showKanban = !showKanban" class="bg-grass text-white font-bold px-6 py-2 rounded hover:bg-grass-dark transition">
                    {{ showKanban ? 'Ocultar Tarefas' : 'Ver Tarefas' }}
                </button>
            </div>
            <div class="mx-auto max-w-7xl px-4 grid grid-cols-1 md:grid-cols-3 gap-8 items-start">
                <!-- Detalhes do Projeto -->
                <div class="bg-white/95 rounded-2xl shadow-xl p-10 min-h-[500px] flex flex-col">
                    <h3 class="text-3xl font-extrabold text-grass mb-6">{{ project.title }}</h3>
                    <div class="flex-1 space-y-2">
                        <div><span class="font-bold text-grass">Cliente:</span> <span class="text-gray-700">{{ project.client_name }}</span></div>
                        <div><span class="font-bold text-grass">Fase:</span> <span class="text-gray-700">{{ project.phase }}</span></div>
                        <div><span class="font-bold text-grass">Equipe:</span> <span class="text-gray-700">{{ project.users.map(u => u.name).join(', ') }}</span></div>
                        <div><span class="font-bold text-grass">Status:</span> <span class="text-grass">{{ statusLabel(project.status) }}</span></div>
                        <div><span class="font-bold text-grass">Prioridade:</span> <span class="text-gray-700">{{ project.prioridade }}</span></div>
                        <div><span class="font-bold text-grass">Data de Início:</span> <span class="text-gray-700">{{ formatDate(project.data_inicio) }}</span></div>
                        <div><span class="font-bold text-grass">Data de Fim:</span> <span class="text-gray-700">{{ formatDate(project.data_fim) }}</span></div>
                        <div><span class="font-bold text-grass">Orçamento Estimado:</span> <span class="text-gray-700">{{ formatMoney(project.orcamento_estimado) }}</span></div>
                        <div><span class="font-bold text-grass">Orçamento Real:</span> <span class="text-gray-700">{{ formatMoney(project.orcamento_real) }}</span></div>
                        <div><span class="font-bold text-grass">Descrição:</span>
                            <div class="bg-gray-50 rounded p-2 text-gray-800 mt-1">{{ project.descricao }}</div>
                        </div>
                    </div>
                </div>
                <!-- Sugestões -->
                <div class="bg-white/95 rounded-2xl shadow-xl p-10 min-h-[500px] flex flex-col">
                    <h3 class="text-2xl font-extrabold text-grass mb-6">Sugestões</h3>
                    <form @submit.prevent="submitSuggestion" class="flex gap-2 mb-6">
                        <input v-model="newSuggestion" :disabled="submitting" class="flex-1 rounded border border-gray-300 p-2 focus:ring-2 focus:ring-grass focus:outline-none" placeholder="Deixe uma sugestão para este projeto..." />
                        <button type="submit" :disabled="submitting || !newSuggestion.trim()" class="rounded bg-grass text-white font-bold px-4 py-2 hover:bg-grass-dark disabled:opacity-50">Enviar</button>
                    </form>
                    <div class="flex-1">
                        <div v-if="loadingSuggestions" class="text-gray-400">Carregando sugestões...</div>
                        <div v-else-if="suggestions.length === 0" class="text-gray-400">Nenhuma sugestão ainda.</div>
                        <ul v-else class="space-y-6">
                            <li v-for="s in suggestions" :key="s.id" class="border-b border-gray-200 pb-2">
                                <div class="flex items-center gap-2 mb-1">
                                    <span class="font-bold text-grass">{{ s.user?.name || 'Usuário' }}</span>
                                    <span class="text-xs text-gray-400">{{ formatDateTime(s.created_at) }}</span>
                                </div>
                                <div class="text-gray-900">{{ s.text }}</div>
                            </li>
                        </ul>
                    </div>
                </div>
                <!-- Feed de Atividades -->
                <div class="bg-white/95 rounded-2xl shadow-xl p-10 min-h-[500px] flex flex-col">
                    <h3 class="text-2xl font-extrabold text-grass mb-6">Feed de Atividades</h3>
                    <div class="flex-1">
                        <div v-if="loadingActivities" class="text-gray-400">Carregando atividades...</div>
                        <div v-else-if="activities.length === 0" class="text-gray-400">Nenhuma atividade registrada.</div>
                        <ul v-else class="space-y-6">
                            <li v-for="a in activities" :key="a.id" class="border-b border-gray-200 pb-2">
                                <div class="flex items-center gap-2 mb-1">
                                    <span class="font-bold text-grass">{{ a.user?.name || 'Sistema' }}</span>
                                    <span class="text-xs text-gray-400">{{ formatDateTime(a.created_at) }}</span>
                                </div>
                                <div class="text-gray-900">{{ a.description }}</div>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
            <TasksKanban v-if="showKanban" />
        </div>
    </AuthenticatedLayout>
</template> 