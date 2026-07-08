<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref, computed } from 'vue';

const props = defineProps({
    encuesta: Object,
    complainants: Array,
});

const currentStep = ref(1);

const form = useForm({
    complainant_id: props.encuesta.complainant_id,
    publicaciones: (props.encuesta.publicaciones || []).map(p => ({
        id: p.id,
        red_social: p.red_social,
        pagina: p.pagina,
        fecha_publicacion: p.fecha_publicacion,
        enlace: p.enlace,
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
    if (currentStep.value === 2 && form.publicaciones.length === 0) {
        alert('Agrega al menos una publicación.');
        return;
    }
    currentStep.value++;
}

function prevStep() {
    currentStep.value--;
}

function addPublicacion() {
    form.publicaciones.push({
        id: null,
        red_social: '',
        pagina: '',
        fecha_publicacion: '',
        enlace: '',
    });
}

function removePublicacion(index) {
    form.publicaciones.splice(index, 1);
}

function submit() {
    form.put(route('admin.denuncias.encuestas.update', props.encuesta.id));
}
</script>

<template>
    <Head title="Editar Denuncia" />
    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">Editar Denuncia de Encuestas</h2>
                <Link :href="route('admin.denuncias.encuestas.show', encuesta.id)" class="text-sm text-gray-500 hover:text-gray-700">
                    <i class="fa-solid fa-arrow-left mr-1"></i> Volver
                </Link>
            </div>
        </template>
        <div class="py-6">
            <div class="mx-auto max-w-3xl px-4 sm:px-6 lg:px-8">
                <div class="flex items-center justify-center mb-8">
                    <template v-for="step in 3" :key="step">
                        <div class="flex items-center">
                            <div class="w-8 h-8 rounded-full flex items-center justify-center text-sm font-bold transition"
                                :class="{ 'bg-emerald-600 text-white': currentStep >= step, 'bg-gray-200 text-gray-500': currentStep < step }">{{ step }}</div>
                            <span class="ml-2 text-sm" :class="{ 'text-emerald-700 font-medium': currentStep >= step, 'text-gray-400': currentStep < step }">{{ step === 1 ? 'Denunciante' : step === 2 ? 'Publicaciones' : 'Confirmar' }}</span>
                        </div>
                        <div v-if="step < 3" class="w-12 sm:w-20 h-px mx-2 transition" :class="currentStep > step ? 'bg-emerald-600' : 'bg-gray-300'" />
                    </template>
                </div>
                <div class="bg-white shadow-sm rounded-lg p-6">
                    <!-- Step 1 -->
                    <div v-show="currentStep === 1">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">Datos del Denunciante</h3>
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Denunciante</label>
                                <select v-model="form.complainant_id" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500">
                                    <option :value="null" disabled>Selecciona un denunciante...</option>
                                    <option v-for="c in complainants" :key="c.id" :value="c.id">{{ c.nombre_completo }} — {{ (c.complaints_count || 0) + (c.lonas_count || 0) }} {{ ((c.complaints_count || 0) + (c.lonas_count || 0)) === 1 ? 'denuncia' : 'denuncias' }}</option>
                                </select>
                            </div>
                            <div v-if="selectedName" class="bg-gray-50 rounded-lg p-4 space-y-2 text-sm">
                                <div class="flex justify-between"><span class="text-gray-500">Clave de Elector:</span><span class="font-mono font-medium">{{ selectedName.clave_elector }}</span></div>
                                <div class="flex justify-between"><span class="text-gray-500">Domicilio:</span><span class="font-medium">{{ selectedName.domicilio }}</span></div>
                                <div class="flex justify-between"><span class="text-gray-500">Email:</span><span class="font-medium">{{ selectedName.email || '—' }}</span></div>
                                <div class="flex justify-between"><span class="text-gray-500">Sexo:</span><span class="font-medium">{{ selectedName.sexo === 'M' ? 'Masculino' : 'Femenino' }}</span></div>
                            </div>
                        </div>
                    </div>
                    <!-- Step 2 -->
                    <div v-show="currentStep === 2">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-semibold text-gray-800">Publicaciones en Redes Sociales</h3>
                            <button type="button" @click="addPublicacion" class="inline-flex items-center gap-1 px-3 py-1.5 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 text-sm transition"><i class="fa-solid fa-plus"></i> Agregar Publicación</button>
                        </div>
                        <div v-for="(pub, idx) in form.publicaciones" :key="idx" class="border rounded-lg p-4 mb-4 relative">
                            <div class="flex items-center justify-between mb-3">
                                <span class="text-sm font-semibold text-gray-700">Publicación #{{ idx + 1 }}</span>
                                <button type="button" @click="removePublicacion(idx)" class="text-red-400 hover:text-red-600 text-sm"><i class="fa-solid fa-trash"></i></button>
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-xs font-medium text-gray-600 mb-1">Red Social</label>
                                    <input v-model="pub.red_social" type="text" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 text-sm" required />
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-600 mb-1">Nombre de la Página</label>
                                    <input v-model="pub.pagina" type="text" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 text-sm" required />
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-600 mb-1">Fecha de Publicación</label>
                                    <input v-model="pub.fecha_publicacion" type="date" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 text-sm" required />
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-600 mb-1">Enlace Web</label>
                                    <input v-model="pub.enlace" type="url" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 text-sm" required />
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
                            <div class="bg-emerald-50 rounded-lg p-4">
                                <h4 class="text-sm font-semibold text-gray-600 mb-2">Publicaciones ({{ form.publicaciones.length }})</h4>
                                <div v-for="(pub, idx) in form.publicaciones" :key="idx" class="text-sm py-2 border-b border-emerald-100 last:border-0">
                                    <p class="font-medium">#{{ idx + 1 }} — {{ pub.red_social }} / {{ pub.pagina }}</p>
                                    <p class="text-gray-500">{{ pub.fecha_publicacion }} · <a :href="pub.enlace" target="_blank" class="text-emerald-600 hover:underline">{{ pub.enlace }}</a></p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="flex justify-between mt-6 pt-4 border-t">
                        <button v-if="currentStep > 1" type="button" @click="prevStep" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition"><i class="fa-solid fa-arrow-left mr-1"></i> Anterior</button>
                        <div v-else />
                        <button v-if="currentStep < 3" type="button" @click="nextStep" class="px-4 py-2 text-sm font-medium text-white bg-emerald-600 rounded-lg hover:bg-emerald-700 transition">Siguiente <i class="fa-solid fa-arrow-right ml-1"></i></button>
                        <button v-else type="button" @click="submit" :disabled="form.processing" class="px-6 py-2 text-sm font-medium text-white bg-emerald-600 rounded-lg hover:bg-emerald-700 transition disabled:opacity-50">
                            <i class="fa-solid fa-check mr-1"></i> {{ form.processing ? 'Guardando...' : 'Guardar Cambios' }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
