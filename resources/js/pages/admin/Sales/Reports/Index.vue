<script setup lang="ts">
import Heading from '@/components/Heading.vue';
import SeoHead from '@/components/SeoHead.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { useI18n } from '@/composables/useI18n';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { router } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

interface PeriodOption {
    value: string;
    label_key: string;
}

interface ChartPoint {
    date: string;
    revenue: number;
    orders: number;
}

interface ReportData {
    period: string;
    from: string;
    to: string;
    orders_count: number;
    revenue: number;
    products_revenue: number;
    services_revenue: number;
    chart: ChartPoint[];
}

const props = defineProps<{
    period: string;
    from: string | null;
    to: string | null;
    report: ReportData;
    periods: PeriodOption[];
}>();

const { t } = useI18n();

const customFrom = ref(props.from ?? props.report.from);
const customTo = ref(props.to ?? props.report.to);

const breadcrumbs = computed<BreadcrumbItem[]>(() => [
    { title: t('nav.sales'), href: route('admin.sales.reports.index') },
    { title: t('admin.sales.reports.title'), href: route('admin.sales.reports.index') },
]);

const formatMoney = (value: number) =>
    new Intl.NumberFormat(undefined, { minimumFractionDigits: 0, maximumFractionDigits: 2 }).format(value);

const maxRevenue = computed(() => Math.max(...props.report.chart.map((point) => point.revenue), 1));

const barHeight = (revenue: number) => `${Math.max((revenue / maxRevenue.value) * 100, revenue > 0 ? 4 : 0)}%`;

const applyPeriod = (value: string) => {
    const params: Record<string, string> = { period: value };

    if (value === 'custom') {
        params.from = customFrom.value;
        params.to = customTo.value;
    }

    router.get(route('admin.sales.reports.index'), params, { preserveState: true });
};
</script>

<template>
    <SeoHead :title="t('admin.sales.reports.title')" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex flex-col gap-6 px-6 py-3">
            <Heading :title="t('admin.sales.reports.title')" :description="t('admin.sales.reports.description')" />

            <div class="flex flex-wrap items-end gap-2">
                <Button
                    v-for="periodOption in periods"
                    :key="periodOption.value"
                    type="button"
                    size="sm"
                    :variant="period === periodOption.value ? 'default' : 'outline'"
                    @click="applyPeriod(periodOption.value)"
                >
                    {{ t(periodOption.label_key) }}
                </Button>
            </div>

            <div v-if="period === 'custom'" class="flex flex-wrap items-end gap-4 rounded-xl border bg-card p-4">
                <div class="grid gap-2">
                    <Label for="from">{{ t('admin.sales.reports.from') }}</Label>
                    <Input id="from" v-model="customFrom" type="date" class="w-44" />
                </div>
                <div class="grid gap-2">
                    <Label for="to">{{ t('admin.sales.reports.to') }}</Label>
                    <Input id="to" v-model="customTo" type="date" class="w-44" />
                </div>
                <Button type="button" @click="applyPeriod('custom')">{{ t('admin.sales.reports.apply') }}</Button>
            </div>

            <p class="text-sm text-muted-foreground">
                {{ t('admin.sales.reports.range') }}: {{ report.from }} — {{ report.to }}
            </p>

            <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                <div class="rounded-xl border bg-card p-4 shadow-sm">
                    <p class="text-sm text-muted-foreground">{{ t('admin.sales.reports.orders_count') }}</p>
                    <p class="mt-1 text-2xl font-semibold">{{ report.orders_count }}</p>
                </div>
                <div class="rounded-xl border bg-card p-4 shadow-sm">
                    <p class="text-sm text-muted-foreground">{{ t('admin.sales.reports.revenue') }}</p>
                    <p class="mt-1 text-2xl font-semibold">{{ formatMoney(report.revenue) }}</p>
                </div>
                <div class="rounded-xl border bg-card p-4 shadow-sm">
                    <p class="text-sm text-muted-foreground">{{ t('admin.sales.reports.products_revenue') }}</p>
                    <p class="mt-1 text-2xl font-semibold">{{ formatMoney(report.products_revenue) }}</p>
                </div>
                <div class="rounded-xl border bg-card p-4 shadow-sm">
                    <p class="text-sm text-muted-foreground">{{ t('admin.sales.reports.services_revenue') }}</p>
                    <p class="mt-1 text-2xl font-semibold">{{ formatMoney(report.services_revenue) }}</p>
                </div>
            </div>

            <section class="rounded-xl border bg-card p-6 shadow-sm">
                <h2 class="text-lg font-semibold">{{ t('admin.sales.reports.chart') }}</h2>

                <div v-if="report.chart.length > 0" class="mt-6 flex h-48 items-end gap-2 overflow-x-auto pb-2">
                    <div v-for="point in report.chart" :key="point.date" class="flex min-w-10 flex-1 flex-col items-center gap-1">
                        <div class="flex h-40 w-full items-end justify-center">
                            <div
                                class="w-full max-w-12 rounded-t bg-primary/80 transition-all"
                                :style="{ height: barHeight(point.revenue) }"
                                :title="`${point.date}: ${formatMoney(point.revenue)}`"
                            />
                        </div>
                        <span class="text-[10px] text-muted-foreground">{{ point.date.slice(5) }}</span>
                    </div>
                </div>
                <p v-else class="mt-6 text-center text-sm text-muted-foreground">{{ t('admin.sales.reports.no_data') }}</p>
            </section>
        </div>
    </AppLayout>
</template>
