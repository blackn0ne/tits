<script setup lang="ts">
import { useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

import HeadingSmall from '@/components/HeadingSmall.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import {
    Dialog,
    DialogClose,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
    DialogTrigger,
} from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { useI18n } from '@/composables/useI18n';

const { t } = useI18n();

const passwordInput = ref<HTMLInputElement | null>(null);

const form = useForm({
    password: '',
});

const deleteUser = (e: Event) => {
    e.preventDefault();

    form.delete(route('profile.destroy'), {
        preserveScroll: true,
        onSuccess: () => closeModal(),
        onError: () => passwordInput.value?.focus(),
        onFinish: () => form.reset(),
    });
};

const closeModal = () => {
    form.clearErrors();
    form.reset();
};
</script>

<template>
    <div class="space-y-6">
        <HeadingSmall :title="t('settings.delete_account')" :description="t('settings.delete_account_description')" />
        <div class="space-y-4 rounded-lg border border-red-100 bg-red-50 p-4 dark:border-red-200/10 dark:bg-red-700/10">
            <div class="relative space-y-0.5 text-red-600 dark:text-red-100">
                <p class="font-medium">{{ t('settings.warning') }}</p>
                <p class="text-sm">{{ t('settings.delete_warning') }}</p>
            </div>
            <Dialog>
                <DialogTrigger as-child>
                    <Button variant="destructive">{{ t('settings.delete_account') }}</Button>
                </DialogTrigger>
                <DialogContent>
                    <form class="space-y-6" @submit="deleteUser">
                        <DialogHeader class="space-y-3">
                            <DialogTitle>{{ t('settings.delete_confirm_title') }}</DialogTitle>
                            <DialogDescription>
                                {{ t('settings.delete_confirm_description') }}
                            </DialogDescription>
                        </DialogHeader>

                        <div class="grid gap-2">
                            <Label for="password" class="sr-only">{{ t('auth.login.password') }}</Label>
                            <Input
                                id="password"
                                type="password"
                                name="password"
                                ref="passwordInput"
                                v-model="form.password"
                                :placeholder="t('auth.login.password_placeholder')"
                            />
                            <InputError :message="form.errors.password" />
                        </div>

                        <DialogFooter>
                            <DialogClose as-child>
                                <Button variant="secondary" @click="closeModal">{{ t('settings.cancel') }}</Button>
                            </DialogClose>

                            <Button variant="destructive" :disabled="form.processing">
                                <button type="submit">{{ t('settings.delete_account') }}</button>
                            </Button>
                        </DialogFooter>
                    </form>
                </DialogContent>
            </Dialog>
        </div>
    </div>
</template>
