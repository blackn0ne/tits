<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { computed } from 'vue';

import AppearanceTabs from '@/components/AppearanceTabs.vue';
import HeadingSmall from '@/components/HeadingSmall.vue';
import { useI18n } from '@/composables/useI18n';
import { type BreadcrumbItem } from '@/types';

import AppLayout from '@/layouts/AppLayout.vue';
import SettingsLayout from '@/layouts/settings/Layout.vue';

const { t } = useI18n();

const breadcrumbItems = computed<BreadcrumbItem[]>(() => [
    {
        title: t('settings.appearance_settings'),
        href: '/settings/appearance',
    },
]);
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbItems">
        <Head :title="t('settings.appearance_settings')" />

        <SettingsLayout>
            <div class="space-y-6">
                <HeadingSmall :title="t('settings.appearance_settings')" :description="t('settings.appearance_description')" />
                <AppearanceTabs />
            </div>
        </SettingsLayout>
    </AppLayout>
</template>
