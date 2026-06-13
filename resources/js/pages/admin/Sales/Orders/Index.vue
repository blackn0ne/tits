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

interface OrderRow {
    id: number;
    client_name: string;
    client_phone: string | null;
    status: string;
    status_label: string;
    total: number;
    ordered_at: string;
    items_count: number;
}

defineProps<{
    orders: Paginated<OrderRow>;
}>();

const { t } = useI18n();

const breadcrumbs = computed<BreadcrumbItem[]>(() => [
    { title: t('nav.sales'), href: route('admin.sales.orders.index') },
    { title: t('admin.sales.orders.title'), href: route('admin.sales.orders.index') },
]);

const formatMoney = (value: number) =>
    new Intl.NumberFormat(undefined, { minimumFractionDigits: 0, maximumFractionDigits: 2 }).format(value);
</script>

<template>
    <SeoHead :title="t('admin.sales.orders.title')" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex flex-col gap-3 px-6 py-3">
            <div class="flex items-start justify-between gap-4">
                <Heading :title="t('admin.sales.orders.title')" :description="t('admin.sales.orders.description')" />
                <Button as-child>
                    <Link :href="route('admin.sales.orders.create')">
                        <Plus class="size-4" />
                        {{ t('admin.sales.orders.create') }}
                    </Link>
                </Button>
            </div>

            <div class="overflow-hidden rounded-xl border bg-card">
                <table class="w-full text-sm">
                    <thead class="border-b bg-muted/50">
                        <tr>
                            <th class="px-4 py-3 text-left font-medium">{{ t('admin.sales.orders.client') }}</th>
                            <th class="px-4 py-3 text-left font-medium">{{ t('admin.sales.orders.date') }}</th>
                            <th class="px-4 py-3 text-left font-medium">{{ t('admin.sales.orders.items_count') }}</th>
                            <th class="px-4 py-3 text-left font-medium">{{ t('admin.sales.orders.total') }}</th>
                            <th class="px-4 py-3 text-left font-medium">{{ t('admin.sales.orders.status') }}</th>
                            <th class="px-4 py-3 text-right font-medium">{{ t('admin.sales.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="order in orders.data" :key="order.id" class="border-b last:border-0">
                            <td class="px-4 py-3">
                                <div>{{ order.client_name }}</div>
                                <div v-if="order.client_phone" class="text-xs text-muted-foreground">{{ order.client_phone }}</div>
                            </td>
                            <td class="px-4 py-3">{{ order.ordered_at }}</td>
                            <td class="px-4 py-3">{{ order.items_count }}</td>
                            <td class="px-4 py-3">{{ formatMoney(order.total) }}</td>
                            <td class="px-4 py-3">
                                <span
                                    class="inline-flex rounded-full px-2 py-0.5 text-xs"
                                    :class="order.status === 'completed' ? 'bg-green-100 text-green-700' : 'bg-muted text-muted-foreground'"
                                >
                                    {{ order.status_label }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex justify-end gap-2">
                                    <Button as-child variant="outline" size="icon" class="size-8">
                                        <Link :href="route('admin.sales.orders.edit', order.id)">
                                            <Pencil class="size-4" />
                                        </Link>
                                    </Button>
                                    <Button as-child variant="outline" size="icon" class="size-8">
                                        <Link :href="route('admin.sales.orders.destroy', order.id)" method="delete" as="button">
                                            <Trash2 class="size-4" />
                                        </Link>
                                    </Button>
                                </div>
                            </td>
                        </tr>
                        <tr v-if="orders.data.length === 0">
                            <td colspan="6" class="px-4 py-8 text-center text-muted-foreground">{{ t('admin.sales.orders.empty') }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <DataPagination :meta="orders" />
        </div>
    </AppLayout>
</template>
