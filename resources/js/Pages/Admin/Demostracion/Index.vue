<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import { reactive, ref, computed } from 'vue';
import axios from 'axios';

const props = defineProps({
    personas: Object,
    filters: Object,
});

const filtros = reactive({
    nombre: props.filters?.nombre ?? '',
    telefono: props.filters?.telefono ?? '',
});

const timers = {};
const buscar = (campo) => {
    clearTimeout(timers[campo]);
    timers[campo] = setTimeout(() => {
        router.get(route('admin.demostracion.index'), { ...filtros }, {
            preserveState: true,
            replace: true,
            preserveScroll: true,
        });
    }, 400);
};

const selected = ref([]);
const mensaje = ref('');
const sending = ref(false);
const resultado = ref(null);
const soloErrores = ref(false);

const allSelected = computed(
    () => props.personas.data.length > 0 && selected.value.length === props.personas.data.length,
);

const toggleAll = () => {
    selected.value = allSelected.value ? [] : props.personas.data.map((p) => p.id);
};

const destinatarios = computed(() => (selected.value.length > 0 ? selected.value.length : props.personas.total));

const resultadosFiltrados = computed(() => {
    const lista = resultado.value?.resultados ?? [];
    return soloErrores.value ? lista.filter((r) => !r.ok) : lista;
});

const preview = computed(() => {
    const nombre = props.personas.data.find((p) => selected.value.includes(p.id))?.nombre
        || props.personas.data[0]?.nombre
        || 'Juan';
    return mensaje.value.replace(/\{nombre\}/gi, nombre);
});

const enviarSms = async () => {
    if (!mensaje.value.trim()) {
        alert('Escribe el mensaje a enviar.');
        return;
    }

    const aTodos = selected.value.length === 0;
    if (aTodos && !confirm(`No seleccionaste destinatarios. ¿Enviar a TODOS los registros del listado (${props.personas.total})?`)) {
        return;
    }

    sending.value = true;
    resultado.value = null;
    soloErrores.value = false;
    try {
        const res = await axios.post(route('admin.demostracion.sms'), {
            mensaje: mensaje.value,
            ids: selected.value,
        });
        resultado.value = res.data;
    } catch (e) {
        const status = e?.response?.status;
        const esTimeout = e?.code === 'ECONNABORTED' || !e?.response || status === 502 || status === 504;
        resultado.value = {
            ok: false,
            enviados: 0,
            total: 0,
            errores: 1,
            resultados: [],
            errorGeneral: esTimeout
                ? 'La operación tardó demasiado (posible timeout del servidor). Verifica en Brevo antes de reenviar para no duplicar SMS.'
                : (e?.response?.data?.message || 'Error al enviar.'),
        };
    } finally {
        sending.value = false;
    }
};

const copiarErrores = async () => {
    const fallidos = (resultado.value?.resultados ?? []).filter((r) => !r.ok);
    const texto = fallidos
        .map((r) => `${r.nombre} (${r.telefono}) [${r.http ?? '-'}]: ${r.error || r.code || 'Error'}`)
        .join('\n');
    try {
        await navigator.clipboard.writeText(texto);
    } catch (err) {
        alert(texto);
    }
};

const eliminar = (id) => {
    if (confirm('¿Eliminar este registro?')) {
        router.delete(route('admin.demostracion.destroy', id), { preserveScroll: true });
    }
};
</script>

<template>
    <Head title="Demostración" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">Demostración</h2>
                <span class="text-sm text-gray-500">{{ personas.total }} registros</span>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8 space-y-4">
                <!-- Mensaje SMS -->
                <div class="bg-white shadow-sm sm:rounded-lg p-4">
                    <h3 class="text-sm font-semibold text-gray-700 mb-1">
                        <i class="fa-solid fa-comment-sms mr-1.5"></i> Enviar mensaje SMS
                    </h3>
                    <p class="text-xs text-gray-500 mb-3">
                        Usa el comodín <code class="px-1 py-0.5 bg-gray-100 rounded text-guinda-700">{nombre}</code> y se reemplazará por el nombre de cada persona.
                    </p>

                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                        <div>
                            <textarea
                                v-model="mensaje"
                                rows="4"
                                maxlength="480"
                                placeholder="Hola {nombre}, te saludamos..."
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-guinda-500 focus:ring-guinda-500 text-sm"
                            ></textarea>
                            <div class="flex items-center justify-between mt-2">
                                <span class="text-xs text-gray-400">{{ mensaje.length }}/480</span>
                                <span class="text-xs text-gray-500">
                                    Destinatarios: <strong>{{ destinatarios }}</strong>
                                    <span v-if="selected.length > 0">(seleccionados)</span>
                                    <span v-else>(todos)</span>
                                </span>
                            </div>
                        </div>

                        <div>
                            <div class="rounded-md border border-gray-200 bg-gray-50 p-3 text-sm text-gray-700 min-h-[96px] whitespace-pre-wrap break-words">
                                {{ preview || 'Vista previa del mensaje...' }}
                            </div>
                            <button
                                @click="enviarSms"
                                :disabled="sending"
                                class="mt-2 w-full px-4 py-2 bg-guinda-600 text-white rounded-lg hover:bg-guinda-700 disabled:opacity-50 text-sm font-medium"
                            >
                                {{ sending ? 'Enviando...' : `Enviar SMS (${destinatarios})` }}
                            </button>
                        </div>
                    </div>

                    <div v-if="resultado" class="mt-4 space-y-3">
                        <div v-if="resultado.errorGeneral" class="rounded-md p-3 text-sm bg-red-50 text-red-800">
                            {{ resultado.errorGeneral }}
                        </div>

                        <div v-else class="rounded-md p-3 text-sm" :class="resultado.errores ? 'bg-amber-50 text-amber-800' : 'bg-green-50 text-green-800'">
                            <p class="font-medium">
                                Enviados: {{ resultado.enviados }} de {{ resultado.total }}
                                <span v-if="resultado.errores"> · Con errores: {{ resultado.errores }}</span>
                            </p>
                        </div>

                        <div v-if="resultado.resultados?.length" class="overflow-x-auto bg-white shadow-sm sm:rounded-lg border border-gray-200">
                            <div class="flex items-center justify-between px-3 py-2 border-b border-gray-100">
                                <label class="inline-flex items-center gap-2 text-xs text-gray-600">
                                    <input type="checkbox" v-model="soloErrores" class="rounded border-gray-300 text-guinda-600 focus:ring-guinda-500" />
                                    Solo errores
                                </label>
                                <button v-if="resultado.errores" @click="copiarErrores" class="text-xs text-guinda-700 hover:underline">
                                    <i class="fa-solid fa-copy mr-1"></i>Copiar errores
                                </button>
                            </div>
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Nombre</th>
                                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Teléfono</th>
                                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">HTTP</th>
                                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Resultado</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    <tr v-for="r in resultadosFiltrados" :key="r.id">
                                        <td class="px-3 py-2 text-sm text-gray-900 whitespace-nowrap">{{ r.nombre }}</td>
                                        <td class="px-3 py-2 text-sm text-gray-600 whitespace-nowrap">{{ r.telefono }}</td>
                                        <td class="px-3 py-2 text-sm whitespace-nowrap" :class="r.ok ? 'text-green-600' : 'text-red-600'">{{ r.http ?? '-' }}</td>
                                        <td class="px-3 py-2 text-sm" :class="r.ok ? 'text-green-700' : 'text-red-700'">
                                            <span v-if="r.ok">messageId {{ r.messageId }}</span>
                                            <span v-else>{{ r.error || r.code || 'Error' }}</span>
                                        </td>
                                    </tr>
                                    <tr v-if="resultadosFiltrados.length === 0">
                                        <td colspan="4" class="px-3 py-6 text-center text-sm text-gray-400">Sin resultados</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Listado -->
                <div class="overflow-x-auto bg-white shadow-sm sm:rounded-lg">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-3 py-2 w-[40px]">
                                    <input type="checkbox" :checked="allSelected" @change="toggleAll" class="rounded border-gray-300 text-guinda-600 focus:ring-guinda-500" />
                                </th>
                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Nombre</th>
                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Teléfono</th>
                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Registro</th>
                                <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase">Acciones</th>
                            </tr>
                            <tr>
                                <th class="px-3 pb-2"></th>
                                <th class="px-3 pb-2">
                                    <input v-model="filtros.nombre" @input="buscar('nombre')" placeholder="Filtrar..." class="w-full rounded border border-gray-200 px-2 py-1 text-xs focus:border-guinda-400 focus:ring-1 focus:ring-guinda-400 outline-none" />
                                </th>
                                <th class="px-3 pb-2">
                                    <input v-model="filtros.telefono" @input="buscar('telefono')" placeholder="Filtrar..." inputmode="numeric" class="w-full rounded border border-gray-200 px-2 py-1 text-xs focus:border-guinda-400 focus:ring-1 focus:ring-guinda-400 outline-none" />
                                </th>
                                <th class="px-3 pb-2"></th>
                                <th class="px-3 pb-2"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            <tr v-if="personas.data.length === 0">
                                <td colspan="5" class="px-3 py-8 text-center text-sm text-gray-400">Sin resultados</td>
                            </tr>
                            <tr v-for="p in personas.data" :key="p.id" :class="{ 'bg-guinda-50': selected.includes(p.id) }">
                                <td class="px-3 py-2.5">
                                    <input v-model="selected" type="checkbox" :value="p.id" class="rounded border-gray-300 text-guinda-600 focus:ring-guinda-500" />
                                </td>
                                <td class="px-3 py-2.5 text-sm text-gray-900 whitespace-nowrap">{{ p.nombre }}</td>
                                <td class="px-3 py-2.5 text-sm text-gray-600 whitespace-nowrap">{{ p.telefono }}</td>
                                <td class="px-3 py-2.5 text-sm text-gray-500 whitespace-nowrap">{{ new Date(p.created_at).toLocaleDateString('es-MX') }}</td>
                                <td class="px-3 py-2.5 text-right text-sm whitespace-nowrap">
                                    <button @click="eliminar(p.id)" class="inline-flex items-center gap-1 text-red-600 hover:text-red-900">
                                        <i class="fa-solid fa-trash"></i> Eliminar
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Paginación -->
                <div v-if="personas.total > 50" class="bg-white shadow-sm sm:rounded-lg p-4">
                    <div class="flex items-center justify-between">
                        <div class="text-sm text-gray-500">
                            Mostrando {{ personas.from }}–{{ personas.to }} de {{ personas.total }}
                        </div>
                        <div class="flex gap-1">
                            <a
                                v-for="link in personas.links"
                                :key="link.label"
                                :href="link.url || '#'"
                                v-html="link.label"
                                :class="[
                                    'px-3 py-1.5 text-sm rounded-md transition-colors',
                                    link.active ? 'bg-guinda-600 text-white' : link.url ? 'text-gray-600 hover:bg-gray-100' : 'text-gray-300 cursor-default',
                                ]"
                            />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
