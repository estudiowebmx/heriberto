<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import axios from 'axios';

const props = defineProps({ persona: Object });

const form = useForm({
    nombre: props.persona?.nombre ?? '',
    apellido_paterno: props.persona?.apellido_paterno ?? '',
    apellido_materno: props.persona?.apellido_materno ?? '',
    telefono: props.persona?.telefono ?? '',
    seccion: props.persona?.seccion ?? '',
    municipio: props.persona?.municipio ?? '',
    distrito_local: props.persona?.distrito_local ?? '',
    distrito_federal: props.persona?.distrito_federal ?? '',
    demarcacion: props.persona?.demarcacion ?? '',
    dmunicipal: props.persona?.demarcacion ? `${props.persona?.municipio || ''} - Demarcación ${props.persona?.demarcacion}` : '',
});

const buscarSeccion = async () => {
    const val = form.seccion?.trim();
    if (!val) {
        form.municipio = '';
        form.distrito_local = '';
        form.distrito_federal = '';
        return;
    }
    try {
        const res = await axios.get('/api/seccion', { params: { q: val } });
        form.municipio = res.data.municipio;
        form.distrito_local = res.data.distrito_local;
        form.distrito_federal = res.data.distrito_federal;
        form.demarcacion = res.data.demarcacion || '';
        form.dmunicipal = res.data.dmunicipal || '';
    } catch {
        form.municipio = '';
        form.distrito_local = '';
        form.distrito_federal = '';
        form.demarcacion = '';
        form.dmunicipal = '';
    }
};

const submit = () => {
    if (props.persona) {
        form.put(route('admin.personas.update', props.persona.id));
    } else {
        form.post(route('admin.personas.store'));
    }
};
</script>

<template>
    <Head :title="persona ? 'Editar Persona' : 'Nueva Persona'" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                {{ persona ? 'Editar Persona' : 'Nueva Persona' }}
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-3xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <form @submit.prevent="submit" class="space-y-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Nombre *</label>
                                <input v-model="form.nombre" type="text" required maxlength="100" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-guinda-500 focus:ring-guinda-500" />
                                <p v-if="form.errors.nombre" class="text-sm text-red-600 mt-1">{{ form.errors.nombre }}</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Apellido Paterno *</label>
                                <input v-model="form.apellido_paterno" type="text" required maxlength="100" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-guinda-500 focus:ring-guinda-500" />
                                <p v-if="form.errors.apellido_paterno" class="text-sm text-red-600 mt-1">{{ form.errors.apellido_paterno }}</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Apellido Materno</label>
                                <input v-model="form.apellido_materno" type="text" maxlength="100" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-guinda-500 focus:ring-guinda-500" />
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Teléfono * (10 dígitos)</label>
                                <input v-model="form.telefono" type="tel" required pattern="[0-9]{10}" maxlength="10" placeholder="5512345678" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-guinda-500 focus:ring-guinda-500" />
                                <p v-if="form.errors.telefono" class="text-sm text-red-600 mt-1">{{ form.errors.telefono }}</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Sección Electoral</label>
                                <div class="flex gap-2 mt-1">
                                    <input v-model="form.seccion" type="text" maxlength="20" class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-guinda-500 focus:ring-guinda-500" placeholder="Ingresa la sección" />
                                    <button type="button" @click="buscarSeccion" class="px-3 py-2 bg-gray-100 text-gray-600 rounded-md hover:bg-gray-200 text-sm">Buscar</button>
                                </div>
                            </div>

                            <div v-if="form.municipio" class="grid grid-cols-3 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-500 mb-1">Municipio</label>
                                    <input :value="form.municipio" readonly class="w-full rounded-md border border-gray-200 bg-gray-50 px-3 py-2 text-sm text-gray-700" />
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-500 mb-1">Distrito Local</label>
                                    <input :value="form.distrito_local" readonly class="w-full rounded-md border border-gray-200 bg-gray-50 px-3 py-2 text-sm text-gray-700" />
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-500 mb-1">Distrito Federal</label>
                                    <input :value="form.distrito_federal" readonly class="w-full rounded-md border border-gray-200 bg-gray-50 px-3 py-2 text-sm text-gray-700" />
                                </div>
                            </div>
                            <div v-if="form.dmunicipal">
                                <label class="block text-sm font-medium text-gray-500 mb-1">Demarcación Municipal</label>
                                <input :value="form.dmunicipal" readonly class="w-full rounded-md border border-gray-200 bg-gray-50 px-3 py-2 text-sm text-gray-700" />
                            </div>

                            <div class="flex items-center gap-4">
                                <button type="submit" :disabled="form.processing" class="px-4 py-2 bg-guinda-600 text-white rounded-lg hover:bg-guinda-700 disabled:opacity-50 text-sm">
                                    {{ persona ? 'Actualizar' : 'Registrar' }}
                                </button>
                                <Link :href="route('admin.personas.index')" class="text-sm text-gray-600 hover:text-gray-900">Cancelar</Link>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
