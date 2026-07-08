<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';

defineProps({
    complainants: Object,
});

const deleteComplainant = (id) => {
    if (confirm('¿Eliminar este denunciante?')) {
        router.delete(route('admin.denunciantes.destroy', id));
    }
};
</script>

<template>
    <Head title="Catálogo de Denunciantes" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">Catálogo de Denunciantes</h2>
                <div class="flex gap-3">
                    
                    <Link :href="route('admin.denunciantes.create')" class="px-4 py-2 bg-verde-600 text-white rounded-lg hover:bg-verde-700 text-sm">
                        + Nuevo Denunciante
                    </Link>
                </div>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8 space-y-4">
                <div class="bg-white shadow-sm sm:rounded-lg px-4 py-2 flex items-center justify-between">
                    <span class="text-sm text-gray-500">{{ complainants.total }} denunciantes registrados</span>
                </div>

                <div class="overflow-x-auto bg-white shadow-sm sm:rounded-lg">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-2 py-3 text-center text-xs font-medium text-gray-500 uppercase w-[60px]">Denuncias</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nombre Completo</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Clave de Elector</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Domicilio</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            <tr v-if="complainants.data.length === 0">
                                <td colspan="6" class="px-4 py-8 text-center text-sm text-gray-400">Sin denunciantes registrados</td>
                            </tr>
                            <tr v-for="c in complainants.data" :key="c.id">
                                <td class="px-2 py-3 text-center" :title="((c.complaints_count || 0) + (c.lonas_count || 0)) + ' denuncias registradas'">
                                    <span class="inline-flex items-center justify-center w-8 h-8 rounded-full text-xs font-bold"
                                        :class="((c.complaints_count || 0) + (c.lonas_count || 0)) > 0 ? 'bg-amber-100 text-amber-700' : 'bg-gray-100 text-gray-400'">
                                        {{ (c.complaints_count || 0) + (c.lonas_count || 0) }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-900">{{ c.nombre_completo }}</td>
                                <td class="px-4 py-3 text-sm text-gray-600 font-mono">{{ c.clave_elector }}</td>
                                <td class="px-4 py-3 text-sm text-gray-600">{{ c.domicilio }}</td>
                                <td class="px-4 py-3 text-sm text-gray-600">{{ c.email || '—' }}</td>
                                <td class="px-4 py-3 text-right text-sm whitespace-nowrap">
                                    <Link :href="route('admin.denunciantes.edit', c.id)" class="inline-flex items-center gap-1 text-verde-600 hover:text-verde-700 mr-3"><i class="fa-solid fa-pen-to-square"></i> Editar</Link>
                                    <button @click="deleteComplainant(c.id)" class="inline-flex items-center gap-1 text-red-600 hover:text-red-900"><i class="fa-solid fa-trash"></i> Eliminar</button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div v-if="complainants.total > 20" class="bg-white shadow-sm sm:rounded-lg p-4">
                    <div class="flex items-center justify-between">
                        <div class="text-sm text-gray-500">
                            Mostrando {{ complainants.from }}–{{ complainants.to }} de {{ complainants.total }}
                        </div>
                        <div class="flex gap-1">
                            <Link
                                v-for="link in complainants.links"
                                :key="link.label"
                                :href="link.url || '#'"
                                v-html="link.label"
                                :class="[
                                    'px-3 py-1.5 text-sm rounded-md transition-colors',
                                    link.active ? 'bg-verde-600 text-white' : link.url ? 'text-gray-600 hover:bg-gray-100' : 'text-gray-300 cursor-default',
                                ]"
                                preserve-scroll
                            />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
