<script setup>
import { Head, router, useForm } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { PhPlus, PhPencil, PhTrash, PhMagnifyingGlass, PhStar, PhX } from '@phosphor-icons/vue'
import { ref, watch } from 'vue'

defineOptions({ layout: AdminLayout })

const props = defineProps({
    reviews: Object,
    issdarat: { type: Array, default: () => [] },
    filters: {
        type: Object,
        default: () => ({ search: '', rating: '' }),
    },
})

const search = ref(props.filters.search || '')
const ratingFilter = ref(props.filters.rating || '')
let searchTimeout = null

function applyFilters() {
    router.get('/admin/reviews', {
        search: search.value || undefined,
        rating: ratingFilter.value || undefined,
    }, { preserveState: true, preserveScroll: true })
}

watch(search, () => {
    clearTimeout(searchTimeout)
    searchTimeout = setTimeout(applyFilters, 400)
})

watch(ratingFilter, applyFilters)

// Modal state
const showModal = ref(false)
const editingReview = ref(null)

const form = useForm({
    issdar_id: '',
    name: '',
    email: '',
    rating: 5,
    review: '',
})

function openAddModal() {
    editingReview.value = null
    form.reset()
    form.rating = 5
    showModal.value = true
}

function openEditModal(review) {
    editingReview.value = review
    form.issdar_id = review.issdar_id
    form.name = review.name
    form.email = review.email
    form.rating = review.rating
    form.review = review.review
    showModal.value = true
}

function closeModal() {
    showModal.value = false
    editingReview.value = null
    form.reset()
}

function submitForm() {
    if (editingReview.value) {
        form.put(`/admin/reviews/${editingReview.value.id}`, {
            onSuccess: () => closeModal(),
        })
    } else {
        form.post('/admin/reviews', {
            onSuccess: () => closeModal(),
        })
    }
}

function deleteReview(review) {
    router.delete(`/admin/reviews/${review.id}`, {
        onBefore: () => confirm('Are you sure you want to delete this review?'),
    })
}

function formatDate(date) {
    return new Date(date).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
    })
}

function truncate(text, length = 60) {
    if (!text) return ''
    return text.length > length ? text.substring(0, length) + '...' : text
}
</script>

<template>
    <Head title="Manage Reviews" />

    <div class="space-y-6">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <h1 class="text-2xl font-bold text-neutral-800">Manage Reviews</h1>
            <span
                @click="openAddModal"
                role="button"
                class="inline-flex items-center gap-2 bg-primary-500 text-neutral-800 font-bold rounded-full px-8 py-3 min-h-[44px] text-lg transition-all hover:shadow-lg cursor-pointer"
            >
                <PhPlus :size="20" weight="bold" />
                Add Review
            </span>
        </div>

        <!-- Filters -->
        <div class="bg-white rounded-3xl border-2 border-neutral-200 shadow-md p-6">
            <div class="flex flex-col sm:flex-row gap-4">
                <div class="relative flex-1">
                    <PhMagnifyingGlass :size="20" class="absolute left-4 top-1/2 -translate-y-1/2 text-neutral-400" />
                    <input
                        v-model="search"
                        type="text"
                        placeholder="Search by name, email, or review..."
                        class="w-full pl-12 pr-5 py-3 text-lg rounded-xl border-2 border-[#F0F0F0] focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 outline-none transition min-h-[52px]"
                    />
                </div>
                <select
                    v-model="ratingFilter"
                    class="px-5 py-3 text-lg rounded-xl border-2 border-[#F0F0F0] focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 outline-none transition bg-white min-h-[52px]"
                >
                    <option value="">All Ratings</option>
                    <option v-for="r in 5" :key="r" :value="r">{{ r }} Star{{ r > 1 ? 's' : '' }}</option>
                </select>
            </div>
        </div>

        <!-- Reviews Table -->
        <div class="bg-white rounded-3xl border-2 border-neutral-200 shadow-md overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-lg">
                    <thead class="bg-neutral-50">
                        <tr>
                            <th class="text-left px-4 py-3 text-neutral-500 font-semibold">Name</th>
                            <th class="text-left px-4 py-3 text-neutral-500 font-semibold">Email</th>
                            <th class="text-left px-4 py-3 text-neutral-500 font-semibold">Issdar</th>
                            <th class="text-center px-4 py-3 text-neutral-500 font-semibold">Rating</th>
                            <th class="text-left px-4 py-3 text-neutral-500 font-semibold">Review</th>
                            <th class="text-center px-4 py-3 text-neutral-500 font-semibold">Date</th>
                            <th class="text-center px-4 py-3 text-neutral-500 font-semibold">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-neutral-100">
                        <tr v-for="review in reviews.data" :key="review.id" class="hover:bg-neutral-50 transition">
                            <td class="px-4 py-3 font-semibold text-neutral-800 whitespace-nowrap">{{ review.name }}</td>
                            <td class="px-4 py-3 text-neutral-600 whitespace-nowrap">{{ review.email }}</td>
                            <td class="px-4 py-3 text-neutral-600 max-w-[200px]">
                                <span class="line-clamp-1">{{ review.issdar?.title ?? '—' }}</span>
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center justify-center gap-0.5">
                                    <PhStar
                                        v-for="i in 5"
                                        :key="i"
                                        :size="16"
                                        :weight="i <= review.rating ? 'fill' : 'regular'"
                                        :class="i <= review.rating ? 'text-primary-500' : 'text-neutral-300'"
                                    />
                                </div>
                            </td>
                            <td class="px-4 py-3 text-neutral-600 max-w-[250px]">
                                <span class="line-clamp-2">{{ truncate(review.review) }}</span>
                            </td>
                            <td class="px-4 py-3 text-center text-neutral-500 whitespace-nowrap">
                                {{ formatDate(review.created_at) }}
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center justify-center gap-2">
                                    <span
                                        @click="openEditModal(review)"
                                        role="button"
                                        class="p-2 text-accent-600 hover:bg-accent-50 rounded-full transition cursor-pointer"
                                        title="Edit"
                                    >
                                        <PhPencil :size="20" />
                                    </span>
                                    <span
                                        @click="deleteReview(review)"
                                        role="button"
                                        class="p-2 text-red-500 hover:bg-red-50 rounded-full transition cursor-pointer"
                                        title="Delete"
                                    >
                                        <PhTrash :size="20" />
                                    </span>
                                </div>
                            </td>
                        </tr>
                        <tr v-if="!reviews.data?.length">
                            <td colspan="7" class="px-6 py-12 text-center text-neutral-400 text-lg">No reviews found</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div v-if="reviews.links?.length > 3" class="px-6 py-4 border-t-2 border-neutral-200">
                <nav class="flex items-center justify-center gap-1">
                    <template v-for="(link, index) in reviews.links" :key="index">
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

    <!-- Add/Edit Modal -->
    <Teleport to="body">
        <div v-if="showModal" class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <!-- Backdrop -->
            <div class="absolute inset-0 bg-neutral-800/50" @click="closeModal"></div>

            <!-- Modal Content -->
            <div class="relative bg-white rounded-3xl border-2 border-accent-200 shadow-xl w-full max-w-lg p-8 space-y-5">
                <div class="flex items-center justify-between mb-2">
                    <h2 class="text-xl font-bold text-neutral-800">
                        {{ editingReview ? 'Edit Review' : 'Add Review' }}
                    </h2>
                    <span
                        @click="closeModal"
                        role="button"
                        class="p-2 text-neutral-400 hover:text-neutral-600 hover:bg-neutral-100 rounded-full transition cursor-pointer"
                    >
                        <PhX :size="20" />
                    </span>
                </div>

                <form @submit.prevent="submitForm" class="space-y-4">
                    <!-- Issdar Select (only for new) -->
                    <div v-if="!editingReview">
                        <label class="block text-lg font-semibold text-neutral-800 mb-2">Issdar</label>
                        <select
                            v-model="form.issdar_id"
                            class="w-full px-5 py-3 text-lg rounded-xl border-2 border-[#F0F0F0] focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 outline-none transition bg-white min-h-[52px]"
                        >
                            <option value="">Select an issdar...</option>
                            <option v-for="issdar in issdarat" :key="issdar.id" :value="issdar.id">
                                {{ issdar.title }}
                            </option>
                        </select>
                        <p v-if="form.errors.issdar_id" class="mt-1 text-base text-red-500">{{ form.errors.issdar_id }}</p>
                    </div>

                    <!-- Name -->
                    <div>
                        <label class="block text-lg font-semibold text-neutral-800 mb-2">Name</label>
                        <input
                            v-model="form.name"
                            type="text"
                            class="w-full px-5 py-3 text-lg rounded-xl border-2 border-[#F0F0F0] focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 outline-none transition min-h-[52px]"
                            placeholder="Reviewer name"
                        />
                        <p v-if="form.errors.name" class="mt-1 text-base text-red-500">{{ form.errors.name }}</p>
                    </div>

                    <!-- Email -->
                    <div>
                        <label class="block text-lg font-semibold text-neutral-800 mb-2">Email</label>
                        <input
                            v-model="form.email"
                            type="email"
                            dir="ltr"
                            class="w-full px-5 py-3 text-lg rounded-xl border-2 border-[#F0F0F0] focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 outline-none transition text-left min-h-[52px]"
                            placeholder="email@example.com"
                        />
                        <p v-if="form.errors.email" class="mt-1 text-base text-red-500">{{ form.errors.email }}</p>
                    </div>

                    <!-- Rating -->
                    <div>
                        <label class="block text-lg font-semibold text-neutral-800 mb-2">Rating</label>
                        <div class="flex items-center gap-1">
                            <PhStar
                                v-for="i in 5"
                                :key="i"
                                :size="28"
                                :weight="i <= form.rating ? 'fill' : 'regular'"
                                :class="i <= form.rating ? 'text-primary-500 cursor-pointer' : 'text-neutral-300 cursor-pointer'"
                                @click="form.rating = i"
                            />
                        </div>
                        <p v-if="form.errors.rating" class="mt-1 text-base text-red-500">{{ form.errors.rating }}</p>
                    </div>

                    <!-- Review Text -->
                    <div>
                        <label class="block text-lg font-semibold text-neutral-800 mb-2">Review</label>
                        <textarea
                            v-model="form.review"
                            rows="4"
                            class="w-full px-5 py-3 text-lg rounded-xl border-2 border-[#F0F0F0] focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 outline-none transition resize-y"
                            placeholder="Review text..."
                        ></textarea>
                        <p v-if="form.errors.review" class="mt-1 text-base text-red-500">{{ form.errors.review }}</p>
                    </div>

                    <!-- Actions -->
                    <div class="flex items-center justify-end gap-4 pt-4 border-t-2 border-neutral-200">
                        <span
                            @click="closeModal"
                            role="button"
                            class="rounded-full px-8 py-3 text-neutral-600 hover:text-neutral-800 font-semibold text-lg transition-all hover:bg-neutral-100 min-h-[44px] cursor-pointer"
                        >
                            Cancel
                        </span>
                        <span
                            @click="submitForm"
                            role="button"
                            class="inline-flex items-center gap-2 bg-primary-500 text-neutral-800 font-bold rounded-full px-8 py-3 text-lg transition-all hover:shadow-lg min-h-[44px] cursor-pointer"
                            :class="{ 'opacity-60 pointer-events-none': form.processing }"
                        >
                            {{ editingReview ? 'Save Changes' : 'Add Review' }}
                        </span>
                    </div>
                </form>
            </div>
        </div>
    </Teleport>
</template>
