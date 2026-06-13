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
import { computed } from 'vue';

interface ServiceForm {
    id: number;
    name: string;
    price: number;
    status: string;
}

interface StatusOption {
    value: string;
    label: string;
}

const props = defineProps<{
    service: ServiceForm | null;
    statuses: StatusOption[];
}>();

const { t } = useI18n();
const isEditing = computed(() => props.service !== null);

const breadcrumbs = computed<BreadcrumbItem[]>(() => [
    { title: t('nav.sales'), href: route('admin.sales.services.index') },
    { title: t('admin.sales.services.title'), href: route('admin.sales.services.index') },
    {
        title: isEditing.value ? t('admin.sales.services.edit') : t('admin.sales.services.create'),
        href: isEditing.value ? route('admin.sales.services.edit', props.service!.id) : route('admin.sales.services.create'),
    },
]);

const form = useForm({
    name: props.service?.name ?? '',
    price: props.service?.price ?? 0,
    status: props.service?.status ?? 'active',
});

const submit = () => {
    if (isEditing.value) {
        form.put(route('admin.sales.services.update', props.service!.id));
        return;
    }

    form.post(route('admin.sales.services.store'));
};
</script>

<template>
    <SeoHead :title="isEditing ? t('admin.sales.services.edit') : t('admin.sales.services.create')" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex flex-col gap-3 px-6 py-3 pb-20">
            <Heading
                :title="isEditing ? t('admin.sales.services.edit') : t('admin.sales.services.create')"
                :description="t('admin.sales.services.form_description')"
            />

            <form @submit.prevent="submit" class="w-full max-w-2xl space-y-6">
                <section class="rounded-xl border bg-card p-6 shadow-sm">
                    <div class="grid gap-4">
                        <div class="grid gap-2">
                            <Label for="name">{{ t('admin.sales.services.name') }}</Label>
                            <Input id="name" v-model="form.name" required />
                            <InputError :message="form.errors.name" />
                        </div>

                        <div class="grid gap-2">
                            <Label for="price">{{ t('admin.sales.services.price') }}</Label>
                            <Input id="price" v-model.number="form.price" type="number" min="0" step="0.01" required />
                            <InputError :message="form.errors.price" />
                        </div>

                        <div class="grid gap-2">
                            <Label for="status">{{ t('admin.sales.services.status') }}</Label>
                            <select
                                id="status"
                                v-model="form.status"
                                class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm"
                            >
                                <option v-for="status in statuses" :key="status.value" :value="status.value">{{ status.label }}</option>
                            </select>
                            <InputError :message="form.errors.status" />
                        </div>
                    </div>
                </section>

                <div class="flex justify-end gap-2">
                    <Button type="button" variant="outline" @click="router.visit(route('admin.sales.services.index'))">
                        {{ t('admin.sales.cancel') }}
                    </Button>
                    <Button type="submit" :disabled="form.processing">{{ t('admin.sales.save') }}</Button>
                </div>
            </form>
        </div>
    </AppLayout>
</template>
