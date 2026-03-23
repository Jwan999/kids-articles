<script setup>
import { ref, computed, onMounted, onUnmounted, watch } from 'vue';
import { Link, usePage, router } from '@inertiajs/vue3';
import { PhHouse, PhSignIn, PhSignOut, PhUser, PhGearSix, PhList, PhX } from '@phosphor-icons/vue';

const page = usePage();
const auth = computed(() => page.props.auth);
const isAuthenticated = computed(() => !!auth.value?.user);
const user = computed(() => auth.value?.user);
const currentPath = computed(() => page.url);

const isScrolled = ref(false);
const mobileMenuOpen = ref(false);

function handleScroll() {
    isScrolled.value = window.scrollY > 20;
}

onMounted(() => {
    window.addEventListener('scroll', handleScroll);
    handleScroll();
});

onUnmounted(() => {
    window.removeEventListener('scroll', handleScroll);
});

watch(mobileMenuOpen, (open) => {
    document.body.style.overflow = open ? 'hidden' : '';
});

function handleLogout() {
    mobileMenuOpen.value = false;
    router.post('/logout');
}

function isActive(path) {
    if (path === '/') return currentPath.value === '/';
    return currentPath.value.startsWith(path);
}

const navLinks = [
    { label: 'Home', to: '/', icon: PhHouse },
];
</script>

<template>
    <div>
        <!-- Navbar -->
        <header
            :class="[
                'fixed left-0 right-0 top-0 z-[100] transition-all duration-300 ease-out',
                isScrolled
                    ? 'my-3 mx-4 md:mx-12 rounded-full bg-white shadow-md px-2'
                    : 'my-0 rounded-none bg-white px-0',
            ]"
        >
            <div class="container mx-auto px-6 md:px-12 relative z-10">
                <div class="flex items-center justify-between h-20">
                    <!-- Logo + Brand -->
                    <Link href="/" class="flex items-center gap-2">
                        <img src="/logo.png" alt="IoT KIDS" class="h-10 w-auto" />
                        <span class="text-xl font-bold text-neutral-800">IoT KIDS</span>
                    </Link>

                    <!-- Desktop nav links (centered) -->
                    <nav class="hidden md:flex items-center gap-1 absolute left-1/2 -translate-x-1/2">
                        <Link
                            v-for="link in navLinks"
                            :key="link.to"
                            :href="link.to"
                            :class="[
                                'px-5 py-2.5 rounded-full font-semibold text-lg transition-all duration-200',
                                isActive(link.to)
                                    ? 'bg-primary-100 text-neutral-800'
                                    : 'text-neutral-500 hover:text-neutral-800 hover:bg-neutral-100',
                            ]"
                        >
                            {{ link.label }}
                        </Link>
                    </nav>

                    <!-- Actions -->
                    <div class="flex items-center gap-3">
                        <!-- Authenticated: admin + logout -->
                        <template v-if="isAuthenticated">
                            <Link
                                href="/admin"
                                class="hidden md:flex items-center gap-2 px-4 py-2.5 rounded-full hover:bg-accent-50 transition-all duration-200 text-neutral-800"
                            >
                                <PhGearSix :size="18" weight="bold" />
                                <span class="text-lg font-semibold">Dashboard</span>
                            </Link>
                            <button
                                class="hidden md:flex items-center gap-2 px-4 py-2.5 rounded-full text-lg font-semibold text-red-500 hover:bg-red-50 transition-all duration-200"
                                @click="handleLogout"
                            >
                                <PhSignOut :size="18" weight="bold" />
                                <span>Logout</span>
                            </button>
                        </template>

                        <!-- Unauthenticated: login button -->
                        <Link
                            v-if="!isAuthenticated"
                            href="/login"
                            class="hidden md:inline-flex items-center gap-2 px-6 py-2.5 rounded-full bg-secondary-500 text-white font-bold text-lg hover:-translate-y-0.5 hover:shadow-lg hover:shadow-secondary-300/40 active:translate-y-0 active:scale-[0.98] transition-all duration-200"
                        >
                            <PhSignIn :size="18" weight="bold" />
                            Login
                        </Link>

                        <!-- Mobile hamburger -->
                        <button
                            class="md:hidden flex items-center justify-center w-11 h-11 rounded-full hover:bg-accent-50 transition-all duration-200"
                            :aria-expanded="mobileMenuOpen"
                            aria-label="Menu"
                            @click="mobileMenuOpen = !mobileMenuOpen"
                        >
                            <PhX v-if="mobileMenuOpen" :size="24" class="text-neutral-800" />
                            <PhList v-else :size="24" class="text-neutral-800" />
                        </button>
                    </div>
                </div>
            </div>
        </header>

        <!-- Spacer -->
        <div class="h-20" />

        <!-- Mobile Menu - Backdrop -->
        <Teleport to="body">
            <Transition
                enter-active-class="transition duration-300 ease-out"
                enter-from-class="opacity-0"
                enter-to-class="opacity-100"
                leave-active-class="transition duration-200 ease-in"
                leave-from-class="opacity-100"
                leave-to-class="opacity-0"
            >
                <div
                    v-if="mobileMenuOpen"
                    class="fixed inset-0 bg-neutral-800/50 z-[200]"
                    @click="mobileMenuOpen = false"
                />
            </Transition>

            <!-- Mobile Menu - Slide-over panel -->
            <Transition
                enter-active-class="transition duration-300 ease-out"
                enter-from-class="-translate-x-full"
                enter-to-class="translate-x-0"
                leave-active-class="transition duration-200 ease-in"
                leave-from-class="translate-x-0"
                leave-to-class="-translate-x-full"
            >
                <div
                    v-if="mobileMenuOpen"
                    dir="ltr"
                    class="fixed top-0 bottom-0 left-0 w-full max-w-sm bg-white shadow-xl z-[201] flex flex-col rounded-r-3xl"
                >
                    <!-- Header -->
                    <div class="flex items-center justify-between px-6 py-5 border-b-2 border-neutral-200">
                        <Link href="/" class="flex items-center gap-2" @click="mobileMenuOpen = false">
                            <img src="/logo.png" alt="IoT KIDS" class="h-9 w-auto" />
                            <span class="text-lg font-bold text-neutral-800">IoT KIDS</span>
                        </Link>
                        <button
                            class="flex items-center justify-center w-10 h-10 rounded-full hover:bg-accent-50 transition-colors"
                            @click="mobileMenuOpen = false"
                        >
                            <PhX :size="22" class="text-neutral-800" />
                        </button>
                    </div>

                    <!-- Nav links -->
                    <nav class="flex-1 overflow-y-auto px-4 py-4 space-y-1">
                        <Link
                            v-for="link in navLinks"
                            :key="link.to"
                            :href="link.to"
                            :class="[
                                'flex items-center gap-3 px-5 py-3.5 rounded-3xl font-semibold text-lg transition-all duration-200',
                                isActive(link.to)
                                    ? 'bg-primary-100 text-neutral-800'
                                    : 'text-neutral-500 hover:text-neutral-800 hover:bg-neutral-50',
                            ]"
                            @click="mobileMenuOpen = false"
                        >
                            <component :is="link.icon" :size="20" :weight="isActive(link.to) ? 'fill' : 'regular'" />
                            {{ link.label }}
                        </Link>

                        <!-- Admin link (authenticated) -->
                        <Link
                            v-if="isAuthenticated"
                            href="/admin"
                            :class="[
                                'flex items-center gap-3 px-5 py-3.5 rounded-3xl font-semibold text-lg transition-all duration-200',
                                isActive('/admin')
                                    ? 'bg-primary-100 text-neutral-800'
                                    : 'text-neutral-500 hover:text-neutral-800 hover:bg-neutral-50',
                            ]"
                            @click="mobileMenuOpen = false"
                        >
                            <PhGearSix :size="20" :weight="isActive('/admin') ? 'fill' : 'regular'" />
                            Dashboard
                        </Link>
                    </nav>

                    <!-- Auth section -->
                    <div class="border-t-2 border-neutral-200 px-4 py-4 space-y-2">
                        <template v-if="isAuthenticated">
                            <div class="px-5 py-2 text-lg text-neutral-500">
                                Welcome, {{ user?.name || 'User' }}
                            </div>
                            <button
                                class="w-full flex items-center gap-3 px-5 py-3.5 rounded-3xl text-lg text-red-500 hover:bg-red-50 transition-colors font-semibold"
                                @click="handleLogout"
                            >
                                <PhSignOut :size="20" weight="bold" />
                                Logout
                            </button>
                        </template>

                        <template v-else>
                            <Link
                                href="/login"
                                class="w-full flex items-center justify-center gap-2 px-6 py-3.5 rounded-full bg-secondary-500 text-white font-bold text-lg transition-all duration-200 hover:bg-secondary-600"
                                @click="mobileMenuOpen = false"
                            >
                                <PhSignIn :size="18" weight="bold" />
                                Login
                            </Link>
                        </template>
                    </div>
                </div>
            </Transition>
        </Teleport>
    </div>
</template>