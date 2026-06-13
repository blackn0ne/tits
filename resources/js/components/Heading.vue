<script setup lang="ts">
import { Separator } from '@/components/ui/separator';

interface Props {
    title: string;
    description?: string;
    withDivider?: boolean;
}

withDefaults(defineProps<Props>(), {
    withDivider: false,
});
</script>

<template>
    <div class="space-y-0.5">
        <h2 class="text-lg font-semibold tracking-tight">{{ title }}</h2>
        <p v-if="description" class="text-sm text-muted-foreground">
            {{ description }}
        </p>
    </div>
    <Separator v-if="withDivider" class="my-3" />
</template>
