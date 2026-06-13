<script setup lang="ts">
import { useSite } from '@/composables/useSite';
import type { SeoData } from '@/types';
import { Head } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps<{
    title?: string;
    seo?: SeoData;
}>();

const { site } = useSite();

const resolved = computed<SeoData>(() => {
    if (props.seo) {
        return props.seo;
    }

    const pageTitle = props.title
        ? `${props.title}${site.value.title_separator ?? ' - '}${site.value.name}`
        : site.value.name;

    return {
        title: pageTitle,
        description: site.value.description ?? null,
        keywords: site.value.keywords ?? null,
        image: site.value.og_image_url ?? site.value.logo_url ?? null,
        canonical: null,
        robots: site.value.default_robots ?? 'index, follow',
        og_type: 'website',
        twitter_card: 'summary_large_image',
        twitter_site: site.value.twitter_handle
            ? site.value.twitter_handle.startsWith('@')
                ? site.value.twitter_handle
                : `@${site.value.twitter_handle}`
            : null,
        google_site_verification: site.value.google_site_verification ?? null,
        yandex_verification: site.value.yandex_verification ?? null,
    };
});
</script>

<template>
    <Head :title="resolved.title">
        <meta v-if="resolved.description" head-key="description" name="description" :content="resolved.description" />
        <meta v-if="resolved.keywords" head-key="keywords" name="keywords" :content="resolved.keywords" />
        <meta v-if="resolved.robots" head-key="robots" name="robots" :content="resolved.robots" />
        <link v-if="resolved.canonical" head-key="canonical" rel="canonical" :href="resolved.canonical" />
        <link v-if="site.favicon_url" head-key="favicon" rel="icon" :href="site.favicon_url" />

        <meta head-key="og:title" property="og:title" :content="resolved.title" />
        <meta head-key="og:description" property="og:description" :content="resolved.description ?? ''" />
        <meta head-key="og:type" property="og:type" :content="resolved.og_type ?? 'website'" />
        <meta v-if="resolved.canonical" head-key="og:url" property="og:url" :content="resolved.canonical" />
        <meta v-if="resolved.image" head-key="og:image" property="og:image" :content="resolved.image" />

        <meta head-key="twitter:card" name="twitter:card" :content="resolved.twitter_card ?? 'summary_large_image'" />
        <meta head-key="twitter:title" name="twitter:title" :content="resolved.title" />
        <meta head-key="twitter:description" name="twitter:description" :content="resolved.description ?? ''" />
        <meta v-if="resolved.image" head-key="twitter:image" name="twitter:image" :content="resolved.image" />
        <meta v-if="resolved.twitter_site" head-key="twitter:site" name="twitter:site" :content="resolved.twitter_site" />

        <meta
            v-if="resolved.google_site_verification"
            head-key="google-verification"
            name="google-site-verification"
            :content="resolved.google_site_verification"
        />
        <meta
            v-if="resolved.yandex_verification"
            head-key="yandex-verification"
            name="yandex-verification"
            :content="resolved.yandex_verification"
        />

        <slot />
    </Head>
</template>
