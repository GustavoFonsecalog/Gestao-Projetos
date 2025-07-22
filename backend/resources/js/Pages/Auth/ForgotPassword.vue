<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, useForm } from '@inertiajs/vue3';

defineProps({
    status: {
        type: String,
    },
});

const form = useForm({
    email: '',
});

const submit = () => {
    form.post(route('password.email'));
};
</script>

<template>
    <GuestLayout>
        <Head title="Recuperar Senha" />

        <div class="min-h-[70vh] flex items-center justify-center">
            <div class="bg-white/95 rounded-2xl shadow-2xl p-8 w-full max-w-md flex flex-col items-center">
                <h1 class="text-2xl font-extrabold text-grass mb-2">Recuperar Senha</h1>
                <p class="mb-6 text-gray-600 text-center text-sm">Esqueceu sua senha? Sem problemas! Informe seu e-mail e enviaremos um link para redefinição.</p>

                <div v-if="status" class="mb-4 text-sm font-medium text-green-600 bg-green-50 border border-green-200 rounded p-2 w-full text-center">
                    {{ status }}
                </div>

                <form @submit.prevent="submit" class="w-full">
                    <div class="mb-4">
                        <InputLabel for="email" value="E-mail" />
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12H8m8 0a4 4 0 11-8 0 4 4 0 018 0zm0 0v1a4 4 0 01-8 0v-1" /></svg>
                            </span>
                            <TextInput
                                id="email"
                                type="email"
                                class="mt-1 block w-full pl-10 pr-3 py-2 rounded border border-gray-300 focus:ring-2 focus:ring-grass focus:outline-none text-gray-900"
                                v-model="form.email"
                                required
                                autofocus
                                autocomplete="username"
                                placeholder="Digite seu e-mail"
                            />
                        </div>
                        <InputError class="mt-2" :message="form.errors.email" />
                    </div>

                    <button
                        type="submit"
                        class="w-full rounded-xl bg-grass hover:bg-grass-dark text-white font-bold py-3 text-lg shadow-lg transition-all duration-150 disabled:opacity-50"
                        :disabled="form.processing"
                    >
                        {{ form.processing ? 'Enviando...' : 'Enviar link de redefinição' }}
                    </button>
                </form>
            </div>
        </div>
    </GuestLayout>
</template>
