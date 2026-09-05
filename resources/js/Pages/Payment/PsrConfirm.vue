<script setup lang="ts">
  import { useClipboard } from '@vueuse/core';
  import PsrBadge from '@Components/PsrBadge.vue';
  import PsrCountdown from '@Components/PsrCountdown.vue';
  import { IconSvgPix } from '@Assets/icons';
  import { computed, ref } from 'vue';
  import { useLocaleCurrency } from '@Composables/Locale';
  import PsrThemeToggle from '@Components/PsrThemeToggle.vue';

  const props = defineProps<{
    payment: {
      id?: string;
      qr_code: string;
      qr_code_img?: string;
      ticket_url?: string;
      transaction_amount: number;
      date_of_expiration: string;
    };
    order?: {
      id: number;
      quantity: number;
      customer_fullname: string;
      customer_telephone: string;
      rifa?: {
        id: number;
        title: string;
        price: number;
        image: string;
        slug: string;
      } | null;
    } | null;
  }>();

  defineEmits<{
    (event: 'end'): void;
  }>();

  const copying = ref(false);
  const transactionAmount = useLocaleCurrency(props.payment.transaction_amount);
  const clipboard = useClipboard({ source: props.payment.qr_code });

  // Tratamento infalível do QR Code: URL externa, Base64 pré-formatado ou cru
  const qrCodeSrc = computed(() => {
    const raw = props.payment.qr_code_img;
    if (!raw) return '';
    if (raw.startsWith('http://') || raw.startsWith('https://') || raw.startsWith('data:')) {
      return raw;
    }
    return `data:image/png;base64,${raw}`;
  });

  function copyPix() {
    clipboard.copy(props.payment.qr_code);
    copying.value = true;
    setTimeout(() => {
      copying.value = false;
    }, 2500);
  }
</script>

<template>
  <div class="min-h-screen bg-astryx-body text-astryx-text-primary pb-28 pt-4 px-3 sm:px-6 transition-colors duration-200">
    <div class="max-w-xl mx-auto space-y-4">
      
      <!-- Top header bar com alternador de tema e home -->
      <div class="flex items-center justify-between px-1">
        <a :href="route('home')" class="text-xs font-bold text-amber-600 dark:text-amber-400 hover:underline flex items-center gap-1">
          ← Voltar para as Rifas
        </a>
        <PsrThemeToggle />
      </div>

      <!-- Card Superior: Header + Status + Cronômetro -->
      <div class="bg-astryx-card border border-astryx-border-subtle rounded-astryx-card p-4 sm:p-5 shadow-astryx-med relative overflow-hidden transition-colors">
        <div class="flex items-center justify-between gap-2 border-b border-astryx-border-subtle pb-3">
          <div class="flex items-center gap-2">
            <span class="flex h-3 w-3 relative">
              <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-500 opacity-75"></span>
              <span class="relative inline-flex rounded-full h-3 w-3 bg-emerald-500"></span>
            </span>
            <span class="text-xs font-black uppercase tracking-wider text-emerald-600 dark:text-emerald-400">
              Pagamento Pix Aberto
            </span>
          </div>
          <PsrBadge type="warning" class="!px-2.5 !py-1 !rounded-xl !text-xs !font-bold flex items-center gap-1">
            <span>⏱️</span>
            <PsrCountdown :time="payment.date_of_expiration" @end="$emit('end')" />
          </PsrBadge>
        </div>

        <!-- Info do Pedido & Rifa -->
        <div class="pt-3 flex items-center justify-between gap-3">
          <div class="min-w-0">
            <p v-if="order?.rifa?.title" class="text-xs text-amber-600 dark:text-amber-400 font-bold uppercase tracking-wider truncate">
              {{ order.rifa.title }}
            </p>
            <p v-else class="text-xs text-astryx-text-secondary font-medium">Kit Marola Rifas</p>
            <h1 class="text-lg sm:text-xl font-black text-astryx-text-primary truncate">
              {{ order?.quantity ? `${order.quantity} Cotas da Sorte` : 'Finalizar Participação' }}
            </h1>
          </div>
          <div class="text-right shrink-0">
            <span class="text-[11px] text-astryx-text-tertiary block font-medium">Valor total</span>
            <span class="text-2xl sm:text-3xl font-black text-emerald-600 dark:text-emerald-400 tracking-tight">
              {{ transactionAmount }}
            </span>
          </div>
        </div>
      </div>

      <!-- Super Ação Mobile: BOTÃO COPIA E COLA GIGANTE & DESTACADO -->
      <div class="bg-astryx-card border-2 border-emerald-500/40 rounded-astryx-card p-5 sm:p-6 shadow-astryx-med space-y-4 transition-colors">
        
        <div class="text-center space-y-1">
          <p class="text-xs font-bold text-astryx-text-primary uppercase tracking-wider">
            Passo Principal para Pagar no Celular:
          </p>
          <p class="text-[13px] text-astryx-text-secondary">
            Copie o código abaixo e abra o aplicativo do seu banco na opção <b>Pix Copia e Cola</b>.
          </p>
        </div>

        <!-- Botão Primário Magnético de Cópia -->
        <button
          type="button"
          @click="copyPix"
          class="w-full relative group overflow-hidden py-4 px-6 rounded-astryx-container font-black text-base uppercase tracking-wider transition-all duration-300 transform active:scale-95 shadow-xl flex items-center justify-center gap-3 cursor-pointer"
          :class="copying 
            ? 'bg-emerald-500 text-white ring-4 ring-emerald-400/40' 
            : 'bg-gradient-to-r from-emerald-500 via-teal-500 to-emerald-600 hover:from-emerald-400 hover:to-teal-400 text-white hover:shadow-emerald-500/30'"
        >
          <template v-if="!copying">
            <IconSvgPix class="w-5 h-5 fill-current" />
            <span>Copiar Código Pix</span>
            <span class="bg-black/15 text-white text-xs px-2 py-0.5 rounded-md font-bold ml-1">
              Copiar
            </span>
          </template>
          <template v-else>
            <span class="text-lg">✅</span>
            <span>Código Pix Copiado com Sucesso!</span>
          </template>
        </button>

        <!-- Preview do Código com Botão de Copiar Secundário -->
        <div class="relative flex items-center bg-astryx-muted border border-astryx-border rounded-astryx-element overflow-hidden">
          <input
            type="text"
            readonly
            :value="payment.qr_code"
            class="w-full bg-transparent px-3.5 py-2.5 text-xs text-astryx-text-secondary font-mono focus:outline-none select-all truncate pr-16"
          />
          <button
            type="button"
            @click="copyPix"
            class="absolute right-1 text-[11px] font-bold px-2.5 py-1 bg-astryx-surface border border-astryx-border-subtle hover:bg-astryx-card-hover text-emerald-600 dark:text-emerald-400 rounded-lg transition-colors shadow-sm"
          >
            {{ copying ? 'Copiado!' : 'Copiar' }}
          </button>
        </div>

        <!-- Feedback de segurança e liberação automática -->
        <div class="flex items-center justify-center gap-2 text-xs text-emerald-600 dark:text-emerald-400 bg-emerald-500/10 border border-emerald-500/20 py-2 px-3 rounded-astryx-element font-medium">
          <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
          <span>Aprovação 100% automática em segundos após o pagamento!</span>
        </div>
      </div>

      <!-- Card QR Code & Diagramação 3 Passos Simples -->
      <div class="bg-astryx-card border border-astryx-border-subtle rounded-astryx-card p-5 sm:p-6 shadow-astryx-med space-y-5 transition-colors">
        
        <!-- Seção QR Code para Pagar por outro Aparelho -->
        <div class="flex flex-col items-center justify-center text-center space-y-3 pt-1">
          <span class="text-xs font-bold text-astryx-text-secondary uppercase tracking-wider">
            Ou pague escaneando o QR Code pelo Computador/Tablet:
          </span>

          <div v-if="qrCodeSrc" class="relative group bg-white p-3 rounded-2xl border-4 border-slate-200 dark:border-slate-800 shadow-xl inline-block transition-transform hover:scale-[1.02]">
            <img
              :src="qrCodeSrc"
              alt="QR Code Pix"
              class="w-48 h-48 sm:w-56 sm:h-56 object-contain rounded-lg"
            />
            <div class="absolute inset-0 flex items-center justify-center bg-black/60 text-white rounded-xl opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none text-xs font-bold">
              Aponte a câmera do seu banco
            </div>
          </div>
          <div v-else class="text-center py-6 text-astryx-text-tertiary text-xs">
            Gerando QR Code Pix...
          </div>
          <span class="text-[11px] text-astryx-text-tertiary font-medium">
            Abra o app do seu banco ➔ Pix ➔ Ler QR Code
          </span>
        </div>

        <!-- Passo a Passo Ilustrado e Intuitivo -->
        <div class="border-t border-astryx-border-subtle pt-4 space-y-3">
          <h3 class="text-xs font-bold text-astryx-text-primary uppercase tracking-wider">
            Como pagar em 3 passos simples:
          </h3>
          <div class="grid grid-cols-1 sm:grid-cols-3 gap-2.5 text-xs">
            <div class="bg-astryx-surface border border-astryx-border-subtle p-3 rounded-astryx-element space-y-1">
              <div class="w-6 h-6 rounded-full bg-amber-500/20 border border-amber-500/30 text-amber-600 dark:text-amber-400 font-black flex items-center justify-center text-xs">
                1
              </div>
              <p class="font-bold text-astryx-text-primary">Copie o código</p>
              <p class="text-astryx-text-secondary text-[11px]">Clique no botão verde acima "Copiar Código Pix".</p>
            </div>

            <div class="bg-astryx-surface border border-astryx-border-subtle p-3 rounded-astryx-element space-y-1">
              <div class="w-6 h-6 rounded-full bg-amber-500/20 border border-amber-500/30 text-amber-600 dark:text-amber-400 font-black flex items-center justify-center text-xs">
                2
              </div>
              <p class="font-bold text-astryx-text-primary">Cole no seu banco</p>
              <p class="text-astryx-text-secondary text-[11px]">No app do seu banco, escolha "Pix Copia e Cola" e cole.</p>
            </div>

            <div class="bg-astryx-surface border border-astryx-border-subtle p-3 rounded-astryx-element space-y-1">
              <div class="w-6 h-6 rounded-full bg-emerald-500/20 border border-emerald-500/30 text-emerald-600 dark:text-emerald-400 font-black flex items-center justify-center text-xs">
                3
              </div>
              <p class="font-bold text-astryx-text-primary">Pronto!</p>
              <p class="text-astryx-text-secondary text-[11px]">O sistema reconhece na hora e envia os números no seu WhatsApp.</p>
            </div>
          </div>
        </div>

        <!-- Rodapé do Card com Status de Verificação em Tempo Real -->
        <div class="pt-2 text-center">
          <div class="inline-flex items-center gap-2 bg-astryx-surface border border-astryx-border-subtle px-4 py-2 rounded-xl text-xs text-astryx-text-secondary">
            <span class="relative flex h-2 w-2">
              <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-500 opacity-75"></span>
              <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
            </span>
            <span>Aguardando confirmação bancária... Não precisa atualizar a página.</span>
          </div>
          <p class="text-[11px] text-astryx-text-tertiary mt-2">
            🔒 Pagamento criptografado e seguro processado via Banco Central / Woovi Pix
          </p>
        </div>

      </div>

    </div>
  </div>
</template>
