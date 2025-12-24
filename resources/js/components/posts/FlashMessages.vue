<script setup lang="ts">
import { ref, watch, onBeforeUnmount } from 'vue';
import type { PropType } from 'vue';
import type { FlashMessages } from '@/types';

const props = defineProps({
    flash: {
        type: Object as PropType<FlashMessages>,
        required: true,
    },
});

// Initialize localFlash with the same shape as FlashMessages
const localFlash = ref<FlashMessages>({ ...props.flash });

let timeoutId: number | null = null;

const clearTimeoutIfExists = () => {
    if (timeoutId !== null) {
        clearTimeout(timeoutId);
        timeoutId = null;
    }
};

const clearFlash = () => {
    localFlash.value = {
        success: null,
        error: null,
        warning: null,
        info: null,
    };
};

const scheduleAutoHide = () => {
    clearTimeoutIfExists();
    timeoutId = window.setTimeout(clearFlash, 5000);
};

// Watch for new flash messages
watch(
    () => props.flash,
    (newFlash) => {
        localFlash.value = { ...newFlash };
        // Only schedule auto-hide if at least one message is non-null
        if (
            newFlash.success ||
            newFlash.error ||
            newFlash.warning ||
            newFlash.info
        ) {
            scheduleAutoHide();
        }
    },
    { immediate: true }
);

onBeforeUnmount(() => {
    clearTimeoutIfExists();
});
</script>

<template>
    <div v-if="localFlash.success" class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
        {{ localFlash.success }}
    </div>
    <div v-if="localFlash.error" class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
        {{ localFlash.error }}
    </div>
    <div v-if="localFlash.warning" class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded mb-4">
        {{ localFlash.warning }}
    </div>
    <div v-if="localFlash.info" class="bg-blue-100 border border-blue-400 text-blue-700 px-4 py-3 rounded mb-4">
        {{ localFlash.info }}
    </div>
</template>
