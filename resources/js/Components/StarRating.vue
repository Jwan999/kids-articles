<script setup>
import { computed } from 'vue';

const props = defineProps({
    rating: {
        type: Number,
        default: 0,
    },
    size: {
        type: String,
        default: 'md',
        validator: (v) => ['sm', 'md', 'lg'].includes(v),
    },
    interactive: {
        type: Boolean,
        default: false,
    },
});

const emit = defineEmits(['update:rating']);

const starSize = computed(() => {
    switch (props.size) {
        case 'sm': return 18;
        case 'lg': return 28;
        default: return 22;
    }
});

function starType(index) {
    const value = props.rating;
    if (value >= index) return 'full';
    if (value >= index - 0.5) return 'half';
    return 'empty';
}

function setRating(index) {
    if (props.interactive) {
        emit('update:rating', index);
    }
}
</script>

<template>
    <div class="flex items-center gap-0.5" :class="{ 'cursor-pointer': interactive }">
        <button
            v-for="i in 5"
            :key="i"
            type="button"
            :disabled="!interactive"
            class="relative focus:outline-none disabled:cursor-default transition-transform"
            :class="{ 'hover:scale-125': interactive }"
            @click="setRating(i)"
        >
            <svg
                :width="starSize"
                :height="starSize"
                viewBox="0 0 24 24"
                fill="none"
                xmlns="http://www.w3.org/2000/svg"
            >
                <!-- Empty star (background) -->
                <path
                    d="M12 2L15.09 8.26L22 9.27L17 14.14L18.18 21.02L12 17.77L5.82 21.02L7 14.14L2 9.27L8.91 8.26L12 2Z"
                    :fill="starType(i) === 'empty' ? '#D1D5DB' : '#FBDC41'"
                    :stroke="starType(i) === 'empty' ? '#D1D5DB' : '#E8C635'"
                    stroke-width="1"
                    stroke-linejoin="round"
                />
                <!-- Half star overlay -->
                <defs v-if="starType(i) === 'half'">
                    <clipPath :id="'half-clip-' + i">
                        <rect x="0" y="0" width="12" height="24" />
                    </clipPath>
                </defs>
                <path
                    v-if="starType(i) === 'half'"
                    d="M12 2L15.09 8.26L22 9.27L17 14.14L18.18 21.02L12 17.77L5.82 21.02L7 14.14L2 9.27L8.91 8.26L12 2Z"
                    fill="#D1D5DB"
                    stroke="#D1D5DB"
                    stroke-width="1"
                    stroke-linejoin="round"
                />
                <path
                    v-if="starType(i) === 'half'"
                    d="M12 2L15.09 8.26L22 9.27L17 14.14L18.18 21.02L12 17.77L5.82 21.02L7 14.14L2 9.27L8.91 8.26L12 2Z"
                    fill="#FBDC41"
                    stroke="#E8C635"
                    stroke-width="1"
                    stroke-linejoin="round"
                    :clip-path="'url(#half-clip-' + i + ')'"
                />
            </svg>
        </button>
    </div>
</template>