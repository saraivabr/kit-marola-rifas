<script setup lang="ts">
  import { computed, ref, watch } from 'vue';
  import { useCounter } from '@vueuse/core';
  import { useLocaleCurrency } from '../Composables/Locale';

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

  function selectExact(qty: number) {
    set(qty);
    countModel.value = qty;
    emits('update:quantity', qty);
    if (typeof navigator !== 'undefined' && 'vibrate' in navigator) {
      try { navigator.vibrate(30); } catch (e) {}
    }
  }
</script>

<template>
  <div class="bg-astryx-card border border-astryx-border-subtle rounded-astryx-card p-5 sm:p-7 shadow-astryx-high space-y-6 transition-colors">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-astryx-border-subtle pb-4">
      <div>
        <span class="text-[11px] font-black text-amber-500 uppercase tracking-widest bg-amber-500/10 px-2.5 py-0.5 rounded-full border border-amber-500/20">
          Combos Mais Desejados
        </span>
        <h3 class="text-lg sm:text-xl font-black text-astryx-text-primary mt-1">
          Multiplique suas chances de levar o prêmio
        </h3>
      </div>
      <div class="flex items-center gap-1.5 text-xs text-emerald-600 dark:text-emerald-400 font-bold bg-emerald-500/10 border border-emerald-500/20 px-3 py-1.5 rounded-astryx-element self-start sm:self-auto">
        <span class="w-2 h-2 rounded-full bg-emerald-500 animate-ping"></span>
        <span>🔥 +87% escolhem combos</span>
      </div>
    </div>

    <!-- Sexy Canvas: Gula + Avareza (Combos com percepção extrema de valor e economia) -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 w-full mx-auto">
      
      <!-- Combo 5 Cotas -->
      <button
        class="button-qntd group hover:border-amber-500/60 transition-all active:scale-95 cursor-pointer relative"
        :class="count === 5 ? 'ring-2 ring-amber-500 !border-amber-500 bg-amber-500/10' : ''"
        data-testid="quantity-1"
        @click="selectExact(5)"
      >
        <span class="text-xs text-astryx-text-tertiary block font-normal">Chances Rápidas</span>
        <span class="text-base font-black text-astryx-text-primary group-hover:text-amber-500">+5 Cotas</span>
        <span class="text-[10px] text-emerald-600 dark:text-emerald-400 font-bold mt-0.5">5 bilhetes</span>
      </button>

      <!-- Combo 15 Cotas (Mais Escolhido / Efeito Manada) -->
      <button
        class="button-qntd relative border-amber-500/60 bg-amber-500/10 hover:bg-amber-500/20 transition-all active:scale-95 cursor-pointer"
        :class="count === 15 ? 'ring-2 ring-amber-500 !bg-amber-500/20' : ''"
        data-testid="quantity-10"
        @click="selectExact(15)"
      >
        <span class="absolute -top-2.5 right-2 bg-gradient-to-r from-amber-500 to-amber-400 text-slate-950 text-[9px] font-black px-2 py-0.5 rounded-full shadow-md uppercase tracking-wider">
          🔥 Mais Vendido
        </span>
        <span class="text-xs text-amber-600 dark:text-amber-400 block font-normal">Super Combo</span>
        <span class="text-base font-black text-amber-600 dark:text-amber-400">+15 Cotas</span>
        <span class="text-[10px] text-amber-700 dark:text-amber-300 font-bold mt-0.5">15x Mais Chances</span>
      </button>

      <!-- Combo 50 Cotas (Gula & Potência) -->
      <button
        class="button-qntd relative border-emerald-500/40 bg-emerald-500/10 hover:bg-emerald-500/20 transition-all active:scale-95 cursor-pointer"
        :class="count === 50 ? 'ring-2 ring-emerald-500 !bg-emerald-500/20' : ''"
        data-testid="quantity-50"
        @click="selectExact(50)"
      >
        <span class="absolute -top-2.5 right-2 bg-gradient-to-r from-emerald-500 to-teal-400 text-slate-950 text-[9px] font-black px-2 py-0.5 rounded-full shadow-md uppercase tracking-wider">
          ⚡ 50x Força
        </span>
        <span class="text-xs text-emerald-600 dark:text-emerald-400 block font-normal">Combo Gigante</span>
        <span class="text-base font-black text-emerald-600 dark:text-emerald-400">+50 Cotas</span>
        <span class="text-[10px] text-emerald-700 dark:text-emerald-300 font-bold mt-0.5">Disparado na frente</span>
      </button>

      <!-- Combo 100 Cotas (Vaidade do Tubarão da Rifa) -->
      <button
        class="button-qntd relative border-purple-500/40 bg-purple-500/10 hover:bg-purple-500/20 transition-all active:scale-95 cursor-pointer"
        :class="count === 100 ? 'ring-2 ring-purple-500 !bg-purple-500/20' : ''"
        data-testid="quantity-100"
        @click="selectExact(100)"
      >
        <span class="absolute -top-2.5 right-2 bg-gradient-to-r from-purple-500 to-indigo-500 text-white text-[9px] font-black px-2 py-0.5 rounded-full shadow-md uppercase tracking-wider">
          👑 Status Tubarão
        </span>
        <span class="text-xs text-purple-600 dark:text-purple-400 block font-normal">Lote Máximo</span>
        <span class="text-base font-black text-purple-600 dark:text-purple-400">+100 Cotas</span>
        <span class="text-[10px] text-purple-700 dark:text-purple-300 font-bold mt-0.5">Domina o Ranking</span>
      </button>
    </div>

    <!-- Controle Manual de Cotas (Preguiça & Facilidade) -->
    <div class="bg-astryx-muted/80 p-4 rounded-astryx-container border border-astryx-border-subtle flex flex-col sm:flex-row items-center justify-between gap-4">
      <div class="flex items-center gap-2 w-full sm:w-auto justify-center">
        <button
          class="w-12 h-12 rounded-astryx-element bg-astryx-surface hover:bg-astryx-card-hover text-astryx-text-primary font-black text-xl flex items-center justify-center transition-all active:scale-90 shadow-astryx-low border border-astryx-border-subtle cursor-pointer"
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
          class="w-12 h-12 rounded-astryx-element bg-astryx-surface hover:bg-astryx-card-hover text-astryx-text-primary font-black text-xl flex items-center justify-center transition-all active:scale-90 shadow-astryx-low border border-astryx-border-subtle cursor-pointer"
          data-testid="increment"
          @click="inc(1)"
        >
          +
        </button>
      </div>

      <!-- Resumo do Preço + Avareza -->
      <div class="text-center sm:text-end w-full sm:w-auto">
        <span class="text-xs text-astryx-text-tertiary block">Total do seu investimento:</span>
        <p class="text-2xl sm:text-3xl font-black text-amber-600 dark:text-amber-400 tracking-tight" data-testid="price-total">Valor total: {{ priceTotal }}</p>
      </div>
    </div>

    <!-- Alerta de Escassez / Ira (Gatilho da Criança Interior - Não Ficar de Fora) -->
    <div class="bg-amber-500/10 border border-amber-500/30 rounded-astryx-container p-3 flex items-center gap-3">
      <span class="text-xl flex-shrink-0 animate-bounce">⚡</span>
      <p class="text-xs text-amber-700 dark:text-amber-200 leading-relaxed">
        <strong>Aviso da Diretoria:</strong> Os números são aleatórios e gerados direto pelo sistema após o Pix. Quanto mais cotas você levar, maior a probabilidade matemática do prêmio ser seu!
      </p>
    </div>

    <!-- CTA de Compra Magnético (Preguiça + Recompensa da Criança Interior) -->
    <div>
      <button
        type="button"
        class="w-full bg-gradient-to-r from-amber-500 via-yellow-400 to-amber-500 hover:from-amber-400 hover:to-yellow-300 text-slate-950 font-black text-base py-4 rounded-astryx-container shadow-xl hover:shadow-amber-500/30 transition-all active:scale-98 flex items-center justify-center gap-2 uppercase tracking-wider cursor-pointer"
        data-testid="confirm"
        @click="$emit('reserveNumbers', count)"
      >
        <span>GARANTIR MINHAS COTAS NO PIX</span>
        <span class="text-xl leading-none">👉</span>
      </button>
      
      <div class="flex flex-wrap items-center justify-center gap-4 mt-3 text-[11px] text-astryx-text-tertiary font-medium">
        <span class="flex items-center gap-1">🔒 Ambiente Criptografado</span>
        <span class="flex items-center gap-1">⚡ Baixa em 5s</span>
        <span class="flex items-center gap-1">📲 Bilhetes no WhatsApp</span>
      </div>
    </div>
  </div>
</template>

<style scoped>
  .button-qntd {
    @apply border border-astryx-border-subtle bg-astryx-surface rounded-astryx-container p-3.5 text-center flex flex-col items-center justify-center;
  }
</style>
