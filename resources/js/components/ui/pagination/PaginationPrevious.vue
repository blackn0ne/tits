<script setup lang="ts">
import PaginationLink from './PaginationLink.vue';
import { cn } from '@/lib/utils';
import { ChevronLeft } from 'lucide-vue-next';
import type { HTMLAttributes } from 'vue';

const props = withDefaults(
    defineProps<{
        class?: HTMLAttributes['class'];
        disabled?: boolean;
    }>(),
    {
        disabled: false,
    },
);
</script>

<template>
    <PaginationLink
        aria-label="Go to previous page"
        size="default"
        :class="cn('gap-1 px-2.5 sm:pl-2.5', props.class)"
        :disabled="disabled"
    >
        <ChevronLeft class="size-4" />
        <span class="hidden sm:block"><slot>Previous</slot></span>
    </PaginationLink>
</template>
