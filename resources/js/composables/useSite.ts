import { usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import type { SharedData, SiteSettings } from '@/types';

const defaultSite: SiteSettings = {
    name: 'TITS',
    description: null,
    keywords: null,
    logo_url: null,
    favicon_url: null,
    phone: null,
    address: null,
    social: {
        facebook: null,
        instagram: null,
        telegram: null,
        whatsapp: null,
        youtube: null,
        tiktok: null,
    },
};

export function useSite() {
    const page = usePage<SharedData>();

    const site = computed(
        () =>
            ({
                ...defaultSite,
                ...(page.props.site ?? {}),
                name: page.props.site?.name ?? page.props.name ?? defaultSite.name,
            }) as SiteSettings,
    );

    return { site };
}
