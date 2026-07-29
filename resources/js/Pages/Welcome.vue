<script setup>
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    canLogin: Boolean,
    canRegister: Boolean,
    laravelVersion: String,
    phpVersion: String,
});
</script>

<template>
    <Head title="Heriberto Castañeda 2027" />

    <div class="min-h-screen flex flex-col items-center justify-center bg-gradient-to-br from-guinda-50 via-white to-guinda-100">
        <div class="text-center px-6">
            <img
                src="/img/portada.png"
                alt="Heriberto Castañeda 2027"
                class="mx-auto w-full max-w-md rounded-2xl shadow-xl mb-8"
            />

            <h1 class="text-4xl font-bold text-gray-800 mb-2">Heriberto Castañeda</h1>
            <p class="text-lg text-gray-600 mb-8">Plataforma Electoral 2027</p>

            <div class="flex gap-4 justify-center flex-wrap">
                <Link
                    v-if="$page.props.auth.user?.role === 'admin'"
                    :href="route('dashboard')"
                    class="px-6 py-3 bg-guinda-600 text-white rounded-lg hover:bg-guinda-700 text-lg font-semibold shadow-lg transition"
                >
                    <i class="fa-solid fa-chart-line mr-2"></i> Dashboard
                </Link>
                <Link
                    v-else-if="$page.props.auth.user"
                    :href="route('admin.personas.index')"
                    class="px-6 py-3 bg-guinda-600 text-white rounded-lg hover:bg-guinda-700 text-lg font-semibold shadow-lg transition"
                >
                    <i class="fa-solid fa-address-book mr-2"></i> Personas
                </Link>
                <template v-else>
                    <Link
                        :href="route('login')"
                        class="px-6 py-3 bg-guinda-600 text-white rounded-lg hover:bg-guinda-700 text-lg font-semibold shadow-lg transition"
                    >
                        <i class="fa-solid fa-right-to-bracket mr-2"></i> Entrar a la plataforma
                    </Link>
                </template>
            </div>

            <footer class="mt-12 text-sm text-gray-500">
                &copy; 2026 Heriberto Castañeda. Todos los derechos reservados.
            </footer>
        </div>
    </div>
</template>
