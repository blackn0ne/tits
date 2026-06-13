<script setup lang="ts">
import SeoHead from '@/components/SeoHead.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { useI18n } from '@/composables/useI18n';
import AuthBase from '@/layouts/AuthLayout.vue';
import { useForm } from '@inertiajs/vue3';
import { LoaderCircle } from 'lucide-vue-next';

defineProps<{
    status?: string;
}>();

const { t } = useI18n();

const form = useForm({
    username: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <AuthBase :title="t('auth.login.title')" :description="t('auth.login.description')">
        <SeoHead :title="t('auth.login.page_title')" />

        <div v-if="status" class="mb-4 text-center text-sm font-medium text-green-600">
            {{ status }}
        </div>

        <form @submit.prevent="submit" class="flex flex-col gap-6">
            <div class="grid gap-6">
                <div class="grid gap-2">
                    <Label for="username">{{ t('auth.login.username') }}</Label>
                    <Input
                        id="username"
                        type="text"
                        required
                        autofocus
                        tabindex="1"
                        autocomplete="username"
                        v-model="form.username"
                        placeholder="admin"
                    />
                    <InputError :message="form.errors.username" />
                </div>

                <div class="grid gap-2">
                    <Label for="password">{{ t('auth.login.password') }}</Label>
                    <Input
                        id="password"
                        type="password"
                        required
                        tabindex="2"
                        autocomplete="current-password"
                        v-model="form.password"
                        :placeholder="t('auth.login.password_placeholder')"
                    />
                    <InputError :message="form.errors.password" />
                </div>

                <div class="flex items-center justify-between" tabindex="3">
                    <Label for="remember" class="flex items-center space-x-3">
                        <Checkbox id="remember" v-model:checked="form.remember" tabindex="4" />
                        <span>{{ t('auth.login.remember') }}</span>
                    </Label>
                </div>

                <Button type="submit" class="mt-4 w-full" tabindex="4" :disabled="form.processing">
                    <LoaderCircle v-if="form.processing" class="h-4 w-4 animate-spin" />
                    {{ t('auth.login.submit') }}
                </Button>
            </div>
        </form>
    </AuthBase>
</template>
