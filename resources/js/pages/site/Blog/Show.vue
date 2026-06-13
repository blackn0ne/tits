<script setup lang="ts">
import SiteLayout from '@/layouts/site/SiteLayout.vue';
import { useI18n } from '@/composables/useI18n';
import { siteAsset } from '@/composables/useSiteAsset';
import { Link } from '@inertiajs/vue3';

type BlogPost = {
    id: number;
    title: string;
    slug: string;
    content: string;
    published_at: string | null;
    published_at_label: string | null;
    banner_url: string | null;
    category: { name: string; slug: string } | null;
};

import type { SeoData } from '@/types';

defineProps<{
    post: BlogPost;
    seo: SeoData;
}>();

const { t } = useI18n();
</script>

<template>
    <SiteLayout :seo="seo" body-class="home-bg main-home overflow-x-visible">
        <section class="site-page-section blog-section mb--16">
            <div class="container">
                <div class="section-inner border-1 bg-white">
                    <div class="site-article">
                        <Link :href="route('blog.index')" class="site-article__back">
                            <i class="fa-solid fa-arrow-left" aria-hidden="true"></i>
                            {{ t('site.blog.back') }}
                        </Link>

                        <div v-if="post.published_at_label || post.category" class="site-article__meta">
                            <p v-if="post.published_at_label" class="blog-card__meta">
                                <time :datetime="post.published_at ?? undefined">{{ post.published_at_label }}</time>
                            </p>
                            <div v-if="post.category" class="slide-category">
                                <span class="slide-category__icon" aria-hidden="true"></span>
                                <span class="slide-category__label">{{ post.category.name }}</span>
                            </div>
                        </div>

                        <h1 class="site-article__title second-font font-semi-bold">{{ post.title }}</h1>

                        <div v-if="post.banner_url" class="site-article__banner">
                            <img :src="post.banner_url" :alt="post.title" />
                        </div>

                        <div class="site-article__content" v-html="post.content"></div>
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
    </SiteLayout>
</template>
