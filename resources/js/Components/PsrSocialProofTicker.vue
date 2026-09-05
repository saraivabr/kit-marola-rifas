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
  // Primeira notificação após 3 segundos
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
      class="fixed bottom-5 left-4 sm:left-6 z-50 max-w-[340px] bg-slate-900/95 border border-amber-500/40 rounded-2xl p-3.5 shadow-2xl shadow-black/80 backdrop-blur-md flex items-center gap-3.5"
    >
      <div class="relative flex-shrink-0">
        <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-amber-500 to-amber-600 flex items-center justify-center text-slate-950 font-black text-sm shadow-md">
          🎟️
        </div>
        <span class="absolute -top-1 -right-1 w-3 h-3 bg-emerald-500 border-2 border-slate-900 rounded-full animate-ping"></span>
        <span class="absolute -top-1 -right-1 w-3 h-3 bg-emerald-500 border-2 border-slate-900 rounded-full"></span>
      </div>

      <div class="flex-1 min-w-0">
        <div class="flex items-center justify-between gap-1">
          <p class="text-xs font-bold text-white truncate">
            {{ notifications[currentIndex].name }}
          </p>
          <span class="text-[10px] text-amber-400/80 font-medium">
            {{ notifications[currentIndex].timeAgo }}
          </span>
        </div>
        <p class="text-[11px] text-gray-300 truncate mt-0.5">
          Comprou <strong class="text-amber-400 font-bold">{{ notifications[currentIndex].qty }} cotas</strong> de
        </p>
        <p class="text-[11px] text-gray-400 font-medium truncate">
          {{ notifications[currentIndex].item }}
        </p>
      </div>

      <button
        type="button"
        class="text-gray-500 hover:text-gray-300 text-xs p-1"
        @click="visible = false"
      >
        ✕
      </button>
    </div>
  </Transition>
</template>
