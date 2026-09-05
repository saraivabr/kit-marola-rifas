<script setup lang="ts">
  import { ref } from 'vue';
  import { onClickOutside } from '@vueuse/core';
  import PsrButton from './PsrButton.vue';

  withDefaults(
    defineProps<{
      buttonCancel?: boolean;
      buttonConfirm?: boolean;
      buttonConfirmDisabled?: boolean;
    }>(),
    {
      buttonCancel: true,
      buttonConfirm: true,
      buttonConfirmDisabled: false,
    }
  );

  const emits = defineEmits<{
    (event: 'dismiss'): void;
    (event: 'confirm'): void;
  }>();

  const modalContent = ref();

  onClickOutside(modalContent, () => {
    emits('dismiss');
  });
</script>

<template>
  <div
    class="fixed h-screen left-0 top-0 w-screen z-50"
    role="dialog"
    aria-modal="true"
    aria-labelledby="modal-heading"
    aria-describedby="modal-content"
  >
    <div class="fixed inset-0 bg-black/60 dark:bg-black/80 backdrop-blur-sm transition-opacity" data-testid="modal-overlay" />

    <div ref="modalContent" class="top-0 overflow-y-auto">
      <div class="flex min-h-screen justify-center p-4 text-center items-center sm:p-0">
        <div
          class="relative overflow-hidden rounded-astryx-modal bg-astryx-card border border-astryx-border-subtle text-left shadow-astryx-high sm:my-8 sm:w-full sm:max-w-lg dialog-content transition-colors duration-200"
        >
          <div class="p-5 sm:p-8 text-center overflow-y-auto">
            <h3 id="modal-heading" class="mb-4 text-xl sm:text-2xl font-black font-[Raleway] text-astryx-text-primary">
              <slot name="heading" />
            </h3>
            <div id="modal-content" class="text-astryx-text-secondary"><slot /></div>

            <slot v-if="buttonCancel || buttonConfirm" name="footer">
              <div class="mt-6 flex justify-end gap-x-3">
                <PsrButton v-if="buttonCancel" variant="outline" data-testid="button-cancel" @click="$emit('dismiss')">
                  Cancelar
                </PsrButton>

                <PsrButton
                  v-if="buttonConfirm"
                  data-testid="button-confirm"
                  :disabled="buttonConfirmDisabled"
                  @click="$emit('confirm')"
                >
                  Confirmar
                </PsrButton>
              </div>
            </slot>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
  .dialog-content {
    max-height: calc(100vh - 4rem);
    overflow: auto;
  }
</style>
