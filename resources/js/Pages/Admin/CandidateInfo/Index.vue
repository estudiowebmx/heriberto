<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';

const props = defineProps({
    sections: Object,
});

const deleteInfo = (id) => {
    if (confirm('¿Eliminar esta información?')) {
        router.delete(route('admin.info.destroy', id));
    }
};
</script>

<template>
    <Head title="Información de la Candidata" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    Información de la Candidata
                </h2>
                    <Link :href="route('admin.info.create')" class="inline-flex items-center gap-2 px-4 py-2 bg-guinda-600 text-white rounded-lg hover:bg-guinda-700 text-sm">
                        <i class="fa-solid fa-plus"></i> Nueva Información
                    </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div v-for="(items, section) in sections" :key="section" class="mb-8">
                    <h3 class="text-lg font-bold text-gray-900 uppercase mb-4">{{ section }}</h3>
                    <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Orden</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Título</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Contenido</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                <tr v-for="item in items" :key="item.id">
                                    <td class="px-6 py-4 text-sm text-gray-500">{{ item.order }}</td>
                                    <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ item.title }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-600 max-w-md truncate">{{ item.content }}</td>
                                    <td class="px-6 py-4 text-right text-sm whitespace-nowrap">
                                        <Link :href="route('admin.info.edit', item.id)" class="inline-flex items-center gap-1 text-guinda-600 hover:text-guinda-700 mr-3"><i class="fa-solid fa-pen-to-square"></i> Editar</Link>
                                        <button @click="deleteInfo(item.id)" class="inline-flex items-center gap-1 text-red-600 hover:text-red-900"><i class="fa-solid fa-trash"></i> Eliminar</button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
