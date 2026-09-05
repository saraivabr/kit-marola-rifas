<script setup lang="ts">
  import { Link as PsrLink } from '@inertiajs/vue3';
  import PsrThemeToggle from '@Components/PsrThemeToggle.vue';

  defineProps<{
    numbers?: string[];
  }>();
</script>

<template>
  <div class="min-h-screen bg-astryx-body flex flex-col items-center justify-center p-4 text-astryx-text-primary transition-colors duration-200">
    <div class="w-full max-w-md flex justify-end mb-2">
      <PsrThemeToggle />
    </div>

    <div class="max-w-md w-full bg-astryx-card border border-astryx-border-subtle rounded-astryx-card p-6 sm:p-8 shadow-astryx-high text-center space-y-6">
      
      <!-- Success Icon -->
      <div class="w-20 h-20 mx-auto rounded-full bg-emerald-500/10 border-2 border-emerald-500 flex items-center justify-center text-4xl shadow-lg shadow-emerald-500/10">
        🎉
      </div>

      <div>
        <h1 class="text-2xl font-black text-emerald-600 dark:text-emerald-400 tracking-wide">
          Pagamento Aprovado!
        </h1>
        <p class="text-sm text-astryx-text-secondary mt-1">
          Seu Pix foi confirmado com sucesso pelo banco.
        </p>
      </div>

      <!-- Revelação dos Números -->
      <div class="p-5 rounded-astryx-container bg-astryx-surface border border-astryx-border-subtle space-y-3">
        <p class="text-xs font-bold text-amber-600 dark:text-amber-400 uppercase tracking-widest flex items-center justify-center gap-1.5">
          <span>✨</span> Seus Números da Sorte <span>✨</span>
        </p>
        
        <div v-if="numbers && numbers.length" class="flex flex-wrap gap-2.5 justify-center pt-1">
          <span
            v-for="num in numbers"
            :key="`lucky-${num}`"
            class="px-4 py-2 bg-gradient-to-br from-amber-500 to-amber-400 text-slate-950 font-black text-xl rounded-xl shadow-md transform hover:scale-105 transition-transform"
          >
            {{ num }}
          </span>
        </div>
        <p v-else class="text-xs text-astryx-text-secondary">
          Seus números foram registrados na rifa e você pode consultá-los a qualquer momento em <b>"Meus Bilhetes"</b> com seu telefone!
        </p>

        <div class="pt-2 border-t border-astryx-border-subtle text-[11px] text-emerald-600 dark:text-emerald-400 font-bold flex items-center justify-center gap-1.5">
          <span>📲</span> Enviamos a confirmação com seus números no seu WhatsApp!
        </div>
      </div>

      <p class="text-xs text-astryx-text-tertiary">
        🍀 Desejamos muita sorte! O sorteio será transmitido ao vivo no Instagram assim que 100% das cotas forem vendidas.
      </p>

      <div class="space-y-3 pt-2">
        <PsrLink
          class="inline-flex items-center justify-center gap-2 w-full py-4 px-6 bg-gradient-to-r from-amber-500 via-amber-400 to-amber-500 hover:from-amber-400 hover:to-amber-300 text-slate-950 font-black text-sm uppercase tracking-wider rounded-astryx-container shadow-astryx-med hover:shadow-amber-500/20 active:scale-95 transition-all"
          title="Ir para a página inicial"
          :href="route('home')"
        >
          <span>🔥</span> Ver Outras Rifas
        </PsrLink>
      </div>

    </div>
  </div>
</template>
