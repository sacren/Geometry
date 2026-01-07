import { ref, computed } from 'vue';
import type { FlashMessages } from '@/types';
import { EMPTY_FLASH } from '@/types';

const localFlash = ref<FlashMessages>({ ...EMPTY_FLASH });
let timeoutId: number | null = null;

const clearTimeoutIfExists = () => {
    if (timeoutId !== null) {
        clearTimeout(timeoutId);
        timeoutId = null;
    }
};

const scheduleAutoHide = () => {
    clearTimeoutIfExists();
    timeoutId = window.setTimeout(() => {
        localFlash.value = { ...EMPTY_FLASH };
    }, 5000);
};

export function useFlash() {
    const flash = computed(() => localFlash.value);
    const hasMessage = computed(() =>
        Boolean(
            flash.value.success ||
            flash.value.error ||
            flash.value.warning ||
            flash.value.info
        )
    );

    const setFlash = (messages: Partial<FlashMessages>) => {
        localFlash.value = { ...EMPTY_FLASH, ...messages };
        if (hasMessage.value) {
            scheduleAutoHide();
        }
    };

    const pauseTimer = () => {
        clearTimeoutIfExists();
    };

    const resumeTimer = () => {
        if (hasMessage.value) {
            scheduleAutoHide();
        }
    };

    return {
        flash,
        hasMessage,
        setFlash,
        pauseTimer,
        resumeTimer,
    };
}
