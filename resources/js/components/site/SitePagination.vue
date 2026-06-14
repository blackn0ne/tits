<script setup lang="ts">
import { useI18n } from '@/composables/useI18n';
import type { Paginated } from '@/types';
import { router } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps<{
    meta: Paginated<unknown>;
}>();

const { t } = useI18n();

const goTo = (url: string | null) => {
    if (!url) {
        return;
    }

    router.get(url, {}, { preserveState: true, preserveScroll: false });
};

const cleanLabel = (label: string) =>
    label
        .replace(/<[^>]*>/g, '')
        .replace(/&laquo;|&raquo;/g, '')
        .trim();

const isNavigationLabel = (label: string) => {
    const text = cleanLabel(label).toLowerCase();

    return text.includes('previous') || text.includes('next') || text === '«' || text === '»';
};

const pageLinks = computed(() =>
    props.meta.links.filter((link) => {
        const text = cleanLabel(link.label);

        return !isNavigationLabel(link.label) && text !== '';
    }),
);

const summary = computed(() => {
    if (props.meta.total === 0 || props.meta.from === null || props.meta.to === null) {
        return null;
    }

    return t('pagination.summary', {
        from: String(props.meta.from),
        to: String(props.meta.to),
        total: String(props.meta.total),
    });
});
</script>

<template>
    <div v-if="meta.last_page > 1" class="site-pagination">
        <p v-if="summary" class="site-pagination__summary">{{ summary }}</p>

        <nav class="wg-pagination" :aria-label="t('pagination.next')">
            <button
                type="button"
                class="pagination-item"
                :class="{ 'is-disabled': !meta.prev_page_url }"
                :disabled="!meta.prev_page_url"
                @click="goTo(meta.prev_page_url)"
            >
                {{ t('pagination.previous') }}
            </button>

            <template v-for="(link, index) in pageLinks" :key="`${link.label}-${index}`">
                <span v-if="cleanLabel(link.label) === '...'" class="pagination-item is-disabled" aria-hidden="true">…</span>
                <button
                    v-else
                    type="button"
                    class="pagination-item"
                    :class="{ active: link.active, 'is-disabled': !link.url }"
                    :disabled="!link.url || link.active"
                    @click="goTo(link.url)"
                >
                    {{ cleanLabel(link.label) }}
                </button>
            </template>

            <button
                type="button"
                class="pagination-item"
                :class="{ 'is-disabled': !meta.next_page_url }"
                :disabled="!meta.next_page_url"
                @click="goTo(meta.next_page_url)"
            >
                {{ t('pagination.next') }}
            </button>
        </nav>
    </div>
</template>
