<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';

const props = defineProps({
    events: Object,
});

const deleteEvent = (id) => {
    if (confirm('¿Eliminar este evento?')) {
        router.delete(route('admin.agenda.destroy', id));
    }
};
</script>

<template>
    <Head title="Agenda" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    Agenda
                </h2>
                    <Link :href="route('admin.agenda.create')" class="inline-flex items-center gap-2 px-4 py-2 bg-guinda-600 text-white rounded-lg hover:bg-guinda-700 text-sm">
                        <i class="fa-solid fa-plus"></i> Nuevo Evento
                    </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fecha</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Título</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Ubicación</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tipo</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            <tr v-for="event in events.data" :key="event.id">
                                <td class="px-6 py-4 text-sm text-gray-500">{{ event.date }}</td>
                                <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ event.title }}</td>
                                <td class="px-6 py-4 text-sm text-gray-600">{{ event.location }}</td>
                                <td class="px-6 py-4 text-sm">
                                    <span class="px-2 py-1 text-xs rounded-full" :class="{
                                        'bg-blue-100 text-blue-800': event.type === 'foro',
                                        'bg-guinda-100 text-guinda-800': event.type === 'visita',
                                        'bg-yellow-100 text-yellow-800': event.type === 'entrevista',
                                        'bg-gray-100 text-gray-800': !['foro','visita','entrevista'].includes(event.type),
                                    }">{{ event.type }}</span>
                                </td>
                                <td class="px-6 py-4 text-right text-sm whitespace-nowrap">
                                    <Link :href="route('admin.agenda.edit', event.id)" class="inline-flex items-center gap-1 text-guinda-600 hover:text-guinda-700 mr-3"><i class="fa-solid fa-pen-to-square"></i> Editar</Link>
                                    <button @click="deleteEvent(event.id)" class="inline-flex items-center gap-1 text-red-600 hover:text-red-900"><i class="fa-solid fa-trash"></i> Eliminar</button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
