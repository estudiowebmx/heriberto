<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    encuesta: Object,
});
</script>

<template>
    <Head title="Ver Denuncia" />
    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">Detalle de Denuncia</h2>
                <div class="flex items-center gap-2">
                    <a v-if="encuesta.verificada" :href="route('admin.denuncias.encuestas.word', encuesta.id)"
                        class="inline-flex items-center gap-1 px-3 py-1.5 bg-purple-600 text-white rounded-lg hover:bg-purple-700 text-sm transition">
                        <i class="fa-solid fa-file-word"></i> Word
                    </a>
                    <Link :href="route('admin.denuncias.encuestas.index')" class="text-sm text-gray-500 hover:text-gray-700">
                        <i class="fa-solid fa-arrow-left mr-1"></i> Volver
                    </Link>
                </div>
            </div>
        </template>
        <div class="py-6">
            <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8">
                <div class="bg-white shadow-sm rounded-lg p-6 mb-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-3">Datos del Denunciante</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-sm">
                        <div><span class="text-gray-500">Nombre:</span><p class="font-medium">{{ encuesta.complainant?.nombre_completo }}</p></div>
                        <div><span class="text-gray-500">Clave de Elector:</span><p class="font-mono font-medium">{{ encuesta.complainant?.clave_elector }}</p></div>
                        <div><span class="text-gray-500">Domicilio:</span><p>{{ encuesta.complainant?.domicilio }}</p></div>
                        <div><span class="text-gray-500">Email:</span><p>{{ encuesta.complainant?.email || '—' }}</p></div>
                        <div><span class="text-gray-500">Registrado por:</span><p>{{ encuesta.registered_by }}</p></div>
                        <div><span class="text-gray-500">Fecha:</span><p>{{ encuesta.dia_numero }} de {{ encuesta.mes }}</p></div>
                    </div>
                </div>
                <div class="bg-white shadow-sm rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-3">Publicaciones ({{ encuesta.publicaciones?.length ?? 0 }})</h3>
                    <div class="space-y-4">
                        <div v-for="pub in encuesta.publicaciones" :key="pub.id" class="border rounded-lg p-4">
                            <div class="flex items-center gap-2 mb-2">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-700">{{ pub.red_social }}</span>
                                <span class="text-xs text-gray-400">#{{ pub.consecutivo }}</span>
                            </div>
                            <p class="text-sm font-medium">{{ pub.pagina }}</p>
                            <p class="text-sm text-gray-500">{{ pub.fecha_publicacion }}</p>
                            <a :href="pub.enlace" target="_blank" class="text-sm text-emerald-600 hover:underline inline-flex items-center gap-1 mt-1">
                                <i class="fa-solid fa-external-link-alt"></i> {{ pub.enlace }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
