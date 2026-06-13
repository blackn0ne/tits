<script setup lang="ts">
import PaginationLink from './PaginationLink.vue';
import { cn } from '@/lib/utils';
import { ChevronRight } from 'lucide-vue-next';
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
        aria-label="Go to next page"
        size="default"
        :class="cn('gap-1 px-2.5 sm:pr-2.5', props.class)"
        :disabled="disabled"
    >
        <span class="hidden sm:block"><slot>Next</slot></span>
        <ChevronRight class="size-4" />
    </PaginationLink>
</template>
