<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';

const props = defineProps({
    encuestas: Object,
});

function confirmDelete(id, name) {
    if (confirm(`¿Eliminar la denuncia de "${name}"?`)) {
        router.delete(route('admin.denuncias.encuestas.destroy', id));
    }
}
</script>

<template>
    <Head title="Encuestas" />
    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">Denuncias de Encuestas</h2>
                <Link :href="route('admin.denuncias.encuestas.create')"
                    class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 text-sm font-medium transition">
                    <i class="fa-solid fa-plus"></i> Nueva Denuncia
                </Link>
            </div>
        </template>

        <div class="py-6">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-4 sm:p-6">
                        <table class="min-w-full divide-y divide-gray-200 text-sm">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-3 py-3 text-center font-semibold text-gray-700 w-10"></th>
                                    <th class="px-3 py-3 text-left font-semibold text-gray-700">Denunciante</th>
                                    <th class="px-3 py-3 text-left font-semibold text-gray-700 hidden md:table-cell">Clave Elector</th>
                                    <th class="px-3 py-3 text-left font-semibold text-gray-700">Publicaciones</th>
                                    <th class="px-3 py-3 text-left font-semibold text-gray-700 hidden lg:table-cell">Registrado por</th>
                                    <th class="px-3 py-3 text-left font-semibold text-gray-700 hidden sm:table-cell">Fecha</th>
                                    <th class="px-3 py-3 text-right font-semibold text-gray-700">Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                <tr v-for="e in encuestas.data" :key="e.id" class="hover:bg-gray-50">
                                    <td class="px-3 py-3 whitespace-nowrap text-center">
                                        <i v-if="e.verificada" class="fa-solid fa-circle-check text-green-500 text-lg" title="Verificada"></i>
                                        <i v-else class="fa-solid fa-circle-xmark text-red-400 text-lg" title="No verificada"></i>
                                    </td>
                                    <td class="px-3 py-3 whitespace-nowrap font-medium text-gray-900">{{ e.complainant?.nombre_completo }}</td>
                                    <td class="px-3 py-3 whitespace-nowrap text-gray-500 hidden md:table-cell font-mono text-xs">{{ e.complainant?.clave_elector }}</td>
                                    <td class="px-3 py-3 whitespace-nowrap text-gray-600">
                                        <span class="inline-flex items-center gap-1 bg-emerald-100 text-emerald-800 text-xs font-medium px-2 py-0.5 rounded-full">{{ e.publicaciones?.length ?? 0 }}</span>
                                    </td>
                                    <td class="px-3 py-3 whitespace-nowrap text-gray-500 hidden lg:table-cell">{{ e.registered_by }}</td>
                                    <td class="px-3 py-3 whitespace-nowrap text-gray-500 hidden sm:table-cell">{{ e.dia_numero }} de {{ e.mes }}</td>
                                    <td class="px-3 py-3 whitespace-nowrap text-right">
                                        <div class="flex items-center justify-end gap-2">
                                            <Link :href="route('admin.denuncias.encuestas.show', e.id)" class="text-blue-600 hover:text-blue-800 text-xs" title="Ver"><i class="fa-solid fa-eye"></i></Link>
                                            <Link v-if="!e.verificada" :href="route('admin.denuncias.encuestas.verify', e.id)" class="text-emerald-600 hover:text-emerald-800 text-xs" title="Verificar"><i class="fa-solid fa-clipboard-check"></i></Link>
                                            <Link v-if="!e.verificada" :href="route('admin.denuncias.encuestas.edit', e.id)" class="text-green-600 hover:text-green-800 text-xs" title="Editar"><i class="fa-solid fa-pen-to-square"></i></Link>
                                            <a v-if="e.verificada" :href="route('admin.denuncias.encuestas.word', e.id)" class="text-purple-600 hover:text-purple-800 text-xs" title="Descargar Word"><i class="fa-solid fa-file-word"></i></a>
                                            <button @click="confirmDelete(e.id, e.complainant?.nombre_completo)" class="text-red-500 hover:text-red-700 text-xs" title="Eliminar"><i class="fa-solid fa-trash"></i></button>
                                        </div>
                                    </td>
                                </tr>
                                <tr v-if="encuestas.data.length === 0">
                                    <td colspan="7" class="px-3 py-8 text-center text-gray-400">No hay denuncias registradas.</td>
                                </tr>
                            </tbody>
                        </table>
                        <div v-if="encuestas.links" class="mt-4 flex justify-center">
                            <div class="flex gap-1 text-sm">
                                <Link v-for="link in encuestas.links" :key="link.label" :href="link.url || '#'" class="px-3 py-1.5 rounded border"
                                    :class="{ 'bg-emerald-600 text-white border-emerald-600': link.active, 'bg-white text-gray-600 border-gray-300 hover:bg-gray-50': !link.active, 'pointer-events-none opacity-40': !link.url }"
                                    v-html="link.label" />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
