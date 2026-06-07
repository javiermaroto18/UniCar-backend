<script setup>
import { Head, router } from '@inertiajs/vue3';
import { ref, watch, computed } from 'vue';
import AdminLayout from '../../../Layouts/AdminLayout.vue';
import Pagination from '../../../Components/Pagination.vue';
import ConfirmModal from '../../../Components/ConfirmModal.vue';

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

// --- Modal de confirmación (type-to-confirm) ---
const modal = ref({ show: false, type: null, user: null });

function askAdmin(user) {
    modal.value = { show: true, type: 'admin', user };
}
function askDelete(user) {
    modal.value = { show: true, type: 'delete', user };
}
function closeModal() {
    modal.value = { show: false, type: null, user: null };
}

const modalConfig = computed(() => {
    const u = modal.value.user;
    if (!u) return {};
    if (modal.value.type === 'admin') {
        return u.is_admin
            ? {
                  title: 'Retirar administrador',
                  message: `Vas a retirar los permisos de administrador a ${u.name}. Dejará de tener acceso al panel.`,
                  confirmWord: 'CONFIRMAR',
                  confirmLabel: 'Retirar admin',
                  tone: 'danger',
              }
            : {
                  title: 'Conceder administrador',
                  message: `Vas a dar permisos de administrador a ${u.name}. Podrá acceder a este panel y gestionar toda la plataforma.`,
                  confirmWord: 'CONFIRMAR',
                  confirmLabel: 'Conceder admin',
                  tone: 'primary',
              };
    }
    return {
        title: 'Eliminar usuario',
        message: `Vas a eliminar a ${u.name} (${u.email}) de forma permanente. Esta acción no se puede deshacer.`,
        confirmWord: 'ELIMINAR',
        confirmLabel: 'Eliminar usuario',
        tone: 'danger',
    };
});

function onConfirm() {
    const { type, user } = modal.value;
    if (type === 'admin') {
        router.patch(`/admin/users/${user.id}`, { action: 'toggle_admin' }, { preserveScroll: true, onFinish: closeModal });
    } else if (type === 'delete') {
        router.delete(`/admin/users/${user.id}`, { preserveScroll: true, onFinish: closeModal });
    }
}

// Verificado de conductor: acción de bajo riesgo, instantánea (sin modal)
function toggleVerified(user) {
    router.patch(`/admin/users/${user.id}`, { action: 'toggle_verified' }, { preserveScroll: true });
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
            class="w-full max-w-sm mb-4 rounded-lg bg-unicar-surface border border-unicar-border px-3 py-2 text-sm text-unicar-text placeholder-unicar-dim focus:border-unicar-primary focus:ring-1 focus:ring-unicar-primary outline-none"
        />

        <div class="bg-unicar-surface rounded-xl border border-unicar-border overflow-hidden">
            <table class="w-full text-sm">
                <thead class="bg-white/5 text-unicar-dim text-left text-xs uppercase tracking-wide">
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
                <tbody class="divide-y divide-unicar-border">
                    <tr v-for="u in users.data" :key="u.id" class="hover:bg-white/5 transition">
                        <td class="px-4 py-3 text-unicar-dim">{{ u.id }}</td>
                        <td class="px-4 py-3 font-medium text-unicar-text">{{ u.name }}</td>
                        <td class="px-4 py-3 text-unicar-muted">{{ u.email }}</td>
                        <td class="px-4 py-3 text-center text-unicar-muted">{{ u.trips_count }}</td>
                        <td class="px-4 py-3 text-center text-unicar-muted">{{ u.bookings_count }}</td>

                        <!-- Conductor verificado: toggle instantáneo -->
                        <td class="px-4 py-3">
                            <div class="flex justify-center">
                                <button
                                    type="button"
                                    role="switch"
                                    :aria-checked="u.is_verified_driver"
                                    @click="toggleVerified(u)"
                                    :class="[
                                        'relative inline-flex h-5 w-9 items-center rounded-full transition',
                                        u.is_verified_driver ? 'bg-emerald-500' : 'bg-unicar-border-strong',
                                    ]"
                                >
                                    <span
                                        :class="[
                                            'inline-block h-4 w-4 transform rounded-full bg-white transition',
                                            u.is_verified_driver ? 'translate-x-4' : 'translate-x-0.5',
                                        ]"
                                    />
                                </button>
                            </div>
                        </td>

                        <!-- Admin: toggle con confirmación escrita -->
                        <td class="px-4 py-3">
                            <div class="flex justify-center">
                                <button
                                    type="button"
                                    role="switch"
                                    :aria-checked="u.is_admin"
                                    @click="askAdmin(u)"
                                    :class="[
                                        'relative inline-flex h-5 w-9 items-center rounded-full transition',
                                        u.is_admin ? 'bg-unicar-primary' : 'bg-unicar-border-strong',
                                    ]"
                                >
                                    <span
                                        :class="[
                                            'inline-block h-4 w-4 transform rounded-full bg-white transition',
                                            u.is_admin ? 'translate-x-4' : 'translate-x-0.5',
                                        ]"
                                    />
                                </button>
                            </div>
                        </td>

                        <td class="px-4 py-3 text-right">
                            <button
                                @click="askDelete(u)"
                                class="text-xs text-red-400 hover:text-red-300 font-medium"
                            >
                                Eliminar
                            </button>
                        </td>
                    </tr>
                    <tr v-if="users.data.length === 0">
                        <td colspan="8" class="px-4 py-10 text-center text-unicar-dim">No se encontraron usuarios.</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <Pagination :links="users.links" />

        <ConfirmModal
            :show="modal.show"
            :title="modalConfig.title"
            :message="modalConfig.message"
            :confirm-word="modalConfig.confirmWord"
            :confirm-label="modalConfig.confirmLabel"
            :tone="modalConfig.tone"
            @confirm="onConfirm"
            @cancel="closeModal"
        />
    </AdminLayout>
</template>
