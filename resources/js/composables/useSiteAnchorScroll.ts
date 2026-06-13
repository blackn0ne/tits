const HEADER_OFFSET = 100;

export function scrollToAnchor(hash: string): void {
    const id = hash.replace('#', '');

    if (!id) {
        return;
    }

    const element = document.getElementById(id);

    if (!element) {
        return;
    }

    const top = element.getBoundingClientRect().top + window.scrollY - HEADER_OFFSET;

    window.scrollTo({ top, behavior: 'smooth' });
    history.replaceState(null, '', `#${id}`);
}

export function useSiteAnchorScroll() {
    const navigate = (hash: string, event?: Event): void => {
        event?.preventDefault();
        scrollToAnchor(hash);
        document.body.classList.remove('mobile-menu-active');
    };

    return { navigate, scrollToAnchor };
}
