<script setup>
import { Head, useForm, router } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import {
    PhPlus, PhPencil, PhTrash, PhUploadSimple, PhX, PhSpinner,
    PhCheck, PhToggleLeft, PhToggleRight, PhStar,
} from '@phosphor-icons/vue'
import { ref, computed } from 'vue'

defineOptions({ layout: AdminLayout })

const props = defineProps({
    banners: { type: Array, default: () => [] },
    bannerGroups: { type: Array, default: () => [] },
})

// Add Banner Form
const addForm = useForm({
    title: '',
    subtitle: '',
    link: '',
    image: null,
})

const imagePreview = ref(null)
const showAddForm = ref(false)

function handleImage(e) {
    const file = e.target.files[0]
    if (file) {
        addForm.image = file
        imagePreview.value = URL.createObjectURL(file)
    }
}

function removeImage() {
    addForm.image = null
    imagePreview.value = null
}

function submitAdd() {
    addForm.post('/admin/banners', {
        forceFormData: true,
        preserveScroll: true,
        onSuccess: () => {
            addForm.reset()
            imagePreview.value = null
            showAddForm.value = false
        },
    })
}

// Edit Banner
const editingId = ref(null)
const editForm = useForm({
    title: '',
    subtitle: '',
    link: '',
    image: null,
})
const editImagePreview = ref(null)

function startEdit(banner) {
    editingId.value = banner.id
    editForm.title = banner.title || ''
    editForm.subtitle = banner.subtitle || ''
    editForm.link = banner.link || ''
    editForm.image = null
    editImagePreview.value = banner.image_url || null
}

function cancelEdit() {
    editingId.value = null
    editForm.reset()
    editImagePreview.value = null
}

function handleEditImage(e) {
    const file = e.target.files[0]
    if (file) {
        editForm.image = file
        editImagePreview.value = URL.createObjectURL(file)
    }
}

function saveEdit(banner) {
    router.post(`/admin/banners/${banner.id}`, {
        _method: 'PUT',
        title: editForm.title,
        subtitle: editForm.subtitle,
        link: editForm.link,
        image: editForm.image,
    }, {
        forceFormData: true,
        preserveScroll: true,
        onSuccess: () => {
            editingId.value = null
            editForm.reset()
            editImagePreview.value = null
        },
        onError: (errors) => {
            editForm.errors = errors
        },
    })
}

// Delete Banner
function deleteBanner(banner) {
    router.delete(`/admin/banners/${banner.id}`, {
        preserveScroll: true,
        onBefore: () => confirm('Are you sure you want to delete this banner?'),
    })
}

// Toggle Active
function toggleActive(banner) {
    router.patch(`/admin/banners/${banner.id}/toggle`, {}, {
        preserveScroll: true,
    })
}

// Banner Group (featured banners)
const activeGroup = props.bannerGroups.find(g => g.is_active)
const selectedBanners = ref(activeGroup?.banner_ids ?? [])
const savingGroup = ref(false)

function toggleBannerSelection(bannerId) {
    const index = selectedBanners.value.indexOf(bannerId)
    if (index > -1) {
        selectedBanners.value.splice(index, 1)
    } else {
        if (selectedBanners.value.length >= 3) {
            alert('You can select up to 3 banners maximum')
            return
        }
        selectedBanners.value.push(bannerId)
    }
}

function saveBannerGroup() {
    savingGroup.value = true
    router.post('/admin/banners/group', {
        name: 'الرئيسية',
        banner_ids: selectedBanners.value,
        is_active: true,
    }, {
        preserveScroll: true,
        onFinish: () => {
            savingGroup.value = false
        },
    })
}
</script>

<template>
    <Head title="Manage Banners" />

    <div class="space-y-8">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <h1 class="text-2xl font-bold text-neutral-800">Manage Banners</h1>
            <button
                @click="showAddForm = !showAddForm"
                class="inline-flex items-center gap-2 bg-primary-500 text-neutral-800 font-bold rounded-full px-8 py-3 text-lg transition-all hover:shadow-lg min-h-[44px]"
            >
                <PhPlus :size="20" weight="bold" />
                Add New Banner
            </button>
        </div>

        <!-- Add Banner Form -->
        <Transition
            enter-active-class="transition duration-200 ease-out"
            enter-from-class="opacity-0 -translate-y-2"
            enter-to-class="opacity-100 translate-y-0"
            leave-active-class="transition duration-150 ease-in"
            leave-from-class="opacity-100 translate-y-0"
            leave-to-class="opacity-0 -translate-y-2"
        >
            <div v-if="showAddForm" class="bg-white rounded-3xl border-2 border-accent-200 shadow-md p-8">
                <h2 class="text-xl font-bold text-neutral-800 mb-4">New Banner</h2>
                <form @submit.prevent="submitAdd" class="space-y-4">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-lg font-semibold text-neutral-800 mb-1">Title</label>
                            <input
                                v-model="addForm.title"
                                type="text"
                                class="w-full px-5 py-3 text-lg rounded-xl border-2 border-[#F0F0F0] focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 outline-none transition min-h-[52px]"
                                placeholder="Banner title"
                            />
                            <p v-if="addForm.errors.title" class="mt-1 text-base text-red-500">{{ addForm.errors.title }}</p>
                        </div>
                        <div>
                            <label class="block text-lg font-semibold text-neutral-800 mb-1">Subtitle</label>
                            <input
                                v-model="addForm.subtitle"
                                type="text"
                                class="w-full px-5 py-3 text-lg rounded-xl border-2 border-[#F0F0F0] focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 outline-none transition min-h-[52px]"
                                placeholder="Subtitle"
                            />
                            <p v-if="addForm.errors.subtitle" class="mt-1 text-base text-red-500">{{ addForm.errors.subtitle }}</p>
                        </div>
                    </div>

                    <div>
                        <label class="block text-lg font-semibold text-neutral-800 mb-1">Link</label>
                        <input
                            v-model="addForm.link"
                            type="url"
                            dir="ltr"
                            class="w-full px-5 py-3 text-lg rounded-xl border-2 border-[#F0F0F0] focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 outline-none transition text-left min-h-[52px]"
                            placeholder="https://..."
                        />
                        <p v-if="addForm.errors.link" class="mt-1 text-base text-red-500">{{ addForm.errors.link }}</p>
                    </div>

                    <div>
                        <label class="block text-lg font-semibold text-neutral-800 mb-1">Image</label>
                        <div v-if="imagePreview" class="mb-3 relative inline-block">
                            <img :src="imagePreview" class="h-32 rounded-2xl object-cover border-2 border-neutral-200" />
                            <button
                                type="button"
                                @click="removeImage"
                                class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full p-1 hover:bg-red-600 transition"
                            >
                                <PhX :size="14" />
                            </button>
                        </div>
                        <label
                            class="flex items-center gap-3 px-5 py-3 border-2 border-dashed border-neutral-300 rounded-xl cursor-pointer hover:border-primary-400 transition text-lg"
                        >
                            <PhUploadSimple :size="24" class="text-neutral-400" />
                            <span class="text-neutral-500">Choose banner image</span>
                            <input type="file" accept="image/*" class="hidden" @change="handleImage" />
                        </label>
                        <p v-if="addForm.errors.image" class="mt-1 text-base text-red-500">{{ addForm.errors.image }}</p>
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-2">
                        <button
                            type="button"
                            @click="showAddForm = false"
                            class="rounded-full px-8 py-3 text-neutral-600 hover:text-neutral-800 font-semibold text-lg transition-all hover:bg-neutral-100 min-h-[44px]"
                        >
                            Cancel
                        </button>
                        <button
                            type="submit"
                            :disabled="addForm.processing"
                            class="inline-flex items-center gap-2 bg-primary-500 text-neutral-800 font-bold rounded-full px-8 py-3 text-lg transition-all hover:shadow-lg disabled:opacity-60 min-h-[44px]"
                        >
                            <PhSpinner v-if="addForm.processing" :size="18" class="animate-spin" />
                            Save Banner
                        </button>
                    </div>
                </form>
            </div>
        </Transition>

        <!-- Banners Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            <div
                v-for="banner in banners"
                :key="banner.id"
                class="bg-white rounded-3xl border-2 border-accent-200 shadow-md overflow-hidden transition-all hover:-translate-y-2 hover:shadow-xl"
            >
                <!-- Edit Mode -->
                <template v-if="editingId === banner.id">
                    <div class="p-6 space-y-3">
                        <div v-if="editImagePreview" class="relative">
                            <img :src="editImagePreview" class="w-full h-40 object-cover rounded-2xl border-2 border-neutral-200" />
                        </div>
                        <label
                            class="flex items-center gap-2 px-5 py-3 border-2 border-dashed border-neutral-300 rounded-xl cursor-pointer hover:border-primary-400 transition text-lg"
                        >
                            <PhUploadSimple :size="18" class="text-neutral-400" />
                            <span class="text-neutral-500">Change image</span>
                            <input type="file" accept="image/*" class="hidden" @change="handleEditImage" />
                        </label>
                        <input
                            v-model="editForm.title"
                            type="text"
                            class="w-full px-5 py-3 text-lg rounded-xl border-2 border-[#F0F0F0] focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 outline-none transition"
                            placeholder="Title"
                        />
                        <input
                            v-model="editForm.subtitle"
                            type="text"
                            class="w-full px-5 py-3 text-lg rounded-xl border-2 border-[#F0F0F0] focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 outline-none transition"
                            placeholder="Subtitle"
                        />
                        <input
                            v-model="editForm.link"
                            type="url"
                            dir="ltr"
                            class="w-full px-5 py-3 text-lg rounded-xl border-2 border-[#F0F0F0] focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 outline-none transition text-left"
                            placeholder="https://..."
                        />
                        <div class="flex items-center gap-2 pt-1">
                            <button
                                @click="saveEdit(banner)"
                                :disabled="editForm.processing"
                                class="flex-1 inline-flex items-center justify-center gap-1 bg-green-500 hover:bg-green-600 text-white font-bold rounded-full px-6 py-3 transition text-lg min-h-[44px]"
                            >
                                <PhSpinner v-if="editForm.processing" :size="18" class="animate-spin" />
                                <PhCheck v-else :size="18" weight="bold" />
                                Save
                            </button>
                            <button
                                @click="cancelEdit"
                                class="flex-1 inline-flex items-center justify-center gap-1 bg-neutral-100 hover:bg-neutral-200 text-neutral-800 font-bold rounded-full px-6 py-3 transition text-lg min-h-[44px]"
                            >
                                <PhX :size="18" />
                                Cancel
                            </button>
                        </div>
                    </div>
                </template>

                <!-- View Mode -->
                <template v-else>
                    <div class="relative">
                        <img
                            v-if="banner.image_url"
                            :src="banner.image_url"
                            :alt="banner.title"
                            class="w-full h-40 object-cover"
                        />
                        <div v-else class="w-full h-40 bg-neutral-100 flex items-center justify-center">
                            <span class="text-neutral-400 text-lg">No image</span>
                        </div>
                        <span
                            class="absolute top-3 left-3 text-base font-semibold px-4 py-1 rounded-full"
                            :class="banner.is_active ? 'bg-green-100 text-green-700' : 'bg-neutral-100 text-neutral-500'"
                        >
                            {{ banner.is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </div>
                    <div class="p-6">
                        <h3 class="text-lg font-bold text-neutral-800">{{ banner.title }}</h3>
                        <p v-if="banner.subtitle" class="text-base text-neutral-500 mt-1">{{ banner.subtitle }}</p>

                        <div class="flex items-center gap-2 mt-4">
                            <button
                                @click="toggleActive(banner)"
                                class="p-2 rounded-full transition"
                                :class="banner.is_active ? 'text-green-600 hover:bg-green-50' : 'text-neutral-400 hover:bg-neutral-50'"
                                :title="banner.is_active ? 'Deactivate' : 'Activate'"
                            >
                                <PhToggleRight v-if="banner.is_active" :size="24" weight="fill" />
                                <PhToggleLeft v-else :size="24" />
                            </button>
                            <button
                                @click="startEdit(banner)"
                                class="p-2 text-accent-600 hover:bg-accent-50 rounded-full transition"
                                title="Edit"
                            >
                                <PhPencil :size="20" />
                            </button>
                            <button
                                @click="deleteBanner(banner)"
                                class="p-2 text-red-500 hover:bg-red-50 rounded-full transition"
                                title="Delete"
                            >
                                <PhTrash :size="20" />
                            </button>
                        </div>
                    </div>
                </template>
            </div>

            <div v-if="!banners.length" class="col-span-full">
                <div class="bg-white rounded-3xl border-2 border-neutral-200 shadow-md p-12 text-center text-neutral-400 text-lg">
                    No banners yet
                </div>
            </div>
        </div>

        <!-- Banner Group Section -->
        <div class="bg-white rounded-3xl border-2 border-accent-200 shadow-md p-6">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h2 class="text-xl font-bold text-neutral-800">Featured Banners</h2>
                    <p class="text-base text-neutral-500 mt-1">Select up to 3 banners to display on the homepage</p>
                </div>
                <button
                    @click="saveBannerGroup"
                    :disabled="savingGroup"
                    class="inline-flex items-center gap-2 bg-primary-500 text-neutral-800 font-bold rounded-full px-8 py-3 text-lg transition-all hover:shadow-lg disabled:opacity-60 min-h-[44px]"
                >
                    <PhSpinner v-if="savingGroup" :size="18" class="animate-spin" />
                    Save Selection
                </button>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                <div
                    v-for="banner in banners"
                    :key="'group-' + banner.id"
                    @click="toggleBannerSelection(banner.id)"
                    class="flex items-center gap-3 p-4 border-2 rounded-xl cursor-pointer transition select-none"
                    :class="selectedBanners.includes(banner.id)
                        ? 'border-primary-500 bg-primary-50'
                        : 'border-neutral-200 hover:border-neutral-300'"
                >
                    <img
                        v-if="banner.image_url"
                        :src="banner.image_url"
                        class="w-16 h-12 rounded-2xl object-cover shrink-0 border-2 border-neutral-200"
                    />
                    <div v-else class="w-16 h-12 rounded-2xl bg-neutral-100 shrink-0 border-2 border-neutral-200"></div>
                    <div class="flex-1 min-w-0">
                        <p class="text-lg font-semibold text-neutral-800 truncate">{{ banner.title }}</p>
                    </div>
                    <div
                        class="w-6 h-6 rounded-full border-2 flex items-center justify-center shrink-0 transition"
                        :class="selectedBanners.includes(banner.id)
                            ? 'border-primary-500 bg-primary-500'
                            : 'border-neutral-300'"
                    >
                        <PhCheck v-if="selectedBanners.includes(banner.id)" :size="14" weight="bold" class="text-white" />
                    </div>
                </div>
            </div>

            <p v-if="!banners.length" class="text-center text-neutral-400 text-lg py-4">
                Add banners first to select featured banners
            </p>
        </div>
    </div>
</template>