<script setup lang="ts">
import UtilityLayout from '@/layouts/site/UtilityLayout.vue';
import { useI18n } from '@/composables/useI18n';
import { Link } from '@inertiajs/vue3';

import type { SeoData } from '@/types';

defineProps<{
    seo: SeoData;
}>();

const { t } = useI18n();
</script>

<template>
    <UtilityLayout :seo="seo" variant="404">
        <p class="utility-card__eyebrow">{{ t('site.not_found.eyebrow') }}</p>
        <h1 class="utility-card__code" aria-label="404">
            <span>4</span><span class="utility-card__code-accent">0</span><span>4</span>
        </h1>
        <h2 class="utility-card__title second-font font-semi-bold">{{ t('site.not_found.heading') }}</h2>
        <p class="utility-card__desc">{{ t('site.not_found.description') }}</p>
        <div class="utility-card__actions">
            <Link :href="route('home')" class="wpr-btn btn-primary">{{ t('site.not_found.home') }}</Link>
            <Link :href="`${route('home')}#services`" class="wpr-btn btn-white">{{ t('site.not_found.services') }}</Link>
        </div>
    </UtilityLayout>
</template>
