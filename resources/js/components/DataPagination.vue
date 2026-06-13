<script setup lang="ts">
import {
    Pagination,
    PaginationContent,
    PaginationEllipsis,
    PaginationItem,
    PaginationLink,
    PaginationNext,
    PaginationPrevious,
} from '@/components/ui/pagination';
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

    router.get(url, {}, { preserveState: true, preserveScroll: true });
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
    <div v-if="meta.last_page > 1" class="flex flex-col items-center gap-3 sm:flex-row sm:justify-between">
        <p v-if="summary" class="text-sm text-muted-foreground">{{ summary }}</p>

        <Pagination>
            <PaginationContent>
                <PaginationItem>
                    <PaginationPrevious :disabled="!meta.prev_page_url" @click="goTo(meta.prev_page_url)">
                        {{ t('pagination.previous') }}
                    </PaginationPrevious>
                </PaginationItem>

                <template v-for="(link, index) in pageLinks" :key="`${link.label}-${index}`">
                    <PaginationItem v-if="cleanLabel(link.label) === '...'">
                        <PaginationEllipsis />
                    </PaginationItem>
                    <PaginationItem v-else>
                        <PaginationLink
                            :is-active="link.active"
                            size="icon"
                            :disabled="!link.url"
                            @click="goTo(link.url)"
                        >
                            {{ cleanLabel(link.label) }}
                        </PaginationLink>
                    </PaginationItem>
                </template>

                <PaginationItem>
                    <PaginationNext :disabled="!meta.next_page_url" @click="goTo(meta.next_page_url)">
                        {{ t('pagination.next') }}
                    </PaginationNext>
                </PaginationItem>
            </PaginationContent>
        </Pagination>
    </div>
</template>
