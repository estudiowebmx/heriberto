<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
</script>

<template>
    <Head title="Encuestas" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Encuestas
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6 text-center text-gray-500">
                        <i class="fa-solid fa-square-poll-horizontal text-6xl text-emerald-400 mb-4 block"></i>
                        <p class="text-lg">Módulo en construcción</p>
                        <Link
                            :href="route('admin.denuncias.index')"
                            class="mt-4 inline-flex items-center gap-1 text-sm text-emerald-600 hover:text-emerald-800"
                        >
                            <i class="fa-solid fa-arrow-left"></i> Volver a Denuncias
                        </Link>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
