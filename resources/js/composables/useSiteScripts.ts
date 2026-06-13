import { hideSitePreloader } from '@/composables/useSiteBodyClass';

const SITE_SCRIPTS = [
    '/site/assets/js/plugins/jquery.min.js',
    '/site/assets/js/plugins/bootstrap.min.js',
    '/site/assets/js/plugins/metismenu.js',
    '/site/assets/js/vendor/jqueryui.js',
    '/site/assets/js/vendor/waypoint.js',
    '/site/assets/js/plugins/swiper.js',
    '/site/assets/js/plugins/gsap.min.js',
    '/site/assets/js/plugins/scrolltigger.js',
    '/site/assets/js/plugins/smoothscroll.js',
    '/site/assets/js/vendor/split-text.js',
    '/site/assets/js/vendor/split-type.js',
    '/site/assets/js/vendor/wow.js',
    '/site/assets/js/vendor/text-plugin.js',
    '/site/assets/js/plugins/odometer.js',
    '/site/assets/js/plugins/contact-form.js',
    '/site/assets/js/main.js',
] as const;

const PRELOADER_MIN_DELAY_MS = 2000;

let scriptsLoaded = false;
let scriptsLoadStartedAt: number | null = null;

function delay(ms: number): Promise<void> {
    return new Promise((resolve) => {
        window.setTimeout(resolve, ms);
    });
}

function loadScript(src: string): Promise<void> {
    return new Promise((resolve, reject) => {
        const existing = document.querySelector(`script[src="${src}"]`);

        if (existing) {
            resolve();
            return;
        }

        const script = document.createElement('script');
        script.src = src;
        script.defer = true;
        script.onload = () => resolve();
        script.onerror = () => reject(new Error(`Failed to load ${src}`));
        document.body.appendChild(script);
    });
}

export function syncSiteWow(): void {
    const wow = (window as Window & { __siteWow?: { sync: () => void } }).__siteWow;

    wow?.sync();
}

export async function loadSiteScripts(): Promise<void> {
    if (scriptsLoaded) {
        return;
    }

    scriptsLoadStartedAt = (window as Window & { __preloaderStartedAt?: number }).__preloaderStartedAt ?? Date.now();

    for (const src of SITE_SCRIPTS) {
        await loadScript(src);
    }

    scriptsLoaded = true;

    const elapsed = Date.now() - (scriptsLoadStartedAt ?? Date.now());
    const remaining = Math.max(0, PRELOADER_MIN_DELAY_MS - elapsed);

    await delay(remaining);

    requestAnimationFrame(() => {
        hideSitePreloader();
        syncSiteWow();
    });
}
