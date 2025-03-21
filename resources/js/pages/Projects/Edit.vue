<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Select, SelectTrigger, SelectValue } from '@/components/ui/select';
import SelectContent from '@/components/ui/select/SelectContent.vue';
import SelectItem from '@/components/ui/select/SelectItem.vue';
import { Textarea } from '@/components/ui/textarea';
import AppLayout from '@/layouts/AppLayout.vue';
import { label } from '@/lib/helpers';
import { Head, useForm, usePage } from '@inertiajs/vue3';


const page = usePage().props as any;

const form = useForm({
    name: page.project?.name,
    description: page.project?.description,
    external_link: page.project?.external_link,
    status: page.project?.status,
});

const submit = () => {
    form.patch(route('project.update', page.project.id), {
        preserveScroll: true,
    });
};
</script>

<template>
    <Head title="Edit Project" />
    <AppLayout>
        <div class="flex flex-col gap-4 rounded-xl p-4">
            <div class="relative flex-1 rounded-xl dark:border-sidebar-border md:min-h-min">
                <div class="flex flex-col justify-between rounded-lg p-5 sm:flex-row">
                    <div>
                        <h1 class="text-4xl font-semibold leading-tight">Edit Project</h1>

                        <p class="leading-8 text-gray-500">Edit the <span class="font-medium">{{ page.project?.name }}</span> project</p>
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
                    <Label for="external_link">Link to Project</Label>
                    <Input v-model="form.external_link" id="external_link" class="mt-1 block w-full" placeholder="External Link" />
                    <InputError class="mt-2" :message="form.errors.external_link" />
                </div>

                <div class="grid gap-2">
                    <Label for="status">Status</Label>
                    <Select v-model="form.status" id="status" class="mt-1 block">
                        <SelectTrigger class="w-[180px]">
                            <SelectValue placeholder="Select project status" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem v-for="(status, i) in page.status" :key="i" :value="status"> {{ label(status) }} </SelectItem>
                        </SelectContent>
                    </Select>
                    <InputError class="mt-2" :message="form.errors.status" />
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
