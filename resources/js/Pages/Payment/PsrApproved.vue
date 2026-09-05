<script setup lang="ts">
  import { Link as PsrLink } from '@inertiajs/vue3';
  import PsrBadge from '@Components/PsrBadge.vue';
  import '@lottiefiles/lottie-player';

  defineProps<{
    numbers?: string[];
  }>();
</script>

<template>
  <div class="min-h-screen bg-slate-950 flex flex-col items-center justify-center p-4 text-white">
    <div class="max-w-md w-full bg-slate-900 border border-amber-500/30 rounded-3xl p-6 sm:p-8 shadow-2xl text-center space-y-6">
      
      <!-- Success Icon -->
      <div class="w-20 h-20 mx-auto rounded-full bg-emerald-500/20 border-2 border-emerald-500 flex items-center justify-center text-4xl shadow-lg shadow-emerald-500/20">
        🎉
      </div>

      <div>
        <h1 class="text-2xl font-black text-emerald-400 tracking-wide">
          Pagamento Aprovado!
        </h1>
        <p class="text-sm text-gray-400 mt-1">
          Seu Pix foi confirmado com sucesso.
        </p>
      </div>

      <!-- Revelação dos Números -->
      <div class="p-5 rounded-2xl bg-slate-950 border border-slate-800 space-y-3">
        <p class="text-xs font-bold text-amber-400 uppercase tracking-widest flex items-center justify-center gap-1.5">
          <span>✨</span> Seus Números da Sorte <span>✨</span>
        </p>
        
        <div v-if="numbers && numbers.length" class="flex flex-wrap gap-2.5 justify-center pt-1">
          <span
            v-for="num in numbers"
            :key="`lucky-${num}`"
            class="px-4 py-2 bg-gradient-to-br from-amber-500 to-amber-400 text-slate-950 font-black text-xl rounded-xl shadow-lg transform hover:scale-105 transition-transform"
          >
            {{ num }}
          </span>
        </div>
        <p v-else class="text-xs text-gray-400">
          Seus números foram registrados na rifa e você pode consultá-los a qualquer momento em <b>"Meus Bilhetes"</b> com seu telefone!
        </p>

        <div class="pt-2 border-t border-slate-800/80 text-[11px] text-emerald-400 font-bold flex items-center justify-center gap-1.5">
          <span>📲</span> Enviamos a confirmação com seus números no seu WhatsApp!
        </div>
      </div>

      <p class="text-xs text-gray-300">
        🍀 Desejamos muita sorte! O sorteio será transmitido ao vivo no Instagram assim que 100% das cotas forem vendidas.
      </p>

      <div class="space-y-3 pt-2">
        <PsrLink
          class="inline-flex items-center justify-center gap-2 w-full py-4 px-6 bg-gradient-to-r from-amber-500 via-amber-400 to-amber-500 hover:from-amber-400 hover:to-amber-300 text-slate-950 font-black text-sm uppercase tracking-wider rounded-2xl shadow-xl hover:shadow-amber-500/20 active:scale-95 transition-all"
          title="Ir para a página inicial"
          :href="route('home')"
        >
          <span>Conferir Outras Rifas</span>
          <span class="text-base">→</span>
        </PsrLink>
      </div>

    </div>
  </div>
</template>
