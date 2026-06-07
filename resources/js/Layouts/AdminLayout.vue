<script setup>
import { Link, usePage, router } from '@inertiajs/vue3';
import { computed } from 'vue';

const page = usePage();
const user = computed(() => page.props.auth.user);
const flash = computed(() => page.props.flash || {});

const nav = [
    { label: 'Dashboard', href: '/admin' },
    { label: 'Usuarios', href: '/admin/users' },
    { label: 'Viajes', href: '/admin/trips' },
    { label: 'Reservas', href: '/admin/bookings' },
    { label: 'Vehículos', href: '/admin/vehicles' },
];

const currentPath = computed(() => page.url.split('?')[0]);

function isActive(href) {
    return href === '/admin'
        ? currentPath.value === '/admin'
        : currentPath.value.startsWith(href);
}

function logout() {
    router.post('/admin/logout');
}
</script>

<template>
    <div class="min-h-screen flex bg-slate-100">
        <!-- Barra lateral -->
        <aside class="w-60 bg-slate-900 text-slate-300 flex flex-col">
            <div class="px-6 py-5 border-b border-slate-800">
                <h1 class="text-lg font-bold text-white">UniCar</h1>
                <p class="text-xs text-slate-400">Panel de administración</p>
            </div>
            <nav class="flex-1 px-3 py-4 space-y-1">
                <Link
                    v-for="item in nav"
                    :key="item.href"
                    :href="item.href"
                    :class="[
                        'block px-3 py-2 rounded-lg text-sm transition',
                        isActive(item.href)
                            ? 'bg-indigo-600 text-white'
                            : 'hover:bg-slate-800 hover:text-white',
                    ]"
                >
                    {{ item.label }}
                </Link>
            </nav>
            <div class="px-4 py-4 border-t border-slate-800">
                <p class="text-sm text-white truncate">{{ user?.name }}</p>
                <p class="text-xs text-slate-400 truncate mb-3">{{ user?.email }}</p>
                <button
                    @click="logout"
                    class="w-full text-sm bg-slate-800 hover:bg-slate-700 text-slate-200 px-3 py-2 rounded-lg transition"
                >
                    Cerrar sesión
                </button>
            </div>
        </aside>

        <!-- Contenido -->
        <div class="flex-1 flex flex-col">
            <header class="bg-white border-b border-slate-200 px-8 py-4">
                <h2 class="text-lg font-semibold text-slate-800">
                    <slot name="title">Panel</slot>
                </h2>
            </header>

            <!-- Mensajes flash -->
            <div v-if="flash.success" class="mx-8 mt-4 rounded-lg bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 text-sm">
                {{ flash.success }}
            </div>
            <div v-if="flash.error" class="mx-8 mt-4 rounded-lg bg-red-50 border border-red-200 text-red-700 px-4 py-3 text-sm">
                {{ flash.error }}
            </div>

            <main class="flex-1 p-8">
                <slot />
            </main>
        </div>
    </div>
</template>
