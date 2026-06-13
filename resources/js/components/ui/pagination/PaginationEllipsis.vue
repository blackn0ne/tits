<script setup lang="ts">
import { cn } from '@/lib/utils';
import { MoreHorizontal } from 'lucide-vue-next';
import type { HTMLAttributes } from 'vue';

const props = defineProps<{
    class?: HTMLAttributes['class'];
}>();
</script>

<template>
    <span aria-hidden class="flex size-9 items-center justify-center" :class="cn(props.class)">
        <MoreHorizontal class="size-4" />
        <span class="sr-only">More pages</span>
    </span>
</template>
