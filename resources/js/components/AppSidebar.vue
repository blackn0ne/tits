<script setup lang="ts">
import NavBlog from '@/components/NavBlog.vue';
import NavMain from '@/components/NavMain.vue';
import NavProjects from '@/components/NavProjects.vue';
import NavSales from '@/components/NavSales.vue';
import NavUser from '@/components/NavUser.vue';
import { useI18n } from '@/composables/useI18n';
import { Sidebar, SidebarContent, SidebarFooter, SidebarHeader, SidebarMenu, SidebarMenuButton, SidebarMenuItem } from '@/components/ui/sidebar';
import type { SharedData } from '@/types';
import { Link, usePage } from '@inertiajs/vue3';
import { Languages, LayoutGrid, Settings } from 'lucide-vue-next';
import { computed } from 'vue';
import AppLogo from './AppLogo.vue';

const { t } = useI18n();
const page = usePage<SharedData>();

const isAdmin = computed(() => page.props.auth.user?.role === 'admin');

const mainNavItems = computed(() => {
    const items = [
        {
            title: t('nav.dashboard'),
            href: route('dashboard'),
            icon: LayoutGrid,
        },
    ];

    if (isAdmin.value) {
        items.push(
            {
                title: t('nav.languages'),
                href: route('admin.languages.index'),
                icon: Languages,
            },
            {
                title: t('nav.site_settings'),
                href: route('admin.site-settings.edit'),
                icon: Settings,
            },
        );
    }

    return items;
});
</script>

<template>
    <Sidebar collapsible="icon" variant="inset">
        <SidebarHeader>
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton size="lg" as-child>
                        <Link :href="route('dashboard')">
                            <AppLogo />
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarHeader>

        <SidebarContent>
            <NavMain :items="mainNavItems" />
            <NavBlog v-if="isAdmin" />
            <NavProjects v-if="isAdmin" />
            <NavSales v-if="isAdmin" />
        </SidebarContent>

        <SidebarFooter>
            <NavUser />
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>
