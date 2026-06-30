<script setup>
import { computed } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { Doughnut } from 'vue-chartjs';
import { Chart as ChartJS, ArcElement, Tooltip, Legend } from 'chart.js';

ChartJS.register(ArcElement, Tooltip, Legend);

const props = defineProps({ encuestas: Object, stats: Object });
const s = computed(() => props.stats);

const chartOptions = {
    responsive: true,
    maintainAspectRatio: true,
    plugins: { legend: { position: 'bottom', labels: { boxWidth: 12, padding: 10, font: { size: 10 } } } },
};

const chartPreocupaciones = computed(() => ({
    labels: Object.keys(s.value.preocupaciones),
    datasets: [{ data: Object.values(s.value.preocupaciones), backgroundColor: ['#54b848','#00ae41','#c7de8d','#f97316','#6366f1','#ec4899','#14b8a6','#eab308','#f43f5e'] }],
}));

const chartJusticia = computed(() => ({
    labels: Object.keys(s.value.problema_justicia),
    datasets: [{ data: Object.values(s.value.problema_justicia), backgroundColor: ['#ef4444','#f97316','#eab308','#6366f1'] }],
}));

const chartErrores = computed(() => ({
    labels: Object.keys(s.value.errores_gobierno),
    datasets: [{ data: Object.values(s.value.errores_gobierno), backgroundColor: ['#ef4444','#f97316','#eab308','#54b848','#6366f1','#ec4899','#14b8a6','#c7de8d','#00ae41'] }],
}));

const chartServicios = computed(() => ({
    labels: Object.keys(s.value.deficiencias_servicios),
    datasets: [{ data: Object.values(s.value.deficiencias_servicios), backgroundColor: ['#6366f1','#eab308','#14b8a6','#f97316','#78716c','#ef4444','#54b848'] }],
}));
</script>

<template>
    <Head title="Encuestas - Conóceme" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">Encuestas "Conóceme"</h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8 space-y-8">
                <div class="bg-white shadow-sm sm:rounded-lg p-4 flex items-center justify-between">
                    <span class="text-sm text-gray-500">{{ s.total }} encuestas totales</span>
                </div>

                <div class="bg-white shadow-sm sm:rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-6">Perspectiva Social y Política</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <div>
                            <h4 class="text-sm font-medium text-gray-600 mb-3 text-center">Preocupaciones actuales</h4>
                            <div class="max-w-xs mx-auto">
                                <Doughnut :data="chartPreocupaciones" :options="chartOptions" />
                            </div>
                        </div>
                        <div>
                            <h4 class="text-sm font-medium text-gray-600 mb-3 text-center">Principal problema de la justicia</h4>
                            <div class="max-w-xs mx-auto">
                                <Doughnut :data="chartJusticia" :options="chartOptions" />
                            </div>
                        </div>
                        <div>
                            <h4 class="text-sm font-medium text-gray-600 mb-3 text-center">Principales errores de los gobiernos</h4>
                            <div class="max-w-xs mx-auto">
                                <Doughnut :data="chartErrores" :options="chartOptions" />
                            </div>
                        </div>
                        <div>
                            <h4 class="text-sm font-medium text-gray-600 mb-3 text-center">Deficiencias en servicios públicos</h4>
                            <div class="max-w-xs mx-auto">
                                <Doughnut :data="chartServicios" :options="chartOptions" />
                            </div>
                        </div>
                    </div>
                </div>

                <div class="overflow-x-auto bg-white shadow-sm sm:rounded-lg">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nombre</th>
                                <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase">Profesión</th>
                                <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase">Lugar Nac.</th>
                                <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase">Teléfono</th>
                                <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fecha</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            <tr v-if="encuestas.data.length === 0">
                                <td colspan="5" class="px-3 py-8 text-center text-sm text-gray-400">Sin datos</td>
                            </tr>
                            <tr v-for="e in encuestas.data" :key="e.id" class="hover:bg-gray-50 text-sm">
                                <td class="px-3 py-2.5 text-gray-900 whitespace-nowrap">{{ e.nombre_completo }}</td>
                                <td class="px-3 py-2.5 text-gray-600 whitespace-nowrap">{{ e.profesion_ocupacion }}</td>
                                <td class="px-3 py-2.5 text-gray-600 whitespace-nowrap">{{ e.lugar_nacimiento }}</td>
                                <td class="px-3 py-2.5 text-gray-600 whitespace-nowrap">{{ e.telefono }}</td>
                                <td class="px-3 py-2.5 text-gray-500 whitespace-nowrap">{{ new Date(e.created_at).toLocaleDateString('es-MX') }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div v-if="encuestas.total > 20" class="bg-white shadow-sm sm:rounded-lg p-4">
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-500">Mostrando {{ encuestas.from }}–{{ encuestas.to }} de {{ encuestas.total }}</span>
                        <div class="flex gap-1">
                            <Link v-for="link in encuestas.links" :key="link.label" :href="link.url || '#'" v-html="link.label" :class="['px-3 py-1.5 text-sm rounded-md', link.active ? 'bg-verde-600 text-white' : link.url ? 'text-gray-600 hover:bg-gray-100' : 'text-gray-300 cursor-default']" preserve-scroll />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
