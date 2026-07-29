<script setup>
import { ref, onMounted } from "vue";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head } from "@inertiajs/vue3";
import VChart from "vue-echarts";
import { use } from "echarts/core";
import { GaugeChart } from "echarts/charts";
import { CanvasRenderer } from "echarts/renderers";
import axios from "axios";

use([GaugeChart, CanvasRenderer]);

const estatal = ref({ total: 0, meta: 0, pct: 0 });
const municipios = ref([]);
const secciones = ref([]);

onMounted(async () => {
    try {
        const res = await axios.get("/api/dashboard");
        estatal.value = res.data.estatal;
        municipios.value = res.data.municipios;
        secciones.value = res.data.secciones || [];
    } catch {}
});

function gaugeOption(total, meta, pct, title) {
    return {
        series: [
            {
                type: "gauge",
                startAngle: 210,
                endAngle: -30,
                min: 0,
                max: 100,
                progress: {
                    show: true,
                    width: 20,
                    itemStyle: {
                        color:
                            pct >= 80
                                ? "#166534"
                                : pct >= 60
                                  ? "#22c55e"
                                  : pct >= 40
                                    ? "#facc15"
                                    : pct >= 20
                                      ? "#f97316"
                                      : "#ef4444",
                    },
                },
                axisLine: { lineStyle: { width: 20 } },
                axisTick: { show: false },
                splitLine: { show: false },
                axisLabel: { show: false },
                detail: {
                    valueAnimation: true,
                    formatter: "{value}%",
                    fontSize: 14,
                    offsetCenter: [0, "60%"],
                },
                data: [
                    { value: Math.min(pct, 100), name: `${total} / ${meta}` },
                ],
                title: { show: true, offsetCenter: [0, "30%"], fontSize: 10 },
            },
        ],
    };
}
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Dashboard
            </h2>
        </template>

        <div class="py-8">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8 space-y-6">
                <!-- State-wide gauge -->
                <div class="bg-white shadow-sm sm:rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">
                        Avance Total
                    </h3>
                    <p class="text-sm text-gray-500 mb-4">
                        Suma de todas las demarcaciones
                    </p>
                    <div class="flex justify-center">
                        <v-chart
                            :option="
                                gaugeOption(
                                    estatal.total,
                                    estatal.meta,
                                    estatal.pct,
                                    'Nayarit',
                                )
                            "
                            style="height: 460px; width: 800px"
                            autoresize
                        />
                    </div>
                </div>

                <!-- Secciones Xalisco -->
                <div class="bg-white shadow-sm sm:rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">
                        Avance por Sección - Xalisco
                    </h3>
                    <div
                        class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4"
                    >
                        <div
                            v-for="s in secciones"
                            :key="s.seccion"
                            class="text-center"
                        >
                            <v-chart
                                :option="
                                    gaugeOption(
                                        s.total,
                                        s.meta,
                                        s.pct,
                                        s.seccion,
                                    )
                                "
                                style="height: 240px"
                                autoresize
                            />
                            <p
                                class="text-xs font-medium text-gray-600 mt-1 truncate"
                            >
                                <strong>Secc. {{ s.seccion }}</strong>
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Demarcaciones Xalisco -->
                <div class="bg-white shadow-sm sm:rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">
                        Por Demarcación
                    </h3>
                    <div
                        class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4"
                    >
                        <div
                            v-for="m in municipios"
                            :key="m.municipio"
                            class="text-center"
                        >
                            <v-chart
                                :option="
                                    gaugeOption(
                                        m.total,
                                        m.meta,
                                        m.pct,
                                        m.municipio,
                                    )
                                "
                                style="height: 240px"
                                autoresize
                            />
                            <p
                                class="text-xs font-medium text-gray-600 mt-1 truncate"
                            >
                                <strong> {{ m.municipio }}</strong>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
