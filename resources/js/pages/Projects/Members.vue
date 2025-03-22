<script setup lang="ts">
import Can from '@/components/Can.vue';
import HeadingSmall from '@/components/HeadingSmall.vue';
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
import NumberField from '@/components/ui/number-field/NumberField.vue';
import NumberFieldContent from '@/components/ui/number-field/NumberFieldContent.vue';
import NumberFieldDecrement from '@/components/ui/number-field/NumberFieldDecrement.vue';
import NumberFieldIncrement from '@/components/ui/number-field/NumberFieldIncrement.vue';
import NumberFieldInput from '@/components/ui/number-field/NumberFieldInput.vue';
import Select from '@/components/ui/select/Select.vue';
import SelectContent from '@/components/ui/select/SelectContent.vue';
import SelectItem from '@/components/ui/select/SelectItem.vue';
import SelectTrigger from '@/components/ui/select/SelectTrigger.vue';
import SelectValue from '@/components/ui/select/SelectValue.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import ProjectLayout from '@/layouts/project/Layout.vue';
import { label, statusColor } from '@/lib/helpers';
import { Head, useForm, usePage } from '@inertiajs/vue3';
import { Plus, User } from 'lucide-vue-next';
import { computed, ref } from 'vue';

const page = computed<any>(() => usePage().props);

const slotModal = ref(false);

const handleChangeStatus = (value: any) => {
    //
};

const slotForm = useForm({
    role: '',
    label: '',
    count: 1,
});

const submitSlot = () => {
    slotForm.post(route('project.members.store', page.value.project.id), {
        preserveScroll: true,
        onSuccess: () => {
            slotModal.value = false;
        },
    });
};
</script>

<template>
    <Head :title="page.project?.name" />
    <AppLayout>
        <ProjectLayout>
            <div class="flex flex-col space-y-6">
                <div class="flex justify-between">
                    <HeadingSmall title="Members" description="Manage project members and their roles." />

                    <Can permission="project:update">
                        <Button variant="ghost" size="icon" @click="slotModal = true">
                            <component :is="Plus" class="size-5" />
                        </Button>
                    </Can>
                </div>
                <pre>
                    {{ page.slots }}
                </pre>

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

                        <Can
                            :permission="
                                (page.auth.user.id === member.id && member.membership.role === 'owner') || member.role === 'admin'
                                    ? false
                                    : 'project:member_checkout'
                            "
                        >
                            <Select @update:model-value="handleChangeStatus" id="status" class="mt-1 block">
                                <SelectTrigger class="w-min">
                                    <Badge :class="statusColor(member.membership.status)">{{ label(member.membership.status) }}</Badge>
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectItem
                                        hide-indicator
                                        class="pl-2"
                                        v-for="(process, i) in page.process"
                                        :key="i"
                                        :value="`${process}-${member.membership.id}`"
                                    >
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
                    <p class="text-sm text-muted-foreground">No members found.</p>
                </div>
            </div>

            <Dialog v-model:open="slotModal">
                <DialogContent class="sm:max-w-[425px]">
                    <DialogHeader>
                        <DialogTitle>Create member slot</DialogTitle>
                        <DialogDescription> Create a new member slot for this project. You can create multiple at once. </DialogDescription>
                    </DialogHeader>
                    <form @submit.prevent="submitSlot" class="space-y-6">
                        <div class="mt-2 grid gap-2">
                            <Label for="label">Label</Label>
                            <Input v-model="slotForm.label" id="label" class="mt-1 block w-full" placeholder="e.g. Big Scary Wolf" />
                            <InputError class="mt-2" :message="slotForm.errors.label" />
                        </div>
                        <div class="mt-2 grid gap-2">
                            <Label for="role">Role</Label>
                            <Select v-model="slotForm.role" id="role" class="mt-1 block">
                                <SelectTrigger class="">
                                    <SelectValue placeholder="Select a role" />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectItem v-for="(status, i) in page.enum.member_role" :key="i" :value="status">
                                        {{ label(status) }}
                                    </SelectItem>
                                </SelectContent>
                            </Select>
                            <InputError class="mt-2" :message="slotForm.errors.role" />
                        </div>
                        <div class="mt-2 grid gap-2">
                            <InputError class="mt-2" :message="slotForm.errors.count" />
                        </div>

                        <DialogFooter>
                            <div class="flex w-full items-center justify-between">
                                <NumberField class="w-[100px]" id="count" v-model="slotForm.count" :min="1" :max="30">
                                    <NumberFieldContent>
                                        <NumberFieldDecrement />
                                        <NumberFieldInput />
                                        <NumberFieldIncrement />
                                    </NumberFieldContent>
                                </NumberField>
                                <Button type="submit" :disabled="slotForm.processing"> Save </Button>
                            </div>
                        </DialogFooter>
                    </form>
                </DialogContent>
            </Dialog>
        </ProjectLayout>
    </AppLayout>
</template>
