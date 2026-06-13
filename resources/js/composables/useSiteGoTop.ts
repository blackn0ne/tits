import { onMounted, onUnmounted, ref } from 'vue';

const DEFAULT_FOOTER_SELECTOR = '.wpr-footer-area';
const DEFAULT_SHOW_AFTER = 100;

export function scrollToTop(): void {
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

export function useSiteGoTop(footerSelector: string = DEFAULT_FOOTER_SELECTOR, showAfter: number = DEFAULT_SHOW_AFTER) {
    const isVisible = ref(false);
    const progressAngle = ref('0deg');

    const update = (): void => {
        const scrollTop = window.scrollY;
        const viewportHeight = window.innerHeight;
        const documentHeight = document.documentElement.scrollHeight;
        const scrollableHeight = Math.max(documentHeight - viewportHeight, 1);
        const scrollPercent = (scrollTop / scrollableHeight) * 100;

        progressAngle.value = `${(scrollPercent / 100) * 360}deg`;

        const footer = document.querySelector(footerSelector);
        const footerTop = footer
            ? footer.getBoundingClientRect().top + scrollTop
            : Number.POSITIVE_INFINITY;
        const windowBottom = scrollTop + viewportHeight;

        isVisible.value = scrollTop > showAfter && windowBottom < footerTop;
    };

    onMounted(() => {
        update();
        window.addEventListener('scroll', update, { passive: true });
        window.addEventListener('resize', update, { passive: true });
    });

    onUnmounted(() => {
        window.removeEventListener('scroll', update);
        window.removeEventListener('resize', update);
    });

    return {
        isVisible,
        progressAngle,
        scrollToTop,
    };
}
