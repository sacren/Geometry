<script setup lang="ts">
import { Head, Link as InertiaLink, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import AppLayout from '@/layouts/AppLayout.vue';
import FlashMessages from '@/components/posts/FlashMessages.vue';
import PostForm from '@/components/posts/PostForm.vue';
import PostPreview from '@/components/posts/PostPreview.vue';
import type { PostsIndexProps, BreadcrumbItem } from '@/types';

const flash = computed(() => usePage().props.flash);

defineProps<PostsIndexProps>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Posts',
        href: '/posts',
    },
];
</script>

<template>
    <Head title="Posts" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="p-4 sm:p-6 max-w-4xl mx-auto space-y-8">
            <!-- Flash messages -->
            <FlashMessages :flash="flash" />
            <h1 class="text-2xl font-bold mb-6">All Posts</h1>

            <!-- Create new post form -->
            <section class="bg-white p-6 rounded-lg shadow-md border border-gray-200">
                <h2 class="text-xl font-semibold mb-4">Write a new post</h2>
                <PostForm />
            </section>

            <!-- Empty state when no posts exist -->
            <div v-if="!posts.data.length" class="text-center py-8 text-gray-500">
                No posts found.
            </div>

            <div v-if="posts.data.length" class="space-y-8">
                <PostPreview
                    v-for="post in posts.data"
                    :key="post.id"
                    :post="post"
                />
            </div>

            <!-- Pagination -->
            <nav
                v-if="posts.links.length > 3"
                class="flex justify-center mt-12 space-x-2"
                aria-label="Pagination"
            >
                <template v-for="(link, index) in posts.links" :key="index">
                    <InertiaLink
                        v-if="link.url"
                        :href="link.url"
                        :aria-label="`Go to page ${link.label}`"
                        v-html="link.label"
                        :class="[ 'px-3 py-1 text-sm border rounded transition', link.active ? 'bg-blue-600 text-white border-blue-600' : 'text-blue-600 hover:bg-blue-50', ]"
                    />
                    <span
                        v-else
                        v-html="link.label"
                        :aria-label="`Page ${link.label}`"
                        class="px-3 py-1 text-sm text-gray-400"
                    />
                </template>
            </nav>
        </div>
    </AppLayout>
</template>
