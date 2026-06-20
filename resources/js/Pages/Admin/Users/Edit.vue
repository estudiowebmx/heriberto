<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const props = defineProps({ user: Object });

const form = useForm({
    name: props.user?.name ?? '',
    email: props.user?.email ?? '',
    password: '',
    role: props.user?.role ?? 'colaborador',
});

const submit = () => {
    if (props.user) {
        form.put(route('admin.users.update', props.user.id));
    } else {
        form.post(route('admin.users.store'));
    }
};
</script>

<template>
    <Head :title="user ? 'Editar Usuario' : 'Nuevo Usuario'" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                {{ user ? 'Editar Usuario' : 'Nuevo Usuario' }}
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-2xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <form @submit.prevent="submit" class="space-y-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Nombre *</label>
                                <input v-model="form.name" type="text" required maxlength="255" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-verde-500 focus:ring-verde-500" />
                                <p v-if="form.errors.name" class="text-sm text-red-600 mt-1">{{ form.errors.name }}</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Email *</label>
                                <input v-model="form.email" type="email" required maxlength="255" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-verde-500 focus:ring-verde-500" />
                                <p v-if="form.errors.email" class="text-sm text-red-600 mt-1">{{ form.errors.email }}</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Contraseña {{ user ? '(dejar vacío para mantener)' : '*' }}</label>
                                <input v-model="form.password" type="password" :required="!user" minlength="6" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-verde-500 focus:ring-verde-500" />
                                <p v-if="form.errors.password" class="text-sm text-red-600 mt-1">{{ form.errors.password }}</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Rol *</label>
                                <select v-model="form.role" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-verde-500 focus:ring-verde-500">
                                    <option value="admin">Administrador</option>
                                    <option value="colaborador">Colaborador</option>
                                </select>
                                <p v-if="form.errors.role" class="text-sm text-red-600 mt-1">{{ form.errors.role }}</p>
                            </div>

                            <div class="flex items-center gap-4">
                                <button type="submit" :disabled="form.processing" class="px-4 py-2 bg-verde-600 text-white rounded-lg hover:bg-verde-700 disabled:opacity-50 text-sm">
                                    {{ user ? 'Actualizar' : 'Crear' }}
                                </button>
                                <Link :href="route('admin.users.index')" class="text-sm text-gray-600 hover:text-gray-900">Cancelar</Link>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
