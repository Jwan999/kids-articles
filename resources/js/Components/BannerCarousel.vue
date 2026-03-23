<script setup>
import { ref, onMounted, onUnmounted, computed } from 'vue';
import { PhCaretLeft, PhCaretRight } from '@phosphor-icons/vue';

const props = defineProps({
    banners: {
        type: Array,
        default: () => [],
    },
});

const currentIndex = ref(0);
const isPaused = ref(false);
let intervalId = null;

const totalSlides = computed(() => props.banners.length);

function goTo(index) {
    currentIndex.value = index;
}

function next() {
    if (totalSlides.value === 0) return;
    currentIndex.value = (currentIndex.value + 1) % totalSlides.value;
}

function prev() {
    if (totalSlides.value === 0) return;
    currentIndex.value = (currentIndex.value - 1 + totalSlides.value) % totalSlides.value;
}

function imageUrl(banner) {
    return banner.image_url || null;
}

function startAutoplay() {
    stopAutoplay();
    intervalId = setInterval(() => {
        if (!isPaused.value) {
            next();
        }
    }, 4000);
}

function stopAutoplay() {
    if (intervalId) {
        clearInterval(intervalId);
        intervalId = null;
    }
}

onMounted(() => {
    if (totalSlides.value > 1) {
        startAutoplay();
    }
});

onUnmounted(() => {
    stopAutoplay();
});
</script>

<template>
    <div
        v-if="banners.length"
        class="carousel-wrapper relative overflow-hidden"
        @mouseenter="isPaused = true"
        @mouseleave="isPaused = false"
    >
        <!-- Slides -->
        <div class="relative w-full h-full">
            <Transition
                mode="out-in"
                enter-active-class="transition-opacity duration-700 ease-in-out"
                enter-from-class="opacity-0"
                enter-to-class="opacity-100"
                leave-active-class="transition-opacity duration-700 ease-in-out"
                leave-from-class="opacity-100"
                leave-to-class="opacity-0"
            >
                <div
                    :key="currentIndex"
                    class="w-full h-full"
                >
                    <a
                        :href="banners[currentIndex].link ?? '#'"
                        class="block w-full h-full relative"
                        :target="banners[currentIndex].link ? '_blank' : '_self'"
                    >
                        <!-- Image or solid placeholder -->
                        <img
                            v-if="imageUrl(banners[currentIndex])"
                            :src="imageUrl(banners[currentIndex])"
                            :alt="banners[currentIndex].title"
                            class="w-full h-full object-cover"
                        />
                        <div
                            v-else
                            class="w-full h-full bg-accent-100 flex items-center justify-center"
                        >
                            <span
                                v-if="banners[currentIndex].title"
                                class="text-neutral-800 font-bold text-2xl text-center px-8"
                            >
                                {{ banners[currentIndex].title }}
                            </span>
                        </div>

                        <!-- Dark overlay (solid semi-transparent) -->
                        <div class="absolute inset-0 bg-neutral-800/50" />

                        <!-- Text overlay -->
                        <div class="absolute bottom-0 inset-x-0 p-6 sm:p-8">
                            <h2
                                v-if="banners[currentIndex].title"
                                class="text-2xl sm:text-3xl font-bold text-white mb-1"
                            >
                                {{ banners[currentIndex].title }}
                            </h2>
                            <p
                                v-if="banners[currentIndex].subtitle"
                                class="text-lg text-white/90"
                            >
                                {{ banners[currentIndex].subtitle }}
                            </p>
                        </div>
                    </a>
                </div>
            </Transition>
        </div>

        <!-- Prev / Next arrows -->
        <template v-if="totalSlides > 1">
            <button
                class="absolute start-3 top-1/2 -translate-y-1/2 bg-white hover:bg-primary-500 hover:text-white rounded-full p-3 shadow-lg transition-all z-10"
                @click="next"
                aria-label="Next"
            >
                <PhCaretRight :size="20" class="text-neutral-800" />
            </button>
            <button
                class="absolute end-3 top-1/2 -translate-y-1/2 bg-white hover:bg-primary-500 hover:text-white rounded-full p-3 shadow-lg transition-all z-10"
                @click="prev"
                aria-label="Previous"
            >
                <PhCaretLeft :size="20" class="text-neutral-800" />
            </button>
        </template>

        <!-- Dot indicators -->
        <div
            v-if="totalSlides > 1"
            class="absolute bottom-3 left-1/2 -translate-x-1/2 flex items-center gap-2 z-10"
        >
            <button
                v-for="(_, index) in banners"
                :key="index"
                class="dot rounded-full transition-all duration-300 focus:outline-none"
                :class="index === currentIndex ? 'bg-white active-dot' : 'bg-white/50 hover:bg-white/80 inactive-dot'"
                @click="goTo(index)"
            />
        </div>
    </div>
</template>

<style scoped>
.carousel-wrapper {
    aspect-ratio: 21 / 9;
}
@media (min-width: 768px) {
    .carousel-wrapper {
        aspect-ratio: 4 / 1;
    }
}
.dot {
    min-height: auto !important;
    padding: 0 !important;
    border-radius: 9999px !important;
}
.active-dot {
    width: 20px;
    height: 6px;
}
.inactive-dot {
    width: 6px;
    height: 6px;
}
</style>