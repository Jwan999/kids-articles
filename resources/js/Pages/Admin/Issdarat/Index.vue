<script setup>
import { Head, Link, router } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { PhPlus, PhPencil, PhTrash, PhEye, PhDownloadSimple, PhMagnifyingGlass, PhStar } from '@phosphor-icons/vue'
import { ref, watch } from 'vue'

defineOptions({ layout: AdminLayout })

const props = defineProps({
    issdarat: Object,
    filters: {
        type: Object,
        default: () => ({ search: '', category: '', sort: 'latest' }),
    },
    categories: { type: Array, default: () => [] },
})

const search = ref(props.filters.search || '')
const category = ref(props.filters.category || '')
const sort = ref(props.filters.sort || 'latest')

let searchTimeout = null

function applyFilters() {
    router.get(
        '/admin/issdarat',
        {
            search: search.value || undefined,
            category: category.value || undefined,
            sort: sort.value || undefined,
        },
        { preserveState: true, preserveScroll: true }
    )
}

watch(search, () => {
    clearTimeout(searchTimeout)
    searchTimeout = setTimeout(applyFilters, 400)
})

watch([category, sort], applyFilters)

function deleteIssdar(issdar) {
    router.delete(`/admin/issdarat/${issdar.id}`, {
        onBefore: () => confirm('Are you sure you want to delete this issdar?'),
    })
}

function formatDate(date) {
    return new Date(date).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
    })
}

const sortOptions = [
    { value: 'latest', label: 'Latest' },
    { value: 'most_viewed', label: 'Most Viewed' },
    { value: 'most_downloaded', label: 'Most Downloaded' },
]
</script>

<template>
    <Head title="Manage Issdarat" />

    <div class="space-y-6">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <h1 class="text-2xl font-bold text-neutral-800">Manage Issdarat</h1>
            <Link
                :href="'/admin/issdarat/create'"
                class="inline-flex items-center gap-2 bg-primary-500 text-neutral-800 font-bold rounded-full px-8 py-3 min-h-[44px] text-lg transition-all hover:shadow-lg"
            >
                <PhPlus :size="20" weight="bold" />
                Add New Issdar
            </Link>
        </div>

        <!-- Filters -->
        <div class="bg-white rounded-3xl border-2 border-neutral-200 shadow-md p-6">
            <div class="flex flex-col sm:flex-row gap-4">
                <div class="relative flex-1">
                    <PhMagnifyingGlass :size="20" class="absolute left-4 top-1/2 -translate-y-1/2 text-neutral-400" />
                    <input
                        v-model="search"
                        type="text"
                        placeholder="Search issdarat..."
                        class="w-full pl-12 pr-5 py-3 text-lg rounded-xl border-2 border-[#F0F0F0] focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 outline-none transition min-h-[52px]"
                    />
                </div>
                <select
                    v-model="category"
                    class="px-5 py-3 text-lg rounded-xl border-2 border-[#F0F0F0] focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 outline-none transition bg-white min-h-[52px]"
                >
                    <option value="">All Categories</option>
                    <option v-for="cat in categories" :key="cat.id" :value="cat.id">{{ cat.name }}</option>
                </select>
                <select
                    v-model="sort"
                    class="px-5 py-3 text-lg rounded-xl border-2 border-[#F0F0F0] focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 outline-none transition bg-white min-h-[52px]"
                >
                    <option v-for="opt in sortOptions" :key="opt.value" :value="opt.value">{{ opt.label }}</option>
                </select>
            </div>
        </div>

        <!-- Issdarat Table -->
        <div class="bg-white rounded-3xl border-2 border-neutral-200 shadow-md overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-lg">
                    <thead class="bg-neutral-50">
                        <tr>
                            <th class="text-left px-4 py-3 text-neutral-500 font-semibold">Image</th>
                            <th class="text-left px-4 py-3 text-neutral-500 font-semibold">Title</th>
                            <th class="text-left px-4 py-3 text-neutral-500 font-semibold">Categories</th>
                            <th class="text-center px-4 py-3 text-neutral-500 font-semibold">Views</th>
                            <th class="text-center px-4 py-3 text-neutral-500 font-semibold">Downloads</th>
                            <th class="text-center px-4 py-3 text-neutral-500 font-semibold">Rating</th>
                            <th class="text-center px-4 py-3 text-neutral-500 font-semibold">Date</th>
                            <th class="text-center px-4 py-3 text-neutral-500 font-semibold">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-neutral-100">
                        <tr v-for="issdar in issdarat.data" :key="issdar.id" class="hover:bg-neutral-50 transition">
                            <td class="px-4 py-3">
                                <img
                                    v-if="issdar.thumbnail_url"
                                    :src="issdar.thumbnail_url"
                                    :alt="issdar.title"
                                    class="w-12 h-12 rounded-2xl object-cover border-2 border-neutral-200"
                                />
                                <div v-else class="w-12 h-12 rounded-2xl bg-neutral-100 border-2 border-neutral-200 flex items-center justify-center">
                                    <span class="text-neutral-400 text-base">No image</span>
                                </div>
                            </td>
                            <td class="px-4 py-3 font-semibold text-neutral-800 max-w-xs">
                                <span class="line-clamp-2">{{ issdar.title }}</span>
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex flex-wrap gap-1">
                                    <span
                                        v-for="cat in issdar.categories"
                                        :key="cat.id"
                                        class="inline-block bg-accent-100 text-accent-700 text-base px-3 py-0.5 rounded-full border border-accent-200"
                                    >
                                        {{ cat.name }}
                                    </span>
                                </div>
                            </td>
                            <td class="px-4 py-3 text-center text-neutral-600">
                                <div class="flex items-center justify-center gap-1">
                                    <PhEye :size="18" class="text-neutral-400" />
                                    {{ issdar.views_count?.toLocaleString('en-US') ?? 0 }}
                                </div>
                            </td>
                            <td class="px-4 py-3 text-center text-neutral-600">
                                <div class="flex items-center justify-center gap-1">
                                    <PhDownloadSimple :size="18" class="text-neutral-400" />
                                    {{ issdar.downloads_count?.toLocaleString('en-US') ?? 0 }}
                                </div>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <div class="flex items-center justify-center gap-1">
                                    <PhStar :size="18" weight="fill" class="text-primary-500" />
                                    <span class="text-neutral-600">{{ issdar.average_rating?.toFixed(1) ?? '—' }}</span>
                                </div>
                            </td>
                            <td class="px-4 py-3 text-center text-neutral-500 whitespace-nowrap">
                                {{ formatDate(issdar.created_at) }}
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center justify-center gap-2">
                                    <Link
                                        :href="`/admin/issdarat/${issdar.id}/edit`"
                                        class="p-2 text-accent-600 hover:bg-accent-50 rounded-full transition"
                                        title="Edit"
                                    >
                                        <PhPencil :size="20" />
                                    </Link>
                                    <button
                                        @click="deleteIssdar(issdar)"
                                        class="p-2 text-red-500 hover:bg-red-50 rounded-full transition"
                                        title="Delete"
                                    >
                                        <PhTrash :size="20" />
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <tr v-if="!issdarat.data?.length">
                            <td colspan="8" class="px-6 py-12 text-center text-neutral-400 text-lg">No issdarat available</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div v-if="issdarat.links?.length > 3" class="px-6 py-4 border-t-2 border-neutral-200">
                <nav class="flex items-center justify-center gap-1">
                    <template v-for="(link, index) in issdarat.links" :key="index">
                        <Link
                            v-if="link.url"
                            :href="link.url"
                            class="px-4 py-2 text-lg rounded-full transition"
                            :class="link.active ? 'bg-primary-500 text-neutral-800 font-bold' : 'text-neutral-600 hover:bg-neutral-100'"
                            v-html="link.label"
                            preserve-state
                        />
                        <span
                            v-else
                            class="px-4 py-2 text-lg text-neutral-300"
                            v-html="link.label"
                        />
                    </template>
                </nav>
            </div>
        </div>
    </div>
</template>