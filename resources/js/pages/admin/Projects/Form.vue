<script setup lang="ts">
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import RichTextEditor from '@/components/RichTextEditor.vue';
import SeoHead from '@/components/SeoHead.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { useI18n } from '@/composables/useI18n';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { router, useForm } from '@inertiajs/vue3';
import { ImageIcon, Trash2, Upload } from 'lucide-vue-next';
import { computed, ref } from 'vue';

interface TranslationForm {
    language_id: number;
    code: string;
    name: string;
    title: string;
    meta_title: string;
    meta_description: string;
    slug: string;
    content: string;
}

interface ProjectForm {
    id: number;
    project_category_id: number | null;
    status: string;
    published_at: string | null;
    banner_url: string | null;
}

interface CategoryOption {
    id: number;
    name: string;
}

interface StatusOption {
    value: string;
    label: string;
}

const props = defineProps<{
    project: ProjectForm | null;
    translationsByLanguage: Record<string, TranslationForm>;
    categories: CategoryOption[];
    statuses: StatusOption[];
}>();

const { t } = useI18n();
const isEditing = computed(() => props.project !== null);

const activeLanguageId = ref(Object.keys(props.translationsByLanguage)[0] ?? '');

const breadcrumbs = computed<BreadcrumbItem[]>(() => [
    { title: t('nav.works'), href: route('admin.projects.index') },
    { title: t('admin.projects.title'), href: route('admin.projects.index') },
    {
        title: isEditing.value ? t('admin.projects.edit') : t('admin.projects.create'),
        href: isEditing.value ? route('admin.projects.edit', props.project!.id) : route('admin.projects.create'),
    },
]);

const form = useForm({
    project_category_id: props.project?.project_category_id ?? '',
    status: props.project?.status ?? 'draft',
    published_at: props.project?.published_at ?? '',
    banner: null as File | null,
    remove_banner: false,
    translations: Object.fromEntries(
        Object.entries(props.translationsByLanguage).map(([id, row]) => [
            id,
            {
                language_id: row.language_id,
                title: row.title,
                meta_title: row.meta_title,
                meta_description: row.meta_description,
                content: row.content,
            },
        ]),
    ),
});

const bannerPreview = computed(() => {
    if (form.banner) {
        return URL.createObjectURL(form.banner);
    }

    if (form.remove_banner) {
        return null;
    }

    return props.project?.banner_url ?? null;
});

const activeTranslation = computed(() => form.translations[activeLanguageId.value]);

const onBannerChange = (event: Event) => {
    const target = event.target as HTMLInputElement;
    form.banner = target.files?.[0] ?? null;
    form.remove_banner = false;
};

const submit = () => {
    const options = { forceFormData: true, preserveScroll: true };

    if (isEditing.value) {
        form.post(route('admin.projects.update', props.project!.id), options);
        return;
    }

    form.post(route('admin.projects.store'), options);
};
</script>

<template>
    <SeoHead :title="isEditing ? t('admin.projects.edit') : t('admin.projects.create')" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex flex-col gap-3 px-6 py-3 pb-20">
            <Heading :title="isEditing ? t('admin.projects.edit') : t('admin.projects.create')" :description="t('admin.projects.form_description')" />

            <form @submit.prevent="submit" class="w-full max-w-4xl space-y-6">
                <section class="rounded-xl border bg-card p-6 shadow-sm">
                    <h2 class="text-lg font-semibold">{{ t('admin.projects.general') }}</h2>

                    <div class="mt-4 grid gap-4 md:grid-cols-2">
                        <div class="grid gap-2">
                            <Label for="category">{{ t('admin.project_categories.name') }}</Label>
                            <select
                                id="category"
                                v-model="form.project_category_id"
                                class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm"
                            >
                                <option value="">{{ t('admin.projects.no_category') }}</option>
                                <option v-for="category in categories" :key="category.id" :value="category.id">{{ category.name }}</option>
                            </select>
                            <InputError :message="form.errors.project_category_id" />
                        </div>

                        <div class="grid gap-2">
                            <Label for="status">{{ t('admin.projects.status') }}</Label>
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
                            <Label for="published_at">{{ t('admin.projects.date') }}</Label>
                            <Input id="published_at" v-model="form.published_at" type="datetime-local" />
                            <InputError :message="form.errors.published_at" />
                        </div>
                    </div>
                </section>

                <section class="rounded-xl border bg-card p-6 shadow-sm">
                    <h2 class="text-lg font-semibold">{{ t('admin.projects.banner') }}</h2>

                    <div class="mt-4 space-y-4 rounded-lg border border-dashed p-4">
                        <div class="flex h-36 items-center justify-center overflow-hidden rounded-lg border bg-muted/40">
                            <img v-if="bannerPreview" :src="bannerPreview" alt="" class="max-h-full max-w-full object-cover" />
                            <ImageIcon v-else class="size-10 text-muted-foreground/50" />
                        </div>
                        <div class="flex items-center gap-2">
                            <label class="inline-flex cursor-pointer items-center gap-2 rounded-md border px-3 py-2 text-sm hover:bg-muted">
                                <Upload class="size-4 shrink-0" />
                                <span>{{ t('admin.site.choose_file') }}</span>
                                <input type="file" accept="image/*" class="hidden" @change="onBannerChange" />
                            </label>
                            <Button
                                v-if="bannerPreview"
                                type="button"
                                variant="outline"
                                size="icon"
                                class="size-9 shrink-0"
                                :title="t('admin.projects.remove_banner')"
                                @click="
                                    form.remove_banner = true;
                                    form.banner = null;
                                "
                            >
                                <Trash2 class="size-4" />
                            </Button>
                        </div>
                        <InputError :message="form.errors.banner" />
                    </div>
                </section>

                <section class="rounded-xl border bg-card p-6 shadow-sm">
                    <h2 class="text-lg font-semibold">{{ t('admin.projects.content') }}</h2>

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
                            <Label>{{ t('admin.projects.project_title') }} ({{ translationsByLanguage[activeLanguageId]?.code }})</Label>
                            <Input v-model="activeTranslation.title" required />
                            <InputError :message="form.errors[`translations.${activeLanguageId}.title`]" />
                        </div>
                        <p v-if="translationsByLanguage[activeLanguageId]?.slug" class="text-xs text-muted-foreground">
                            {{ t('admin.projects.slug') }}: {{ translationsByLanguage[activeLanguageId].slug }}
                        </p>
                        <div class="grid gap-3 rounded-lg border bg-muted/20 p-4">
                            <p class="text-sm font-semibold">{{ t('admin.projects.seo_section') }}</p>
                            <div class="grid gap-2">
                                <Label>{{ t('admin.projects.meta_title') }}</Label>
                                <Input v-model="activeTranslation.meta_title" />
                                <p class="text-xs text-muted-foreground">{{ t('admin.projects.meta_title_hint') }}</p>
                            </div>
                            <div class="grid gap-2">
                                <Label>{{ t('admin.projects.meta_description') }}</Label>
                                <textarea
                                    v-model="activeTranslation.meta_description"
                                    rows="2"
                                    class="flex min-h-[60px] w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2"
                                />
                                <p class="text-xs text-muted-foreground">{{ t('admin.projects.meta_description_hint') }}</p>
                            </div>
                        </div>
                        <div class="grid gap-2">
                            <Label>{{ t('admin.projects.body') }}</Label>
                            <RichTextEditor v-model="activeTranslation.content" />
                            <InputError :message="form.errors[`translations.${activeLanguageId}.content`]" />
                        </div>
                    </div>
                </section>

                <div class="flex justify-end gap-2">
                    <Button type="button" variant="outline" @click="router.visit(route('admin.projects.index'))">
                        {{ t('admin.project_categories.cancel') }}
                    </Button>
                    <Button type="submit" :disabled="form.processing">{{ t('admin.projects.save') }}</Button>
                </div>
            </form>
        </div>
    </AppLayout>
</template>
