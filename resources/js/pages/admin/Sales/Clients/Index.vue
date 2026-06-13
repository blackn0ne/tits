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

interface ClientRow {
    id: number;
    full_name: string;
    phone: string;
    orders_count: number;
}

defineProps<{
    clients: Paginated<ClientRow>;
}>();

const { t } = useI18n();

const breadcrumbs = computed<BreadcrumbItem[]>(() => [
    { title: t('nav.sales'), href: route('admin.sales.clients.index') },
    { title: t('admin.sales.clients.title'), href: route('admin.sales.clients.index') },
]);
</script>

<template>
    <SeoHead :title="t('admin.sales.clients.title')" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex flex-col gap-3 px-6 py-3">
            <div class="flex items-start justify-between gap-4">
                <Heading :title="t('admin.sales.clients.title')" :description="t('admin.sales.clients.description')" />
                <Button as-child>
                    <Link :href="route('admin.sales.clients.create')">
                        <Plus class="size-4" />
                        {{ t('admin.sales.clients.create') }}
                    </Link>
                </Button>
            </div>

            <div class="overflow-hidden rounded-xl border bg-card">
                <table class="w-full text-sm">
                    <thead class="border-b bg-muted/50">
                        <tr>
                            <th class="px-4 py-3 text-left font-medium">{{ t('admin.sales.clients.full_name') }}</th>
                            <th class="px-4 py-3 text-left font-medium">{{ t('admin.sales.clients.phone') }}</th>
                            <th class="px-4 py-3 text-left font-medium">{{ t('admin.sales.clients.orders_count') }}</th>
                            <th class="px-4 py-3 text-right font-medium">{{ t('admin.sales.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="client in clients.data" :key="client.id" class="border-b last:border-0">
                            <td class="px-4 py-3">{{ client.full_name }}</td>
                            <td class="px-4 py-3">{{ client.phone }}</td>
                            <td class="px-4 py-3">{{ client.orders_count }}</td>
                            <td class="px-4 py-3">
                                <div class="flex justify-end gap-2">
                                    <Button as-child variant="outline" size="icon" class="size-8">
                                        <Link :href="route('admin.sales.clients.edit', client.id)">
                                            <Pencil class="size-4" />
                                        </Link>
                                    </Button>
                                    <Button as-child variant="outline" size="icon" class="size-8">
                                        <Link :href="route('admin.sales.clients.destroy', client.id)" method="delete" as="button">
                                            <Trash2 class="size-4" />
                                        </Link>
                                    </Button>
                                </div>
                            </td>
                        </tr>
                        <tr v-if="clients.data.length === 0">
                            <td colspan="4" class="px-4 py-8 text-center text-muted-foreground">{{ t('admin.sales.clients.empty') }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <DataPagination :meta="clients" />
        </div>
    </AppLayout>
</template>
