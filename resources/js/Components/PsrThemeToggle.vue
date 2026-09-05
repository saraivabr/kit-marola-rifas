<script setup lang="ts">
import { useTheme } from '../Composables/Theme';

const { currentTheme, toggleTheme } = useTheme();
</script>

<template>
  <button
    type="button"
    @click="toggleTheme"
    class="px-2.5 py-1.5 rounded-full border border-astryx-border-subtle bg-astryx-surface text-astryx-text-secondary hover:text-amber-500 hover:border-amber-500/50 shadow-sm transition-all duration-200 active:scale-95 flex items-center justify-center gap-1.5 text-xs font-semibold cursor-pointer"
    :title="currentTheme === 'dark' ? 'Mudar para tema claro' : 'Mudar para tema escuro'"
    aria-label="Alternar entre modo claro e escuro"
  >
    <template v-if="currentTheme === 'dark'">
      <span class="text-sm">☀️</span>
      <span class="text-astryx-text-primary text-[11px] font-bold">Claro</span>
    </template>
    <template v-else>
      <span class="text-sm">🌙</span>
      <span class="text-astryx-text-primary text-[11px] font-bold">Escuro</span>
    </template>
  </button>
</template>
