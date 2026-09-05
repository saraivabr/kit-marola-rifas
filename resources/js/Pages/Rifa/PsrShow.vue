<script setup lang="ts">
  import { computed, ref } from 'vue';
  import { useLocaleCurrency } from '@Composables/Locale';
  import { useSorted } from '@vueuse/core';
  import PsrCard from '@Components/PsrCard.vue';
  import PsrRanking from '@Components/PsrRanking.vue';
  import ReserveNumbers from '@Components/ReserveNumbers.vue';
  import FormReserveNumbers from '@Components/FormReserveNumbers.vue';
  import ButtonListOrders from '@Components/ButtonListOrders.vue';
  import PsrHeader from '@Components/PsrHeader.vue';
  import PsrFooter from '@Components/PsrFooter.vue';
  import PsrSocialProofTicker from '@Components/PsrSocialProofTicker.vue';
  import PsrBottomBar from '@Components/PsrBottomBar.vue';

  const props = defineProps<{
    rifa: Rifa;
    ranking: Ranking[];
    winners: Winner[];
  }>();

  const displayFormReserveNumbers = ref(false);
  const orderQuantitySelected = ref(props.rifa.buy_min || 1);

  const price = computed(() => (props.rifa.price ? useLocaleCurrency(props.rifa.price) : ''));
  const progress = computed(() => Number(props.rifa.sold_percentage ?? props.rifa.progress_percentage ?? 0));

  const isFinished = computed(
    () =>
      !!props.winners.length ||
      props.rifa.status === 'finished'
  );

  const listWinners = useSorted(props.winners, (prev, cur) => (prev.position > cur.position ? 1 : -1));

  const winnerVideo = computed(() => listWinners.value.filter((winner) => winner.video));

  function onReserveNumbers(): void {
    displayFormReserveNumbers.value = true;
  }
</script>

<template>
  <!-- ------ -->
  <!-- Header -->
  <!-- ------ -->
  <PsrHeader />

  <!-- Ticker flutuante de FOMO -->
  <PsrSocialProofTicker />

  <!-- ------------ -->
  <!-- Rifa Content -->
  <!-- ------------ -->
  <div v-if="rifa" class="container max-w-[1060px] mx-auto space-y-6 pt-6 pb-28 px-4 text-astryx-text-primary">
    <!-- Header da Rifa com FOMO e Visualizadores -->
    <div class="space-y-4">
      <div class="flex flex-wrap items-center justify-between gap-2">
        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-black bg-amber-500/10 border border-amber-500/30 text-amber-400 uppercase tracking-wider">
          <span class="w-2 h-2 rounded-full bg-amber-400 animate-pulse"></span>
          Edição Oficial Tabacaria
        </span>

        <div class="flex items-center gap-2 bg-astryx-surface border border-astryx-border-subtle px-3 py-1 rounded-full text-xs text-astryx-text-secondary">
          <span class="w-2 h-2 rounded-full bg-emerald-400 animate-ping"></span>
          <span>👀 <strong>37 pessoas</strong> de olho nesta rifa</span>
        </div>
      </div>

      <h1 class="text-2xl sm:text-4xl font-black font-[Raleway] leading-tight text-astryx-text-primary tracking-tight">
        {{ rifa.title }}
      </h1>
      
      <!-- Barra de Progresso de Vendas com Efeito Glow -->
      <div class="bg-astryx-card border border-astryx-border-subtle rounded-astryx-card p-5 shadow-astryx-med">
        <div class="flex justify-between items-center text-sm font-semibold mb-2.5">
          <span class="text-astryx-text-primary flex items-center gap-2 font-bold">
            <span class="text-amber-500 text-lg animate-pulse">🔥</span> Progresso das Cotas Vendidas
          </span>
          <span class="text-amber-600 dark:text-amber-400 font-black text-base bg-amber-500/10 border border-amber-500/30 px-3 py-0.5 rounded-full">
            {{ progress }}% Vendido
          </span>
        </div>
        <div class="w-full bg-astryx-muted rounded-full h-3.5 overflow-hidden border border-astryx-border-subtle p-[2px]">
          <div
            class="bg-gradient-to-r from-amber-500 via-amber-400 to-yellow-300 h-full rounded-full transition-all duration-700 ease-out shadow-sm"
            :style="{ width: `${Math.min(100, Math.max(0, progress))}%` }"
          ></div>
        </div>
        <div class="flex flex-wrap justify-between items-center text-xs text-astryx-text-tertiary mt-2.5 gap-2">
          <span class="text-amber-300 font-semibold flex items-center gap-1">
            ⚡ Sorteio oficial assim que atingir 100% das cotas!
          </span>
          <span class="text-emerald-400 font-bold flex items-center gap-1">
            ❖ Baixa Instantânea no PIX
          </span>
        </div>
      </div>
    </div>

    <!-- Imagem da Rifa com Moldura e Badge -->
    <div class="relative rounded-astryx-card overflow-hidden shadow-astryx-high border border-astryx-border-subtle bg-astryx-surface">
      <picture>
        <source media="(max-width: 425px)" :srcset="`${rifa.thumbnail}?width=425`" />
        <source media="(max-width: 768px)" :srcset="`${rifa.thumbnail}?width=768`" />
        <source media="(max-width: 1024px)" :srcset="`${rifa.thumbnail}?width=1024`" />
        <img :src="rifa.thumbnail" :alt="rifa.title" class="w-full max-h-[500px] object-cover object-center" />
      </picture>

      <!-- Sexy Canvas: Liberdade de Escolha da Criança Interior -->
      <div class="absolute top-4 left-4 flex flex-col gap-2 z-10">
        <span class="bg-black/80 backdrop-blur-md text-emerald-400 border border-emerald-500/40 text-xs font-black px-3 py-1 rounded-full shadow-lg flex items-center gap-1.5">
          <span>🎁</span> O Ganhador escolhe: Kit Físico ou Pix na Conta!
        </span>
      </div>

      <div class="absolute bottom-4 right-4 bg-black/85 backdrop-blur-md border border-amber-500/40 text-white px-4 py-2 rounded-astryx-container shadow-astryx-high flex items-center gap-2">
        <span class="text-xs text-slate-300">Por apenas</span>
        <span class="text-xl font-black text-amber-400">{{ price }}</span>
        <span class="text-[10px] text-emerald-400 font-bold bg-emerald-500/20 px-2 py-0.5 rounded">a cota</span>
      </div>
    </div>

    <!-- ----------------------- -->
    <!-- Description and Ranking -->
    <!-- ----------------------- -->
    <div class="space-y-4 sm:space-y-0 sm:flex sm:gap-4">
      <PsrCard class="md:flex-auto">
        <template #heading>Descrição do Kit</template>
        <!-- eslint-disable-next-line -->
        <p v-html="rifa.description" />
      </PsrCard>

      <PsrRanking v-if="ranking.length && rifa.ranking_buyer" class="sm:flex-auto md:flex-initial" :users="ranking" />
    </div>

    <!-- -------------------- -->
    <!-- Form Reserve Numbers -->
    <!-- -------------------- -->
    <ReserveNumbers
      v-if="!isFinished"
      v-model:quantity="orderQuantitySelected"
      :price="rifa.price"
      :buy-max="rifa.buy_max"
      :buy-min="rifa.buy_min"
      @reserve-numbers="onReserveNumbers"
    />

    <!-- ----------- -->
    <!-- Winner List -->
    <!-- ----------- -->
    <PsrCard v-if="listWinners.length" class="bg-green-600">
      <template #heading>
        <span class="text-2xl text-white">Ganhadores</span>
      </template>

      <template #default>
        <ol class="text-center" data-testid="winners-list">
          <li v-for="winner in listWinners" :key="`winner-${winner.position}`" class="font-bold text-xl text-white">
            {{ winner.position }}º Prêmio - {{ winner.customer_fullname }}
          </li>
        </ol>
      </template>
    </PsrCard>

    <PsrCard v-if="winnerVideo.length">
      <template #heading>Vídeo do Ganhador</template>

      <template #default>
        <video
          class="max-h-[570px] m-auto"
          controls
          controlslist="nodownload noremoteplayback"
          :src="winnerVideo[0].video"
        />
      </template>
    </PsrCard>

    <!-- ------------------------------------- -->
    <!-- Payment Method, Raffle and My Numbers -->
    <!-- ------------------------------------- -->
    <div class="flex flex-wrap gap-4 justify-between text-center">
      <PsrCard class="md:flex-auto">
        <template #heading>Meio de Pagamento</template>
        <div><span class="text-[#51d2bb] font-bold text-xl">❖</span> PIX</div>
      </PsrCard>

      <PsrCard class="md:flex-auto">
        <template #heading>Sorteio</template>
        <div>🍀 {{ rifa.raffle }}</div>
      </PsrCard>

      <PsrCard class="flex-1 md:flex-auto">
        <template #heading>Consulte</template>
        <div><ButtonListOrders :rifa-slug="rifa.slug" /></div>
      </PsrCard>
    </div>

    <!-- ------------------------------ -->
    <!-- Form Reserve Numbers Component -->
    <!-- ------------------------------ -->
    <Teleport v-if="displayFormReserveNumbers" to="body">
      <FormReserveNumbers
        :rifa="rifa.id"
        :quantity="orderQuantitySelected"
        @dismiss="displayFormReserveNumbers = false"
      />
    </Teleport>
  </div>

  <!-- Sticky Bottom CTA Flutuante (Apenas Mobile) -->
  <div v-if="!isFinished" class="md:hidden fixed bottom-14 left-0 right-0 z-30 p-3 bg-gradient-to-t from-astryx-body via-astryx-body/95 to-transparent pointer-events-none">
    <button
      type="button"
      @click="onReserveNumbers"
      class="pointer-events-auto w-full py-3.5 px-5 bg-gradient-to-r from-amber-500 via-amber-400 to-yellow-500 active:scale-[0.98] text-zinc-950 font-black text-sm uppercase tracking-wider rounded-2xl shadow-2xl shadow-amber-500/25 flex items-center justify-between transition-all"
    >
      <span class="flex items-center gap-2">
        <span class="text-base">🎟️</span>
        <span>Comprar Cotas</span>
      </span>
      <span class="bg-zinc-950/20 px-3 py-1 rounded-xl text-xs font-black">
        Apenas {{ price }}
      </span>
    </button>
  </div>

  <PsrFooter />

  <!-- Barra de Navegação Inferior Fixa no Mobile -->
  <PsrBottomBar />
</template>
