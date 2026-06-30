<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link } from "@inertiajs/vue3";

defineProps({
    complaint: Object,
});

const downloadWord = (id) => {
    window.open(route("admin.lonas-old.word", id), "_blank");
};
</script>

<template>
    <Head title="Ver Denuncia" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    Detalle de Denuncia
                </h2>
                <div class="flex gap-3">
                    <Link
                        :href="route('admin.lonas-old.edit', complaint.id)"
                        class="px-4 py-2 bg-verde-600 text-white rounded-lg hover:bg-verde-700 text-sm"
                        >Editar</Link
                    >
                    <button
                        @click="downloadWord(complaint.id)"
                        class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 text-sm"
                    >
                        Generar Word
                    </button>
                    <Link
                        :href="route('admin.lonas-old.index')"
                        class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 text-sm"
                        >&larr; Volver</Link
                    >
                </div>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-4xl sm:px-6 lg:px-8 space-y-6">
                <div class="bg-white shadow-sm sm:rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">
                        Datos del Denunciante
                    </h3>
                    <dl class="grid grid-cols-2 gap-4">
                        <div>
                            <dt class="text-sm text-gray-500">
                                Nombre Completo
                            </dt>
                            <dd class="text-sm text-gray-900 font-medium">
                                {{ complaint.complainant?.nombre_completo }}
                            </dd>
                        </div>
                        <div>
                            <dt class="text-sm text-gray-500">
                                Clave de Elector
                            </dt>
                            <dd class="text-sm text-gray-900 font-mono">
                                {{ complaint.complainant?.clave_elector }}
                            </dd>
                        </div>
                        <div>
                            <dt class="text-sm text-gray-500">Domicilio</dt>
                            <dd class="text-sm text-gray-900">
                                {{ complaint.complainant?.domicilio }}
                            </dd>
                        </div>
                        <div>
                            <dt class="text-sm text-gray-500">Email</dt>
                            <dd class="text-sm text-gray-900">
                                {{ complaint.complainant?.email || "—" }}
                            </dd>
                        </div>
                    </dl>
                </div>

                <div class="bg-white shadow-sm sm:rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">
                        Datos de la Denuncia
                    </h3>
                    <dl class="grid grid-cols-2 gap-4">
                        <div>
                            <dt class="text-sm text-gray-500">
                                Presidente CAC
                            </dt>
                            <dd class="text-sm text-gray-900 font-medium">
                                {{ complaint.cac_president_name }}
                            </dd>
                        </div>
                        <div>
                            <dt class="text-sm text-gray-500">Colonia</dt>
                            <dd class="text-sm text-gray-900">
                                {{ complaint.colonia }}
                            </dd>
                        </div>
                    </dl>
                </div>

                <div class="bg-white shadow-sm sm:rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">
                        Evidencias ({{ complaint.evidences?.length ?? 0 }})
                    </h3>

                    <div
                        v-if="!complaint.evidences?.length"
                        class="text-sm text-gray-400"
                    >
                        Sin evidencias registradas.
                    </div>

                    <div
                        v-for="ev in complaint.evidences"
                        :key="ev.id"
                        class="border border-gray-200 rounded-lg p-4 mb-3"
                    >
                        <div class="flex items-center gap-2 mb-3">
                            <span
                                class="text-xs font-medium px-2 py-0.5 rounded-full"
                                :class="
                                    ev.is_cac_president
                                        ? 'bg-verde-100 text-verde-700'
                                        : 'bg-blue-100 text-blue-700'
                                "
                            >
                                {{
                                    ev.is_cac_president
                                        ? "Presidente CAC"
                                        : "Adicional"
                                }}
                            </span>
                            <span
                                class="text-xs px-2 py-0.5 rounded-full"
                                :class="
                                    ev.is_inhabited
                                        ? 'bg-yellow-100 text-yellow-700'
                                        : 'bg-gray-100 text-gray-600'
                                "
                            >
                                {{
                                    ev.is_inhabited ? "Habitado" : "Deshabitado"
                                }}
                            </span>
                        </div>
                        <dl class="grid grid-cols-2 gap-3">
                            <div>
                                <dt class="text-xs text-gray-500">Dirección</dt>
                                <dd class="text-sm text-gray-900">
                                    {{ ev.address }}
                                </dd>
                            </div>
                            <div>
                                <dt class="text-xs text-gray-500">Leyenda</dt>
                                <dd class="text-sm text-gray-900 italic">
                                    "{{ ev.leyenda }}"
                                </dd>
                            </div>
                        </dl>
                        <div class="mt-3">
                            <dt class="text-xs text-gray-500 mb-1">
                                Fotografía
                            </dt>
                            <img
                                :src="'/storage/' + ev.photo_path"
                                :alt="ev.leyenda"
                                class="max-h-48 rounded border border-gray-200 object-cover"
                            />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
