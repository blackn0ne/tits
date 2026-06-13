<script setup lang="ts">
import SiteHead from '@/components/site/SiteHead.vue';
import { setSiteBodyClass } from '@/composables/useSiteBodyClass';
import { loadSiteScripts } from '@/composables/useSiteScripts';
import { useSite } from '@/composables/useSite';
import { siteAsset } from '@/composables/useSiteAsset';
import { Link } from '@inertiajs/vue3';
import { computed, onMounted, onUnmounted } from 'vue';

import type { SeoData } from '@/types';

const props = defineProps<{
    title?: string;
    seo?: SeoData;
    variant?: '404' | 'maintenance';
}>();

const { site } = useSite();

const logoUrl = computed(() => site.value.logo_url ?? siteAsset('images/logo/01.svg'));
const year = new Date().getFullYear();

onMounted(() => {
    setSiteBodyClass('utility-page', 'home-bg');
    loadSiteScripts();
});

onUnmounted(() => {
    document.body.className = '';
});
</script>

<template>
    <div>
        <SiteHead :seo="seo" :title="title" robots="noindex, nofollow" />
        <div class="utility-page home-bg">
            <main class="utility-main">
                <div class="container">
                    <div class="utility-layout section-inner bg-white">
                        <header class="utility-header square-dot border-1 bg-white">
                            <Link :href="route('home')" class="utility-header__logo">
                                <img :src="logoUrl" :alt="site.name" />
                            </Link>
                            <span class="square-shape top-left"></span>
                            <span class="square-shape bottom-left"></span>
                            <span class="square-shape top-right"></span>
                            <span class="square-shape bottom-right"></span>
                        </header>

                        <div class="utility-card">
                            <div class="utility-card__bg" aria-hidden="true">
                                <img :src="siteAsset('images/banner/shape/bg-shape.svg')" alt="" />
                            </div>
                            <div
                                class="utility-card__content"
                                :class="variant === '404' ? 'utility-card__content--404' : undefined"
                            >
                                <slot />
                            </div>
                        </div>

                        <div class="bottom-shape-area square-dot border-1 bg-white">
                            <img :src="siteAsset('images/about/shape-02.svg')" alt="" />
                            <span class="square-shape top-left"></span>
                            <span class="square-shape bottom-left"></span>
                            <span class="square-shape top-right"></span>
                            <span class="square-shape bottom-right"></span>
                        </div>
                    </div>
                </div>
            </main>

            <footer class="utility-footer">
                <p>© {{ year }} {{ site.name }}</p>
            </footer>
        </div>
    </div>
</template>
