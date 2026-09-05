<script setup lang="ts">
  import { useLocaleCurrency } from '@Composables/Locale';
  import { computed } from 'vue';
  import PsrBadge from './PsrBadge.vue';

  const props = defineProps<{
    rifa: Pick<Rifa, 'id' | 'expired_at' | 'price' | 'thumbnail' | 'slug' | 'title' | 'sold_percentage' | 'progress_percentage'>;
  }>();

  const progress = computed(() => Number(props.rifa.sold_percentage ?? props.rifa.progress_percentage ?? 0));
  const price = computed(() => (props.rifa.price ? useLocaleCurrency(props.rifa.price) : ''));

  // FOMO: Visualizadores ativos dinâmicos baseados no ID da rifa
  const liveViewers = computed(() => {
    const base = ((props.rifa.id || 1) * 7) % 25 + 18;
    return base;
  });

  const scarcityBadge = computed(() => {
    if (progress.value >= 65) {
      return { text: '🚨 ÚLTIMAS COTAS!', variant: 'danger' as const, extra: 'animate-pulse' };
    }
    if (progress.value >= 40) {
      return { text: '🔥 ALTA PROCURA', variant: 'warning' as const, extra: '' };
    }
    return { text: '⭐ DESTAQUE', variant: 'accent' as const, extra: '' };
  });
</script>

<template>
  <div class="group bg-astryx-card border border-astryx-border-subtle hover:border-amber-500/50 rounded-astryx-card overflow-hidden shadow-astryx-low hover:shadow-astryx-med transition-all duration-[var(--astryx-duration-fast)] ease-[var(--astryx-ease-standard)] flex flex-col h-full relative">
    <!-- Image & Badges -->
    <a :href="route('rifas.show', [rifa.slug])" class="relative block h-56 overflow-hidden bg-astryx-muted">
      <img
        :alt="rifa.title"
        class="object-cover object-center w-full h-full group-hover:scale-105 transition-transform duration-500 ease-[var(--astryx-ease-standard)]"
        :src="rifa.thumbnail"
      />

      <!-- Overlay Gradiente na base da imagem -->
      <div class="absolute inset-0 bg-gradient-to-t from-[var(--astryx-color-background-card)] via-transparent to-black/30"></div>

      <!-- Badge de Preço -->
      <div class="absolute top-3 left-3 flex flex-col gap-1.5 z-10">
        <span class="bg-gradient-to-r from-amber-500 to-amber-400 text-slate-950 text-xs font-black px-3 py-1 rounded-astryx-element shadow-astryx-low flex items-center gap-1">
          <span>Apenas</span> {{ price }}
        </span>
      </div>

      <!-- Badge de Escassez / FOMO -->
      <div class="absolute top-3 right-3 z-10 flex flex-col items-end gap-1.5">
        <PsrBadge :variant="scarcityBadge.variant" :class="scarcityBadge.extra">
          {{ scarcityBadge.text }}
        </PsrBadge>
      </div>

      <!-- Visualizadores ao vivo sobre a imagem -->
      <div class="absolute bottom-3 left-3 z-10 flex items-center gap-1.5 bg-astryx-body/85 backdrop-blur-md px-2.5 py-1 rounded-full border border-astryx-border-subtle text-[11px] text-astryx-text-secondary font-medium">
        <span class="w-2 h-2 rounded-full bg-emerald-400 animate-ping"></span>
        <span>👀 <strong>{{ liveViewers }}</strong> pessoas vendo</span>
      </div>
    </a>

    <!-- Details -->
    <div class="p-5 flex flex-col flex-1 justify-between bg-astryx-card">
      <div>
        <h2 class="text-astryx-text-primary text-base font-bold line-clamp-2 group-hover:text-amber-400 transition-colors duration-[var(--astryx-duration-fast)] leading-snug">
          <a :href="route('rifas.show', [rifa.slug])">{{ rifa.title }}</a>
        </h2>

        <!-- Barra de Progresso de Vendas com FOMO -->
        <div class="mt-4 bg-astryx-muted/80 p-3 rounded-astryx-container border border-astryx-border-subtle">
          <div class="flex justify-between items-center text-xs mb-1.5 font-medium">
            <span class="text-astryx-text-secondary flex items-center gap-1 text-[11px]">
              <span class="text-amber-400 animate-pulse">🔥</span> Vendas no Pix
            </span>
            <span class="text-amber-400 font-black tracking-tight text-xs">{{ progress }}% vendido</span>
          </div>
          <div class="w-full bg-slate-950 rounded-full h-2.5 overflow-hidden border border-astryx-border-subtle p-[1px]">
            <div
              class="bg-gradient-to-r from-amber-500 via-amber-400 to-yellow-300 h-full rounded-full transition-all duration-700 ease-out shadow-sm"
              :style="{ width: `${Math.min(100, Math.max(0, progress))}%` }"
            ></div>
          </div>
          <div class="flex justify-between items-center text-[10px] text-astryx-text-tertiary mt-1.5">
            <span>Sorteio ao atingir 100%</span>
            <span class="text-emerald-400 font-semibold">Envio no WhatsApp</span>
          </div>
        </div>
      </div>

      <!-- Footer do Card com CTA -->
      <div class="mt-5 pt-3.5 border-t border-astryx-border-subtle flex items-center justify-between gap-3">
        <div class="text-[11px] text-astryx-text-secondary flex flex-col">
          <span>Pagamento via</span>
          <strong class="text-emerald-400 font-bold flex items-center gap-1">
            ❖ PIX Automático
          </strong>
        </div>

        <a
          :href="route('rifas.show', [rifa.slug])"
          class="inline-flex items-center justify-center gap-1.5 text-xs font-black text-slate-950 bg-gradient-to-r from-amber-500 via-amber-400 to-amber-500 hover:from-amber-400 hover:to-amber-300 px-4 py-2 rounded-astryx-element transition-all duration-[var(--astryx-duration-fast)] ease-[var(--astryx-ease-standard)] shadow-astryx-low hover:shadow-astryx-med active:scale-[0.97]"
        >
          Participar
          <span class="text-sm leading-none font-bold">→</span>
        </a>
      </div>
    </div>
  </div>
</template>
