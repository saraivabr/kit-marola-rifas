<script setup lang="ts">
  import { useLocaleCurrency } from '@Composables/Locale';
  import { computed } from 'vue';
  import PsrBadge from './PsrBadge.vue';

  const props = defineProps<{
    rifa: Pick<Rifa, 'id' | 'expired_at' | 'price' | 'thumbnail' | 'slug' | 'title' | 'sold_percentage' | 'progress_percentage'>;
  }>();

  const progress = computed(() => Number(props.rifa.sold_percentage ?? props.rifa.progress_percentage ?? 0));
  const price = computed(() => (props.rifa.price ? useLocaleCurrency(props.rifa.price) : ''));

  // Sexy Canvas - Inveja & Curiosidade (Visualizadores dinâmicos)
  const liveViewers = computed(() => {
    const base = ((props.rifa.id || 1) * 7) % 25 + 18;
    return base;
  });

  // Sexy Canvas - Avareza & Gula (Estimativa de valor de vitrine do kit)
  const estimatedMarketValue = computed(() => {
    const p = props.rifa.price || 2.99;
    if (p <= 3) return 'R$ 450,00';
    if (p <= 5) return 'R$ 800,00';
    return 'R$ 1.500,00';
  });

  // Sexy Canvas - Ira / Urgência
  const scarcityBadge = computed(() => {
    if (progress.value >= 65) {
      return { text: '🚨 ÚLTIMAS COTAS!', variant: 'danger' as const, extra: 'animate-pulse' };
    }
    if (progress.value >= 40) {
      return { text: '🔥 ALTA PROCURA', variant: 'warning' as const, extra: '' };
    }
    return { text: '👑 EDIÇÃO VIP', variant: 'accent' as const, extra: '' };
  });
</script>

<template>
  <div class="group bg-astryx-card border border-astryx-border-subtle hover:border-amber-500/60 rounded-astryx-card overflow-hidden shadow-astryx-med hover:shadow-astryx-high transition-all duration-300 flex flex-col h-full relative">
    
    <!-- Image & Badges Sexy Canvas -->
    <a :href="route('rifas.show', [rifa.slug])" class="relative block h-60 overflow-hidden bg-astryx-muted">
      <img
        :alt="rifa.title"
        class="object-cover object-center w-full h-full group-hover:scale-108 transition-transform duration-700 ease-out"
        :src="rifa.thumbnail"
      />

      <!-- Overlay de Luxúria & Atmosfera -->
      <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-transparent to-black/30"></div>

      <!-- Badge de Preço + Avareza (Custo vs Retorno) -->
      <div class="absolute top-3 left-3 flex flex-col gap-1 z-10">
        <span class="bg-gradient-to-r from-amber-500 via-yellow-400 to-amber-500 text-slate-950 text-xs font-black px-3 py-1 rounded-astryx-element shadow-lg flex items-center gap-1">
          <span>Apenas</span> {{ price }}
        </span>
        <span class="bg-black/75 backdrop-blur-sm text-emerald-400 text-[10px] font-bold px-2 py-0.5 rounded-md border border-emerald-500/30">
          Kit avaliado em {{ estimatedMarketValue }}
        </span>
      </div>

      <!-- Badge de Escassez / Ira (Medo de Ficar de Fora) -->
      <div class="absolute top-3 right-3 z-10 flex flex-col items-end gap-1.5">
        <PsrBadge :variant="scarcityBadge.variant" :class="scarcityBadge.extra">
          {{ scarcityBadge.text }}
        </PsrBadge>
      </div>

      <!-- Visualizadores ao vivo (Inveja & Efeito Manada) -->
      <div class="absolute bottom-3 left-3 z-10 flex items-center gap-1.5 bg-black/80 backdrop-blur-md px-3 py-1 rounded-full border border-white/10 text-[11px] text-white font-medium">
        <span class="w-2 h-2 rounded-full bg-emerald-400 animate-ping"></span>
        <span>👀 <strong>{{ liveViewers }}</strong> disputando cotas</span>
      </div>
      
      <!-- Opção de Prêmio (Liberdade da Criança Interior) -->
      <div class="absolute bottom-3 right-3 z-10 hidden sm:flex items-center text-[10px] font-bold bg-amber-500/90 text-slate-950 px-2 py-0.5 rounded-full shadow">
        🎁 Kit ou Pix na Conta
      </div>
    </a>

    <!-- Detalhes e Gatilhos -->
    <div class="p-5 flex flex-col flex-1 justify-between bg-astryx-card">
      <div>
        <h2 class="text-astryx-text-primary text-base font-bold line-clamp-2 group-hover:text-amber-500 transition-colors duration-200 leading-snug">
          <a :href="route('rifas.show', [rifa.slug])">{{ rifa.title }}</a>
        </h2>

        <!-- Barra de Progresso de Vendas (Gula & Ira) -->
        <div class="mt-4 bg-astryx-muted/80 p-3.5 rounded-astryx-container border border-astryx-border-subtle">
          <div class="flex justify-between items-center text-xs mb-1.5 font-semibold">
            <span class="text-astryx-text-secondary flex items-center gap-1 text-[11px]">
              <span class="text-amber-500 animate-pulse">🔥</span> Vendas no Pix
            </span>
            <span class="text-amber-600 dark:text-amber-400 font-black tracking-tight text-xs">{{ progress }}% preenchido</span>
          </div>
          <div class="w-full bg-astryx-surface rounded-full h-3 overflow-hidden border border-astryx-border-subtle p-[1px]">
            <div
              class="bg-gradient-to-r from-amber-500 via-amber-400 to-yellow-300 h-full rounded-full transition-all duration-700 ease-out shadow-sm"
              :style="{ width: `${Math.min(100, Math.max(0, progress))}%` }"
            ></div>
          </div>
          <div class="flex justify-between items-center text-[10px] text-astryx-text-tertiary mt-2">
            <span class="font-medium text-amber-600 dark:text-amber-400">⚡ Sorteio 100% no WhatsApp</span>
            <span class="text-emerald-600 dark:text-emerald-400 font-bold">Frete Grátis Brasil</span>
          </div>
        </div>
      </div>

      <!-- Footer do Card com CTA Magnético (Preguiça + Avareza) -->
      <div class="mt-5 pt-3.5 border-t border-astryx-border-subtle flex items-center justify-between gap-3">
        <div class="text-[11px] text-astryx-text-secondary flex flex-col">
          <span>Participação imediata</span>
          <strong class="text-emerald-600 dark:text-emerald-400 font-bold flex items-center gap-1">
            ❖ Pix Instantâneo
          </strong>
        </div>

        <a
          :href="route('rifas.show', [rifa.slug])"
          class="inline-flex items-center justify-center gap-2 text-xs font-black text-slate-950 bg-gradient-to-r from-amber-500 via-yellow-400 to-amber-500 hover:from-amber-400 hover:to-amber-300 px-4 py-2.5 rounded-astryx-element transition-all shadow-md hover:shadow-amber-500/20 active:scale-95 uppercase tracking-wider"
        >
          <span>QUERO GANHAR</span>
          <span class="text-sm font-black leading-none">👉</span>
        </a>
      </div>
    </div>
  </div>
</template>
