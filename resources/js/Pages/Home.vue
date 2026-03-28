<script setup>
import { Head, router } from '@inertiajs/vue3'
import PublicLayout from '@/Layouts/PublicLayout.vue'
import BannerCarousel from '@/Components/BannerCarousel.vue'
import IssdarCard from '@/Components/IssdarCard.vue'
import {
    PhSparkle,
    PhArticle,
    PhFolders,
    PhEye,
    PhStar,
    PhQuotes,
} from '@phosphor-icons/vue'
import { computed, ref, onMounted, onUnmounted } from 'vue'

defineOptions({ layout: PublicLayout })

const props = defineProps({
    banners: { type: Array, default: () => [] },
    latestIssdarat: { type: Array, default: () => [] },
    popularIssdarat: { type: Array, default: () => [] },
    categories: { type: Array, default: () => [] },
    stats: { type: Object, default: () => ({}) },
    topReviews: { type: Array, default: () => [] },
})

const selectedCategory = ref(Number(new URLSearchParams(window.location.search).get('category')) || null)

function filterByCategory(categoryId) {
    selectedCategory.value = categoryId
    router.get('/', categoryId ? { category: categoryId } : {}, {
        preserveState: true,
        preserveScroll: true,
    })
}

const allIssdarat = computed(() => {
    const seen = new Set()
    const combined = []
    for (const a of [...props.latestIssdarat, ...props.popularIssdarat]) {
        if (!seen.has(a.id)) {
            seen.add(a.id)
            combined.push(a)
        }
    }
    return combined
})

const currentReview = ref(0)
let reviewInterval = null

function startReviewCarousel() {
    reviewInterval = setInterval(() => {
        if (props.topReviews.length) {
            currentReview.value = (currentReview.value + 1) % props.topReviews.length
        }
    }, 5000)
}

onMounted(() => {
    startReviewCarousel()
})

onUnmounted(() => {
    clearInterval(reviewInterval)
})
</script>

<template>
    <Head title="Home" />

    <div class="min-h-screen bg-neutral-100">
        <!-- Banner Section (full width, no padding) -->
        <section v-if="banners.length">
            <BannerCarousel :banners="banners" />
        </section>

        <!-- Hero Section -->
        <section class="pt-16 pb-4 px-6 text-center">
            <div class="max-w-7xl mx-auto">
                <h1 class="text-4xl sm:text-5xl font-bold text-neutral-800 mb-4">
                    Discover Our Latest Issdarat
                </h1>
            </div>
        </section>

        <!-- Quick Stats Section -->
        <section class="pt-4 pb-16 px-6">
            <div class="max-w-3xl mx-auto">
                <div class="grid grid-cols-2 md:grid-cols-4 gap-0 text-center">
                    <div>
                        <div class="text-3xl md:text-4xl font-bold text-neutral-800">
                            {{ stats.totalIssdarat ?? 0 }}
                        </div>
                        <div class="text-lg text-neutral-500">Issdarat</div>
                    </div>
                    <div>
                        <div class="text-3xl md:text-4xl font-bold text-neutral-800">
                            {{ stats.totalCategories ?? 0 }}
                        </div>
                        <div class="text-lg text-neutral-500">Categories</div>
                    </div>
                    <div>
                        <div class="text-3xl md:text-4xl font-bold text-neutral-800">
                            {{ stats.totalViews?.toLocaleString('en-US') ?? 0 }}
                        </div>
                        <div class="text-lg text-neutral-500">Views</div>
                    </div>
                    <div>
                        <div class="text-3xl md:text-4xl font-bold text-neutral-800">
                            {{ stats.totalDownloads?.toLocaleString('en-US') ?? 0 }}
                        </div>
                        <div class="text-lg text-neutral-500">Downloads</div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Browse Section with Filters -->
        <section class="py-12 px-6">
            <div class="max-w-7xl mx-auto">
                <!-- Category filter pills -->
                <div class="flex gap-3 overflow-x-auto pb-4 mb-8 scrollbar-hide" style="-webkit-overflow-scrolling: touch">
                    <button
                        @click="filterByCategory(null)"
                        class="shrink-0 rounded-full px-6 py-2.5 text-lg font-semibold border-2 transition-all"
                        :class="!selectedCategory
                            ? 'bg-primary-500 text-neutral-800 border-primary-500'
                            : 'bg-white text-neutral-600 border-neutral-200 hover:border-primary-400'"
                    >
                        All
                    </button>
                    <button
                        v-for="category in categories"
                        :key="category.id"
                        @click="filterByCategory(category.id)"
                        class="shrink-0 rounded-full px-6 py-2.5 text-lg font-semibold border-2 transition-all"
                        :class="selectedCategory === category.id
                            ? 'bg-primary-500 text-neutral-800 border-primary-500'
                            : 'bg-white text-neutral-600 border-neutral-200 hover:border-primary-400'"
                    >
                        {{ category.name }}
                        <span
                            v-if="category.issdarat_count != null"
                            class="ml-1 text-base opacity-70"
                        >
                            ({{ category.issdarat_count }})
                        </span>
                    </button>
                </div>

                <!-- Issdarat Grid -->
                <div
                    v-if="allIssdarat.length"
                    class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8"
                >
                    <IssdarCard
                        v-for="issdar in allIssdarat"
                        :key="issdar.id"
                        :issdar="issdar"
                    />
                </div>

                <!-- Empty state -->
                <div v-else class="py-16 text-center">
                    <PhArticle :size="48" weight="thin" class="mx-auto mb-4 text-neutral-500" />
                    <p class="text-lg text-neutral-500">
                        No issdarat available
                    </p>
                </div>
            </div>
        </section>

        <!-- Top Reviews Section -->
        <section v-if="topReviews.length" class="py-12 px-6">
            <div class="max-w-7xl mx-auto">
                <h2 class="text-3xl font-bold text-neutral-800 text-center mb-8">
                    Top Reviews
                </h2>
                <div class="max-w-2xl mx-auto relative">
                    <div
                        v-for="(review, index) in topReviews"
                        :key="review.id"
                        class="transition-all duration-500"
                        :class="index === currentReview ? 'opacity-100' : 'opacity-0 absolute inset-0'"
                    >
                        <div class="bg-white rounded-3xl border-2 border-accent-200 shadow-md p-8 text-center">
                            <PhQuotes :size="36" weight="fill" class="text-primary-500 mx-auto mb-4" />
                            <p class="text-xl text-neutral-600 leading-relaxed mb-6">
                                {{ review.review }}
                            </p>
                            <div class="flex items-center justify-center gap-0.5 mb-3">
                                <PhStar
                                    v-for="i in 5"
                                    :key="i"
                                    :size="18"
                                    weight="fill"
                                    class="text-primary-500"
                                />
                            </div>
                            <p class="font-bold text-neutral-800 text-lg">{{ review.name }}</p>
                            <p v-if="review.issdar" class="text-base text-neutral-400">{{ review.issdar.title }}</p>
                        </div>
                    </div>

                    <!-- Dots -->
                    <div class="flex items-center justify-center gap-2 mt-6">
                        <span
                            v-for="(_, index) in topReviews"
                            :key="index"
                            @click="currentReview = index"
                            role="button"
                            class="rounded-full transition-all duration-300 cursor-pointer inline-block"
                            :class="index === currentReview
                                ? 'w-5 h-2.5 bg-primary-500'
                                : 'w-2.5 h-2.5 bg-neutral-300 hover:bg-neutral-400'"
                        />
                    </div>
                </div>
            </div>
        </section>
    </div>
</template>