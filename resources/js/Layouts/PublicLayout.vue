<script setup>
import Navbar from '@/Components/Navbar.vue';
import Footer from '@/Components/Footer.vue';
import FlashMessage from '@/Components/FlashMessage.vue';
</script>

<template>
    <div dir="ltr" class="min-h-screen flex flex-col bg-neutral-100 font-sans">
        <Navbar />
        <FlashMessage />

        <main class="flex-1">
            <slot />
        </main>

        <Footer />
    </div>
</template>