<script setup>
import { useForm } from '@inertiajs/vue3';
import { PhPaperPlaneRight } from '@phosphor-icons/vue';
import StarRating from '@/Components/StarRating.vue';

const props = defineProps({
    issdarId: {
        type: [Number, String],
        required: true,
    },
});

const form = useForm({
    issdar_id: props.issdarId,
    name: '',
    email: '',
    rating: 0,
    review: '',
});

function submit() {
    form.post('/reviews', {
        preserveScroll: true,
        onSuccess: () => {
            form.reset('name', 'email', 'rating', 'review');
        },
    });
}
</script>

<template>
    <form @submit.prevent="submit" class="space-y-5">
        <!-- Name -->
        <div>
            <label class="block text-neutral-800 font-semibold text-lg mb-2">Name</label>
            <input
                v-model="form.name"
                type="text"
                placeholder="Enter your name"
                class="w-full rounded-xl border-2 border-[#F0F0F0] bg-white px-5 py-3 text-lg min-h-[52px] text-neutral-800 placeholder:text-neutral-400 hover:border-neutral-300 focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 transition-colors outline-none"
            />
            <p v-if="form.errors.name" class="text-error text-base mt-1">{{ form.errors.name }}</p>
        </div>

        <!-- Email -->
        <div>
            <label class="block text-neutral-800 font-semibold text-lg mb-2">Email</label>
            <input
                v-model="form.email"
                type="email"
                placeholder="example@email.com"
                dir="ltr"
                class="w-full rounded-xl border-2 border-[#F0F0F0] bg-white px-5 py-3 text-lg min-h-[52px] text-neutral-800 text-left placeholder:text-neutral-400 hover:border-neutral-300 focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 transition-colors outline-none"
            />
            <p v-if="form.errors.email" class="text-error text-base mt-1">{{ form.errors.email }}</p>
        </div>

        <!-- Rating -->
        <div>
            <label class="block text-neutral-800 font-semibold text-lg mb-2">Rating</label>
            <StarRating
                :rating="form.rating"
                size="lg"
                :interactive="true"
                @update:rating="form.rating = $event"
            />
            <p v-if="form.errors.rating" class="text-error text-base mt-1">{{ form.errors.rating }}</p>
        </div>

        <!-- Review Text -->
        <div>
            <label class="block text-neutral-800 font-semibold text-lg mb-2">Review</label>
            <textarea
                v-model="form.review"
                rows="4"
                placeholder="Share your thoughts..."
                class="w-full rounded-xl border-2 border-[#F0F0F0] bg-white px-5 py-3 text-lg min-h-[52px] text-neutral-800 placeholder:text-neutral-400 hover:border-neutral-300 focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 transition-colors outline-none resize-none"
            />
            <p v-if="form.errors.review" class="text-error text-base mt-1">{{ form.errors.review }}</p>
        </div>

        <!-- Submit -->
        <button
            type="submit"
            :disabled="form.processing"
            class="inline-flex items-center justify-center gap-2 bg-primary-500 text-neutral-800 rounded-full px-10 py-3 text-lg font-bold min-h-[52px] hover:-translate-y-1 hover:scale-[1.02] active:translate-y-0 active:scale-[0.98] transition-all disabled:opacity-50 disabled:cursor-not-allowed"
        >
            <svg
                v-if="form.processing"
                class="animate-spin h-5 w-5"
                xmlns="http://www.w3.org/2000/svg"
                fill="none"
                viewBox="0 0 24 24"
            >
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z" />
            </svg>
            <PhPaperPlaneRight v-else :size="20" />
            <span>{{ form.processing ? 'Submitting...' : 'Submit Review' }}</span>
        </button>
    </form>
</template>