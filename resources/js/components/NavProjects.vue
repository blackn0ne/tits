<script setup lang="ts">
import { Collapsible, CollapsibleContent, CollapsibleTrigger } from '@/components/ui/collapsible';
import {
    SidebarGroup,
    SidebarGroupLabel,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
    SidebarMenuSub,
    SidebarMenuSubButton,
    SidebarMenuSubItem,
} from '@/components/ui/sidebar';
import { useI18n } from '@/composables/useI18n';
import { Link, usePage } from '@inertiajs/vue3';
import { Briefcase, ChevronRight, FolderTree } from 'lucide-vue-next';
import { computed } from 'vue';

const { t } = useI18n();
const page = usePage();

const isProjectsSection = computed(() => {
    const url = page.url;

    return url.startsWith('/admin/projects') || url.startsWith('/admin/project-categories');
});

const isProjectsActive = computed(() => page.url.startsWith('/admin/projects'));
const isCategoriesActive = computed(() => page.url.startsWith('/admin/project-categories'));
</script>

<template>
    <SidebarGroup class="px-2 py-0">
        <SidebarGroupLabel>{{ t('nav.works') }}</SidebarGroupLabel>
        <SidebarMenu>
            <Collapsible :default-open="isProjectsSection" class="group/collapsible">
                <SidebarMenuItem>
                    <CollapsibleTrigger as-child>
                        <SidebarMenuButton :tooltip="t('nav.works')">
                            <Briefcase />
                            <span>{{ t('nav.works') }}</span>
                            <ChevronRight class="ml-auto transition-transform group-data-[state=open]/collapsible:rotate-90" />
                        </SidebarMenuButton>
                    </CollapsibleTrigger>
                    <CollapsibleContent>
                        <SidebarMenuSub>
                            <SidebarMenuSubItem>
                                <SidebarMenuSubButton as-child :is-active="isProjectsActive">
                                    <Link :href="route('admin.projects.index')">
                                        <span>{{ t('nav.projects') }}</span>
                                    </Link>
                                </SidebarMenuSubButton>
                            </SidebarMenuSubItem>
                            <SidebarMenuSubItem>
                                <SidebarMenuSubButton as-child :is-active="isCategoriesActive">
                                    <Link :href="route('admin.project-categories.index')">
                                        <FolderTree class="size-4" />
                                        <span>{{ t('nav.project_categories') }}</span>
                                    </Link>
                                </SidebarMenuSubButton>
                            </SidebarMenuSubItem>
                        </SidebarMenuSub>
                    </CollapsibleContent>
                </SidebarMenuItem>
            </Collapsible>
        </SidebarMenu>
    </SidebarGroup>
</template>
