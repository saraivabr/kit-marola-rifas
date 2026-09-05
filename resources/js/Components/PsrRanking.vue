<script setup lang="ts">
  import { computed, readonly } from 'vue';
  import PsrCard from './PsrCard.vue';

  const props = defineProps<{
    users: Ranking[];
  }>();

  const ranking = computed(() =>
    Array.from(props.users)
      .sort((prev, cur) => (prev.total_numbers < cur.total_numbers ? 1 : -1))
      .slice(0, 3)
  );

  const medals = readonly(['🥇', '🥈', '🥉']);
  const titles = readonly(['Tubarão da Rifa', 'Mestre das Cotas', 'Comprador VIP']);
</script>

<template>
  <PsrCard class="border-2 border-amber-500/30">
    <template #heading>
      <div class="flex items-center justify-center gap-2">
        <span>🏆</span>
        <span>Ranking dos Maiores Compradores</span>
      </div>
    </template>

    <div class="flex flex-wrap justify-around gap-4 mx-auto sm:max-w-xl text-center pt-2">
      <div
        v-for="(user, index) in ranking"
        :key="`ranking-${user.total_numbers}`"
        :class="index === 0 ? 'flex-initial w-full sm:flex-auto sm:w-auto p-4 bg-amber-500/10 rounded-2xl border border-amber-500/30 shadow-md' : 'flex-none sm:flex-auto p-3 bg-astryx-surface rounded-xl border border-astryx-border-subtle'"
        data-test="ranking-user"
      >
        <span class="text-[36px] block transform hover:scale-110 transition-transform">{{ medals.at(index) }}</span>
        <span class="inline-block text-[10px] font-black uppercase tracking-wider text-amber-600 dark:text-amber-400 bg-amber-500/20 px-2 py-0.5 rounded-full mt-1">
          {{ titles.at(index) }}
        </span>
        <p class="text-sm font-black text-astryx-text-primary mt-2">
          {{ user.customer_fullname }}
        </p>
        <p class="text-xs text-emerald-600 dark:text-emerald-400 font-bold mt-0.5">
          <strong>{{ user.total_numbers }}</strong> bilhetes adquiridos
        </p>
      </div>
    </div>
  </PsrCard>
</template>
