<script setup>
import { Head, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import AdminLayout from '../../../Layouts/AdminLayout.vue';
import Pagination from '../../../Components/Pagination.vue';

const props = defineProps({
    trips: { type: Object, required: true },
    filters: { type: Object, required: true },
});

const search = ref(props.filters.search || '');
const status = ref(props.filters.status || '');
let timeout = null;

function reload() {
    router.get('/admin/trips', { search: search.value, status: status.value }, { preserveState: true, replace: true });
}

watch(search, () => {
    clearTimeout(timeout);
    timeout = setTimeout(reload, 300);
});
watch(status, reload);

function cancelTrip(trip) {
    if (confirm(`¿Cancelar el viaje #${trip.id}? Se anularán también sus reservas.`)) {
        router.patch(`/admin/trips/${trip.id}/cancel`, {}, { preserveScroll: true });
    }
}

const statusStyle = {
    scheduled: 'bg-emerald-500/15 text-emerald-300 ring-emerald-500/30',
    completed: 'bg-white/5 text-unicar-muted ring-unicar-border-strong',
    cancelled: 'bg-red-500/15 text-red-300 ring-red-500/30',
};
const statusLabel = { scheduled: 'Programado', completed: 'Completado', cancelled: 'Cancelado' };

function formatDate(dt) {
    if (!dt) return '—';
    return new Date(dt).toLocaleString('es-ES', { dateStyle: 'short', timeStyle: 'short' });
}
</script>

<template>
    <Head title="Viajes" />

    <AdminLayout>
        <template #title>Gestión de viajes</template>

        <div class="flex flex-wrap gap-3 mb-4">
            <input
                v-model="search"
                type="search"
                placeholder="Buscar por origen o destino…"
                class="flex-1 min-w-[200px] max-w-sm rounded-lg bg-unicar-surface border border-unicar-border px-3 py-2 text-sm text-unicar-text placeholder-unicar-dim focus:border-unicar-primary focus:ring-1 focus:ring-unicar-primary outline-none"
            />
            <select
                v-model="status"
                class="rounded-lg bg-unicar-surface border border-unicar-border px-3 py-2 text-sm text-unicar-text focus:border-unicar-primary focus:ring-1 focus:ring-unicar-primary outline-none"
            >
                <option value="">Todos los estados</option>
                <option value="scheduled">Programados</option>
                <option value="completed">Completados</option>
                <option value="cancelled">Cancelados</option>
            </select>
        </div>

        <div class="bg-unicar-surface rounded-xl border border-unicar-border overflow-hidden">
            <table class="w-full text-sm">
                <thead class="bg-white/5 text-unicar-dim text-left text-xs uppercase tracking-wide">
                    <tr>
                        <th class="px-4 py-3 font-medium">#</th>
                        <th class="px-4 py-3 font-medium">Conductor</th>
                        <th class="px-4 py-3 font-medium">Ruta</th>
                        <th class="px-4 py-3 font-medium">Salida</th>
                        <th class="px-4 py-3 font-medium text-center">Plazas</th>
                        <th class="px-4 py-3 font-medium text-right">Precio</th>
                        <th class="px-4 py-3 font-medium text-center">Estado</th>
                        <th class="px-4 py-3 font-medium text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-unicar-border">
                    <tr v-for="t in trips.data" :key="t.id" class="hover:bg-white/5 transition">
                        <td class="px-4 py-3 text-unicar-dim">{{ t.id }}</td>
                        <td class="px-4 py-3 text-unicar-muted">{{ t.driver || '—' }}</td>
                        <td class="px-4 py-3 font-medium text-unicar-text">{{ t.origin }} → {{ t.destination }}</td>
                        <td class="px-4 py-3 text-unicar-muted">{{ formatDate(t.departure_time) }}</td>
                        <td class="px-4 py-3 text-center text-unicar-muted">{{ t.seats_available }}/{{ t.seats_total }}</td>
                        <td class="px-4 py-3 text-right text-unicar-muted">{{ t.price_per_seat }} €</td>
                        <td class="px-4 py-3 text-center">
                            <span :class="['px-2 py-1 rounded-full text-xs font-medium ring-1', statusStyle[t.status]]">
                                {{ statusLabel[t.status] }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-right">
                            <button
                                v-if="t.status === 'scheduled'"
                                @click="cancelTrip(t)"
                                class="text-xs text-red-400 hover:text-red-300 font-medium"
                            >
                                Cancelar
                            </button>
                            <span v-else class="text-xs text-unicar-dim">—</span>
                        </td>
                    </tr>
                    <tr v-if="trips.data.length === 0">
                        <td colspan="8" class="px-4 py-10 text-center text-unicar-dim">No se encontraron viajes.</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <Pagination :links="trips.links" />
    </AdminLayout>
</template>
