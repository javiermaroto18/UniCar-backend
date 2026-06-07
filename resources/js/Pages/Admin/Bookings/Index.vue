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
    pending: 'bg-amber-500/15 text-amber-300 ring-amber-500/30',
    paid: 'bg-emerald-500/15 text-emerald-300 ring-emerald-500/30',
    cancelled: 'bg-red-500/15 text-red-300 ring-red-500/30',
};
const statusLabel = { pending: 'Pendiente', paid: 'Pagada', cancelled: 'Cancelada' };
</script>

<template>
    <Head title="Reservas" />

    <AdminLayout>
        <template #title>Gestión de reservas</template>

        <select
            v-model="status"
            class="mb-4 rounded-lg bg-unicar-surface border border-unicar-border px-3 py-2 text-sm text-unicar-text focus:border-unicar-primary focus:ring-1 focus:ring-unicar-primary outline-none"
        >
            <option value="">Todos los estados</option>
            <option value="pending">Pendientes</option>
            <option value="paid">Pagadas</option>
            <option value="cancelled">Canceladas</option>
        </select>

        <div class="bg-unicar-surface rounded-xl border border-unicar-border overflow-hidden">
            <table class="w-full text-sm">
                <thead class="bg-white/5 text-unicar-dim text-left text-xs uppercase tracking-wide">
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
                <tbody class="divide-y divide-unicar-border">
                    <tr v-for="b in bookings.data" :key="b.id" class="hover:bg-white/5 transition">
                        <td class="px-4 py-3 text-unicar-dim">{{ b.id }}</td>
                        <td class="px-4 py-3 font-medium text-unicar-text">{{ b.passenger || '—' }}</td>
                        <td class="px-4 py-3 text-unicar-muted">{{ b.trip }}</td>
                        <td class="px-4 py-3 text-center text-unicar-muted">{{ b.seats_booked }}</td>
                        <td class="px-4 py-3 text-right text-unicar-muted">{{ b.total_price }} €</td>
                        <td class="px-4 py-3 text-center">
                            <span :class="['px-2 py-1 rounded-full text-xs font-medium ring-1', statusStyle[b.status]]">
                                {{ statusLabel[b.status] }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-unicar-dim">{{ b.created_at }}</td>
                        <td class="px-4 py-3 text-right">
                            <button
                                v-if="b.status !== 'cancelled'"
                                @click="cancelBooking(b)"
                                class="text-xs text-red-400 hover:text-red-300 font-medium"
                            >
                                Cancelar
                            </button>
                            <span v-else class="text-xs text-unicar-dim">—</span>
                        </td>
                    </tr>
                    <tr v-if="bookings.data.length === 0">
                        <td colspan="8" class="px-4 py-10 text-center text-unicar-dim">No se encontraron reservas.</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <Pagination :links="bookings.links" />
    </AdminLayout>
</template>
