<script setup>
import axios from 'axios';
import { onBeforeUnmount, ref } from 'vue';

const emit = defineEmits(['datos']);

const archivo = ref(null);
const preview = ref(null);
const procesando = ref(false);
const error = ref('');
const exito = ref('');

const seleccionarArchivo = (event) => {
    const file = event.target.files?.[0] ?? null;

    error.value = '';
    exito.value = '';

    if (!file) {
        return;
    }

    if (!file.type.startsWith('image/')) {
        error.value = 'Selecciona una imagen válida.';
        archivo.value = null;

        return;
    }

    if (file.size > 10 * 1024 * 1024) {
        error.value = 'La imagen no debe superar los 10 MB.';
        archivo.value = null;

        return;
    }

    archivo.value = file;

    if (preview.value) {
        URL.revokeObjectURL(preview.value);
    }

    preview.value = URL.createObjectURL(file);
};

const extraerDatos = async () => {
    if (!archivo.value || procesando.value) {
        return;
    }

    procesando.value = true;
    error.value = '';
    exito.value = '';

    const formData = new FormData();
    formData.append('imagen', archivo.value);

    try {
        const { data } = await axios.post(route('admin.personas.ine'), formData, {
            headers: { 'Content-Type': 'multipart/form-data' },
        });

        emit('datos', data.data ?? {});
        exito.value = 'Datos extraídos. Revisa y corrige los campos antes de guardar.';
    } catch (e) {
        error.value =
            e.response?.data?.errors?.imagen?.[0] ??
            e.response?.data?.message ??
            'No se pudo leer la INE. Intenta con una foto más clara y bien iluminada.';
    } finally {
        procesando.value = false;
    }
};

onBeforeUnmount(() => {
    if (preview.value) {
        URL.revokeObjectURL(preview.value);
    }
});
</script>

<template>
    <div class="rounded-xl border border-guinda-100 bg-guinda-50/50 p-5">
        <div class="flex flex-col gap-1">
            <h3 class="text-base font-semibold text-gray-900">Cargar desde INE</h3>
            <p class="text-sm text-gray-600">
                Sube o toma una fotografía de la credencial y DeepSeek intentará llenar los campos automáticamente.
            </p>
        </div>

        <div class="mt-4 grid gap-4 sm:grid-cols-[220px_1fr]">
            <label class="group relative flex min-h-[150px] cursor-pointer items-center justify-center overflow-hidden rounded-lg border-2 border-dashed border-gray-300 bg-white transition-colors hover:border-guinda-400">
                <input type="file" accept="image/*" capture="environment" class="sr-only" @change="seleccionarArchivo" />

                <img v-if="preview" :src="preview" alt="Vista previa de la INE" class="h-full max-h-[220px] w-full object-contain" />

                <div v-else class="px-4 py-8 text-center">
                    <i class="fa-solid fa-id-card text-3xl text-gray-300"></i>
                    <p class="mt-2 text-sm font-medium text-gray-600">Seleccionar o tomar foto</p>
                    <p class="mt-1 text-xs text-gray-400">JPG, PNG, WEBP o GIF</p>
                </div>
            </label>

            <div class="flex flex-col justify-center gap-3">
                <button
                    type="button"
                    :disabled="!archivo || procesando"
                    class="inline-flex w-full items-center justify-center gap-2 rounded-lg bg-guinda-600 px-4 py-2.5 text-sm font-medium text-white transition-colors hover:bg-guinda-700 disabled:cursor-not-allowed disabled:opacity-50 sm:w-auto"
                    @click="extraerDatos"
                >
                    <i v-if="procesando" class="fa-solid fa-spinner animate-spin"></i>
                    <i v-else class="fa-solid fa-wand-magic-sparkles"></i>
                    {{ procesando ? 'Leyendo credencial...' : 'Extraer datos del INE' }}
                </button>

                <p v-if="error" class="text-sm text-red-600">{{ error }}</p>
                <p v-if="exito" class="text-sm text-green-700">{{ exito }}</p>
                <p class="text-xs text-gray-500">Tamaño máximo: 10 MB. La imagen no se guarda.</p>
            </div>
        </div>
    </div>
</template>
