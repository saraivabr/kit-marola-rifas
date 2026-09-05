<script setup lang="ts">
  import PsrFooter from '@Components/PsrFooter.vue';
  import PsrHeader from '@Components/PsrHeader.vue';
  import PsrRifaCard from '@Components/PsrRifaCard.vue';
  import PsrSlider from '@Components/PsrSlider.vue';
  import PsrSocialProofTicker from '@Components/PsrSocialProofTicker.vue';
  import PsrTrustBadges from '@Components/PsrTrustBadges.vue';
  import PsrBottomBar from '@Components/PsrBottomBar.vue';

  defineProps<{
    values: Rifa[];
    slideshows: Slideshow[];
  }>();
</script>

<template>
  <PsrHeader />

  <!-- Ticker flutuante de compras recentes (FOMO) -->
  <PsrSocialProofTicker />

  <div class="bg-astryx-body min-h-screen text-astryx-text-primary">
    <!-- Slider / Banner Principal -->
    <div class="max-w-[1200px] mx-auto pt-4 px-4">
      <PsrSlider :items="slideshows" />
    </div>

    <main class="container max-w-[1200px] mx-auto px-4 py-8">
      <!-- Selos de Confiança e Benefícios -->
      <PsrTrustBadges />

      <!-- Cabeçalho da Seção de Rifas Ativas com Indicador Live -->
      <div class="flex flex-col sm:flex-row sm:items-end justify-between mb-8 gap-4 border-b border-astryx-border-subtle pb-5">
        <div>
          <div class="flex items-center gap-2 mb-2">
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-black bg-amber-500/10 border border-amber-500/30 text-amber-400 uppercase tracking-wider">
              <span class="w-2 h-2 rounded-full bg-amber-400 animate-pulse"></span>
              Sorteios Oficiais
            </span>
            <span class="text-xs text-astryx-text-tertiary font-medium">100% no WhatsApp</span>
          </div>
          <h2 class="text-2xl sm:text-3xl font-black text-astryx-text-primary tracking-tight flex items-center gap-2 font-[Raleway]">
            🔥 Rifas em Andamento
          </h2>
          <p class="text-xs sm:text-sm text-astryx-text-secondary mt-1">
            Escolha seu kit favorito, pague via Pix e receba seus números da sorte instantaneamente.
          </p>
        </div>

        <div class="flex items-center gap-2 bg-astryx-surface px-4 py-2 rounded-astryx-element border border-astryx-border-subtle text-xs text-astryx-text-secondary">
          <span class="text-emerald-400 font-bold">⚡ Alta Demanda:</span>
          <span>Mais de 300 cotas vendidas hoje</span>
        </div>
      </div>

      <!-- Grid de Rifas -->
      <section class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-16">
        <article v-for="rifa in values" :key="rifa.slug" class="h-full">
          <PsrRifaCard :rifa="rifa" />
        </article>
      </section>

      <!-- Seção de Prova Social & Depoimentos de Ganhadores -->
      <section class="bg-astryx-card border border-astryx-border-subtle rounded-astryx-page p-6 sm:p-10 mb-12 shadow-astryx-med relative overflow-hidden">
        <div class="absolute -right-10 -bottom-10 w-60 h-60 bg-amber-500/5 rounded-full blur-3xl pointer-events-none"></div>

        <div class="text-center max-w-xl mx-auto mb-8">
          <span class="text-xs font-bold text-amber-600 dark:text-amber-400 uppercase tracking-widest bg-amber-500/10 px-3 py-1 rounded-full border border-amber-500/20">
            🏆 Ganhadores Satisfeitos
          </span>
          <h3 class="text-xl sm:text-2xl font-black text-astryx-text-primary mt-3 font-[Raleway]">
            Quem participa, aprova e recebe!
          </h3>
          <p class="text-xs sm:text-sm text-astryx-text-secondary mt-1">
            Confira quem já levou os kits mais cobiçados da tabacaria para casa.
          </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
          <div class="bg-astryx-surface border border-astryx-border-subtle rounded-astryx-container p-5 shadow-astryx-low">
            <div class="flex items-center gap-1 text-amber-500 text-sm mb-2">
              ★★★★★
            </div>
            <p class="text-xs text-astryx-text-secondary italic mb-4">
              "Comprei 5 cotas despretensioso e levei o Boné da RAW e o kit case! O Pix confirmou na hora e os números chegaram no meu zap. Veio super bem embalado!"
            </p>
            <div class="flex items-center gap-3">
              <div class="w-9 h-9 rounded-full bg-amber-500/20 border border-amber-500/40 flex items-center justify-center text-amber-600 dark:text-amber-400 font-bold text-xs">
                RS
              </div>
              <div>
                <p class="text-xs font-bold text-astryx-text-primary">Rodrigo Silva</p>
                <p class="text-[11px] text-astryx-text-tertiary">São Paulo, SP • Ganhador Kit RAW</p>
              </div>
            </div>
          </div>

          <div class="bg-astryx-surface border border-astryx-border-subtle rounded-astryx-container p-5 shadow-astryx-low">
            <div class="flex items-center gap-1 text-amber-500 text-sm mb-2">
              ★★★★★
            </div>
            <p class="text-xs text-astryx-text-secondary italic mb-4">
              "Sistema muito rápido! O sorteio foi transmitido ao vivo e o envio foi feito no mesmo dia via Sedex com rastreio. 100% de confiança."
            </p>
            <div class="flex items-center gap-3">
              <div class="w-9 h-9 rounded-full bg-emerald-500/20 border border-emerald-500/40 flex items-center justify-center text-emerald-600 dark:text-emerald-400 font-bold text-xs">
                ML
              </div>
              <div>
                <p class="text-xs font-bold text-astryx-text-primary">Murilo Lima</p>
                <p class="text-[11px] text-astryx-text-tertiary">Curitiba, PR • Ganhador Narguilé</p>
              </div>
            </div>
          </div>

          <div class="bg-astryx-surface border border-astryx-border-subtle rounded-astryx-container p-5 shadow-astryx-low">
            <div class="flex items-center gap-1 text-amber-500 text-sm mb-2">
              ★★★★★
            </div>
            <p class="text-xs text-astryx-text-secondary italic mb-4">
              "Gastei menos de 15 reais e ganhei o Pod Vaporesso com os juices importados. O atendimento no WhatsApp foi nota 10!"
            </p>
            <div class="flex items-center gap-3">
              <div class="w-9 h-9 rounded-full bg-purple-500/20 border border-purple-500/40 flex items-center justify-center text-purple-600 dark:text-purple-400 font-bold text-xs">
                TC
              </div>
              <div>
                <p class="text-xs font-bold text-astryx-text-primary">Thiago Castro</p>
                <p class="text-[11px] text-astryx-text-tertiary">Belo Horizonte, MG • Ganhador Vape</p>
              </div>
            </div>
          </div>
        </div>
      </section>
    </main>
  </div>

  <PsrFooter />

  <!-- Barra de Navegação Inferior Fixa no Mobile -->
  <PsrBottomBar />
</template>

<style>
  #app {
    padding: 0;
  }

  /**
  * Header
  */
  nav > a {
    @apply hover:text-[#6366f1] hover:underline underline-offset-4;
  }

  /**
   * Slider
   */
  .slider-items::-webkit-scrollbar {
    display: none;
  }
  .slider-items {
    scrollbar-width: none;
  }
</style>
