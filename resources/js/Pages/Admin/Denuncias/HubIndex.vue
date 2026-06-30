<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

const cards = [
    {
        title: 'Lonas',
        description: 'Gestión de denuncias por colocación de lonas. Es necesario contar con domicilio y fotografía de la lona.',
        image: '/lonas.jpg',
        route: 'admin.denuncias.lonas.index',
        color: 'from-amber-500 to-orange-600',
    },
    {
        title: 'Servidores Públicos',
        description: 'Registro de denuncias contra servidores públicos por actos anticipados de campaña.',
        image: '/servidores.jpg',
        route: 'admin.denuncias.servidores.index',
        color: 'from-blue-500 to-indigo-600',
    },
    {
        title: 'Encuestas',
        description: 'Captura de denuncias por encuestas de percepción ciudadana y estadísticas tendenciosas.',
        image: '/encuesta.jpg',
        route: 'admin.denuncias.encuestas.index',
        color: 'from-emerald-500 to-teal-600',
    },
    {
        title: 'Sonríe',
        description: 'Registro de denuncias por el programa Sonríe.',
        image: '/sonrie.jpg',
        route: 'admin.denuncias.sonrie.index',
        color: 'from-pink-500 to-rose-600',
    },
];
</script>

<template>
    <Head title="Denuncias" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Tipos de Denuncia
            </h2>
        </template>

        <div class="py-8">
            <div class="mx-auto max-w-5xl px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <Link
                        v-for="card in cards"
                        :key="card.title"
                        :href="route(card.route)"
                        class="group block rounded-xl shadow-md hover:shadow-xl transition-shadow duration-300 overflow-hidden bg-white"
                    >
                        <div
                            class="h-40 w-full bg-gradient-to-r"
                            :class="card.color"
                        >
                            <img
                                :src="card.image"
                                :alt="card.title"
                                class="h-full w-full object-cover opacity-80 group-hover:opacity-100 transition-opacity"
                            />
                        </div>
                        <div class="p-5">
                            <h3 class="text-lg font-bold text-gray-900">{{ card.title }}</h3>
                            <p class="mt-1 text-sm text-gray-500">{{ card.description }}</p>
                        </div>
                    </Link>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
