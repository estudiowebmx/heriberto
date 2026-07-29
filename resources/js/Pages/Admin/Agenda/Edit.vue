<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const props = defineProps({
    event: Object,
});

const form = useForm({
    date: props.event?.date ?? '',
    title: props.event?.title ?? '',
    description: props.event?.description ?? '',
    location: props.event?.location ?? '',
    type: props.event?.type ?? 'evento',
});

const submit = () => {
    if (props.event) {
        form.put(route('admin.agenda.update', props.event.id));
    } else {
        form.post(route('admin.agenda.store'));
    }
};
</script>

<template>
    <Head :title="event ? 'Editar Evento' : 'Nuevo Evento'" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                {{ event ? 'Editar Evento' : 'Nuevo Evento' }}
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-3xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <form @submit.prevent="submit" class="space-y-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Fecha</label>
                                <input v-model="form.date" type="date" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-guinda-500 focus:ring-guinda-500" />
                                <p v-if="form.errors.date" class="text-sm text-red-600 mt-1">{{ form.errors.date }}</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Título</label>
                                <input v-model="form.title" type="text" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-guinda-500 focus:ring-guinda-500" />
                                <p v-if="form.errors.title" class="text-sm text-red-600 mt-1">{{ form.errors.title }}</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Descripción</label>
                                <textarea v-model="form.description" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-guinda-500 focus:ring-guinda-500"></textarea>
                                <p v-if="form.errors.description" class="text-sm text-red-600 mt-1">{{ form.errors.description }}</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Ubicación</label>
                                <input v-model="form.location" type="text" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-guinda-500 focus:ring-guinda-500" />
                                <p v-if="form.errors.location" class="text-sm text-red-600 mt-1">{{ form.errors.location }}</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Tipo</label>
                                <select v-model="form.type" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-guinda-500 focus:ring-guinda-500">
                                    <option value="evento">Evento</option>
                                    <option value="foro">Foro</option>
                                    <option value="visita">Visita</option>
                                    <option value="entrevista">Entrevista</option>
                                    <option value="reunion">Reunión</option>
                                </select>
                                <p v-if="form.errors.type" class="text-sm text-red-600 mt-1">{{ form.errors.type }}</p>
                            </div>

                            <div class="flex items-center gap-4">
                                <button type="submit" :disabled="form.processing" class="px-4 py-2 bg-guinda-600 text-white rounded-lg hover:bg-guinda-700 disabled:opacity-50 text-sm">
                                    {{ event ? 'Actualizar' : 'Crear' }}
                                </button>
                                <Link :href="route('admin.agenda.index')" class="text-sm text-gray-600 hover:text-gray-900">Cancelar</Link>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
