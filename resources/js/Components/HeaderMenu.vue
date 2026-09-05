<script setup lang="ts">
  import PsrLogo from './PsrLogo.vue';
  import PsrThemeToggle from './PsrThemeToggle.vue';

  defineEmits<{
    (event: 'update:active', value: boolean): void;
  }>();

  defineProps<{
    active: boolean;
  }>();
</script>

<template>
  <div class="wrapper" :class="{ '-translate-x-full': !active }">
    <nav class="flex flex-col order-3 w-full md:w-max md:flex-row md:items-center md:gap-2">
      <a :href="route('home')" class="menu-item">🔥 Rifas Ativas</a>
      <a :href="route('rifas.finalizadas')" class="menu-item">🏆 Ganhadores</a>
      <a :href="route('testimonials.list')" class="menu-item">⭐ Depoimentos</a>
      <a :href="route('contact')" class="menu-item">💬 Suporte / Contato</a>

      <div class="p-3 md:p-0 flex items-center gap-2">
        <PsrThemeToggle />
      </div>

      <button
        type="button"
        class="p-4 border-b border-astryx-border-subtle text-start w-full md:p-0 md:px-4 md:border-0 md:w-auto md:hidden md:invisible font-semibold text-amber-500"
        @click="$emit('update:active', false)"
      >
        ✕ Fechar Menu
      </button>
    </nav>

    <div
      class="aspect-video order-1 bg-astryx-surface flex flex-wrap drop-shadow place-content-center w-full md:hidden md:invisible p-4 border-b border-astryx-border-subtle"
      role="button"
      aria-label="Voltar para a página anterior"
      @click="$emit('update:active', false)"
    >
      <PsrLogo />
    </div>

    <p class="text-astryx-text-tertiary text-xs my-4 order-2 md:hidden md:invisible">Menu de Navegação</p>
  </div>
</template>

<style scoped>
  .wrapper {
    @apply flex
  items-center
  bg-astryx-surface
  z-40
  fixed
  h-screen
  inset-0
  p-6
  transition-transform
  duration-300
  ease-in-out
  flex-col
  md:p-0
  md:flex-row
  md:h-auto
  md:static
  md:gap-3
  md:translate-x-0;
  }
  .menu-item {
    @apply p-3 text-sm font-bold text-astryx-text-primary hover:text-amber-500 transition-colors border-b border-astryx-border-subtle w-full md:p-2 md:px-3 md:border-0 md:w-auto rounded-xl hover:bg-amber-500/10;
  }
</style>
