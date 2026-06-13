import { usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import type { SharedData } from '@/types';

type TranslationValue = string | TranslationTree;
type TranslationTree = Record<string, TranslationValue>;

function resolveTranslation(tree: TranslationTree, key: string): string | undefined {
    const value = key.split('.').reduce<TranslationValue | undefined>((current, segment) => {
        if (current === undefined || typeof current === 'string') {
            return undefined;
        }

        return current[segment];
    }, tree);

    return typeof value === 'string' ? value : undefined;
}

export function useI18n() {
    const page = usePage<SharedData>();

    const locale = computed(() => page.props.locale);
    const i18n = computed(() => page.props.i18n as TranslationTree);

    const t = (key: string, replacements: Record<string, string> = {}): string => {
        let value = resolveTranslation(i18n.value, key) ?? key;

        Object.entries(replacements).forEach(([placeholder, replacement]) => {
            value = value.replace(`:${placeholder}`, replacement);
        });

        return value;
    };

    const has = (key: string): boolean => resolveTranslation(i18n.value, key) !== undefined;

    return { t, has, locale, i18n };
}
