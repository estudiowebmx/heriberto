<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import imageCompression from 'browser-image-compression';

const props = defineProps({
    complainants: Array,
    colonias: Array,
});

const currentStep = ref(1);
const selectedComplainant = ref(null);
const coloniaInputs = ref([]);

const form = useForm({
    complainant_id: null,
    evidences: [],
});

const selectedName = computed(() => {
    if (!selectedComplainant.value) return null;
    return props.complainants.find(c => c.id === selectedComplainant.value);
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
        calle_y_num: '',
        colonia: '',
        condicion_inmueble: 'Habitado',
        photo: null,
    });
    coloniaInputs.value.push('');
}

function removeEvidence(index) {
    form.evidences.splice(index, 1);
    coloniaInputs.value.splice(index, 1);
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
    form.post(route('admin.denuncias.lonas.store'));
}
</script>

<template>
    <Head title="Nueva Denuncia - Lonas" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    Nueva Denuncia de Lonas
                </h2>
                <Link
                    :href="route('admin.denuncias.lonas.index')"
                    class="text-sm text-gray-500 hover:text-gray-700"
                >
                    <i class="fa-solid fa-arrow-left mr-1"></i> Volver
                </Link>
            </div>
        </template>

        <div class="py-6">
            <div class="mx-auto max-w-3xl px-4 sm:px-6 lg:px-8">
                <!-- Steps indicator -->
                <div class="flex items-center justify-center mb-8">
                    <template v-for="step in 3" :key="step">
                        <div class="flex items-center">
                            <div
                                class="w-8 h-8 rounded-full flex items-center justify-center text-sm font-bold transition"
                                :class="{
                                    'bg-amber-600 text-white': currentStep >= step,
                                    'bg-gray-200 text-gray-500': currentStep < step,
                                }"
                            >
                                {{ step }}
                            </div>
                            <span
                                class="ml-2 text-sm"
                                :class="{
                                    'text-amber-700 font-medium': currentStep >= step,
                                    'text-gray-400': currentStep < step,
                                }"
                            >
                                {{ step === 1 ? 'Denunciante' : step === 2 ? 'Lonas' : 'Confirmar' }}
                            </span>
                        </div>
                        <div
                            v-if="step < 3"
                            class="w-12 sm:w-20 h-px mx-2 transition"
                            :class="currentStep > step ? 'bg-amber-600' : 'bg-gray-300'"
                        />
                    </template>
                </div>

                <div class="bg-white shadow-sm rounded-lg p-6">
                    <!-- Step 1: Denunciante -->
                    <div v-show="currentStep === 1">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">Datos del Denunciante</h3>

                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Denunciante
                                </label>
                                <select
                                    v-model="form.complainant_id"
                                    @change="selectedComplainant = form.complainant_id"
                                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-amber-500 focus:ring-amber-500"
                                >
                                    <option :value="null" disabled>Selecciona un denunciante...</option>
                                    <option
                                        v-for="c in complainants"
                                        :key="c.id"
                                        :value="c.id"
                                    >
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

                    <!-- Step 2: Lonas -->
                    <div v-show="currentStep === 2">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-semibold text-gray-800">Lonas</h3>
                            <button
                                type="button"
                                @click="addEvidence"
                                class="inline-flex items-center gap-1 px-3 py-1.5 bg-amber-600 text-white rounded-lg hover:bg-amber-700 text-sm transition"
                            >
                                <i class="fa-solid fa-plus"></i> Agregar Lona
                            </button>
                        </div>

                        <div v-if="form.evidences.length === 0" class="text-center text-gray-400 py-8">
                            <i class="fa-solid fa-images text-4xl mb-2 block"></i>
                            <p>Agrega al menos una lona para continuar.</p>
                        </div>

                        <div
                            v-for="(ev, idx) in form.evidences"
                            :key="idx"
                            class="border rounded-lg p-4 mb-4 relative"
                        >
                            <div class="flex items-center justify-between mb-3">
                                <span class="text-sm font-semibold text-gray-700">
                                    Lona #{{ idx + 1 }}
                                </span>
                                <button
                                    type="button"
                                    @click="removeEvidence(idx)"
                                    class="text-red-400 hover:text-red-600 text-sm"
                                >
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <div class="sm:col-span-2">
                                    <label class="block text-xs font-medium text-gray-600 mb-1">
                                        Calle y Número
                                    </label>
                                    <input
                                        v-model="ev.calle_y_num"
                                        type="text"
                                        class="w-full rounded-lg border-gray-300 shadow-sm focus:border-amber-500 focus:ring-amber-500 text-sm"
                                        placeholder="Ej: Av. Principal #123"
                                        required
                                    />
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-600 mb-1">
                                        Colonia (Tepic)
                                    </label>
                                    <v-select
                                        v-model="ev.colonia"
                                        :options="colonias"
                                        placeholder="Buscar colonia..."
                                        class="text-sm"
                                    />
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-600 mb-1">
                                        Condición del Inmueble
                                    </label>
                                    <select
                                        v-model="ev.condicion_inmueble"
                                        class="w-full rounded-lg border-gray-300 shadow-sm focus:border-amber-500 focus:ring-amber-500 text-sm"
                                    >
                                        <option value="Habitado">Habitado</option>
                                        <option value="Deshabitado">Deshabitado</option>
                                    </select>
                                </div>
                                <div class="sm:col-span-2">
                                    <label class="block text-xs font-medium text-gray-600 mb-1">
                                        Fotografía
                                    </label>
                                    <input
                                        type="file"
                                        accept="image/jpeg,image/png,image/webp"
                                        @change="handlePhoto(idx, $event)"
                                        class="w-full text-sm text-gray-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-amber-50 file:text-amber-700 hover:file:bg-amber-100"
                                        required
                                    />
                                    <div
                                        v-if="ev.photo"
                                        class="mt-2 text-xs text-green-600 flex items-center gap-1"
                                    >
                                        <i class="fa-solid fa-check-circle"></i>
                                        Imagen seleccionada
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Step 3: Confirm -->
                    <div v-show="currentStep === 3">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">Confirmar Denuncia</h3>

                        <div v-if="selectedName" class="space-y-4">
                            <div class="bg-gray-50 rounded-lg p-4">
                                <h4 class="text-sm font-semibold text-gray-600 mb-2">Denunciante</h4>
                                <div class="text-sm space-y-1">
                                    <p><span class="text-gray-500">Nombre:</span> <strong>{{ selectedName.nombre_completo }}</strong></p>
                                    <p><span class="text-gray-500">Clave Elector:</span> <span class="font-mono">{{ selectedName.clave_elector }}</span></p>
                                    <p><span class="text-gray-500">Domicilio:</span> {{ selectedName.domicilio }}</p>
                                    <p><span class="text-gray-500">Email:</span> {{ selectedName.email || '—' }}</p>
                                </div>
                            </div>

                            <div class="bg-amber-50 rounded-lg p-4">
                                <h4 class="text-sm font-semibold text-gray-600 mb-2">
                                    Lonas ({{ form.evidences.length }})
                                </h4>
                                <div
                                    v-for="(ev, idx) in form.evidences"
                                    :key="idx"
                                    class="text-sm py-2 border-b border-amber-100 last:border-0"
                                >
                                    <p class="font-medium">Lona #{{ idx + 1 }}</p>
                                    <p class="text-gray-500">{{ ev.calle_y_num }} — Col. {{ ev.colonia }}</p>
                                    <p class="text-gray-400 text-xs">{{ ev.condicion_inmueble }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Navigation buttons -->
                    <div class="flex justify-between mt-6 pt-4 border-t">
                        <button
                            v-if="currentStep > 1"
                            type="button"
                            @click="prevStep"
                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition"
                        >
                            <i class="fa-solid fa-arrow-left mr-1"></i> Anterior
                        </button>
                        <div v-else />

                        <button
                            v-if="currentStep < 3"
                            type="button"
                            @click="nextStep"
                            class="px-4 py-2 text-sm font-medium text-white bg-amber-600 rounded-lg hover:bg-amber-700 transition"
                        >
                            Siguiente <i class="fa-solid fa-arrow-right ml-1"></i>
                        </button>
                        <button
                            v-else
                            type="button"
                            @click="submit"
                            :disabled="form.processing"
                            class="px-6 py-2 text-sm font-medium text-white bg-amber-600 rounded-lg hover:bg-amber-700 transition disabled:opacity-50"
                        >
                            <i class="fa-solid fa-check mr-1"></i>
                            {{ form.processing ? 'Registrando...' : 'Registrar Denuncia' }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
