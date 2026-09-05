<script setup lang="ts">
import { ref, onMounted } from 'vue';

const showModal = ref(false);
const isUnderage = ref(false);

onMounted(() => {
  const verified = localStorage.getItem('tabacaria_age_verified');
  if (verified !== 'true') {
    showModal.value = true;
  }
});

function confirmAge() {
  localStorage.setItem('tabacaria_age_verified', 'true');
  showModal.value = false;
}

function denyAge() {
  isUnderage.value = true;
}
</script>

<template>
  <Teleport v-if="showModal" to="body">
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/80 backdrop-blur-sm">
      <div class="relative w-full max-w-md p-6 overflow-hidden bg-slate-900 border border-amber-500/30 rounded-2xl shadow-2xl text-center text-white">
        
        <div class="w-16 h-16 mx-auto mb-4 flex items-center justify-center bg-amber-500/10 border border-amber-500/40 rounded-full">
          <span class="text-2xl font-black text-amber-500">18+</span>
        </div>

        <h3 class="text-2xl font-bold mb-2 tracking-wide text-amber-400">Verificação de Idade</h3>
        
        <p v-if="!isUnderage" class="text-gray-300 text-sm mb-6 leading-relaxed">
          Este site contém produtos destinados exclusivamente a maiores de 18 anos (artigos de tabacaria, acessórios e insumos).
          <br /><br />
          <strong>Você possui 18 anos ou mais?</strong>
        </p>

        <p v-else class="text-red-400 text-sm mb-6 font-semibold leading-relaxed">
          Desculpe, o acesso a esta plataforma é restrito a maiores de 18 anos.
        </p>

        <div v-if="!isUnderage" class="flex flex-col sm:flex-row gap-3">
          <button
            type="button"
            class="flex-1 py-3 px-4 bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-600 hover:to-amber-700 text-slate-950 font-bold rounded-xl shadow-lg transition-all transform hover:-translate-y-0.5 active:translate-y-0"
            @click="confirmAge"
          >
            Sim, tenho +18
          </button>
          
          <button
            type="button"
            class="flex-1 py-3 px-4 bg-slate-800 hover:bg-slate-700 text-gray-300 font-semibold rounded-xl border border-slate-700 transition-all"
            @click="denyAge"
          >
            Não, sou menor
          </button>
        </div>

        <div v-else class="mt-4">
          <a
            href="https://www.google.com"
            class="inline-block py-2.5 px-6 bg-slate-800 hover:bg-slate-700 text-gray-300 rounded-lg text-sm"
          >
            Sair do site
          </a>
        </div>

      </div>
    </div>
  </Teleport>
</template>
