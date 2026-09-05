<script setup lang="ts">
import { ref, onMounted, onUnmounted } from 'vue';

interface PurchaseNotification {
  name: string;
  city: string;
  item: string;
  qty: number;
  timeAgo: string;
  badge: string;
}

const notifications: PurchaseNotification[] = [
  { name: 'Felipe M.', city: 'São Paulo - SP', item: 'Boné RAW + Case Completo', qty: 15, timeAgo: 'há 2 minutos', badge: '🔥 Super Combo' },
  { name: 'Rodrigo S.', city: 'Rio de Janeiro - RJ', item: 'Boné RAW + Case Completo', qty: 50, timeAgo: 'há 4 minutos', badge: '⚡ Combo Gigante' },
  { name: 'Lucas P.', city: 'Belo Horizonte - MG', item: 'Kit Narguilé Amazon Hookah', qty: 10, timeAgo: 'há 6 minutos', badge: '🔥 10 Cotas' },
  { name: 'Gabriel K.', city: 'Curitiba - PR', item: 'Boné RAW + Case Completo', qty: 100, timeAgo: 'há 8 minutos', badge: '👑 Status Tubarão' },
  { name: 'Matheus B.', city: 'Campinas - SP', item: 'Pod System Vaporesso Luxe', qty: 25, timeAgo: 'há 11 minutos', badge: '⚡ 25 Cotas' },
  { name: 'Diego A.', city: 'Florianópolis - SC', item: 'Boné RAW + Case Completo', qty: 50, timeAgo: 'há 15 minutos', badge: '⚡ 50 Cotas' },
];

const currentIndex = ref(0);
const visible = ref(false);
let timer: any = null;
let displayTimer: any = null;

function showNext() {
  visible.value = true;
  displayTimer = setTimeout(() => {
    visible.value = false;
    currentIndex.value = (currentIndex.value + 1) % notifications.length;
  }, 4500);
}

onMounted(() => {
  setTimeout(() => {
    showNext();
    timer = setInterval(showNext, 8500);
  }, 2500);
});

onUnmounted(() => {
  if (timer) clearInterval(timer);
  if (displayTimer) clearTimeout(displayTimer);
});
</script>

<template>
  <Transition
    enter-active-class="transform transition ease-out duration-300"
    enter-from-class="translate-y-10 opacity-0 scale-95"
    enter-to-class="translate-y-0 opacity-100 scale-100"
    leave-active-class="transform transition ease-in duration-200"
    leave-from-class="translate-y-0 opacity-100 scale-100"
    leave-to-class="translate-y-6 opacity-0 scale-95"
  >
    <div
      v-if="visible"
      class="fixed bottom-5 left-4 sm:left-6 z-50 max-w-[340px] bg-astryx-card/95 border-2 border-amber-500/40 rounded-astryx-container p-3.5 shadow-2xl shadow-black/80 backdrop-blur-md flex items-center gap-3.5 transition-colors"
    >
      <div class="w-10 h-10 rounded-full bg-gradient-to-tr from-amber-500 to-yellow-400 text-slate-950 font-black text-sm flex items-center justify-center flex-shrink-0 shadow-md">
        💸
      </div>
      <div class="min-w-0 flex-1">
        <div class="flex items-center justify-between gap-1 mb-0.5">
          <p class="text-xs font-black text-astryx-text-primary truncate">
            {{ notifications[currentIndex].name }}
          </p>
          <span class="text-[9px] font-bold uppercase px-1.5 py-0.5 rounded bg-amber-500/20 text-amber-600 dark:text-amber-400">
            {{ notifications[currentIndex].badge }}
          </span>
        </div>
        <p class="text-[11px] text-astryx-text-secondary truncate">
          Acabou de pagar <strong class="text-emerald-600 dark:text-emerald-400 font-black">{{ notifications[currentIndex].qty }} cotas</strong>
        </p>
        <span class="text-[10px] text-astryx-text-tertiary flex items-center justify-between mt-0.5">
          <span>📍 {{ notifications[currentIndex].city }}</span>
          <span>{{ notifications[currentIndex].timeAgo }}</span>
        </span>
      </div>
    </div>
  </Transition>
</template>
