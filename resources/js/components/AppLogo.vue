<script setup lang="ts">
import { useSite } from '@/composables/useSite';
import { cn } from '@/lib/utils';

interface Props {
    class?: string;
}

const props = defineProps<Props>();

const { site } = useSite();
</script>

<template>
    <span :class="cn('truncate font-semibold text-sm leading-tight', props.class)">{{ site.name }}</span>
</template>
