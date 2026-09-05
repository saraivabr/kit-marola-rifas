<script setup lang="ts">
  import { ref, computed } from 'vue';
  import { useForm } from '@inertiajs/vue3';
  import { useLocaleCurrency, useLocaleDateLong, useLocaleTelephone } from '@Composables/Locale';
  import PsrBadge from '@Components/PsrBadge.vue';
  import PsrCard from '@Components/PsrCard.vue';
  import PsrCountdown from '@Components/PsrCountdown.vue';
  import PsrButton from '@Components/PsrButton.vue';
  import PsrDialog from '@Components/PsrDialog.vue';

  const props = defineProps<{
    order: Order & { transaction_amount: number };
    rifa: Partial<Rifa>;
  }>();

  const hasError = ref<boolean>(false);
  const showNumbers = ref(false);
  const expireAt = computed(() => (props.order.expire_at ? useLocaleDateLong(props.order.expire_at) : ''));
  const transactionAmount = computed(() => useLocaleCurrency(props.order.transaction_amount));
  const telephone = useLocaleTelephone(props.order.customer_telephone);

  const expired = ref(false);

  const isPaid = ref(false);
  const checkIsPaid = computed(() => isPaid.value || props.order.status === 'paid');

  const form = useForm({
    orderId: props.order.id,
  });

  function countdownEnd() {
    location = route('rifas.show', [props.rifa.slug]);
  }

  function confirmOrder() {
    form.post(route('payment.store'), {
      onError: (errors) => {
        hasError.value = !!(errors?.warning ?? false);
      },
    });
  }
</script>

<template>
  <div class="container max-w-[558px] space-y-4 pt-10 pb-32 px-4">
    <div class="mb-4" data-testid="heading">
      <h1 class="text-2xl font-extrabold font-[Raleway] leading-none">Resumo do Pedido</h1>
      <PsrBadge v-if="!checkIsPaid && order.expire_at" type="warning" data-testid="expire">
        Expira em: {{ expireAt }}
      </PsrBadge>
    </div>

    <PsrDialog v-if="hasError" :button-confirm="false" @dismiss="hasError = false">
      Não foi possível finalizar o pedido. Entre em contato com o administrador do site.
    </PsrDialog>

    <PsrCard>
      <div class="!mt-0 space-y-3" role="table">
        <div role="row">
          <p role="cell">
            <span role="cell">Rifa: </span>
            <b role="cell">{{ rifa.title }}</b>
          </p>
        </div>
        <hr />
        <div role="row">
          <p role="cell">
            <span role="cell">Nome: </span>
            <b role="cell">{{ order.customer_fullname || order.customer_email }}</b>
          </p>
        </div>
        <hr />
        <div role="row">
          <p role="cell">
            <span role="cell">Telefone: </span>
            <b role="cell">{{ telephone }}</b>
          </p>
        </div>
        <hr />
        <div v-if="order.customer_instagram" role="row">
          <p role="cell">
            <span role="cell">Instagram: </span>
            <b role="cell" class="text-amber-500">{{ order.customer_instagram.startsWith('@') ? order.customer_instagram : `@${order.customer_instagram}` }}</b>
          </p>
        </div>
        <hr v-if="order.customer_instagram" />
        <div role="row">
          <p role="cell">
            <span role="cell">Email: </span>
            <b role="cell">{{ order.customer_email }}</b>
          </p>
        </div>
        <hr />
        <div role="row">
          <p role="cell">
            <span role="cell">Quantidade de cotas: </span>
            <b role="cell">{{ order.quantity || (order.numbers_reserved ? order.numbers_reserved.length : 1) }}</b>
          </p>
        </div>
        <hr />
        <div role="row">
          <p role="cell">
            <span role="cell">Valor total: </span>
            <b role="cell" class="text-amber-400 font-black text-xl">{{ transactionAmount }}</b>
          </p>
        </div>
      </div>

      <div class="!mt-6 p-4 rounded-2xl bg-amber-500/10 border border-amber-500/30 text-center">
        <p class="font-bold text-amber-300 text-sm flex items-center justify-center gap-1.5 mb-1">
          <span>🔒</span> Números gerados após o pagamento
        </p>
        <p class="text-xs text-gray-300 leading-relaxed">
          Seus números da sorte serão gerados e revelados de forma 100% transparente logo após a confirmação do Pix, e enviados também no seu WhatsApp.
        </p>
      </div>
    </PsrCard>
  </div>

  <div class="fixed bottom-0 left-0 w-screen !m-0 p-4 space-y-2 bg-slate-950/95 border-t border-slate-800 backdrop-blur-md text-center z-40">
    <p class="text-xs text-center text-gray-300">
      <span>Pague em até: </span>
      <PsrCountdown
        v-if="!checkIsPaid"
        class="inline-block text-amber-400 font-bold"
        data-testid="countdown"
        :time="order.expire_at"
        @end="countdownEnd"
      />
    </p>

    <PsrButton
      v-if="!checkIsPaid"
      class="w-full max-w-[558px] !bg-gradient-to-r !from-amber-500 !via-amber-400 !to-amber-500 hover:!from-amber-400 hover:!to-amber-300 !text-slate-950 !font-black !py-3.5 !rounded-2xl !shadow-lg active:scale-95 text-base uppercase tracking-wider"
      data-testid="button-payment"
      :disabled="expired || !!form.processing"
      @click="confirmOrder"
    >
      <span v-if="form.processing" class="text-slate-950">Aguarde</span>
      <span v-else class="text-slate-950 flex items-center justify-center gap-2">
        {{ expired ? 'Expirado' : 'Avançar para o Pix 👉' }}
      </span>
    </PsrButton>
  </div>
</template>

<style scoped>
  b {
    @apply font-semibold;
  }

  .fixed {
    box-shadow:
      rgba(0, 0, 0, 0.25) 0px 54px 55px,
      rgba(0, 0, 0, 0.12) 0px -12px 30px,
      rgba(0, 0, 0, 0.12) 0px 4px 6px,
      rgba(0, 0, 0, 0.17) 0px 12px 13px,
      rgba(0, 0, 0, 0.09) 0px -3px 5px;
  }
</style>
