<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import axios from 'axios';
import { vMaska } from 'maska/vue';
import { useTelephone } from '../Composables/Validators';
import ListOrders from './ListOrders.vue';
import PsrLoading from './PsrLoading.vue';

const isOrdersModalOpen = ref(false);
const telephone = ref('');
const orders = ref<any[]>([]);
const isLoading = ref(false);
const searched = ref(false);

const isValidTelephone = computed(() => useTelephone(telephone.value));

async function searchOrders() {
  if (!isValidTelephone.value) return;
  isLoading.value = true;
  searched.value = true;
  const telephoneRaw = telephone.value.replace(/\D/g, '');

  try {
    const { data: response } = await axios.get(
      route('rifas.show.orders', ['bone-raw-original-case-completo-com-cuia-tesoura-seda-e-porta-beck', telephoneRaw])
    );
    orders.value = response.data || [];
  } catch (e) {
    orders.value = [];
  } finally {
    isLoading.value = false;
  }
}

function closeModal() {
  isOrdersModalOpen.value = false;
  orders.value = [];
  telephone.value = '';
  searched.value = false;
}
</script>

<template>
  <div>
    <!-- Barra de Navegação Inferior Fixa (Estilo App Nativo) -->
    <nav class="md:hidden fixed bottom-0 left-0 right-0 z-40 bg-astryx-body/95 backdrop-blur-xl border-t border-astryx-border-subtle px-2 py-1.5 safe-area-pb shadow-astryx-high">
      <div class="grid grid-cols-4 items-center justify-around text-center">
        <!-- Início -->
        <Link
          :href="route('home')"
          class="flex flex-col items-center justify-center py-1 text-astryx-text-secondary hover:text-amber-400 active:scale-95 transition-all duration-[var(--astryx-duration-fast)]"
        >
          <svg class="w-5 h-5 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
          </svg>
          <span class="text-[10px] font-medium tracking-tight">Início</span>
        </Link>

        <!-- Meus Bilhetes -->
        <button
          type="button"
          @click="isOrdersModalOpen = true"
          class="flex flex-col items-center justify-center py-1 text-astryx-text-secondary hover:text-amber-400 active:scale-95 transition-all duration-[var(--astryx-duration-fast)]"
        >
          <svg class="w-5 h-5 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z" />
          </svg>
          <span class="text-[10px] font-medium tracking-tight">Meus Números</span>
        </button>

        <!-- Ganhadores -->
        <a
          :href="route('home') + '#ranking'"
          class="flex flex-col items-center justify-center py-1 text-astryx-text-secondary hover:text-amber-400 active:scale-95 transition-all duration-[var(--astryx-duration-fast)]"
        >
          <svg class="w-5 h-5 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />
          </svg>
          <span class="text-[10px] font-medium tracking-tight">Ganhadores</span>
        </a>

        <!-- WhatsApp Suporte VIP -->
        <a
          href="https://wa.me/5511980948484?text=Ol%C3%A1!%20Gostaria%20de%20tirar%20d%C3%BAvidas%20sobre%20as%20rifas%20do%20Kit%20Marola"
          target="_blank"
          rel="noopener"
          class="flex flex-col items-center justify-center py-1 text-emerald-400 hover:text-emerald-300 active:scale-95 transition-all duration-[var(--astryx-duration-fast)]"
        >
          <svg class="w-5 h-5 mb-1" fill="currentColor" viewBox="0 0 24 24">
            <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/>
          </svg>
          <span class="text-[10px] font-medium tracking-tight">WhatsApp</span>
        </a>
      </div>
    </nav>

    <!-- Modal Mobile de Meus Bilhetes -->
    <Teleport to="body">
      <div v-if="isOrdersModalOpen" class="fixed inset-0 z-50 flex items-end sm:items-center justify-center p-0 sm:p-4 bg-black/80 backdrop-blur-sm transition-all duration-[var(--astryx-duration-fast)]">
        <!-- Backdrop -->
        <div class="fixed inset-0" @click="closeModal"></div>

        <!-- Bottom Sheet Dialog -->
        <div class="relative w-full sm:max-w-md bg-astryx-surface border border-astryx-border-subtle rounded-t-astryx-page sm:rounded-astryx-modal p-5 shadow-astryx-high z-10 max-h-[85vh] overflow-y-auto">
          <!-- Puxador Mobile -->
          <div class="w-12 h-1.5 bg-astryx-border-strong rounded-full mx-auto mb-4 sm:hidden"></div>

          <div class="flex items-center justify-between mb-4">
            <h3 class="text-base font-bold text-astryx-text-primary flex items-center gap-2">
              <span>🎟️</span>
              <span>Consultar Meus Números</span>
            </h3>
            <button @click="closeModal" class="text-astryx-text-secondary hover:text-astryx-text-primary p-1 rounded-full bg-astryx-muted">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
              </svg>
            </button>
          </div>

          <div v-if="!searched || orders.length === 0" class="space-y-4">
            <p class="text-xs text-astryx-text-secondary">
              Digite o número do WhatsApp informado no momento da compra para ver seus bilhetes e números da sorte:
            </p>

            <div class="space-y-2">
              <div class="relative">
                <input
                  v-model="telephone"
                  v-maska
                  type="tel"
                  placeholder="(11) 98888-8888"
                  data-maska="(##) #####-####"
                  class="w-full bg-astryx-body border border-astryx-border rounded-astryx-element px-4 py-3 text-astryx-text-primary text-sm focus:outline-none focus:border-amber-400 focus:ring-1 focus:ring-amber-400 transition-all duration-[var(--astryx-duration-fast)]"
                  @keyup.enter="searchOrders"
                />
              </div>

              <button
                type="button"
                :disabled="!isValidTelephone || isLoading"
                @click="searchOrders"
                class="w-full h-[var(--astryx-size-element-lg)] px-4 bg-gradient-to-r from-amber-500 to-amber-400 hover:from-amber-400 hover:to-amber-300 disabled:opacity-50 text-slate-950 font-black rounded-astryx-element text-sm transition-all duration-[var(--astryx-duration-fast)] ease-[var(--astryx-ease-standard)] active:scale-[0.98] shadow-astryx-low flex items-center justify-center gap-2"
              >
                <span v-if="isLoading">Buscando...</span>
                <span v-else>🔍 Ver Meus Bilhetes</span>
              </button>
            </div>

            <div v-if="searched && orders.length === 0 && !isLoading" class="p-3 bg-astryx-body border border-astryx-border-subtle rounded-astryx-element text-center">
              <p class="text-xs text-astryx-text-secondary">Nenhum pedido encontrado para este telefone.</p>
            </div>
          </div>

          <!-- Loading -->
          <div v-if="isLoading" class="py-8 flex justify-center">
            <PsrLoading />
          </div>

          <!-- Lista de Pedidos -->
          <div v-else-if="orders.length > 0" class="space-y-3 mt-2">
            <div v-for="order in orders" :key="order.id" class="p-3.5 bg-zinc-950 border border-zinc-800/80 rounded-xl space-y-2">
              <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-amber-400">Pedido #{{ order.id }}</span>
                <span
                  class="text-[10px] font-bold px-2 py-0.5 rounded-full"
                  :class="order.status === 'paid' ? 'bg-emerald-500/20 text-emerald-400 border border-emerald-500/30' : 'bg-amber-500/20 text-amber-400 border border-amber-500/30'"
                >
                  {{ order.status === 'paid' ? 'PAGO' : 'AGUARDANDO PIX' }}
                </span>
              </div>

              <div v-if="order.numbers_reserved && order.numbers_reserved.length" class="space-y-1">
                <span class="text-[11px] text-zinc-400">Seus Números da Sorte:</span>
                <div class="flex flex-wrap gap-1.5 max-h-32 overflow-y-auto pt-1">
                  <span
                    v-for="num in order.numbers_reserved"
                    :key="num"
                    class="px-2 py-1 bg-amber-500/10 border border-amber-500/30 text-amber-300 font-mono font-bold text-xs rounded-lg"
                  >
                    #{{ num }}
                  </span>
                </div>
              </div>
              <div v-else class="text-xs text-zinc-500 italic">
                Os números serão gerados assim que o Pix for aprovado.
              </div>
            </div>

            <button
              @click="orders = []; searched = false;"
              class="w-full py-2.5 text-xs text-zinc-400 hover:text-white transition-all text-center"
            >
              ← Buscar outro telefone
            </button>
          </div>
        </div>
      </div>
    </Teleport>
  </div>
</template>

<style scoped>
.safe-area-pb {
  padding-bottom: max(0.5rem, env(safe-area-inset-bottom));
}
</style>
