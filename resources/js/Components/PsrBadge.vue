<script setup lang="ts">
  import { computed } from 'vue';

  const props = withDefaults(
    defineProps<{
      type?: 'default' | 'accent' | 'danger' | 'error' | 'information' | 'info' | 'success' | 'warning';
      variant?: 'default' | 'accent' | 'danger' | 'error' | 'information' | 'info' | 'success' | 'warning';
    }>(),
    {
      type: 'default',
      variant: undefined,
    }
  );

  const resolved = computed(() => props.variant ?? props.type);

  const badgeClass = computed(() => {
    switch (resolved.value) {
      case 'accent':
        return 'bg-amber-500/15 text-amber-400 border-amber-500/30';
      case 'danger':
      case 'error':
        return 'bg-red-500/15 text-red-400 border-red-500/30';
      case 'information':
      case 'info':
        return 'bg-sky-500/15 text-sky-400 border-sky-500/30';
      case 'success':
        return 'bg-emerald-500/15 text-emerald-400 border-emerald-500/30';
      case 'warning':
        return 'bg-amber-400/15 text-amber-300 border-amber-400/30';
      case 'default':
      default:
        return 'bg-slate-800 text-slate-300 border-slate-700/60';
    }
  });
</script>

<template>
  <span
    :class="[
      badgeClass,
      type,
      'badge inline-flex items-center gap-1.5 text-[11px] font-bold px-2.5 py-0.5 rounded-full border tracking-wide uppercase shadow-sm select-none'
    ]"
  >
    <slot />
  </span>
</template>

<style scoped>
  /* Variantes Astryx retrocompatíveis */
</style>
