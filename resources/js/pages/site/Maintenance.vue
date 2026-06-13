<script setup lang="ts">
import UtilityLayout from '@/layouts/site/UtilityLayout.vue';
import { useI18n } from '@/composables/useI18n';
import { useSite } from '@/composables/useSite';
import type { SeoData } from '@/types';
import { computed } from 'vue';

defineProps<{
    seo: SeoData;
}>();

const { t } = useI18n();
const { site } = useSite();

const socialNetworks = [
    { key: 'whatsapp', icon: 'fa-brands fa-whatsapp', label: 'WhatsApp' },
    { key: 'telegram', icon: 'fa-brands fa-telegram', label: 'Telegram' },
    { key: 'instagram', icon: 'fa-brands fa-instagram', label: 'Instagram' },
] as const;

const visibleSocial = computed(() =>
    socialNetworks.filter((network) => site.value.social[network.key]),
);
</script>

<template>
    <UtilityLayout :seo="seo" variant="maintenance">
        <p class="utility-card__eyebrow">
            <span class="utility-card__status-dot" aria-hidden="true"></span>
            {{ t('site.maintenance.eyebrow') }}
        </p>
        <h1 class="utility-card__title utility-card__title--large second-font font-semi-bold" v-html="t('site.maintenance.heading')" />
        <p class="utility-card__desc">{{ t('site.maintenance.description') }}</p>
        <div
            class="utility-card__progress"
            role="progressbar"
            :aria-label="t('site.maintenance.progress_label')"
            aria-valuemin="0"
            aria-valuemax="100"
        >
            <span class="utility-card__progress-bar"></span>
        </div>
        <p class="utility-card__note">{{ t('site.maintenance.note') }}</p>
        <div v-if="visibleSocial.length" class="utility-card__contacts">
            <a
                v-for="network in visibleSocial"
                :key="network.key"
                :href="site.social[network.key]!"
                class="utility-social-btn"
                target="_blank"
                rel="noopener noreferrer"
                :aria-label="network.label"
            >
                <i :class="network.icon"></i>
            </a>
        </div>
    </UtilityLayout>
</template>
