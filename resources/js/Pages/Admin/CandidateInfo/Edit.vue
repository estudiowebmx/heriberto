<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const props = defineProps({
    info: Object,
});

const form = useForm({
    section: props.info?.section ?? '',
    title: props.info?.title ?? '',
    content: props.info?.content ?? '',
    order: props.info?.order ?? 0,
});

const submit = () => {
    if (props.info) {
        form.put(route('admin.info.update', props.info.id));
    } else {
        form.post(route('admin.info.store'));
    }
};
</script>

<template>
    <Head :title="info ? 'Editar Información' : 'Nueva Información'" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                {{ info ? 'Editar Información' : 'Nueva Información' }}
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-3xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <form @submit.prevent="submit" class="space-y-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Sección</label>
                                <select v-model="form.section" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-verde-500 focus:ring-verde-500">
                                    <option value="biografia">Biografía</option>
                                    <option value="vision">Visión</option>
                                    <option value="propuestas">Propuestas</option>
                                    <option value="mensaje">Mensaje</option>
                                    <option value="otro">Otro</option>
                                </select>
                                <p v-if="form.errors.section" class="text-sm text-red-600 mt-1">{{ form.errors.section }}</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Título</label>
                                <input v-model="form.title" type="text" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-verde-500 focus:ring-verde-500" />
                                <p v-if="form.errors.title" class="text-sm text-red-600 mt-1">{{ form.errors.title }}</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Contenido</label>
                                <textarea v-model="form.content" rows="6" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-verde-500 focus:ring-verde-500"></textarea>
                                <p v-if="form.errors.content" class="text-sm text-red-600 mt-1">{{ form.errors.content }}</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Orden</label>
                                <input v-model="form.order" type="number" min="0" class="mt-1 block w-24 rounded-md border-gray-300 shadow-sm focus:border-verde-500 focus:ring-verde-500" />
                                <p v-if="form.errors.order" class="text-sm text-red-600 mt-1">{{ form.errors.order }}</p>
                            </div>

                            <div class="flex items-center gap-4">
                                <button type="submit" :disabled="form.processing" class="px-4 py-2 bg-verde-600 text-white rounded-lg hover:bg-verde-700 disabled:opacity-50 text-sm">
                                    {{ info ? 'Actualizar' : 'Crear' }}
                                </button>
                                <Link :href="route('admin.info.index')" class="text-sm text-gray-600 hover:text-gray-900">Cancelar</Link>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
