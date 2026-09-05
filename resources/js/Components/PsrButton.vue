<script setup lang="ts">
  import { computed } from 'vue';

  const props = withDefaults(
    defineProps<{
      type?: 'primary' | 'secondary' | 'link' | 'outline' | 'ghost' | 'danger';
      variant?: 'primary' | 'secondary' | 'link' | 'outline' | 'ghost' | 'danger';
      size?: 'sm' | 'md' | 'lg';
      disabled?: boolean;
    }>(),
    {
      type: undefined,
      variant: undefined,
      size: 'md',
      disabled: false,
    }
  );

  const resolvedVariant = computed(() => props.variant ?? props.type ?? 'primary');

  const sizeClasses = computed(() => {
    switch (props.size) {
      case 'sm':
        return 'h-[var(--astryx-size-element-sm)] px-3 text-xs gap-1.5';
      case 'lg':
        return 'h-[var(--astryx-size-element-lg)] px-6 text-sm gap-2.5 font-bold';
      case 'md':
      default:
        return 'h-[var(--astryx-size-element-md)] px-4 text-xs sm:text-sm gap-2 font-semibold';
    }
  });

  const variantClasses = computed(() => {
    switch (resolvedVariant.value) {
      case 'secondary':
        return 'bg-astryx-card text-astryx-text-primary border border-astryx-border-subtle hover:bg-astryx-card-hover hover:border-astryx-border shadow-astryx-low';
      case 'outline':
        return 'bg-transparent text-astryx-text-primary border border-astryx-border hover:bg-white/5 hover:border-amber-400/50';
      case 'ghost':
        return 'bg-transparent text-astryx-text-secondary hover:text-astryx-text-primary hover:bg-white/5';
      case 'danger':
        return 'bg-red-600 hover:bg-red-500 text-white shadow-astryx-low';
      case 'link':
        return 'bg-transparent text-amber-400 hover:text-amber-300 underline-offset-4 hover:underline p-0 h-auto font-normal';
      case 'primary':
      default:
        return 'bg-gradient-to-r from-amber-500 via-amber-400 to-amber-500 hover:from-amber-400 hover:to-amber-300 text-slate-950 font-black shadow-astryx-low hover:shadow-astryx-med';
    }
  });
</script>

<template>
  <button
    :class="[
      resolvedVariant,
      sizeClasses,
      variantClasses,
      'inline-flex items-center justify-center rounded-astryx-element transition-all duration-[var(--astryx-duration-fast)] ease-[var(--astryx-ease-standard)] select-none astryx-focus-ring',
      disabled ? 'opacity-50 cursor-not-allowed pointer-events-none' : 'active:scale-[0.97] cursor-pointer'
    ]"
    :disabled="disabled"
  >
    <slot />
  </button>
</template>

<style scoped>
  /* Suporte retrocompatível para classes directas se necessário */
</style>
