<script setup lang="ts">
import { router } from '@inertiajs/vue3';
import { useDateFormatter } from '@/composables/useDateFormatter';
import { useFlash } from '@/composables/useFlash';
import type { Post } from '@/types';
import type { PropType } from 'vue';

const props = defineProps({
    post: {
        type: Object as PropType<Post>,
        required: true,
    },
});

const { setFlash } = useFlash();

const authorName = props.post.user?.name || 'Unknown author';
const { formatDate } = useDateFormatter();
const formattedDate = formatDate(props.post.created_at);

const confirmDelete = () => {
    if (confirm('Are you sure you want to delete this post?')) {
        router.delete(`/posts/${props.post.id}`, {
            preserveScroll: true,
            onSuccess: () => {
                setFlash({ success: 'Post deleted successfully.' });
            },
        });
    }
}
</script>

<template>
    <article class="bg-white shadow-lg rounded-lg overflow-hidden border border-gray-200">
        <header class="bg-gradient-to-r from-blue-500 to-purple-600 text-white p-6">
            <h2 class="text-2xl font-bold leading-tight">{{ post.title }}</h2>
            <div class="mt-1 text-blue-100">
                By <span class="font-semibold">{{ authorName }}</span> on
                <time :datetime="post.created_at">
                    {{ formattedDate }}
                </time>
            </div>
        </header>

        <!-- Post content -->
        <div class="p-6">
            <p class="text-gray-700 leading-relaxed">{{ post.content }}</p>
        </div>

        <!-- Delete button (only for owner) -->
        <div v-if="$page.props.auth.user?.id === post.user_id" class="px-6 pb-6">
            <button
                type="button"
                @click="confirmDelete"
                class="text-sm text-red-600 hover:text-red-800 font-medium"
            >
                Delete Post
            </button>
        </div>
    </article>
</template>
