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
    scheduled: 'bg-emerald-100 text-emerald-700',
    completed: 'bg-slate-100 text-slate-600',
    cancelled: 'bg-red-100 text-red-600',
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
                class="flex-1 min-w-[200px] max-w-sm rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 outline-none"
            />
            <select
                v-model="status"
                class="rounded-lg border border-slate-300 px-3 py-2 text-sm bg-white focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 outline-none"
            >
                <option value="">Todos los estados</option>
                <option value="scheduled">Programados</option>
                <option value="completed">Completados</option>
                <option value="cancelled">Cancelados</option>
            </select>
        </div>

        <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 text-slate-500 text-left">
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
                <tbody class="divide-y divide-slate-100">
                    <tr v-for="t in trips.data" :key="t.id" class="hover:bg-slate-50">
                        <td class="px-4 py-3 text-slate-400">{{ t.id }}</td>
                        <td class="px-4 py-3 text-slate-700">{{ t.driver || '—' }}</td>
                        <td class="px-4 py-3 font-medium text-slate-800">{{ t.origin }} → {{ t.destination }}</td>
                        <td class="px-4 py-3 text-slate-600">{{ formatDate(t.departure_time) }}</td>
                        <td class="px-4 py-3 text-center text-slate-600">{{ t.seats_available }}/{{ t.seats_total }}</td>
                        <td class="px-4 py-3 text-right text-slate-600">{{ t.price_per_seat }} €</td>
                        <td class="px-4 py-3 text-center">
                            <span :class="['px-2 py-1 rounded-full text-xs font-medium', statusStyle[t.status]]">
                                {{ statusLabel[t.status] }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-right">
                            <button
                                v-if="t.status === 'scheduled'"
                                @click="cancelTrip(t)"
                                class="text-xs text-red-600 hover:text-red-800 font-medium"
                            >
                                Cancelar
                            </button>
                            <span v-else class="text-xs text-slate-300">—</span>
                        </td>
                    </tr>
                    <tr v-if="trips.data.length === 0">
                        <td colspan="8" class="px-4 py-8 text-center text-slate-400">No se encontraron viajes.</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <Pagination :links="trips.links" />
    </AdminLayout>
</template>
