<script setup>
import Checkbox from '@/Components/Checkbox.vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

defineProps({
    canResetPassword: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Login" />
        <div v-if="status" class="mb-4 text-sm font-medium text-green-600">
            {{ status }}
        </div>
        <form @submit.prevent="submit" class="space-y-6 w-full max-w-xs mx-auto">
            <div>
                <InputLabel for="email" value="E-mail" class="text-gray-900 dark:text-white font-semibold" />
                <TextInput
                    id="email"
                    type="email"
                    class="mt-1 block w-full border-0 border-b-2 border-blue-400 focus:border-blue-600 bg-transparent text-gray-900 dark:text-white placeholder-gray-400 focus:ring-0"
                    v-model="form.email"
                    required
                    autofocus
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
                    autocomplete="current-password"
                    placeholder="Digite sua senha"
                />
                <InputError class="mt-2" :message="form.errors.password" />
            </div>
            <div class="flex items-center gap-2">
                <Checkbox name="remember" v-model:checked="form.remember" />
                <span class="text-xs text-gray-500">Lembrar-me</span>
                <Link :href="route('password.request')" class="ml-auto text-xs text-grass hover:underline">Esqueceu a senha?</Link>
            </div>
            <div class="flex gap-4 justify-center mt-4">
                <PrimaryButton
                    class="h-12 px-8 rounded-full flex items-center justify-center bg-grass hover:bg-grass-dark border-none text-white font-bold text-base shadow transition"
                    :class="{ 'opacity-25': form.processing }"
                    :disabled="form.processing"
                >
                    Entrar
                </PrimaryButton>
                <Link
                    :href="route('register')"
                    class="h-12 px-8 rounded-full flex items-center justify-center bg-white text-grass font-bold text-base shadow border border-grass hover:bg-grass hover:text-white transition"
                >
                    Cadastre-se
                </Link>
            </div>
        </form>
    </GuestLayout>
</template>
