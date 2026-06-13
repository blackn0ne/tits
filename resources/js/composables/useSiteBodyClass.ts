export function setSiteBodyClass(...classes: string[]): void {
    document.body.className = classes.join(' ');
}

export function hideSitePreloader(): void {
    const preloader = document.querySelector<HTMLElement>('.preloader');

    if (!preloader) {
        return;
    }

    const gsap = (window as Window & { gsap?: { to: (target: string, vars: Record<string, unknown>) => void } }).gsap;

    if (gsap) {
        gsap.to('.preloader', {
            opacity: 0,
            duration: 0.4,
            ease: 'power2.out',
            onComplete: () => preloader.remove(),
        });

        return;
    }

    preloader.style.transition = 'opacity 0.4s ease';
    preloader.style.opacity = '0';

    window.setTimeout(() => preloader.remove(), 400);
}
