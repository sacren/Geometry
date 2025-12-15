<script setup lang="ts">
import { useDateFormatter } from '@/composables/useDateFormatter';
import type { Post } from '@/types';
import type { PropType } from 'vue';

defineProps({
    post: {
        type: Object as PropType<Post>,
        required: true,
    },
});

const { formatDate } = useDateFormatter();
</script>

<template>
    <article class="bg-white shadow-lg rounded-lg overflow-hidden border border-gray-200">
        <header class="bg-gradient-to-r from-blue-500 to-purple-600 text-white p-6">
            <h2 class="text-2xl font-bold leading-tight">{{ post.title }}</h2>
            <div class="mt-1 text-blue-100">
                By <span class="font-semibold">{{ post.user.name }}</span> on
                <time :datetime="post.created_at">
                    {{ formatDate(post.created_at) }}
                </time>
            </div>
        </header>

        <!-- Post content -->
        <div class="p-6">
            <p class="text-gray-700 leading-relaxed">{{ post.content }}</p>
        </div>
    </article>
</template>
