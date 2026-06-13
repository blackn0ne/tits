<script setup lang="ts">
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import SeoHead from '@/components/SeoHead.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { useI18n } from '@/composables/useI18n';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem, type SharedData } from '@/types';
import { useForm, usePage, Link } from '@inertiajs/vue3';
import { ExternalLink, ImageIcon, Trash2, Upload } from 'lucide-vue-next';
import { computed, ref } from 'vue';

interface SeoForm {
    language_id: number;
    code: string;
    name: string;
    site_name: string;
    description: string;
    keywords: string;
    home_title: string;
    home_meta_description: string;
    blog_index_title: string;
    blog_index_meta_description: string;
    projects_index_title: string;
    projects_index_meta_description: string;
}

interface SettingForm {
    maintenance_mode: boolean;
    sitemap_enabled: boolean;
    title_separator: string;
    default_robots: string;
    twitter_handle: string | null;
    google_site_verification: string | null;
    yandex_verification: string | null;
    robots_txt: string | null;
    logo_url: string | null;
    favicon_url: string | null;
    og_image_url: string | null;
    phone: string | null;
    address: string | null;
    social: Record<string, string | null>;
}

const props = defineProps<{
    setting: SettingForm;
    seoByLanguage: Record<string, SeoForm>;
}>();

const { t } = useI18n();
const page = usePage<SharedData>();

const socialNetworks = ['facebook', 'instagram', 'telegram', 'whatsapp', 'youtube', 'tiktok'] as const;

const activeLanguageId = ref(Object.keys(props.seoByLanguage)[0] ?? '');

const breadcrumbs = computed<BreadcrumbItem[]>(() => [
    {
        title: t('admin.site.title'),
        href: route('admin.site-settings.edit'),
    },
]);

const form = useForm({
    maintenance_mode: props.setting.maintenance_mode,
    sitemap_enabled: props.setting.sitemap_enabled,
    title_separator: props.setting.title_separator,
    default_robots: props.setting.default_robots,
    twitter_handle: props.setting.twitter_handle ?? '',
    google_site_verification: props.setting.google_site_verification ?? '',
    yandex_verification: props.setting.yandex_verification ?? '',
    robots_txt: props.setting.robots_txt ?? '',
    phone: props.setting.phone ?? '',
    address: props.setting.address ?? '',
    social: { ...props.setting.social },
    logo: null as File | null,
    favicon: null as File | null,
    og_image: null as File | null,
    remove_logo: false,
    remove_favicon: false,
    remove_og_image: false,
    translations: Object.fromEntries(
        Object.entries(props.seoByLanguage).map(([id, seo]) => [
            id,
            {
                language_id: seo.language_id,
                site_name: seo.site_name,
                description: seo.description,
                keywords: seo.keywords,
                home_title: seo.home_title,
                home_meta_description: seo.home_meta_description,
                blog_index_title: seo.blog_index_title,
                blog_index_meta_description: seo.blog_index_meta_description,
                projects_index_title: seo.projects_index_title,
                projects_index_meta_description: seo.projects_index_meta_description,
            },
        ]),
    ),
});

const logoPreview = computed(() => {
    if (form.logo) {
        return URL.createObjectURL(form.logo);
    }

    if (form.remove_logo) {
        return null;
    }

    return props.setting.logo_url;
});

const faviconPreview = computed(() => {
    if (form.favicon) {
        return URL.createObjectURL(form.favicon);
    }

    if (form.remove_favicon) {
        return null;
    }

    return props.setting.favicon_url;
});

const ogImagePreview = computed(() => {
    if (form.og_image) {
        return URL.createObjectURL(form.og_image);
    }

    if (form.remove_og_image) {
        return null;
    }

    return props.setting.og_image_url;
});

const activeSeo = computed(() => form.translations[activeLanguageId.value]);

const submit = () => {
    form.post(route('admin.site-settings.update'), {
        forceFormData: true,
        preserveScroll: true,
    });
};

const onLogoChange = (event: Event) => {
    const target = event.target as HTMLInputElement;
    form.logo = target.files?.[0] ?? null;
    form.remove_logo = false;
};

const onFaviconChange = (event: Event) => {
    const target = event.target as HTMLInputElement;
    form.favicon = target.files?.[0] ?? null;
    form.remove_favicon = false;
};

const onOgImageChange = (event: Event) => {
    const target = event.target as HTMLInputElement;
    form.og_image = target.files?.[0] ?? null;
    form.remove_og_image = false;
};
</script>

<template>
    <SeoHead :title="t('admin.site.title')" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex flex-col gap-3 px-6 py-3 pb-20">
            <Heading :title="t('admin.site.title')" :description="t('admin.site.description')" />

            <div
                v-if="page.props.flash?.status === 'site-settings-saved'"
                class="rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700 dark:border-green-900 dark:bg-green-950 dark:text-green-300"
            >
                {{ t('admin.site.saved') }}
            </div>

            <form @submit.prevent="submit" class="w-full max-w-4xl space-y-6">
                <section class="rounded-xl border bg-card p-6 shadow-sm">
                    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                        <div class="space-y-1">
                            <h2 class="text-lg font-semibold">{{ t('admin.site.maintenance') }}</h2>
                            <p class="text-sm text-muted-foreground">{{ t('admin.site.maintenance_description') }}</p>
                        </div>
                        <label class="relative inline-flex shrink-0 cursor-pointer items-center gap-3">
                            <span class="text-sm font-medium text-muted-foreground">
                                {{ form.maintenance_mode ? t('admin.site.maintenance_enabled') : t('admin.site.maintenance_disabled') }}
                            </span>
                            <input
                                v-model="form.maintenance_mode"
                                type="checkbox"
                                class="peer sr-only"
                                role="switch"
                                :aria-checked="form.maintenance_mode"
                            />
                            <span
                                class="relative h-7 w-12 rounded-full bg-muted transition-colors peer-checked:bg-primary peer-focus-visible:outline-hidden peer-focus-visible:ring-2 peer-focus-visible:ring-ring peer-focus-visible:ring-offset-2 after:absolute after:start-0.5 after:top-0.5 after:size-6 after:rounded-full after:bg-background after:shadow-sm after:transition-transform peer-checked:after:translate-x-5"
                                aria-hidden="true"
                            />
                        </label>
                    </div>
                    <div class="mt-4">
                        <Link
                            :href="route('maintenance')"
                            target="_blank"
                            class="inline-flex items-center gap-2 text-sm font-medium text-primary hover:underline"
                        >
                            {{ t('admin.site.maintenance_preview') }}
                            <ExternalLink class="size-4" />
                        </Link>
                    </div>
                    <InputError :message="form.errors.maintenance_mode" class="mt-2" />
                </section>

                <section class="rounded-xl border bg-card p-6 shadow-sm">
                    <h2 class="text-lg font-semibold">{{ t('admin.site.branding') }}</h2>
                    <p class="mt-1 text-sm text-muted-foreground">{{ t('admin.site.description') }}</p>

                    <div class="mt-6 grid gap-6 md:grid-cols-2">
                        <div class="space-y-4 rounded-lg border border-dashed p-4">
                            <Label>{{ t('admin.site.logo') }}</Label>
                            <div class="flex h-28 items-center justify-center overflow-hidden rounded-lg border bg-muted/40">
                                <img v-if="logoPreview" :src="logoPreview" :alt="t('admin.site.logo')" class="max-h-full max-w-full object-contain p-2" />
                                <ImageIcon v-else class="size-10 text-muted-foreground/50" />
                            </div>
                            <div class="flex items-center gap-2">
                                <label class="inline-flex cursor-pointer items-center gap-2 rounded-md border px-3 py-2 text-sm hover:bg-muted">
                                    <Upload class="size-4 shrink-0" />
                                    <span>{{ t('admin.site.choose_file') }}</span>
                                    <input type="file" accept="image/*" class="hidden" @change="onLogoChange" />
                                </label>
                                <Button
                                    v-if="logoPreview"
                                    type="button"
                                    variant="outline"
                                    size="icon"
                                    class="size-9 shrink-0"
                                    :title="t('admin.site.remove_logo')"
                                    @click="
                                        form.remove_logo = true;
                                        form.logo = null;
                                    "
                                >
                                    <Trash2 class="size-4" />
                                    <span class="sr-only">{{ t('admin.site.remove_logo') }}</span>
                                </Button>
                            </div>
                            <InputError :message="form.errors.logo" />
                        </div>

                        <div class="space-y-4 rounded-lg border border-dashed p-4">
                            <Label>{{ t('admin.site.favicon') }}</Label>
                            <div class="flex h-28 items-center justify-center overflow-hidden rounded-lg border bg-muted/40">
                                <img v-if="faviconPreview" :src="faviconPreview" :alt="t('admin.site.favicon')" class="max-h-16 max-w-16 object-contain" />
                                <ImageIcon v-else class="size-8 text-muted-foreground/50" />
                            </div>
                            <div class="flex items-center gap-2">
                                <label class="inline-flex cursor-pointer items-center gap-2 rounded-md border px-3 py-2 text-sm hover:bg-muted">
                                    <Upload class="size-4 shrink-0" />
                                    <span>{{ t('admin.site.choose_file') }}</span>
                                    <input type="file" accept="image/*" class="hidden" @change="onFaviconChange" />
                                </label>
                                <Button
                                    v-if="faviconPreview"
                                    type="button"
                                    variant="outline"
                                    size="icon"
                                    class="size-9 shrink-0"
                                    :title="t('admin.site.remove_favicon')"
                                    @click="
                                        form.remove_favicon = true;
                                        form.favicon = null;
                                    "
                                >
                                    <Trash2 class="size-4" />
                                    <span class="sr-only">{{ t('admin.site.remove_favicon') }}</span>
                                </Button>
                            </div>
                            <InputError :message="form.errors.favicon" />
                        </div>
                    </div>
                </section>

                <section class="rounded-xl border bg-card p-6 shadow-sm">
                    <h2 class="text-lg font-semibold">{{ t('admin.site.seo') }}</h2>
                    <p class="mt-1 text-sm text-muted-foreground">{{ t('admin.site.seo_description') }}</p>

                    <div class="mt-6 grid gap-4 md:grid-cols-2">
                        <div class="grid gap-2">
                            <Label for="title_separator">{{ t('admin.site.title_separator') }}</Label>
                            <Input id="title_separator" v-model="form.title_separator" maxlength="10" />
                            <InputError :message="form.errors.title_separator" />
                        </div>
                        <div class="grid gap-2">
                            <Label for="default_robots">{{ t('admin.site.default_robots') }}</Label>
                            <Input id="default_robots" v-model="form.default_robots" />
                            <InputError :message="form.errors.default_robots" />
                        </div>
                        <div class="grid gap-2">
                            <Label for="twitter_handle">{{ t('admin.site.twitter_handle') }}</Label>
                            <Input id="twitter_handle" v-model="form.twitter_handle" placeholder="@brand" />
                            <InputError :message="form.errors.twitter_handle" />
                        </div>
                        <div class="grid gap-2">
                            <Label for="google_site_verification">{{ t('admin.site.google_verification') }}</Label>
                            <Input id="google_site_verification" v-model="form.google_site_verification" />
                            <InputError :message="form.errors.google_site_verification" />
                        </div>
                        <div class="grid gap-2">
                            <Label for="yandex_verification">{{ t('admin.site.yandex_verification') }}</Label>
                            <Input id="yandex_verification" v-model="form.yandex_verification" />
                            <InputError :message="form.errors.yandex_verification" />
                        </div>
                        <div class="flex items-center justify-between gap-4 rounded-lg border p-4 md:col-span-2">
                            <div>
                                <p class="text-sm font-medium">{{ t('admin.site.sitemap_enabled') }}</p>
                                <p class="text-xs text-muted-foreground">{{ t('admin.site.sitemap_enabled_hint') }}</p>
                            </div>
                            <input v-model="form.sitemap_enabled" type="checkbox" class="size-4 rounded border" />
                        </div>
                    </div>

                    <div class="mt-6 space-y-4 rounded-lg border border-dashed p-4">
                        <Label>{{ t('admin.site.og_image') }}</Label>
                        <div class="flex h-36 items-center justify-center overflow-hidden rounded-lg border bg-muted/40">
                            <img v-if="ogImagePreview" :src="ogImagePreview" :alt="t('admin.site.og_image')" class="max-h-full max-w-full object-contain p-2" />
                            <ImageIcon v-else class="size-10 text-muted-foreground/50" />
                        </div>
                        <div class="flex items-center gap-2">
                            <label class="inline-flex cursor-pointer items-center gap-2 rounded-md border px-3 py-2 text-sm hover:bg-muted">
                                <Upload class="size-4 shrink-0" />
                                <span>{{ t('admin.site.choose_file') }}</span>
                                <input type="file" accept="image/*" class="hidden" @change="onOgImageChange" />
                            </label>
                            <Button
                                v-if="ogImagePreview"
                                type="button"
                                variant="outline"
                                size="icon"
                                class="size-9 shrink-0"
                                @click="
                                    form.remove_og_image = true;
                                    form.og_image = null;
                                "
                            >
                                <Trash2 class="size-4" />
                            </Button>
                        </div>
                        <InputError :message="form.errors.og_image" />
                    </div>

                    <div class="mt-6 grid gap-2">
                        <Label for="robots_txt">{{ t('admin.site.robots_txt') }}</Label>
                        <textarea
                            id="robots_txt"
                            v-model="form.robots_txt"
                            rows="5"
                            class="flex min-h-[120px] w-full rounded-md border border-input bg-background px-3 py-2 font-mono text-xs ring-offset-background placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2"
                            :placeholder="t('admin.site.robots_txt_placeholder')"
                        />
                        <div class="flex flex-wrap gap-3 text-sm">
                            <Link :href="route('seo.robots')" target="_blank" class="inline-flex items-center gap-1 text-primary hover:underline">
                                robots.txt <ExternalLink class="size-3.5" />
                            </Link>
                            <Link :href="route('seo.sitemap')" target="_blank" class="inline-flex items-center gap-1 text-primary hover:underline">
                                sitemap.xml <ExternalLink class="size-3.5" />
                            </Link>
                        </div>
                        <InputError :message="form.errors.robots_txt" />
                    </div>

                    <div class="mt-6 inline-flex flex-wrap gap-2 rounded-lg border bg-muted/30 p-1">
                        <Button
                            v-for="(seo, id) in seoByLanguage"
                            :key="id"
                            type="button"
                            :variant="activeLanguageId === id ? 'default' : 'ghost'"
                            size="sm"
                            @click="activeLanguageId = id"
                        >
                            {{ seo.name }}
                        </Button>
                    </div>

                    <div v-if="activeSeo" class="mt-6 grid gap-4">
                        <div class="grid gap-2">
                            <Label>{{ t('admin.site.site_name') }}</Label>
                            <Input v-model="activeSeo.site_name" required />
                            <InputError :message="form.errors[`translations.${activeLanguageId}.site_name`]" />
                        </div>
                        <div class="grid gap-2">
                            <Label>{{ t('admin.site.meta_description') }}</Label>
                            <textarea
                                v-model="activeSeo.description"
                                rows="3"
                                class="flex min-h-[80px] w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2"
                            />
                            <InputError :message="form.errors[`translations.${activeLanguageId}.description`]" />
                        </div>
                        <div class="grid gap-2">
                            <Label>{{ t('admin.site.meta_keywords') }}</Label>
                            <Input v-model="activeSeo.keywords" placeholder="keyword1, keyword2" />
                            <InputError :message="form.errors[`translations.${activeLanguageId}.keywords`]" />
                        </div>
                        <div class="grid gap-2 border-t pt-4">
                            <Label>{{ t('admin.site.home_title') }}</Label>
                            <Input v-model="activeSeo.home_title" />
                            <InputError :message="form.errors[`translations.${activeLanguageId}.home_title`]" />
                        </div>
                        <div class="grid gap-2">
                            <Label>{{ t('admin.site.home_meta_description') }}</Label>
                            <textarea
                                v-model="activeSeo.home_meta_description"
                                rows="2"
                                class="flex min-h-[60px] w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2"
                            />
                            <InputError :message="form.errors[`translations.${activeLanguageId}.home_meta_description`]" />
                        </div>
                        <div class="grid gap-2 md:grid-cols-2 md:gap-4">
                            <div class="grid gap-2">
                                <Label>{{ t('admin.site.blog_index_title') }}</Label>
                                <Input v-model="activeSeo.blog_index_title" />
                            </div>
                            <div class="grid gap-2">
                                <Label>{{ t('admin.site.projects_index_title') }}</Label>
                                <Input v-model="activeSeo.projects_index_title" />
                            </div>
                        </div>
                        <div class="grid gap-2 md:grid-cols-2 md:gap-4">
                            <div class="grid gap-2">
                                <Label>{{ t('admin.site.blog_index_meta_description') }}</Label>
                                <textarea
                                    v-model="activeSeo.blog_index_meta_description"
                                    rows="2"
                                    class="flex min-h-[60px] w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2"
                                />
                            </div>
                            <div class="grid gap-2">
                                <Label>{{ t('admin.site.projects_index_meta_description') }}</Label>
                                <textarea
                                    v-model="activeSeo.projects_index_meta_description"
                                    rows="2"
                                    class="flex min-h-[60px] w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2"
                                />
                            </div>
                        </div>
                    </div>
                </section>

                <section class="rounded-xl border bg-card p-6 shadow-sm">
                    <h2 class="text-lg font-semibold">{{ t('admin.site.contacts') }}</h2>
                    <div class="mt-6 grid gap-4 md:grid-cols-2">
                        <div class="grid gap-2">
                            <Label for="phone">{{ t('admin.site.phone') }}</Label>
                            <Input id="phone" v-model="form.phone" placeholder="+7 (___) ___-__-__" />
                            <InputError :message="form.errors.phone" />
                        </div>
                        <div class="grid gap-2 md:col-span-2">
                            <Label for="address">{{ t('admin.site.address') }}</Label>
                            <textarea
                                id="address"
                                v-model="form.address"
                                rows="2"
                                class="flex min-h-[60px] w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2"
                            />
                            <InputError :message="form.errors.address" />
                        </div>
                    </div>
                </section>

                <section class="rounded-xl border bg-card p-6 shadow-sm">
                    <h2 class="text-lg font-semibold">{{ t('admin.site.social') }}</h2>
                    <div class="mt-6 grid gap-4 md:grid-cols-2">
                        <div v-for="network in socialNetworks" :key="network" class="grid gap-2">
                            <Label :for="network">{{ t(`admin.site.social_networks.${network}`) }}</Label>
                            <Input :id="network" v-model="form.social[network]" placeholder="https://" />
                            <InputError :message="form.errors[`social.${network}`]" />
                        </div>
                    </div>
                </section>

                <div class="sticky bottom-4 flex justify-end">
                    <Button type="submit" size="lg" :disabled="form.processing">{{ t('admin.site.save') }}</Button>
                </div>
            </form>
        </div>
    </AppLayout>
</template>
