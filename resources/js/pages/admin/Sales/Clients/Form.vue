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

interface ClientForm {
    id: number;
    full_name: string;
    phone: string;
}

const props = defineProps<{
    client: ClientForm | null;
}>();

const { t } = useI18n();
const isEditing = computed(() => props.client !== null);

const breadcrumbs = computed<BreadcrumbItem[]>(() => [
    { title: t('nav.sales'), href: route('admin.sales.clients.index') },
    { title: t('admin.sales.clients.title'), href: route('admin.sales.clients.index') },
    {
        title: isEditing.value ? t('admin.sales.clients.edit') : t('admin.sales.clients.create'),
        href: isEditing.value ? route('admin.sales.clients.edit', props.client!.id) : route('admin.sales.clients.create'),
    },
]);

const form = useForm({
    full_name: props.client?.full_name ?? '',
    phone: props.client?.phone ?? '',
});

const submit = () => {
    if (isEditing.value) {
        form.put(route('admin.sales.clients.update', props.client!.id));
        return;
    }

    form.post(route('admin.sales.clients.store'));
};
</script>

<template>
    <SeoHead :title="isEditing ? t('admin.sales.clients.edit') : t('admin.sales.clients.create')" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex flex-col gap-3 px-6 py-3 pb-20">
            <Heading
                :title="isEditing ? t('admin.sales.clients.edit') : t('admin.sales.clients.create')"
                :description="t('admin.sales.clients.form_description')"
            />

            <form @submit.prevent="submit" class="w-full max-w-2xl space-y-6">
                <section class="rounded-xl border bg-card p-6 shadow-sm">
                    <div class="grid gap-4">
                        <div class="grid gap-2">
                            <Label for="full_name">{{ t('admin.sales.clients.full_name') }}</Label>
                            <Input id="full_name" v-model="form.full_name" required />
                            <InputError :message="form.errors.full_name" />
                        </div>

                        <div class="grid gap-2">
                            <Label for="phone">{{ t('admin.sales.clients.phone') }}</Label>
                            <Input id="phone" v-model="form.phone" required />
                            <InputError :message="form.errors.phone" />
                        </div>
                    </div>
                </section>

                <div class="flex justify-end gap-2">
                    <Button type="button" variant="outline" @click="router.visit(route('admin.sales.clients.index'))">
                        {{ t('admin.sales.cancel') }}
                    </Button>
                    <Button type="submit" :disabled="form.processing">{{ t('admin.sales.save') }}</Button>
                </div>
            </form>
        </div>
    </AppLayout>
</template>
