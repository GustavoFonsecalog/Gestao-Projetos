<script setup>
import { ref, onMounted } from 'vue';
import { usePage } from '@inertiajs/vue3';

const project = usePage().props.project;
const tasks = ref([]);
const loading = ref(false);

const statusColumns = [
  { key: 'a_fazer', label: 'A Fazer' },
  { key: 'em_andamento', label: 'Em Andamento' },
  { key: 'concluida', label: 'Concluída' },
  { key: 'pausada', label: 'Pausada' },
];

async function fetchTasks() {
  loading.value = true;
  const res = await fetch(`/projects/${project.id}/tasks`);
  if (res.ok) {
    tasks.value = await res.json();
  }
  loading.value = false;
}
onMounted(fetchTasks);

function tasksByStatus(status) {
  return tasks.value.filter(t => t.status === status);
}

const creatingTaskStatus = ref(null);
const newTask = ref({ title: '', prioridade: 'media', due_date: '', user_id: '', description: '' });
const creating = ref(false);
const editingTaskId = ref(null);
const editTask = ref({});
const deletingTaskId = ref(null);
const toast = ref('');
const draggingTaskId = ref(null);
const moving = ref(false);

function onDragStart(task) {
  draggingTaskId.value = task.id;
}
function onDragEnd() {
  draggingTaskId.value = null;
}
async function onDrop(status) {
  if (!draggingTaskId.value) return;
  const idx = tasks.value.findIndex(t => t.id === draggingTaskId.value);
  if (idx !== -1 && tasks.value[idx].status !== status) {
    tasks.value[idx].status = status;
    toast.value = 'Tarefa movida!';
    moving.value = true;
    fetch(`/tasks/${tasks.value[idx].id}`, {
      method: 'PUT',
      headers: { 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]')?.content },
      body: JSON.stringify({ status })
    }).then(() => fetchTasks()).finally(() => { moving.value = false; });
  }
  draggingTaskId.value = null;
}

async function createTask(status) {
  creating.value = true;
  await fetch(`/projects/${project.id}/tasks`, {
    method: 'POST',
    headers: { 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]')?.content },
    body: JSON.stringify({ ...newTask.value, status, prioridade: newTask.value.prioridade || 'media' })
  });
  newTask.value = { title: '', prioridade: 'media', due_date: '', user_id: '', description: '' };
  creatingTaskStatus.value = null;
  await fetchTasks();
  toast.value = 'Tarefa criada!';
  creating.value = false;
}
function startCreateTask(status) {
  creatingTaskStatus.value = status;
  newTask.value = { title: '', prioridade: 'media', due_date: '', user_id: '', description: '' };
}
function cancelCreateTask() {
  creatingTaskStatus.value = null;
}
function startEditTask(task) {
  editingTaskId.value = task.id;
  editTask.value = { ...task };
}
async function saveEditTask(task) {
  await fetch(`/tasks/${task.id}`, {
    method: 'PUT',
    headers: { 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]')?.content },
    body: JSON.stringify(editTask.value)
  });
  editingTaskId.value = null;
  await fetchTasks();
  toast.value = 'Tarefa atualizada!';
}
function cancelEditTask() {
  editingTaskId.value = null;
}
async function deleteTask(id) {
  const idx = tasks.value.findIndex(t => t.id === id);
  if (idx !== -1) {
    tasks.value.splice(idx, 1);
    toast.value = 'Tarefa excluída!';
    fetch(`/tasks/${id}`, { method: 'DELETE', headers: { 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]')?.content } })
      .then(() => fetchTasks());
  }
}
const newSubtask = ref({});
const creatingSubtaskTaskId = ref(null);
async function addSubtask(taskId) {
  if (!newSubtask.value[taskId] || !newSubtask.value[taskId].trim()) return;
  await fetch(`/tasks/${taskId}/subtasks`, {
    method: 'POST',
    headers: { 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]')?.content },
    body: JSON.stringify({ title: newSubtask.value[taskId], status: 'a_fazer' })
  });
  newSubtask.value[taskId] = '';
  await fetchTasks();
  toast.value = 'Subtarefa criada!';
}
async function toggleSubtask(sub) {
  await fetch(`/subtasks/${sub.id}`, {
    method: 'PUT',
    headers: { 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]')?.content },
    body: JSON.stringify({ is_checked: !sub.is_checked })
  });
  await fetchTasks();
}
async function deleteSubtask(id) {
  for (const t of tasks.value) {
    const subIdx = t.subtasks.findIndex(s => s.id === id);
    if (subIdx !== -1) {
      t.subtasks.splice(subIdx, 1);
      toast.value = 'Subtarefa excluída!';
      fetch(`/subtasks/${id}`, { method: 'DELETE', headers: { 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]')?.content } })
        .then(() => fetchTasks());
      break;
    }
  }
}
</script>
<template>
  <div class="py-8">
    <h2 class="text-2xl font-extrabold text-grass mb-8">Tarefas do Projeto</h2>
    <div v-if="toast" class="fixed top-4 right-4 bg-grass text-white px-4 py-2 rounded shadow-lg z-50">{{ toast }}</div>
    <div v-if="moving" class="fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center z-50">
      <div class="bg-white rounded-lg p-8 flex flex-col items-center shadow-lg">
        <svg class="animate-spin h-8 w-8 text-grass mb-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path></svg>
        <span class="text-grass font-bold">Movendo tarefa...</span>
      </div>
    </div>
    <div v-if="loading" class="text-gray-400">Carregando tarefas...</div>
    <div v-else class="grid grid-cols-1 md:grid-cols-4 gap-6">
      <div v-for="col in statusColumns" :key="col.key" class="bg-white/95 rounded-2xl shadow-xl p-4 min-h-[400px] flex flex-col"
        @dragover.prevent
        @drop="onDrop(col.key)">
        <div class="flex items-center justify-between mb-4">
          <h3 class="text-lg font-bold text-grass">{{ col.label }}</h3>
          <button @click="startCreateTask(col.key)" class="text-grass font-bold text-xl hover:text-grass-dark">+</button>
        </div>
        <div v-if="creatingTaskStatus === col.key" class="mb-4 bg-gray-100 rounded p-3 flex flex-col gap-2">
          <input v-model="newTask.title" class="rounded border p-2" placeholder="Título da tarefa" />
          <input v-model="newTask.due_date" type="date" class="rounded border p-2" />
          <select v-model="newTask.prioridade" class="rounded border p-2">
            <option value="baixa">Baixa</option>
            <option value="media">Média</option>
            <option value="alta">Alta</option>
            <option value="urgente">Urgente</option>
          </select>
          <textarea v-model="newTask.description" class="rounded border p-2" placeholder="Descrição"></textarea>
          <div class="flex gap-2 mt-2">
            <button @click="createTask(col.key)" :disabled="creating || !newTask.title.trim()" class="bg-grass text-white font-bold px-4 py-1 rounded">Salvar</button>
            <button @click="cancelCreateTask" class="text-gray-500 font-bold px-4 py-1 rounded">Cancelar</button>
          </div>
        </div>
        <div class="flex-1 space-y-4">
          <div v-for="task in tasksByStatus(col.key)" :key="task.id" class="bg-gray-50 rounded-lg p-4 shadow flex flex-col gap-2"
            draggable="true"
            @dragstart="onDragStart(task)"
            @dragend="onDragEnd">
            <div v-if="editingTaskId === task.id" class="flex flex-col gap-2 mb-2">
              <input v-model="editTask.title" class="rounded border p-2" />
              <input v-model="editTask.due_date" type="date" class="rounded border p-2" />
              <select v-model="editTask.prioridade" class="rounded border p-2">
                <option value="baixa">Baixa</option>
                <option value="media">Média</option>
                <option value="alta">Alta</option>
                <option value="urgente">Urgente</option>
              </select>
              <textarea v-model="editTask.description" class="rounded border p-2" />
              <div class="flex gap-2 mt-2">
                <button @click="saveEditTask(task)" class="bg-grass text-white font-bold px-4 py-1 rounded">Salvar</button>
                <button @click="cancelEditTask" class="text-gray-500 font-bold px-4 py-1 rounded">Cancelar</button>
              </div>
            </div>
            <div v-else>
              <div class="flex items-center justify-between">
                <div class="font-bold text-gray-800">{{ task.title }}</div>
                <div class="flex gap-2">
                  <button @click="startEditTask(task)" class="text-xs text-grass font-bold">Editar</button>
                  <button @click="deleteTask(task.id)" class="text-xs text-red-600 font-bold">Excluir</button>
                </div>
              </div>
              <div class="text-xs text-gray-500">Responsável: {{ task.user?.name || '---' }}</div>
              <div class="text-xs text-gray-500">Prazo: {{ task.due_date ? (new Date(task.due_date)).toLocaleDateString('pt-BR') : '-' }}</div>
              <div class="text-xs text-gray-500">Prioridade: {{ task.prioridade }}</div>
              <div class="mt-2">
                <span class="text-xs font-semibold text-grass">Subtarefas:</span>
                <ul class="ml-2 mt-1 space-y-1">
                  <li v-for="sub in task.subtasks" :key="sub.id" class="flex items-center gap-2">
                    <input type="checkbox" :checked="sub.is_checked" @change="toggleSubtask(sub)" />
                    <span :class="sub.is_checked ? 'line-through text-gray-400' : ''">{{ sub.title }}</span>
                    <button @click="deleteSubtask(sub.id)" class="text-xs text-red-600 font-bold ml-2">Excluir</button>
                  </li>
                  <li class="flex items-center gap-2 mt-2">
                    <input v-model="newSubtask[task.id]" class="rounded border p-1 text-xs" placeholder="Nova subtarefa" @keyup.enter="addSubtask(task.id)" />
                    <button @click="addSubtask(task.id)" class="text-xs text-grass font-bold">+</button>
                  </li>
                </ul>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
<style scoped>
.bg-white\/95 {
  background: rgba(255,255,255,0.95);
}
</style> 