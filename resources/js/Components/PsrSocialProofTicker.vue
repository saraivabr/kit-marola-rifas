<script setup lang="ts">
import { ref, onMounted, onUnmounted } from 'vue';

interface PurchaseNotification {
  name: string;
  city: string;
  item: string;
  qty: number;
  timeAgo: string;
}

const notifications: PurchaseNotification[] = [
  { name: 'Felipe M.', city: 'São Paulo - SP', item: 'Boné RAW + Case Completo', qty: 5, timeAgo: 'há 2 minutos' },
  { name: 'Rodrigo S.', city: 'Rio de Janeiro - RJ', item: 'Boné RAW + Case Completo', qty: 10, timeAgo: 'há 4 minutos' },
  { name: 'Lucas P.', city: 'Belo Horizonte - MG', item: 'Kit Narguilé Amazon Hookah', qty: 3, timeAgo: 'há 6 minutos' },
  { name: 'Gabriel K.', city: 'Curitiba - PR', item: 'Boné RAW + Case Completo', qty: 8, timeAgo: 'há 8 minutos' },
  { name: 'Matheus B.', city: 'Campinas - SP', item: 'Pod System Vaporesso Luxe', qty: 4, timeAgo: 'há 11 minutos' },
  { name: 'Diego A.', city: 'Florianópolis - SC', item: 'Boné RAW + Case Completo', qty: 12, timeAgo: 'há 15 minutos' },
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
    timer = setInterval(showNext, 9000);
  }, 3000);
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
      class="fixed bottom-5 left-4 sm:left-6 z-50 max-w-[340px] bg-astryx-card/95 border border-amber-500/40 rounded-astryx-container p-3.5 shadow-astryx-high backdrop-blur-md flex items-center gap-3.5 transition-colors"
    >
      <div class="w-9 h-9 rounded-full bg-emerald-500/15 border border-emerald-500/30 flex items-center justify-center text-emerald-600 dark:text-emerald-400 font-black text-sm flex-shrink-0">
        ✓
      </div>
      <div class="min-w-0 flex-1">
        <div class="flex items-center justify-between gap-1 mb-0.5">
          <p class="text-xs font-black text-astryx-text-primary truncate">
            {{ notifications[currentIndex].name }}
          </p>
          <span class="text-[10px] text-astryx-text-tertiary whitespace-nowrap">
            {{ notifications[currentIndex].timeAgo }}
          </span>
        </div>
        <p class="text-[11px] text-astryx-text-secondary truncate">
          Comprou <strong class="text-amber-600 dark:text-amber-400 font-black">{{ notifications[currentIndex].qty }} cotas</strong> de {{ notifications[currentIndex].item }}
        </p>
        <span class="text-[9px] text-astryx-text-tertiary block">
          📍 {{ notifications[currentIndex].city }}
        </span>
      </div>
    </div>
  </Transition>
</template>
