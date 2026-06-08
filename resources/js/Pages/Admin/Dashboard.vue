<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { computed } from 'vue';
import AdminLayout from '../../Layouts/AdminLayout.vue';

const props = defineProps({
    stats: { type: Object, required: true },
    tripsByStatus: { type: Object, required: true },
    bookingsByStatus: { type: Object, required: true },
    recentTrips: { type: Array, required: true },
});

const cards = computed(() => [
    { label: 'Usuarios', value: props.stats.users, accent: 'text-unicar-primary', bg: 'bg-unicar-primary/15', href: '/admin/users', icon: 'users' },
    { label: 'Viajes', value: props.stats.trips, accent: 'text-emerald-400', bg: 'bg-emerald-500/15', href: '/admin/trips', icon: 'trip' },
    { label: 'Reservas', value: props.stats.bookings, accent: 'text-amber-400', bg: 'bg-amber-500/15', href: '/admin/bookings', icon: 'booking' },
    { label: 'Vehículos activos', value: props.stats.vehicles, accent: 'text-sky-400', bg: 'bg-sky-500/15', href: '/admin/vehicles', icon: 'vehicle' },
]);

function segments(obj, palette) {
    const total = Object.values(obj).reduce((a, b) => a + b, 0) || 1;
    return Object.entries(obj).map(([key, value]) => ({
        key,
        value,
        pct: (value / total) * 100,
        ...palette[key],
    }));
}

const tripSegs = computed(() =>
    segments(props.tripsByStatus, {
        scheduled: { label: 'Programados', bar: 'bg-emerald-500', text: 'text-emerald-400' },
        completed: { label: 'Completados', bar: 'bg-slate-500', text: 'text-unicar-muted' },
        cancelled: { label: 'Cancelados', bar: 'bg-red-500', text: 'text-red-400' },
    })
);

const bookingSegs = computed(() =>
    segments(props.bookingsByStatus, {
        pending: { label: 'Pendientes', bar: 'bg-amber-500', text: 'text-amber-400' },
        paid: { label: 'Pagadas', bar: 'bg-emerald-500', text: 'text-emerald-400' },
        cancelled: { label: 'Canceladas', bar: 'bg-red-500', text: 'text-red-400' },
    })
);

const statusStyle = {
    scheduled: 'bg-emerald-500/15 text-emerald-300',
    completed: 'bg-white/5 text-unicar-muted',
    cancelled: 'bg-red-500/15 text-red-300',
};
const statusLabel = { scheduled: 'Programado', completed: 'Completado', cancelled: 'Cancelado' };

function formatDate(dt) {
    if (!dt) return '—';
    return new Date(dt).toLocaleDateString('es-ES', { day: '2-digit', month: 'short' });
}
</script>

<template>
    <Head title="Dashboard" />

    <AdminLayout>
        <template #title>Resumen general</template>

        <!-- Tarjetas de estadísticas (clicables) -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
            <Link
                v-for="card in cards"
                :key="card.label"
                :href="card.href"
                class="bg-unicar-surface rounded-xl border border-unicar-border p-5 hover:border-unicar-border-strong transition group"
            >
                <div :class="['flex h-10 w-10 items-center justify-center rounded-lg mb-4', card.bg]">
                    <svg viewBox="0 0 24 24" fill="currentColor" :class="['h-5 w-5', card.accent]">
                        <path v-if="card.icon === 'users'" d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5c-1.66 0-3 1.34-3 3s1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5C6.34 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.97 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5z" />
                        <path v-else-if="card.icon === 'trip'" d="M18.92 6.01C18.72 5.42 18.16 5 17.5 5h-11c-.66 0-1.21.42-1.42 1.01L3 12v8c0 .55.45 1 1 1h1c.55 0 1-.45 1-1v-1h12v1c0 .55.45 1 1 1h1c.55 0 1-.45 1-1v-8l-2.08-5.99zM6.5 16c-.83 0-1.5-.67-1.5-1.5S5.67 13 6.5 13s1.5.67 1.5 1.5S7.33 16 6.5 16zm11 0c-.83 0-1.5-.67-1.5-1.5s.67-1.5 1.5-1.5 1.5.67 1.5 1.5-.67 1.5-1.5 1.5zM5 11l1.5-4.5h11L19 11H5z" />
                        <path v-else-if="card.icon === 'booking'" d="M17 12h-5v5h5v-5zM16 1v2H8V1H6v2H5c-1.11 0-1.99.9-1.99 2L3 19c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2h-1V1h-2zm3 18H5V8h14v11z" />
                        <path v-else d="M18.92 6.01C18.72 5.42 18.16 5 17.5 5h-11c-.66 0-1.21.42-1.42 1.01L3 12v8c0 .55.45 1 1 1h1c.55 0 1-.45 1-1v-1h12v1c0 .55.45 1 1 1h1c.55 0 1-.45 1-1v-8l-2.08-5.99zM6.5 16c-.83 0-1.5-.67-1.5-1.5S5.67 13 6.5 13s1.5.67 1.5 1.5S7.33 16 6.5 16zm11 0c-.83 0-1.5-.67-1.5-1.5s.67-1.5 1.5-1.5 1.5.67 1.5 1.5-.67 1.5-1.5 1.5zM5 11l1.5-4.5h11L19 11H5z" />
                    </svg>
                </div>
                <p class="text-3xl font-bold">{{ card.value }}</p>
                <p class="text-sm text-unicar-muted mt-0.5 group-hover:text-unicar-text transition">{{ card.label }}</p>
            </Link>
        </div>

        <!-- Desgloses por estado -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mb-8">
            <div class="bg-unicar-surface rounded-xl border border-unicar-border p-5">
                <h3 class="text-sm font-semibold text-unicar-muted uppercase tracking-wide mb-4">Viajes por estado</h3>
                <div class="flex h-2.5 w-full rounded-full overflow-hidden bg-unicar-bg mb-4">
                    <div v-for="s in tripSegs" :key="s.key" :class="s.bar" :style="{ width: s.pct + '%' }"></div>
                </div>
                <div class="space-y-2">
                    <div v-for="s in tripSegs" :key="s.key" class="flex items-center justify-between text-sm">
                        <span class="flex items-center gap-2 text-unicar-muted">
                            <span :class="['h-2.5 w-2.5 rounded-full', s.bar]"></span>{{ s.label }}
                        </span>
                        <span :class="['font-semibold', s.text]">{{ s.value }}</span>
                    </div>
                </div>
            </div>

            <div class="bg-unicar-surface rounded-xl border border-unicar-border p-5">
                <h3 class="text-sm font-semibold text-unicar-muted uppercase tracking-wide mb-4">Reservas por estado</h3>
                <div class="flex h-2.5 w-full rounded-full overflow-hidden bg-unicar-bg mb-4">
                    <div v-for="s in bookingSegs" :key="s.key" :class="s.bar" :style="{ width: s.pct + '%' }"></div>
                </div>
                <div class="space-y-2">
                    <div v-for="s in bookingSegs" :key="s.key" class="flex items-center justify-between text-sm">
                        <span class="flex items-center gap-2 text-unicar-muted">
                            <span :class="['h-2.5 w-2.5 rounded-full', s.bar]"></span>{{ s.label }}
                        </span>
                        <span :class="['font-semibold', s.text]">{{ s.value }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Actividad reciente -->
        <div class="bg-unicar-surface rounded-xl border border-unicar-border overflow-hidden">
            <div class="flex items-center justify-between px-5 py-4 border-b border-unicar-border">
                <h3 class="text-sm font-semibold text-unicar-muted uppercase tracking-wide">Últimos viajes publicados</h3>
                <Link href="/admin/trips" class="text-xs text-unicar-primary hover:underline">Ver todos</Link>
            </div>
            <table class="w-full text-sm">
                <tbody class="divide-y divide-unicar-border">
                    <tr v-for="t in recentTrips" :key="t.id" class="hover:bg-white/5 transition">
                        <td class="px-5 py-3 text-unicar-dim w-10">{{ t.id }}</td>
                        <td class="px-2 py-3 font-medium text-unicar-text">{{ t.route }}</td>
                        <td class="px-2 py-3 text-unicar-muted">{{ t.driver || '—' }}</td>
                        <td class="px-2 py-3 text-unicar-dim">{{ formatDate(t.departure_time) }}</td>
                        <td class="px-5 py-3 text-right">
                            <span :class="['px-2 py-1 rounded-full text-xs font-medium', statusStyle[t.status]]">
                                {{ statusLabel[t.status] }}
                            </span>
                        </td>
                    </tr>
                    <tr v-if="recentTrips.length === 0">
                        <td colspan="5" class="px-5 py-8 text-center text-unicar-dim">Aún no hay viajes.</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </AdminLayout>
</template>
