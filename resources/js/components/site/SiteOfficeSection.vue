<script setup lang="ts">
import { useI18n } from '@/composables/useI18n';
import { useSite } from '@/composables/useSite';
import { siteAsset } from '@/composables/useSiteAsset';
import { computed } from 'vue';

const { t } = useI18n();
const { site } = useSite();

const mapUrl =
    'https://2gis.kz/turkestan/geo/70030076260448441/68.348996%2C43.273271?m=68.348959%2C43.2732%2F18.03';

const phoneHref = computed(() => {
    if (!site.value.phone) {
        return undefined;
    }

    const digits = site.value.phone.replace(/\D/g, '');

    return digits ? `tel:+${digits}` : undefined;
});

const socialNetworks = [
    { key: 'whatsapp', icon: 'fa-brands fa-whatsapp', label: 'WhatsApp' },
    { key: 'telegram', icon: 'fa-brands fa-telegram', label: 'Telegram' },
    { key: 'instagram', icon: 'fa-brands fa-instagram', label: 'Instagram' },
] as const;
</script>

<template>
    <section id="about" class="office-section mb--16">
        <div class="container">
            <div class="section-inner border-1 bg-white">
                <div class="office-section__head">
                    <div class="office-section__intro">
                        <p class="office-section__eyebrow">
                            <span class="office-section__eyebrow-mark" aria-hidden="true"></span>
                            {{ t('site.office.eyebrow') }}
                        </p>
                        <h2 class="office-section__title second-font font-semi-bold">
                            {{ t('site.office.title_prefix') }}
                            <span class="office-section__accent">{{ t('site.office.title_accent') }}</span>
                        </h2>
                    </div>
                    <div class="office-section__meta">
                        <p class="office-section__lead">{{ t('site.office.lead') }}</p>
                        <p class="office-section__status">
                            <span class="office-section__status-dot" aria-hidden="true"></span>
                            {{ t('site.office.status') }}
                        </p>
                    </div>
                </div>

                <div class="office-section__body square-dot">
                    <div class="office-panel">
                        <component
                            :is="phoneHref ? 'a' : 'div'"
                            :href="phoneHref"
                            class="office-cell office-cell--phone wow fadeInUp"
                            data-wow-delay=".1s"
                        >
                            <span class="office-cell__label">{{ t('site.office.phone_label') }}</span>
                            <span class="office-cell__value">{{ site.phone ?? t('site.office.phone_fallback') }}</span>
                            <span class="office-cell__hint">{{ t('site.office.phone_hint') }}</span>
                        </component>
                        <div class="office-cell office-cell--address wow fadeInUp" data-wow-delay=".2s">
                            <span class="office-cell__label">{{ t('site.office.address_label') }}</span>
                            <span class="office-cell__value">{{ site.address ?? t('site.office.address_fallback') }}</span>
                            <span class="office-cell__hint">{{ site.name }}</span>
                        </div>
                        <div class="office-cell office-cell--social wow fadeInUp" data-wow-delay=".3s">
                            <span class="office-cell__label">{{ t('site.office.social_label') }}</span>
                            <div class="office-cell__social">
                                <a
                                    v-for="network in socialNetworks"
                                    :key="network.key"
                                    v-show="site.social[network.key]"
                                    :href="site.social[network.key]!"
                                    class="office-social-link"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    :aria-label="network.label"
                                >
                                    <i :class="network.icon"></i>
                                    <span>{{ network.label }}</span>
                                </a>
                            </div>
                        </div>
                        <a
                            :href="mapUrl"
                            class="office-cell office-cell--map wow fadeInUp"
                            data-wow-delay=".4s"
                            target="_blank"
                            rel="noopener noreferrer"
                        >
                            <span class="office-cell__corner-icon" aria-hidden="true">
                                <i class="fa-solid fa-arrow-up-right-from-square"></i>
                            </span>
                            <span class="office-cell__label">{{ t('site.office.map_label') }}</span>
                            <span class="office-cell__value">{{ t('site.office.map_action') }}</span>
                            <span class="office-cell__hint">{{ t('site.office.map_hint') }}</span>
                        </a>
                    </div>

                    <figure class="office-visual wow fadeInRight" data-wow-delay=".2s">
                        <div class="office-visual__media">
                            <img
                                :src="siteAsset('images/office.jpeg')"
                                width="802"
                                height="1060"
                                :alt="t('site.office.image_alt', { name: site.name })"
                            />
                        </div>
                        <figcaption class="office-visual__caption">
                            <span class="office-visual__caption-mark" aria-hidden="true"></span>
                            <span class="office-visual__caption-text">{{ site.name.toUpperCase() }}</span>
                        </figcaption>
                    </figure>

                    <span class="square-shape top-left"></span>
                    <span class="square-shape top-right"></span>
                </div>

                <div class="bottom-shape-area square-dot">
                    <img :src="siteAsset('images/about/shape-02.svg')" alt="" />
                    <span class="square-shape top-left"></span>
                    <span class="square-shape bottom-left"></span>
                    <span class="square-shape top-right"></span>
                    <span class="square-shape bottom-right"></span>
                </div>
            </div>
        </div>
    </section>
</template>

<style>
.office-cell__corner-icon {
    position: absolute;
    top: 22px;
    right: 24px;
    font-size: 20px;
    line-height: 1;
    color: var(--color-heading-1);
    opacity: 0.55;
    transition: transform 0.35s ease, opacity 0.35s ease;
    pointer-events: none;
}

.office-cell--map:hover .office-cell__corner-icon {
    transform: translate(3px, -3px);
    opacity: 1;
}
</style>
