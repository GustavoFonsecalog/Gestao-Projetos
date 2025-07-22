<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post(route('register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Cadastro" />
        <form @submit.prevent="submit" class="space-y-6 w-full max-w-xs mx-auto">
            <div>
                <InputLabel for="name" value="Nome" class="text-gray-900 dark:text-white font-semibold" />
                <TextInput
                    id="name"
                    type="text"
                    class="mt-1 block w-full border-0 border-b-2 border-blue-400 focus:border-blue-600 bg-transparent text-gray-900 dark:text-white placeholder-gray-400 focus:ring-0"
                    v-model="form.name"
                    required
                    autofocus
                    autocomplete="name"
                    placeholder="Digite seu nome"
                />
                <InputError class="mt-2" :message="form.errors.name" />
            </div>
            <div>
                <InputLabel for="email" value="E-mail" class="text-gray-900 dark:text-white font-semibold" />
                <TextInput
                    id="email"
                    type="email"
                    class="mt-1 block w-full border-0 border-b-2 border-blue-400 focus:border-blue-600 bg-transparent text-gray-900 dark:text-white placeholder-gray-400 focus:ring-0"
                    v-model="form.email"
                    required
                    autocomplete="username"
                    placeholder="Digite seu e-mail"
                />
                <InputError class="mt-2" :message="form.errors.email" />
            </div>
            <div>
                <InputLabel for="password" value="Senha" class="text-gray-900 dark:text-white font-semibold" />
                <TextInput
                    id="password"
                    type="password"
                    class="mt-1 block w-full border-0 border-b-2 border-blue-400 focus:border-blue-600 bg-transparent text-gray-900 dark:text-white placeholder-gray-400 focus:ring-0"
                    v-model="form.password"
                    required
                    autocomplete="new-password"
                    placeholder="Digite sua senha"
                />
                <InputError class="mt-2" :message="form.errors.password" />
            </div>
            <div>
                <InputLabel for="password_confirmation" value="Confirmar Senha" class="text-gray-900 dark:text-white font-semibold" />
                <TextInput
                    id="password_confirmation"
                    type="password"
                    class="mt-1 block w-full border-0 border-b-2 border-blue-400 focus:border-blue-600 bg-transparent text-gray-900 dark:text-white placeholder-gray-400 focus:ring-0"
                    v-model="form.password_confirmation"
                    required
                    autocomplete="new-password"
                    placeholder="Confirme sua senha"
                />
                <InputError class="mt-2" :message="form.errors.password_confirmation" />
            </div>
            <div class="flex gap-4 justify-center mt-4">
                <PrimaryButton
                    class="h-12 px-8 rounded-full flex items-center justify-center bg-grass hover:bg-grass-dark border-none text-white font-bold text-base shadow transition"
                    :class="{ 'opacity-25': form.processing }"
                    :disabled="form.processing"
                >
                    Cadastrar
                </PrimaryButton>
            </div>
            <div class="text-center mt-4">
                <span class="text-sm text-gray-400">Já possui uma conta?</span>
                <Link :href="route('login')" class="ml-1 text-grass font-bold hover:underline">Entrar</Link>
            </div>
        </form>
    </GuestLayout>
</template>
