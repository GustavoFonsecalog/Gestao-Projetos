<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref, onMounted, watch, nextTick } from 'vue';
import vueFilePond from 'vue-filepond';
import 'filepond/dist/filepond.min.css';
import SignaturePad from 'signature_pad';
import Multiselect from 'vue-multiselect';
import 'vue-multiselect/dist/vue-multiselect.min.css';

const users = ref([]);
const form = useForm({
    title: '',
    client_name: '',
    phase: '',
    descricao: '',
    data_inicio: '',
    data_fim: '',
    status: 'em_andamento',
    prioridade: 'media',
    orcamento_estimado: '',
    orcamento_real: '',
    users: [],
    signature_base64: '',
});

const FilePond = vueFilePond();
const files = ref([]);
const signaturePad = ref(null);
const signatureData = ref('');

onMounted(async () => {
    const response = await fetch('/users');
    if (response.ok) {
        users.value = await response.json();
    }
    await nextTick();
    if (signaturePad.value) {
        const pad = new SignaturePad(signaturePad.value);
        signaturePad.value._instance = pad;
    }
});

watch(signaturePad, async (el) => {
    await nextTick();
    if (el) {
        const pad = new SignaturePad(el);
        el._instance = pad;
    }
});

function clearSignature() {
    if (signaturePad.value && signaturePad.value._instance) {
        signaturePad.value._instance.clear();
        signatureData.value = '';
    }
}

function saveSignature() {
    if (signaturePad.value && signaturePad.value._instance && !signaturePad.value._instance.isEmpty()) {
        signatureData.value = signaturePad.value._instance.toDataURL();
    }
}

const submit = () => {
    form.signature_base64 = signatureData.value;
    form.users = form.users.map(u => u.id); // Corrige para enviar apenas IDs
    form.post('/projects', {
        onSuccess: () => form.reset(),
    });
};
</script>
<template>
    <Head title="Novo Projeto" />
    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-2xl font-bold text-grass">Novo Projeto</h2>
                <Link href="/dashboard" class="text-grass hover:underline">Voltar</Link>
            </div>
        </template>
        <div class="py-8 flex justify-center min-h-[80vh] bg-[#232544]">
            <div class="flex flex-col md:flex-row gap-8 w-full max-w-7xl">
                <form @submit.prevent="submit" class="flex-1 bg-white rounded-xl shadow-lg p-8 gap-y-6 flex flex-col">
                    <div>
                        <h3 class="text-lg font-bold text-grass mb-4 flex items-center gap-2">Informações do Projeto</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="title" class="block text-gray-700 text-sm font-medium mb-1">Título</label>
                                <input id="title" v-model="form.title" required class="w-full h-12 rounded-xl border border-gray-200 bg-gray-50 text-gray-900 placeholder-gray-400 focus:ring-2 focus:ring-grass focus:outline-none px-4 text-base" placeholder="Digite o título" />
                            </div>
                            <div>
                                <label for="client_name" class="block text-gray-700 text-sm font-medium mb-1">Cliente</label>
                                <input id="client_name" v-model="form.client_name" required class="w-full h-12 rounded-xl border border-gray-200 bg-gray-50 text-gray-900 placeholder-gray-400 focus:ring-2 focus:ring-grass focus:outline-none px-4 text-base" placeholder="Digite o nome do cliente" />
                            </div>
                        </div>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-grass mb-4 flex items-center gap-2">Equipe e Fase</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="phase" class="block text-gray-700 text-sm font-medium mb-1">Fase</label>
                                <input id="phase" v-model="form.phase" required class="w-full h-12 rounded-xl border border-gray-200 bg-gray-50 text-gray-900 placeholder-gray-400 focus:ring-2 focus:ring-grass focus:outline-none px-4 text-base" placeholder="Digite a fase" />
                            </div>
                            <div>
                                <label for="users" class="block text-gray-700 text-sm font-medium mb-1">Equipe</label>
                                <Multiselect
                                    id="users"
                                    v-model="form.users"
                                    :options="users.map(u => ({ id: u.id, name: u.name }))"
                                    :multiple="true"
                                    :close-on-select="false"
                                    :clear-on-select="false"
                                    :preserve-search="true"
                                    placeholder="Selecione os membros da equipe"
                                    label="name"
                                    track-by="id"
                                    :preselect-first="false"
                                    class="dark-multiselect"
                                />
                            </div>
                        </div>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-grass mb-4 flex items-center gap-2">Período e Status</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <div class="flex flex-col">
                                <label for="data_inicio" class="block text-gray-700 text-sm font-medium mb-1">Início</label>
                                <input id="data_inicio" type="date" v-model="form.data_inicio" class="w-full h-12 rounded-2xl border border-gray-200 bg-gray-50 text-gray-900 focus:ring-2 focus:ring-grass focus:outline-none px-4 text-base" />
                            </div>
                            <div class="flex flex-col">
                                <label for="data_fim" class="block text-gray-700 text-sm font-medium mb-1">Fim</label>
                                <input id="data_fim" type="date" v-model="form.data_fim" class="w-full h-12 rounded-2xl border border-gray-200 bg-gray-50 text-gray-900 focus:ring-2 focus:ring-grass focus:outline-none px-4 text-base" />
                            </div>
                            <div class="flex flex-col">
                                <label for="status" class="block text-gray-700 text-sm font-medium mb-1">Status</label>
                                <select id="status" v-model="form.status" class="w-full h-12 rounded-2xl border border-gray-200 bg-gray-50 text-gray-900 focus:ring-2 focus:ring-grass focus:outline-none px-4 text-base">
                                    <option value="em_andamento">Em andamento</option>
                                    <option value="finalizado">Finalizado</option>
                                    <option value="cancelado">Cancelado</option>
                                </select>
                            </div>
                            <div class="flex flex-col">
                                <label for="prioridade" class="block text-gray-700 text-sm font-medium mb-1">Prioridade</label>
                                <select id="prioridade" v-model="form.prioridade" class="w-full h-12 rounded-2xl border border-gray-200 bg-gray-50 text-gray-900 focus:ring-2 focus:ring-grass focus:outline-none px-4 text-base">
                                    <option value="baixa">Baixa</option>
                                    <option value="media">Média</option>
                                    <option value="alta">Alta</option>
                                    <option value="urgente">Urgente</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-grass mb-4 flex items-center gap-2">Orçamentos</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="orcamento_estimado" class="block text-gray-700 text-sm font-medium mb-1">Orçamento Estimado</label>
                                <input id="orcamento_estimado" type="number" step="0.01" v-model="form.orcamento_estimado" class="w-full h-12 rounded-xl border border-gray-200 bg-gray-50 text-gray-900 placeholder-gray-400 focus:ring-2 focus:ring-grass focus:outline-none px-4 text-base" placeholder="R$" />
                            </div>
                            <div>
                                <label for="orcamento_real" class="block text-gray-700 text-sm font-medium mb-1">Orçamento Real</label>
                                <input id="orcamento_real" type="number" step="0.01" v-model="form.orcamento_real" class="w-full h-12 rounded-xl border border-gray-200 bg-gray-50 text-gray-900 placeholder-gray-400 focus:ring-2 focus:ring-grass focus:outline-none px-4 text-base" placeholder="R$" />
                            </div>
                        </div>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-grass mb-4 flex items-center gap-2">Descrição</h3>
                        <label for="descricao" class="block text-gray-700 text-sm font-medium mb-1">Descrição do Projeto</label>
                        <textarea id="descricao" v-model="form.descricao" rows="4" class="w-full rounded-xl border border-gray-200 bg-gray-50 text-gray-900 placeholder-gray-400 focus:ring-2 focus:ring-grass focus:outline-none px-4 text-base" placeholder="Descreva o projeto"></textarea>
                    </div>
                    <div class="w-full flex justify-end mt-4">
                        <button type="submit" class="rounded-xl bg-grass hover:bg-grass-dark text-white font-bold py-3 px-12 text-lg shadow-lg transition-all duration-150">Salvar</button>
                    </div>
                </form>
                <div class="flex-1 flex flex-col gap-8">
                    <div class="h-full flex items-center justify-center mx-2 text-gray-400 text-sm select-none">Preencha os dados do projeto e anexe os arquivos necessários.</div>
                    <div class="bg-white rounded-xl shadow-lg p-8 flex flex-col gap-8 mt-0">
                        <div>
                            <h3 class="text-lg font-bold text-grass mb-4 flex items-center gap-2">Upload de Arquivos</h3>
                            <div class="bg-gray-50 rounded-xl p-2 shadow-sm">
                                <FilePond
                                    ref="pond"
                                    :files="files"
                                    allow-multiple="true"
                                    accepted-file-types="['application/pdf','image/*']"
                                    label-idle="<span class='text-gray-500'>Arraste e solte arquivos ou <span class='filepond--label-action text-grass'>Selecione</span></span>"
                                />
                            </div>
                        </div>  
                        <div>
                            <h3 class="text-lg font-bold text-grass mb-4 flex items-center gap-2">Assinatura</h3>
                            <div class="bg-gray-50 rounded-xl shadow flex flex-col items-center justify-center text-grass-400 p-4">
                                <canvas ref="signaturePad" width="350" height="120" class="bg-white border border-grass rounded-xl shadow" style="touch-action: none;"></canvas>
                                <div class="flex gap-2 mt-2">
                                    <button type="button" @click="clearSignature" class="px-3 py-1 rounded bg-red-100 text-red-600 hover:bg-red-200 text-xs">Limpar</button>
                                    <button type="button" @click="saveSignature" class="px-3 py-1 rounded bg-grass text-white hover:bg-grass-dark text-xs">Salvar Assinatura</button>
                                </div>
                                <div v-if="signatureData" class="mt-2">
                                    <img :src="signatureData" alt="Assinatura" class="h-12 border rounded-xl" />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
.dark-multiselect .multiselect {
  background: #23263a;
  border-radius: 1rem;
  border: none;
  color: #fff;
  min-height: 3rem;
}
.dark-multiselect .multiselect__input,
.dark-multiselect .multiselect__single {
  background: #23263a;
  color: #fff;
}
.dark-multiselect .multiselect__option--highlight {
  background: #2d8a4b;
  color: #fff;
}
.dark-multiselect .multiselect__tag {
  background: #2d8a4b;
  color: #fff;
  border-radius: 0.75rem;
}
.dark-multiselect .multiselect__content-wrapper {
  background: #23263a;
  color: #fff;
  border-radius: 1rem;
  border: 1px solid #2d8a4b;
}
.dark-multiselect .multiselect__option {
  color: #fff;
}
</style> 