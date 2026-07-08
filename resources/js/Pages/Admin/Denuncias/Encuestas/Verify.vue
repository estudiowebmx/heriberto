<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, watch, onUnmounted } from 'vue';

const props = defineProps({
    encuesta: Object,
});

const currentStep = ref(0);
const totalSteps = 1 + (props.encuesta.publicaciones?.length ?? 0);
const rejected = ref(false);
const rushed = ref(false);

const timerProgress = ref(0);
const timerReady = ref(false);
let timerInterval = null;
const DELAY_MS = 5000;

function startTimer() {
    timerReady.value = false;
    timerProgress.value = 0;
    rushed.value = false;
    clearInterval(timerInterval);
    const start = Date.now();
    timerInterval = setInterval(() => {
        const elapsed = Date.now() - start;
        timerProgress.value = Math.min(100, (elapsed / DELAY_MS) * 100);
        if (elapsed >= DELAY_MS) {
            timerProgress.value = 100;
            timerReady.value = true;
            clearInterval(timerInterval);
            timerInterval = null;
        }
    }, 50);
}

watch(currentStep, () => { startTimer(); }, { immediate: true });

onUnmounted(() => { clearInterval(timerInterval); });

function onSi() {
    if (!timerReady.value) {
        rushed.value = true;
        return;
    }
    if (currentStep.value < totalSteps - 1) {
        currentStep.value++;
    } else {
        router.post(route('admin.denuncias.encuestas.verify.confirm', props.encuesta.id));
    }
}

function onNo() {
    rejected.value = true;
}

function goEdit() {
    router.get(route('admin.denuncias.encuestas.edit', props.encuesta.id));
}
</script>

<template>
    <Head title="Verificar Denuncia" />
    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">Verificar Denuncia</h2>
                <Link :href="route('admin.denuncias.encuestas.show', encuesta.id)" class="text-sm text-gray-500 hover:text-gray-700">
                    <i class="fa-solid fa-arrow-left mr-1"></i> Volver
                </Link>
            </div>
        </template>
        <div class="py-6">
            <div class="mx-auto max-w-2xl px-4 sm:px-6 lg:px-8">
                <div class="bg-white shadow-sm rounded-lg p-6">
                    <!-- Progress -->
                    <div class="mb-6">
                        <div class="flex items-center justify-between text-sm text-gray-500 mb-2">
                            <span>Paso {{ currentStep + 1 }} de {{ totalSteps }}</span>
                            <span v-if="!rejected">{{ currentStep === 0 ? 'Denunciante' : `Publicación #${currentStep}` }}</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-emerald-600 h-2 rounded-full transition-all duration-300" :style="{ width: ((currentStep) / totalSteps) * 100 + '%' }" />
                        </div>
                    </div>
                    <!-- Countdown bar -->
                    <div class="mb-5">
                        <div class="flex items-center justify-between text-xs text-gray-400 mb-1">
                            <span v-if="!timerReady">Revisa la información...</span>
                            <span v-else class="text-green-600 font-medium">Ya puedes continuar</span>
                            <span>{{ Math.ceil((100 - timerProgress) / 100 * 5) }}s</span>
                        </div>
                        <div class="w-full bg-gray-100 rounded-full h-1 overflow-hidden">
                            <div class="h-1 rounded-full transition-all duration-100 ease-linear" :class="timerReady ? 'bg-green-500' : 'bg-emerald-400'" :style="{ width: timerProgress + '%' }" />
                        </div>
                    </div>
                    <!-- Rush warning -->
                    <div v-if="rushed" class="mb-5 bg-yellow-50 border border-yellow-200 rounded-lg p-3 text-center">
                        <p class="text-yellow-700 text-sm font-medium"><i class="fa-solid fa-triangle-exclamation mr-1"></i> Por favor revisa minuciosamente la información.</p>
                    </div>
                    <!-- Step 0: Denunciante -->
                    <div v-if="currentStep === 0 && !rejected">
                        <div class="text-center mb-6">
                            <i class="fa-solid fa-user-check text-5xl text-emerald-500 mb-3 block"></i>
                            <h3 class="text-xl font-bold text-gray-800 mb-2">¿El denunciante es correcto?</h3>
                        </div>
                        <div class="bg-gray-50 rounded-lg p-4 mb-6">
                            <div class="grid grid-cols-1 gap-2 text-sm">
                                <div><span class="text-gray-500">Nombre:</span><p class="font-semibold text-gray-900 text-lg">{{ encuesta.complainant?.nombre_completo }}</p></div>
                                <div class="grid grid-cols-2 gap-2">
                                    <div><span class="text-gray-500">Clave Elector:</span><p class="font-mono">{{ encuesta.complainant?.clave_elector }}</p></div>
                                    <div><span class="text-gray-500">Sexo:</span><p>{{ encuesta.complainant?.sexo === 'M' ? 'Masculino' : 'Femenino' }}</p></div>
                                </div>
                                <div><span class="text-gray-500">Domicilio:</span><p>{{ encuesta.complainant?.domicilio }}</p></div>
                                <div><span class="text-gray-500">Email:</span><p>{{ encuesta.complainant?.email || '—' }}</p></div>
                            </div>
                        </div>
                    </div>
                    <!-- Publication steps -->
                    <div v-if="currentStep > 0 && !rejected">
                        <div class="text-center mb-6">
                            <i class="fa-solid fa-share-nodes text-5xl text-emerald-500 mb-3 block"></i>
                            <h3 class="text-xl font-bold text-gray-800 mb-2">¿Los datos de la publicación #{{ currentStep }} son correctos?</h3>
                        </div>
                        <template v-for="(pub, idx) in encuesta.publicaciones" :key="pub.id">
                            <div v-if="idx + 1 === currentStep" class="bg-gray-50 rounded-lg p-4 mb-6">
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-sm">
                                    <div><span class="text-gray-500">Red Social:</span><p class="font-medium">{{ pub.red_social }}</p></div>
                                    <div><span class="text-gray-500">Página:</span><p class="font-medium">{{ pub.pagina }}</p></div>
                                    <div><span class="text-gray-500">Fecha:</span><p class="font-medium">{{ pub.fecha_publicacion }}</p></div>
                                    <div><span class="text-gray-500">Enlace:</span><a :href="pub.enlace" target="_blank" class="font-medium text-emerald-600 hover:underline break-all">{{ pub.enlace }}</a></div>
                                </div>
                            </div>
                        </template>
                    </div>
                    <!-- Rejected state -->
                    <div v-if="rejected" class="text-center py-6">
                        <i class="fa-solid fa-triangle-exclamation text-6xl text-red-400 mb-4 block"></i>
                        <p class="text-lg font-semibold text-gray-800 mb-2">Datos incorrectos</p>
                        <p class="text-gray-500 mb-6">Edita la denuncia y corrige el dato por favor.</p>
                        <button type="button" @click="goEdit" class="inline-flex items-center gap-2 px-6 py-2.5 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 font-medium transition">
                            <i class="fa-solid fa-pen-to-square"></i> Editar Denuncia
                        </button>
                    </div>
                    <!-- Done state -->
                    <div v-if="currentStep === totalSteps - 1 && !rejected" class="text-center py-4 mb-4">
                        <i class="fa-solid fa-circle-check text-5xl text-green-500 mb-3 block"></i>
                        <p class="text-lg font-semibold text-gray-700">Todos los datos revisados</p>
                        <p class="text-sm text-gray-500 mt-1">Presiona "Sí" para confirmar la verificación.</p>
                    </div>
                    <!-- SI/NO buttons -->
                    <div v-if="!rejected" class="flex items-center justify-center gap-4 mt-6 pt-4 border-t">
                        <button type="button" @click="onNo" class="inline-flex items-center gap-2 px-8 py-3 text-lg font-semibold text-red-600 bg-red-50 border-2 border-red-200 rounded-xl hover:bg-red-100 transition">
                            <i class="fa-solid fa-xmark text-xl"></i> No
                        </button>
                        <button type="button" @click="onSi" :disabled="false"
                            class="inline-flex items-center gap-2 px-8 py-3 text-lg font-semibold rounded-xl transition"
                            :class="timerReady ? 'text-green-600 bg-green-50 border-2 border-green-200 hover:bg-green-100' : 'text-gray-400 bg-gray-50 border-2 border-gray-200 cursor-not-allowed'">
                            <i class="fa-solid fa-check text-xl"></i> Sí
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
