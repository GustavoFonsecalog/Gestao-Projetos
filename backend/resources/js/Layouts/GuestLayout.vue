<script setup>
import { ref, onMounted } from 'vue';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import { Link } from '@inertiajs/vue3';

const dark = ref(false);

onMounted(() => {
  if (localStorage.getItem('theme') === 'dark') {
    dark.value = true;
    document.documentElement.classList.add('dark');
  }
});

function toggleTheme() {
  dark.value = !dark.value;
  if (dark.value) {
    document.documentElement.classList.add('dark');
    localStorage.setItem('theme', 'dark');
  } else {
    document.documentElement.classList.remove('dark');
    localStorage.setItem('theme', 'light');
  }
}
</script>

<template>
  <div class="min-h-screen flex items-center justify-center bg-[#181c2f]">
    <div class="flex w-full max-w-5xl rounded-2xl shadow-2xl overflow-hidden bg-white">
      <div class="flex flex-col justify-center items-center w-full md:w-1/2 p-12 bg-[#23264a] text-white relative z-10">
        <div class="flex items-center justify-center mb-6">
          <img src="../../../public/assets/green-gear.png" alt="Engrenagem" class="h-16 w-16 mb-2" />
        </div>
        <span class="text-3xl font-bold text-grass mb-2">GeSenior</span>
        <slot />
      </div>
      <div class="hidden md:flex flex-col justify-between items-center w-1/2 bg-gradient-to-br from-grass to-[#23264a] relative p-8">
        <nav class="flex w-full justify-end gap-4 text-sm font-medium mb-8 text-white">
          <Link href="/sobre" class="hover:text-grass transition">Sobre</Link>
          <Link href="/funcionalidades" class="hover:text-grass transition">Funcionalidades</Link>
        </nav>
        <div class="relative z-10 text-center px-8 mt-16">
          <h1 class="text-5xl font-extrabold text-white mb-4 drop-shadow-lg">Bem-vindo.</h1>
          <p class="text-lg text-white/90">
            Acesse o sistema de gestão de projetos mais moderno e eficiente.<br/>
            GeSenior é produtividade e controle!
          </p>
        </div>
        <div class="h-8"></div>
      </div>
    </div>
  </div>
</template>

<style>
:root {
  --color-grass-light: #66bb6a;
}
.dark .text-grass {
  color: var(--color-grass-light) !important;
}
</style>