<script setup lang="ts">
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import SeoHead from '@/components/SeoHead.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { useI18n } from '@/composables/useI18n';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { router, useForm } from '@inertiajs/vue3';
import { Plus, Trash2, X } from 'lucide-vue-next';
import { computed, ref } from 'vue';

interface ClientOption {
    id: number;
    full_name: string;
    phone: string;
    is_store: boolean;
}

interface CatalogProduct {
    id: number;
    name: string;
    price: number;
    unit: string;
    status: string;
}

interface CatalogService {
    id: number;
    name: string;
    price: number;
    status: string;
}

interface StatusOption {
    value: string;
    label: string;
}

interface OrderItemForm {
    item_type: 'product' | 'service';
    sales_product_id: number | null;
    sales_service_id: number | null;
    name: string;
    quantity: number;
    unit: string | null;
    unit_price: number;
}

interface OrderForm {
    id: number;
    sales_client_id: number;
    status: string;
    ordered_at: string;
    notes: string | null;
    total: number;
    items: OrderItemForm[];
}

const props = defineProps<{
    order: OrderForm | null;
    default_client_id: number;
    clients: ClientOption[];
    products: CatalogProduct[];
    services: CatalogService[];
    statuses: StatusOption[];
}>();

const { t } = useI18n();
const isEditing = computed(() => props.order !== null);
const catalogOpen = ref(false);
const catalogTab = ref<'products' | 'services'>('products');

const defaultOrderedAt = () => {
    const now = new Date();
    const offset = now.getTimezoneOffset();
    const local = new Date(now.getTime() - offset * 60 * 1000);
    return local.toISOString().slice(0, 16);
};

const breadcrumbs = computed<BreadcrumbItem[]>(() => [
    { title: t('nav.sales'), href: route('admin.sales.orders.index') },
    { title: t('admin.sales.orders.title'), href: route('admin.sales.orders.index') },
    {
        title: isEditing.value ? t('admin.sales.orders.edit') : t('admin.sales.orders.create'),
        href: isEditing.value ? route('admin.sales.orders.edit', props.order!.id) : route('admin.sales.orders.create'),
    },
]);

const form = useForm({
    sales_client_id: props.order?.sales_client_id ?? props.default_client_id,
    status: props.order?.status ?? props.statuses[0]?.value ?? 'completed',
    ordered_at: props.order?.ordered_at ?? defaultOrderedAt(),
    notes: props.order?.notes ?? '',
    items: (props.order?.items ?? []).map((item) => ({
        item_type: item.item_type,
        sales_product_id: item.sales_product_id,
        sales_service_id: item.sales_service_id,
        name: item.name,
        quantity: item.quantity,
        unit: item.unit,
        unit_price: item.unit_price,
    })),
});

const lineTotal = (item: OrderItemForm) => Math.round(item.quantity * item.unit_price * 100) / 100;

const orderTotal = computed(() => form.items.reduce((sum, item) => sum + lineTotal(item), 0));

const formatMoney = (value: number) =>
    new Intl.NumberFormat(undefined, { minimumFractionDigits: 0, maximumFractionDigits: 2 }).format(value);

const addProduct = (product: CatalogProduct) => {
    form.items.push({
        item_type: 'product',
        sales_product_id: product.id,
        sales_service_id: null,
        name: product.name,
        quantity: 1,
        unit: product.unit,
        unit_price: product.price,
    });
    catalogOpen.value = false;
};

const addService = (service: CatalogService) => {
    form.items.push({
        item_type: 'service',
        sales_product_id: null,
        sales_service_id: service.id,
        name: service.name,
        quantity: 1,
        unit: null,
        unit_price: service.price,
    });
    catalogOpen.value = false;
};

const removeItem = (index: number) => {
    form.items.splice(index, 1);
};

const submit = () => {
    if (isEditing.value) {
        form.put(route('admin.sales.orders.update', props.order!.id));
        return;
    }

    form.post(route('admin.sales.orders.store'));
};
</script>

<template>
    <SeoHead :title="isEditing ? t('admin.sales.orders.edit') : t('admin.sales.orders.create')" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex flex-col gap-3 px-6 py-3 pb-20">
            <Heading
                :title="isEditing ? t('admin.sales.orders.edit') : t('admin.sales.orders.create')"
                :description="t('admin.sales.orders.form_description')"
            />

            <form @submit.prevent="submit" class="w-full max-w-5xl space-y-6">
                <section class="rounded-xl border bg-card p-6 shadow-sm">
                    <div class="grid gap-4 md:grid-cols-2">
                        <div class="grid gap-2">
                            <Label for="sales_client_id">{{ t('admin.sales.orders.client') }}</Label>
                            <select
                                id="sales_client_id"
                                v-model="form.sales_client_id"
                                class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm"
                                required
                            >
                                <option v-for="client in clients" :key="client.id" :value="client.id">
                                    {{ client.is_store ? client.full_name : `${client.full_name} (${client.phone})` }}
                                </option>
                            </select>
                            <InputError :message="form.errors.sales_client_id" />
                        </div>

                        <div class="grid gap-2">
                            <Label for="ordered_at">{{ t('admin.sales.orders.date') }}</Label>
                            <Input id="ordered_at" v-model="form.ordered_at" type="datetime-local" required />
                            <InputError :message="form.errors.ordered_at" />
                        </div>

                        <div class="grid gap-2">
                            <Label for="status">{{ t('admin.sales.orders.status') }}</Label>
                            <select
                                id="status"
                                v-model="form.status"
                                class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm"
                            >
                                <option v-for="status in statuses" :key="status.value" :value="status.value">{{ status.label }}</option>
                            </select>
                            <InputError :message="form.errors.status" />
                        </div>

                        <div class="grid gap-2 md:col-span-2">
                            <Label for="notes">{{ t('admin.sales.orders.notes') }}</Label>
                            <textarea
                                id="notes"
                                v-model="form.notes"
                                rows="3"
                                class="flex min-h-20 w-full rounded-md border border-input bg-background px-3 py-2 text-sm"
                            />
                            <InputError :message="form.errors.notes" />
                        </div>
                    </div>
                </section>

                <section class="rounded-xl border bg-card p-6 shadow-sm">
                    <div class="flex items-center justify-between gap-4">
                        <h2 class="text-lg font-semibold">{{ t('admin.sales.orders.items') }}</h2>
                        <Button type="button" variant="outline" size="sm" @click="catalogOpen = true">
                            <Plus class="size-4" />
                            {{ t('admin.sales.orders.add_item') }}
                        </Button>
                    </div>

                    <InputError class="mt-2" :message="form.errors.items" />

                    <div class="mt-4 overflow-hidden rounded-lg border">
                        <table class="w-full text-sm">
                            <thead class="border-b bg-muted/50">
                                <tr>
                                    <th class="px-3 py-2 text-left font-medium">{{ t('admin.sales.orders.item_name') }}</th>
                                    <th class="px-3 py-2 text-left font-medium">{{ t('admin.sales.orders.item_quantity') }}</th>
                                    <th class="px-3 py-2 text-left font-medium">{{ t('admin.sales.orders.item_unit_price') }}</th>
                                    <th class="px-3 py-2 text-left font-medium">{{ t('admin.sales.orders.item_line_total') }}</th>
                                    <th class="px-3 py-2 text-right font-medium">{{ t('admin.sales.actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(item, index) in form.items" :key="index" class="border-b last:border-0">
                                    <td class="px-3 py-2">
                                        <Input v-model="item.name" class="h-8" required />
                                        <InputError :message="form.errors[`items.${index}.name`]" />
                                    </td>
                                    <td class="px-3 py-2">
                                        <Input v-model.number="item.quantity" type="number" min="0.01" step="0.01" class="h-8 w-24" required />
                                        <InputError :message="form.errors[`items.${index}.quantity`]" />
                                    </td>
                                    <td class="px-3 py-2">
                                        <Input v-model.number="item.unit_price" type="number" min="0" step="0.01" class="h-8 w-28" required />
                                        <InputError :message="form.errors[`items.${index}.unit_price`]" />
                                    </td>
                                    <td class="px-3 py-2 font-medium">{{ formatMoney(lineTotal(item)) }}</td>
                                    <td class="px-3 py-2">
                                        <div class="flex justify-end">
                                            <Button type="button" variant="outline" size="icon" class="size-8" @click="removeItem(index)">
                                                <Trash2 class="size-4" />
                                            </Button>
                                        </div>
                                    </td>
                                </tr>
                                <tr v-if="form.items.length === 0">
                                    <td colspan="5" class="px-3 py-6 text-center text-muted-foreground">{{ t('admin.sales.orders.no_items') }}</td>
                                </tr>
                            </tbody>
                            <tfoot v-if="form.items.length > 0" class="border-t bg-muted/30">
                                <tr>
                                    <td colspan="3" class="px-3 py-3 text-right font-medium">{{ t('admin.sales.orders.total') }}</td>
                                    <td class="px-3 py-3 font-semibold">{{ formatMoney(orderTotal) }}</td>
                                    <td />
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </section>

                <div class="flex justify-end gap-2">
                    <Button type="button" variant="outline" @click="router.visit(route('admin.sales.orders.index'))">
                        {{ t('admin.sales.cancel') }}
                    </Button>
                    <Button type="submit" :disabled="form.processing">{{ t('admin.sales.save') }}</Button>
                </div>
            </form>
        </div>

        <div v-if="catalogOpen" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4" @click.self="catalogOpen = false">
            <div class="flex max-h-[80vh] w-full max-w-lg flex-col rounded-xl border bg-card shadow-lg">
                <div class="flex items-center justify-between border-b px-4 py-3">
                    <h3 class="font-semibold">{{ t('admin.sales.orders.pick_catalog') }}</h3>
                    <Button type="button" variant="ghost" size="icon" class="size-8" @click="catalogOpen = false">
                        <X class="size-4" />
                    </Button>
                </div>

                <div class="border-b px-4">
                    <div class="inline-flex gap-1 py-2">
                        <Button
                            type="button"
                            size="sm"
                            :variant="catalogTab === 'products' ? 'default' : 'ghost'"
                            @click="catalogTab = 'products'"
                        >
                            {{ t('nav.sales_products') }}
                        </Button>
                        <Button
                            type="button"
                            size="sm"
                            :variant="catalogTab === 'services' ? 'default' : 'ghost'"
                            @click="catalogTab = 'services'"
                        >
                            {{ t('nav.sales_services') }}
                        </Button>
                    </div>
                </div>

                <div class="overflow-y-auto p-2">
                    <template v-if="catalogTab === 'products'">
                        <button
                            v-for="product in products"
                            :key="product.id"
                            type="button"
                            class="flex w-full items-center justify-between rounded-lg px-3 py-2 text-left text-sm hover:bg-muted"
                            @click="addProduct(product)"
                        >
                            <span>{{ product.name }}</span>
                            <span class="flex items-center gap-2 text-muted-foreground">
                                {{ formatMoney(product.price) }}
                                <Plus class="size-4" />
                            </span>
                        </button>
                        <p v-if="products.length === 0" class="px-3 py-6 text-center text-sm text-muted-foreground">
                            {{ t('admin.sales.products.empty') }}
                        </p>
                    </template>

                    <template v-else>
                        <button
                            v-for="service in services"
                            :key="service.id"
                            type="button"
                            class="flex w-full items-center justify-between rounded-lg px-3 py-2 text-left text-sm hover:bg-muted"
                            @click="addService(service)"
                        >
                            <span>{{ service.name }}</span>
                            <span class="flex items-center gap-2 text-muted-foreground">
                                {{ formatMoney(service.price) }}
                                <Plus class="size-4" />
                            </span>
                        </button>
                        <p v-if="services.length === 0" class="px-3 py-6 text-center text-sm text-muted-foreground">
                            {{ t('admin.sales.services.empty') }}
                        </p>
                    </template>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
