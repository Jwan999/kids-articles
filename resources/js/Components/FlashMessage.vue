<script setup>
import { ref, watch, onUnmounted } from 'vue';
import { usePage } from '@inertiajs/vue3';
import { PhCheckCircle, PhWarningCircle, PhX } from '@phosphor-icons/vue';

const page = usePage();
const visible = ref(false);
const message = ref('');
const type = ref('success');
let timer = null;

function show(msg, msgType) {
    message.value = msg;
    type.value = msgType;
    visible.value = true;
    clearTimeout(timer);
    timer = setTimeout(() => {
        visible.value = false;
    }, 4000);
}

function dismiss() {
    visible.value = false;
    clearTimeout(timer);
}

watch(
    () => page.props.flash,
    (flash) => {
        if (flash?.success) {
            show(flash.success, 'success');
        } else if (flash?.error) {
            show(flash.error, 'error');
        }
    },
    { immediate: true, deep: true }
);

onUnmounted(() => {
    clearTimeout(timer);
});
</script>

<template>
    <Transition
        enter-active-class="transition-all duration-300 ease-out"
        enter-from-class="opacity-0 -translate-y-4"
        enter-to-class="opacity-100 translate-y-0"
        leave-active-class="transition-all duration-200 ease-in"
        leave-from-class="opacity-100 translate-y-0"
        leave-to-class="opacity-0 -translate-y-4"
    >
        <div
            v-if="visible"
            class="fixed top-4 left-1/2 -translate-x-1/2 z-[100] w-full max-w-md px-4"
        >
            <div
                :class="[
                    'flex items-center gap-3 px-5 py-4 rounded-xl shadow-lg border-2',
                    type === 'success'
                        ? 'bg-[#4CAF50]/10 border-[#4CAF50] text-[#4CAF50]'
                        : 'bg-error/10 border-error text-error',
                ]"
            >
                <PhCheckCircle v-if="type === 'success'" :size="22" weight="fill" class="text-[#4CAF50] shrink-0" />
                <PhWarningCircle v-else :size="22" weight="fill" class="text-error shrink-0" />

                <span class="flex-1 font-medium text-lg">{{ message }}</span>

                <button
                    class="p-1 rounded-xl hover:bg-black/5 transition-colors shrink-0"
                    @click="dismiss"
                >
                    <PhX :size="18" />
                </button>
            </div>
        </div>
    </Transition>
</template>