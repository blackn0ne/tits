<script setup lang="ts">
import SiteOfficeSection from '@/components/site/SiteOfficeSection.vue';
import { useI18n } from '@/composables/useI18n';
import { siteAsset } from '@/composables/useSiteAsset';
import { Link } from '@inertiajs/vue3';

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
}>();

const { t } = useI18n();

const heroTags = ['pc_repair', 'printing', 'web', 'mobile'] as const;

const serviceCards = [
    {
        id: 'print',
        icon: '01.svg',
        delay: '.2s',
        tagKeys: ['printing', 'copy', 'binding', 'cards', 'presentations'],
    },
    {
        id: 'repair',
        icon: '02.svg',
        delay: '.4s',
        tagKeys: ['pc', 'laptops', 'printers', 'setup', 'cleaning'],
    },
    {
        id: 'web',
        icon: '03.svg',
        delay: '.6s',
        tagKeys: ['sites', 'bots', 'shops', 'landings', 'crm'],
    },
    {
        id: 'mobile',
        icon: '04.svg',
        delay: '.8s',
        tagKeys: ['ios', 'android', 'mvp', 'uiux', 'support'],
    },
] as const;

const portfolioImages = [
    siteAsset('images/portfolio/01.webp'),
    siteAsset('images/portfolio/02.webp'),
    siteAsset('images/portfolio/03.webp'),
];

const projectImage = (index: number, bannerUrl: string | null): string =>
    bannerUrl ?? portfolioImages[index % portfolioImages.length];
</script>

<template>
    <section class="wpr-banner-area intro-hero">
        <div class="container">
            <div class="banner-content-area">
                <div class="bg-shape" aria-hidden="true">
                    <img :src="siteAsset('images/banner/shape/bg-shape.svg')" alt="" />
                </div>
                <div class="intro-hero__front wow fadeInUp" data-wow-delay=".2s">
                    <p class="intro-hero__eyebrow">
                        <span class="intro-hero__eyebrow-mark" aria-hidden="true"></span>
                        {{ t('site.hero.eyebrow') }}
                    </p>
                    <h1 class="intro-hero__title">
                        <span class="intro-hero__line">TURKISTAN</span>
                        <span class="intro-hero__line intro-hero__line--split">
                            <span class="intro-hero__accent">IT</span>
                            <span class="intro-hero__outline">SOLUTIONS</span>
                        </span>
                    </h1>
                    <p class="intro-hero__tagline">{{ t('site.hero.tagline') }}</p>
                    <p class="intro-hero__desc">{{ t('site.hero.description') }}</p>
                    <ul class="intro-hero__tags" :aria-label="t('site.hero.tags_label')">
                        <li v-for="tag in heroTags" :key="tag">{{ t(`site.hero.tags.${tag}`) }}</li>
                    </ul>
                </div>
            </div>
            <div class="bottom-shape-area square-dot bg-white">
                <img :src="siteAsset('images/about/shape-02.svg')" alt="" />
                <span class="square-shape top-left"></span>
                <span class="square-shape bottom-left"></span>
                <span class="square-shape top-right"></span>
                <span class="square-shape bottom-right"></span>
            </div>
        </div>
    </section>

    <section class="services-section mb--16" id="services">
        <div class="container">
            <div class="section-inner border-1 bg-white">
                <div class="section-title-area">
                    <p class="sub-title">{{ t('site.services.subtitle') }}</p>
                    <h2 class="section-title second-font font-semi-bold text-normal">
                        {{ t('site.services.title_line1') }} <br />
                        {{ t('site.services.title_line2') }}
                    </h2>
                    <p class="services-section__lead">{{ t('site.services.lead') }}</p>
                </div>
                <div class="services-section__grid">
                    <article
                        v-for="(card, index) in serviceCards"
                        :key="card.id"
                        class="service-card square-dot wow fadeInRight"
                        :data-wow-delay="card.delay"
                    >
                        <span class="service-card__num" aria-hidden="true">{{ String(index + 1).padStart(2, '0') }}</span>
                        <div class="service-card__icon">
                            <img :src="siteAsset(`images/icons/category/${card.icon}`)" alt="" />
                        </div>
                        <div class="service-card__body">
                            <h3 class="service-card__title h6 second-font font-semi-bold text-normal">
                                {{ t(`site.services.cards.${card.id}.title`) }}
                            </h3>
                            <p class="service-card__desc">{{ t(`site.services.cards.${card.id}.description`) }}</p>
                            <ul class="service-card__tags">
                                <li v-for="tagKey in card.tagKeys" :key="tagKey">
                                    {{ t(`site.services.cards.${card.id}.tags.${tagKey}`) }}
                                </li>
                            </ul>
                        </div>
                        <span class="square-shape top-left"></span>
                        <span v-if="index === 0" class="square-shape top-right"></span>
                        <span v-else class="square-shape top-right"></span>
                    </article>
                </div>
                <div class="bottom-shape-area square-dot">
                    <img :src="siteAsset('images/about/shape-02.svg')" alt="" />
                    <span class="square-shape top-left"></span>
                    <span class="square-shape bottom-left"></span>
                    <span class="square-shape top-right"></span>
                    <span class="square-shape bottom-right"></span>
                </div>
                <div class="services-section__decor" aria-hidden="true">
                    <img :src="siteAsset('images/services/decor-top-right.svg')" alt="" />
                </div>
            </div>
        </div>
    </section>

    <section id="cases" class="cases-section mb--16">
        <div class="container">
            <div class="section-inner border-1 bg-white">
                <div class="cases-section__head wow fadeInUp" data-wow-delay=".1s">
                    <div class="cases-section__intro">
                        <p class="cases-section__eyebrow">
                            <span class="cases-section__eyebrow-mark" aria-hidden="true"></span>
                            {{ t('site.projects.eyebrow') }}
                        </p>
                        <h2 class="cases-section__title second-font font-semi-bold">
                            {{ t('site.projects.title_prefix') }}
                            <span class="cases-section__accent">{{ t('site.projects.title_accent') }}</span>
                        </h2>
                        <p class="cases-section__lead">{{ t('site.projects.lead') }}</p>
                    </div>
                    <div class="cases-section__controls" :aria-label="t('site.slider.cases_nav')">
                        <button type="button" class="cases-slider-btn cases-slider-prev" :aria-label="t('site.slider.cases_prev')">
                            <svg width="17" height="30" viewBox="0 0 17 30" fill="none" aria-hidden="true">
                                <path
                                    d="M14.6094 0L0.859375 13.75L0 14.6484L0.859375 15.5469L14.6094 29.2969L16.4062 27.5L3.55469 14.6484L16.4062 1.79688L14.6094 0Z"
                                    fill="currentColor"
                                />
                            </svg>
                        </button>
                        <button type="button" class="cases-slider-btn cases-slider-next" :aria-label="t('site.slider.cases_next')">
                            <svg width="17" height="30" viewBox="0 0 17 30" fill="none" aria-hidden="true">
                                <path
                                    d="M1.79688 0L0 1.79688L12.8516 14.6484L0 27.5L1.79688 29.2969L15.5469 15.5469L16.4062 14.6484L15.5469 13.75L1.79688 0Z"
                                    fill="currentColor"
                                />
                            </svg>
                        </button>
                    </div>
                </div>

                <div class="cases-carousel square-dot wow fadeInUp" data-wow-delay=".25s">
                    <div class="swiper cases-slider">
                        <div class="swiper-wrapper">
                            <div v-for="(project, index) in projects" :key="project.id" class="swiper-slide">
                                <article class="case-slide">
                                    <div class="case-slide__media">
                                        <img :src="projectImage(index, project.banner_url)" :alt="project.title" />
                                    </div>
                                    <div class="case-slide__panel">
                                        <div v-if="project.category" class="slide-category">
                                            <span class="slide-category__icon" aria-hidden="true"></span>
                                            <span class="slide-category__label">{{ project.category.name }}</span>
                                        </div>
                                        <h3 class="case-slide__title second-font font-semi-bold">{{ project.title }}</h3>
                                        <p class="case-slide__desc">{{ project.excerpt }}</p>
                                        <Link :href="route('projects.show', { slug: project.slug })" class="case-slide__link">
                                            {{ t('site.projects.view') }}
                                            <i class="fa-solid fa-arrow-up-right-from-square" aria-hidden="true"></i>
                                        </Link>
                                    </div>
                                </article>
                            </div>
                        </div>
                    </div>
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

    <section id="blog" class="blog-section mb--16">
        <div class="container">
            <div class="section-inner border-1 bg-white">
                <div class="blog-section__head wow fadeInUp" data-wow-delay=".1s">
                    <div class="blog-section__intro">
                        <p class="blog-section__eyebrow">
                            <span class="blog-section__eyebrow-mark" aria-hidden="true"></span>
                            {{ t('site.blog.eyebrow') }}
                        </p>
                        <h2 class="blog-section__title second-font font-semi-bold">
                            {{ t('site.blog.title_prefix') }}
                            <span class="blog-section__accent">{{ t('site.blog.title_accent') }}</span>
                        </h2>
                        <p class="blog-section__lead">{{ t('site.blog.lead') }}</p>
                    </div>
                    <div class="blog-section__controls" :aria-label="t('site.slider.blog_nav')">
                        <button type="button" class="blog-slider-btn blog-slider-prev" :aria-label="t('site.slider.blog_prev')">
                            <svg width="17" height="30" viewBox="0 0 17 30" fill="none" aria-hidden="true">
                                <path
                                    d="M14.6094 0L0.859375 13.75L0 14.6484L0.859375 15.5469L14.6094 29.2969L16.4062 27.5L3.55469 14.6484L16.4062 1.79688L14.6094 0Z"
                                    fill="currentColor"
                                />
                            </svg>
                        </button>
                        <button type="button" class="blog-slider-btn blog-slider-next" :aria-label="t('site.slider.blog_next')">
                            <svg width="17" height="30" viewBox="0 0 17 30" fill="none" aria-hidden="true">
                                <path
                                    d="M1.79688 0L0 1.79688L12.8516 14.6484L0 27.5L1.79688 29.2969L15.5469 15.5469L16.4062 14.6484L15.5469 13.75L1.79688 0Z"
                                    fill="currentColor"
                                />
                            </svg>
                        </button>
                    </div>
                </div>

                <div class="blog-carousel square-dot wow fadeInUp" data-wow-delay=".25s">
                    <div class="swiper blog-slider">
                        <div class="swiper-wrapper">
                            <div v-for="post in posts" :key="post.id" class="swiper-slide">
                                <article class="blog-card">
                                    <div v-if="post.category" class="slide-category">
                                        <span class="slide-category__icon" aria-hidden="true"></span>
                                        <span class="slide-category__label">{{ post.category.name }}</span>
                                    </div>
                                    <p v-if="post.published_at_label" class="blog-card__meta">
                                        <time :datetime="post.published_at ?? undefined">{{ post.published_at_label }}</time>
                                    </p>
                                    <h3 class="blog-card__title second-font font-semi-bold">
                                        <Link :href="route('blog.show', { slug: post.slug })">{{ post.title }}</Link>
                                    </h3>
                                    <p class="blog-card__excerpt">{{ post.excerpt }}</p>
                                    <Link :href="route('blog.show', { slug: post.slug })" class="blog-card__link">
                                        {{ t('site.blog.read_more') }}
                                        <i class="fa-solid fa-arrow-up-right-from-square" aria-hidden="true"></i>
                                    </Link>
                                </article>
                            </div>
                        </div>
                    </div>
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

    <SiteOfficeSection />
</template>
