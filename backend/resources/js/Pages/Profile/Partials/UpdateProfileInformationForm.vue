<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Link, useForm, usePage } from '@inertiajs/vue3';

defineProps({
    mustVerifyEmail: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const user = usePage().props.auth.user;

const form = useForm({
    name: user.name,
    email: user.email,
});
</script>

<template>
    <form @submit.prevent="submit">
        <h2 class="text-lg font-bold mb-2">Informações do Perfil</h2>
        <p class="mb-4 text-gray-600">Atualize seu nome e e-mail.</p>
        <label class="block mb-2 font-semibold">Nome</label>
        <input v-model="form.name" class="w-full mb-4 rounded border border-gray-300 p-2" />
        <label class="block mb-2 font-semibold">E-mail</label>
        <input v-model="form.email" class="w-full mb-4 rounded border border-gray-300 p-2" disabled />
        <button type="submit" class="bg-grass text-white font-bold px-6 py-2 rounded hover:bg-grass-dark">Salvar</button>
    </form>
</template>
