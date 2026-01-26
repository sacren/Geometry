<script setup lang="ts">
import { router } from '@inertiajs/vue3';
import { like, unlike } from '@/routes/posts';
import type { Likeable } from '@/types';
import { ref } from 'vue';

const props = defineProps<{
    item: Likeable;
}>();

// Track loading state to prevent duplicate requests
const isLoading = ref(false);

// Track local state for optimistic updates
const localItem = ref({
    liked_by_current_user: props.item.liked_by_current_user,
    likes_count: props.item.likes_count,
});

const toggleLike = (): void => {
    // Prevent multiple clicks during request
    if (isLoading.value) return;

    // Optimistic update: immediately update UI
    const wasLiked = localItem.value.liked_by_current_user;
    localItem.value.liked_by_current_user = !wasLiked;
    localItem.value.likes_count += wasLiked ? -1 : 1;

    isLoading.value = true;

    if (wasLiked) {
        router.delete(unlike.url(props.item.id), {
            preserveScroll: true,
            onError: () => {
                // Revert optimistic update on error
                localItem.value.liked_by_current_user = wasLiked;
                localItem.value.likes_count += wasLiked ? 1 : -1;
            },
            onFinish: () => {
                isLoading.value = false;
            },
        });
    } else {
        router.post(like.url(props.item.id), {}, {
            preserveScroll: true,
            onError: () => {
                // Revert optimistic update on error
                localItem.value.liked_by_current_user = wasLiked;
                localItem.value.likes_count += wasLiked ? 1 : -1;
            },
            onFinish: () => {
                isLoading.value = false;
            },
        });
    }
};
</script>

<template>
    <button
        type="button"
        @click="toggleLike"
        :disabled="isLoading"
        class="flex items-center space-x-2 text-sm font-medium transition-opacity duration-200"
        :class="{
            'text-red-600 hover:text-red-800': localItem.liked_by_current_user,
            'text-gray-600 hover:text-gray-800': !localItem.liked_by_current_user,
            'opacity-75 cursor-not-allowed': isLoading,
        }"
    >
        <!-- Loading spinner -->
        <svg
            v-if="isLoading"
            class="animate-spin h-5 w-5 text-current"
            xmlns="http://www.w3.org/2000/svg"
            fill="none"
            viewBox="0 0 24 24"
        >
            <circle
                class="opacity-25"
                cx="12"
                cy="12"
                r="10"
                stroke="currentColor"
                stroke-width="4"
            ></circle>
            <path
                class="opacity-75"
                fill="currentColor"
                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
            ></path>
        </svg>

        <!-- Heart icon when liked -->
        <svg
            v-else-if="localItem.liked_by_current_user"
            xmlns="http://www.w3.org/2000/svg"
            class="h-5 w-5"
            viewBox="0 0 24 24"
            fill="currentColor"
        >
            <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z" />
        </svg>

        <!-- Heart outline icon when not liked -->
        <svg
            v-else
            xmlns="http://www.w3.org/2000/svg"
            class="h-5 w-5"
            fill="none"
            viewBox="0 0 24 24"
            stroke="currentColor"
        >
            <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"
            />
        </svg>

        <span>{{ localItem.liked_by_current_user ? 'Liked' : 'Like' }}</span>
        <span>({{ localItem.likes_count }})</span>
    </button>
</template>
