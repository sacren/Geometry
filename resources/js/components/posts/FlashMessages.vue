<script setup lang="ts">
import type { PropType } from 'vue';
import type { FlashMessages } from '@/types';

defineProps({
    flash: {
        type: Object as PropType<FlashMessages>,
        required: true,
    },
});
</script>

<template>
    <div v-if="flash.success" class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
        {{ flash.success }}
    </div>
    <div v-if="flash.error" class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
        {{ flash.error }}
    </div>
    <div v-if="flash.warning" class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded mb-4">
        {{ flash.warning }}
    </div>
    <div v-if="flash.info" class="bg-blue-100 border border-blue-400 text-blue-700 px-4 py-3 rounded mb-4">
        {{ flash.info }}
    </div>
</template>
