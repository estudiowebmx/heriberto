<script setup>
import { ref, reactive, computed } from 'vue';
import axios from 'axios';
import { Head, Link } from '@inertiajs/vue3';

const step = ref(1);
const sending = ref(false);
const done = ref(false);
const msg = ref('');

const form = reactive({
    nombre_completo: '',
    fecha_nacimiento: '',
    telefono: '',
    profesion_ocupacion: '',
    lugar_nacimiento: '',
    domicilio_actual: '',
    hijos_info: '',
    padres_viven: '',
    padres_origen: '',
    padres_ocupacion: '',
    preocupaciones: [],
    problema_justicia: '',
    enfoque_gobernador: '',
    errores_gobierno: [],
    deficiencias_servicios: [],
});

const validTelefono = computed(() => form.telefono.replace(/\D/g, '').length === 10);

function filtrarNumeros() {
    form.telefono = form.telefono.replace(/\D/g, '').slice(0, 10);
}

const next = () => { step.value++; window.scrollTo(0, 0); };
const prev = () => { step.value--; window.scrollTo(0, 0); };

const toggle = (arr, val) => {
    const idx = arr.indexOf(val);
    if (idx > -1) arr.splice(idx, 1);
    else arr.push(val);
};

const submit = async () => {
    const tel = form.telefono.replace(/\D/g, '');
    if (tel.length !== 10) {
        msg.value = 'El teléfono debe tener 10 dígitos.';
        return;
    }
    sending.value = true;
    try {
        const res = await axios.post('/api/encuesta', { ...form });
        msg.value = res.data.message;
        done.value = true;
    } catch (e) {
        msg.value = 'Error al enviar. Intenta de nuevo.';
    } finally {
        sending.value = false;
    }
};
</script>

<template>
    <Head title="Conóceme - Jasmin Bugarin" />

    <div class="min-h-screen bg-gradient-to-br from-verde-50 via-white to-verde-50">
        <div class="max-w-3xl mx-auto px-4 py-10">
            <div v-if="done" class="text-center py-20">
                <div class="w-20 h-20 rounded-full bg-verde-100 flex items-center justify-center mx-auto mb-6">
                    <svg class="w-10 h-10 text-verde-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                </div>
                <h2 class="text-2xl font-bold text-gray-900 mb-4">¡Gracias!</h2>
                <p class="text-gray-600 mb-6">{{ msg }}</p>
                <Link href="/" class="text-verde-600 hover:text-verde-700 font-medium">Volver al chat</Link>
            </div>

            <div v-else>
                <div class="text-center mb-8">
                    <div class="w-16 h-16 rounded-full overflow-hidden mx-auto mb-4 shadow-lg">
                        <img src="/img/perfil.jpg" alt="Jasmin Bugarin" class="w-full h-full object-cover" />
                    </div>
                    <h1 class="text-3xl font-bold text-gray-900">Conóceme</h1>
                    <p class="text-gray-600 mt-2">Jasmin Bugarin quiere conocerte mejor</p>
                </div>

                <!-- Progress -->
                <div class="flex items-center justify-center gap-2 mb-10">
                    <div v-for="s in 3" :key="s" class="flex items-center">
                        <div :class="['w-8 h-8 rounded-full flex items-center justify-center text-sm font-semibold transition-colors', step >= s ? 'bg-verde-600 text-white' : 'bg-gray-200 text-gray-500']">{{ s }}</div>
                        <div v-if="s < 3" :class="['h-1 w-12 transition-colors', step > s ? 'bg-verde-600' : 'bg-gray-200']"></div>
                    </div>
                </div>

                <!-- Step 1: Personal -->
                <div v-if="step === 1" class="bg-white rounded-2xl shadow-lg p-8">
                    <h2 class="text-xl font-semibold text-gray-900 mb-6">Información Personal</h2>
                    <div class="space-y-5">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nombre Completo *</label>
                            <input v-model="form.nombre_completo" type="text" required class="w-full rounded-xl border border-gray-300 px-4 py-3 text-sm focus:border-verde-500 focus:ring-1 focus:ring-verde-500 outline-none" />
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Fecha de Nacimiento</label>
                                <input v-model="form.fecha_nacimiento" type="date" max="2009-12-31" class="w-full rounded-xl border border-gray-300 px-4 py-3 text-sm focus:border-verde-500 focus:ring-1 focus:ring-verde-500 outline-none appearance-none" />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Teléfono * <span class="text-gray-400 font-normal">(10 dígitos)</span></label>
                                <input v-model="form.telefono" @input="filtrarNumeros" type="tel" inputmode="numeric" required placeholder="5512345678" :class="['w-full rounded-xl border px-4 py-3 text-sm outline-none transition-colors', form.telefono.length > 0 ? (validTelefono ? 'border-green-400 focus:border-green-500 focus:ring-1 focus:ring-green-500' : 'border-red-300 focus:border-red-500 focus:ring-1 focus:ring-red-500') : 'border-gray-300 focus:border-verde-500 focus:ring-1 focus:ring-verde-500']" />
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Profesión / Ocupación *</label>
                            <input v-model="form.profesion_ocupacion" type="text" required class="w-full rounded-xl border border-gray-300 px-4 py-3 text-sm focus:border-verde-500 focus:ring-1 focus:ring-verde-500 outline-none" placeholder="Ej: Ingeniero, Estudiante, Ama de casa..." />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Lugar de Nacimiento *</label>
                            <input v-model="form.lugar_nacimiento" type="text" required class="w-full rounded-xl border border-gray-300 px-4 py-3 text-sm focus:border-verde-500 focus:ring-1 focus:ring-verde-500 outline-none" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Domicilio Actual</label>
                            <textarea v-model="form.domicilio_actual" rows="3" class="w-full rounded-xl border border-gray-300 px-4 py-3 text-sm focus:border-verde-500 focus:ring-1 focus:ring-verde-500 outline-none"></textarea>
                        </div>
                    </div>
                    <div class="mt-8 flex justify-end">
                        <button @click="next" :disabled="!form.nombre_completo || !form.profesion_ocupacion || !form.lugar_nacimiento || !validTelefono" class="px-6 py-3 bg-verde-600 text-white rounded-xl hover:bg-verde-700 disabled:opacity-50 transition-colors font-medium text-sm">Siguiente →</button>
                    </div>
                </div>

                <!-- Step 2: Familiar -->
                <div v-if="step === 2" class="bg-white rounded-2xl shadow-lg p-8">
                    <h2 class="text-xl font-semibold text-gray-900 mb-6">Información Familiar</h2>
                    <div class="space-y-5">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Hijos o dependientes económicos (edades y escolaridad)</label>
                            <textarea v-model="form.hijos_info" rows="3" class="w-full rounded-xl border border-gray-300 px-4 py-3 text-sm focus:border-verde-500 focus:ring-1 focus:ring-verde-500 outline-none" placeholder="Ej: Hijo 12 años (secundaria), Hija 8 años (primaria)..."></textarea>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">¿Tus padres viven?</label>
                            <div class="flex gap-4">
                                <label v-for="op in ['si','no','solo_uno']" :key="op" class="flex items-center gap-2 cursor-pointer">
                                    <input v-model="form.padres_viven" type="radio" :value="op" class="text-verde-600 focus:ring-verde-500" />
                                    <span class="text-sm text-gray-700">{{ op === 'si' ? 'Sí' : op === 'no' ? 'No' : 'Solo uno' }}</span>
                                </label>
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Lugar de origen de tus padres</label>
                            <input v-model="form.padres_origen" type="text" class="w-full rounded-xl border border-gray-300 px-4 py-3 text-sm focus:border-verde-500 focus:ring-1 focus:ring-verde-500 outline-none" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">¿A qué se dedican o dedicaron?</label>
                            <input v-model="form.padres_ocupacion" type="text" class="w-full rounded-xl border border-gray-300 px-4 py-3 text-sm focus:border-verde-500 focus:ring-1 focus:ring-verde-500 outline-none" />
                        </div>
                    </div>
                    <div class="mt-8 flex justify-between">
                        <button @click="prev" class="px-6 py-3 bg-gray-100 text-gray-700 rounded-xl hover:bg-gray-200 transition-colors font-medium text-sm">← Anterior</button>
                        <button @click="next" class="px-6 py-3 bg-verde-600 text-white rounded-xl hover:bg-verde-700 transition-colors font-medium text-sm">Siguiente →</button>
                    </div>
                </div>

                <!-- Step 3: Social y Política -->
                <div v-if="step === 3" class="bg-white rounded-2xl shadow-lg p-8">
                    <h2 class="text-xl font-semibold text-gray-900 mb-6">Perspectiva Social y Política</h2>
                    <div class="space-y-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Preocupaciones actuales (selecciona todas las que apliquen)</label>
                            <div class="grid grid-cols-2 gap-2">
                                <label v-for="op in ['Salud','Empleo','Educación','Seguridad','Sistema de Justicia','Otro']" :key="op" class="flex items-center gap-2 cursor-pointer bg-gray-50 rounded-lg px-3 py-2 hover:bg-gray-100">
                                    <input type="checkbox" :checked="form.preocupaciones.includes(op)" @change="toggle(form.preocupaciones, op)" class="rounded text-verde-600 focus:ring-verde-500" />
                                    <span class="text-sm text-gray-700">{{ op }}</span>
                                </label>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Principal problema de la justicia</label>
                            <div class="space-y-2">
                                <label v-for="op in ['Corrupción','Falta de capacidad','El sistema está mal diseñado','Mala educación profesional']" :key="op" class="flex items-center gap-2 cursor-pointer bg-gray-50 rounded-lg px-3 py-2 hover:bg-gray-100">
                                    <input v-model="form.problema_justicia" type="radio" :value="op" class="text-verde-600 focus:ring-verde-500" />
                                    <span class="text-sm text-gray-700">{{ op }}</span>
                                </label>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">¿En qué le gustaría que el próximo gobernador se enfocara?</label>
                            <textarea v-model="form.enfoque_gobernador" rows="3" class="w-full rounded-xl border border-gray-300 px-4 py-3 text-sm focus:border-verde-500 focus:ring-1 focus:ring-verde-500 outline-none"></textarea>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Principales errores de los gobiernos actuales</label>
                            <div class="grid grid-cols-2 gap-2">
                                <label v-for="op in ['Economía','Educación','Corrupción','Falta de oportunidades','Seguridad','Sistemas de Salud','Trámites de gobierno','Impuestos','Empleo']" :key="op" class="flex items-center gap-2 cursor-pointer bg-gray-50 rounded-lg px-3 py-2 hover:bg-gray-100">
                                    <input type="checkbox" :checked="form.errores_gobierno.includes(op)" @change="toggle(form.errores_gobierno, op)" class="rounded text-verde-600 focus:ring-verde-500" />
                                    <span class="text-sm text-gray-700">{{ op }}</span>
                                </label>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Deficiencias en Servicios Públicos</label>
                            <div class="grid grid-cols-2 gap-2">
                                <label v-for="op in ['Agua','Alumbrado público','Alcantarillado','Transporte público','Calles','Hospitales','Escuelas']" :key="op" class="flex items-center gap-2 cursor-pointer bg-gray-50 rounded-lg px-3 py-2 hover:bg-gray-100">
                                    <input type="checkbox" :checked="form.deficiencias_servicios.includes(op)" @change="toggle(form.deficiencias_servicios, op)" class="rounded text-verde-600 focus:ring-verde-500" />
                                    <span class="text-sm text-gray-700">{{ op }}</span>
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="mt-8 flex justify-between">
                        <button @click="prev" class="px-6 py-3 bg-gray-100 text-gray-700 rounded-xl hover:bg-gray-200 transition-colors font-medium text-sm">← Anterior</button>
                        <button @click="submit" :disabled="sending" class="px-8 py-3 bg-verde-600 text-white rounded-xl hover:bg-verde-700 disabled:opacity-50 transition-colors font-medium text-sm">{{ sending ? 'Enviando...' : 'Enviar' }}</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
