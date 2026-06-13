<script setup lang="ts">
import AppLogo from '@/components/AppLogo.vue';
import LocaleSwitcher from '@/components/LocaleSwitcher.vue';
import { useSite } from '@/composables/useSite';
import { Link } from '@inertiajs/vue3';

const { site } = useSite();

defineProps<{
    title?: string;
    description?: string;
}>();
</script>

<template>
    <div class="relative grid h-dvh flex-col items-center justify-center px-8 sm:px-0 lg:max-w-none lg:grid-cols-2 lg:px-0">
        <div class="relative hidden h-full flex-col bg-muted p-10 text-white dark:border-r lg:flex">
            <div class="absolute inset-0 bg-zinc-900" />
            <Link :href="route('home')" class="relative z-20 flex items-center text-lg font-medium text-white">
                <AppLogo />
            </Link>
            <div v-if="site.description" class="relative z-20 mt-auto">
                <blockquote class="space-y-2">
                    <p class="text-lg">&ldquo;{{ site.description }}&rdquo;</p>
                    <footer v-if="site.address" class="text-sm text-neutral-300">{{ site.address }}</footer>
                </blockquote>
            </div>
        </div>
        <div class="relative lg:p-8">
            <div class="mx-auto flex w-full flex-col justify-center space-y-6 sm:w-[350px]">
                <div class="absolute right-4 top-4 lg:right-8 lg:top-8">
                    <LocaleSwitcher />
                </div>
                <div class="flex flex-col space-y-2 text-center">
                    <h1 class="text-xl font-medium tracking-tight" v-if="title">{{ title }}</h1>
                    <p class="text-sm text-muted-foreground" v-if="description">{{ description }}</p>
                </div>
                <slot />
            </div>
        </div>
    </div>
</template>
