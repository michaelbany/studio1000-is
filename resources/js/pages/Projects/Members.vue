<script setup lang="ts">
import Can from '@/components/Can.vue';
import HeadingSmall from '@/components/HeadingSmall.vue';
import InputError from '@/components/InputError.vue';
import Badge from '@/components/ui/badge/Badge.vue';
import Button from '@/components/ui/button/Button.vue';
import Collapsible from '@/components/ui/collapsible/Collapsible.vue';
import CollapsibleContent from '@/components/ui/collapsible/CollapsibleContent.vue';
import CollapsibleTrigger from '@/components/ui/collapsible/CollapsibleTrigger.vue';
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
import Separator from '@/components/ui/separator/Separator.vue';
import { can } from '@/composables/usePermissions';
import AppLayout from '@/layouts/AppLayout.vue';
import ProjectLayout from '@/layouts/project/Layout.vue';
import { label, statusColor } from '@/lib/helpers';
import { Head, router, useForm, usePage } from '@inertiajs/vue3';
import { ChevronsDownUp, Plus, UserPlus2 } from 'lucide-vue-next';
import { computed, ref } from 'vue';
import { toast } from 'vue-sonner';

const page = computed<any>(() => usePage().props);

const slotModal = ref(false);
const slotEditModal = ref(false);

const roleOrder = [
    'director',
    'writer',
    'producer',
    'camera_operator',
    'lighting_technician',
    'set_designer',
    'makeup_artist',
    'costume_designer',
    'actor',
    'editor',
    'vfx_supervisor',
    'sound_engineer',
    'owner',
];

const slotForm = useForm({
    role: '',
    label: '',
    count: 1,
});

const slotEditForm = useForm<{
    label: string;
    id: number | string;
    role: string;
    user: any;
}>({
    label: '',
    role: '',
    id: '',
    user: '',
});

const submitSlot = () => {
    slotForm.post(route('project.members.store', page.value.project.id), {
        preserveScroll: true,
        onSuccess: () => {
            slotModal.value = false;
        },
    });
};

const submitEditSlot = (update?: boolean | undefined) => {
    if (update) {
        slotEditForm.put(route('project.members.update', [page.value.project.id, slotEditForm.id]), {
            preserveScroll: true,
            onSuccess: () => {
                slotEditModal.value = false;
            },
        });
    } else {
        slotEditForm.delete(route('project.members.destroy', [page.value.project.id, slotEditForm.id]), {
            preserveScroll: true,
            onSuccess: () => {
                slotEditModal.value = false;
            },
        });
    }
};

const submitCheckout = (value: any) => {
    if (!value || value.split('-').length !== 2) return;
    router.post(
        route('project.members.checkout', [page.value.project?.id, value.split('-')[1]]),
        {
            status: value.split('-')[0],
        },
        {
            preserveScroll: true,
            onFinish: () => {
                if (page.value.errors.checkout) {
                    toast.error(page.value.errors.checkout);
                }
            },
        },
    );
};

const submitApply = (slot: any) => {
    router.post(
        route('project.members.apply', [page.value.project.id, slot.id]),
        {},
        {
            preserveScroll: true,
            onFinish: () => {
                if (page.value.errors.apply) {
                    toast.error(page.value.errors.apply);
                }
            },
        },
    );
};

const handleEditSlot = (slot: any) => {
    if (!can('project:update')) {
        return;
    }
    
    slotEditForm.label = slot.label;
    slotEditForm.id = slot.id;
    slotEditForm.role = slot.role;
    slotEditForm.user = slot.user_id;
    slotEditModal.value = true;
};

const membership = computed(() => {
    const sorted = page.value.members
        .map((member: any) => {
            const { membership, ...user } = member;
            return {
                ...membership,
                user_id: user,
            };
        })
        .sort((a:any, b:any) => {
            const nameA = a.user_id?.name?.toLowerCase() ?? '';
            const nameB = b.user_id?.name?.toLowerCase() ?? '';
            return nameA.localeCompare(nameB);
        });
    sorted.push(...page.value.slots);

    return sorted;
});

const unorderedGroups = computed<Record<string, any>>(() => {
    return membership.value.reduce((acc: any, slot: any) => {
        if (!acc[slot.role]) {
            acc[slot.role] = [];
        }

        acc[slot.role].push(slot);

        return acc;
    }, {});
});

const groups = computed<Record<string, any>>(() => {
    const unordered = unorderedGroups.value;

    return Object.fromEntries(
        Object.entries(unordered).sort(([a], [b]) => {
            const weightA = roleOrder.indexOf(a);
            const weightB = roleOrder.indexOf(b);

            const aWeight = weightA === -1 ? 999 : weightA;
            const bWeight = weightB === -1 ? 999 : weightB;

            if (aWeight !== bWeight) {
                return aWeight - bWeight;
            }

            // Pokud mají stejnou váhu (obě nejsou v seznamu), řaď abecedně
            return a.localeCompare(b);
        }),
    );
});
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

                <Separator />

                <Collapsible default-open class="space-y-2" v-for="(group, role) in groups" :key="role">
                    <div class="flex items-center justify-between space-x-4">
                        <h4 class="text-sm font-semibold">{{ label(role) }}s</h4>
                        <CollapsibleTrigger as-child>
                            <Button variant="ghost" size="sm" class="w-9 p-0">
                                <component :is="ChevronsDownUp" class="h-4 w-4" />
                                <span class="sr-only">Toggle</span>
                            </Button>
                        </CollapsibleTrigger>
                    </div>

                    <CollapsibleContent class="relative space-y-2">
                        <div
                            v-for="(member, i) in group"
                            :key="i"
                            @click="() => handleEditSlot(member)"
                            :class="can('project:update') ? 'cursor-pointer' : ''"
                            class="rounded-md border px-4 py-3 text-sm transition-colors hover:border-muted"
                        >
                            <div class="flex items-center justify-between">
                                <div class="flex flex-col gap-2">
                                    <span class="text-xs font-medium text-muted-foreground">
                                        {{ member.label || 'Untitled' }}
                                    </span>
                                    <Separator orientation="horizontal" class="w-[100px]" />
                                    <div v-if="member.user_id">
                                        <p class="font-semibold">{{ member.user_id.name }}</p>
                                        <p class="text-xs text-gray-500">{{ member.user_id.email }}</p>
                                    </div>
                                    <div v-else>
                                        <p class="italic">Still available</p>
                                    </div>
                                </div>

                                <template v-if="member.user_id">
                                    <Can
                                        :permission="
                                            (page.auth.user.id === member.user_id.id && member.role === 'owner') || member.user_id.role === 'admin'
                                                ? false
                                                : 'project:member_checkout'
                                        "
                                    >
                                        <Select @update:model-value="submitCheckout" id="status" class="mt-1 block">
                                            <SelectTrigger class="w-min">
                                                <Badge :class="statusColor(member.status)">{{ label(member.status) }}</Badge>
                                            </SelectTrigger>
                                            <SelectContent>
                                                <SelectItem
                                                    hide-indicator
                                                    class="pl-2"
                                                    v-for="(member_status, i) in page.enum.member_status"
                                                    :key="i"
                                                    :value="`${member_status}-${member.id}`"
                                                >
                                                    <Badge :class="statusColor(member_status)">{{ label(member_status) }}</Badge>
                                                </SelectItem>
                                            </SelectContent>
                                        </Select>
                                        <template #else>
                                            <Badge :class="statusColor(member.status)">{{ label(member.status) }}</Badge>
                                        </template>
                                    </Can>
                                </template>

                                <Button v-else variant="ghost" :disabled="!can('project:join')" size="sm" @click.stop="() => submitApply(member)">
                                    <component :is="UserPlus2" class="size-5" />
                                    Apply
                                </Button>
                            </div>
                        </div>
                    </CollapsibleContent>
                </Collapsible>
            </div>

            <Dialog v-model:open="slotModal">
                <DialogContent class="sm:max-w-[425px]">
                    <DialogHeader>
                        <DialogTitle>Create a member slot</DialogTitle>
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

            <Dialog v-model:open="slotEditModal">
                <DialogContent class="sm:max-w-[425px]">
                    <DialogHeader>
                        <DialogTitle>Edit member slot</DialogTitle>
                        <DialogDescription> Edit a member slot for this project. </DialogDescription>
                    </DialogHeader>
                    <form @submit.prevent="submitEditSlot(true)" class="space-y-6">
                        <div class="mt-2 grid gap-2">
                            <Label for="label">Label</Label>
                            <Input v-model="slotEditForm.label" id="label" class="mt-1 block w-full" placeholder="e.g. Big Scary Wolf" />
                            <InputError class="mt-2" :message="slotEditForm.errors.label" />
                        </div>

                        <DialogFooter>
                            <div class="flex w-full items-center justify-between">
                                <Button
                                    type="button"
                                    variant="destructive"
                                    :disabled="
                                        slotForm.processing ||
                                        (page.auth.user.id === slotEditForm.user?.id && slotEditForm.role === 'owner') ||
                                        slotEditForm.user?.role === 'admin'
                                    "
                                    @click="submitEditSlot(false)"
                                >
                                    {{ slotEditForm.user?.id ? 'Clear slot' : 'Delete' }}
                                </Button>
                                <Button type="submit" :disabled="slotForm.processing"> Save </Button>
                            </div>
                        </DialogFooter>
                    </form>
                </DialogContent>
            </Dialog>
        </ProjectLayout>
    </AppLayout>
</template>
