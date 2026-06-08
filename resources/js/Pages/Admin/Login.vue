<script setup>
import { useForm, Head } from '@inertiajs/vue3';
import UnicarLogo from '../../Components/UnicarLogo.vue';

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

function submit() {
    form.post('/admin/login', {
        onFinish: () => form.reset('password'),
    });
}
</script>

<template>
    <Head title="Acceso" />

    <div class="min-h-screen flex items-center justify-center bg-unicar-bg text-unicar-text px-4">
        <div class="w-full max-w-md">
            <div class="flex flex-col items-center mb-8">
                <UnicarLogo size="lg" :show-text="false" class="mb-3" />
                <h1 class="text-2xl font-bold text-unicar-primary">UniCar</h1>
                <p class="text-unicar-dim text-sm mt-1">Panel de administración</p>
            </div>

            <form
                @submit.prevent="submit"
                class="bg-unicar-surface border border-unicar-border rounded-2xl p-8 space-y-5 shadow-xl"
            >
                <div>
                    <label class="block text-sm font-medium text-unicar-muted mb-1.5">Correo electrónico</label>
                    <input
                        v-model="form.email"
                        type="email"
                        autocomplete="email"
                        autofocus
                        class="w-full rounded-lg bg-unicar-bg border border-unicar-border px-3 py-2.5 text-sm placeholder-unicar-dim focus:border-unicar-primary focus:ring-1 focus:ring-unicar-primary outline-none"
                    />
                    <p v-if="form.errors.email" class="text-sm text-red-400 mt-1.5">{{ form.errors.email }}</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-unicar-muted mb-1.5">Contraseña</label>
                    <input
                        v-model="form.password"
                        type="password"
                        autocomplete="current-password"
                        class="w-full rounded-lg bg-unicar-bg border border-unicar-border px-3 py-2.5 text-sm placeholder-unicar-dim focus:border-unicar-primary focus:ring-1 focus:ring-unicar-primary outline-none"
                    />
                </div>

                <label class="flex items-center gap-2 text-sm text-unicar-muted">
                    <input v-model="form.remember" type="checkbox" class="rounded border-unicar-border bg-unicar-bg text-unicar-primary focus:ring-unicar-primary" />
                    Mantener la sesión iniciada
                </label>

                <button
                    type="submit"
                    :disabled="form.processing"
                    class="w-full bg-unicar-primary hover:bg-unicar-primary-hover disabled:opacity-60 text-white font-medium py-2.5 rounded-lg transition"
                >
                    {{ form.processing ? 'Accediendo…' : 'Acceder' }}
                </button>
            </form>
        </div>
    </div>
</template>
