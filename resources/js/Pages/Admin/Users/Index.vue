<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';

defineProps({
    users: Object,
});

const deleteUser = (id) => {
    if (confirm('¿Eliminar este usuario?')) {
        router.delete(route('admin.users.destroy', id));
    }
};
</script>

<template>
    <Head title="Usuarios del Sistema" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">Usuarios del Sistema</h2>
                <Link :href="route('admin.users.create')" class="inline-flex items-center gap-2 px-4 py-2 bg-verde-600 text-white rounded-lg hover:bg-verde-700 text-sm">
                    <i class="fa-solid fa-plus"></i> Nuevo Usuario
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-5xl sm:px-6 lg:px-8 space-y-4">
                <div class="bg-white shadow-sm sm:rounded-lg px-4 py-2 flex items-center justify-between">
                    <span class="text-sm text-gray-500">{{ users.total }} usuarios</span>
                </div>

                <div class="overflow-x-auto bg-white shadow-sm sm:rounded-lg">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nombre</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Rol</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fecha</th>
                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            <tr v-if="users.data.length === 0">
                                <td colspan="5" class="px-4 py-8 text-center text-sm text-gray-400">Sin usuarios</td>
                            </tr>
                            <tr v-for="u in users.data" :key="u.id">
                                <td class="px-4 py-3 text-sm text-gray-900">{{ u.name }}</td>
                                <td class="px-4 py-3 text-sm text-gray-600">{{ u.email }}</td>
                                <td class="px-4 py-3 text-sm">
                                    <span class="px-2 py-0.5 rounded-full text-xs font-medium" :class="u.role === 'admin' ? 'bg-purple-100 text-purple-700' : 'bg-blue-100 text-blue-700'">
                                        {{ u.role === 'admin' ? 'Administrador' : 'Colaborador' }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-500">{{ new Date(u.created_at).toLocaleDateString('es-MX') }}</td>
                                <td class="px-4 py-3 text-right text-sm whitespace-nowrap">
                                    <Link :href="route('admin.users.edit', u.id)" class="inline-flex items-center gap-1 text-verde-600 hover:text-verde-700 mr-2">
                                        <i class="fa-solid fa-pen-to-square"></i> Editar
                                    </Link>
                                    <button @click="deleteUser(u.id)" class="inline-flex items-center gap-1 text-red-600 hover:text-red-900">
                                        <i class="fa-solid fa-trash"></i> Eliminar
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div v-if="users.total > 20" class="bg-white shadow-sm sm:rounded-lg p-4">
                    <div class="flex items-center justify-between">
                        <div class="text-sm text-gray-500">
                            Mostrando {{ users.from }}–{{ users.to }} de {{ users.total }}
                        </div>
                        <div class="flex gap-1">
                            <Link
                                v-for="link in users.links"
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
