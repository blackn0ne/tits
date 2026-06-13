<script setup lang="ts">
import Heading from '@/components/Heading.vue';
import IconPicker from '@/components/IconPicker.vue';
import InputError from '@/components/InputError.vue';
import SeoHead from '@/components/SeoHead.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { useI18n } from '@/composables/useI18n';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { router, useForm } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

interface TranslationForm {
    language_id: number;
    code: string;
    name: string;
    value: string;
    slug: string;
}

interface CategoryForm {
    id: number;
    icon: string;
    is_active: boolean;
    sort_order: number;
}

const props = defineProps<{
    category: CategoryForm | null;
    translationsByLanguage: Record<string, TranslationForm>;
}>();

const { t } = useI18n();
const isEditing = computed(() => props.category !== null);

const activeLanguageId = ref(Object.keys(props.translationsByLanguage)[0] ?? '');

const breadcrumbs = computed<BreadcrumbItem[]>(() => [
    { title: t('nav.blog'), href: route('admin.blog-posts.index') },
    { title: t('admin.categories.title'), href: route('admin.blog-categories.index') },
    {
        title: isEditing.value ? t('admin.categories.edit') : t('admin.categories.create'),
        href: isEditing.value ? route('admin.blog-categories.edit', props.category!.id) : route('admin.blog-categories.create'),
    },
]);

const form = useForm({
    icon: props.category?.icon ?? 'Folder',
    is_active: props.category?.is_active ?? true,
    sort_order: props.category?.sort_order ?? 0,
    translations: Object.fromEntries(
        Object.entries(props.translationsByLanguage).map(([id, row]) => [
            id,
            {
                language_id: row.language_id,
                name: row.value,
            },
        ]),
    ),
});

const activeTranslation = computed(() => form.translations[activeLanguageId.value]);

const submit = () => {
    if (isEditing.value) {
        form.put(route('admin.blog-categories.update', props.category!.id));
        return;
    }

    form.post(route('admin.blog-categories.store'));
};
</script>

<template>
    <SeoHead :title="isEditing ? t('admin.categories.edit') : t('admin.categories.create')" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex flex-col gap-3 px-6 py-3 pb-20">
            <Heading
                :title="isEditing ? t('admin.categories.edit') : t('admin.categories.create')"
                :description="t('admin.categories.form_description')"
            />

            <form @submit.prevent="submit" class="w-full max-w-2xl space-y-6">
                <section class="rounded-xl border bg-card p-6 shadow-sm">
                    <div class="grid gap-4">
                        <div class="grid gap-2">
                            <Label>{{ t('admin.categories.icon') }}</Label>
                            <IconPicker v-model="form.icon" />
                            <InputError :message="form.errors.icon" />
                        </div>

                        <div class="grid gap-2">
                            <Label>{{ t('admin.categories.sort_order') }}</Label>
                            <Input v-model.number="form.sort_order" type="number" min="0" />
                            <InputError :message="form.errors.sort_order" />
                        </div>

                        <label class="flex items-center gap-2 text-sm">
                            <input v-model="form.is_active" type="checkbox" class="rounded border-input" />
                            {{ t('admin.categories.active') }}
                        </label>
                    </div>
                </section>

                <section class="rounded-xl border bg-card p-6 shadow-sm">
                    <h2 class="text-lg font-semibold">{{ t('admin.categories.names') }}</h2>

                    <div class="mt-4 inline-flex flex-wrap gap-2 rounded-lg border bg-muted/30 p-1">
                        <Button
                            v-for="(row, id) in translationsByLanguage"
                            :key="id"
                            type="button"
                            :variant="activeLanguageId === id ? 'default' : 'ghost'"
                            size="sm"
                            @click="activeLanguageId = id"
                        >
                            {{ row.name }}
                        </Button>
                    </div>

                    <div v-if="activeTranslation" class="mt-4 grid gap-4">
                        <div class="grid gap-2">
                            <Label>{{ t('admin.categories.name') }} ({{ translationsByLanguage[activeLanguageId]?.code }})</Label>
                            <Input v-model="activeTranslation.name" required />
                            <InputError :message="form.errors[`translations.${activeLanguageId}.name`]" />
                        </div>
                        <p v-if="translationsByLanguage[activeLanguageId]?.slug" class="text-xs text-muted-foreground">
                            {{ t('admin.blog.slug') }}: {{ translationsByLanguage[activeLanguageId].slug }}
                        </p>
                    </div>
                </section>

                <div class="flex justify-end gap-2">
                    <Button type="button" variant="outline" @click="router.visit(route('admin.blog-categories.index'))">
                        {{ t('admin.categories.cancel') }}
                    </Button>
                    <Button type="submit" :disabled="form.processing">{{ t('admin.categories.save') }}</Button>
                </div>
            </form>
        </div>
    </AppLayout>
</template>
