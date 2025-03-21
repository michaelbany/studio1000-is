<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Textarea } from '@/components/ui/textarea';
import AppLayout from '@/layouts/AppLayout.vue';
import { label } from '@/lib/helpers';
import { Head, useForm, usePage } from '@inertiajs/vue3';

const form = useForm({
    name: '',
    description: '',
    join_as: '',
});

const page = usePage().props;

const submit = () => {
    form.post(route('project.store'), {
        preserveScroll: true,
    });
};
</script>

<template>
    <Head title="Create Project" />
    <AppLayout>
        <div class="flex flex-col gap-4 rounded-xl p-4">
            <div class="relative flex-1 rounded-xl dark:border-sidebar-border md:min-h-min">
                <div class="flex flex-col justify-between rounded-lg p-5 sm:flex-row">
                    <div>
                        <h1 class="text-4xl font-semibold leading-tight">Create Project</h1>

                        <p class="leading-8 text-gray-500">Create a new project</p>
                    </div>
                </div>
            </div>

            <form @submit.prevent="submit" class="space-y-6">
                <div class="grid gap-2">
                    <Label for="name">Name</Label>
                    <Input id="name" class="mt-1 block w-full" v-model="form.name" required autocomplete="name" placeholder="Project Name" />
                    <InputError class="mt-2" :message="form.errors.name" />
                </div>

                <div class="grid gap-2">
                    <Label for="description">Description</Label>
                    <Textarea rows="15" v-model="form.description" id="description" class="mt-1 block w-full" required placeholder="Project Description" />
                    <InputError class="mt-2" :message="form.errors.description" />
                </div>

                <div class="grid gap-2">
                    <Label for="external_link">Join As</Label>
                    <Select v-model="form.join_as" id="join_as" class="mt-1 block">
                        <SelectTrigger class="w-[180px]">
                            <SelectValue placeholder="Select a role" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem v-for="(role, i) in page.roles" :key="i" :value="role"> {{ label(role) }} </SelectItem>
                        </SelectContent>
                    </Select>
                    <InputError class="mt-2" :message="form.errors.join_as" />
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
    </AppLayout>
</template>
