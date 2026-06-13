<script setup lang="ts">
import Heading from '@/components/Heading.vue';
import SeoHead from '@/components/SeoHead.vue';
import { Button } from '@/components/ui/button';
import { useI18n } from '@/composables/useI18n';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem, type SharedData } from '@/types';
import { Link, usePage } from '@inertiajs/vue3';
import {
    ArrowDownRight,
    ArrowUpRight,
    BarChart3,
    FileText,
    FolderKanban,
    Package,
    Plus,
    Settings,
    ShoppingCart,
    Users,
    Wrench,
} from 'lucide-vue-next';
import { computed } from 'vue';

interface Trend {
    direction: 'up' | 'down';
    percent: number | null;
}

interface ChartPoint {
    date: string;
    revenue: number;
    orders: number;
}

interface RecentOrder {
    id: number;
    client_name: string;
    status: string;
    status_label: string;
    total: number;
    ordered_at: string;
}

interface DashboardSummary {
    period: { from: string; to: string };
    sales: {
        revenue: number;
        revenue_trend: Trend | null;
        orders_count: number;
        orders_trend: Trend | null;
        average_order: number;
        average_order_trend: Trend | null;
        products_revenue: number;
        services_revenue: number;
    };
    catalog: {
        active_products: number;
        active_services: number;
        clients: number;
    };
    content: {
        blog_published: number;
        blog_total: number;
        blog_drafts: number;
        projects_published: number;
        projects_total: number;
        projects_drafts: number;
    };
    site: {
        maintenance_mode: boolean;
    };
    chart: ChartPoint[];
    recent_orders: RecentOrder[];
}

const props = defineProps<{
    is_admin: boolean;
    summary: DashboardSummary | null;
}>();

const { t } = useI18n();
const page = usePage<SharedData>();

const breadcrumbs = computed<BreadcrumbItem[]>(() => [
    {
        title: t('nav.dashboard'),
        href: route('dashboard'),
    },
]);

const formatMoney = (value: number) =>
    new Intl.NumberFormat(undefined, { minimumFractionDigits: 0, maximumFractionDigits: 2 }).format(value);

const maxRevenue = computed(() => Math.max(...(props.summary?.chart.map((point) => point.revenue) ?? [0]), 1));

const barHeight = (revenue: number) => `${Math.max((revenue / maxRevenue.value) * 100, revenue > 0 ? 4 : 0)}%`;

const trendLabel = (trend: Trend | null) => {
    if (!trend) {
        return t('dashboard.trend.no_change');
    }

    if (trend.percent === null) {
        return t('dashboard.trend.new');
    }

    const sign = trend.direction === 'up' ? '+' : '−';

    return `${sign}${trend.percent}%`;
};

const trendClass = (trend: Trend | null) => {
    if (!trend) {
        return 'text-muted-foreground bg-muted';
    }

    return trend.direction === 'up'
        ? 'text-emerald-700 bg-emerald-100 dark:text-emerald-300 dark:bg-emerald-950'
        : 'text-rose-700 bg-rose-100 dark:text-rose-300 dark:bg-rose-950';
};
</script>

<template>
    <div>
        <SeoHead :title="t('nav.dashboard')" />

        <AppLayout :breadcrumbs="breadcrumbs">
            <div class="flex flex-col gap-6 px-6 py-3 pb-20">
                <div class="flex flex-wrap items-start justify-between gap-4">
                    <Heading
                        :title="t('dashboard.title')"
                        :description="
                            is_admin && summary
                                ? t('dashboard.description_admin', { from: summary.period.from, to: summary.period.to })
                                : t('dashboard.description_user', { name: page.props.auth.user?.name ?? '' })
                        "
                    />

                    <div v-if="is_admin" class="flex flex-wrap gap-2">
                        <Button as-child size="sm">
                            <Link :href="route('admin.sales.orders.create')">
                                <Plus class="size-4" />
                                {{ t('dashboard.actions.new_order') }}
                            </Link>
                        </Button>
                        <Button as-child size="sm" variant="outline">
                            <Link :href="route('admin.sales.reports.index')">
                                <BarChart3 class="size-4" />
                                {{ t('dashboard.actions.reports') }}
                            </Link>
                        </Button>
                    </div>
                </div>

                <template v-if="is_admin && summary">
                    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                        <div class="rounded-xl border bg-card p-5 shadow-sm">
                            <div class="flex items-start justify-between gap-3">
                                <p class="text-sm text-muted-foreground">{{ t('dashboard.kpi.revenue') }}</p>
                                <span
                                    v-if="summary.sales.revenue_trend"
                                    class="inline-flex items-center gap-1 rounded-full px-2 py-0.5 text-xs font-medium"
                                    :class="trendClass(summary.sales.revenue_trend)"
                                >
                                    <ArrowUpRight v-if="summary.sales.revenue_trend.direction === 'up'" class="size-3" />
                                    <ArrowDownRight v-else class="size-3" />
                                    {{ trendLabel(summary.sales.revenue_trend) }}
                                </span>
                            </div>
                            <p class="mt-2 text-3xl font-semibold tracking-tight">{{ formatMoney(summary.sales.revenue) }}</p>
                            <p class="mt-1 text-xs text-muted-foreground">{{ t('dashboard.kpi.vs_last_month') }}</p>
                        </div>

                        <div class="rounded-xl border bg-card p-5 shadow-sm">
                            <div class="flex items-start justify-between gap-3">
                                <p class="text-sm text-muted-foreground">{{ t('dashboard.kpi.orders') }}</p>
                                <span
                                    v-if="summary.sales.orders_trend"
                                    class="inline-flex items-center gap-1 rounded-full px-2 py-0.5 text-xs font-medium"
                                    :class="trendClass(summary.sales.orders_trend)"
                                >
                                    <ArrowUpRight v-if="summary.sales.orders_trend.direction === 'up'" class="size-3" />
                                    <ArrowDownRight v-else class="size-3" />
                                    {{ trendLabel(summary.sales.orders_trend) }}
                                </span>
                            </div>
                            <p class="mt-2 text-3xl font-semibold tracking-tight">{{ summary.sales.orders_count }}</p>
                            <p class="mt-1 text-xs text-muted-foreground">{{ t('dashboard.kpi.vs_last_month') }}</p>
                        </div>

                        <div class="rounded-xl border bg-card p-5 shadow-sm">
                            <div class="flex items-start justify-between gap-3">
                                <p class="text-sm text-muted-foreground">{{ t('dashboard.kpi.average_order') }}</p>
                                <span
                                    v-if="summary.sales.average_order_trend"
                                    class="inline-flex items-center gap-1 rounded-full px-2 py-0.5 text-xs font-medium"
                                    :class="trendClass(summary.sales.average_order_trend)"
                                >
                                    <ArrowUpRight v-if="summary.sales.average_order_trend.direction === 'up'" class="size-3" />
                                    <ArrowDownRight v-else class="size-3" />
                                    {{ trendLabel(summary.sales.average_order_trend) }}
                                </span>
                            </div>
                            <p class="mt-2 text-3xl font-semibold tracking-tight">{{ formatMoney(summary.sales.average_order) }}</p>
                            <p class="mt-1 text-xs text-muted-foreground">{{ t('dashboard.kpi.vs_last_month') }}</p>
                        </div>

                        <div class="rounded-xl border bg-card p-5 shadow-sm">
                            <p class="text-sm text-muted-foreground">{{ t('dashboard.kpi.clients') }}</p>
                            <p class="mt-2 text-3xl font-semibold tracking-tight">{{ summary.catalog.clients }}</p>
                            <p class="mt-1 text-xs text-muted-foreground">{{ t('dashboard.kpi.clients_hint') }}</p>
                        </div>
                    </div>

                    <div class="grid gap-4 xl:grid-cols-3">
                        <section class="rounded-xl border bg-card p-6 shadow-sm xl:col-span-2">
                            <div class="flex items-center justify-between gap-3">
                                <h2 class="text-lg font-semibold">{{ t('dashboard.chart.title') }}</h2>
                                <span class="text-xs text-muted-foreground">{{ t('dashboard.chart.subtitle') }}</span>
                            </div>

                            <div v-if="summary.chart.length > 0" class="mt-6 flex h-48 items-end gap-1.5 overflow-x-auto pb-2">
                                <div
                                    v-for="point in summary.chart"
                                    :key="point.date"
                                    class="flex min-w-8 flex-1 flex-col items-center gap-1"
                                >
                                    <div class="flex h-40 w-full items-end justify-center">
                                        <div
                                            class="w-full max-w-10 rounded-t bg-primary/80 transition-all"
                                            :style="{ height: barHeight(point.revenue) }"
                                            :title="`${point.date}: ${formatMoney(point.revenue)}`"
                                        />
                                    </div>
                                    <span class="text-[10px] text-muted-foreground">{{ point.date.slice(5) }}</span>
                                </div>
                            </div>
                            <p v-else class="mt-6 text-center text-sm text-muted-foreground">{{ t('dashboard.chart.empty') }}</p>

                            <div class="mt-6 grid gap-3 sm:grid-cols-2">
                                <div class="rounded-lg border bg-muted/30 p-4">
                                    <p class="text-xs text-muted-foreground">{{ t('dashboard.sales_breakdown.products') }}</p>
                                    <p class="mt-1 text-xl font-semibold">{{ formatMoney(summary.sales.products_revenue) }}</p>
                                </div>
                                <div class="rounded-lg border bg-muted/30 p-4">
                                    <p class="text-xs text-muted-foreground">{{ t('dashboard.sales_breakdown.services') }}</p>
                                    <p class="mt-1 text-xl font-semibold">{{ formatMoney(summary.sales.services_revenue) }}</p>
                                </div>
                            </div>
                        </section>

                        <div class="flex flex-col gap-4">
                            <section class="rounded-xl border bg-card p-5 shadow-sm">
                                <h2 class="text-sm font-semibold">{{ t('dashboard.catalog.title') }}</h2>
                                <div class="mt-4 space-y-3">
                                    <div class="flex items-center justify-between gap-3">
                                        <span class="flex items-center gap-2 text-sm text-muted-foreground">
                                            <Package class="size-4" />
                                            {{ t('dashboard.catalog.products') }}
                                        </span>
                                        <span class="font-semibold">{{ summary.catalog.active_products }}</span>
                                    </div>
                                    <div class="flex items-center justify-between gap-3">
                                        <span class="flex items-center gap-2 text-sm text-muted-foreground">
                                            <Wrench class="size-4" />
                                            {{ t('dashboard.catalog.services') }}
                                        </span>
                                        <span class="font-semibold">{{ summary.catalog.active_services }}</span>
                                    </div>
                                    <div class="flex items-center justify-between gap-3">
                                        <span class="flex items-center gap-2 text-sm text-muted-foreground">
                                            <Users class="size-4" />
                                            {{ t('dashboard.catalog.clients') }}
                                        </span>
                                        <span class="font-semibold">{{ summary.catalog.clients }}</span>
                                    </div>
                                </div>
                            </section>

                            <section class="rounded-xl border bg-card p-5 shadow-sm">
                                <h2 class="text-sm font-semibold">{{ t('dashboard.content.title') }}</h2>
                                <div class="mt-4 space-y-3">
                                    <div class="flex items-center justify-between gap-3">
                                        <span class="flex items-center gap-2 text-sm text-muted-foreground">
                                            <FileText class="size-4" />
                                            {{ t('dashboard.content.blog') }}
                                        </span>
                                        <span class="font-semibold">
                                            {{ summary.content.blog_published }}/{{ summary.content.blog_total }}
                                        </span>
                                    </div>
                                    <div class="flex items-center justify-between gap-3">
                                        <span class="flex items-center gap-2 text-sm text-muted-foreground">
                                            <FolderKanban class="size-4" />
                                            {{ t('dashboard.content.projects') }}
                                        </span>
                                        <span class="font-semibold">
                                            {{ summary.content.projects_published }}/{{ summary.content.projects_total }}
                                        </span>
                                    </div>
                                    <div class="flex items-center justify-between gap-3 border-t pt-3">
                                        <span class="text-sm text-muted-foreground">{{ t('dashboard.content.drafts') }}</span>
                                        <span class="font-semibold">
                                            {{ summary.content.blog_drafts + summary.content.projects_drafts }}
                                        </span>
                                    </div>
                                </div>
                            </section>

                            <section
                                class="rounded-xl border p-5 shadow-sm"
                                :class="
                                    summary.site.maintenance_mode
                                        ? 'border-amber-300 bg-amber-50 dark:border-amber-800 dark:bg-amber-950/40'
                                        : 'border-emerald-300 bg-emerald-50 dark:border-emerald-800 dark:bg-emerald-950/40'
                                "
                            >
                                <p class="text-sm font-semibold">{{ t('dashboard.site.title') }}</p>
                                <p class="mt-2 text-sm">
                                    {{
                                        summary.site.maintenance_mode
                                            ? t('dashboard.site.maintenance_on')
                                            : t('dashboard.site.maintenance_off')
                                    }}
                                </p>
                                <Button as-child size="sm" variant="outline" class="mt-4">
                                    <Link :href="route('admin.site-settings.edit')">
                                        <Settings class="size-4" />
                                        {{ t('dashboard.actions.site_settings') }}
                                    </Link>
                                </Button>
                            </section>
                        </div>
                    </div>

                    <div class="grid gap-4 xl:grid-cols-3">
                        <section class="rounded-xl border bg-card shadow-sm xl:col-span-2">
                            <div class="flex items-center justify-between gap-3 border-b px-5 py-4">
                                <h2 class="text-lg font-semibold">{{ t('dashboard.recent_orders.title') }}</h2>
                                <Button as-child size="sm" variant="ghost">
                                    <Link :href="route('admin.sales.orders.index')">{{ t('dashboard.recent_orders.all') }}</Link>
                                </Button>
                            </div>

                            <div v-if="summary.recent_orders.length > 0" class="overflow-x-auto">
                                <table class="w-full text-sm">
                                    <thead class="border-b bg-muted/40">
                                        <tr>
                                            <th class="px-5 py-3 text-left font-medium">{{ t('dashboard.recent_orders.client') }}</th>
                                            <th class="px-5 py-3 text-left font-medium">{{ t('dashboard.recent_orders.date') }}</th>
                                            <th class="px-5 py-3 text-left font-medium">{{ t('dashboard.recent_orders.total') }}</th>
                                            <th class="px-5 py-3 text-left font-medium">{{ t('dashboard.recent_orders.status') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="order in summary.recent_orders" :key="order.id" class="border-b last:border-0">
                                            <td class="px-5 py-3">{{ order.client_name }}</td>
                                            <td class="px-5 py-3 text-muted-foreground">{{ order.ordered_at }}</td>
                                            <td class="px-5 py-3 font-medium">{{ formatMoney(order.total) }}</td>
                                            <td class="px-5 py-3">
                                                <span
                                                    class="inline-flex rounded-full px-2 py-0.5 text-xs"
                                                    :class="
                                                        order.status === 'completed'
                                                            ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300'
                                                            : 'bg-muted text-muted-foreground'
                                                    "
                                                >
                                                    {{ order.status_label }}
                                                </span>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <p v-else class="px-5 py-8 text-center text-sm text-muted-foreground">
                                {{ t('dashboard.recent_orders.empty') }}
                            </p>
                        </section>

                        <section class="rounded-xl border bg-card p-5 shadow-sm">
                            <h2 class="text-lg font-semibold">{{ t('dashboard.actions.title') }}</h2>
                            <div class="mt-4 grid gap-2">
                                <Button as-child variant="outline" class="justify-start">
                                    <Link :href="route('admin.sales.orders.create')">
                                        <ShoppingCart class="size-4" />
                                        {{ t('dashboard.actions.new_order') }}
                                    </Link>
                                </Button>
                                <Button as-child variant="outline" class="justify-start">
                                    <Link :href="route('admin.blog-posts.create')">
                                        <FileText class="size-4" />
                                        {{ t('dashboard.actions.new_post') }}
                                    </Link>
                                </Button>
                                <Button as-child variant="outline" class="justify-start">
                                    <Link :href="route('admin.projects.create')">
                                        <FolderKanban class="size-4" />
                                        {{ t('dashboard.actions.new_project') }}
                                    </Link>
                                </Button>
                                <Button as-child variant="outline" class="justify-start">
                                    <Link :href="route('admin.sales.reports.index')">
                                        <BarChart3 class="size-4" />
                                        {{ t('dashboard.actions.reports') }}
                                    </Link>
                                </Button>
                            </div>
                        </section>
                    </div>
                </template>

                <section v-else class="rounded-xl border bg-card p-8 shadow-sm">
                    <h2 class="text-xl font-semibold">{{ t('dashboard.user_welcome', { name: page.props.auth.user?.name ?? '' }) }}</h2>
                    <p class="mt-2 text-sm text-muted-foreground">{{ t('dashboard.user_hint') }}</p>
                    <Button as-child class="mt-6">
                        <Link :href="route('profile.edit')">{{ t('nav.settings') }}</Link>
                    </Button>
                </section>
            </div>
        </AppLayout>
    </div>
</template>
