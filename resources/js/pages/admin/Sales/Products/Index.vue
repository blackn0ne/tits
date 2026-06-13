<script setup lang="ts">
import DataPagination from '@/components/DataPagination.vue';
import Heading from '@/components/Heading.vue';
import SeoHead from '@/components/SeoHead.vue';
import { Button } from '@/components/ui/button';
import { useI18n } from '@/composables/useI18n';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem, type Paginated } from '@/types';
import { Link } from '@inertiajs/vue3';
import { ExternalLink, Pencil, Plus, Trash2 } from 'lucide-vue-next';
import { computed } from 'vue';

interface ProductRow {
    id: number;
    name: string;
    price: number;
    quantity: number;
    unit: string;
    status: string;
    status_label: string;
    kaspi_link: string | null;
    image_url: string | null;
}

defineProps<{
    products: Paginated<ProductRow>;
}>();

const { t } = useI18n();

const breadcrumbs = computed<BreadcrumbItem[]>(() => [
    { title: t('nav.sales'), href: route('admin.sales.products.index') },
    { title: t('admin.sales.products.title'), href: route('admin.sales.products.index') },
]);

const formatMoney = (value: number) =>
    new Intl.NumberFormat(undefined, { minimumFractionDigits: 0, maximumFractionDigits: 2 }).format(value);
</script>

<template>
    <SeoHead :title="t('admin.sales.products.title')" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex flex-col gap-3 px-6 py-3">
            <div class="flex items-start justify-between gap-4">
                <Heading :title="t('admin.sales.products.title')" :description="t('admin.sales.products.description')" />
                <Button as-child>
                    <Link :href="route('admin.sales.products.create')">
                        <Plus class="size-4" />
                        {{ t('admin.sales.products.create') }}
                    </Link>
                </Button>
            </div>

            <div class="overflow-hidden rounded-xl border bg-card">
                <table class="w-full text-sm">
                    <thead class="border-b bg-muted/50">
                        <tr>
                            <th class="px-4 py-3 text-left font-medium">{{ t('admin.sales.products.image') }}</th>
                            <th class="px-4 py-3 text-left font-medium">{{ t('admin.sales.products.name') }}</th>
                            <th class="px-4 py-3 text-left font-medium">{{ t('admin.sales.products.price') }}</th>
                            <th class="px-4 py-3 text-left font-medium">{{ t('admin.sales.products.quantity') }}</th>
                            <th class="px-4 py-3 text-left font-medium">{{ t('admin.sales.products.status') }}</th>
                            <th class="px-4 py-3 text-left font-medium">{{ t('admin.sales.products.kaspi_link') }}</th>
                            <th class="px-4 py-3 text-right font-medium">{{ t('admin.sales.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="product in products.data" :key="product.id" class="border-b last:border-0">
                            <td class="px-4 py-3">
                                <img v-if="product.image_url" :src="product.image_url" alt="" class="size-10 rounded object-cover" />
                                <span v-else class="text-muted-foreground">—</span>
                            </td>
                            <td class="px-4 py-3">{{ product.name }}</td>
                            <td class="px-4 py-3">{{ formatMoney(product.price) }}</td>
                            <td class="px-4 py-3">{{ product.quantity }} {{ product.unit }}</td>
                            <td class="px-4 py-3">
                                <span
                                    class="inline-flex rounded-full px-2 py-0.5 text-xs"
                                    :class="product.status === 'active' ? 'bg-green-100 text-green-700' : 'bg-muted text-muted-foreground'"
                                >
                                    {{ product.status_label }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <a
                                    v-if="product.kaspi_link"
                                    :href="product.kaspi_link"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    class="inline-flex items-center gap-1 text-primary hover:underline"
                                >
                                    <ExternalLink class="size-3.5" />
                                    {{ t('admin.sales.products.open_kaspi') }}
                                </a>
                                <span v-else class="text-muted-foreground">—</span>
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex justify-end gap-2">
                                    <Button as-child variant="outline" size="icon" class="size-8">
                                        <Link :href="route('admin.sales.products.edit', product.id)">
                                            <Pencil class="size-4" />
                                        </Link>
                                    </Button>
                                    <Button as-child variant="outline" size="icon" class="size-8">
                                        <Link :href="route('admin.sales.products.destroy', product.id)" method="delete" as="button">
                                            <Trash2 class="size-4" />
                                        </Link>
                                    </Button>
                                </div>
                            </td>
                        </tr>
                        <tr v-if="products.data.length === 0">
                            <td colspan="7" class="px-4 py-8 text-center text-muted-foreground">{{ t('admin.sales.products.empty') }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <DataPagination :meta="products" />
        </div>
    </AppLayout>
</template>
