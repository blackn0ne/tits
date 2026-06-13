<script setup lang="ts">
import type { SharedData } from '@/types';
import { router, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const page = usePage<SharedData>();

const languages = computed(() => page.props.languages ?? []);
const currentLocale = computed(() => page.props.locale);

const localeLabel = (code: string): string => code.toUpperCase();

const switchLocale = (code: string): void => {
    if (code === currentLocale.value) {
        return;
    }

    router.get(
        route('locale.update', { locale: code }),
        {},
        {
            preserveScroll: true,
            preserveState: false,
            replace: true,
        },
    );
};
</script>

<template>
    <button
        v-for="language in languages"
        :key="language.code"
        type="button"
        class="wpr-btn btn-primary header-social-btn header-lang-btn"
        :class="{ 'header-lang-btn--active': currentLocale === language.code }"
        :aria-label="language.name"
        :aria-current="currentLocale === language.code ? 'true' : undefined"
        @click="switchLocale(language.code)"
    >
        {{ localeLabel(language.code) }}
    </button>
</template>
