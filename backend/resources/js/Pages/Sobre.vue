<template>
    <div class="min-h-screen flex flex-col items-center justify-center bg-gradient-to-br from-grass to-blue-700 p-8">
        <div class="bg-white bg-opacity-90 rounded-2xl shadow-xl max-w-2xl w-full p-10">
            <h1 class="text-3xl font-bold text-grass mb-4">Sobre o GeSenior</h1>
            <p class="text-lg text-gray-700 mb-6">
                O <b>GeSenior</b> é um sistema de gestão de projetos desenvolvido para tornar o acompanhamento de tarefas, equipes e resultados mais simples, moderno e eficiente.
            </p>
            <h2 class="text-xl font-semibold text-grass mb-2">Propósito</h2>
            <p class="mb-4 text-gray-700">
                Facilitar a vida de gestores, equipes e clientes, centralizando informações, otimizando processos e promovendo colaboração.
            </p>
            <h2 class="text-xl font-semibold text-grass mb-2">Para quem foi criado?</h2>
            <p class="mb-4 text-gray-700">
                O sistema é ideal para pequenas e médias empresas, consultorias, agências, times de TI, startups e qualquer organização que precise de controle e transparência em seus projetos.
            </p>
            <h2 class="text-xl font-semibold text-grass mb-2">Visão</h2>
            <p class="mb-4 text-gray-700">
                Tornar a gestão de projetos acessível, intuitiva e poderosa, com foco em usabilidade e resultados reais.
            </p>
            <Link href="/login" class="inline-block mt-6 text-grass font-bold hover:underline">Voltar para o início</Link>
        </div>
    </div>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';
</script> 