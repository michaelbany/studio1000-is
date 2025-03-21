<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Dialog, DialogContent, DialogDescription, DialogFooter, DialogHeader, DialogTitle, DialogTrigger } from '@/components/ui/dialog';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Separator } from '@/components/ui/separator';
import { can } from '@/composables/usePermissions';
import AppLayout from '@/layouts/AppLayout.vue';
import { label, statusColor } from '@/lib/helpers';
import { Head, useForm, usePage } from '@inertiajs/vue3';
import { User, UserPlus2 } from 'lucide-vue-next';
import { computed, ref } from 'vue';

const form = useForm({
    role: '',
});

const submit = () => {
    form.post(route('project.member.store', page.value.project.id), {
        preserveScroll: true,
        errorBag: 'joinProject',
        onSuccess: () => {
            form.reset();
            modal.value = false;
        },
    });
};

const modal = ref(false);
const page = computed(() => usePage().props as any);
</script>

<template>
    <Head :title="page.project?.name" />
    <AppLayout>
        <div class="flex flex-col gap-4 rounded-xl p-4">
            <div class="relative min-h-[100vh] flex-1 rounded-xl dark:border-sidebar-border md:min-h-min">
                <div class="flex flex-col justify-between rounded-lg p-5 sm:flex-row">
                    <div>
                        <h1 class="text-4xl font-semibold leading-tight">{{ page.project?.name }}</h1>
                        <p class="leading-8 text-gray-500">{{ page.project?.description }}</p>
                    </div>
                    <div class="flex flex-wrap items-center gap-3 pt-3 sm:pt-0">
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
                                    <DialogDescription> 
                                        We are excited to have you on board. Please select a role to join us as.    
                                    </DialogDescription>
                                </DialogHeader>
                                <form @submit.prevent="submit" class="space-y-6">
                                    <div class="grid gap-2 mt-2">
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

            <div v-if="page.project.members && page.project.members.length">
                <div
                    v-for="member in page.project.members"
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
                    <Badge :class="statusColor(member.membership.status)">{{ label(member.membership.status) }}</Badge>
                </div>
            </div>

            <div v-else>
                <p class="text-center text-gray-500">No members</p>
            </div>
        </div>
    </AppLayout>
</template>
