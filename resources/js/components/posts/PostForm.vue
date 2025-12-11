<script setup lang="ts">
import { reactive } from 'vue';
import { router } from '@inertiajs/vue3';

interface FormProps {
    onSuccess?: () => void;
}

const props = withDefaults(defineProps<FormProps>(), {
    onSuccess: () => {},
});

const form = reactive({
    title: '',
    content: '',
});

const handleSubmit = () => {
    router.post('/posts', form, {
        preserveScroll: true,
        onSuccess: () => {
            form.title = '';
            form.content = '';
            props.onSuccess();
        },
        onError: (errors) => {
            console.log(errors);
        },
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
            <div v-if="$page.props.errors.content" class="text-red-600 text-sm mt-1" role="alert">
                {{ $page.props.errors.content }}
            </div>
        </div>

        <button
            type="submit"
            class="px-6 py-2 bg-blue-600 text-white font-medium rounded-md hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed transition"
        >
            Create Post
        </button>
    </form>
</template>
