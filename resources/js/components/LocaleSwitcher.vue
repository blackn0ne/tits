<script setup lang="ts">
import { Button } from '@/components/ui/button';
import type { SharedData } from '@/types';
import { Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const page = usePage<SharedData>();

const languages = computed(() => page.props.languages ?? []);
const currentLocale = computed(() => page.props.locale);
</script>

<template>
    <div class="inline-flex items-center gap-1 rounded-lg border border-border bg-background p-1">
        <Button
            v-for="language in languages"
            :key="language.code"
            variant="ghost"
            size="sm"
            :class="[
                'h-8 px-3 text-xs font-medium',
                currentLocale === language.code ? 'bg-muted text-foreground' : 'text-muted-foreground',
            ]"
            as-child
        >
            <Link :href="route('locale.update', { locale: language.code })" method="post" as="button">
                {{ language.name }}
            </Link>
        </Button>
    </div>
</template>
