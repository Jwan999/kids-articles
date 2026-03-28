<script setup>
import { Head, useForm } from '@inertiajs/vue3'

const form = useForm({
    email: '',
    password: '',
})

function submit() {
    form.post('/login', {
        onFinish: () => form.reset('password'),
    })
}
</script>

<template>
    <Head title="Login" />

    <div class="flex min-h-screen items-center justify-center bg-neutral-100 px-6" dir="ltr">
        <div class="w-full max-w-md">
            <div class="bg-white rounded-3xl border-2 border-primary-200 shadow-lg p-8">
                <!-- Logo -->
                <div class="mb-8 text-center">
                    <img src="/logo.png?v=2" alt="IoT KIDS" class="mx-auto mb-3 h-16 w-16 object-contain" />
                    <h1 class="text-2xl font-bold text-primary-700">
                        IoT KIDS
                    </h1>
                </div>

                <!-- Heading -->
                <h2 class="mb-6 text-center text-2xl font-bold text-neutral-800">
                    Login
                </h2>

                <form @submit.prevent="submit" class="space-y-5">
                    <!-- Email -->
                    <div>
                        <label
                            for="email"
                            class="mb-1.5 block text-lg font-semibold text-neutral-800"
                        >
                            Email
                        </label>
                        <input
                            id="email"
                            v-model="form.email"
                            type="email"
                            autocomplete="email"
                            required
                            class="w-full rounded-xl border-2 border-[#F0F0F0] px-5 py-3 text-lg min-h-[52px] text-neutral-800 transition-all focus:outline-none focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20"
                            :class="{ 'border-red-500': form.errors.email }"
                            placeholder="example@email.com"
                            dir="ltr"
                        />
                        <p v-if="form.errors.email" class="mt-1.5 text-base text-red-500">
                            {{ form.errors.email }}
                        </p>
                    </div>

                    <!-- Password -->
                    <div>
                        <label
                            for="password"
                            class="mb-1.5 block text-lg font-semibold text-neutral-800"
                        >
                            Password
                        </label>
                        <input
                            id="password"
                            v-model="form.password"
                            type="password"
                            autocomplete="current-password"
                            required
                            class="w-full rounded-xl border-2 border-[#F0F0F0] px-5 py-3 text-lg min-h-[52px] text-neutral-800 transition-all focus:outline-none focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20"
                            :class="{ 'border-red-500': form.errors.password }"
                            placeholder="••••••••"
                            dir="ltr"
                        />
                        <p v-if="form.errors.password" class="mt-1.5 text-base text-red-500">
                            {{ form.errors.password }}
                        </p>
                    </div>

                    <!-- Submit -->
                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="w-full bg-primary-500 text-neutral-800 rounded-full py-3 text-xl font-bold min-h-[52px] transition-all hover:shadow-lg disabled:cursor-not-allowed disabled:opacity-60"
                    >
                        <span v-if="form.processing" class="flex items-center justify-center gap-2">
                            <svg class="h-5 w-5 animate-spin" viewBox="0 0 24 24" fill="none">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z" />
                            </svg>
                            Logging in...
                        </span>
                        <span v-else>Login</span>
                    </button>
                </form>
            </div>
        </div>
    </div>
</template>