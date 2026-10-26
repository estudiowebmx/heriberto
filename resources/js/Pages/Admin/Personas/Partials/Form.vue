<script setup>
import { Link, useForm, usePage } from '@inertiajs/vue3';
import axios from 'axios';
import { reactive } from 'vue';
import IneUploader from './IneUploader.vue';

const props = defineProps({
    persona: {
        type: Object,
        default: null,
    },
    grupos: {
        type: Array,
        default: () => [],
    },
});

const page = usePage();

const form = useForm({
    nombre: props.persona?.nombre ?? '',
    apellido_paterno: props.persona?.apellido_paterno ?? '',
    apellido_materno: props.persona?.apellido_materno ?? '',
    sexo: props.persona?.sexo ?? '',
    fecha_nacimiento: props.persona?.fecha_nacimiento ?? '',
    celular: props.persona?.celular ?? '',
    curp: props.persona?.curp ?? '',
    clave_elector: props.persona?.clave_elector ?? '',
    domicilio: props.persona?.domicilio ?? '',
    colonia: props.persona?.colonia ?? '',
    localidad: props.persona?.localidad ?? '',
    seccion: props.persona?.seccion ?? '',
    promotor: props.persona?.promotor ?? page.props.auth?.user?.name ?? '',
    grupos: props.persona?.grupos?.map((grupo) => grupo.id) ?? [],
    nuevos_grupos: '',
});

const ubicacion = reactive({
    municipio: props.persona?.seccion_info?.municipio ?? '',
    distrito_local: props.persona?.seccion_info?.distrito_local ?? '',
    distrito_federal: props.persona?.seccion_info?.distrito_federal ?? '',
    demarcacion: props.persona?.seccion_info?.demarcacion ?? '',
});

const limpiarUbicacion = () => {
    ubicacion.municipio = '';
    ubicacion.distrito_local = '';
    ubicacion.distrito_federal = '';
    ubicacion.demarcacion = '';
};

const buscarSeccion = async () => {
    const valor = form.seccion?.trim();

    if (!valor) {
        limpiarUbicacion();

        return;
    }

    try {
        const { data } = await axios.get('/api/seccion', { params: { q: valor } });
        ubicacion.municipio = data.municipio ?? '';
        ubicacion.distrito_local = data.distrito_local ?? '';
        ubicacion.distrito_federal = data.distrito_federal ?? '';
        ubicacion.demarcacion = data.demarcacion ?? '';
    } catch {
        limpiarUbicacion();
    }
};

const submit = () => {
    if (props.persona) {
        form.put(route('admin.personas.update', props.persona.id));
    } else {
        form.post(route('admin.personas.store'));
    }
};

const aplicarDatosIne = async (datos) => {
    const campos = [
        'nombre',
        'apellido_paterno',
        'apellido_materno',
        'sexo',
        'fecha_nacimiento',
        'curp',
        'clave_elector',
        'domicilio',
        'colonia',
        'localidad',
        'seccion',
    ];

    campos.forEach((campo) => {
        if (datos[campo] !== null && datos[campo] !== undefined && datos[campo] !== '') {
            form[campo] = datos[campo];
        }
    });

    if (datos.seccion) {
        await buscarSeccion();
    }
};
</script>

<template>
    <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
        <div class="p-6">
            <form @submit.prevent="submit" class="space-y-5">
                <IneUploader @datos="aplicarDatosIne" />

                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Nombre *</label>
                        <input v-model="form.nombre" type="text" required maxlength="100" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-guinda-500 focus:ring-guinda-500" />
                        <p v-if="form.errors.nombre" class="mt-1 text-sm text-red-600">{{ form.errors.nombre }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Apellido Paterno</label>
                        <input v-model="form.apellido_paterno" type="text" maxlength="100" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-guinda-500 focus:ring-guinda-500" />
                        <p v-if="form.errors.apellido_paterno" class="mt-1 text-sm text-red-600">{{ form.errors.apellido_paterno }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Apellido Materno</label>
                        <input v-model="form.apellido_materno" type="text" maxlength="100" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-guinda-500 focus:ring-guinda-500" />
                        <p v-if="form.errors.apellido_materno" class="mt-1 text-sm text-red-600">{{ form.errors.apellido_materno }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Sexo</label>
                        <select v-model="form.sexo" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-guinda-500 focus:ring-guinda-500">
                            <option value="">Sin especificar</option>
                            <option value="Masculino">Masculino</option>
                            <option value="Femenino">Femenino</option>
                            <option value="Otro">Otro</option>
                        </select>
                        <p v-if="form.errors.sexo" class="mt-1 text-sm text-red-600">{{ form.errors.sexo }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Fecha de Nacimiento</label>
                        <input v-model="form.fecha_nacimiento" type="date" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-guinda-500 focus:ring-guinda-500" />
                        <p v-if="form.errors.fecha_nacimiento" class="mt-1 text-sm text-red-600">{{ form.errors.fecha_nacimiento }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Celular (10 dígitos)</label>
                        <input v-model="form.celular" type="tel" inputmode="numeric" maxlength="10" placeholder="5512345678" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-guinda-500 focus:ring-guinda-500" />
                        <p v-if="form.errors.celular" class="mt-1 text-sm text-red-600">{{ form.errors.celular }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">CURP</label>
                        <input v-model="form.curp" type="text" maxlength="18" class="mt-1 block w-full rounded-md border-gray-300 uppercase shadow-sm focus:border-guinda-500 focus:ring-guinda-500" />
                        <p v-if="form.errors.curp" class="mt-1 text-sm text-red-600">{{ form.errors.curp }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Clave de Elector</label>
                        <input v-model="form.clave_elector" type="text" maxlength="20" class="mt-1 block w-full rounded-md border-gray-300 uppercase shadow-sm focus:border-guinda-500 focus:ring-guinda-500" />
                        <p v-if="form.errors.clave_elector" class="mt-1 text-sm text-red-600">{{ form.errors.clave_elector }}</p>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Domicilio</label>
                    <input v-model="form.domicilio" type="text" maxlength="255" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-guinda-500 focus:ring-guinda-500" />
                    <p v-if="form.errors.domicilio" class="mt-1 text-sm text-red-600">{{ form.errors.domicilio }}</p>
                </div>

                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Colonia</label>
                        <input v-model="form.colonia" type="text" maxlength="255" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-guinda-500 focus:ring-guinda-500" />
                        <p v-if="form.errors.colonia" class="mt-1 text-sm text-red-600">{{ form.errors.colonia }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Localidad</label>
                        <input v-model="form.localidad" type="text" maxlength="255" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-guinda-500 focus:ring-guinda-500" />
                        <p v-if="form.errors.localidad" class="mt-1 text-sm text-red-600">{{ form.errors.localidad }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Sección Electoral</label>
                        <div class="mt-1 flex gap-2">
                            <input v-model="form.seccion" type="text" maxlength="20" class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-guinda-500 focus:ring-guinda-500" placeholder="Ingresa la sección" @blur="buscarSeccion" />
                            <button type="button" @click="buscarSeccion" class="rounded-md bg-gray-100 px-3 py-2 text-sm text-gray-600 hover:bg-gray-200">Buscar</button>
                        </div>
                        <p v-if="form.errors.seccion" class="mt-1 text-sm text-red-600">{{ form.errors.seccion }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Promotor</label>
                        <input v-model="form.promotor" type="text" maxlength="255" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-guinda-500 focus:ring-guinda-500" />
                        <p v-if="form.errors.promotor" class="mt-1 text-sm text-red-600">{{ form.errors.promotor }}</p>
                    </div>
                </div>

                <div v-if="ubicacion.municipio || ubicacion.distrito_local || ubicacion.distrito_federal || ubicacion.demarcacion" class="rounded-lg border border-gray-200 bg-gray-50 p-4">
                    <p class="mb-3 text-xs font-semibold uppercase tracking-wide text-gray-500">Ubicación derivada de la sección</p>
                    <div class="grid gap-3 text-sm sm:grid-cols-4">
                        <div>
                            <span class="block text-xs text-gray-500">Municipio</span>
                            <span class="font-medium text-gray-800">{{ ubicacion.municipio || '—' }}</span>
                        </div>
                        <div>
                            <span class="block text-xs text-gray-500">Distrito Local</span>
                            <span class="font-medium text-gray-800">{{ ubicacion.distrito_local || '—' }}</span>
                        </div>
                        <div>
                            <span class="block text-xs text-gray-500">Distrito Federal</span>
                            <span class="font-medium text-gray-800">{{ ubicacion.distrito_federal || '—' }}</span>
                        </div>
                        <div>
                            <span class="block text-xs text-gray-500">Demarcación</span>
                            <span class="font-medium text-gray-800">{{ ubicacion.demarcacion || '—' }}</span>
                        </div>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Grupos / Etiquetas</label>
                    <div v-if="grupos.length" class="mt-2 flex flex-wrap gap-3">
                        <label v-for="grupo in grupos" :key="grupo.id" class="inline-flex items-center gap-2 rounded-full border border-gray-200 bg-gray-50 px-3 py-1.5 text-sm text-gray-700">
                            <input v-model="form.grupos" type="checkbox" :value="grupo.id" class="rounded border-gray-300 text-guinda-600 focus:ring-guinda-500" />
                            <span>{{ grupo.nombre }}</span>
                        </label>
                    </div>
                    <p v-else class="mt-2 text-sm text-gray-500">Todavía no hay etiquetas registradas.</p>
                    <p v-if="form.errors.grupos" class="mt-1 text-sm text-red-600">{{ form.errors.grupos }}</p>

                    <input v-model="form.nuevos_grupos" type="text" maxlength="1000" placeholder="Nueva etiqueta (separa varias con coma)" class="mt-3 block w-full rounded-md border-gray-300 shadow-sm focus:border-guinda-500 focus:ring-guinda-500" />
                    <p v-if="form.errors.nuevos_grupos" class="mt-1 text-sm text-red-600">{{ form.errors.nuevos_grupos }}</p>
                </div>

                <div class="flex items-center gap-4 border-t border-gray-100 pt-5">
                    <button type="submit" :disabled="form.processing" class="rounded-lg bg-guinda-600 px-4 py-2 text-sm text-white hover:bg-guinda-700 disabled:opacity-50">
                        {{ persona ? 'Actualizar' : 'Registrar' }}
                    </button>
                    <Link :href="route('admin.personas.index')" class="text-sm text-gray-600 hover:text-gray-900">Cancelar</Link>
                </div>
            </form>
        </div>
    </div>
</template>
