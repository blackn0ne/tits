<script setup lang="ts">
import { useI18n } from '@/composables/useI18n';

withDefaults(
    defineProps<{
        title: string;
        bannerUrl: string | null;
        variant?: 'case' | 'article';
    }>(),
    {
        variant: 'case',
    },
);

const { t } = useI18n();
</script>

<template>
    <div
        :class="[
            variant === 'article' ? 'site-article__banner' : 'case-slide__media',
            !bannerUrl && (variant === 'article' ? 'site-article__banner--placeholder' : 'case-slide__media--placeholder'),
        ]"
    >
        <img v-if="bannerUrl" :src="bannerUrl" :alt="title" />
        <div
            v-else
            class="case-slide__placeholder"
            :class="{ 'case-slide__placeholder--article': variant === 'article' }"
        >
            <span class="case-slide__placeholder-mark" aria-hidden="true"></span>
            <span class="case-slide__placeholder-text">{{ t('site.projects.banner_soon') }}</span>
        </div>
    </div>
</template>
