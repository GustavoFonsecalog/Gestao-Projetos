<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { ref, onMounted, reactive } from 'vue';

const projects = ref([]);
const showSuggestionModal = ref(false);
const suggestionText = ref('');
const selectedProjectId = ref(null);
const menuState = reactive({});
const showEmailModal = ref(false);
const emailTo = ref('');
const emailMessage = ref('');
const emailLoading = ref(false);
const emailError = ref('');
const emailSuccess = ref('');
const emailProjectId = ref(null);

function openSuggestionModal(projectId) {
    selectedProjectId.value = projectId;
    suggestionText.value = '';
    showSuggestionModal.value = true;
}
function closeSuggestionModal() {
    showSuggestionModal.value = false;
    suggestionText.value = '';
    selectedProjectId.value = null;
}
function sendSuggestion() {
    // Aqui você pode fazer um fetch/post para o backend futuramente
    // Por enquanto só fecha o modal
    closeSuggestionModal();
    // Exemplo: alert('Sugestão enviada!');
}

function openMenu(id) {
    menuState[id] = !menuState[id];
}
function closeMenu(id) {
    menuState[id] = false;
}
function handleArchive(project) {
    fetch(`/projects/${project.id}/archive`, { method: 'POST', headers: { 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]')?.content } })
        .then(r => r.json()).then(() => {
            // Remove o projeto da lista localmente
            projects.value = projects.value.filter(p => p.id !== project.id);
        });
    closeMenu(project.id);
}
function handleDuplicate(project) {
    fetch(`/projects/${project.id}/duplicate`, { method: 'POST', headers: { 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]')?.content } })
        .then(r => r.json()).then(() => window.location.reload());
    closeMenu(project.id);
}
function handleDownloadPdf(project) {
    window.open(`/projects/${project.id}/pdf`, '_blank');
    closeMenu(project.id);
}
function handleSendEmail(project) {
    emailTo.value = '';
    emailMessage.value = '';
    emailProjectId.value = project.id;
    emailError.value = '';
    emailSuccess.value = '';
    showEmailModal.value = true;
    closeMenu(project.id);
}
function sendEmail() {
    emailLoading.value = true;
    emailError.value = '';
    emailSuccess.value = '';
    fetch(`/projects/${emailProjectId.value}/send-email`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]')?.content
        },
        body: JSON.stringify({ to: emailTo.value, message: emailMessage.value })
    })
        .then(async r => {
            const data = await r.json();
            if (!r.ok) throw new Error(data.error || 'Erro ao enviar e-mail');
            emailSuccess.value = data.message;
        })
        .catch(e => { emailError.value = e.message; })
        .finally(() => { emailLoading.value = false; });
}
function closeEmailModal() {
    showEmailModal.value = false;
    emailTo.value = '';
    emailMessage.value = '';
    emailError.value = '';
    emailSuccess.value = '';
    emailProjectId.value = null;
}

function statusLabel(status) {
    if (status === 'em_andamento') return 'Em andamento';
    if (status === 'finalizado') return 'Finalizado';
    if (status === 'cancelado') return 'Cancelado';
    if (status === 'em_atraso') return 'Em atraso';
    return status;
}
function statusColor(status) {
    if (status === 'em_andamento') return 'bg-green-100 text-green-700';
    if (status === 'finalizado') return 'bg-gray-200 text-gray-700';
    if (status === 'cancelado') return 'bg-red-100 text-red-700';
    if (status === 'em_atraso') return 'bg-yellow-100 text-yellow-700';
    return 'bg-gray-100 text-gray-700';
}
function formatDate(date) {
    if (!date) return '-';
    return new Date(date).toLocaleDateString('pt-BR');
}
function getProgress(project) {
    // Simulação: se tiver orcamento_real e orcamento_estimado, calcula %; senão, random
    if (project.orcamento_estimado && project.orcamento_real) {
        let pct = Math.min(100, Math.round((project.orcamento_real / project.orcamento_estimado) * 100));
        return pct;
    }
    return Math.floor(Math.random() * 40) + 60;
}

onMounted(async () => {
    const response = await fetch('/projects');
    if (response.ok) {
        projects.value = await response.json();
    }
});
</script>

<template>
    <Head title="Projetos" />
    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-2xl font-bold text-grass">Meus Projetos</h2>
                <Link href="/projects/create" class="rounded-full bg-grass hover:bg-grass-dark text-white font-bold py-2 px-6 shadow transition">Novo Projeto</Link>
            </div>
        </template>
        <div class="py-8 bg-[#181c2f] min-h-screen">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    <div v-for="project in projects" :key="project.id" class="bg-[#23264a] rounded-xl shadow p-6 flex flex-col justify-between border-l-8 border-grass relative">
                        <div class="flex justify-between items-start mb-2">
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-bold" :class="statusColor(project.status)">
                                {{ statusLabel(project.status) }}
                            </span>
                            <div class="relative group">
                                <button @click="openMenu(project.id)" class="text-gray-400 hover:text-grass text-xl px-2 focus:outline-none">⋯</button>
                                <div v-if="menuState[project.id]" class="absolute right-0 mt-2 w-40 bg-white rounded shadow-lg z-10 py-2 text-sm">
                                    <button @click="handleArchive(project)" class="block w-full text-left px-4 py-2 hover:bg-gray-100 text-red-600">Excluir</button>
                                    <button @click="handleDuplicate(project)" class="block w-full text-left px-4 py-2 hover:bg-gray-100">Duplicar</button>
                                    <button @click="handleDownloadPdf(project)" class="block w-full text-left px-4 py-2 hover:bg-gray-100">Baixar PDF</button>
                                    <button @click="handleSendEmail(project)" class="block w-full text-left px-4 py-2 hover:bg-gray-100">Enviar por e-mail</button>
                                </div>
                            </div>
                        </div>
                        <h3 class="text-xl font-bold text-grass mb-1">{{ project.title }}</h3>
                        <div class="text-gray-200 text-sm mb-1">
                            <span class="font-medium">Cliente:</span> {{ project.client_name }}<br>
                            <span class="font-medium">Fase:</span> {{ project.phase }}
                        </div>
                        <div class="text-gray-200 text-sm mb-2">
                            <span class="font-medium">Equipe:</span>
                            {{ project.users.slice(0,2).map(u => u.name).join(', ') }}
                            <span v-if="project.users.length > 2">+{{ project.users.length - 2 }}</span>
                        </div>
                        <div class="mb-2">
                            <div class="h-2 w-full bg-gray-200 rounded-full overflow-hidden">
                                <div class="bg-green-500 h-full transition-all duration-300" :style="{ width: getProgress(project) + '%' }"></div>
                            </div>
                            <div class="text-xs text-gray-400 mt-1">Progresso: {{ getProgress(project) }}%</div>
                        </div>
                        <div class="flex justify-between text-xs text-gray-300 mt-2">
                            <span>Entrega: {{ formatDate(project.data_fim) }}</span>
                            <span>Orçamento: R$ {{ project.orcamento_estimado || '-' }} / {{ project.orcamento_real || '-' }}</span>
                        </div>
                        <div class="mt-4 flex gap-2">
                            <Link :href="`/projects/${project.id}/edit`" class="rounded-full bg-grass-light hover:bg-grass text-white px-4 py-1 text-sm font-semibold">Editar</Link>
                            <Link :href="`/projects/${project.id}`" class="rounded-full border border-grass text-grass px-4 py-1 text-sm font-semibold hover:bg-grass hover:text-white transition">Detalhes</Link>
                            <button @click="openSuggestionModal(project.id)" class="rounded-full border border-blue-400 text-blue-400 px-4 py-1 text-sm font-semibold hover:bg-blue-400 hover:text-white transition">Sugestão</button>
                        </div>
                    </div>
                </div>
                <div v-if="projects.length === 0" class="text-center text-gray-400 mt-12 text-lg">Nenhum projeto encontrado.</div>
            </div>
        </div>
                <div v-if="showSuggestionModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-40">
                    <div class="bg-white rounded-xl shadow-lg p-8 w-full max-w-md relative">
                        <button @click="closeSuggestionModal" class="absolute top-2 right-2 text-gray-400 hover:text-red-500 text-xl">&times;</button>
                        <h2 class="text-xl font-bold text-grass mb-4">Enviar sugestão</h2>
                        <textarea v-model="suggestionText" rows="4" class="w-full rounded border border-gray-300 p-3 mb-4 focus:ring-2 focus:ring-grass focus:outline-none" placeholder="Digite sua sugestão para este projeto..."></textarea>
                        <div class="flex justify-end gap-2">
                            <button @click="closeSuggestionModal" class="px-4 py-2 rounded bg-gray-200 text-gray-700 hover:bg-gray-300">Cancelar</button>
                            <button @click="sendSuggestion" :disabled="!suggestionText.trim()" class="px-4 py-2 rounded bg-grass text-white font-bold hover:bg-grass-dark disabled:opacity-50">Enviar</button>
                        </div>
                    </div>
                </div>
                <div v-if="showEmailModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-40">
                    <div class="bg-white rounded-xl shadow-lg p-8 w-full max-w-md relative">
                        <button @click="closeEmailModal" class="absolute top-2 right-2 text-gray-400 hover:text-red-500 text-xl">&times;</button>
                        <h2 class="text-xl font-bold text-grass mb-4">Enviar Projeto por E-mail</h2>
                        <input v-model="emailTo" type="email" class="w-full rounded border border-gray-300 p-3 mb-3 focus:ring-2 focus:ring-grass focus:outline-none" placeholder="E-mail do destinatário" />
                        <textarea v-model="emailMessage" rows="3" class="w-full rounded border border-gray-300 p-3 mb-4 focus:ring-2 focus:ring-grass focus:outline-none" placeholder="Mensagem (opcional)"></textarea>
                        <div v-if="emailError" class="text-red-600 mb-2">{{ emailError }}</div>
                        <div v-if="emailSuccess" class="text-green-600 mb-2">{{ emailSuccess }}</div>
                        <div class="flex justify-end gap-2">
                            <button @click="closeEmailModal" class="px-4 py-2 rounded bg-gray-200 text-gray-700 hover:bg-gray-300">Cancelar</button>
                            <button @click="sendEmail" :disabled="!emailTo || emailLoading" class="px-4 py-2 rounded bg-grass text-white font-bold hover:bg-grass-dark disabled:opacity-50">{{ emailLoading ? 'Enviando...' : 'Enviar' }}</button>
                        </div>
                    </div>
                </div>
    </AuthenticatedLayout>
</template>
