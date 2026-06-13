<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Bold, Italic, Link, List, ListOrdered, Underline } from 'lucide-vue-next';
import { onMounted, ref, watch } from 'vue';

const model = defineModel<string>({ default: '' });

const editor = ref<HTMLDivElement | null>(null);

const syncFromModel = () => {
    if (editor.value && editor.value.innerHTML !== model.value) {
        editor.value.innerHTML = model.value;
    }
};

const onInput = () => {
    model.value = editor.value?.innerHTML ?? '';
};

const exec = (command: string, value?: string) => {
    document.execCommand(command, false, value);
    editor.value?.focus();
    onInput();
};

const addLink = () => {
    const url = window.prompt('URL');

    if (url) {
        exec('createLink', url);
    }
};

onMounted(syncFromModel);

watch(model, syncFromModel);
</script>

<template>
    <div class="overflow-hidden rounded-md border bg-background">
        <div class="flex flex-wrap gap-1 border-b bg-muted/40 p-1">
            <Button type="button" variant="ghost" size="icon" class="size-8" @click="exec('bold')">
                <Bold class="size-4" />
            </Button>
            <Button type="button" variant="ghost" size="icon" class="size-8" @click="exec('italic')">
                <Italic class="size-4" />
            </Button>
            <Button type="button" variant="ghost" size="icon" class="size-8" @click="exec('underline')">
                <Underline class="size-4" />
            </Button>
            <Button type="button" variant="ghost" size="icon" class="size-8" @click="exec('insertUnorderedList')">
                <List class="size-4" />
            </Button>
            <Button type="button" variant="ghost" size="icon" class="size-8" @click="exec('insertOrderedList')">
                <ListOrdered class="size-4" />
            </Button>
            <Button type="button" variant="ghost" size="icon" class="size-8" @click="addLink">
                <Link class="size-4" />
            </Button>
        </div>
        <div
            ref="editor"
            contenteditable
            class="min-h-[200px] px-3 py-2 text-sm outline-none focus-visible:ring-2 focus-visible:ring-ring"
            @input="onInput"
        />
    </div>
</template>
