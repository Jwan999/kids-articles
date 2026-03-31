<script setup>
import { Head, Link } from '@inertiajs/vue3'
import PublicLayout from '@/Layouts/PublicLayout.vue'
import IssdarCard from '@/Components/IssdarCard.vue'
import StarRating from '@/Components/StarRating.vue'
import ReviewForm from '@/Components/ReviewForm.vue'
import {
    PhEye,
    PhDownloadSimple,
    PhCalendar,
    PhStar,
    PhLink,
    PhTag,
    PhChatCircleDots,
    PhFilePdf,
    PhPlus,
    PhArrowLeft,
} from '@phosphor-icons/vue'
import { computed, ref } from 'vue'

defineOptions({ layout: PublicLayout })

const props = defineProps({
    issdar: { type: Object, required: true },
    relatedIssdarat: { type: Array, default: () => [] },
})

const showReviewForm = ref(false)

const formattedDate = computed(() => {
    if (!props.issdar.release_date) return ''
    return new Date(props.issdar.release_date).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
    })
})

const averageRating = computed(() => {
    const reviews = props.issdar.reviews || []
    if (!reviews.length) return 0
    const sum = reviews.reduce((acc, r) => acc + r.rating, 0)
    return (sum / reviews.length).toFixed(1)
})

const reviewsList = computed(() => props.issdar.reviews || [])
</script>

<template>
    <Head :title="issdar.title" />

    <div class="min-h-screen bg-neutral-100">
        <div class="max-w-7xl mx-auto px-6 py-8">

            <!-- Back Button -->
            <Link
                href="/"
                class="inline-flex items-center gap-2 text-neutral-500 hover:text-neutral-800 font-semibold text-lg mb-6 transition-colors"
            >
                <PhArrowLeft :size="20" />
                Back
            </Link>

            <!-- Issdar Header Card -->
            <div class="bg-white rounded-3xl border-2 border-accent-200 shadow-md p-8">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                    <!-- Image -->
                    <div class="flex justify-center">
                        <div class="w-full max-w-md aspect-[210/297] bg-neutral-50 rounded-2xl border-2 border-neutral-200 overflow-hidden">
                            <img
                                v-if="issdar.thumbnail_url"
                                :src="issdar.thumbnail_url"
                                :alt="issdar.title"
                                class="w-full h-full object-cover"
                                @error="$event.target.style.display='none'; $event.target.nextElementSibling.style.display='flex'"
                            />
                            <div
                                class="w-full h-full bg-accent-50 flex items-center justify-center"
                                :style="issdar.thumbnail_url ? 'display:none' : ''"
                            >
                                <PhFilePdf :size="64" weight="thin" class="text-neutral-500" />
                            </div>
                        </div>
                    </div>

                    <!-- Info side -->
                    <div class="flex flex-col h-full">
                        <h1 class="text-3xl font-bold text-neutral-800 mb-4">
                            {{ issdar.title }}
                        </h1>

                        <!-- Categories as badges -->
                        <div v-if="issdar.categories?.length" class="flex flex-wrap gap-2 mb-4">
                            <span
                                v-for="cat in issdar.categories"
                                :key="cat.id"
                                class="inline-flex items-center gap-1.5 bg-accent-100 text-accent-700 rounded-full px-4 py-1.5 text-base font-semibold border border-accent-200"
                            >
                                <PhTag :size="14" weight="bold" />
                                {{ cat.name }}
                            </span>
                        </div>

                        <!-- Stats row -->
                        <div class="flex flex-wrap items-center gap-4 text-base text-neutral-500 mb-4">
                            <div class="flex items-center gap-1.5">
                                <StarRating :rating="Number(averageRating)" :readonly="true" size="sm" />
                                <span class="font-semibold">{{ averageRating }}</span>
                            </div>
                            <span class="flex items-center gap-1">
                                <PhEye :size="18" />
                                {{ issdar.views ?? 0 }} views
                            </span>
                            <span class="flex items-center gap-1">
                                <PhDownloadSimple :size="18" />
                                {{ issdar.downloads ?? 0 }} downloads
                            </span>
                        </div>

                        <!-- Release date -->
                        <div v-if="formattedDate" class="flex items-center gap-1.5 text-base text-neutral-400 mb-6">
                            <PhCalendar :size="18" />
                            {{ formattedDate }}
                        </div>

                        <!-- About section -->
                        <div class="mb-6 flex-1">
                            <h3 class="text-xl font-semibold text-neutral-800 mb-2">About This Issdar</h3>
                            <p class="text-lg text-neutral-600 leading-relaxed whitespace-pre-line">
                                {{ issdar.description }}
                            </p>
                        </div>

                        <!-- Action buttons -->
                        <div class="flex gap-3 justify-end mt-auto">
                            <a
                                v-if="issdar.file_url"
                                :href="issdar.file_url"
                                target="_blank"
                                rel="noopener noreferrer"
                                class="bg-accent-100 text-accent-700 rounded-full p-3 transition-all hover:shadow-lg"
                                title="View PDF"
                            >
                                <PhFilePdf :size="22" weight="bold" />
                            </a>
                            <a
                                :href="'/issdar/' + issdar.id + '/download'"
                                class="bg-primary-500 text-neutral-800 rounded-full p-3 transition-all hover:shadow-lg"
                                title="Download"
                            >
                                <PhDownloadSimple :size="22" weight="bold" />
                            </a>
                            <a
                                v-if="issdar.link"
                                :href="issdar.link"
                                target="_blank"
                                rel="noopener noreferrer"
                                class="bg-accent-500 text-white rounded-full p-3 transition-all hover:shadow-lg"
                                title="External Link"
                            >
                                <PhLink :size="22" weight="bold" />
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Reviews Section -->
            <div class="bg-white rounded-3xl border-2 border-secondary-200 shadow-md p-8 mt-8">
                <!-- Header -->
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-2xl font-bold text-neutral-800">
                        Reviews
                        <span class="text-neutral-500 font-normal text-lg">({{ reviewsList.length }})</span>
                    </h2>
                    <button
                        @click="showReviewForm = !showReviewForm"
                        class="inline-flex items-center gap-1.5 bg-primary-500 text-neutral-800 rounded-full px-8 py-3 text-base font-bold transition-all hover:shadow-lg min-h-[44px]"
                    >
                        <PhPlus :size="18" weight="bold" />
                        Add Review
                    </button>
                </div>

                <!-- Review Form (toggle) -->
                <div v-if="showReviewForm" class="mb-6 p-6 bg-neutral-100 rounded-3xl border-2 border-neutral-200">
                    <ReviewForm :issdar-id="issdar.id" />
                </div>

                <!-- Reviews List -->
                <div v-if="reviewsList.length" class="space-y-4">
                    <div
                        v-for="review in reviewsList"
                        :key="review.id"
                        class="border-l-4 border-primary-500 pl-6 py-4"
                    >
                        <div class="flex items-center justify-between mb-2">
                            <div class="flex items-center gap-3">
                                <div class="flex h-10 w-10 items-center justify-center rounded-full bg-accent-100 text-accent-700 font-bold text-base">
                                    {{ review.name?.charAt(0) || '?' }}
                                </div>
                                <div>
                                    <p class="text-lg font-bold text-neutral-800">{{ review.name }}</p>
                                    <p class="text-base text-neutral-400">
                                        {{ new Date(review.created_at).toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' }) }}
                                    </p>
                                </div>
                            </div>
                            <StarRating :rating="review.rating" :readonly="true" size="sm" />
                        </div>
                        <p v-if="review.review" class="text-lg text-neutral-600 leading-relaxed">
                            {{ review.review }}
                        </p>
                    </div>
                </div>

                <!-- Empty state -->
                <div v-else class="py-12 text-center">
                    <PhChatCircleDots :size="48" weight="thin" class="mx-auto mb-3 text-neutral-500" />
                    <p class="text-lg text-neutral-500">No reviews yet. Be the first!</p>
                </div>
            </div>

            <!-- Related Issdarat -->
            <section v-if="relatedIssdarat.length" class="mt-8">
                <h2 class="text-2xl font-bold text-neutral-800 mb-6">Related Issdarat</h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8">
                    <IssdarCard
                        v-for="related in relatedIssdarat"
                        :key="related.id"
                        :issdar="related"
                    />
                </div>
            </section>
        </div>
    </div>
</template>