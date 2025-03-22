<script setup lang="ts">
import Can from '@/components/Can.vue';
import InputError from '@/components/InputError.vue';
import Badge from '@/components/ui/badge/Badge.vue';
import Button from '@/components/ui/button/Button.vue';
import Dialog from '@/components/ui/dialog/Dialog.vue';
import DialogContent from '@/components/ui/dialog/DialogContent.vue';
import DialogDescription from '@/components/ui/dialog/DialogDescription.vue';
import DialogFooter from '@/components/ui/dialog/DialogFooter.vue';
import DialogHeader from '@/components/ui/dialog/DialogHeader.vue';
import DialogTitle from '@/components/ui/dialog/DialogTitle.vue';
import Input from '@/components/ui/input/Input.vue';
import Label from '@/components/ui/label/Label.vue';
import Select from '@/components/ui/select/Select.vue';
import SelectContent from '@/components/ui/select/SelectContent.vue';
import SelectItem from '@/components/ui/select/SelectItem.vue';
import SelectTrigger from '@/components/ui/select/SelectTrigger.vue';
import SelectValue from '@/components/ui/select/SelectValue.vue';
import Textarea from '@/components/ui/textarea/Textarea.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import ProjectLayout from '@/layouts/project/Layout.vue';
import { label, statusColor } from '@/lib/helpers';
import { Head, router, useForm, usePage } from '@inertiajs/vue3';
import { Edit } from 'lucide-vue-next';
import { computed, ref } from 'vue';

const page = computed<any>(() => usePage().props);

const form = useForm({
    name: page.value.project?.name,
    description: page.value.project?.description,
    status: page.value.project?.status,
    external_link: page.value.project?.external_link,
});

const submit = () => {
    form.patch(route('project.update', page.value.project.id), {
        preserveScroll: true,
        onSuccess: () => {
            modal.value = false;
        },
    });
}

const modal = ref(false);
</script>

<template>
    <Head :title="page.project?.name" />
    <AppLayout>
        <ProjectLayout>
            <div class="flex flex-col space-y-6">
                <div class="flex justify-between">
                    <div>
                        <h1 class="text-4xl font-semibold leading-tight">{{ page.project?.name }}</h1>
                        <Badge :class="statusColor(page.project?.status)" class="">{{ label(page.project?.status) }}</Badge>
                    </div>

                    <Can permission="project:update">
                        <Button variant="ghost" size="icon" @click="modal = true">
                            <component :is="Edit" class="size-5" />
                        </Button>
                    </Can>
                </div>
                <p class="mt-3 whitespace-pre-wrap leading-8 text-gray-500">{{ page.project?.description }}</p>
            </div>

            <Dialog v-model:open="modal">
                <DialogContent class="sm:max-w-[425px]">
                    <DialogHeader>
                        <DialogTitle>Edit project</DialogTitle>
                        <DialogDescription>
                             Edit general information about the project.
                        </DialogDescription>
                    </DialogHeader>
                    <form @submit.prevent="submit" class="space-y-6">
                        <div class="mt-2 grid gap-2">
                            <Label for="name">Name</Label>
                            <Input v-model="form.name" id="name" class="mt-1 block w-full" />
                            <InputError class="mt-2" :message="form.errors.name" />
                        </div>
                        <div class="mt-2 grid gap-2">
                            <Label for="description">Description</Label>
                            <Textarea rows="10" v-model="form.description" id="description" class="mt-1 block w-full" />
                            <InputError class="mt-2" :message="form.errors.description" />
                        </div>
                        <div class="mt-2 grid gap-2">
                            <Label for="status">Status</Label>
                            <Select v-model="form.status" id="status" class="mt-1 block">
                                <SelectTrigger class="">
                                    <SelectValue placeholder="Select a status" />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectItem v-for="(status, i) in page.enum.project_status" :key="i" :value="status"> {{ label(status) }} </SelectItem>
                                </SelectContent>
                            </Select>
                            <InputError class="mt-2" :message="form.errors.status" />
                        </div>
                        <div class="mt-2 grid gap-2">
                            <Label for="external_link">External Link</Label>
                            <Input v-model="form.external_link" id="external_link" class="mt-1 block w-full" />
                            <InputError class="mt-2" :message="form.errors.external_link" />
                        </div>
                        <DialogFooter>
                            <Button type="submit" :disabled="form.processing"> Save </Button>
                        </DialogFooter>
                    </form>
                </DialogContent>
            </Dialog>
        </ProjectLayout>
    </AppLayout>
</template>
