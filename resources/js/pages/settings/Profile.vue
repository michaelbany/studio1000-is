<script setup lang="ts">
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';

import HeadingSmall from '@/components/HeadingSmall.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import Separator from '@/components/ui/separator/Separator.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import SettingsLayout from '@/layouts/settings/Layout.vue';
import { type BreadcrumbItem, type SharedData, type User } from '@/types';
import { Calendar, Download } from 'lucide-vue-next';

interface Props {
    mustVerifyEmail: boolean;
    status?: string;
}

defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Profile settings',
        href: '/settings/profile',
    },
];

const page = usePage<SharedData>();
const user = page.props.auth.user as User;

const form = useForm({
    name: user.name,
    email: user.email,
});

const submit = () => {
    form.patch(route('profile.update'), {
        preserveScroll: true,
    });
};

const calendarSubscribeUrl = `webcal://${page.props.ziggy.url.replace(/^https?:\/\//, '')}/calendar/user/${page.props.auth.user.id}/${page.props.auth.user.calendar_token}.ics`;
const calendarDownloadUrl = `${page.props.ziggy.url}/calendar/user/${page.props.auth.user.id}/${page.props.auth.user.calendar_token}.ics`;
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <Head title="Profile settings" />

        <SettingsLayout>
            <div class="flex flex-col space-y-6">
                <HeadingSmall title="Profile information" description="Update your name and email address" />

                <form @submit.prevent="submit" class="space-y-6">
                    <div class="grid gap-2">
                        <Label for="name">Name</Label>
                        <Input id="name" class="mt-1 block w-full" v-model="form.name" required autocomplete="name" placeholder="Full name" />
                        <InputError class="mt-2" :message="form.errors.name" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="email">Email address</Label>
                        <Input
                            id="email"
                            type="email"
                            class="mt-1 block w-full"
                            v-model="form.email"
                            required
                            autocomplete="username"
                            placeholder="Email address"
                        />
                        <InputError class="mt-2" :message="form.errors.email" />
                    </div>

                    <div v-if="mustVerifyEmail && !user.email_verified_at">
                        <p class="-mt-4 text-sm text-muted-foreground">
                            Your email address is unverified.
                            <Link
                                :href="route('verification.send')"
                                method="post"
                                as="button"
                                class="text-foreground underline decoration-neutral-300 underline-offset-4 transition-colors duration-300 ease-out hover:!decoration-current dark:decoration-neutral-500"
                            >
                                Click here to resend the verification email.
                            </Link>
                        </p>

                        <div v-if="status === 'verification-link-sent'" class="mt-2 text-sm font-medium text-green-600">
                            A new verification link has been sent to your email address.
                        </div>
                    </div>

                    <div class="flex items-center gap-4">
                        <Button :disabled="form.processing">Save</Button>

                        <Transition
                            enter-active-class="transition ease-in-out"
                            enter-from-class="opacity-0"
                            leave-active-class="transition ease-in-out"
                            leave-to-class="opacity-0"
                        >
                            <p v-show="form.recentlySuccessful" class="text-sm text-neutral-600">Saved.</p>
                        </Transition>
                    </div>
                </form>
            </div>

            <div class="flex flex-col space-y-6">
                <HeadingSmall title="Calendar and Events" description="Manage your calendar and events you are part of" />

                <div class="space-x-2">
                            <Button as="a" size="sm" variant="default" :href="calendarSubscribeUrl" target="_blank">
                                <component :is="Calendar" class="size-4" />
                                Subscribe to all
                            </Button>
                            <Button as="a" size="sm" variant="secondary" :href="calendarDownloadUrl" target="_blank">
                                <component :is="Download" class="size-4" />
                                Download all
                            </Button>
                        </div>

                <div v-for="schedule in page.props.schedules" :key="schedule.id" class="flex items-start rounded-lg border p-4">
                    <div class="w-10 flex-shrink-0">
                        <component :is="Calendar" class="mt-1 size-5 text-primary" />
                    </div>
                    <div class="flex-1">
                        <h3 class="text-lg font-semibold">{{ schedule.project.name }}</h3>
                        <Separator class="my-2" />
                        <div class="ml-auto space-x-2">
                            <Button as="a" size="sm" variant="default" :href="calendarSubscribeUrl + '?project=' + schedule.project.id" target="_blank">
                                <component :is="Calendar" class="size-4" />
                                Subscribe
                            </Button>
                            <Button as="a" size="sm" variant="outline" :href="calendarDownloadUrl + '?project=' + schedule.project.id" target="_blank">
                                <component :is="Download" class="size-4" />
                                Download
                            </Button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- <DeleteUser /> -->
        </SettingsLayout>
    </AppLayout>
</template>
