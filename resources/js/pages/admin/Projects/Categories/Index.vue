<script setup lang="ts">
import DataPagination from '@/components/DataPagination.vue';
import Heading from '@/components/Heading.vue';
import SeoHead from '@/components/SeoHead.vue';
import { Button } from '@/components/ui/button';
import { useI18n } from '@/composables/useI18n';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem, type Paginated } from '@/types';
import * as LucideIcons from 'lucide-vue-next';
import { Link } from '@inertiajs/vue3';
import { Pencil, Plus, Trash2 } from 'lucide-vue-next';
import { computed } from 'vue';

interface CategoryRow {
    id: number;
    icon: string;
    is_active: boolean;
    sort_order: number;
    name: string;
    slug: string | null;
}

defineProps<{
    categories: Paginated<CategoryRow>;
}>();

const { t } = useI18n();

const breadcrumbs = computed<BreadcrumbItem[]>(() => [
    { title: t('nav.works'), href: route('admin.projects.index') },
    { title: t('admin.project_categories.title'), href: route('admin.project-categories.index') },
]);

const resolveIcon = (name: string) => {
    return (LucideIcons as Record<string, unknown>)[name] as typeof LucideIcons.Folder | undefined;
};
</script>

<template>
    <SeoHead :title="t('admin.project_categories.title')" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex flex-col gap-3 px-6 py-3">
            <div class="flex items-start justify-between gap-4">
                <Heading :title="t('admin.project_categories.title')" :description="t('admin.project_categories.description')" />
                <Button as-child>
                    <Link :href="route('admin.project-categories.create')">
                        <Plus class="size-4" />
                        {{ t('admin.project_categories.create') }}
                    </Link>
                </Button>
            </div>

            <div class="overflow-hidden rounded-xl border bg-card">
                <table class="w-full text-sm">
                    <thead class="border-b bg-muted/50">
                        <tr>
                            <th class="px-4 py-3 text-left font-medium">{{ t('admin.project_categories.icon') }}</th>
                            <th class="px-4 py-3 text-left font-medium">{{ t('admin.project_categories.name') }}</th>
                            <th class="px-4 py-3 text-left font-medium">{{ t('admin.project_categories.slug') }}</th>
                            <th class="px-4 py-3 text-left font-medium">{{ t('admin.project_categories.status') }}</th>
                            <th class="px-4 py-3 text-right font-medium">{{ t('admin.project_categories.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="category in categories.data" :key="category.id" class="border-b last:border-0">
                            <td class="px-4 py-3">
                                <component :is="resolveIcon(category.icon)" class="size-4" />
                            </td>
                            <td class="px-4 py-3">{{ category.name }}</td>
                            <td class="px-4 py-3 font-mono text-xs text-muted-foreground">{{ category.slug }}</td>
                            <td class="px-4 py-3">
                                <span
                                    class="inline-flex rounded-full px-2 py-0.5 text-xs"
                                    :class="category.is_active ? 'bg-green-100 text-green-700' : 'bg-muted text-muted-foreground'"
                                >
                                    {{ category.is_active ? t('admin.project_categories.active') : t('admin.project_categories.inactive') }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex justify-end gap-2">
                                    <Button as-child variant="outline" size="icon" class="size-8">
                                        <Link :href="route('admin.project-categories.edit', category.id)">
                                            <Pencil class="size-4" />
                                        </Link>
                                    </Button>
                                    <Button as-child variant="outline" size="icon" class="size-8">
                                        <Link :href="route('admin.project-categories.destroy', category.id)" method="delete" as="button">
                                            <Trash2 class="size-4" />
                                        </Link>
                                    </Button>
                                </div>
                            </td>
                        </tr>
                        <tr v-if="categories.data.length === 0">
                            <td colspan="5" class="px-4 py-8 text-center text-muted-foreground">{{ t('admin.project_categories.empty') }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <DataPagination :meta="categories" />
        </div>
    </AppLayout>
</template>
