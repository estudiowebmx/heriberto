<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { reactive, watch, ref } from 'vue';

const props = defineProps({
    personas: Object,
    filters: Object,
});

const filtros = reactive({
    nombre: props.filters?.nombre ?? '',
    telefono: props.filters?.telefono ?? '',
    seccion: props.filters?.seccion ?? '',
    municipio: props.filters?.municipio ?? '',
    distrito_local: props.filters?.distrito_local ?? '',
    distrito_federal: props.filters?.distrito_federal ?? '',
    demarcacion: props.filters?.demarcacion ?? '',
});

const timers = {};
const buscar = (campo) => {
    clearTimeout(timers[campo]);
    timers[campo] = setTimeout(() => {
        router.get(route('admin.personas.index'), { ...filtros }, {
            preserveState: true,
            replace: true,
            preserveScroll: true,
        });
    }, 400);
};

const limpiar = () => {
    Object.keys(filtros).forEach(k => filtros[k] = '');
    router.get(route('admin.personas.index'), {}, {
        preserveState: true,
        preserveScroll: true,
    });
};

const deletePersona = (id) => {
    if (confirm('¿Eliminar esta persona?')) {
        router.delete(route('admin.personas.destroy', id));
    }
};

const activeFilters = () => Object.values(filtros).some(v => v !== '');
</script>

<template>
    <Head title="Personas Registradas" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">Personas Registradas</h2>
                <Link :href="route('admin.personas.create')" class="px-4 py-2 bg-verde-600 text-white rounded-lg hover:bg-verde-700 text-sm">+ Nueva Persona</Link>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8 space-y-4">
                <!-- Filters bar -->
                <div class="bg-white shadow-sm sm:rounded-lg px-4 py-2 flex items-center justify-between">
                    <span class="text-sm text-gray-500">{{ personas.total }} registros</span>
                    <button
                        v-if="activeFilters()"
                        @click="limpiar"
                        class="text-sm text-verde-600 hover:text-verde-700"
                    >Limpiar filtros</button>
                </div>

                <!-- Table -->
                <div class="overflow-x-auto bg-white shadow-sm sm:rounded-lg">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-2 py-2 text-left text-xs font-medium text-gray-500 uppercase w-[140px]">Nombre</th>
                                <th class="px-2 py-2 text-left text-xs font-medium text-gray-500 uppercase w-[110px]">Teléfono</th>
                                <th class="px-2 py-2 text-left text-xs font-medium text-gray-500 uppercase w-[80px]">Sección</th>
                                <th class="px-2 py-2 text-left text-xs font-medium text-gray-500 uppercase w-[140px]">Municipio</th>
                                <th class="px-2 py-2 text-left text-xs font-medium text-gray-500 uppercase w-[70px]">D. Local</th>
                                <th class="px-2 py-2 text-left text-xs font-medium text-gray-500 uppercase w-[80px]">D. Federal</th>
                                <th class="px-2 py-2 text-left text-xs font-medium text-gray-500 uppercase w-[80px]">Demarc.</th>
                                <th class="px-2 py-2 text-left text-xs font-medium text-gray-500 uppercase w-[80px]">Fecha</th>
                                <th class="px-2 py-2 text-right text-xs font-medium text-gray-500 uppercase w-[120px]">Acciones</th>
                            </tr>
                            <tr>
                                <th class="px-2 pb-2">
                                    <input v-model="filtros.nombre" @input="buscar('nombre')" placeholder="Filtrar..." class="w-full rounded border border-gray-200 px-2 py-1 text-xs focus:border-verde-400 focus:ring-1 focus:ring-verde-400 outline-none" />
                                </th>
                                <th class="px-2 pb-2">
                                    <input v-model="filtros.telefono" @input="buscar('telefono')" placeholder="Filtrar..." inputmode="numeric" class="w-full rounded border border-gray-200 px-2 py-1 text-xs focus:border-verde-400 focus:ring-1 focus:ring-verde-400 outline-none" />
                                </th>
                                <th class="px-2 pb-2">
                                    <input v-model="filtros.seccion" @input="buscar('seccion')" placeholder="Filtrar..." inputmode="numeric" class="w-full rounded border border-gray-200 px-2 py-1 text-xs focus:border-verde-400 focus:ring-1 focus:ring-verde-400 outline-none" />
                                </th>
                                <th class="px-2 pb-2">
                                    <input v-model="filtros.municipio" @input="buscar('municipio')" placeholder="Filtrar..." class="w-full rounded border border-gray-200 px-2 py-1 text-xs focus:border-verde-400 focus:ring-1 focus:ring-verde-400 outline-none" />
                                </th>
                                <th class="px-2 pb-2">
                                    <input v-model="filtros.distrito_local" @input="buscar('distrito_local')" placeholder="..." inputmode="numeric" class="w-full rounded border border-gray-200 px-1 py-1 text-xs focus:border-verde-400 focus:ring-1 focus:ring-verde-400 outline-none text-center" />
                                </th>
                                <th class="px-2 pb-2">
                                    <input v-model="filtros.distrito_federal" @input="buscar('distrito_federal')" placeholder="..." inputmode="numeric" class="w-full rounded border border-gray-200 px-1 py-1 text-xs focus:border-verde-400 focus:ring-1 focus:ring-verde-400 outline-none text-center" />
                                </th>
                                <th class="px-2 pb-2">
                                    <input v-model="filtros.demarcacion" @input="buscar('demarcacion')" placeholder="..." inputmode="numeric" class="w-full rounded border border-gray-200 px-1 py-1 text-xs focus:border-verde-400 focus:ring-1 focus:ring-verde-400 outline-none text-center" />
                                </th>
                                <th class="px-2 pb-2"></th>
                                <th class="px-2 pb-2"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            <tr v-if="personas.data.length === 0">
                                <td colspan="9" class="px-3 py-8 text-center text-sm text-gray-400">Sin resultados</td>
                            </tr>
                            <tr v-for="p in personas.data" :key="p.id">
                                <td class="px-2 py-2.5 text-sm text-gray-900 whitespace-nowrap">{{ p.nombre }} {{ p.apellido_paterno }}</td>
                                <td class="px-2 py-2.5 text-sm text-gray-600 whitespace-nowrap">{{ p.telefono }}</td>
                                <td class="px-2 py-2.5 text-sm text-gray-600 whitespace-nowrap">{{ p.seccion }}</td>
                                <td class="px-2 py-2.5 text-sm text-gray-600 whitespace-nowrap">{{ p.municipio }}</td>
                                <td class="px-2 py-2.5 text-sm text-gray-600 whitespace-nowrap text-center">{{ p.distrito_local }}</td>
                                <td class="px-2 py-2.5 text-sm text-gray-600 whitespace-nowrap text-center">{{ p.distrito_federal }}</td>
                                <td class="px-2 py-2.5 text-sm text-gray-600 whitespace-nowrap text-center">{{ p.demarcacion }}</td>
                                <td class="px-2 py-2.5 text-sm text-gray-500 whitespace-nowrap">{{ new Date(p.created_at).toLocaleDateString('es-MX') }}</td>
                                <td class="px-2 py-2.5 text-right text-sm whitespace-nowrap">
                                    <Link :href="route('admin.personas.edit', p.id)" class="text-verde-600 hover:text-verde-700 mr-2">Editar</Link>
                                    <button @click="deletePersona(p.id)" class="text-red-600 hover:text-red-900">Eliminar</button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div v-if="personas.total > 20" class="bg-white shadow-sm sm:rounded-lg p-4">
                    <div class="flex items-center justify-between">
                        <div class="text-sm text-gray-500">
                            Mostrando {{ personas.from }}–{{ personas.to }} de {{ personas.total }}
                        </div>
                        <div class="flex gap-1">
                            <Link
                                v-for="link in personas.links"
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
