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

const handleChangeStatus = (value: any) => {
    if (!value || value.split('-').length !== 2) return;
    router.patch(route('project.member.checkout', [page.value.project?.id, value.split('-')[1]]), {
        status: value.split('-')[0],
    });
};

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

        <!-- <div class="flex flex-col gap-4 rounded-xl p-4">
            <div class="relative flex-1 rounded-xl dark:border-sidebar-border md:min-h-min">
                <div class="flex flex-col items-start justify-between rounded-lg p-5 sm:flex-row">
                    <div>
                        <h1 class="text-4xl font-semibold leading-tight">{{ page.project?.name }}</h1>
                        <Badge :class="statusColor(page.project?.status)" class="mt-2">{{ label(page.project?.status) }}</Badge>
                        <p class="mt-3 leading-8 text-gray-500 whitespace-pre-wrap">{{ page.project?.description }}</p>
                    </div>
                    <div class="mt-3 flex items-center gap-3">
                        <Can permission="project:update">
                            <Button :as="Link" size="icon" variant="ghost" :href="`/project/${page.project.id}/edit`">
                                <component :is="Edit" class="size-5" />
                            </Button>
                        </Can>
                        <Dialog v-model:open="modal">
                            <DialogTrigger as-child>
                                <Button :disabled="!can('project:join')">
                                    <component :is="UserPlus2" class="size-5" />
                                    Join project
                                </Button>
                            </DialogTrigger>
                            <DialogContent class="sm:max-w-[425px]">
                                <DialogHeader>
                                    <DialogTitle>Join project</DialogTitle>
                                    <DialogDescription> We are excited to have you on board. Please select a role to join us as. </DialogDescription>
                                </DialogHeader>
                                <form @submit.prevent="submit" class="space-y-6">
                                    <div class="mt-2 grid gap-2">
                                        <Label for="join_as">Join As</Label>
                                        <Select v-model="form.role" id="join_as" class="mt-1 block">
                                            <SelectTrigger class="">
                                                <SelectValue placeholder="Select a role" />
                                            </SelectTrigger>
                                            <SelectContent>
                                                <SelectItem v-for="(role, i) in page.roles" :key="i" :value="role"> {{ label(role) }} </SelectItem>
                                            </SelectContent>
                                        </Select>
                                        <InputError class="mt-2" :message="form.errors.role" />
                                    </div>
                                    <DialogFooter>
                                        <Button type="submit" :disabled="form.processing"> Join </Button>
                                    </DialogFooter>
                                </form>
                            </DialogContent>
                        </Dialog>
                    </div>
                </div>
            </div>

            <Separator />

            <div v-if="page.members && page.members.length">
                <div
                    v-for="member in page.members"
                    :key="member.id"
                    class="flex items-center justify-between rounded-lg p-4 transition-colors hover:bg-stone-100 dark:hover:bg-stone-900"
                >
                    <div>
                        <p class="font-semibold" :class="member.id === page.auth.user.id ? 'text-red-500' : ''">{{ member.name }}</p>
                        <p class="text-sm text-gray-500">{{ member.email }}</p>
                    </div>

                    <div class="flex items-center gap-2">
                        <component :is="User" class="size-5" />
                        <span>{{ label(member.membership.role) }}</span>
                    </div>

                    <Can :permission="(page.auth.user.id === member.id && member.membership.role === 'owner') || member.role === 'admin' ? false : 'project:member_checkout'">
                        <Select @update:model-value="handleChangeStatus" id="status" class="mt-1 block">
                            <SelectTrigger class="w-min">
                                <Badge :class="statusColor(member.membership.status)">{{ label(member.membership.status) }}</Badge>
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem hide-indicator class="pl-2" v-for="(process, i) in page.process" :key="i" :value="`${process}-${member.membership.id}`"> 
                                    <Badge :class="statusColor(process)">{{ label(process) }}</Badge>
                                </SelectItem>
                            </SelectContent>
                        </Select>
                        <template #else>
                            <Badge :class="statusColor(member.membership.status)">{{ label(member.membership.status) }}</Badge>
                        </template>
                    </Can>
                </div>
            </div>

            <div v-else>
                <p class="text-center mt-4 text-gray-500">No members</p>
            </div>
        </div> -->
    </AppLayout>
</template>
