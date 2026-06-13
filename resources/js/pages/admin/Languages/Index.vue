<script setup lang="ts">
import Heading from '@/components/Heading.vue';
import SeoHead from '@/components/SeoHead.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { useI18n } from '@/composables/useI18n';
import { type BreadcrumbItem } from '@/types';
import { computed } from 'vue';

interface LanguageRow {
    id: number;
    name: string;
    code: string;
}

defineProps<{
    languages: LanguageRow[];
}>();

const { t } = useI18n();

const breadcrumbs = computed<BreadcrumbItem[]>(() => [
    {
        title: t('admin.languages.title'),
        href: route('admin.languages.index'),
    },
]);
</script>

<template>
    <SeoHead :title="t('admin.languages.title')" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex flex-col gap-3 px-6 py-3">
            <Heading :title="t('admin.languages.title')" :description="t('admin.languages.description')" />

            <div class="overflow-hidden rounded-xl border bg-card">
                <table class="w-full text-sm">
                    <thead class="border-b bg-muted/50">
                        <tr>
                            <th class="px-4 py-3 text-left font-medium">{{ t('admin.languages.name') }}</th>
                            <th class="px-4 py-3 text-left font-medium">{{ t('admin.languages.code') }}</th>
                            <th class="px-4 py-3 text-left font-medium">{{ t('admin.languages.file') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="language in languages" :key="language.id" class="border-b last:border-0">
                            <td class="px-4 py-3">{{ language.name }}</td>
                            <td class="px-4 py-3 font-mono text-xs">{{ language.code }}</td>
                            <td class="px-4 py-3 font-mono text-xs text-muted-foreground">lang/{{ language.code }}.json</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <p class="text-sm text-muted-foreground">
                {{ t('admin.languages.file_hint') }}
                <code class="rounded bg-muted px-1.5 py-0.5 text-xs">lang/{code}.json</code>
            </p>
        </div>
    </AppLayout>
</template>
