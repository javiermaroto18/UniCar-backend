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
            class="w-full max-w-sm mb-4 rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 outline-none"
        />

        <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 text-slate-500 text-left">
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
                <tbody class="divide-y divide-slate-100">
                    <tr v-for="v in vehicles.data" :key="v.id" class="hover:bg-slate-50">
                        <td class="px-4 py-3 text-slate-400">{{ v.id }}</td>
                        <td class="px-4 py-3 text-slate-700">{{ v.owner || '—' }}</td>
                        <td class="px-4 py-3 font-medium text-slate-800">{{ v.brand_model }}</td>
                        <td class="px-4 py-3 text-slate-600 font-mono">{{ v.license_plate }}</td>
                        <td class="px-4 py-3 text-center text-slate-600">{{ v.trips_count }}</td>
                        <td class="px-4 py-3 text-center">
                            <span v-if="v.is_frequent" class="text-xs text-indigo-600">★</span>
                            <span v-else class="text-xs text-slate-300">—</span>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <span
                                :class="[
                                    'px-2 py-1 rounded-full text-xs font-medium',
                                    v.is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-500',
                                ]"
                            >
                                {{ v.is_active ? 'Activo' : 'Inactivo' }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-right">
                            <button
                                @click="toggle(v)"
                                :class="['text-xs font-medium', v.is_active ? 'text-red-600 hover:text-red-800' : 'text-emerald-600 hover:text-emerald-800']"
                            >
                                {{ v.is_active ? 'Desactivar' : 'Reactivar' }}
                            </button>
                        </td>
                    </tr>
                    <tr v-if="vehicles.data.length === 0">
                        <td colspan="8" class="px-4 py-8 text-center text-slate-400">No se encontraron vehículos.</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <Pagination :links="vehicles.links" />
    </AdminLayout>
</template>
