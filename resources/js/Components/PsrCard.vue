<template>
  <div class="border border-astryx-border-subtle bg-astryx-card p-4 sm:p-6 rounded-astryx-card shadow-astryx-low transition-all duration-[var(--astryx-duration-fast)] ease-[var(--astryx-ease-standard)] space-y-4 text-astryx-text-primary">
    <p v-if="$slots.heading" class="uppercase text-center font-bold font-[Raleway] text-xs sm:text-sm tracking-wider text-amber-400">
      <slot name="heading" />
    </p>
    <slot />
  </div>
</template>
