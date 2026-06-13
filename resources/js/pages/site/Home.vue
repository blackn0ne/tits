<script setup lang="ts">
import SiteHomeContent from '@/components/site/SiteHomeContent.vue';
import SiteLayout from '@/layouts/site/SiteLayout.vue';
import { scrollToAnchor } from '@/composables/useSiteAnchorScroll';
import { syncSiteWow } from '@/composables/useSiteScripts';
import { useI18n } from '@/composables/useI18n';
import type { SeoData } from '@/types';
import { nextTick, onMounted } from 'vue';

type ContentItem = {
    id: number;
    title: string;
    slug: string;
    excerpt: string;
    published_at: string | null;
    published_at_label: string | null;
    banner_url: string | null;
    category: { name: string; slug: string } | null;
};

defineProps<{
    posts: ContentItem[];
    projects: ContentItem[];
    seo: SeoData;
}>();

const { t } = useI18n();

onMounted(async () => {
    await nextTick();
    window.setTimeout(() => {
        syncSiteWow();
    }, 350);

    if (!window.location.hash) {
        return;
    }

    window.setTimeout(() => {
        scrollToAnchor(window.location.hash);
    }, 300);
});
</script>

<template>
    <SiteLayout :seo="seo">
        <SiteHomeContent :posts="posts" :projects="projects" />
    </SiteLayout>
</template>
