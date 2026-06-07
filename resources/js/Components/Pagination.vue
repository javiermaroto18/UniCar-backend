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
                class="px-3 py-1.5 text-sm text-unicar-dim rounded-lg"
                v-html="link.label"
            />
            <Link
                v-else
                :href="link.url"
                preserve-scroll
                :class="[
                    'px-3 py-1.5 text-sm rounded-lg border transition',
                    link.active
                        ? 'bg-unicar-primary border-unicar-primary text-white'
                        : 'bg-unicar-surface border-unicar-border text-unicar-muted hover:text-unicar-text hover:border-unicar-border-strong',
                ]"
                v-html="link.label"
            />
        </template>
    </div>
</template>
