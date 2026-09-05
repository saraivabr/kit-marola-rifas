<script setup lang="ts">
  import { computed, ref, watch } from 'vue';
  import { useCounter } from '@vueuse/core';
  import { useLocaleCurrency } from '../Composables/Locale';

  import PsrCard from './PsrCard.vue';
  import PsrButton from './PsrButton.vue';

  const emits = defineEmits<{
    (event: 'reserveNumbers', quantity: number): void;
    (event: 'update:quantity', value: number): void;
  }>();

  const props = defineProps<{
    price: number;
    buyMax: number;
    buyMin: number;
  }>();

  const { count, inc, dec, set } = useCounter(props.buyMin, { max: props.buyMax, min: props.buyMin });
  const countModel = ref<number>(props.buyMin);

  const priceTotal = computed(() => useLocaleCurrency(props.price * count.value));
  watch(countModel, (newValue) => {
    set(newValue);
    emits('update:quantity', count.value);
  });
</script>

<template>
  <div class="bg-astryx-card border border-astryx-border-subtle rounded-astryx-card p-5 sm:p-7 shadow-astryx-med space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-astryx-border-subtle pb-4">
      <div>
        <span class="text-[11px] font-black text-amber-400 uppercase tracking-widest bg-amber-500/10 px-2.5 py-0.5 rounded-full border border-amber-500/20">
          Passo 1 de 2
        </span>
        <h3 class="text-lg sm:text-xl font-black text-astryx-text-primary mt-1">Selecione a quantidade de cotas</h3>
      </div>
      <div class="flex items-center gap-1.5 text-xs text-emerald-400 font-bold bg-emerald-500/10 border border-emerald-500/20 px-3 py-1.5 rounded-astryx-element self-start sm:self-auto">
        <span class="w-2 h-2 rounded-full bg-emerald-400 animate-ping"></span>
        <span>🔥 Alta chance de contemplação</span>
      </div>
    </div>

    <!-- Botões de Seleção Rápida de Cotas -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-2.5 w-full mx-auto">
      <button
        class="button-qntd group hover:border-amber-500/60 transition-all duration-[var(--astryx-duration-fast)] ease-[var(--astryx-ease-standard)] active:scale-95"
        data-testid="quantity-1"
        @click="inc(1)"
      >
        <span class="text-xs text-astryx-text-tertiary block font-normal">Inicial</span>
        <span class="text-base font-black text-astryx-text-primary group-hover:text-amber-400">+1 Cota</span>
      </button>

      <button
        class="button-qntd relative border-amber-500/50 bg-amber-500/5 hover:bg-amber-500/10 transition-all duration-[var(--astryx-duration-fast)] ease-[var(--astryx-ease-standard)] active:scale-95"
        data-testid="quantity-10"
        @click="inc(10)"
      >
        <span class="absolute -top-2.5 right-2 bg-gradient-to-r from-amber-500 to-amber-400 text-slate-950 text-[9px] font-black px-2 py-0.5 rounded-full shadow-astryx-low uppercase">
          🔥 Mais Escolhido
        </span>
        <span class="text-xs text-amber-400/80 block font-normal">Combo</span>
        <span class="text-base font-black text-amber-400">+10 Cotas</span>
      </button>

      <button
        class="button-qntd relative border-emerald-500/40 bg-emerald-500/5 hover:bg-emerald-500/10 transition-all duration-[var(--astryx-duration-fast)] ease-[var(--astryx-ease-standard)] active:scale-95"
        data-testid="quantity-50"
        @click="inc(50)"
      >
        <span class="absolute -top-2.5 right-2 bg-gradient-to-r from-emerald-500 to-teal-400 text-slate-950 text-[9px] font-black px-2 py-0.5 rounded-full shadow-astryx-low uppercase">
          ⚡ 50x Chances
        </span>
        <span class="text-xs text-emerald-400/80 block font-normal">Super Combo</span>
        <span class="text-base font-black text-emerald-400">+50 Cotas</span>
      </button>

      <button
        class="button-qntd relative border-purple-500/40 bg-purple-500/5 hover:bg-purple-500/10 transition-all duration-[var(--astryx-duration-fast)] ease-[var(--astryx-ease-standard)] active:scale-95"
        data-testid="quantity-100"
        @click="inc(100)"
      >
        <span class="absolute -top-2.5 right-2 bg-gradient-to-r from-purple-500 to-indigo-400 text-white text-[9px] font-black px-2 py-0.5 rounded-full shadow-astryx-low uppercase">
          👑 VIP Master
        </span>
        <span class="text-xs text-purple-400/80 block font-normal">Lote Máximo</span>
        <span class="text-base font-black text-purple-400">+100 Cotas</span>
      </button>
    </div>

    <!-- Controle Manual de Cotas -->
    <div class="bg-astryx-muted/80 p-4 rounded-astryx-container border border-astryx-border-subtle flex flex-col sm:flex-row items-center justify-between gap-4">
      <div class="flex items-center gap-2 w-full sm:w-auto justify-center">
        <button
          class="w-12 h-12 rounded-astryx-element bg-astryx-surface hover:bg-astryx-card-hover text-astryx-text-primary font-black text-xl flex items-center justify-center transition-all duration-[var(--astryx-duration-fast)] ease-[var(--astryx-ease-standard)] active:scale-90 shadow-astryx-low border border-astryx-border-subtle cursor-pointer"
          data-testid="decrement"
          @click="dec(1)"
        >
          -
        </button>

        <input
          v-model="countModel"
          type="number"
          class="bg-astryx-body border border-astryx-border text-astryx-text-primary font-black text-xl rounded-astryx-element text-center w-28 h-12 shadow-inner focus:border-amber-500 focus:outline-none"
          data-testid="quantity"
          :aria-label="`Escolha quantas cotas você quer comprar. Mínimo: ${buyMin}. Máximo: ${buyMax}`"
        />

        <button
          class="w-12 h-12 rounded-astryx-element bg-astryx-surface hover:bg-astryx-card-hover text-astryx-text-primary font-black text-xl flex items-center justify-center transition-all duration-[var(--astryx-duration-fast)] ease-[var(--astryx-ease-standard)] active:scale-90 shadow-astryx-low border border-astryx-border-subtle cursor-pointer"
          data-testid="increment"
          @click="inc(1)"
        >
          +
        </button>
      </div>

      <!-- Resumo do Preço -->
      <div class="text-center sm:text-end w-full sm:w-auto">
        <p class="text-xl sm:text-2xl font-black text-amber-600 dark:text-amber-400 tracking-tight" data-testid="price-total">Valor total: {{ priceTotal }}</p>
      </div>
    </div>

    <!-- Alerta de Escassez / FOMO -->
    <div class="bg-amber-500/10 border border-amber-500/30 rounded-astryx-container p-3 flex items-center gap-3">
      <span class="text-xl flex-shrink-0 animate-bounce">⚡</span>
      <p class="text-xs text-amber-700 dark:text-amber-200">
        <strong>Atenção:</strong> As cotas são limitadas e reservadas apenas após a confirmação do Pix. Não perca a chance!
      </p>
    </div>

    <!-- CTA de Compra -->
    <div>
      <button
        type="button"
        class="w-full bg-gradient-to-r from-amber-500 via-amber-400 to-amber-500 hover:from-amber-400 hover:to-amber-300 text-slate-950 font-black text-base py-4 rounded-astryx-element sm:rounded-astryx-container shadow-astryx-med hover:shadow-astryx-high transition-all duration-[var(--astryx-duration-fast)] ease-[var(--astryx-ease-standard)] active:scale-98 flex items-center justify-center gap-2 uppercase tracking-wider"
        data-testid="confirm"
        @click="$emit('reserveNumbers', count)"
      >
        <span>GARANTIR MINHAS COTAS NO PIX</span>
        <span class="text-xl leading-none">👉</span>
      </button>
      <div class="flex items-center justify-center gap-4 mt-3 text-[11px] text-astryx-text-tertiary font-medium">
        <span class="flex items-center gap-1">🔒 Compra Segura</span>
        <span class="flex items-center gap-1">⚡ Baixa em 5s</span>
        <span class="flex items-center gap-1">📲 Direto no WhatsApp</span>
      </div>
    </div>
  </div>
</template>

<style scoped>
  .button-qntd {
    @apply border border-astryx-border-subtle bg-astryx-surface rounded-astryx-container p-3.5 text-center flex flex-col items-center justify-center;
  }
</style>
