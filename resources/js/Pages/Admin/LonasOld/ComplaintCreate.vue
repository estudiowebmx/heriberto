<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { reactive } from 'vue';
import imageCompression from 'browser-image-compression';

const props = defineProps({
    complainants: Array,
});

const form = useForm({
    complainant_id: '',
    colonia: '',
    cac_president_name: '',
    evidences: [],
});

const defaultEvidence = () => ({
    address: '',
    leyenda: '',
    is_inhabited: false,
    is_cac_president: true,
    photo: null,
});

form.evidences = reactive([defaultEvidence()]);

const addEvidence = () => {
    form.evidences.push(reactive({
        address: '',
        leyenda: '',
        is_inhabited: false,
        is_cac_president: false,
        photo: null,
    }));
};

const removeEvidence = (index) => {
    if (form.evidences.length <= 1) return;
    form.evidences.splice(index, 1);
};

const compressOptions = {
    maxWidthOrHeight: 1280,
    maxSizeMB: 0.5,
    useWebWorker: true,
};

const compressing = reactive({});

const onPhotoChange = async (index, event) => {
    const file = event.target.files[0];
    if (!file) return;

    try {
        compressing[index] = true;
        const compressed = await imageCompression(file, compressOptions);
        form.evidences[index].photo = compressed;
    } catch {
        form.evidences[index].photo = file;
    } finally {
        compressing[index] = false;
    }
};

const submit = () => {
    form.post(route('admin.lonas-old.store'));
};
</script>

<template>
    <Head title="Nueva Denuncia" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">Nueva Denuncia</h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-4xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <form @submit.prevent="submit" class="space-y-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Denunciante *</label>
                                <select v-model="form.complainant_id" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-verde-500 focus:ring-verde-500">
                                    <option value="" disabled>Selecciona un denunciante...</option>
                                    <option v-for="c in complainants" :key="c.id" :value="c.id">{{ c.nombre_completo }}</option>
                                </select>
                                <p v-if="form.errors.complainant_id" class="text-sm text-red-600 mt-1">{{ form.errors.complainant_id }}</p>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Colonia *</label>
                                    <input v-model="form.colonia" type="text" required maxlength="255" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-verde-500 focus:ring-verde-500" />
                                    <p v-if="form.errors.colonia" class="text-sm text-red-600 mt-1">{{ form.errors.colonia }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Nombre del Presidente CAC *</label>
                                    <input v-model="form.cac_president_name" type="text" required maxlength="255" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-verde-500 focus:ring-verde-500" />
                                    <p v-if="form.errors.cac_president_name" class="text-sm text-red-600 mt-1">{{ form.errors.cac_president_name }}</p>
                                </div>
                            </div>

                            <div>
                                <div class="flex items-center justify-between mb-3">
                                    <label class="block text-sm font-medium text-gray-700">Evidencias</label>
                                    <button type="button" @click="addEvidence" class="px-3 py-1.5 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 text-sm">
                                        + Agregar otro domicilio
                                    </button>
                                </div>

                                <p v-if="form.errors.evidences" class="text-sm text-red-600 mb-3">{{ form.errors.evidences }}</p>

                                <div v-for="(ev, idx) in form.evidences" :key="idx" class="border border-gray-200 rounded-lg p-4 mb-3 bg-gray-50">
                                    <div class="flex items-center justify-between mb-3">
                                        <span class="text-xs font-medium px-2 py-0.5 rounded-full" :class="ev.is_cac_president ? 'bg-verde-100 text-verde-700' : 'bg-blue-100 text-blue-700'">
                                            {{ ev.is_cac_president ? 'Domicilio Presidente CAC' : `Domicilio ${idx}` }}
                                        </span>
                                        <button v-if="!ev.is_cac_president" type="button" @click="removeEvidence(idx)" class="text-red-500 hover:text-red-700 text-xs">Eliminar</button>
                                    </div>

                                    <div class="grid grid-cols-2 gap-3">
                                        <div>
                                            <label class="block text-xs font-medium text-gray-600 mb-1">Dirección (Calle y Número) *</label>
                                            <input v-model="ev.address" type="text" required maxlength="500" class="block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-verde-500 focus:ring-verde-500" />
                                            <p v-if="form.errors[`evidences.${idx}.address`]" class="text-xs text-red-600 mt-1">{{ form.errors[`evidences.${idx}.address`] }}</p>
                                        </div>
                                        <div>
                                            <label class="block text-xs font-medium text-gray-600 mb-1">Leyenda *</label>
                                            <input v-model="ev.leyenda" type="text" required maxlength="255" class="block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-verde-500 focus:ring-verde-500" />
                                            <p v-if="form.errors[`evidences.${idx}.leyenda`]" class="text-xs text-red-600 mt-1">{{ form.errors[`evidences.${idx}.leyenda`] }}</p>
                                        </div>
                                    </div>

                                    <div class="grid grid-cols-2 gap-3 mt-3">
                                        <div>
                                            <label class="inline-flex items-center gap-2 text-sm text-gray-700">
                                                <input v-model="ev.is_inhabited" type="checkbox" class="rounded border-gray-300 text-verde-600 shadow-sm focus:border-verde-500 focus:ring-verde-500" />
                                                ¿Inmueble habitado?
                                            </label>
                                        </div>
                                        <div>
                                            <label class="block text-xs font-medium text-gray-600 mb-1">Fotografía *</label>
                                            <input type="file" accept="image/*" @change="onPhotoChange(idx, $event)" class="block w-full text-sm text-gray-600 file:mr-3 file:py-1.5 file:px-3 file:rounded file:border-0 file:text-xs file:font-medium file:bg-verde-50 file:text-verde-700 hover:file:bg-verde-100" />
                                            <span v-if="compressing[idx]" class="text-xs text-verde-600">Comprimiendo...</span>
                                            <p v-if="form.errors[`evidences.${idx}.photo`]" class="text-xs text-red-600 mt-1">{{ form.errors[`evidences.${idx}.photo`] }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="flex items-center gap-4 pt-4 border-t">
                                <button type="submit" :disabled="form.processing" class="px-4 py-2 bg-verde-600 text-white rounded-lg hover:bg-verde-700 disabled:opacity-50 text-sm">
                                    Registrar Denuncia
                                </button>
                                <Link :href="route('admin.lonas-old.index')" class="text-sm text-gray-600 hover:text-gray-900">Cancelar</Link>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
