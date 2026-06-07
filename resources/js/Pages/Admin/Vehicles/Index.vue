<script setup>
import { Head, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import AdminLayout from '../../../Layouts/AdminLayout.vue';
import Pagination from '../../../Components/Pagination.vue';

const props = defineProps({
    vehicles: { type: Object, required: true },
    filters: { type: Object, required: true },
});

const search = ref(props.filters.search || '');
let timeout = null;

watch(search, (value) => {
    clearTimeout(timeout);
    timeout = setTimeout(() => {
        router.get('/admin/vehicles', { search: value }, { preserveState: true, replace: true });
    }, 300);
});

function toggle(v) {
    const verb = v.is_active ? 'desactivar' : 'reactivar';
    if (confirm(`¿Seguro que quieres ${verb} el vehículo "${v.brand_model}"?`)) {
        router.patch(`/admin/vehicles/${v.id}/toggle`, {}, { preserveScroll: true });
    }
}
</script>

<template>
    <Head title="Vehículos" />

    <AdminLayout>
        <template #title>Gestión de vehículos</template>

        <input
            v-model="search"
            type="search"
            placeholder="Buscar por modelo o matrícula…"
            class="w-full max-w-sm mb-4 rounded-lg bg-unicar-surface border border-unicar-border px-3 py-2 text-sm text-unicar-text placeholder-unicar-dim focus:border-unicar-primary focus:ring-1 focus:ring-unicar-primary outline-none"
        />

        <div class="bg-unicar-surface rounded-xl border border-unicar-border overflow-hidden">
            <table class="w-full text-sm">
                <thead class="bg-white/5 text-unicar-dim text-left text-xs uppercase tracking-wide">
                    <tr>
                        <th class="px-4 py-3 font-medium">#</th>
                        <th class="px-4 py-3 font-medium">Propietario</th>
                        <th class="px-4 py-3 font-medium">Modelo</th>
                        <th class="px-4 py-3 font-medium">Matrícula</th>
                        <th class="px-4 py-3 font-medium text-center">Viajes</th>
                        <th class="px-4 py-3 font-medium text-center">Frecuente</th>
                        <th class="px-4 py-3 font-medium text-center">Estado</th>
                        <th class="px-4 py-3 font-medium text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-unicar-border">
                    <tr v-for="v in vehicles.data" :key="v.id" class="hover:bg-white/5 transition">
                        <td class="px-4 py-3 text-unicar-dim">{{ v.id }}</td>
                        <td class="px-4 py-3 text-unicar-muted">{{ v.owner || '—' }}</td>
                        <td class="px-4 py-3 font-medium text-unicar-text">{{ v.brand_model }}</td>
                        <td class="px-4 py-3 text-unicar-muted font-mono">{{ v.license_plate }}</td>
                        <td class="px-4 py-3 text-center text-unicar-muted">{{ v.trips_count }}</td>
                        <td class="px-4 py-3 text-center">
                            <span v-if="v.is_frequent" class="text-unicar-primary">★</span>
                            <span v-else class="text-unicar-dim">—</span>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <span
                                :class="[
                                    'px-2 py-1 rounded-full text-xs font-medium ring-1',
                                    v.is_active ? 'bg-emerald-500/15 text-emerald-300 ring-emerald-500/30' : 'bg-white/5 text-unicar-muted ring-unicar-border-strong',
                                ]"
                            >
                                {{ v.is_active ? 'Activo' : 'Inactivo' }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-right">
                            <button
                                @click="toggle(v)"
                                :class="['text-xs font-medium', v.is_active ? 'text-red-400 hover:text-red-300' : 'text-emerald-400 hover:text-emerald-300']"
                            >
                                {{ v.is_active ? 'Desactivar' : 'Reactivar' }}
                            </button>
                        </td>
                    </tr>
                    <tr v-if="vehicles.data.length === 0">
                        <td colspan="8" class="px-4 py-10 text-center text-unicar-dim">No se encontraron vehículos.</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <Pagination :links="vehicles.links" />
    </AdminLayout>
</template>
