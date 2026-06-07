<script setup>
import { useForm, Head } from '@inertiajs/vue3';

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

    <div class="min-h-screen flex items-center justify-center bg-slate-100 px-4">
        <div class="w-full max-w-md">
            <div class="text-center mb-8">
                <h1 class="text-3xl font-bold text-indigo-600">UniCar</h1>
                <p class="text-slate-500 mt-1">Panel de administración</p>
            </div>

            <form
                @submit.prevent="submit"
                class="bg-white shadow-sm rounded-xl p-8 space-y-5 border border-slate-200"
            >
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">
                        Correo electrónico
                    </label>
                    <input
                        v-model="form.email"
                        type="email"
                        autocomplete="email"
                        autofocus
                        class="w-full rounded-lg border border-slate-300 px-3 py-2 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 outline-none"
                    />
                    <p v-if="form.errors.email" class="text-sm text-red-600 mt-1">
                        {{ form.errors.email }}
                    </p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">
                        Contraseña
                    </label>
                    <input
                        v-model="form.password"
                        type="password"
                        autocomplete="current-password"
                        class="w-full rounded-lg border border-slate-300 px-3 py-2 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 outline-none"
                    />
                </div>

                <label class="flex items-center gap-2 text-sm text-slate-600">
                    <input v-model="form.remember" type="checkbox" class="rounded border-slate-300" />
                    Mantener la sesión iniciada
                </label>

                <button
                    type="submit"
                    :disabled="form.processing"
                    class="w-full bg-indigo-600 hover:bg-indigo-700 disabled:opacity-60 text-white font-medium py-2.5 rounded-lg transition"
                >
                    {{ form.processing ? 'Accediendo…' : 'Acceder' }}
                </button>
            </form>
        </div>
    </div>
</template>
