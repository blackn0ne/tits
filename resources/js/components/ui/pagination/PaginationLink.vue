<script setup lang="ts">
import { buttonVariants, type ButtonVariants } from '@/components/ui/button';
import { cn } from '@/lib/utils';
import { Primitive, type PrimitiveProps } from 'radix-vue';
import type { HTMLAttributes } from 'vue';

type PaginationLinkProps = {
    isActive?: boolean;
    size?: ButtonVariants['size'];
    class?: HTMLAttributes['class'];
    disabled?: boolean;
} & PrimitiveProps;

const props = withDefaults(defineProps<PaginationLinkProps>(), {
    as: 'button',
    size: 'icon',
    disabled: false,
});
</script>

<template>
    <Primitive
        :as="as"
        :as-child="asChild"
        :aria-current="isActive ? 'page' : undefined"
        :disabled="disabled"
        :class="
            cn(
                buttonVariants({
                    variant: isActive ? 'outline' : 'ghost',
                    size,
                }),
                props.class,
            )
        "
    >
        <slot />
    </Primitive>
</template>
