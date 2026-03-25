<script setup>
import { Head, Link } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { PhArticle, PhEye, PhDownloadSimple, PhChatDots, PhStar } from '@phosphor-icons/vue'

defineOptions({ layout: AdminLayout })

const props = defineProps({
    totalIssdarat: { type: Number, default: 0 },
    totalViews: { type: Number, default: 0 },
    totalDownloads: { type: Number, default: 0 },
    totalReviews: { type: Number, default: 0 },
    averageRating: { type: Number, default: 0 },
    latestIssdarat: { type: Array, default: () => [] },
    latestReviews: { type: Array, default: () => [] },
    categoriesCount: { type: Number, default: 0 },
})

function formatDate(date) {
    return new Date(date).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
    })
}

function truncate(text, length = 80) {
    if (!text) return ''
    return text.length > length ? text.substring(0, length) + '...' : text
}

const statsCards = [
    {
        label: 'Total Issdarat',
        key: 'totalIssdarat',
        icon: PhArticle,
        bgColor: 'bg-primary-100',
        iconColor: 'text-primary-600',
    },
    {
        label: 'Total Views',
        key: 'totalViews',
        icon: PhEye,
        bgColor: 'bg-accent-100',
        iconColor: 'text-accent-600',
    },
    {
        label: 'Total Downloads',
        key: 'totalDownloads',
        icon: PhDownloadSimple,
        bgColor: 'bg-secondary-100',
        iconColor: 'text-secondary-600',
    },
    {
        label: 'Total Reviews',
        key: 'totalReviews',
        icon: PhChatDots,
        bgColor: 'bg-green-100',
        iconColor: 'text-green-600',
    },
]
</script>

<template>
    <Head title="Dashboard" />

    <div class="space-y-8">
        <h1 class="text-2xl font-bold text-neutral-800">Dashboard</h1>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            <div
                v-for="card in statsCards"
                :key="card.key"
                class="bg-white rounded-3xl border-2 border-accent-200 shadow-md p-6 flex items-center gap-4"
            >
                <div :class="[card.bgColor, 'w-14 h-14 rounded-full flex items-center justify-center shrink-0']">
                    <component :is="card.icon" :size="28" :class="card.iconColor" weight="duotone" />
                </div>
                <div>
                    <p class="text-3xl font-bold text-neutral-800">{{ props[card.key]?.toLocaleString('en-US') }}</p>
                    <p class="text-lg text-neutral-500 mt-1">{{ card.label }}</p>
                </div>
            </div>
        </div>

        <!-- Average Rating -->
        <div class="bg-white rounded-3xl border-2 border-accent-200 shadow-md p-6">
            <h2 class="text-xl font-bold text-neutral-800 mb-4">Average Rating</h2>
            <div class="flex items-center gap-3">
                <div class="flex items-center gap-1">
                    <PhStar
                        v-for="i in 5"
                        :key="i"
                        :size="28"
                        :weight="i <= Math.round(averageRating) ? 'fill' : 'regular'"
                        :class="i <= Math.round(averageRating) ? 'text-primary-500' : 'text-neutral-300'"
                    />
                </div>
                <span class="text-2xl font-bold text-neutral-800">{{ averageRating.toFixed(1) }}</span>
                <span class="text-lg text-neutral-500">out of 5</span>
            </div>
        </div>

        <!-- Latest Issdarat -->
        <div class="bg-white rounded-3xl border-2 border-neutral-200 shadow-md overflow-hidden">
            <div class="p-6 border-b-2 border-neutral-200">
                <div class="flex items-center justify-between">
                    <h2 class="text-xl font-bold text-neutral-800">Latest Issdarat</h2>
                    <Link href="/admin/issdarat" class="text-lg text-accent-600 hover:text-accent-700 font-semibold">
                        View All
                    </Link>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-lg">
                    <thead class="bg-neutral-50">
                        <tr>
                            <th class="text-left px-6 py-3 text-neutral-500 font-semibold">Title</th>
                            <th class="text-center px-4 py-3 text-neutral-500 font-semibold">Views</th>
                            <th class="text-center px-4 py-3 text-neutral-500 font-semibold">Downloads</th>
                            <th class="text-center px-4 py-3 text-neutral-500 font-semibold">Date</th>
                            <th class="text-center px-4 py-3 text-neutral-500 font-semibold">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-neutral-100">
                        <tr v-for="issdar in latestIssdarat" :key="issdar.id" class="hover:bg-neutral-50 transition">
                            <td class="px-6 py-4 font-semibold text-neutral-800">{{ issdar.title }}</td>
                            <td class="px-4 py-4 text-center text-neutral-600">{{ issdar.views?.toLocaleString('en-US') ?? 0 }}</td>
                            <td class="px-4 py-4 text-center text-neutral-600">{{ issdar.downloads?.toLocaleString('en-US') ?? 0 }}</td>
                            <td class="px-4 py-4 text-center text-neutral-500">{{ formatDate(issdar.created_at) }}</td>
                            <td class="px-4 py-4 text-center">
                                <Link
                                    :href="`/admin/issdarat/${issdar.id}/edit`"
                                    class="text-accent-600 hover:text-accent-700 font-semibold"
                                >
                                    Edit
                                </Link>
                            </td>
                        </tr>
                        <tr v-if="!latestIssdarat.length">
                            <td colspan="5" class="px-6 py-8 text-center text-neutral-400 text-lg">No issdarat yet</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Latest Reviews -->
        <div class="bg-white rounded-3xl border-2 border-neutral-200 shadow-md overflow-hidden">
            <div class="p-6 border-b-2 border-neutral-200">
                <h2 class="text-xl font-bold text-neutral-800">Latest Reviews</h2>
            </div>
            <div class="divide-y divide-neutral-100">
                <div v-for="review in latestReviews" :key="review.id" class="p-6">
                    <div class="flex items-start justify-between gap-4">
                        <div class="flex-1">
                            <div class="flex items-center gap-3 mb-2">
                                <span class="text-lg font-bold text-neutral-800">{{ review.name }}</span>
                                <span class="text-neutral-400">|</span>
                                <span class="text-base text-neutral-500">{{ review.issdar?.title }}</span>
                            </div>
                            <div class="flex items-center gap-1 mb-2">
                                <PhStar
                                    v-for="i in 5"
                                    :key="i"
                                    :size="18"
                                    :weight="i <= review.rating ? 'fill' : 'regular'"
                                    :class="i <= review.rating ? 'text-primary-500' : 'text-neutral-300'"
                                />
                            </div>
                            <p class="text-lg text-neutral-600 leading-relaxed">{{ truncate(review.review) }}</p>
                        </div>
                    </div>
                </div>
                <div v-if="!latestReviews.length" class="p-8 text-center text-neutral-400 text-lg">
                    No reviews yet
                </div>
            </div>
        </div>
    </div>
</template>