<script setup>
import { Head, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import AdminLayout from '../../../Layouts/AdminLayout.vue';
import Pagination from '../../../Components/Pagination.vue';

const props = defineProps({
    bookings: { type: Object, required: true },
    filters: { type: Object, required: true },
});

const status = ref(props.filters.status || '');

watch(status, (value) => {
    router.get('/admin/bookings', { status: value }, { preserveState: true, replace: true });
});

function cancelBooking(b) {
    if (confirm(`¿Cancelar la reserva #${b.id}? Se liberará la plaza en el viaje.`)) {
        router.patch(`/admin/bookings/${b.id}/cancel`, {}, { preserveScroll: true });
    }
}

const statusStyle = {
    pending: 'bg-amber-100 text-amber-700',
    paid: 'bg-emerald-100 text-emerald-700',
    cancelled: 'bg-red-100 text-red-600',
};
const statusLabel = { pending: 'Pendiente', paid: 'Pagada', cancelled: 'Cancelada' };
</script>

<template>
    <Head title="Reservas" />

    <AdminLayout>
        <template #title>Gestión de reservas</template>

        <select
            v-model="status"
            class="mb-4 rounded-lg border border-slate-300 px-3 py-2 text-sm bg-white focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 outline-none"
        >
            <option value="">Todos los estados</option>
            <option value="pending">Pendientes</option>
            <option value="paid">Pagadas</option>
            <option value="cancelled">Canceladas</option>
        </select>

        <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 text-slate-500 text-left">
                    <tr>
                        <th class="px-4 py-3 font-medium">#</th>
                        <th class="px-4 py-3 font-medium">Pasajero</th>
                        <th class="px-4 py-3 font-medium">Viaje</th>
                        <th class="px-4 py-3 font-medium text-center">Plazas</th>
                        <th class="px-4 py-3 font-medium text-right">Total</th>
                        <th class="px-4 py-3 font-medium text-center">Estado</th>
                        <th class="px-4 py-3 font-medium">Fecha</th>
                        <th class="px-4 py-3 font-medium text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <tr v-for="b in bookings.data" :key="b.id" class="hover:bg-slate-50">
                        <td class="px-4 py-3 text-slate-400">{{ b.id }}</td>
                        <td class="px-4 py-3 font-medium text-slate-800">{{ b.passenger || '—' }}</td>
                        <td class="px-4 py-3 text-slate-600">{{ b.trip }}</td>
                        <td class="px-4 py-3 text-center text-slate-600">{{ b.seats_booked }}</td>
                        <td class="px-4 py-3 text-right text-slate-600">{{ b.total_price }} €</td>
                        <td class="px-4 py-3 text-center">
                            <span :class="['px-2 py-1 rounded-full text-xs font-medium', statusStyle[b.status]]">
                                {{ statusLabel[b.status] }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-slate-500">{{ b.created_at }}</td>
                        <td class="px-4 py-3 text-right">
                            <button
                                v-if="b.status !== 'cancelled'"
                                @click="cancelBooking(b)"
                                class="text-xs text-red-600 hover:text-red-800 font-medium"
                            >
                                Cancelar
                            </button>
                            <span v-else class="text-xs text-slate-300">—</span>
                        </td>
                    </tr>
                    <tr v-if="bookings.data.length === 0">
                        <td colspan="8" class="px-4 py-8 text-center text-slate-400">No se encontraron reservas.</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <Pagination :links="bookings.links" />
    </AdminLayout>
</template>
