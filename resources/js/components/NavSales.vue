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
import { BarChart3, ChevronRight, ClipboardList, Package, ShoppingCart, Users, Wrench } from 'lucide-vue-next';
import { computed } from 'vue';

const { t } = useI18n();
const page = usePage();

const isSalesSection = computed(() => page.url.startsWith('/admin/sales'));

const isOrdersActive = computed(() => page.url.startsWith('/admin/sales/orders'));
const isProductsActive = computed(() => page.url.startsWith('/admin/sales/products'));
const isServicesActive = computed(() => page.url.startsWith('/admin/sales/services'));
const isClientsActive = computed(() => page.url.startsWith('/admin/sales/clients'));
const isReportsActive = computed(() => page.url.startsWith('/admin/sales/reports'));
</script>

<template>
    <SidebarGroup class="px-2 py-0">
        <SidebarGroupLabel>{{ t('nav.sales') }}</SidebarGroupLabel>
        <SidebarMenu>
            <Collapsible :default-open="isSalesSection" class="group/collapsible">
                <SidebarMenuItem>
                    <CollapsibleTrigger as-child>
                        <SidebarMenuButton :tooltip="t('nav.sales')">
                            <ShoppingCart />
                            <span>{{ t('nav.sales') }}</span>
                            <ChevronRight class="ml-auto transition-transform group-data-[state=open]/collapsible:rotate-90" />
                        </SidebarMenuButton>
                    </CollapsibleTrigger>
                    <CollapsibleContent>
                        <SidebarMenuSub>
                            <SidebarMenuSubItem>
                                <SidebarMenuSubButton as-child :is-active="isOrdersActive">
                                    <Link :href="route('admin.sales.orders.index')">
                                        <ClipboardList class="size-4" />
                                        <span>{{ t('nav.sales_orders') }}</span>
                                    </Link>
                                </SidebarMenuSubButton>
                            </SidebarMenuSubItem>
                            <SidebarMenuSubItem>
                                <SidebarMenuSubButton as-child :is-active="isProductsActive">
                                    <Link :href="route('admin.sales.products.index')">
                                        <Package class="size-4" />
                                        <span>{{ t('nav.sales_products') }}</span>
                                    </Link>
                                </SidebarMenuSubButton>
                            </SidebarMenuSubItem>
                            <SidebarMenuSubItem>
                                <SidebarMenuSubButton as-child :is-active="isServicesActive">
                                    <Link :href="route('admin.sales.services.index')">
                                        <Wrench class="size-4" />
                                        <span>{{ t('nav.sales_services') }}</span>
                                    </Link>
                                </SidebarMenuSubButton>
                            </SidebarMenuSubItem>
                            <SidebarMenuSubItem>
                                <SidebarMenuSubButton as-child :is-active="isClientsActive">
                                    <Link :href="route('admin.sales.clients.index')">
                                        <Users class="size-4" />
                                        <span>{{ t('nav.sales_clients') }}</span>
                                    </Link>
                                </SidebarMenuSubButton>
                            </SidebarMenuSubItem>
                            <SidebarMenuSubItem>
                                <SidebarMenuSubButton as-child :is-active="isReportsActive">
                                    <Link :href="route('admin.sales.reports.index')">
                                        <BarChart3 class="size-4" />
                                        <span>{{ t('nav.sales_reports') }}</span>
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
