<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { reactive } from 'vue';

const props = defineProps({
    personas: Object,
    filters: Object,
    gruposCatalogo: {
        type: Array,
        default: () => [],
    },
});

const filtros = reactive({
    nombre: props.filters?.nombre ?? '',
    celular: props.filters?.celular ?? '',
    seccion: props.filters?.seccion ?? '',
    colonia: props.filters?.colonia ?? '',
    localidad: props.filters?.localidad ?? '',
    promotor: props.filters?.promotor ?? '',
    sexo: props.filters?.sexo ?? '',
    grupo: props.filters?.grupo ?? '',
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
    Object.keys(filtros).forEach((key) => (filtros[key] = ''));
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

const activeFilters = () => Object.values(filtros).some((value) => value !== '');

const fecha = (valor) => (valor ? new Date(`${valor}T00:00:00`).toLocaleDateString('es-MX') : '—');

const nombreCompleto = (persona) => [persona.nombre, persona.apellido_paterno, persona.apellido_materno].filter(Boolean).join(' ');
</script>

<template>
    <Head title="Personas Registradas" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">Personas Registradas</h2>
                <Link :href="route('admin.personas.create')" class="rounded-lg bg-guinda-600 px-4 py-2 text-sm text-white hover:bg-guinda-700">+ Nueva Persona</Link>
            </div>
        </template>

        <div class="py-8">
            <div class="mx-auto max-w-[1800px] space-y-4 sm:px-6 lg:px-8">
                <div class="flex items-center justify-between rounded-lg bg-white px-4 py-2 shadow-sm">
                    <span class="text-sm text-gray-500">{{ personas.total }} registros</span>
                    <button v-if="activeFilters()" @click="limpiar" class="text-sm text-guinda-600 hover:text-guinda-700">Limpiar filtros</button>
                </div>

                <div class="grid gap-2 rounded-lg bg-white p-3 shadow-sm sm:grid-cols-4">
                    <input v-model="filtros.municipio" @input="buscar('municipio')" placeholder="Municipio (derivado)" class="rounded border border-gray-200 px-2 py-1.5 text-xs outline-none focus:border-guinda-400 focus:ring-1 focus:ring-guinda-400" />
                    <input v-model="filtros.distrito_local" @input="buscar('distrito_local')" placeholder="Distrito local" inputmode="numeric" class="rounded border border-gray-200 px-2 py-1.5 text-xs outline-none focus:border-guinda-400 focus:ring-1 focus:ring-guinda-400" />
                    <input v-model="filtros.distrito_federal" @input="buscar('distrito_federal')" placeholder="Distrito federal" inputmode="numeric" class="rounded border border-gray-200 px-2 py-1.5 text-xs outline-none focus:border-guinda-400 focus:ring-1 focus:ring-guinda-400" />
                    <input v-model="filtros.demarcacion" @input="buscar('demarcacion')" placeholder="Demarcación" inputmode="numeric" class="rounded border border-gray-200 px-2 py-1.5 text-xs outline-none focus:border-guinda-400 focus:ring-1 focus:ring-guinda-400" />
                </div>

                <div class="overflow-x-auto rounded-lg bg-white shadow-sm">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-2 py-2 text-left text-xs font-medium uppercase text-gray-500">Nombre</th>
                                <th class="px-2 py-2 text-left text-xs font-medium uppercase text-gray-500">Sexo</th>
                                <th class="px-2 py-2 text-left text-xs font-medium uppercase text-gray-500">Fecha Nac.</th>
                                <th class="px-2 py-2 text-left text-xs font-medium uppercase text-gray-500">Celular</th>
                                <th class="px-2 py-2 text-left text-xs font-medium uppercase text-gray-500">CURP</th>
                                <th class="px-2 py-2 text-left text-xs font-medium uppercase text-gray-500">Clave Elector</th>
                                <th class="px-2 py-2 text-left text-xs font-medium uppercase text-gray-500">Domicilio</th>
                                <th class="px-2 py-2 text-left text-xs font-medium uppercase text-gray-500">Colonia</th>
                                <th class="px-2 py-2 text-left text-xs font-medium uppercase text-gray-500">Localidad</th>
                                <th class="px-2 py-2 text-left text-xs font-medium uppercase text-gray-500">Sección</th>
                                <th class="px-2 py-2 text-left text-xs font-medium uppercase text-gray-500">Promotor</th>
                                <th class="px-2 py-2 text-left text-xs font-medium uppercase text-gray-500">Grupos</th>
                                <th class="px-2 py-2 text-left text-xs font-medium uppercase text-gray-500">Ubicación</th>
                                <th class="px-2 py-2 text-left text-xs font-medium uppercase text-gray-500">Registro</th>
                                <th class="px-2 py-2 text-right text-xs font-medium uppercase text-gray-500">Acciones</th>
                            </tr>
                            <tr>
                                <th class="px-2 pb-2">
                                    <input v-model="filtros.nombre" @input="buscar('nombre')" placeholder="Filtrar..." class="w-full rounded border border-gray-200 px-2 py-1 text-xs outline-none focus:border-guinda-400 focus:ring-1 focus:ring-guinda-400" />
                                </th>
                                <th class="px-2 pb-2">
                                    <select v-model="filtros.sexo" @change="buscar('sexo')" class="w-full rounded border border-gray-200 px-1 py-1 text-xs outline-none focus:border-guinda-400 focus:ring-1 focus:ring-guinda-400">
                                        <option value="">Todos</option>
                                        <option value="Masculino">Masculino</option>
                                        <option value="Femenino">Femenino</option>
                                        <option value="Otro">Otro</option>
                                    </select>
                                </th>
                                <th class="px-2 pb-2"></th>
                                <th class="px-2 pb-2">
                                    <input v-model="filtros.celular" @input="buscar('celular')" placeholder="Filtrar..." inputmode="numeric" class="w-full rounded border border-gray-200 px-2 py-1 text-xs outline-none focus:border-guinda-400 focus:ring-1 focus:ring-guinda-400" />
                                </th>
                                <th class="px-2 pb-2"></th>
                                <th class="px-2 pb-2"></th>
                                <th class="px-2 pb-2"></th>
                                <th class="px-2 pb-2">
                                    <input v-model="filtros.colonia" @input="buscar('colonia')" placeholder="Filtrar..." class="w-full rounded border border-gray-200 px-2 py-1 text-xs outline-none focus:border-guinda-400 focus:ring-1 focus:ring-guinda-400" />
                                </th>
                                <th class="px-2 pb-2">
                                    <input v-model="filtros.localidad" @input="buscar('localidad')" placeholder="Filtrar..." class="w-full rounded border border-gray-200 px-2 py-1 text-xs outline-none focus:border-guinda-400 focus:ring-1 focus:ring-guinda-400" />
                                </th>
                                <th class="px-2 pb-2">
                                    <input v-model="filtros.seccion" @input="buscar('seccion')" placeholder="Filtrar..." inputmode="numeric" class="w-full rounded border border-gray-200 px-2 py-1 text-xs outline-none focus:border-guinda-400 focus:ring-1 focus:ring-guinda-400" />
                                </th>
                                <th class="px-2 pb-2">
                                    <input v-model="filtros.promotor" @input="buscar('promotor')" placeholder="Filtrar..." class="w-full rounded border border-gray-200 px-2 py-1 text-xs outline-none focus:border-guinda-400 focus:ring-1 focus:ring-guinda-400" />
                                </th>
                                <th class="px-2 pb-2">
                                    <select v-model="filtros.grupo" @change="buscar('grupo')" class="w-full rounded border border-gray-200 px-1 py-1 text-xs outline-none focus:border-guinda-400 focus:ring-1 focus:ring-guinda-400">
                                        <option value="">Todos</option>
                                        <option v-for="grupo in gruposCatalogo" :key="grupo.id" :value="grupo.nombre">{{ grupo.nombre }}</option>
                                    </select>
                                </th>
                                <th class="px-2 pb-2"></th>
                                <th class="px-2 pb-2"></th>
                                <th class="px-2 pb-2"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            <tr v-if="personas.data.length === 0">
                                <td colspan="15" class="px-3 py-8 text-center text-sm text-gray-400">Sin resultados</td>
                            </tr>
                            <tr v-for="persona in personas.data" :key="persona.id">
                                <td class="whitespace-nowrap px-2 py-2.5 text-sm font-medium text-gray-900">{{ nombreCompleto(persona) }}</td>
                                <td class="whitespace-nowrap px-2 py-2.5 text-sm text-gray-600">
                                    <span v-if="persona.sexo" class="rounded-full px-2 py-0.5 text-xs" :class="persona.sexo === 'Femenino' ? 'bg-pink-100 text-pink-700' : persona.sexo === 'Masculino' ? 'bg-blue-100 text-blue-700' : 'bg-gray-100 text-gray-600'">{{ persona.sexo }}</span>
                                    <span v-else class="text-gray-400">—</span>
                                </td>
                                <td class="whitespace-nowrap px-2 py-2.5 text-sm text-gray-600">{{ fecha(persona.fecha_nacimiento) }}</td>
                                <td class="whitespace-nowrap px-2 py-2.5 text-sm text-gray-600">{{ persona.celular || '—' }}</td>
                                <td class="whitespace-nowrap px-2 py-2.5 text-sm text-gray-600">{{ persona.curp || '—' }}</td>
                                <td class="whitespace-nowrap px-2 py-2.5 text-sm text-gray-600">{{ persona.clave_elector || '—' }}</td>
                                <td class="max-w-[220px] truncate whitespace-nowrap px-2 py-2.5 text-sm text-gray-600" :title="persona.domicilio">{{ persona.domicilio || '—' }}</td>
                                <td class="whitespace-nowrap px-2 py-2.5 text-sm text-gray-600">{{ persona.colonia || '—' }}</td>
                                <td class="whitespace-nowrap px-2 py-2.5 text-sm text-gray-600">{{ persona.localidad || '—' }}</td>
                                <td class="whitespace-nowrap px-2 py-2.5 text-sm text-gray-600">{{ persona.seccion || '—' }}</td>
                                <td class="max-w-[180px] truncate whitespace-nowrap px-2 py-2.5 text-sm text-gray-600" :title="persona.promotor">{{ persona.promotor || '—' }}</td>
                                <td class="px-2 py-2.5 text-sm">
                                    <div v-if="persona.grupos?.length" class="flex flex-wrap gap-1">
                                        <span v-for="grupo in persona.grupos" :key="grupo.id" class="rounded-full bg-guinda-50 px-2 py-0.5 text-xs text-guinda-700">{{ grupo.nombre }}</span>
                                    </div>
                                    <span v-else class="text-gray-400">—</span>
                                </td>
                                <td class="px-2 py-2.5 text-xs text-gray-600">
                                    <template v-if="persona.seccion_info">
                                        <div class="font-medium text-gray-700">{{ persona.seccion_info.municipio }}</div>
                                        <div class="text-gray-400">DL {{ persona.seccion_info.distrito_local }} · DF {{ persona.seccion_info.distrito_federal }} · D{{ persona.seccion_info.demarcacion }}</div>
                                    </template>
                                    <span v-else class="text-gray-400">—</span>
                                </td>
                                <td class="whitespace-nowrap px-2 py-2.5 text-sm text-gray-500">{{ fecha(persona.created_at?.slice(0, 10)) }}</td>
                                <td class="whitespace-nowrap px-2 py-2.5 text-right text-sm">
                                    <Link :href="route('admin.personas.edit', persona.id)" class="mr-2 inline-flex items-center gap-1 text-guinda-600 hover:text-guinda-700"><i class="fa-solid fa-pen-to-square"></i> Editar</Link>
                                    <button @click="deletePersona(persona.id)" class="inline-flex items-center gap-1 text-red-600 hover:text-red-900"><i class="fa-solid fa-trash"></i> Eliminar</button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div v-if="personas.total > 20" class="rounded-lg bg-white p-4 shadow-sm">
                    <div class="flex items-center justify-between">
                        <div class="text-sm text-gray-500">Mostrando {{ personas.from }}–{{ personas.to }} de {{ personas.total }}</div>
                        <div class="flex gap-1">
                            <Link
                                v-for="link in personas.links"
                                :key="link.label"
                                :href="link.url || '#'"
                                v-html="link.label"
                                :class="[
                                    'rounded-md px-3 py-1.5 text-sm transition-colors',
                                    link.active ? 'bg-guinda-600 text-white' : link.url ? 'text-gray-600 hover:bg-gray-100' : 'cursor-default text-gray-300',
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
