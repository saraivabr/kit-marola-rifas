<script setup lang="ts">
  import PsrLogo from './PsrLogo.vue';
  import { IconSvgFacebook, IconSvgInstagram, IconSvgTwitter } from '@Assets/icons';

  const year = new Date().getFullYear();
  const facebook = import.meta.env.VITE_FACEBOOK;
  const twitter = import.meta.env.VITE_TWITTER;
  const instagram = import.meta.env.VITE_INSTAGRAM;
</script>

<template>
  <footer class="bg-slate-950 text-gray-400 pt-10 pb-6 border-t border-amber-500/20">
    <div
      class="container max-w-[1024px] mx-auto px-4 flex flex-col gap-6"
    >
      <div class="flex flex-col sm:flex-row items-center justify-between gap-4 pb-6 border-b border-slate-800">
        <a
          :href="route('home')"
          class="flex items-center text-white"
        >
          <PsrLogo />
        </a>

        <span class="social inline-flex justify-center items-center space-x-4">
          <a :href="facebook" v-if="facebook" class="text-gray-400 hover:text-amber-400 transition-colors"><IconSvgFacebook /></a>
          <a :href="twitter" v-if="twitter" class="text-gray-400 hover:text-amber-400 transition-colors"><IconSvgTwitter /></a>
          <a :href="instagram" v-if="instagram" class="text-gray-400 hover:text-amber-400 transition-colors"><IconSvgInstagram /></a>
        </span>
      </div>

      <!-- Legal disclaimer for tobacco products -->
      <div class="text-xs text-center sm:text-left text-gray-500 space-y-2 leading-relaxed">
        <p class="text-amber-400/90 font-semibold flex items-center justify-center sm:justify-start gap-1">
          <span class="inline-block w-2 h-2 rounded-full bg-amber-500"></span>
          Venda proibida para menores de 18 anos.
        </p>
        <p>
          Este site destina-se ao entretenimento através de rifas de artigos de tabacaria, vapes e acessórios. 
          O uso de produtos fumígenos é prejudicial à saúde. Todos os pagamentos e atribuições de números são processados de forma transparente via Pix.
        </p>
      </div>

      <div class="flex flex-col sm:flex-row items-center justify-between text-xs text-gray-500 pt-4 border-t border-slate-900 gap-3">
        <p>© <span v-text="year"></span> <strong>Kit Marola Tabacaria Rifas</strong> — Todos os direitos reservados.</p>
        <div class="flex items-center gap-3">
          <span class="text-gray-400 flex items-center gap-1">🔒 SSL 256-bit</span>
          <span class="text-emerald-400 font-bold flex items-center gap-1">❖ PIX Banco Central</span>
          <span class="text-amber-400 font-bold flex items-center gap-1">🔞 +18 Anos</span>
        </div>
      </div>
    </div>
  </footer>
</template>

<style scoped>
  .social a {
    @apply text-gray-400 hover:text-amber-400;
  }
</style>
