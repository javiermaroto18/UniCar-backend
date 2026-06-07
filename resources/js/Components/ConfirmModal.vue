<script setup>
import { ref, watch, computed, nextTick } from 'vue';

const props = defineProps({
    show: { type: Boolean, default: false },
    title: { type: String, default: 'Confirmar acción' },
    message: { type: String, default: '' },
    confirmWord: { type: String, default: 'CONFIRMAR' },
    confirmLabel: { type: String, default: 'Confirmar' },
    tone: { type: String, default: 'primary' }, // 'primary' | 'danger'
});

const emit = defineEmits(['confirm', 'cancel']);

const typed = ref('');
const input = ref(null);

const matches = computed(
    () => typed.value.trim().toUpperCase() === props.confirmWord.toUpperCase()
);

// Al abrir el modal, limpiamos el campo y enfocamos
watch(
    () => props.show,
    (open) => {
        if (open) {
            typed.value = '';
            nextTick(() => input.value?.focus());
        }
    }
);

function confirm() {
    if (matches.value) emit('confirm');
}
</script>

<template>
    <Transition
        enter-active-class="transition duration-150 ease-out"
        enter-from-class="opacity-0"
        leave-active-class="transition duration-100 ease-in"
        leave-to-class="opacity-0"
    >
        <div
            v-if="show"
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm px-4"
            @click.self="emit('cancel')"
            @keydown.esc="emit('cancel')"
        >
            <div class="w-full max-w-md rounded-2xl bg-unicar-surface border border-unicar-border-strong shadow-2xl p-6">
                <h3 class="text-lg font-semibold text-unicar-text">{{ title }}</h3>
                <p class="mt-2 text-sm text-unicar-muted leading-relaxed">{{ message }}</p>

                <label class="mt-5 block text-xs font-medium text-unicar-dim uppercase tracking-wide">
                    Escribe <span class="text-unicar-text font-bold">{{ confirmWord }}</span> para continuar
                </label>
                <input
                    ref="input"
                    v-model="typed"
                    type="text"
                    autocomplete="off"
                    spellcheck="false"
                    class="mt-2 w-full rounded-lg bg-unicar-bg border border-unicar-border px-3 py-2 text-sm text-unicar-text placeholder-unicar-dim focus:border-unicar-primary focus:ring-1 focus:ring-unicar-primary outline-none"
                    :placeholder="confirmWord"
                    @keydown.enter="confirm"
                />

                <div class="mt-6 flex justify-end gap-3">
                    <button
                        type="button"
                        class="px-4 py-2 rounded-lg text-sm text-unicar-muted hover:text-unicar-text hover:bg-white/5 transition"
                        @click="emit('cancel')"
                    >
                        Cancelar
                    </button>
                    <button
                        type="button"
                        :disabled="!matches"
                        :class="[
                            'px-4 py-2 rounded-lg text-sm font-medium text-white transition disabled:opacity-40 disabled:cursor-not-allowed',
                            tone === 'danger'
                                ? 'bg-red-600 hover:bg-red-500'
                                : 'bg-unicar-primary hover:bg-unicar-primary-hover',
                        ]"
                        @click="confirm"
                    >
                        {{ confirmLabel }}
                    </button>
                </div>
            </div>
        </div>
    </Transition>
</template>
