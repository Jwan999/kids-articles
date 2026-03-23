<script setup>
import { Head, router } from '@inertiajs/vue3'
import PublicLayout from '@/Layouts/PublicLayout.vue'
import BannerCarousel from '@/Components/BannerCarousel.vue'
import ArticleCard from '@/Components/ArticleCard.vue'
import {
    PhSparkle,
    PhArticle,
    PhFolders,
    PhEye,
    PhStar,
} from '@phosphor-icons/vue'
import { computed } from 'vue'

defineOptions({ layout: PublicLayout })

const props = defineProps({
    banners: { type: Array, default: () => [] },
    latestArticles: { type: Array, default: () => [] },
    popularArticles: { type: Array, default: () => [] },
    categories: { type: Array, default: () => [] },
    stats: { type: Object, default: () => ({}) },
})

const selectedCategory = computed(() => {
    const params = new URLSearchParams(window.location.search)
    return Number(params.get('category')) || null
})

function filterByCategory(categoryId) {
    router.get('/', categoryId ? { category: categoryId } : {}, {
        preserveState: true,
        preserveScroll: true,
    })
}

const allArticles = computed(() => {
    const seen = new Set()
    const combined = []
    for (const a of [...props.latestArticles, ...props.popularArticles]) {
        if (!seen.has(a.id)) {
            seen.add(a.id)
            combined.push(a)
        }
    }
    return combined
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
        <section class="py-16 px-6 text-center">
            <div class="max-w-7xl mx-auto">
                <h1 class="text-4xl sm:text-5xl font-bold text-neutral-800 mb-4">
                    Discover Our Latest Pages
                </h1>
            </div>
        </section>

        <!-- Quick Stats Section -->
        <section class="border-y-2 border-neutral-200 py-8 px-6">
            <div class="max-w-7xl mx-auto">
                <div class="grid grid-cols-2 md:grid-cols-4 gap-6 text-center">
                    <div>
                        <div class="text-3xl md:text-4xl font-bold text-primary-500">
                            {{ stats.totalArticles ?? 0 }}
                        </div>
                        <div class="text-lg text-neutral-500">Pages</div>
                    </div>
                    <div>
                        <div class="text-3xl md:text-4xl font-bold text-primary-500">
                            {{ stats.totalCategories ?? 0 }}
                        </div>
                        <div class="text-lg text-neutral-500">Categories</div>
                    </div>
                    <div>
                        <div class="text-3xl md:text-4xl font-bold text-primary-500">
                            {{ stats.totalViews?.toLocaleString('en-US') ?? 0 }}
                        </div>
                        <div class="text-lg text-neutral-500">Views</div>
                    </div>
                    <div>
                        <div class="text-3xl md:text-4xl font-bold text-primary-500">
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
                <h2 class="text-3xl font-bold text-neutral-800 text-center mb-8">
                    Browse Pages
                </h2>

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
                            v-if="category.articles_count != null"
                            class="ml-1 text-base opacity-70"
                        >
                            ({{ category.articles_count }})
                        </span>
                    </button>
                </div>

                <!-- Articles Grid -->
                <div
                    v-if="allArticles.length"
                    class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8"
                >
                    <ArticleCard
                        v-for="article in allArticles"
                        :key="article.id"
                        :article="article"
                    />
                </div>

                <!-- Empty state -->
                <div v-else class="py-16 text-center">
                    <PhArticle :size="48" weight="thin" class="mx-auto mb-4 text-neutral-500" />
                    <p class="text-lg text-neutral-500">
                        No pages available
                    </p>
                </div>
            </div>
        </section>
    </div>
</template>