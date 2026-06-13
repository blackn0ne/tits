import type { LucideIcon } from 'lucide-vue-next';

export interface Auth {
    user: User;
}

export interface BreadcrumbItem {
    title: string;
    href: string;
}

export interface NavItem {
    title: string;
    href: string;
    icon?: LucideIcon;
    isActive?: boolean;
}

export interface Language {
    name: string;
    code: string;
}

export interface SiteSocialLinks {
    facebook: string | null;
    instagram: string | null;
    telegram: string | null;
    whatsapp: string | null;
    youtube: string | null;
    tiktok: string | null;
}

export interface SiteSettings {
    name: string;
    description: string | null;
    keywords: string | null;
    logo_url: string | null;
    favicon_url: string | null;
    phone: string | null;
    address: string | null;
    social: SiteSocialLinks;
    title_separator: string;
    default_robots: string;
    og_image_url: string | null;
    twitter_handle: string | null;
    google_site_verification: string | null;
    yandex_verification: string | null;
    sitemap_enabled: boolean;
    robots_txt: string | null;
    home_title: string | null;
    home_meta_description: string | null;
    blog_index_title: string | null;
    blog_index_meta_description: string | null;
    projects_index_title: string | null;
    projects_index_meta_description: string | null;
}

export interface SeoData {
    title: string;
    description: string | null;
    keywords: string | null;
    image: string | null;
    canonical: string | null;
    robots: string | null;
    og_type: string;
    twitter_card: string;
    twitter_site: string | null;
    google_site_verification: string | null;
    yandex_verification: string | null;
}

export interface SharedData {
    name: string;
    site: SiteSettings;
    quote: { message: string; author: string };
    auth: Auth;
    locale: string;
    languages: Language[];
    i18n: Record<string, unknown>;
    flash?: {
        status?: string;
    };
    ziggy: {
        location: string;
        url: string;
        port: null | number;
        defaults: Record<string, unknown>;
        routes: Record<string, string>;
    };
}

export interface User {
    id: number;
    name: string;
    username: string;
    email: string;
    role: 'admin' | 'user';
    avatar?: string;
    email_verified_at: string | null;
    created_at: string;
    updated_at: string;
}

export type BreadcrumbItemType = BreadcrumbItem;

export interface PaginationLink {
    url: string | null;
    label: string;
    active: boolean;
}

export interface Paginated<T> {
    data: T[];
    current_page: number;
    first_page_url: string;
    from: number | null;
    last_page: number;
    last_page_url: string;
    links: PaginationLink[];
    next_page_url: string | null;
    path: string;
    per_page: number;
    prev_page_url: string | null;
    to: number | null;
    total: number;
}
