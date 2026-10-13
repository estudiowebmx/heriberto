<script setup>
import { ref, reactive, computed } from 'vue';
import axios from 'axios';
import { Head } from '@inertiajs/vue3';

const form = reactive({
    nombre: '',
    telefono: '',
});

const sending = ref(false);
const done = ref(false);
const error = ref('');

const validTelefono = computed(() => form.telefono.length === 10);

function filtrarNumeros() {
    form.telefono = form.telefono.replace(/\D/g, '').slice(0, 10);
}

const submit = async () => {
    error.value = '';

    if (!form.nombre.trim()) {
        error.value = 'Escribe tu primer nombre.';
        return;
    }

    if (!validTelefono.value) {
        error.value = 'El teléfono debe tener 10 dígitos.';
        return;
    }

    sending.value = true;
    try {
        await axios.post('/demostracion', {
            nombre: form.nombre.trim(),
            telefono: form.telefono,
        });
        done.value = true;
    } catch (e) {
        error.value = e?.response?.data?.message || 'No se pudo registrar. Intenta de nuevo.';
    } finally {
        sending.value = false;
    }
};
</script>

<template>
    <Head title="Demostración - Heriberto Castañeda" />

    <div class="min-h-screen bg-gradient-to-br from-guinda-50 via-white to-guinda-50 flex items-center justify-center px-4 py-10">
        <div class="w-full max-w-md">
            <div v-if="done" class="bg-white rounded-2xl shadow-lg p-10 text-center">
                <div class="w-20 h-20 rounded-full bg-guinda-100 flex items-center justify-center mx-auto mb-6">
                    <svg class="w-10 h-10 text-guinda-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                </div>
                <h2 class="text-2xl font-bold text-gray-900 mb-4">¡Gracias!</h2>
                <p class="text-gray-600">Tus datos se registraron correctamente.</p>
            </div>

            <div v-else class="bg-white rounded-2xl shadow-lg p-8">
                <div class="text-center mb-8">
                    <div class="w-16 h-16 rounded-full overflow-hidden mx-auto mb-4 shadow-lg">
                        <img src="/img/perfil.jpg" alt="Heriberto Castañeda" class="w-full h-full object-cover" />
                    </div>
                    <h1 class="text-2xl font-bold text-gray-900">Demostración</h1>
                    <p class="text-gray-600 mt-2 text-sm">Registra tu primer nombre y tu número de teléfono.</p>
                </div>

                <form @submit.prevent="submit" class="space-y-5">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Primer nombre *</label>
                        <input
                            v-model="form.nombre"
                            type="text"
                            required
                            maxlength="100"
                            placeholder="Ej: Juan"
                            class="w-full rounded-xl border border-gray-300 px-4 py-3 text-sm focus:border-guinda-500 focus:ring-1 focus:ring-guinda-500 outline-none"
                        />
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Teléfono * <span class="text-gray-400 font-normal">(10 dígitos)</span>
                        </label>
                        <input
                            v-model="form.telefono"
                            @input="filtrarNumeros"
                            type="tel"
                            inputmode="numeric"
                            required
                            placeholder="5512345678"
                            :class="[
                                'w-full rounded-xl border px-4 py-3 text-sm outline-none transition-colors',
                                form.telefono.length > 0
                                    ? (validTelefono ? 'border-guinda-400 focus:border-guinda-500 focus:ring-1 focus:ring-guinda-500' : 'border-red-300 focus:border-red-500 focus:ring-1 focus:ring-red-500')
                                    : 'border-gray-300 focus:border-guinda-500 focus:ring-1 focus:ring-guinda-500',
                            ]"
                        />
                    </div>

                    <p v-if="error" class="text-sm text-red-600">{{ error }}</p>

                    <button
                        type="submit"
                        :disabled="sending"
                        class="w-full px-4 py-3 bg-guinda-600 text-white rounded-xl font-semibold hover:bg-guinda-700 disabled:opacity-50 transition-colors"
                    >
                        {{ sending ? 'Enviando...' : 'Registrar' }}
                    </button>
                </form>
            </div>
        </div>
    </div>
</template>
