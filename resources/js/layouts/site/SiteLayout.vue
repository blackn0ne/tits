<script setup lang="ts">
import SiteFooter from '@/components/site/SiteFooter.vue';
import SiteHeader from '@/components/site/SiteHeader.vue';
import SiteHead from '@/components/site/SiteHead.vue';
import { setSiteBodyClass } from '@/composables/useSiteBodyClass';
import { useSiteGoTop } from '@/composables/useSiteGoTop';
import { loadSiteScripts } from '@/composables/useSiteScripts';
import { useI18n } from '@/composables/useI18n';
import { onMounted, onUnmounted } from 'vue';

import type { SeoData } from '@/types';

const props = defineProps<{
    title?: string;
    seo?: SeoData;
    bodyClass?: string;
}>();

const bodyClasses = () => (props.bodyClass ?? 'home-bg main-home onepage overflow-x-visible').split(' ').filter(Boolean);

const { isVisible, progressAngle, scrollToTop } = useSiteGoTop();
const { t } = useI18n();

onMounted(() => {
    setSiteBodyClass(...bodyClasses());
    loadSiteScripts();
});

onUnmounted(() => {
    document.body.className = '';
});
</script>

<template>
    <div>
        <SiteHead :seo="seo" :title="title" />
        <div :class="bodyClass ?? 'home-bg main-home onepage overflow-x-visible'">
            <SiteHeader />
            <slot />
            <SiteFooter />
            <button
                id="goTop"
                type="button"
                data-vue-managed
                :class="{ show: isVisible }"
                :aria-hidden="!isVisible"
                :aria-label="t('site.footer.scroll_top')"
                @click="scrollToTop"
            >
                <span class="border-progress" :style="{ '--progress-angle': progressAngle }"></span>
                <span class="ic-wrap">
                    <span class="icon icon-long-arrow-alt-up-solid"><i class="fa-sharp fa-regular fa-arrow-up-long"></i></span>
                </span>
            </button>
        </div>
    </div>
</template>
