<script setup>
import { ref, onMounted } from 'vue';
import L from 'leaflet';
import 'leaflet/dist/leaflet.css';
import axios from 'axios';
import { CircleUser, CirclePercent } from '@lucide/vue';

const mapContainer = ref(null);
const layerType = ref('municipio');
const rankings = ref([]);
const metas = ref([]);
const seccionesMunicipios = ref({});
const loading = ref(true);
const mapError = ref('');
const sortField = ref('total');
const sortOrder = ref('desc');

let map = null;
let baseLayer = null;
let currentLayer = null;
let geoData = {};

const progressColor = (ratio) => {
    if (ratio >= 0.8) return '#166534';
    if (ratio >= 0.6) return '#22c55e';
    if (ratio >= 0.4) return '#86efac';
    if (ratio >= 0.2) return '#facc15';
    if (ratio >= 0.01) return '#f97316';
    if (ratio > 0) return '#ef4444';
    return '#fee2e2';
};

const fetchRankings = async (tipo) => {
    try {
        const [rk, mt] = await Promise.all([
            axios.get('/api/ranking', { params: { tipo } }),
            axios.get('/api/metas', { params: { tipo } }),
        ]);
        rankings.value = rk.data;
        metas.value = mt.data;
    } catch {}
};

const buildProgressMap = () => {
    const mmap = {};
    metas.value.forEach(m => { mmap[m.clave] = parseInt(m.meta) || 0; });
    const pmap = {};
    rankings.value.forEach(r => {
        const meta = mmap[r.clave] || 1;
        pmap[r.clave] = {
            ratio: parseInt(r.total) / meta,
            total: parseInt(r.total),
            meta: mmap[r.clave] || 0,
        };
    });
    return pmap;
};

const sortedRankings = () => {
    const merged = rankings.value.map(r => {
        const mmap = {};
        metas.value.forEach(m => { mmap[m.clave] = parseInt(m.meta) || 0; });
        return {
            ...r,
            meta: mmap[r.clave] || 0,
            progress: mmap[r.clave] ? Math.round((parseInt(r.total) / (mmap[r.clave] || 1)) * 100) : 0,
        };
    });
    const mult = sortOrder.value === 'desc' ? -1 : 1;
    if (sortField.value === 'progress') {
        return merged.sort((a, b) => mult * (b.progress - a.progress));
    }
    return merged.sort((a, b) => mult * (parseInt(b.total) - parseInt(a.total)));
};

const toggleSort = (field) => {
    if (sortField.value === field) {
        sortOrder.value = sortOrder.value === 'desc' ? 'asc' : 'desc';
    } else {
        sortField.value = field;
        sortOrder.value = 'desc';
    }
};

const createGeoJSONLayer = (data, keyField, progressMap) => {
    return L.geoJSON(data, {
        style: (feature) => {
            const key = String(feature.properties[keyField]);
            const ratio = progressMap[key]?.ratio ?? 0;
            return {
                fillColor: progressColor(ratio),
                weight: 1.5,
                opacity: 1,
                color: '#d1d5db',
                fillOpacity: 0.55,
            };
        },
        onEachFeature: (feature, layer) => {
            const key = String(feature.properties[keyField]);
            const data = progressMap[key] ?? { ratio: 0, total: 0, meta: 0 };
            const ratio = data.ratio;
            const total = data.total;
            const meta = data.meta;
            const pct = Math.round(ratio * 100);
            let name = `Distrito ${key}`;
            if (keyField === 'seccion') {
                const dbMunicipio = seccionesMunicipios.value[key] || getMunicipioName(feature.properties.municipio);
                name = `Seccion ${feature.properties.seccion}<br>Municipio: ${dbMunicipio}`;
            } else if (keyField === 'nombre') {
                name = feature.properties.nombre;
            } else if (keyField === 'dmunicipal') {
                name = feature.properties.dmunicipal || `Demarcacion ${key}`;
            }
            layer.bindTooltip(`${name}<br>Promovidos: ${total}<br>Progreso: ${pct}%`, { sticky: true });
        },
    });
};

const getMunicipioName = (code) => {
    const names = {
        1:'ACAPONETA',2:'AHUACATLAN',3:'AMATLAN DE CAÑAS',4:'COMPOSTELA',
        5:'HUAJICORI',6:'IXTLAN DEL RIO',7:'JALA',8:'XALISCO',
        9:'DEL NAYAR',10:'ROSAMORADA',11:'RUIZ',12:'SAN BLAS',
        13:'SAN PEDRO LAGUNILLAS',14:'SANTA MARIA DEL ORO',15:'SANTIAGO IXCUINTLA',
        16:'TECUALA',17:'TEPIC',18:'TUXPAN',19:'LA YESCA',20:'BAHIA DE BANDERAS'
    };
    return names[code] || `Mun. ${code}`;
};

const clearLayer = () => {
    if (currentLayer && map) {
        map.removeLayer(currentLayer);
        currentLayer = null;
    }
};

const showLayer = async (tipo) => {
    if (layerType.value === tipo && currentLayer) return;
    layerType.value = tipo;
    clearLayer();
    loading.value = true;
    await fetchRankings(tipo);
    const progressMap = buildProgressMap();

    const keyField = tipo === 'seccion' ? 'seccion'
        : tipo === 'distrito_local' ? 'distrito_l'
        : tipo === 'municipio' ? 'nombre'
        : tipo === 'demarcacion' ? 'dmunicipal'
        : 'distrito_f';

    const fileName = tipo === 'distrito_local' ? 'distrito_local'
        : tipo === 'distrito_federal' ? 'distrito_federal'
        : tipo === 'municipio' ? 'municipio'
        : tipo === 'demarcacion' ? 'demarcacion'
        : 'seccion';

    if (!geoData[tipo]) {
        try {
            const res = await fetch(`/geo/${fileName}.json`);
            geoData[tipo] = await res.json();
        } catch {
            mapError.value = `Error al cargar capa de ${tipo}`;
            loading.value = false;
            return;
        }
    }

    currentLayer = createGeoJSONLayer(geoData[tipo], keyField, progressMap);
    currentLayer.addTo(map);
    loading.value = false;
};

onMounted(async () => {
    loading.value = true;

    try {
        const smRes = await axios.get('/api/secciones-municipios');
        seccionesMunicipios.value = smRes.data;
    } catch {}

    map = L.map(mapContainer.value, {
        center: [21.75, -105.0],
        zoom: 8,
        zoomControl: true,
        attributionControl: false,
    });

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; OpenStreetMap contributors',
    }).addTo(map);

    const FullscreenControl = L.Control.extend({
        onAdd: function () {
            const btn = L.DomUtil.create('button', 'leaflet-bar leaflet-control');
            btn.innerHTML = '<span style="font-size:16px;display:flex;align-items:center;justify-content:center;width:30px;height:30px">&#9974;</span>';
            btn.title = 'Pantalla completa';
            btn.style.cursor = 'pointer';
            btn.style.backgroundColor = '#fff';
            btn.style.border = '2px solid rgba(0,0,0,0.2)';
            btn.style.borderRadius = '2px';
            L.DomEvent.on(btn, 'click', () => {
                const el = mapContainer.value;
                if (document.fullscreenElement) { document.exitFullscreen(); }
                else { el.requestFullscreen(); }
            });
            return btn;
        },
    });
    new FullscreenControl({ position: 'topleft' }).addTo(map);

    try {
        const entRes = await fetch('/geo/entidad.json');
        const ent = await entRes.json();
        baseLayer = L.geoJSON(ent, {
            style: { color: '#6366f1', weight: 3, fillOpacity: 0, dashArray: '5 5' },
        }).addTo(map);
    } catch {
        map.setView([21.75, -105.0], 8);
    }

    await fetchRankings('municipio');
    const progressMap = buildProgressMap();

    try {
        const res = await fetch('/geo/municipio.json');
        geoData.municipio = await res.json();
        currentLayer = createGeoJSONLayer(geoData.municipio, 'nombre', progressMap);
        currentLayer.addTo(map);
    } catch (e) {
        mapError.value = 'Error al cargar capa de municipios';
    }

    loading.value = false;
});

const layerLabel = () => {
    const labels = { seccion: 'Sección', municipio: 'Municipio', demarcacion: 'Demarcación', distrito_local: 'Distrito Local', distrito_federal: 'Distrito Federal' };
    return labels[layerType.value] || '';
};
</script>

<template>
    <div class="flex flex-1 overflow-hidden">
        <div class="flex-1 relative">
            <div v-if="loading" class="absolute inset-0 z-[1000] flex items-center justify-center bg-white/80">
                <div class="text-center">
                    <div class="animate-spin w-10 h-10 border-4 border-guinda-600 border-t-transparent rounded-full mx-auto mb-3"></div>
                    <p class="text-gray-600 text-sm">Cargando mapa...</p>
                </div>
            </div>
            <div v-if="mapError" class="absolute top-4 left-1/2 -translate-x-1/2 z-[1000] bg-red-500 text-white px-4 py-2 rounded-lg text-sm shadow-lg">
                {{ mapError }}
            </div>
            <div ref="mapContainer" class="h-full w-full"></div>
        </div>

        <div class="w-96 bg-white shadow-xl flex flex-col z-10 border-l border-gray-200">
            <div class="p-4 border-b border-gray-200">
                <h2 class="text-lg font-bold text-gray-900">Mapa Electoral</h2>
                <p class="text-sm text-gray-500">Nayarit</p>
            </div>

            <div class="p-3 border-b border-gray-100 space-y-2">
                <div class="flex gap-1">
                    <button @click="showLayer('distrito_federal')" :class="['flex-1 py-2 text-xs font-medium rounded-lg transition-colors', layerType === 'distrito_federal' ? 'bg-guinda-600 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200']">Distrito Federal</button>
                    <button @click="showLayer('distrito_local')" :class="['flex-1 py-2 text-xs font-medium rounded-lg transition-colors', layerType === 'distrito_local' ? 'bg-guinda-600 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200']">Distrito Local</button>
                    <button @click="showLayer('municipio')" :class="['flex-1 py-2 text-xs font-medium rounded-lg transition-colors', layerType === 'municipio' ? 'bg-guinda-600 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200']">Municipio</button>
                </div>
                <div class="flex gap-1">
                    <button @click="showLayer('demarcacion')" :class="['flex-1 py-2 text-xs font-medium rounded-lg transition-colors', layerType === 'demarcacion' ? 'bg-guinda-600 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200']">Demarcación</button>
                    <button @click="showLayer('seccion')" :class="['flex-1 py-2 text-xs font-medium rounded-lg transition-colors', layerType === 'seccion' ? 'bg-guinda-600 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200']">Sección</button>
                </div>
            </div>

            <div class="p-3 border-b border-gray-100">
                <p class="text-xs font-medium text-gray-500 uppercase mb-2">Progreso vs Meta</p>
                <div class="flex h-4 rounded overflow-hidden">
                    <div class="flex-1" style="background:#ef4444"></div>
                    <div class="flex-1" style="background:#f97316"></div>
                    <div class="flex-1" style="background:#facc15"></div>
                    <div class="flex-1" style="background:#86efac"></div>
                    <div class="flex-1" style="background:#22c55e"></div>
                    <div class="flex-1" style="background:#166534"></div>
                </div>
                <div class="flex justify-between text-xs text-gray-400 mt-1">
                    <span>0%</span>
                    <span>20%</span>
                    <span>40%</span>
                    <span>60%</span>
                    <span>80%</span>
                    <span>100%</span>
                </div>
            </div>

            <div class="flex-1 overflow-y-auto">
                <div class="p-3">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b border-gray-200">
                                <th class="text-left py-2 text-xs font-semibold text-gray-600 uppercase">{{ layerLabel() }}</th>
                                <th class="text-center py-2 text-xs font-semibold text-gray-600 uppercase cursor-pointer select-none hover:text-guinda-600 w-10" title="Promovidos" @click="toggleSort('total')">
                                    <div class="flex items-center justify-center gap-0.5">
                                        <CircleUser :size="14" :stroke-width="2.5" />
                                        <span v-if="sortField === 'total'" class="inline-block"><svg v-if="sortOrder === 'desc'" class="w-2.5 h-2.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/></svg><svg v-else class="w-2.5 h-2.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M14.707 12.707a1 1 0 01-1.414 0L10 9.414l-3.293 3.293a1 1 0 01-1.414-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 010 1.414z" clip-rule="evenodd"/></svg></span>
                                    </div>
                                </th>
                                <th class="text-center py-2 text-xs font-semibold text-gray-600 uppercase cursor-pointer select-none hover:text-guinda-600 w-10" title="Porcentaje" @click="toggleSort('progress')">
                                    <div class="flex items-center justify-center gap-0.5">
                                        <CirclePercent :size="14" :stroke-width="2.5" />
                                        <span v-if="sortField === 'progress'" class="inline-block"><svg v-if="sortOrder === 'desc'" class="w-2.5 h-2.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/></svg><svg v-else class="w-2.5 h-2.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M14.707 12.707a1 1 0 01-1.414 0L10 9.414l-3.293 3.293a1 1 0 01-1.414-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 010 1.414z" clip-rule="evenodd"/></svg></span>
                                    </div>
                                </th>
                                <th class="text-center py-2 text-xs font-semibold text-gray-600 uppercase w-10">Meta</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-if="sortedRankings().length === 0">
                                <td colspan="4" class="py-8 text-center text-gray-400">Sin datos aún</td>
                            </tr>
                            <tr v-for="(item, idx) in sortedRankings().slice(0, 50)" :key="idx" class="border-b border-gray-50 hover:bg-gray-50">
                                <td class="py-1.5 pr-2 max-w-[180px]">
                                    <div class="flex items-center gap-1.5">
                                        <span class="text-xs text-gray-400 w-5 flex-shrink-0 text-right">{{ idx + 1 }}.</span>
                                        <div class="flex-1 min-w-0 relative">
                                            <div class="absolute inset-y-0 left-0 rounded-r opacity-30" :style="{ width: item.progress + '%', backgroundColor: progressColor(item.progress / 100) }"></div>
                                            <span class="relative truncate block text-gray-700 text-xs">{{ item.clave }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-1.5 text-center font-semibold text-guinda-600 text-xs w-10">{{ item.total }}</td>
                                <td class="py-1.5 text-center text-xs font-medium w-10" :class="item.progress >= 80 ? 'text-guinda-700' : item.progress >= 40 ? 'text-yellow-600' : 'text-red-500'">{{ item.progress }}%</td>
                                <td class="py-1.5 text-center text-xs text-gray-500 w-10">{{ item.meta }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</template>
