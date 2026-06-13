<script setup lang="ts">
import SeoHead from '@/components/SeoHead.vue';
import { siteAsset } from '@/composables/useSiteAsset';
import type { SeoData } from '@/types';

defineProps<{
    title?: string;
    seo?: SeoData;
    robots?: string;
}>();
</script>

<template>
    <SeoHead :title="title" :seo="seo">
        <meta v-if="robots" head-key="robots" name="robots" :content="robots" />
        <link head-key="site-swiper" rel="stylesheet" :href="siteAsset('css/plugins/swiper.min.css')" />
        <link head-key="site-metismenu" rel="stylesheet" :href="siteAsset('css/plugins/metismenu.css')" />
        <link head-key="site-bootstrap" rel="stylesheet" :href="siteAsset('css/vendor/bootstrap.min.css')" />
        <link head-key="site-animate" rel="stylesheet" :href="siteAsset('css/vendor/animate.css')" />
        <link head-key="site-odometer" rel="stylesheet" :href="siteAsset('css/plugins/odometer.css')" />
        <link head-key="site-fontawesome" rel="stylesheet" :href="siteAsset('css/plugins/fontawesome.min.css')" />
        <link head-key="site-style" rel="stylesheet" :href="siteAsset('css/style.css')" />
    </SeoHead>
</template>
