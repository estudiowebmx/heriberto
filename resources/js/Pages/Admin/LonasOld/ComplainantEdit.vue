<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const props = defineProps({ complainant: Object });

const form = useForm({
    nombre_completo: props.complainant?.nombre_completo ?? '',
    clave_elector: props.complainant?.clave_elector ?? '',
    domicilio: props.complainant?.domicilio ?? '',
    email: props.complainant?.email ?? '',
    sexo: props.complainant?.sexo ?? 'M',
});

const submit = () => {
    if (props.complainant) {
        form.put(route('admin.lonas-old.complainants.update', props.complainant.id));
    } else {
        form.post(route('admin.lonas-old.complainants.store'));
    }
};
</script>

<template>
    <Head :title="complainant ? 'Editar Denunciante' : 'Nuevo Denunciante'" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                {{ complainant ? 'Editar Denunciante' : 'Nuevo Denunciante' }}
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-3xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <form @submit.prevent="submit" class="space-y-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Nombre Completo *</label>
                                <input v-model="form.nombre_completo" type="text" required maxlength="255" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-verde-500 focus:ring-verde-500" />
                                <p v-if="form.errors.nombre_completo" class="text-sm text-red-600 mt-1">{{ form.errors.nombre_completo }}</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Clave de Elector *</label>
                                <input v-model="form.clave_elector" type="text" required maxlength="30" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-verde-500 focus:ring-verde-500 font-mono" />
                                <p v-if="form.errors.clave_elector" class="text-sm text-red-600 mt-1">{{ form.errors.clave_elector }}</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Domicilio *</label>
                                <input v-model="form.domicilio" type="text" required maxlength="500" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-verde-500 focus:ring-verde-500" />
                                <p v-if="form.errors.domicilio" class="text-sm text-red-600 mt-1">{{ form.errors.domicilio }}</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Email</label>
                                <input v-model="form.email" type="email" maxlength="255" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-verde-500 focus:ring-verde-500" />
                                <p v-if="form.errors.email" class="text-sm text-red-600 mt-1">{{ form.errors.email }}</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Sexo *</label>
                                <select v-model="form.sexo" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-verde-500 focus:ring-verde-500">
                                    <option value="M">Masculino</option>
                                    <option value="F">Femenino</option>
                                </select>
                                <p v-if="form.errors.sexo" class="text-sm text-red-600 mt-1">{{ form.errors.sexo }}</p>
                            </div>

                            <div class="flex items-center gap-4">
                                <button type="submit" :disabled="form.processing" class="px-4 py-2 bg-verde-600 text-white rounded-lg hover:bg-verde-700 disabled:opacity-50 text-sm">
                                    {{ complainant ? 'Actualizar' : 'Registrar' }}
                                </button>
                                <Link :href="route('admin.lonas-old.complainants.index')" class="text-sm text-gray-600 hover:text-gray-900">Cancelar</Link>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
