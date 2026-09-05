<script setup lang="ts">
  import { ref, watch, onMounted } from 'vue';
  import { vMaska } from 'maska/vue';
  import { useForm } from '@inertiajs/vue3';
  import axios from 'axios';
  import { IconSvgEmail, IconSvgPeople, IconSvgTelephone } from '@Assets/icons';
  import PsrDialog from './PsrDialog.vue';
  import PsrBadge from './PsrBadge.vue';

  defineEmits<{
    (event: 'confirm', value: Partial<FormReserveNumbers>): Promise<void>;
    (event: 'dismiss'): void;
  }>();

  const props = defineProps<{
    rifa: Rifa['id'];
    quantity: FormReserveNumbers['quantity'];
  }>();

  const form = useForm<FormReserveNumbers>({
    confirmTelephone: '',
    email: '',
    fullname: '',
    instagram: '',
    quantity: props.quantity,
    rifa: props.rifa,
    telephone: '',
    terms: true,
  });

  const isSearching = ref(false);
  const isCustomerFound = ref(false);
  const showManualFields = ref(false);
  let lookupTimeout: any = null;

  async function checkCustomerPhone(phoneStr: string) {
    const clean = (phoneStr || '').replace(/\D/g, '');
    if (clean.length < 10) {
      isCustomerFound.value = false;
      return;
    }

    isSearching.value = true;
    try {
      const { data } = await axios.get(route('customers.lookup'), {
        params: { telephone: clean },
      });

      if (data && data.found && data.customer) {
        form.fullname = data.customer.fullname || form.fullname;
        form.email = data.customer.email || form.email;
        form.instagram = data.customer.instagram || form.instagram || '@cliente';
        form.confirmTelephone = form.telephone;
        form.terms = true;
        isCustomerFound.value = true;
        showManualFields.value = false;

        // Persiste no aparelho do cliente para compras futuras imediatas
        try {
          localStorage.setItem('kitmarola_last_phone', form.telephone);
          localStorage.setItem('kitmarola_customer', JSON.stringify(data.customer));
        } catch (e) {
          // ignore
        }
      } else {
        isCustomerFound.value = false;
        showManualFields.value = true;
      }
    } catch (e) {
      isCustomerFound.value = false;
    } finally {
      isSearching.value = false;
    }
  }

  // Monitora digitação do telefone com debounce suave
  watch(
    () => form.telephone,
    (newVal) => {
      if (newVal) {
        form.confirmTelephone = newVal;
      }
      clearTimeout(lookupTimeout);
      const clean = (newVal || '').replace(/\D/g, '');
      if (clean.length >= 10) {
        lookupTimeout = setTimeout(() => {
          checkCustomerPhone(newVal);
        }, 350);
      } else {
        isCustomerFound.value = false;
      }
    }
  );

  onMounted(() => {
    try {
      const savedPhone = localStorage.getItem('kitmarola_last_phone');
      const savedCustomer = localStorage.getItem('kitmarola_customer');

      if (savedPhone) {
        form.telephone = savedPhone;
        form.confirmTelephone = savedPhone;

        if (savedCustomer) {
          const parsed = JSON.parse(savedCustomer);
          form.fullname = parsed.fullname || '';
          form.email = parsed.email || '';
          form.instagram = parsed.instagram || '';
          isCustomerFound.value = true;
          showManualFields.value = false;
        } else {
          checkCustomerPhone(savedPhone);
        }
      }
    } catch (e) {
      // ignore
    }
  });

  function submitForm() {
    // Sincroniza confirmTelephone caso não tenha sido preenchido manualmente
    if (!form.confirmTelephone && form.telephone) {
      form.confirmTelephone = form.telephone;
    }
    // Salva no localStorage para as próximas vezes
    try {
      localStorage.setItem('kitmarola_last_phone', form.telephone);
      if (form.fullname) {
        localStorage.setItem(
          'kitmarola_customer',
          JSON.stringify({
            fullname: form.fullname,
            email: form.email,
            telephone: form.telephone,
            instagram: form.instagram,
          })
        );
      }
    } catch (e) {
      // ignore
    }
    form.post(route('orders.store'));
  }
</script>

<template>
  <PsrDialog @confirm="submitForm" @dismiss="$emit('dismiss')">
    <template #heading>
      <div class="flex items-center justify-center gap-2">
        <span>🎟️</span>
        <span>Finalizar Reserva de Bilhetes</span>
      </div>
    </template>

    <template #default>
      <div class="space-y-3.5">
        <!-- 1. Campo Principal: Telefone / Celular (WhatsApp) -->
        <div class="text-start" :class="{ error: form.errors.telephone }">
          <label class="block text-xs font-bold text-gray-300 mb-1">
            📱 Seu Celular (WhatsApp)
          </label>
          <div class="rounded-astryx-element shadow-sm relative">
            <div class="flex left-0 absolute top-0 bottom-0 items-center pl-3 pointer-events-none">
              <IconSvgTelephone class="text-amber-400" height="22" width="22" />
            </div>

            <input
              v-model="form.telephone"
              v-maska
              type="tel"
              placeholder="(11) 98888-8888"
              data-testid="input-telephone"
              autocomplete="tel"
              aria-label="Informe seu telefone para contato"
              aria-errormessage="error-telephone"
              data-maska="(##) #####-####"
              required
              autofocus
              :aria-invalid="!!form.errors.telephone"
              class="bg-astryx-body border border-astryx-border text-white text-base font-semibold pl-10 pr-10 h-12 rounded-astryx-element w-full focus:outline-none focus:border-amber-400 focus:ring-1 focus:ring-amber-400"
              @blur="checkCustomerPhone(form.telephone)"
            />

            <!-- Indicador de busca ao vivo -->
            <div v-if="isSearching" class="absolute right-3 top-0 bottom-0 flex items-center">
              <span class="w-4 h-4 border-2 border-amber-400 border-t-transparent rounded-full animate-spin"></span>
            </div>
            <div v-else-if="isCustomerFound" class="absolute right-3 top-0 bottom-0 flex items-center text-emerald-400 text-lg">
              ✓
            </div>
          </div>

          <span v-if="form.errors.telephone" id="error-telephone" class="text-xs text-red-400 font-medium">Informe seu telefone com DDD</span>
          <p v-else class="text-[11px] text-gray-400 mt-1">
            Seus números da sorte e bilhetes serão enviados para este WhatsApp.
          </p>
        </div>

        <!-- 2. Card de Reconhecimento VIP se já for cliente cadastrado -->
        <div v-if="isCustomerFound" class="bg-emerald-500/10 border border-emerald-500/30 rounded-astryx-element p-3.5 flex items-center justify-between gap-3 text-start transition-all">
          <div class="flex items-center gap-2.5 min-w-0">
            <div class="w-9 h-9 rounded-full bg-emerald-500/20 border border-emerald-500/40 flex items-center justify-center text-emerald-400 font-black text-sm flex-shrink-0">
              ✓
            </div>
            <div class="min-w-0">
              <span class="inline-block text-[10px] font-black uppercase tracking-wider text-emerald-400 bg-emerald-500/20 px-2 py-0.5 rounded-full mb-0.5">
                Cliente VIP Reconhecido
              </span>
              <p class="text-sm font-black text-white truncate">{{ form.fullname }}</p>
              <p class="text-[11px] text-gray-400 truncate">{{ form.email }} <span v-if="form.instagram && form.instagram !== '@cliente'">• {{ form.instagram }}</span></p>
            </div>
          </div>

          <button
            type="button"
            @click="showManualFields = !showManualFields"
            class="text-xs text-amber-400 hover:text-amber-300 font-semibold underline flex-shrink-0 py-1 px-2"
          >
            {{ showManualFields ? 'Ocultar' : 'Alterar' }}
          </button>
        </div>

        <!-- 3. Mensagem para primeira compra (caso não cadastrado) -->
        <div v-if="!isCustomerFound && form.telephone && form.telephone.replace(/\D/g, '').length >= 10 && !isSearching" class="bg-amber-500/10 border border-amber-500/20 rounded-astryx-element p-2.5 text-xs text-amber-300 text-start flex items-center gap-2">
          <span>👋</span>
          <span>Primeira vez por aqui? Complete seus dados para registrar seus números:</span>
        </div>

        <!-- 4. Campos de Cadastro (Exibidos se novo cliente ou se clicou em Alterar) -->
        <div v-show="!isCustomerFound || showManualFields" class="space-y-3 pt-1">
          <!-- Nome Completo -->
          <div class="text-start" :class="{ error: form.errors.fullname }">
            <label class="block text-xs font-medium text-gray-300 mb-1">
              Nome Completo
            </label>
            <div class="rounded-astryx-element shadow-sm relative">
              <div class="flex left-0 absolute top-0 bottom-0 items-center pl-3 pointer-events-none">
                <IconSvgPeople class="text-gray-400" height="20" width="20" />
              </div>

              <input
                v-model="form.fullname"
                type="text"
                placeholder="Ex: João da Silva"
                data-testid="input-full-name"
                autocomplete="name"
                aria-label="Informe seu nome completo"
                required
                aria-errormessage="error-fullname"
                :aria-invalid="!!form.errors.fullname"
                class="bg-astryx-body border border-astryx-border text-white text-sm pl-10 h-11 rounded-astryx-element w-full focus:outline-none focus:border-amber-400"
              />
            </div>

            <span v-if="form.errors.fullname" id="error-fullname" class="text-xs text-red-400 font-medium">Informe seu nome e sobrenome</span>
          </div>

          <!-- E-mail -->
          <div class="text-start" :class="{ error: form.errors.email }">
            <label class="block text-xs font-medium text-gray-300 mb-1">
              E-mail para Comprovante
            </label>
            <div class="rounded-astryx-element shadow-sm relative">
              <div class="flex left-0 absolute top-0 bottom-0 items-center pl-3 pointer-events-none">
                <IconSvgEmail class="text-gray-400" height="20" width="20" />
              </div>

              <input
                v-model="form.email"
                type="email"
                placeholder="seuemail@exemplo.com"
                data-testid="input-email"
                autocomplete="email"
                aria-label="Informe seu e-mail de contato"
                aria-errormessage="error-email"
                required
                :aria-invalid="!!form.errors.email"
                class="bg-astryx-body border border-astryx-border text-white text-sm pl-10 h-11 rounded-astryx-element w-full focus:outline-none focus:border-amber-400"
              />
            </div>

            <span v-if="form.errors.email" id="error-email" class="text-xs text-red-400 font-medium">Informe um e-mail válido</span>
          </div>

          <!-- Confirmação de Telefone (sincronizada automaticamente) -->
          <div class="text-start" :class="{ error: form.errors.confirmTelephone }">
            <label class="block text-xs font-medium text-gray-300 mb-1">
              Confirmar Celular
            </label>
            <div class="rounded-astryx-element shadow-sm relative">
              <div class="flex left-0 absolute top-0 bottom-0 items-center pl-3 pointer-events-none">
                <IconSvgTelephone class="text-gray-400" height="20" width="20" />
              </div>

              <input
                v-model="form.confirmTelephone"
                v-maska
                type="tel"
                placeholder="Confirme seu telefone"
                autocomplete="off"
                data-testid="input-confirm-telephone"
                aria-label="Confirme seu telefone para contato"
                aria-errormessage="error-confirm-telephone"
                data-maska="(##) #####-####"
                required
                :aria-invalid="!!form.errors.confirmTelephone"
                class="bg-astryx-body border border-astryx-border text-white text-sm pl-10 h-11 rounded-astryx-element w-full focus:outline-none focus:border-amber-400"
              />
            </div>

            <span v-if="form.errors.confirmTelephone" id="error-confirm-telephone" class="text-xs text-red-400 font-medium">
              Confirme seu telefone
            </span>
          </div>

          <!-- Instagram (Opcional) -->
          <div class="text-start" :class="{ error: form.errors.instagram }">
            <label class="block text-xs font-medium text-gray-300 mb-1">
              Instagram <span class="text-gray-500 font-normal">(Opcional)</span>
            </label>
            <div class="rounded-astryx-element shadow-sm relative">
              <div class="flex left-0 absolute top-0 bottom-0 items-center pl-3 text-gray-400 font-bold text-sm pointer-events-none">
                @
              </div>

              <input
                v-model="form.instagram"
                type="text"
                placeholder="seu.perfil"
                autocomplete="off"
                data-testid="input-instagram"
                aria-label="Informe seu Instagram para ser notificado do sorteio"
                aria-errormessage="error-instagram"
                class="bg-astryx-body border border-astryx-border text-white text-sm pl-9 h-11 rounded-astryx-element w-full focus:outline-none focus:border-amber-400"
              />
            </div>

            <span v-if="form.errors.instagram" id="error-instagram" class="text-xs text-red-400 font-medium">
              Informe seu Instagram
            </span>
          </div>
        </div>

        <!-- 5. Termos e Condições -->
        <div class="relative flex gap-x-3 text-start pt-2" :class="{ error: form.errors.terms }">
          <div class="flex h-5 items-center">
            <input
              id="terms"
              v-model="form.terms"
              type="checkbox"
              data-testid="input-terms"
              class="h-4 w-4 rounded border-slate-700 bg-slate-900 text-amber-500 focus:ring-amber-500"
              aria-errormessage="error-terms"
              :aria-invalid="!!form.errors.terms"
            />
          </div>
          <div class="text-xs leading-5">
            <label class="text-gray-400" for="terms">
              Concordo com os
              <a
                class="text-amber-400 hover:underline"
                :href="route('terms')"
                target="_blank"
              >Termos</a> da campanha e autorizo envio dos números no WhatsApp.
            </label>

            <p v-if="form.errors.terms" id="error-terms" class="text-xs text-red-400 font-medium">
              É necessário concordar com os termos para prosseguir
            </p>
          </div>
        </div>
      </div>
    </template>
  </PsrDialog>
</template>

<style scoped>
  div.error input,
  div.error input::placeholder,
  div.error svg,
  div.error span,
  div.error p {
    border-color: #ef4444;
    color: #ef4444;
  }
</style>

