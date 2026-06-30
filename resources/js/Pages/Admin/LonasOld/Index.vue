<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';

const props = defineProps({
    complaints: Object,
});

const deleteComplaint = (id) => {
    if (confirm('¿Eliminar esta denuncia?')) {
        router.delete(route('admin.lonas-old.destroy', id));
    }
};

const downloadWord = (id) => {
    window.open(route('admin.lonas-old.word', id), '_blank');
};

const downloadPdf = (id) => {
    window.open(route('admin.lonas-old.pdf', id), '_blank');
};
</script>

<template>
    <Head title="Denuncias Electorales" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">Denuncias Electorales</h2>
                <div class="flex gap-3">
                    <Link v-if="$page.props.auth.user?.role === 'admin'" :href="route('admin.lonas-old.complainants.index')" class="inline-flex items-center gap-2 px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 text-sm">
                        <i class="fa-solid fa-users"></i>
                        Catálogo de Denunciantes
                    </Link>
                    <Link :href="route('admin.lonas-old.create')" class="inline-flex items-center gap-2 px-4 py-2 bg-verde-600 text-white rounded-lg hover:bg-verde-700 text-sm">
                        <i class="fa-solid fa-plus"></i>
                        Nueva Denuncia
                    </Link>
                </div>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8 space-y-4">
                <div class="bg-white shadow-sm sm:rounded-lg px-4 py-2 flex items-center justify-between">
                    <span class="text-sm text-gray-500">{{ complaints.total }} denuncias registradas</span>
                </div>

                <div class="overflow-x-auto bg-white shadow-sm sm:rounded-lg">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Denunciante</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Presidente CAC</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Colonia</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Evidencias</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fecha</th>
                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            <tr v-if="complaints.data.length === 0">
                                <td colspan="6" class="px-4 py-8 text-center text-sm text-gray-400">Sin denuncias registradas</td>
                            </tr>
                            <tr v-for="c in complaints.data" :key="c.id">
                                <td class="px-4 py-3 text-sm text-gray-900">{{ c.complainant?.nombre_completo }}</td>
                                <td class="px-4 py-3 text-sm text-gray-600">{{ c.cac_president_name }}</td>
                                <td class="px-4 py-3 text-sm text-gray-600">{{ c.colonia }}</td>
                                <td class="px-4 py-3 text-sm text-gray-600">{{ c.evidences?.length ?? 0 }}</td>
                                <td class="px-4 py-3 text-sm text-gray-500">{{ new Date(c.created_at).toLocaleDateString('es-MX') }}</td>
                                <td class="px-4 py-3 text-right text-sm whitespace-nowrap space-x-1">
                                    <Link :href="route('admin.lonas-old.show', c.id)" class="inline-flex items-center gap-1 text-blue-600 hover:text-blue-800" title="Ver">
                                        <i class="fa-solid fa-eye"></i> Ver
                                    </Link>
                                    <Link :href="route('admin.lonas-old.edit', c.id)" class="inline-flex items-center gap-1 text-verde-600 hover:text-verde-700" title="Editar">
                                        <i class="fa-solid fa-pen-to-square"></i> Editar
                                    </Link>
                                    <button @click="downloadWord(c.id)" class="inline-flex items-center gap-1 text-purple-600 hover:text-purple-800" title="Descargar Word">
                                        <i class="fa-solid fa-file-word"></i> Word
                                    </button>
                                    <!-- <button @click="downloadPdf(c.id)" class="inline-flex items-center gap-1 text-orange-600 hover:text-orange-800" title="Descargar PDF">
                                        <i class="fa-solid fa-file-pdf"></i> PDF
                                    </button> -->
                                    <button @click="deleteComplaint(c.id)" class="inline-flex items-center gap-1 text-red-600 hover:text-red-900" title="Eliminar">
                                        <i class="fa-solid fa-trash"></i> Eliminar
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div v-if="complaints.total > 20" class="bg-white shadow-sm sm:rounded-lg p-4">
                    <div class="flex items-center justify-between">
                        <div class="text-sm text-gray-500">
                            Mostrando {{ complaints.from }}–{{ complaints.to }} de {{ complaints.total }}
                        </div>
                        <div class="flex gap-1">
                            <Link
                                v-for="link in complaints.links"
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
