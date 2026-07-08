<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import imageCompression from 'browser-image-compression';

const props = defineProps({
    lona: Object,
    complainants: Array,
    colonias: Array,
});

const currentStep = ref(1);
const selectedComplainant = ref(props.lona.denunciante_clave_elector
    ? props.complainants.find(c => c.clave_elector === props.lona.denunciante_clave_elector)?.id
    : null);

const form = useForm({
    complainant_id: selectedComplainant.value,
    evidences: (props.lona.evidences || []).map(ev => ({
        id: ev.id,
        calle_y_num: ev.calle_y_num,
        colonia: ev.colonia,
        condicion_inmueble: ev.condicion_inmueble,
        photo: null,
        photo_path: ev.photo_path,
    })),
});

const selectedName = computed(() => {
    if (!form.complainant_id) return null;
    return props.complainants.find(c => c.id === form.complainant_id);
});

function nextStep() {
    if (currentStep.value === 1 && !form.complainant_id) {
        alert('Selecciona un denunciante.');
        return;
    }
    if (currentStep.value === 2 && form.evidences.length === 0) {
        alert('Agrega al menos una lona.');
        return;
    }
    currentStep.value++;
}

function prevStep() {
    currentStep.value--;
}

function addEvidence() {
    form.evidences.push({
        id: null,
        calle_y_num: '',
        colonia: '',
        condicion_inmueble: 'Habitado',
        photo: null,
        photo_path: null,
    });
}

function removeEvidence(index) {
    form.evidences.splice(index, 1);
}

async function handlePhoto(index, event) {
    const file = event.target.files[0];
    if (!file) return;
    try {
        const compressed = await imageCompression(file, {
            maxSizeMB: 0.5,
            maxWidthOrHeight: 1280,
            useWebWorker: true,
        });
        form.evidences[index].photo = compressed;
    } catch {
        form.evidences[index].photo = file;
    }
}

function submit() {
    form.put(route('admin.denuncias.lonas.update', props.lona.id));
}
</script>

<template>
    <Head title="Editar Denuncia" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    Editar Denuncia
                </h2>
                <Link
                    :href="route('admin.denuncias.lonas.show', lona.id)"
                    class="text-sm text-gray-500 hover:text-gray-700"
                >
                    <i class="fa-solid fa-arrow-left mr-1"></i> Volver
                </Link>
            </div>
        </template>

        <div class="py-6">
            <div class="mx-auto max-w-3xl px-4 sm:px-6 lg:px-8">
                <div class="flex items-center justify-center mb-8">
                    <template v-for="step in 3" :key="step">
                        <div class="flex items-center">
                            <div
                                class="w-8 h-8 rounded-full flex items-center justify-center text-sm font-bold transition"
                                :class="{
                                    'bg-amber-600 text-white': currentStep >= step,
                                    'bg-gray-200 text-gray-500': currentStep < step,
                                }"
                            >{{ step }}</div>
                            <span
                                class="ml-2 text-sm"
                                :class="{ 'text-amber-700 font-medium': currentStep >= step, 'text-gray-400': currentStep < step }"
                            >{{ step === 1 ? 'Denunciante' : step === 2 ? 'Lonas' : 'Confirmar' }}</span>
                        </div>
                        <div v-if="step < 3" class="w-12 sm:w-20 h-px mx-2 transition"
                            :class="currentStep > step ? 'bg-amber-600' : 'bg-gray-300'"
                        />
                    </template>
                </div>

                <div class="bg-white shadow-sm rounded-lg p-6">
                    <!-- Step 1 -->
                    <div v-show="currentStep === 1">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">Datos del Denunciante</h3>
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Denunciante</label>
                                <select
                                    v-model="form.complainant_id"
                                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-amber-500 focus:ring-amber-500"
                                >
                                    <option :value="null" disabled>Selecciona un denunciante...</option>
                                    <option v-for="c in complainants" :key="c.id" :value="c.id">
                                        {{ c.nombre_completo }} — {{ (c.complaints_count || 0) + (c.lonas_count || 0) }} {{ ((c.complaints_count || 0) + (c.lonas_count || 0)) === 1 ? 'denuncia' : 'denuncias' }}
                                    </option>
                                </select>
                            </div>
                            <div v-if="selectedName" class="bg-gray-50 rounded-lg p-4 space-y-2 text-sm">
                                <div class="flex justify-between">
                                    <span class="text-gray-500">Clave de Elector:</span>
                                    <span class="font-mono font-medium">{{ selectedName.clave_elector }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-500">Domicilio:</span>
                                    <span class="font-medium">{{ selectedName.domicilio }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-500">Email:</span>
                                    <span class="font-medium">{{ selectedName.email || '—' }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-500">Sexo:</span>
                                    <span class="font-medium">{{ selectedName.sexo === 'M' ? 'Masculino' : 'Femenino' }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Step 2 -->
                    <div v-show="currentStep === 2">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-semibold text-gray-800">Lonas</h3>
                            <button type="button" @click="addEvidence"
                                class="inline-flex items-center gap-1 px-3 py-1.5 bg-amber-600 text-white rounded-lg hover:bg-amber-700 text-sm transition">
                                <i class="fa-solid fa-plus"></i> Agregar Lona
                            </button>
                        </div>
                        <div v-for="(ev, idx) in form.evidences" :key="idx" class="border rounded-lg p-4 mb-4 relative">
                            <div class="flex items-center justify-between mb-3">
                                <span class="text-sm font-semibold text-gray-700">Lona #{{ idx + 1 }}</span>
                                <button type="button" @click="removeEvidence(idx)" class="text-red-400 hover:text-red-600 text-sm">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <div class="sm:col-span-2">
                                    <label class="block text-xs font-medium text-gray-600 mb-1">Calle y Número</label>
                                    <input v-model="ev.calle_y_num" type="text"
                                        class="w-full rounded-lg border-gray-300 shadow-sm focus:border-amber-500 focus:ring-amber-500 text-sm"
                                        required />
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-600 mb-1">Colonia (Tepic)</label>
                                    <v-select
                                        v-model="ev.colonia"
                                        :options="colonias"
                                        placeholder="Buscar colonia..."
                                        class="text-sm"
                                    />
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-600 mb-1">Condición</label>
                                    <select v-model="ev.condicion_inmueble"
                                        class="w-full rounded-lg border-gray-300 shadow-sm focus:border-amber-500 focus:ring-amber-500 text-sm">
                                        <option value="Habitado">Habitado</option>
                                        <option value="Deshabitado">Deshabitado</option>
                                    </select>
                                </div>
                                <div class="sm:col-span-2">
                                    <label class="block text-xs font-medium text-gray-600 mb-1">Fotografía</label>
                                    <div v-if="ev.photo_path" class="mb-2 flex items-center gap-2">
                                        <img :src="'/storage/' + ev.photo_path" class="h-12 w-12 object-cover rounded border" />
                                        <span class="text-xs text-gray-400">Actual</span>
                                    </div>
                                    <input type="file" accept="image/jpeg,image/png,image/webp"
                                        @change="handlePhoto(idx, $event)"
                                        class="w-full text-sm text-gray-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-amber-50 file:text-amber-700 hover:file:bg-amber-100" />
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Step 3 -->
                    <div v-show="currentStep === 3">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">Confirmar Cambios</h3>
                        <div v-if="selectedName" class="space-y-4">
                            <div class="bg-gray-50 rounded-lg p-4">
                                <h4 class="text-sm font-semibold text-gray-600 mb-2">Denunciante</h4>
                                <p class="text-sm font-medium">{{ selectedName.nombre_completo }}</p>
                                <p class="text-xs text-gray-500">{{ selectedName.clave_elector }}</p>
                            </div>
                            <div class="bg-amber-50 rounded-lg p-4">
                                <h4 class="text-sm font-semibold text-gray-600 mb-2">Lonas ({{ form.evidences.length }})</h4>
                                <div v-for="(ev, idx) in form.evidences" :key="idx"
                                    class="text-sm py-2 border-b border-amber-100 last:border-0">
                                    <p class="font-medium">Lona #{{ idx + 1 }} — {{ ev.calle_y_num }}</p>
                                    <p class="text-gray-500">Col. {{ ev.colonia }} · {{ ev.condicion_inmueble }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-between mt-6 pt-4 border-t">
                        <button v-if="currentStep > 1" type="button" @click="prevStep"
                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition">
                            <i class="fa-solid fa-arrow-left mr-1"></i> Anterior
                        </button>
                        <div v-else />
                        <button v-if="currentStep < 3" type="button" @click="nextStep"
                            class="px-4 py-2 text-sm font-medium text-white bg-amber-600 rounded-lg hover:bg-amber-700 transition">
                            Siguiente <i class="fa-solid fa-arrow-right ml-1"></i>
                        </button>
                        <button v-else type="button" @click="submit" :disabled="form.processing"
                            class="px-6 py-2 text-sm font-medium text-white bg-amber-600 rounded-lg hover:bg-amber-700 transition disabled:opacity-50">
                            <i class="fa-solid fa-check mr-1"></i>
                            {{ form.processing ? 'Guardando...' : 'Guardar Cambios' }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
