<script setup lang="ts">
  import { readonly } from 'vue';
  import PsrBadge from './PsrBadge.vue';
  import PsrCountdown from './PsrCountdown.vue';
  import { computed } from 'vue';

  const props = defineProps<{
    orders: OrderWithPayment[];
  }>();

  const statuses = readonly<OrderStatuses>({
    expired: 'Expirado',
    paid: 'Pago',
    reserved: 'Reservado',
    unknown: 'Desconhecido',
  });

  const allOrders = computed(() =>
    props.orders.map((order) => ({
      ...order,
      paymentLink: order.payment ? route('payment.show', [order.payment.id]) : route('orders.show', [order.id]),
    }))
  );
</script>

<template>
  <div class="mt-4 space-y-4">
    <div
      v-for="order in allOrders"
      :key="`number-${order.id}`"
      class="bg-astryx-card border border-astryx-border-subtle gap-2 p-4 rounded-astryx-element shadow-astryx-low space-y-3 text-start hover:bg-astryx-card-hover transition-colors"
    >
      <div class="flex items-center justify-between text-xs">
        <span class="text-astryx-text-secondary font-medium">Pedido #{{ order.id }}</span>
        <PsrBadge :type="order.status === 'paid' ? 'success' : (order.status === 'reserved' ? 'warning' : 'danger')">
          {{ statuses[order.status] ?? statuses.unknown }}
        </PsrBadge>
      </div>

      <div v-if="order.status === 'paid' && order.numbers_reserved && order.numbers_reserved.length" class="flex flex-wrap justify-start gap-2">
        <PsrBadge
          v-for="(number, idx) in order.numbers_reserved"
          :key="`number-${idx}`"
          type="success"
        >
          {{ number }}
        </PsrBadge>
      </div>
      <div v-else-if="order.status === 'reserved'" class="text-xs text-amber-700 dark:text-amber-300 bg-amber-500/10 border border-amber-500/30 p-2.5 rounded-lg">
        🔒 <b>Aguardando pagamento Pix</b>. Seus números da sorte serão gerados e exibidos aqui assim que o Pix for aprovado.
      </div>

      <p v-if="order.status === 'reserved' && order.expire_at" class="text-xs text-astryx-text-tertiary">
        <span>Expira em: </span>
        <PsrCountdown :time="order.expire_at" />
      </p>

      <p v-if="order.status === 'reserved'" class="text-center pt-1">
        <a
          :href="order.paymentLink"
          class="inline-flex items-center justify-center gap-1.5 w-full py-2.5 px-4 bg-emerald-600 hover:bg-emerald-500 text-white font-bold rounded-xl text-xs transition-colors shadow-sm"
        >
          <span>❖</span> Pagar Pix Agora
        </a>
      </p>
    </div>
  </div>
</template>
