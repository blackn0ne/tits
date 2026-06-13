<script setup lang="ts">
import { useI18n } from '@/composables/useI18n';
import { scrollToTop } from '@/composables/useSiteGoTop';
import { useSite } from '@/composables/useSite';
import { siteAsset } from '@/composables/useSiteAsset';
import { Link } from '@inertiajs/vue3';

const { t } = useI18n();
const { site } = useSite();
const year = new Date().getFullYear();

const footerLogo = siteAsset('images/footer.png');
</script>

<template>
    <div id="contacts" class="wpr-footer-area">
        <div class="container">
            <div class="section-inner border-1">
                <div class="footer-logo-area square-dot">
                    <Link :href="route('home')"><img :src="footerLogo" :alt="site.name" /></Link>
                    <div class="shape one"><img :src="siteAsset('images/logo/shape-01.svg')" alt="" /></div>
                    <div class="shape two"><img :src="siteAsset('images/logo/shape-02.svg')" alt="" /></div>
                    <span class="square-shape top-left"></span>
                    <span class="square-shape top-right"></span>
                    <span class="square-shape bottom-left"></span>
                    <span class="square-shape bottom-right"></span>
                </div>
                <div class="copyright-area">
                    <div class="left-social-area"></div>
                    <p class="copyright">© {{ year }} {{ site.name }}. {{ t('site.footer.copyright') }}</p>
                    <button class="scroll-top-btn" type="button" @click="scrollToTop">
                        {{ t('site.footer.scroll_top') }}
                        <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M8 3.29688L7.64062 3.64062L1.39062 9.89062L2.10938 10.6094L8 4.71875L13.8906 10.6094L14.6094 9.89062L8.35938 3.64062L8 3.29688Z"
                                fill="black"
                            />
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>
