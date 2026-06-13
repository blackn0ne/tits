<script setup lang="ts">
import SiteLayout from '@/layouts/site/SiteLayout.vue';
import { useI18n } from '@/composables/useI18n';
import { siteAsset } from '@/composables/useSiteAsset';
import { Link } from '@inertiajs/vue3';

type BlogCategory = {
    id: number;
    name: string;
    slug: string;
    icon: string | null;
};

type BlogPost = {
    id: number;
    title: string;
    slug: string;
    excerpt: string;
    published_at: string | null;
    published_at_label: string | null;
    banner_url: string | null;
    category: { name: string; slug: string } | null;
};

import type { SeoData } from '@/types';

defineProps<{
    posts: BlogPost[];
    categories: BlogCategory[];
    seo: SeoData;
}>();

const { t } = useI18n();
</script>

<template>
    <SiteLayout :seo="seo" body-class="home-bg main-home overflow-x-visible">
        <section class="site-page-section blog-section mb--16">
            <div class="container">
                <div class="section-inner border-1 bg-white">
                    <div class="blog-section__head">
                        <div class="blog-section__intro">
                            <p class="blog-section__eyebrow">
                                <span class="blog-section__eyebrow-mark" aria-hidden="true"></span>
                                {{ t('site.blog.eyebrow') }}
                            </p>
                            <h1 class="blog-section__title second-font font-semi-bold">
                                {{ t('site.blog.title_prefix') }}
                                <span class="blog-section__accent">{{ t('site.blog.title_accent') }}</span>
                            </h1>
                            <p class="blog-section__lead">{{ t('site.blog.lead') }}</p>
                        </div>
                    </div>

                    <div v-if="posts.length" class="site-cards-grid square-dot">
                        <article v-for="post in posts" :key="post.id" class="blog-card site-cards-grid__item">
                            <div v-if="post.category" class="slide-category">
                                <span class="slide-category__icon" aria-hidden="true"></span>
                                <span class="slide-category__label">{{ post.category.name }}</span>
                            </div>
                            <p v-if="post.published_at_label" class="blog-card__meta">
                                <time :datetime="post.published_at ?? undefined">{{ post.published_at_label }}</time>
                            </p>
                            <h2 class="blog-card__title second-font font-semi-bold">
                                <Link :href="route('blog.show', { slug: post.slug })">{{ post.title }}</Link>
                            </h2>
                            <p class="blog-card__excerpt">{{ post.excerpt }}</p>
                            <Link :href="route('blog.show', { slug: post.slug })" class="blog-card__link">
                                {{ t('site.blog.read_more') }}
                                <i class="fa-solid fa-arrow-up-right-from-square" aria-hidden="true"></i>
                            </Link>
                        </article>
                        <span class="square-shape top-left"></span>
                        <span class="square-shape top-right"></span>
                    </div>

                    <p v-else class="site-page-empty">{{ t('site.blog.empty') }}</p>

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
    </SiteLayout>
</template>
