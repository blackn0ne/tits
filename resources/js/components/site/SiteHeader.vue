<script setup lang="ts">
import SiteLocaleSwitcher from '@/components/site/SiteLocaleSwitcher.vue';
import { useI18n } from '@/composables/useI18n';
import { useSite } from '@/composables/useSite';
import { useSiteAnchorScroll } from '@/composables/useSiteAnchorScroll';
import { siteAsset } from '@/composables/useSiteAsset';
import { Link, usePage } from '@inertiajs/vue3';
import { computed, onMounted, onUnmounted } from 'vue';

const { t } = useI18n();
const { site } = useSite();
const { navigate } = useSiteAnchorScroll();
const page = usePage();

const logoUrl = computed(() => site.value.logo_url ?? siteAsset('images/logo/01.svg'));
const isHome = computed(() => page.component === 'site/Home');
const languages = computed(() => page.props.languages ?? []);

const socialNetworks = [
    { key: 'instagram', icon: 'fa-brands fa-instagram', label: 'Instagram' },
    { key: 'whatsapp', icon: 'fa-brands fa-whatsapp', label: 'WhatsApp' },
    { key: 'telegram', icon: 'fa-brands fa-telegram', label: 'Telegram' },
] as const;

const anchorItems = computed(() => [
    { hash: '#about', label: t('site.nav.about') },
    { hash: '#services', label: t('site.nav.services') },
]);

const pageItems = computed(() => [
    { routeName: 'projects.index', label: t('site.nav.projects') },
    { routeName: 'blog.index', label: t('site.nav.blog') },
]);

const contactsItem = computed(() => ({
    hash: '#contacts',
    label: t('site.nav.contacts'),
}));

const handleAnchorClick = (hash: string, event?: Event): void => {
    if (isHome.value) {
        navigate(hash, event);
        closeMobileMenu();

        return;
    }
};

const hasSocialLinks = computed(() => socialNetworks.some((network) => Boolean(site.value.social[network.key])));

const toggleMobileMenu = (): void => {
    document.body.classList.toggle('mobile-menu-active');
};

const closeMobileMenu = (): void => {
    document.body.classList.remove('mobile-menu-active');
};

onMounted(() => {
    const menuBtn = document.getElementById('menu-btn');
    menuBtn?.addEventListener('click', toggleMobileMenu);
});

onUnmounted(() => {
    const menuBtn = document.getElementById('menu-btn');
    menuBtn?.removeEventListener('click', toggleMobileMenu);
    document.body.classList.remove('mobile-menu-active');
});
</script>

<template>
    <header class="header-style-one">
        <div class="container">
            <div class="header-style-one-wrapper">
                <div class="left-area square-dot">
                    <div class="logo-area">
                        <Link :href="route('home')" class="logo">
                            <img :src="logoUrl" :alt="site.name" />
                        </Link>
                    </div>
                    <span class="square-shape top-left"></span>
                    <span class="square-shape bottom-left"></span>
                    <span class="square-shape top-right"></span>
                    <span class="square-shape bottom-right"></span>
                </div>
                <nav class="main-nav-area">
                    <ul class="list-unstyled wpr-desktop-menu">
                        <li v-for="item in anchorItems" :key="item.hash" class="menu-item">
                            <Link
                                v-if="!isHome"
                                class="main-element"
                                :href="`${route('home')}${item.hash}`"
                            >
                                {{ item.label }}
                            </Link>
                            <a
                                v-else
                                class="main-element"
                                :href="item.hash"
                                @click="handleAnchorClick(item.hash, $event)"
                            >
                                {{ item.label }}
                            </a>
                        </li>
                        <li v-for="item in pageItems" :key="item.routeName" class="menu-item">
                            <Link class="main-element" :href="route(item.routeName)">{{ item.label }}</Link>
                        </li>
                        <li class="menu-item">
                            <Link
                                v-if="!isHome"
                                class="main-element"
                                :href="`${route('home')}${contactsItem.hash}`"
                            >
                                {{ contactsItem.label }}
                            </Link>
                            <a
                                v-else
                                class="main-element"
                                :href="contactsItem.hash"
                                @click="handleAnchorClick(contactsItem.hash, $event)"
                            >
                                {{ contactsItem.label }}
                            </a>
                        </li>
                    </ul>
                </nav>
                <div class="button-area-start square-dot">
                    <div class="header-social-links">
                        <a
                            v-for="network in socialNetworks"
                            :key="network.key"
                            v-show="site.social[network.key]"
                            :href="site.social[network.key]!"
                            class="wpr-btn btn-primary header-social-btn"
                            target="_blank"
                            rel="noopener noreferrer"
                            :aria-label="network.label"
                        >
                            <i :class="network.icon"></i>
                        </a>
                        <SiteLocaleSwitcher />
                    </div>
                    <div id="menu-btn" class="menu-btn d-flex d-lg-none d-md-flex d-sm-flex">
                        <span class="line one"></span>
                        <span class="line two"></span>
                    </div>
                    <span class="square-shape top-left"></span>
                    <span class="square-shape bottom-left"></span>
                    <span class="square-shape top-right"></span>
                    <span class="square-shape bottom-right"></span>
                </div>
                <div id="side-bar" class="side-bar">
                    <div class="sidebar-inner">
                        <div class="mobile-menu-main">
                            <nav class="nav-main mainmenu-nav">
                                <ul id="mobile-menu" class="list-unstyled wpr-desktop-menu">
                                    <li v-for="item in anchorItems" :key="`m-${item.hash}`" class="menu-item">
                                        <Link
                                            v-if="!isHome"
                                            class="main-element"
                                            :href="`${route('home')}${item.hash}`"
                                            @click="closeMobileMenu"
                                        >
                                            {{ item.label }}
                                        </Link>
                                        <a
                                            v-else
                                            class="main-element"
                                            :href="item.hash"
                                            @click="handleAnchorClick(item.hash, $event)"
                                        >
                                            {{ item.label }}
                                        </a>
                                    </li>
                                    <li v-for="item in pageItems" :key="`m-${item.routeName}`" class="menu-item">
                                        <Link
                                            class="main-element"
                                            :href="route(item.routeName)"
                                            @click="closeMobileMenu"
                                        >
                                            {{ item.label }}
                                        </Link>
                                    </li>
                                    <li class="menu-item">
                                        <Link
                                            v-if="!isHome"
                                            class="main-element"
                                            :href="`${route('home')}${contactsItem.hash}`"
                                            @click="closeMobileMenu"
                                        >
                                            {{ contactsItem.label }}
                                        </Link>
                                        <a
                                            v-else
                                            class="main-element"
                                            :href="contactsItem.hash"
                                            @click="handleAnchorClick(contactsItem.hash, $event)"
                                        >
                                            {{ contactsItem.label }}
                                        </a>
                                    </li>
                                </ul>
                            </nav>
                            <div
                                v-if="hasSocialLinks || languages.length"
                                class="social-wrapper-one mobile-menu-actions header-social-links"
                            >
                                <a
                                    v-for="network in socialNetworks"
                                    :key="`mobile-${network.key}`"
                                    v-show="site.social[network.key]"
                                    :href="site.social[network.key]!"
                                    class="wpr-btn btn-primary header-social-btn"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    :aria-label="network.label"
                                    @click="closeMobileMenu"
                                >
                                    <i :class="network.icon"></i>
                                </a>
                                <SiteLocaleSwitcher @switched="closeMobileMenu" />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>
</template>
