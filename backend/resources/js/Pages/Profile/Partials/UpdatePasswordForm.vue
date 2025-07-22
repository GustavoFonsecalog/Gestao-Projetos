<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const passwordInput = ref(null);
const currentPasswordInput = ref(null);

const form = useForm({
    current_password: '',
    password: '',
    password_confirmation: '',
});

const updatePassword = () => {
    form.put(route('password.update'), {
        preserveScroll: true,
        onSuccess: () => form.reset(),
        onError: () => {
            if (form.errors.password) {
                form.reset('password', 'password_confirmation');
                passwordInput.value.focus();
            }
            if (form.errors.current_password) {
                form.reset('current_password');
                currentPasswordInput.value.focus();
            }
        },
    });
};
</script>

<template>
    <form @submit.prevent="updatePassword">
        <h2 class="text-lg font-bold mb-2">Atualizar Senha</h2>
        <p class="mb-4 text-gray-600">Use uma senha longa e segura para proteger sua conta.</p>
        <label class="block mb-2 font-semibold">Senha Atual</label>
        <input type="password" v-model="form.current_password" class="w-full mb-4 rounded border border-gray-300 p-2" />
        <label class="block mb-2 font-semibold">Nova Senha</label>
        <input type="password" v-model="form.password" class="w-full mb-4 rounded border border-gray-300 p-2" />
        <label class="block mb-2 font-semibold">Confirmar Nova Senha</label>
        <input type="password" v-model="form.password_confirmation" class="w-full mb-4 rounded border border-gray-300 p-2" />
        <button type="submit" class="bg-grass text-white font-bold px-6 py-2 rounded hover:bg-grass-dark">Salvar</button>
    </form>
</template>
