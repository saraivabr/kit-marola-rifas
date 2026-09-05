<script setup lang="ts">
  import { ref } from 'vue';
  import HeaderMenu from './HeaderMenu.vue';
  import PsrLogo from './PsrLogo.vue';
  import PsrThemeToggle from './PsrThemeToggle.vue';
  import AgeVerificationModal from './AgeVerificationModal.vue';

  const navSideOpen = ref(false);
</script>

<template>
  <AgeVerificationModal />

  <!-- Top bar de avisos e status -->
  <div class="bg-gradient-to-r from-amber-600 via-amber-500 to-amber-600 text-slate-950 text-[11px] py-1.5 px-4 text-center font-black tracking-wider flex items-center justify-center gap-2 shadow-sm">
    <span class="bg-slate-950 text-amber-400 px-2 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider">
      18+ Apenas
    </span>
    <span>🔥 SORTEIOS OFICIAIS TABACARIA | COTAS A PARTIR DE R$ 2,99 | RECEBA NO WHATSAPP</span>
  </div>

  <header class="bg-astryx-body/90 backdrop-blur-md border-b border-astryx-border-subtle sticky top-0 z-40 shadow-astryx-med">
    <div
      class="container max-w-[1140px] flex flex-wrap px-4 py-3.5 flex-col place-content-center sm:flex-row sm:flex-nowrap sm:gap-4 sm:justify-between items-center"
    >
      <div class="flex flex-wrap items-center justify-between w-full">
        <button type="button" class="appearance-none md:hidden md:invisible text-amber-400 p-1 rounded-astryx-element hover:bg-white/5 transition-colors duration-[var(--astryx-duration-fast)]" @click="navSideOpen = true">
          <svg
            class="h-7 w-7"
            preserveAspectRatio="xMidYMid meet"
            viewBox="0 0 50 50"
            xmlns="http://www.w3.org/2000/svg"
          >
            <defs>
              <clipPath id="__lottie_element_35"><rect height="50" width="50" x="0" y="0" /></clipPath>
            </defs>
            <g clip-path="url(#__lottie_element_35)">
              <g opacity="1" transform="matrix(1,0,0,1,0,0)" style="display: block">
                <g opacity="1" transform="matrix(1,0,0,1,0,0)">
                  <path
                    d=" M0,40 C0,40 50,40 50,40"
                    fill-opacity="0"
                    stroke="rgb(245,158,11)"
                    stroke-linecap="butt"
                    stroke-linejoin="miter"
                    stroke-miterlimit="4"
                    stroke-opacity="1"
                    stroke-width="5"
                  />
                </g>
                <g opacity="1" transform="matrix(1,0,0,1,0,0)">
                  <path
                    d=" M50,25 C50,25 0,25 0,25"
                    fill-opacity="0"
                    stroke="rgb(245,158,11)"
                    stroke-linecap="butt"
                    stroke-linejoin="miter"
                    stroke-miterlimit="4"
                    stroke-opacity="1"
                    stroke-width="5"
                  />
                </g>
                <g opacity="1" transform="matrix(1,0,0,1,0,0)">
                  <path
                    d=" M50,10 C50,10 0,10 0,10"
                    fill-opacity="0"
                    stroke="rgb(245,158,11)"
                    stroke-linecap="butt"
                    stroke-linejoin="miter"
                    stroke-miterlimit="4"
                    stroke-opacity="1"
                    stroke-width="5"
                  />
                </g>
              </g>
              <g opacity="1" transform="matrix(1,0,0,1,0,0)" style="display: none">
                <g opacity="1" transform="matrix(1,0,0,1,0,0)">
                  <path
                    d=" M0,40 C0,40 50,40 50,40"
                    fill-opacity="0"
                    stroke="rgb(245,158,11)"
                    stroke-linecap="butt"
                    stroke-linejoin="miter"
                    stroke-miterlimit="4"
                    stroke-opacity="1"
                    stroke-width="5"
                  />
                </g>
                <g opacity="1" transform="matrix(1,0,0,1,0,0)">
                  <path
                    d=" M50,25 C50,25 0,25 0,25"
                    fill-opacity="0"
                    stroke="rgb(245,158,11)"
                    stroke-linecap="butt"
                    stroke-linejoin="miter"
                    stroke-miterlimit="4"
                    stroke-opacity="1"
                    stroke-width="5"
                  />
                </g>
                <g opacity="1" transform="matrix(1,0,0,1,0,0)">
                  <path
                    d=" M50,10 C50,10 0,10 0,10 C0,10 50,10 50,10z"
                    fill-opacity="0"
                    stroke="rgb(245,158,11)"
                    stroke-linecap="butt"
                    stroke-linejoin="miter"
                    stroke-miterlimit="4"
                    stroke-opacity="1"
                    stroke-width="5"
                  />
                </g>
              </g>
              <g opacity="1" transform="matrix(1,0,0,1,0,0)" style="display: none">
                <g opacity="1" transform="matrix(1,0,0,1,0,0)">
                  <path
                    d=" M50,25 C50,25 0,25 0,25"
                    fill-opacity="0"
                    stroke="rgb(0,0,0)"
                    stroke-linecap="butt"
                    stroke-linejoin="miter"
                    stroke-miterlimit="4"
                    stroke-opacity="1"
                    stroke-width="5"
                  />
                </g>
                <g opacity="1" transform="matrix(1,0,0,1,0,0)">
                  <path
                    d=" M50,25 C50,25 0,25 0,25"
                    fill-opacity="0"
                    stroke="rgb(0,0,0)"
                    stroke-linecap="butt"
                    stroke-linejoin="miter"
                    stroke-miterlimit="4"
                    stroke-opacity="1"
                    stroke-width="5"
                  />
                </g>
              </g>
            </g>
          </svg>
        </button>

        <a :href="route('home')" class="flex font-medium items-center text-astryx-text-primary sm:m-0">
          <PsrLogo />
        </a>

        <!-- Botão de Tema no Header para Acesso Imediato -->
        <div class="flex items-center gap-2 md:hidden">
          <PsrThemeToggle />
        </div>
      </div>

      <HeaderMenu v-model:active="navSideOpen" />
    </div>
  </header>
</template>
