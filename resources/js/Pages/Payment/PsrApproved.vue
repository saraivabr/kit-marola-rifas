<script setup lang="ts">
  import { ref, onMounted } from 'vue';
  import { Link as PsrLink } from '@inertiajs/vue3';
  import PsrThemeToggle from '@Components/PsrThemeToggle.vue';

  const props = defineProps<{
    numbers?: string[];
  }>();

  const copiedShare = ref(false);

  // Vibração háptica de dopamina ao carregar
  onMounted(() => {
    if (typeof navigator !== 'undefined' && 'vibrate' in navigator) {
      try { navigator.vibrate([100, 50, 100, 50, 200]); } catch (e) {}
    }
  });

  function shareNumbers() {
    const text = `🎟️ Acabei de garantir minhas cotas da sorte no Kit Marola Rifas! Meus números: ${props.numbers?.join(', ') || 'Registrados'}. Boa sorte pra mim! 🍀 kitmarola.saraiva.ai`;
    if (typeof navigator !== 'undefined' && navigator.share) {
      navigator.share({
        title: 'Kit Marola Rifas',
        text: text,
        url: 'https://kitmarola.saraiva.ai',
      }).catch(() => {});
    } else if (typeof navigator !== 'undefined' && navigator.clipboard) {
      navigator.clipboard.writeText(text);
      copiedShare.value = true;
      setTimeout(() => copiedShare.value = false, 2500);
    }
  }
</script>

<template>
  <div class="min-h-screen bg-astryx-body flex flex-col items-center justify-center p-4 text-astryx-text-primary transition-colors duration-200">
    <div class="w-full max-w-md flex justify-between items-center mb-3">
      <PsrLink :href="route('home')" class="text-xs font-bold text-amber-600 dark:text-amber-400 hover:underline">
        ← Voltar ao Início
      </PsrLink>
      <PsrThemeToggle />
    </div>

    <div class="max-w-md w-full bg-astryx-card border-2 border-emerald-500/30 rounded-astryx-card p-6 sm:p-8 shadow-astryx-high text-center space-y-6 relative overflow-hidden">
      <div class="absolute -right-16 -top-16 w-36 h-36 bg-emerald-500/10 rounded-full blur-2xl pointer-events-none"></div>

      <!-- Ícone Animado de Celebração (Dopamina & Criança Interior) -->
      <div class="w-24 h-24 mx-auto rounded-full bg-gradient-to-tr from-emerald-500/20 to-teal-400/20 border-2 border-emerald-500 flex items-center justify-center text-5xl shadow-xl shadow-emerald-500/20 animate-bounce">
        🎉
      </div>

      <div>
        <span class="inline-block text-[11px] font-black uppercase tracking-widest text-emerald-600 dark:text-emerald-400 bg-emerald-500/15 px-3 py-1 rounded-full mb-2">
          ❖ Pagamento Aprovado Instantaneamente
        </span>
        <h1 class="text-2xl sm:text-3xl font-black text-astryx-text-primary tracking-tight font-[Raleway]">
          Você está Concorrendo!
        </h1>
        <p class="text-xs sm:text-sm text-astryx-text-secondary mt-1">
          Suas cotas foram carimbadas no sistema oficial e enviadas para o seu WhatsApp.
        </p>
      </div>

      <!-- Revelação dos Números (Curiosidade & Recompensa) -->
      <div class="p-5 rounded-astryx-container bg-astryx-surface border border-astryx-border-subtle space-y-3 shadow-inner">
        <p class="text-xs font-black text-amber-600 dark:text-amber-400 uppercase tracking-widest flex items-center justify-center gap-1.5">
          <span>✨</span> Seus Números da Sorte <span>✨</span>
        </p>
        
        <div v-if="numbers && numbers.length" class="flex flex-wrap gap-2 justify-center pt-1 max-h-48 overflow-y-auto p-1">
          <span
            v-for="num in numbers"
            :key="`lucky-${num}`"
            class="px-3.5 py-2 bg-gradient-to-br from-amber-500 via-yellow-400 to-amber-500 text-slate-950 font-black text-lg rounded-xl shadow-md transform hover:scale-110 transition-transform cursor-default"
          >
            {{ num }}
          </span>
        </div>
        <p v-else class="text-xs text-astryx-text-secondary">
          Seus números foram registrados na rifa e você pode consultá-los a qualquer momento em <b>"Meus Bilhetes"</b> com seu telefone!
        </p>

        <div class="pt-2 border-t border-astryx-border-subtle text-[11px] text-emerald-600 dark:text-emerald-400 font-bold flex items-center justify-center gap-1.5">
          <span>📲</span> Enviamos o comprovante completo no seu WhatsApp!
        </div>
      </div>

      <!-- Sexy Canvas: Vaidade (Compartilhar com Amigos / Story) -->
      <div class="space-y-3">
        <button
          type="button"
          @click="shareNumbers"
          class="w-full py-3 px-4 bg-gradient-to-r from-emerald-600 via-teal-500 to-emerald-600 hover:from-emerald-500 hover:to-teal-400 text-white font-black text-xs uppercase tracking-wider rounded-xl shadow-md transition-all active:scale-95 flex items-center justify-center gap-2 cursor-pointer"
        >
          <span>📸</span>
          <span>{{ copiedShare ? 'Copiado para o seu Story!' : 'Compartilhar nos Stories' }}</span>
        </button>

        <!-- Pertencimento (Comunidade VIP da Tabacaria) -->
        <a
          href="https://chat.whatsapp.com/invite"
          target="_blank"
          rel="noopener"
          class="w-full py-3 px-4 bg-astryx-surface hover:bg-astryx-card-hover border border-astryx-border text-astryx-text-primary font-bold text-xs uppercase tracking-wider rounded-xl transition-all flex items-center justify-center gap-2"
        >
          <span>💬</span>
          <span>Entrar no Grupo VIP do WhatsApp</span>
        </a>

        <PsrLink
          class="inline-flex items-center justify-center gap-2 w-full py-3.5 px-6 bg-gradient-to-r from-amber-500 via-yellow-400 to-amber-500 hover:from-amber-400 hover:to-yellow-300 text-slate-950 font-black text-xs uppercase tracking-wider rounded-xl shadow-lg active:scale-95 transition-all"
          title="Ir para a página inicial"
          :href="route('home')"
        >
          <span>🔥</span> Ver Mais Rifas Ativas
        </PsrLink>
      </div>

      <p class="text-[11px] text-astryx-text-tertiary">
        🍀 O sorteio será transmitido ao vivo no Instagram oficial com base no resultado da Loteria Federal assim que as cotas esgotarem.
      </p>

    </div>
  </div>
</template>
