<script setup lang="ts">
import { useForm } from '@inertiajs/vue3';
import { useFlash } from '@/composables/useFlash';

// Define the form data structure
interface PostFormData {
    content: string;
}

const form = useForm<PostFormData>({
    content: '',
});

const { setFlash } = useFlash();

const handleSubmit = () => {
    form.post('/posts', {
        preserveScroll: true,
        onSuccess: () => {
            form.reset('content');
            setFlash({
                success: 'Post created successfully.'
            });
        },
        // Note: onError for field-level errors is handled automatically by useForm
        // We don't need to manually log or assign them — they appear in form.errors
    });
};
</script>

<template>
    <form @submit.prevent="handleSubmit" class="space-y-4">
        <div>
            <label for="content" class="sr-only">What's on your mind?</label>
            <textarea
                id="content"
                v-model="form.content"
                rows="3"
                class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
                placeholder="What's on your mind?"
            />
            <div v-if="form.errors.content" class="text-red-600 text-sm mt-1" role="alert">
                {{ form.errors.content }}
            </div>
        </div>

        <button
            type="submit"
            class="px-6 py-2 bg-blue-600 text-white font-medium rounded-md hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed transition"
            :disabled="form.processing"
        >
            Create Post
        </button>
    </form>
</template>
