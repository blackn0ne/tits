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
import { ChevronRight, FolderTree, Newspaper } from 'lucide-vue-next';
import { computed } from 'vue';

const { t } = useI18n();
const page = usePage();

const isBlogSection = computed(() => {
    const url = page.url;

    return url.startsWith('/admin/blog-posts') || url.startsWith('/admin/blog-categories');
});

const isPostsActive = computed(() => page.url.startsWith('/admin/blog-posts'));
const isCategoriesActive = computed(() => page.url.startsWith('/admin/blog-categories'));
</script>

<template>
    <SidebarGroup class="px-2 py-0">
        <SidebarGroupLabel>{{ t('nav.blog') }}</SidebarGroupLabel>
        <SidebarMenu>
            <Collapsible :default-open="isBlogSection" class="group/collapsible">
                <SidebarMenuItem>
                    <CollapsibleTrigger as-child>
                        <SidebarMenuButton :tooltip="t('nav.blog')">
                            <Newspaper />
                            <span>{{ t('nav.blog') }}</span>
                            <ChevronRight class="ml-auto transition-transform group-data-[state=open]/collapsible:rotate-90" />
                        </SidebarMenuButton>
                    </CollapsibleTrigger>
                    <CollapsibleContent>
                        <SidebarMenuSub>
                            <SidebarMenuSubItem>
                                <SidebarMenuSubButton as-child :is-active="isPostsActive">
                                    <Link :href="route('admin.blog-posts.index')">
                                        <span>{{ t('nav.blog_posts') }}</span>
                                    </Link>
                                </SidebarMenuSubButton>
                            </SidebarMenuSubItem>
                            <SidebarMenuSubItem>
                                <SidebarMenuSubButton as-child :is-active="isCategoriesActive">
                                    <Link :href="route('admin.blog-categories.index')">
                                        <FolderTree class="size-4" />
                                        <span>{{ t('nav.blog_categories') }}</span>
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
