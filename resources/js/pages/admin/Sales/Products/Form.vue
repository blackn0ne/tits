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
import { ImageIcon, Trash2, Upload } from 'lucide-vue-next';
import { computed } from 'vue';

interface ProductForm {
    id: number;
    name: string;
    short_description: string | null;
    price: number;
    quantity: number;
    unit: string;
    status: string;
    kaspi_link: string | null;
    image_url: string | null;
}

interface StatusOption {
    value: string;
    label: string;
}

const props = defineProps<{
    product: ProductForm | null;
    statuses: StatusOption[];
}>();

const { t } = useI18n();
const isEditing = computed(() => props.product !== null);

const breadcrumbs = computed<BreadcrumbItem[]>(() => [
    { title: t('nav.sales'), href: route('admin.sales.products.index') },
    { title: t('admin.sales.products.title'), href: route('admin.sales.products.index') },
    {
        title: isEditing.value ? t('admin.sales.products.edit') : t('admin.sales.products.create'),
        href: isEditing.value ? route('admin.sales.products.edit', props.product!.id) : route('admin.sales.products.create'),
    },
]);

const form = useForm({
    name: props.product?.name ?? '',
    short_description: props.product?.short_description ?? '',
    price: props.product?.price ?? 0,
    quantity: props.product?.quantity ?? 0,
    unit: props.product?.unit ?? '',
    status: props.product?.status ?? 'active',
    kaspi_link: props.product?.kaspi_link ?? '',
    image: null as File | null,
    remove_image: false,
});

const imagePreview = computed(() => {
    if (form.image) {
        return URL.createObjectURL(form.image);
    }

    if (form.remove_image) {
        return null;
    }

    return props.product?.image_url ?? null;
});

const onImageChange = (event: Event) => {
    const target = event.target as HTMLInputElement;
    form.image = target.files?.[0] ?? null;
    form.remove_image = false;
};

const submit = () => {
    const options = { forceFormData: true, preserveScroll: true };

    if (isEditing.value) {
        form.post(route('admin.sales.products.update', props.product!.id), {
            ...options,
            method: 'put',
        });
        return;
    }

    form.post(route('admin.sales.products.store'), options);
};
</script>

<template>
    <SeoHead :title="isEditing ? t('admin.sales.products.edit') : t('admin.sales.products.create')" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex flex-col gap-3 px-6 py-3 pb-20">
            <Heading
                :title="isEditing ? t('admin.sales.products.edit') : t('admin.sales.products.create')"
                :description="t('admin.sales.products.form_description')"
            />

            <form @submit.prevent="submit" class="w-full max-w-2xl space-y-6">
                <section class="rounded-xl border bg-card p-6 shadow-sm">
                    <div class="grid gap-4">
                        <div class="grid gap-2">
                            <Label for="name">{{ t('admin.sales.products.name') }}</Label>
                            <Input id="name" v-model="form.name" required />
                            <InputError :message="form.errors.name" />
                        </div>

                        <div class="grid gap-2">
                            <Label for="short_description">{{ t('admin.sales.products.short_description') }}</Label>
                            <textarea
                                id="short_description"
                                v-model="form.short_description"
                                rows="4"
                                class="flex min-h-24 w-full rounded-md border border-input bg-background px-3 py-2 text-sm"
                            />
                            <InputError :message="form.errors.short_description" />
                        </div>

                        <div class="grid gap-4 md:grid-cols-2">
                            <div class="grid gap-2">
                                <Label for="price">{{ t('admin.sales.products.price') }}</Label>
                                <Input id="price" v-model.number="form.price" type="number" min="0" step="0.01" required />
                                <InputError :message="form.errors.price" />
                            </div>

                            <div class="grid gap-2">
                                <Label for="quantity">{{ t('admin.sales.products.quantity') }}</Label>
                                <Input id="quantity" v-model.number="form.quantity" type="number" min="0" required />
                                <InputError :message="form.errors.quantity" />
                            </div>

                            <div class="grid gap-2">
                                <Label for="unit">{{ t('admin.sales.products.unit') }}</Label>
                                <Input id="unit" v-model="form.unit" required />
                                <InputError :message="form.errors.unit" />
                            </div>

                            <div class="grid gap-2">
                                <Label for="status">{{ t('admin.sales.products.status') }}</Label>
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
                                <Label for="kaspi_link">{{ t('admin.sales.products.kaspi_link') }}</Label>
                                <Input id="kaspi_link" v-model="form.kaspi_link" type="url" />
                                <InputError :message="form.errors.kaspi_link" />
                            </div>
                        </div>
                    </div>
                </section>

                <section class="rounded-xl border bg-card p-6 shadow-sm">
                    <h2 class="text-lg font-semibold">{{ t('admin.sales.products.image') }}</h2>

                    <div class="mt-4 space-y-4 rounded-lg border border-dashed p-4">
                        <div class="flex h-36 items-center justify-center overflow-hidden rounded-lg border bg-muted/40">
                            <img v-if="imagePreview" :src="imagePreview" alt="" class="max-h-full max-w-full object-cover" />
                            <ImageIcon v-else class="size-10 text-muted-foreground/50" />
                        </div>
                        <div class="flex items-center gap-2">
                            <label class="inline-flex cursor-pointer items-center gap-2 rounded-md border px-3 py-2 text-sm hover:bg-muted">
                                <Upload class="size-4 shrink-0" />
                                <span>{{ t('admin.site.choose_file') }}</span>
                                <input type="file" accept="image/*" class="hidden" @change="onImageChange" />
                            </label>
                            <Button
                                v-if="imagePreview"
                                type="button"
                                variant="outline"
                                size="icon"
                                class="size-9 shrink-0"
                                :title="t('admin.sales.products.remove_image')"
                                @click="
                                    form.remove_image = true;
                                    form.image = null;
                                "
                            >
                                <Trash2 class="size-4" />
                            </Button>
                        </div>
                        <InputError :message="form.errors.image" />
                    </div>
                </section>

                <div class="flex justify-end gap-2">
                    <Button type="button" variant="outline" @click="router.visit(route('admin.sales.products.index'))">
                        {{ t('admin.sales.cancel') }}
                    </Button>
                    <Button type="submit" :disabled="form.processing">{{ t('admin.sales.save') }}</Button>
                </div>
            </form>
        </div>
    </AppLayout>
</template>
