<script setup>
import { Head, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import AdminLayout from '../../../Layouts/AdminLayout.vue';
import Pagination from '../../../Components/Pagination.vue';

const props = defineProps({
    users: { type: Object, required: true },
    filters: { type: Object, required: true },
});

const search = ref(props.filters.search || '');
let timeout = null;

watch(search, (value) => {
    clearTimeout(timeout);
    timeout = setTimeout(() => {
        router.get('/admin/users', { search: value }, { preserveState: true, replace: true });
    }, 300);
});

function toggleVerified(user) {
    router.patch(`/admin/users/${user.id}`, { action: 'toggle_verified' }, { preserveScroll: true });
}

function toggleAdmin(user) {
    router.patch(`/admin/users/${user.id}`, { action: 'toggle_admin' }, { preserveScroll: true });
}

function destroy(user) {
    if (confirm(`¿Eliminar al usuario "${user.name}"? Esta acción no se puede deshacer.`)) {
        router.delete(`/admin/users/${user.id}`, { preserveScroll: true });
    }
}
</script>

<template>
    <Head title="Usuarios" />

    <AdminLayout>
        <template #title>Gestión de usuarios</template>

        <input
            v-model="search"
            type="search"
            placeholder="Buscar por nombre o email…"
            class="w-full max-w-sm mb-4 rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 outline-none"
        />

        <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 text-slate-500 text-left">
                    <tr>
                        <th class="px-4 py-3 font-medium">#</th>
                        <th class="px-4 py-3 font-medium">Nombre</th>
                        <th class="px-4 py-3 font-medium">Email</th>
                        <th class="px-4 py-3 font-medium text-center">Viajes</th>
                        <th class="px-4 py-3 font-medium text-center">Reservas</th>
                        <th class="px-4 py-3 font-medium text-center">Conductor</th>
                        <th class="px-4 py-3 font-medium text-center">Admin</th>
                        <th class="px-4 py-3 font-medium text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <tr v-for="u in users.data" :key="u.id" class="hover:bg-slate-50">
                        <td class="px-4 py-3 text-slate-400">{{ u.id }}</td>
                        <td class="px-4 py-3 font-medium text-slate-800">{{ u.name }}</td>
                        <td class="px-4 py-3 text-slate-600">{{ u.email }}</td>
                        <td class="px-4 py-3 text-center text-slate-600">{{ u.trips_count }}</td>
                        <td class="px-4 py-3 text-center text-slate-600">{{ u.bookings_count }}</td>
                        <td class="px-4 py-3 text-center">
                            <button
                                @click="toggleVerified(u)"
                                :class="[
                                    'px-2 py-1 rounded-full text-xs font-medium',
                                    u.is_verified_driver ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-500',
                                ]"
                            >
                                {{ u.is_verified_driver ? 'Sí' : 'No' }}
                            </button>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <button
                                @click="toggleAdmin(u)"
                                :class="[
                                    'px-2 py-1 rounded-full text-xs font-medium',
                                    u.is_admin ? 'bg-indigo-100 text-indigo-700' : 'bg-slate-100 text-slate-500',
                                ]"
                            >
                                {{ u.is_admin ? 'Admin' : '—' }}
                            </button>
                        </td>
                        <td class="px-4 py-3 text-right">
                            <button
                                @click="destroy(u)"
                                class="text-xs text-red-600 hover:text-red-800 font-medium"
                            >
                                Eliminar
                            </button>
                        </td>
                    </tr>
                    <tr v-if="users.data.length === 0">
                        <td colspan="8" class="px-4 py-8 text-center text-slate-400">
                            No se encontraron usuarios.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <Pagination :links="users.links" />
    </AdminLayout>
</template>
