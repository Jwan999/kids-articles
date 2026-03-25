<script setup>
import { Head, useForm, router } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { PhPlus, PhPencil, PhTrash, PhCheck, PhX, PhSpinner } from '@phosphor-icons/vue'
import { ref } from 'vue'

defineOptions({ layout: AdminLayout })

const props = defineProps({
    categories: { type: Array, default: () => [] },
})

const addForm = useForm({
    name: '',
})

const editingId = ref(null)
const editForm = useForm({
    name: '',
})

function addCategory() {
    addForm.post('/admin/categories', {
        preserveScroll: true,
        onSuccess: () => {
            addForm.reset()
        },
    })
}

function startEdit(category) {
    editingId.value = category.id
    editForm.name = category.name
}

function cancelEdit() {
    editingId.value = null
    editForm.reset()
}

function saveEdit(category) {
    editForm.put(`/admin/categories/${category.id}`, {
        preserveScroll: true,
        onSuccess: () => {
            editingId.value = null
            editForm.reset()
        },
    })
}

function deleteCategory(category) {
    if (category.issdarat_count > 0) {
        alert('Cannot delete a category that has issdarat. Please remove the issdarat first.')
        return
    }
    router.delete(`/admin/categories/${category.id}`, {
        preserveScroll: true,
        onBefore: () => confirm('Are you sure you want to delete this category?'),
    })
}
</script>

<template>
    <Head title="Manage Categories" />

    <div class="max-w-2xl mx-auto space-y-6">
        <h1 class="text-2xl font-bold text-neutral-800">Manage Categories</h1>

        <!-- Add Category -->
        <div class="bg-white rounded-3xl border-2 border-neutral-200 shadow-md p-6">
            <form @submit.prevent="addCategory" class="flex items-start gap-3">
                <div class="flex-1">
                    <input
                        v-model="addForm.name"
                        type="text"
                        class="w-full px-5 py-3 text-lg rounded-xl border-2 border-[#F0F0F0] focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 outline-none transition min-h-[52px]"
                        placeholder="New category name"
                    />
                    <p v-if="addForm.errors.name" class="mt-1 text-base text-red-500">{{ addForm.errors.name }}</p>
                </div>
                <button
                    type="submit"
                    :disabled="addForm.processing || !addForm.name.trim()"
                    class="inline-flex items-center gap-2 bg-primary-500 text-neutral-800 font-bold rounded-full px-8 py-3 text-lg transition-all hover:shadow-lg disabled:opacity-60 shrink-0 min-h-[44px]"
                >
                    <PhSpinner v-if="addForm.processing" :size="18" class="animate-spin" />
                    <PhPlus v-else :size="18" weight="bold" />
                    Add
                </button>
            </form>
        </div>

        <!-- Categories List -->
        <div class="bg-white rounded-3xl border-2 border-neutral-200 shadow-md overflow-hidden">
            <div class="divide-y divide-neutral-100">
                <div
                    v-for="category in categories"
                    :key="category.id"
                    class="px-6 py-4 flex items-center gap-4 hover:bg-neutral-50 transition rounded-xl"
                >
                    <!-- View Mode -->
                    <template v-if="editingId !== category.id">
                        <div class="flex-1">
                            <span class="text-lg font-semibold text-neutral-800">{{ category.name }}</span>
                            <span class="ml-2 text-base text-neutral-400">
                                ({{ category.issdarat_count }} {{ category.issdarat_count === 1 ? 'issdar' : 'issdarat' }})
                            </span>
                        </div>
                        <div class="flex items-center gap-2">
                            <button
                                @click="startEdit(category)"
                                class="p-2 text-accent-600 hover:bg-accent-50 rounded-full transition"
                                title="Edit"
                            >
                                <PhPencil :size="20" />
                            </button>
                            <button
                                @click="deleteCategory(category)"
                                class="p-2 rounded-full transition"
                                :class="category.issdarat_count > 0
                                    ? 'text-neutral-300 cursor-not-allowed'
                                    : 'text-red-500 hover:bg-red-50'"
                                :title="category.issdarat_count > 0 ? 'Cannot delete - has issdarat' : 'Delete'"
                            >
                                <PhTrash :size="20" />
                            </button>
                        </div>
                    </template>

                    <!-- Edit Mode -->
                    <template v-else>
                        <div class="flex-1">
                            <input
                                v-model="editForm.name"
                                type="text"
                                class="w-full px-5 py-3 text-lg rounded-xl border-2 border-[#F0F0F0] focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 outline-none transition"
                                @keyup.enter="saveEdit(category)"
                                @keyup.escape="cancelEdit"
                            />
                            <p v-if="editForm.errors.name" class="mt-1 text-base text-red-500">{{ editForm.errors.name }}</p>
                        </div>
                        <div class="flex items-center gap-2">
                            <button
                                @click="saveEdit(category)"
                                :disabled="editForm.processing"
                                class="p-2 text-green-600 hover:bg-green-50 rounded-full transition"
                                title="Save"
                            >
                                <PhSpinner v-if="editForm.processing" :size="20" class="animate-spin" />
                                <PhCheck v-else :size="20" weight="bold" />
                            </button>
                            <button
                                @click="cancelEdit"
                                class="p-2 text-neutral-400 hover:bg-neutral-50 rounded-full transition"
                                title="Cancel"
                            >
                                <PhX :size="20" />
                            </button>
                        </div>
                    </template>
                </div>

                <div v-if="!categories.length" class="px-6 py-12 text-center text-neutral-400 text-lg">
                    No categories yet
                </div>
            </div>
        </div>
    </div>
</template>