<script setup>
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';
import { PhEye, PhDownloadSimple, PhStar, PhArticle } from '@phosphor-icons/vue';

const props = defineProps({
    issdar: {
        type: Object,
        required: true,
    },
});

const rating = computed(() => props.issdar.average_rating ?? 0);

const publishYear = computed(() => {
    if (!props.issdar.release_date) return null;
    return new Date(props.issdar.release_date).getFullYear();
});

const formattedDate = computed(() => {
    if (!props.issdar.release_date) return null;
    return new Date(props.issdar.release_date).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
    });
});
</script>

<template>
    <Link
        :href="`/issdar/${issdar.id}`"
        class="group bg-white rounded-3xl border-2 border-accent-200 shadow-md hover:shadow-xl hover:-translate-y-2 transition-all duration-300 cursor-pointer overflow-hidden h-full flex flex-col"
    >
        <!-- Thumbnail -->
        <div class="relative aspect-[210/297] bg-neutral-100 overflow-hidden">
            <img
                v-if="issdar.thumbnail_url"
                :src="issdar.thumbnail_url"
                :alt="issdar.title"
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
                class="absolute top-3 start-3 bg-primary-500 text-neutral-800 rounded-full px-4 py-1.5 text-base font-bold"
            >
                {{ publishYear }}
            </span>
        </div>

        <!-- Content -->
        <div class="p-5 flex-1 flex flex-col">
            <!-- Title -->
            <h3 class="text-xl font-bold text-neutral-800 mb-2 line-clamp-2">
                {{ issdar.title }}
            </h3>

            <!-- Categories -->
            <div v-if="issdar.categories?.length" class="flex flex-wrap gap-1.5 mb-6">
                <span
                    v-for="category in issdar.categories"
                    :key="category.id"
                    class="bg-secondary-100 text-secondary-700 rounded-full px-3 py-1 text-sm font-semibold"
                >
                    {{ category.name }}
                </span>
            </div>

            <!-- Stats + Date -->
            <div class="flex items-center justify-between mt-auto">
                <div class="flex items-center gap-4 text-neutral-500 text-base">
                    <span class="flex items-center gap-1">
                        <PhEye :size="16" />
                        {{ issdar.views ?? 0 }}
                    </span>
                    <span class="flex items-center gap-1">
                        <PhDownloadSimple :size="16" />
                        {{ issdar.downloads ?? 0 }}
                    </span>
                </div>
                <p v-if="formattedDate" class="text-neutral-400 text-base">
                    {{ formattedDate }}
                </p>
            </div>
        </div>
    </Link>
</template>