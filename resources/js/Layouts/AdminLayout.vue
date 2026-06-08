<script setup>
import { Link, usePage, router } from '@inertiajs/vue3';
import { computed } from 'vue';
import UnicarLogo from '../Components/UnicarLogo.vue';

const page = usePage();
const user = computed(() => page.props.auth.user);
const flash = computed(() => page.props.flash || {});

const nav = [
    { label: 'Dashboard', href: '/admin', icon: '◧' },
    { label: 'Usuarios', href: '/admin/users', icon: '○' },
    { label: 'Viajes', href: '/admin/trips', icon: '▸' },
    { label: 'Reservas', href: '/admin/bookings', icon: '▤' },
    { label: 'Vehículos', href: '/admin/vehicles', icon: '◈' },
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

const initials = computed(() => {
    const n = user.value?.name || '?';
    return n.split(' ').map((w) => w[0]).slice(0, 2).join('').toUpperCase();
});
</script>

<template>
    <div class="min-h-screen flex bg-unicar-bg text-unicar-text">
        <!-- Barra lateral -->
        <aside class="w-64 bg-unicar-surface border-r border-unicar-border flex flex-col">
            <div class="px-6 py-5 border-b border-unicar-border">
                <UnicarLogo size="md" />
                <p class="text-[11px] text-unicar-dim mt-1 ml-[2.9rem] -mt-0.5">Administración</p>
            </div>

            <nav class="flex-1 px-3 py-4 space-y-1">
                <Link
                    v-for="item in nav"
                    :key="item.href"
                    :href="item.href"
                    :class="[
                        'flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm transition',
                        isActive(item.href)
                            ? 'bg-unicar-primary/15 text-unicar-primary font-medium ring-1 ring-unicar-primary/30'
                            : 'text-unicar-muted hover:bg-white/5 hover:text-unicar-text',
                    ]"
                >
                    <span class="text-base leading-none">{{ item.icon }}</span>
                    {{ item.label }}
                </Link>
            </nav>

            <div class="px-4 py-4 border-t border-unicar-border">
                <div class="flex items-center gap-3 mb-3">
                    <span class="flex h-9 w-9 items-center justify-center rounded-full bg-unicar-primary/20 text-unicar-primary text-xs font-bold">
                        {{ initials }}
                    </span>
                    <div class="min-w-0">
                        <p class="text-sm truncate">{{ user?.name }}</p>
                        <p class="text-[11px] text-unicar-dim truncate">{{ user?.email }}</p>
                    </div>
                </div>
                <button
                    @click="logout"
                    class="w-full text-sm bg-white/5 hover:bg-white/10 text-unicar-muted hover:text-unicar-text px-3 py-2 rounded-lg transition"
                >
                    Cerrar sesión
                </button>
            </div>
        </aside>

        <!-- Contenido -->
        <div class="flex-1 flex flex-col min-w-0">
            <header class="px-8 py-5 border-b border-unicar-border">
                <h2 class="text-xl font-semibold">
                    <slot name="title">Panel</slot>
                </h2>
            </header>

            <div v-if="flash.success" class="mx-8 mt-4 rounded-lg bg-emerald-500/10 border border-emerald-500/30 text-emerald-300 px-4 py-3 text-sm">
                {{ flash.success }}
            </div>
            <div v-if="flash.error" class="mx-8 mt-4 rounded-lg bg-red-500/10 border border-red-500/30 text-red-300 px-4 py-3 text-sm">
                {{ flash.error }}
            </div>

            <main class="flex-1 p-8">
                <slot />
            </main>
        </div>
    </div>
</template>
