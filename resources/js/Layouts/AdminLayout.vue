<script setup>
import { ref, computed } from 'vue';
import { Link, router, usePage } from '@inertiajs/vue3';
import {
    PhSquaresFour,
    PhArticle,
    PhFolders,
    PhImage,
    PhSignOut,
    PhList,
    PhX,
} from '@phosphor-icons/vue';
import FlashMessage from '@/Components/FlashMessage.vue';

const sidebarOpen = ref(false);
const page = usePage();
const currentUrl = computed(() => page.url);

const navLinks = [
    { label: 'Dashboard', href: '/admin', icon: PhSquaresFour },
    { label: 'Issdarat', href: '/admin/issdarat', icon: PhArticle },
    { label: 'Categories', href: '/admin/categories', icon: PhFolders },
    { label: 'Banners', href: '/admin/banners', icon: PhImage },
];

function isActive(href) {
    if (href === '/admin') {
        return currentUrl.value === '/admin' || currentUrl.value === '/admin/';
    }
    return currentUrl.value.startsWith(href);
}

function logout() {
    router.post('/logout');
}
</script>

<template>
    <div dir="ltr" class="min-h-screen flex bg-neutral-100 font-sans">
        <!-- Sidebar Overlay (mobile) -->
        <Transition
            enter-active-class="transition-opacity duration-300"
            enter-from-class="opacity-0"
            enter-to-class="opacity-100"
            leave-active-class="transition-opacity duration-300"
            leave-from-class="opacity-100"
            leave-to-class="opacity-0"
        >
            <div
                v-if="sidebarOpen"
                class="fixed inset-0 z-40 bg-black/40 lg:hidden"
                @click="sidebarOpen = false"
            />
        </Transition>

        <!-- Sidebar -->
        <aside
            :class="[
                'fixed lg:static inset-y-0 left-0 z-50 w-64 bg-white shadow-lg border-r border-neutral-200',
                'transform transition-transform duration-300 lg:transform-none',
                sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0',
            ]"
        >
            <div class="flex flex-col h-full">
                <!-- Sidebar Header -->
                <div class="flex items-center justify-between px-5 py-5 border-b border-neutral-200">
                    <div class="flex items-center gap-3">
                        <img src="/logo.png" alt="IoT KIDS" class="h-10 w-10" />
                        <span class="text-lg font-bold text-primary-700">Dashboard</span>
                    </div>
                    <button
                        class="lg:hidden p-1 rounded-xl text-neutral-400 hover:bg-neutral-100"
                        @click="sidebarOpen = false"
                    >
                        <PhX :size="20" />
                    </button>
                </div>

                <!-- Navigation -->
                <nav class="flex-1 px-3 py-4 space-y-1 overflow-y-auto">
                    <Link
                        v-for="link in navLinks"
                        :key="link.href"
                        :href="link.href"
                        :class="[
                            'flex items-center gap-3 px-4 py-3 rounded-3xl transition-all font-semibold text-lg',
                            isActive(link.href)
                                ? 'bg-primary-100 text-primary-800 shadow-md'
                                : 'text-neutral-500 hover:bg-neutral-100 hover:text-neutral-800',
                        ]"
                        @click="sidebarOpen = false"
                    >
                        <component
                            :is="link.icon"
                            :size="22"
                            :weight="isActive(link.href) ? 'fill' : 'regular'"
                        />
                        <span>{{ link.label }}</span>
                    </Link>
                </nav>

                <!-- Logout -->
                <div class="px-3 py-4 border-t border-neutral-200">
                    <button
                        class="flex items-center gap-3 w-full px-4 py-3 rounded-full text-red-500 hover:bg-red-50 transition-all font-semibold text-lg min-h-[44px]"
                        @click="logout"
                    >
                        <PhSignOut :size="22" />
                        <span>Logout</span>
                    </button>
                </div>
            </div>
        </aside>

        <!-- Main Content Area -->
        <div class="flex-1 flex flex-col min-h-screen">
            <!-- Top Bar -->
            <header class="sticky top-0 z-30 bg-white/80 backdrop-blur-sm border-b-2 border-neutral-200">
                <div class="flex items-center justify-between px-4 sm:px-6 py-3">
                    <div class="flex items-center gap-3">
                        <button
                            class="lg:hidden p-2 rounded-xl text-neutral-500 hover:bg-neutral-100 transition-colors"
                            @click="sidebarOpen = true"
                        >
                            <PhList :size="22" />
                        </button>
                        <h1 class="text-lg font-bold text-neutral-800">
                            IoT KIDS - Dashboard
                        </h1>
                    </div>
                </div>
            </header>

            <FlashMessage />

            <!-- Page Content -->
            <main class="flex-1 p-4 sm:p-6 lg:p-8">
                <slot />
            </main>
        </div>
    </div>
</template>