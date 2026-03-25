<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { PhUploadSimple, PhX, PhSpinner } from '@phosphor-icons/vue'
import { ref } from 'vue'

defineOptions({ layout: AdminLayout })

const props = defineProps({
    categories: { type: Array, default: () => [] },
})

const form = useForm({
    title: '',
    description: '',
    thumbnail: null,
    file: null,
    link: '',
    release_date: '',
    categories: [],
})

const thumbnailPreview = ref(null)
const fileName = ref('')

function handleThumbnail(e) {
    const file = e.target.files[0]
    if (file) {
        form.thumbnail = file
        thumbnailPreview.value = URL.createObjectURL(file)
    }
}

function removeThumbnail() {
    form.thumbnail = null
    thumbnailPreview.value = null
}

function handleFile(e) {
    const file = e.target.files[0]
    if (file) {
        form.file = file
        fileName.value = file.name
    }
}

function removeFile() {
    form.file = null
    fileName.value = ''
}

function toggleCategory(categoryId) {
    const index = form.categories.indexOf(categoryId)
    if (index > -1) {
        form.categories.splice(index, 1)
    } else {
        form.categories.push(categoryId)
    }
}

function submit() {
    form.post('/admin/issdarat', {
        forceFormData: true,
    })
}
</script>

<template>
    <Head title="Create New Issdar" />

    <div class="max-w-3xl mx-auto space-y-6">
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-bold text-neutral-800">Create New Issdar</h1>
            <Link
                :href="'/admin/issdarat'"
                class="text-neutral-500 hover:text-neutral-800 font-semibold text-lg rounded-full px-6 py-2 transition-all hover:bg-neutral-100"
            >
                Cancel
            </Link>
        </div>

        <form @submit.prevent="submit" class="bg-white rounded-3xl border-2 border-accent-200 shadow-md p-8 space-y-6">
            <!-- Title -->
            <div>
                <label class="block text-lg font-semibold text-neutral-800 mb-2">Title</label>
                <input
                    v-model="form.title"
                    type="text"
                    class="w-full px-5 py-3 text-lg rounded-xl border-2 border-[#F0F0F0] focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 outline-none transition min-h-[52px]"
                    placeholder="Issdar title"
                />
                <p v-if="form.errors.title" class="mt-1 text-base text-red-500">{{ form.errors.title }}</p>
            </div>

            <!-- Description -->
            <div>
                <label class="block text-lg font-semibold text-neutral-800 mb-2">Description</label>
                <textarea
                    v-model="form.description"
                    rows="6"
                    class="w-full px-5 py-3 text-lg rounded-xl border-2 border-[#F0F0F0] focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 outline-none transition resize-y"
                    placeholder="Issdar description..."
                ></textarea>
                <p v-if="form.errors.description" class="mt-1 text-base text-red-500">{{ form.errors.description }}</p>
            </div>

            <!-- Thumbnail -->
            <div>
                <label class="block text-lg font-semibold text-neutral-800 mb-2">Thumbnail</label>
                <div v-if="thumbnailPreview" class="mb-3 relative inline-block">
                    <img :src="thumbnailPreview" class="w-40 h-40 object-cover rounded-2xl border-2 border-neutral-200" />
                    <button
                        type="button"
                        @click="removeThumbnail"
                        class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full p-1 hover:bg-red-600 transition"
                    >
                        <PhX :size="14" />
                    </button>
                </div>
                <label
                    class="flex items-center gap-3 px-5 py-3 border-2 border-dashed border-neutral-300 rounded-xl cursor-pointer hover:border-primary-400 transition text-lg"
                >
                    <PhUploadSimple :size="24" class="text-neutral-400" />
                    <span class="text-neutral-500">Choose a thumbnail</span>
                    <input type="file" accept="image/*" class="hidden" @change="handleThumbnail" />
                </label>
                <p v-if="form.errors.thumbnail" class="mt-1 text-base text-red-500">{{ form.errors.thumbnail }}</p>
            </div>

            <!-- Issdar File -->
            <div>
                <label class="block text-lg font-semibold text-neutral-800 mb-2">Issdar File (PDF)</label>
                <div v-if="fileName" class="mb-3 flex items-center gap-2 text-lg text-neutral-600 bg-neutral-50 px-5 py-3 rounded-xl border-2 border-neutral-200">
                    <span>{{ fileName }}</span>
                    <button type="button" @click="removeFile" class="text-red-500 hover:text-red-600">
                        <PhX :size="18" />
                    </button>
                </div>
                <label
                    class="flex items-center gap-3 px-5 py-3 border-2 border-dashed border-neutral-300 rounded-xl cursor-pointer hover:border-primary-400 transition text-lg"
                >
                    <PhUploadSimple :size="24" class="text-neutral-400" />
                    <span class="text-neutral-500">Choose a PDF file</span>
                    <input type="file" accept=".pdf" class="hidden" @change="handleFile" />
                </label>
                <p v-if="form.errors.file" class="mt-1 text-base text-red-500">{{ form.errors.file }}</p>
            </div>

            <!-- Link -->
            <div>
                <label class="block text-lg font-semibold text-neutral-800 mb-2">Link (optional)</label>
                <input
                    v-model="form.link"
                    type="url"
                    dir="ltr"
                    class="w-full px-5 py-3 text-lg rounded-xl border-2 border-[#F0F0F0] focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 outline-none transition text-left min-h-[52px]"
                    placeholder="https://..."
                />
                <p v-if="form.errors.link" class="mt-1 text-base text-red-500">{{ form.errors.link }}</p>
            </div>

            <!-- Release Date -->
            <div>
                <label class="block text-lg font-semibold text-neutral-800 mb-2">Release Date</label>
                <input
                    v-model="form.release_date"
                    type="date"
                    class="w-full px-5 py-3 text-lg rounded-xl border-2 border-[#F0F0F0] focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 outline-none transition min-h-[52px]"
                />
                <p v-if="form.errors.release_date" class="mt-1 text-base text-red-500">{{ form.errors.release_date }}</p>
            </div>

            <!-- Categories -->
            <div>
                <label class="block text-lg font-semibold text-neutral-800 mb-3">Categories</label>
                <div class="flex flex-wrap gap-3">
                    <label
                        v-for="cat in categories"
                        :key="cat.id"
                        class="inline-flex items-center gap-2 px-5 py-2.5 border-2 rounded-lg cursor-pointer transition select-none text-lg"
                        :class="form.categories.includes(cat.id)
                            ? 'bg-primary-50 border-primary-500 text-neutral-800 font-semibold'
                            : 'bg-white border-neutral-200 text-neutral-600 hover:border-neutral-300'"
                    >
                        <input
                            type="checkbox"
                            :value="cat.id"
                            :checked="form.categories.includes(cat.id)"
                            @change="toggleCategory(cat.id)"
                            class="sr-only"
                        />
                        {{ cat.name }}
                    </label>
                </div>
                <p v-if="form.errors.categories" class="mt-1 text-base text-red-500">{{ form.errors.categories }}</p>
            </div>

            <!-- Actions -->
            <div class="flex items-center justify-end gap-4 pt-4 border-t-2 border-neutral-200">
                <Link
                    :href="'/admin/issdarat'"
                    class="rounded-full px-8 py-3 text-neutral-600 hover:text-neutral-800 font-semibold text-lg transition-all hover:bg-neutral-100 min-h-[44px]"
                >
                    Cancel
                </Link>
                <button
                    type="submit"
                    :disabled="form.processing"
                    class="inline-flex items-center gap-2 bg-primary-500 text-neutral-800 font-bold rounded-full px-8 py-3 text-lg transition-all hover:shadow-lg disabled:opacity-60 min-h-[44px]"
                >
                    <PhSpinner v-if="form.processing" :size="20" class="animate-spin" />
                    Create
                </button>
            </div>
        </form>
    </div>
</template>