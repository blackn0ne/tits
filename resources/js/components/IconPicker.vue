<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { useI18n } from '@/composables/useI18n';
import { LUCIDE_ICON_NAMES, type LucideIconName } from '@/data/lucide-icons';
import { cn } from '@/lib/utils';
import * as LucideIcons from 'lucide-vue-next';
import { Search } from 'lucide-vue-next';
import { computed, ref } from 'vue';

const model = defineModel<string>({ required: true });

const { t } = useI18n();
const search = ref('');

const filteredIcons = computed(() => {
    const query = search.value.trim().toLowerCase();

    if (!query) {
        return LUCIDE_ICON_NAMES;
    }

    return LUCIDE_ICON_NAMES.filter((name) => name.toLowerCase().includes(query));
});

const resolveIcon = (name: string) => {
    return (LucideIcons as Record<string, unknown>)[name] as typeof LucideIcons.Folder | undefined;
};

const selectIcon = (name: LucideIconName) => {
    model.value = name;
};
</script>

<template>
    <div class="space-y-3">
        <div class="relative">
            <Search class="absolute left-2.5 top-2.5 size-4 text-muted-foreground" />
            <Input v-model="search" class="pl-8" :placeholder="t('admin.blog.search_icon')" />
        </div>

        <div class="grid max-h-48 grid-cols-6 gap-2 overflow-y-auto rounded-lg border p-2 sm:grid-cols-8">
            <Button
                v-for="iconName in filteredIcons"
                :key="iconName"
                type="button"
                variant="outline"
                size="icon"
                :class="cn('size-9', model === iconName && 'border-primary bg-primary/10')"
                :title="iconName"
                @click="selectIcon(iconName)"
            >
                <component :is="resolveIcon(iconName)" class="size-4" />
            </Button>
        </div>

        <p v-if="filteredIcons.length === 0" class="text-sm text-muted-foreground">—</p>
    </div>
</template>
