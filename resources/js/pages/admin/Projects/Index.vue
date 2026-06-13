<script setup lang="ts">
import DataPagination from '@/components/DataPagination.vue';
import Heading from '@/components/Heading.vue';
import SeoHead from '@/components/SeoHead.vue';
import { Button } from '@/components/ui/button';
import { useI18n } from '@/composables/useI18n';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem, type Paginated } from '@/types';
import { Link } from '@inertiajs/vue3';
import { Pencil, Plus, Trash2 } from 'lucide-vue-next';
import { computed } from 'vue';

interface ProjectRow {
    id: number;
    title: string;
    slug: string | null;
    status: string;
    status_label: string;
    published_at: string | null;
    category_name: string | null;
    banner_url: string | null;
}

defineProps<{
    projects: Paginated<ProjectRow>;
}>();

const { t } = useI18n();

const breadcrumbs = computed<BreadcrumbItem[]>(() => [
    { title: t('nav.works'), href: route('admin.projects.index') },
    { title: t('admin.projects.title'), href: route('admin.projects.index') },
]);
</script>

<template>
    <SeoHead :title="t('admin.projects.title')" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex flex-col gap-3 px-6 py-3">
            <div class="flex items-start justify-between gap-4">
                <Heading :title="t('admin.projects.title')" :description="t('admin.projects.description')" />
                <Button as-child>
                    <Link :href="route('admin.projects.create')">
                        <Plus class="size-4" />
                        {{ t('admin.projects.create') }}
                    </Link>
                </Button>
            </div>

            <div class="overflow-hidden rounded-xl border bg-card">
                <table class="w-full text-sm">
                    <thead class="border-b bg-muted/50">
                        <tr>
                            <th class="px-4 py-3 text-left font-medium">{{ t('admin.projects.banner') }}</th>
                            <th class="px-4 py-3 text-left font-medium">{{ t('admin.projects.project_title') }}</th>
                            <th class="px-4 py-3 text-left font-medium">{{ t('admin.project_categories.name') }}</th>
                            <th class="px-4 py-3 text-left font-medium">{{ t('admin.projects.date') }}</th>
                            <th class="px-4 py-3 text-left font-medium">{{ t('admin.projects.status') }}</th>
                            <th class="px-4 py-3 text-right font-medium">{{ t('admin.project_categories.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="project in projects.data" :key="project.id" class="border-b last:border-0">
                            <td class="px-4 py-3">
                                <img v-if="project.banner_url" :src="project.banner_url" alt="" class="size-10 rounded object-cover" />
                                <span v-else class="text-muted-foreground">—</span>
                            </td>
                            <td class="px-4 py-3">
                                <div>{{ project.title }}</div>
                                <div class="font-mono text-xs text-muted-foreground">{{ project.slug }}</div>
                            </td>
                            <td class="px-4 py-3">{{ project.category_name ?? '—' }}</td>
                            <td class="px-4 py-3">{{ project.published_at ?? '—' }}</td>
                            <td class="px-4 py-3">
                                <span
                                    class="inline-flex rounded-full px-2 py-0.5 text-xs"
                                    :class="project.status === 'published' ? 'bg-green-100 text-green-700' : 'bg-muted text-muted-foreground'"
                                >
                                    {{ project.status_label }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex justify-end gap-2">
                                    <Button as-child variant="outline" size="icon" class="size-8">
                                        <Link :href="route('admin.projects.edit', project.id)">
                                            <Pencil class="size-4" />
                                        </Link>
                                    </Button>
                                    <Button as-child variant="outline" size="icon" class="size-8">
                                        <Link :href="route('admin.projects.destroy', project.id)" method="delete" as="button">
                                            <Trash2 class="size-4" />
                                        </Link>
                                    </Button>
                                </div>
                            </td>
                        </tr>
                        <tr v-if="projects.data.length === 0">
                            <td colspan="6" class="px-4 py-8 text-center text-muted-foreground">{{ t('admin.projects.empty') }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <DataPagination :meta="projects" />
        </div>
    </AppLayout>
</template>
