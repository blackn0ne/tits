<script setup lang="ts">
import SiteHomeContent from '@/components/site/SiteHomeContent.vue';
import SiteLayout from '@/layouts/site/SiteLayout.vue';
import { scrollToAnchor } from '@/composables/useSiteAnchorScroll';
import { initSiteSliders, loadSiteScripts, syncSiteWow } from '@/composables/useSiteScripts';
import { useI18n } from '@/composables/useI18n';
import type { SeoData } from '@/types';
import { nextTick, onMounted, onUnmounted } from 'vue';

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
    await loadSiteScripts();
    await nextTick();

    requestAnimationFrame(() => {
        initSiteSliders();
        syncSiteWow();
    });

    if (!window.location.hash) {
        return;
    }

    window.setTimeout(() => {
        scrollToAnchor(window.location.hash);
    }, 300);
});

onUnmounted(() => {
    document.querySelector<HTMLElement & { swiper?: { destroy: (deleteInstance?: boolean, cleanStyles?: boolean) => void } }>('.cases-slider')?.swiper?.destroy(true, true);
    document.querySelector<HTMLElement & { swiper?: { destroy: (deleteInstance?: boolean, cleanStyles?: boolean) => void } }>('.blog-slider')?.swiper?.destroy(true, true);
});
</script>

<template>
    <SiteLayout :seo="seo">
        <SiteHomeContent :posts="posts" :projects="projects" />
    </SiteLayout>
</template>
