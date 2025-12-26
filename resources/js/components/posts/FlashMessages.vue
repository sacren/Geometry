<script setup lang="ts">
import { ref, watch, onBeforeUnmount, computed } from 'vue';
import type { PropType } from 'vue';
import type { FlashMessages } from '@/types';
import { EMPTY_FLASH } from '@/types';

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
    localFlash.value = { ...EMPTY_FLASH };
};

const scheduleAutoHide = () => {
    clearTimeoutIfExists();
    timeoutId = window.setTimeout(clearFlash, 5000);
};

// UX: Pause/Resume the timer if the user hovers over/off the message
const pauseTimer = () => clearTimeoutIfExists();

const resumeTimer = () => {
    if (hasMessage.value) {
        scheduleAutoHide();
    }
};

// Helper to check if any message exists
const hasMessage = computed(() =>
    Boolean(
        localFlash.value.success ||
        localFlash.value.error ||
        localFlash.value.warning ||
        localFlash.value.info
    )
);

// Watch for new flash messages
watch(
    () => props.flash,
    (newFlash) => {
        localFlash.value = { ...newFlash };

        if (hasMessage.value) {
            scheduleAutoHide();
        } else {
            // If parent clears flash, we ensure timer is killed
            clearTimeoutIfExists();
        }
    },
    { immediate: true }
);

onBeforeUnmount(() => {
    clearTimeoutIfExists();
});
</script>

<template>
    <div
        v-if="localFlash.success"
        class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4"
        role="alert"
        @mouseenter="pauseTimer"
        @mouseleave="resumeTimer"
    >
        {{ localFlash.success }}
    </div>
    <div
        v-if="localFlash.error"
        class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4"
        role="alert"
        @mouseenter="pauseTimer"
        @mouseleave="resumeTimer"
    >
        {{ localFlash.error }}
    </div>
    <div
        v-if="localFlash.warning"
        class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded mb-4"
        role="alert"
        @mouseenter="pauseTimer"
        @mouseleave="resumeTimer"
    >
        {{ localFlash.warning }}
    </div>
    <div
        v-if="localFlash.info"
        class="bg-blue-100 border border-blue-400 text-blue-700 px-4 py-3 rounded mb-4"
        role="alert"
        @mouseenter="pauseTimer"
        @mouseleave="resumeTimer"
    >
        {{ localFlash.info }}
    </div>
</template>
