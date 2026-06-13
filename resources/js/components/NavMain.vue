<script setup lang="ts">
import { useI18n } from '@/composables/useI18n';
import { SidebarGroup, SidebarGroupLabel, SidebarMenu, SidebarMenuButton, SidebarMenuItem } from '@/components/ui/sidebar';
import type { SharedData } from '@/types';
import { Link, usePage } from '@inertiajs/vue3';
import type { Component } from 'vue';

interface NavItem {
    title: string;
    href: string;
    icon: Component;
}

defineProps<{
    items: NavItem[];
}>();

const page = usePage<SharedData>();
const { t } = useI18n();
</script>

<template>
    <SidebarGroup class="px-2 py-0">
        <SidebarGroupLabel>{{ t('nav.platform') }}</SidebarGroupLabel>
        <SidebarMenu>
            <SidebarMenuItem v-for="item in items" :key="item.href">
                <SidebarMenuButton as-child :is-active="item.href === page.url">
                    <Link :href="item.href">
                        <component :is="item.icon" />
                        <span>{{ item.title }}</span>
                    </Link>
                </SidebarMenuButton>
            </SidebarMenuItem>
        </SidebarMenu>
    </SidebarGroup>
</template>
