<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    lona: Object,
});
</script>

<template>
    <Head title="Ver Denuncia" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    Detalle de Denuncia
                </h2>
                <div class="flex items-center gap-2">
                    <a
                        v-if="lona.verificada"
                        :href="route('admin.denuncias.lonas.word', lona.id)"
                        class="inline-flex items-center gap-1 px-3 py-1.5 bg-purple-600 text-white rounded-lg hover:bg-purple-700 text-sm transition"
                    >
                        <i class="fa-solid fa-file-word"></i> Word
                    </a>
                    <Link
                        :href="route('admin.denuncias.lonas.index')"
                        class="text-sm text-gray-500 hover:text-gray-700"
                    >
                        <i class="fa-solid fa-arrow-left mr-1"></i> Volver
                    </Link>
                </div>
            </div>
        </template>

        <div class="py-6">
            <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8">
                <!-- Denunciante card -->
                <div class="bg-white shadow-sm rounded-lg p-6 mb-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-3">Datos del Denunciante</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-sm">
                        <div>
                            <span class="text-gray-500">Nombre:</span>
                            <p class="font-medium">{{ lona.denunciante_nombre }}</p>
                        </div>
                        <div>
                            <span class="text-gray-500">Clave de Elector:</span>
                            <p class="font-mono font-medium">{{ lona.denunciante_clave_elector }}</p>
                        </div>
                        <div>
                            <span class="text-gray-500">Domicilio:</span>
                            <p>{{ lona.denunciante_domicilio }}</p>
                        </div>
                        <div>
                            <span class="text-gray-500">Email:</span>
                            <p>{{ lona.denunciante_email || '—' }}</p>
                        </div>
                        <div>
                            <span class="text-gray-500">Registrado por:</span>
                            <p>{{ lona.registered_by }}</p>
                        </div>
                        <div>
                            <span class="text-gray-500">Fecha:</span>
                            <p>{{ lona.dia_numero }} de {{ lona.mes }}</p>
                        </div>
                    </div>
                </div>

                <!-- Evidences / Lonas -->
                <div class="bg-white shadow-sm rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-3">
                        Lonas ({{ lona.evidences?.length ?? 0 }})
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div
                            v-for="ev in lona.evidences"
                            :key="ev.id"
                            class="border rounded-lg p-4"
                        >
                            <div class="flex items-center gap-2 mb-2">
                                <span
                                    class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium"
                                    :class="ev.condicion_inmueble === 'Habitado'
                                        ? 'bg-green-100 text-green-700'
                                        : 'bg-red-100 text-red-700'"
                                >
                                    {{ ev.condicion_inmueble }}
                                </span>
                                <span class="text-xs text-gray-400">Lona #{{ ev.consecutivo }}</span>
                            </div>
                            <p class="text-sm font-medium">{{ ev.calle_y_num }}</p>
                            <p class="text-sm text-gray-500">Col. {{ ev.colonia }}</p>
                            <img
                                :src="'/storage/' + ev.photo_path"
                                class="mt-3 w-full h-48 object-cover rounded-lg border"
                                alt="Evidencia"
                            />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
