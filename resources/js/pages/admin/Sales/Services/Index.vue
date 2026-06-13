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

interface ServiceRow {
    id: number;
    name: string;
    price: number;
    status: string;
    status_label: string;
}

defineProps<{
    services: Paginated<ServiceRow>;
}>();

const { t } = useI18n();

const breadcrumbs = computed<BreadcrumbItem[]>(() => [
    { title: t('nav.sales'), href: route('admin.sales.services.index') },
    { title: t('admin.sales.services.title'), href: route('admin.sales.services.index') },
]);

const formatMoney = (value: number) =>
    new Intl.NumberFormat(undefined, { minimumFractionDigits: 0, maximumFractionDigits: 2 }).format(value);
</script>

<template>
    <SeoHead :title="t('admin.sales.services.title')" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex flex-col gap-3 px-6 py-3">
            <div class="flex items-start justify-between gap-4">
                <Heading :title="t('admin.sales.services.title')" :description="t('admin.sales.services.description')" />
                <Button as-child>
                    <Link :href="route('admin.sales.services.create')">
                        <Plus class="size-4" />
                        {{ t('admin.sales.services.create') }}
                    </Link>
                </Button>
            </div>

            <div class="overflow-hidden rounded-xl border bg-card">
                <table class="w-full text-sm">
                    <thead class="border-b bg-muted/50">
                        <tr>
                            <th class="px-4 py-3 text-left font-medium">{{ t('admin.sales.services.name') }}</th>
                            <th class="px-4 py-3 text-left font-medium">{{ t('admin.sales.services.price') }}</th>
                            <th class="px-4 py-3 text-left font-medium">{{ t('admin.sales.services.status') }}</th>
                            <th class="px-4 py-3 text-right font-medium">{{ t('admin.sales.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="service in services.data" :key="service.id" class="border-b last:border-0">
                            <td class="px-4 py-3">{{ service.name }}</td>
                            <td class="px-4 py-3">{{ formatMoney(service.price) }}</td>
                            <td class="px-4 py-3">
                                <span
                                    class="inline-flex rounded-full px-2 py-0.5 text-xs"
                                    :class="service.status === 'active' ? 'bg-green-100 text-green-700' : 'bg-muted text-muted-foreground'"
                                >
                                    {{ service.status_label }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex justify-end gap-2">
                                    <Button as-child variant="outline" size="icon" class="size-8">
                                        <Link :href="route('admin.sales.services.edit', service.id)">
                                            <Pencil class="size-4" />
                                        </Link>
                                    </Button>
                                    <Button as-child variant="outline" size="icon" class="size-8">
                                        <Link :href="route('admin.sales.services.destroy', service.id)" method="delete" as="button">
                                            <Trash2 class="size-4" />
                                        </Link>
                                    </Button>
                                </div>
                            </td>
                        </tr>
                        <tr v-if="services.data.length === 0">
                            <td colspan="4" class="px-4 py-8 text-center text-muted-foreground">{{ t('admin.sales.services.empty') }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <DataPagination :meta="services" />
        </div>
    </AppLayout>
</template>
