<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import Form from './Partials/Form.vue';

defineProps({
    persona: {
        type: Object,
        default: null,
    },
    gruposCatalogo: {
        type: Array,
        default: () => [],
    },
});
</script>

<template>
    <Head title="Editar Persona" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">Editar Persona</h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-4xl sm:px-6 lg:px-8">
                <Form :persona="persona" :grupos="gruposCatalogo" />
            </div>
        </div>
    </AuthenticatedLayout>
</template>
