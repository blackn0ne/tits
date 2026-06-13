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

interface PostRow {
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
    posts: Paginated<PostRow>;
}>();

const { t } = useI18n();

const breadcrumbs = computed<BreadcrumbItem[]>(() => [
    { title: t('nav.blog'), href: route('admin.blog-posts.index') },
    { title: t('admin.blog.title'), href: route('admin.blog-posts.index') },
]);
</script>

<template>
    <SeoHead :title="t('admin.blog.title')" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex flex-col gap-3 px-6 py-3">
            <div class="flex items-start justify-between gap-4">
                <Heading :title="t('admin.blog.title')" :description="t('admin.blog.description')" />
                <Button as-child>
                    <Link :href="route('admin.blog-posts.create')">
                        <Plus class="size-4" />
                        {{ t('admin.blog.create') }}
                    </Link>
                </Button>
            </div>

            <div class="overflow-hidden rounded-xl border bg-card">
                <table class="w-full text-sm">
                    <thead class="border-b bg-muted/50">
                        <tr>
                            <th class="px-4 py-3 text-left font-medium">{{ t('admin.blog.banner') }}</th>
                            <th class="px-4 py-3 text-left font-medium">{{ t('admin.blog.post_title') }}</th>
                            <th class="px-4 py-3 text-left font-medium">{{ t('admin.categories.name') }}</th>
                            <th class="px-4 py-3 text-left font-medium">{{ t('admin.blog.date') }}</th>
                            <th class="px-4 py-3 text-left font-medium">{{ t('admin.blog.status') }}</th>
                            <th class="px-4 py-3 text-right font-medium">{{ t('admin.categories.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="post in posts.data" :key="post.id" class="border-b last:border-0">
                            <td class="px-4 py-3">
                                <img v-if="post.banner_url" :src="post.banner_url" alt="" class="size-10 rounded object-cover" />
                                <span v-else class="text-muted-foreground">—</span>
                            </td>
                            <td class="px-4 py-3">
                                <div>{{ post.title }}</div>
                                <div class="font-mono text-xs text-muted-foreground">{{ post.slug }}</div>
                            </td>
                            <td class="px-4 py-3">{{ post.category_name ?? '—' }}</td>
                            <td class="px-4 py-3">{{ post.published_at ?? '—' }}</td>
                            <td class="px-4 py-3">
                                <span
                                    class="inline-flex rounded-full px-2 py-0.5 text-xs"
                                    :class="post.status === 'published' ? 'bg-green-100 text-green-700' : 'bg-muted text-muted-foreground'"
                                >
                                    {{ post.status_label }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex justify-end gap-2">
                                    <Button as-child variant="outline" size="icon" class="size-8">
                                        <Link :href="route('admin.blog-posts.edit', post.id)">
                                            <Pencil class="size-4" />
                                        </Link>
                                    </Button>
                                    <Button as-child variant="outline" size="icon" class="size-8">
                                        <Link :href="route('admin.blog-posts.destroy', post.id)" method="delete" as="button">
                                            <Trash2 class="size-4" />
                                        </Link>
                                    </Button>
                                </div>
                            </td>
                        </tr>
                        <tr v-if="posts.data.length === 0">
                            <td colspan="6" class="px-4 py-8 text-center text-muted-foreground">{{ t('admin.blog.empty') }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <DataPagination :meta="posts" />
        </div>
    </AppLayout>
</template>
