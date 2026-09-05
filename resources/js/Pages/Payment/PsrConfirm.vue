<script setup lang="ts">
  import { useClipboard } from '@vueuse/core';
  import PsrBadge from '@Components/PsrBadge.vue';
  import PsrButton from '@Components/PsrButton.vue';
  import PsrCard from '@Components/PsrCard.vue';
  import { IconSvgPix } from '@Assets/icons';
  import { watch } from 'vue';
  import { ref } from 'vue';
  import { useLocaleCurrency } from '@Composables/Locale';
  import PsrCountdown from '@Components/PsrCountdown.vue';

  const props = defineProps<{
    payment: {
      qr_code: string;
      qr_code_img: string;
      ticket_url: string;
      transaction_amount: number;
      date_of_expiration: string;
    };
  }>();

  defineEmits<{
    (event: 'end'): void;
  }>();

  const copying = ref(false);
  const transactionAmount = useLocaleCurrency(props.payment.transaction_amount);

  const clipboard = useClipboard({ source: props.payment.qr_code });

  watch(clipboard.copied, () => {
    copying.value = true;

    setTimeout(() => (copying.value = false), 1500);
  });
</script>

<template>
  <div class="container max-w-[860px] mx-auto space-y-6 pt-6 pb-28 px-4">
    <div class="flex flex-wrap items-center justify-between gap-3 bg-slate-900 border border-slate-800 p-4 sm:p-5 rounded-3xl shadow-xl" data-testid="heading">
      <div>
        <span class="text-[11px] font-black text-emerald-400 uppercase tracking-widest bg-emerald-500/10 px-2.5 py-0.5 rounded-full border border-emerald-500/20">
          Pagamento Instantâneo
        </span>
        <h1 class="text-xl sm:text-2xl font-black text-white mt-1">Finalize seu Pix</h1>
      </div>
      <PsrBadge type="warning" class="!bg-amber-500/15 !text-amber-300 !border-amber-500/30 !px-3 !py-1.5 !rounded-xl !text-xs !font-bold">
        ⏳ Pague até:
        <PsrCountdown :time="payment.date_of_expiration" @end="$emit('end')" />
      </PsrBadge>
    </div>

    <div class="bg-slate-900 border border-slate-800 rounded-3xl p-6 sm:p-8 shadow-2xl space-y-6">
      <div class="text-center sm:text-start border-b border-slate-800/80 pb-5">
        <span class="text-xs text-gray-400 block font-medium">Valor exato do seu Pix:</span>
        <span class="text-3xl sm:text-4xl font-black text-transparent bg-clip-text bg-gradient-to-r from-emerald-400 to-teal-300 tracking-tight">
          {{ transactionAmount }}
        </span>
        <p class="text-xs text-gray-300 mt-1 flex items-center justify-center sm:justify-start gap-1.5">
          <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
          Aprovação imediata com números gerados e enviados no WhatsApp na hora.
        </p>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-8 items-center">
        <!-- QR Code -->
        <div class="flex flex-col items-center bg-slate-950 p-6 rounded-3xl border border-slate-800/80 shadow-inner">
          <img
            :src="payment.qr_code_img.startsWith('data:') ? payment.qr_code_img : `data:image/png;base64,${payment.qr_code_img}`"
            :alt="payment.qr_code"
            class="w-60 h-60 rounded-2xl shadow-xl border-4 border-slate-800 p-2 bg-white"
          />
          <span class="text-[11px] text-gray-400 mt-3 font-medium flex items-center gap-1">
            📱 Aponte a câmera do app do seu banco
          </span>
        </div>

        <!-- Instruções e Botão Copia e Cola -->
        <div class="space-y-5">
          <div class="space-y-2">
            <label class="text-xs font-bold text-gray-300 block">
              Pix Copia e Cola (código para colar no banco):
            </label>
            <div class="relative">
              <input
                type="text"
                readonly
                :value="payment.qr_code"
                class="w-full bg-slate-950 border border-slate-800 rounded-2xl px-3.5 py-3 text-xs text-gray-300 font-mono focus:outline-none select-all"
              />
            </div>
            <PsrButton
              class="w-full !bg-gradient-to-r !from-emerald-500 !to-teal-500 hover:!from-emerald-400 hover:!to-teal-400 !text-slate-950 !font-black !py-3.5 !rounded-2xl !shadow-lg !transition-all active:scale-95 flex items-center justify-center gap-2 text-sm uppercase tracking-wider"
              @click="clipboard.copy()"
            >
              <span v-if="!copying" class="flex items-center gap-2">
                <IconSvgPix class="inline w-4 h-4" /> Copiar Código Pix
              </span>
              <span v-else class="flex items-center gap-2 font-bold text-slate-950">
                ✔ Código Pix Copiado com Sucesso!
              </span>
            </PsrButton>
          </div>

          <ol class="space-y-3 steps text-xs text-gray-300 pt-2">
            <li data-step="1">Abra o app do seu banco e acesse a área <b>Pix</b></li>
            <li data-step="2">Selecione <b>Pix Copia e Cola</b> ou <b>Ler QR Code</b></li>
            <li data-step="3">Cole o código ou aponte a câmera para a imagem</li>
            <li data-step="4">Confirme o pagamento de <b>{{ transactionAmount }}</b></li>
          </ol>
        </div>
      </div>

      <div class="pt-6 border-t border-slate-800/80 text-center space-y-2">
        <div class="inline-flex items-center gap-2 bg-slate-950 px-4 py-2 rounded-xl border border-slate-800 text-[11px] text-gray-400">
          <span class="w-2 h-2 rounded-full bg-emerald-400 animate-ping"></span>
          <span>Aguardando pagamento... Não feche esta tela</span>
        </div>

        <p class="text-[11px] text-gray-500 pt-2">
          Ambiente criptografado e seguro | Pagamento processado instantaneamente
        </p>
      </div>
    </div>
  </div>
</template>

<style scoped>
  .steps > li {
    @apply ml-9 relative;
  }
  .steps > li::before {
    @apply bg-amber-500 text-slate-950 font-black text-xs h-6 w-6 rounded-full inline-flex items-center justify-center absolute -left-9 top-0 shadow-sm;
    content: attr(data-step);
  }
</style>
