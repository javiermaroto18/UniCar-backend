<script setup>
import { Head } from '@inertiajs/vue3';
import AdminLayout from '../../Layouts/AdminLayout.vue';

defineProps({
    stats: { type: Object, required: true },
    tripsByStatus: { type: Object, required: true },
});

const cards = (stats) => [
    { label: 'Usuarios', value: stats.users, color: 'bg-indigo-500' },
    { label: 'Viajes', value: stats.trips, color: 'bg-emerald-500' },
    { label: 'Reservas', value: stats.bookings, color: 'bg-amber-500' },
    { label: 'Vehículos activos', value: stats.vehicles, color: 'bg-sky-500' },
];
</script>

<template>
    <Head title="Dashboard" />

    <AdminLayout>
        <template #title>Resumen general</template>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-10">
            <div
                v-for="card in cards(stats)"
                :key="card.label"
                class="bg-white rounded-xl border border-slate-200 p-5"
            >
                <div :class="[card.color, 'w-10 h-10 rounded-lg mb-3']"></div>
                <p class="text-3xl font-bold text-slate-800">{{ card.value }}</p>
                <p class="text-sm text-slate-500">{{ card.label }}</p>
            </div>
        </div>

        <h3 class="text-base font-semibold text-slate-800 mb-4">Viajes por estado</h3>
        <div class="bg-white rounded-xl border border-slate-200 p-5 flex gap-10">
            <div>
                <p class="text-2xl font-bold text-emerald-600">{{ tripsByStatus.scheduled }}</p>
                <p class="text-sm text-slate-500">Programados</p>
            </div>
            <div>
                <p class="text-2xl font-bold text-slate-600">{{ tripsByStatus.completed }}</p>
                <p class="text-sm text-slate-500">Completados</p>
            </div>
            <div>
                <p class="text-2xl font-bold text-red-500">{{ tripsByStatus.cancelled }}</p>
                <p class="text-sm text-slate-500">Cancelados</p>
            </div>
        </div>
    </AdminLayout>
</template>
