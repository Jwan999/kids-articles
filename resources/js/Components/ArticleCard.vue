<script setup>
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';
import { PhEye, PhDownloadSimple, PhStar, PhArticle } from '@phosphor-icons/vue';

const props = defineProps({
    article: {
        type: Object,
        required: true,
    },
});

const rating = computed(() => props.article.average_rating ?? 0);

const publishYear = computed(() => {
    if (!props.article.release_date) return null;
    return new Date(props.article.release_date).getFullYear();
});

const formattedDate = computed(() => {
    if (!props.article.release_date) return null;
    return new Date(props.article.release_date).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
    });
});
</script>

<template>
    <Link
        :href="`/article/${article.id}`"
        class="group bg-white rounded-3xl border-2 border-accent-200 shadow-md hover:shadow-xl hover:-translate-y-2 transition-all duration-300 cursor-pointer overflow-hidden h-full flex flex-col"
    >
        <!-- Thumbnail -->
        <div class="relative aspect-[210/297] bg-neutral-100 overflow-hidden">
            <img
                v-if="article.thumbnail_url"
                :src="article.thumbnail_url"
                :alt="article.title"
                class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105"
            />
            <div v-else class="w-full h-full flex items-center justify-center bg-accent-50">
                <PhArticle :size="48" class="text-accent-300" />
            </div>

            <!-- Hover overlay -->
            <div class="absolute inset-0 bg-black/0 group-hover:bg-black/30 transition-colors duration-300 flex items-center justify-center">
                <PhEye
                    :size="36"
                    class="text-white opacity-0 group-hover:opacity-100 transition-opacity duration-300"
                />
            </div>

            <!-- Year badge -->
            <span
                v-if="publishYear"
                class="absolute top-2 start-2 bg-neutral-800/70 text-white rounded-full px-3 py-1 text-sm backdrop-blur-sm"
            >
                {{ publishYear }}
            </span>
        </div>

        <!-- Content -->
        <div class="p-5 flex-1 flex flex-col">
            <!-- Title -->
            <h3 class="text-xl font-bold text-neutral-800 mb-3 line-clamp-2">
                {{ article.title }}
            </h3>

            <!-- Date -->
            <p
                v-if="formattedDate"
                class="text-neutral-400 text-base mb-3"
            >
                {{ formattedDate }}
            </p>

            <!-- Rating -->
            <div class="flex items-center gap-0.5 mb-3">
                <PhStar
                    v-for="i in 5"
                    :key="i"
                    :size="18"
                    :weight="i <= Math.round(rating) ? 'fill' : 'regular'"
                    :class="i <= Math.round(rating) ? 'text-primary-500' : 'text-neutral-300'"
                />
            </div>

            <!-- Stats -->
            <div class="flex items-center gap-4 text-neutral-500 text-base mb-3">
                <span class="flex items-center gap-1">
                    <PhEye :size="16" />
                    {{ article.views ?? 0 }}
                </span>
                <span class="flex items-center gap-1">
                    <PhDownloadSimple :size="16" />
                    {{ article.downloads ?? 0 }}
                </span>
            </div>

            <!-- Categories -->
            <div v-if="article.categories?.length" class="flex flex-wrap gap-1.5 mt-auto">
                <span
                    v-for="category in article.categories"
                    :key="category.id"
                    class="bg-secondary-100 text-secondary-700 rounded-full px-3 py-1 text-sm font-semibold"
                >
                    {{ category.name }}
                </span>
            </div>
        </div>
    </Link>
</template>