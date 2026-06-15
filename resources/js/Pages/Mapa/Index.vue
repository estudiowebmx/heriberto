<script setup>
import { Head } from "@inertiajs/vue3";
//import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import MapLayout from "@/Layouts/MapLayout.vue";
import MapView from "@/Components/MapView.vue";

defineOptions({ layout: MapLayout });
</script>

<template>
    <Head title="Mapa Electoral - Nayarit" />
    <MapView />
</template>
