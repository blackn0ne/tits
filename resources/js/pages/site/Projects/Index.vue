<script setup lang="ts">
import SiteLayout from '@/layouts/site/SiteLayout.vue';
import ProjectCaseMedia from '@/components/site/ProjectCaseMedia.vue';
import { useI18n } from '@/composables/useI18n';
import { siteAsset } from '@/composables/useSiteAsset';
import { Link } from '@inertiajs/vue3';

type ProjectCategory = {
    id: number;
    name: string;
    slug: string;
    icon: string | null;
};

type Project = {
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
    projects: Project[];
    categories: ProjectCategory[];
    seo: SeoData;
}>();

const { t } = useI18n();
</script>

<template>
    <SiteLayout :seo="seo" body-class="home-bg main-home overflow-x-visible">
        <section class="site-page-section cases-section mb--16">
            <div class="container">
                <div class="section-inner border-1 bg-white">
                    <div class="cases-section__head">
                        <div class="cases-section__intro">
                            <p class="cases-section__eyebrow">
                                <span class="cases-section__eyebrow-mark" aria-hidden="true"></span>
                                {{ t('site.projects.eyebrow') }}
                            </p>
                            <h1 class="cases-section__title second-font font-semi-bold">
                                {{ t('site.projects.title_prefix') }}
                                <span class="cases-section__accent">{{ t('site.projects.title_accent') }}</span>
                            </h1>
                            <p class="cases-section__lead">{{ t('site.projects.lead') }}</p>
                        </div>
                    </div>

                    <div v-if="projects.length" class="site-projects-grid square-dot">
                        <article v-for="project in projects" :key="project.id" class="case-slide site-projects-grid__item">
                            <ProjectCaseMedia :title="project.title" :banner-url="project.banner_url" />
                            <div class="case-slide__panel">
                                <div v-if="project.category" class="slide-category">
                                    <span class="slide-category__icon" aria-hidden="true"></span>
                                    <span class="slide-category__label">{{ project.category.name }}</span>
                                </div>
                                <h2 class="case-slide__title second-font font-semi-bold">{{ project.title }}</h2>
                                <p class="case-slide__desc">{{ project.excerpt }}</p>
                                <Link :href="route('projects.show', { slug: project.slug })" class="case-slide__link">
                                    {{ t('site.projects.view') }}
                                    <i class="fa-solid fa-arrow-up-right-from-square" aria-hidden="true"></i>
                                </Link>
                            </div>
                        </article>
                        <span class="square-shape top-left"></span>
                        <span class="square-shape top-right"></span>
                    </div>

                    <p v-else class="site-page-empty">{{ t('site.projects.empty') }}</p>

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
