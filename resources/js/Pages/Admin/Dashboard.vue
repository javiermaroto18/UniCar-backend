<script setup>
import { Head } from '@inertiajs/vue3';
import AdminLayout from '../../Layouts/AdminLayout.vue';

defineProps({
    stats: { type: Object, required: true },
    tripsByStatus: { type: Object, required: true },
});

const cards = (stats) => [
    { label: 'Usuarios', value: stats.users, accent: 'text-unicar-primary', ring: 'ring-unicar-primary/30', bg: 'bg-unicar-primary/15' },
    { label: 'Viajes', value: stats.trips, accent: 'text-emerald-400', ring: 'ring-emerald-500/30', bg: 'bg-emerald-500/15' },
    { label: 'Reservas', value: stats.bookings, accent: 'text-amber-400', ring: 'ring-amber-500/30', bg: 'bg-amber-500/15' },
    { label: 'Vehículos activos', value: stats.vehicles, accent: 'text-sky-400', ring: 'ring-sky-500/30', bg: 'bg-sky-500/15' },
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
                class="bg-unicar-surface rounded-xl border border-unicar-border p-5"
            >
                <div :class="['flex h-10 w-10 items-center justify-center rounded-lg mb-4 ring-1', card.bg, card.ring]">
                    <span :class="['h-2.5 w-2.5 rounded-full bg-current', card.accent]"></span>
                </div>
                <p class="text-3xl font-bold">{{ card.value }}</p>
                <p class="text-sm text-unicar-muted mt-0.5">{{ card.label }}</p>
            </div>
        </div>

        <h3 class="text-sm font-semibold text-unicar-muted uppercase tracking-wide mb-4">Viajes por estado</h3>
        <div class="bg-unicar-surface rounded-xl border border-unicar-border p-6 flex flex-wrap gap-10">
            <div>
                <p class="text-2xl font-bold text-emerald-400">{{ tripsByStatus.scheduled }}</p>
                <p class="text-sm text-unicar-muted">Programados</p>
            </div>
            <div>
                <p class="text-2xl font-bold text-unicar-muted">{{ tripsByStatus.completed }}</p>
                <p class="text-sm text-unicar-muted">Completados</p>
            </div>
            <div>
                <p class="text-2xl font-bold text-red-400">{{ tripsByStatus.cancelled }}</p>
                <p class="text-sm text-unicar-muted">Cancelados</p>
            </div>
        </div>
    </AdminLayout>
</template>
