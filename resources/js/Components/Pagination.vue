<script setup>
import { Link } from '@inertiajs/vue3';

defineProps({
    links: { type: Array, required: true },
});
</script>

<template>
    <div v-if="links.length > 3" class="flex flex-wrap gap-1 mt-4">
        <template v-for="(link, i) in links" :key="i">
            <span
                v-if="!link.url"
                class="px-3 py-1.5 text-sm text-slate-400 rounded-lg"
                v-html="link.label"
            />
            <Link
                v-else
                :href="link.url"
                preserve-scroll
                :class="[
                    'px-3 py-1.5 text-sm rounded-lg border transition',
                    link.active
                        ? 'bg-indigo-600 border-indigo-600 text-white'
                        : 'bg-white border-slate-200 text-slate-600 hover:bg-slate-50',
                ]"
                v-html="link.label"
            />
        </template>
    </div>
</template>
