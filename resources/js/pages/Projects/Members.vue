<script setup lang="ts">
import Can from '@/components/Can.vue';
import HeadingSmall from '@/components/HeadingSmall.vue';
import Badge from '@/components/ui/badge/Badge.vue';
import Select from '@/components/ui/select/Select.vue';
import SelectContent from '@/components/ui/select/SelectContent.vue';
import SelectItem from '@/components/ui/select/SelectItem.vue';
import SelectTrigger from '@/components/ui/select/SelectTrigger.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import ProjectLayout from '@/layouts/project/Layout.vue';
import { label, statusColor } from '@/lib/helpers';
import { Head, usePage } from '@inertiajs/vue3';
import { User } from 'lucide-vue-next';
import { computed } from 'vue';

const page = computed<any>(() => usePage().props);

const handleChangeStatus = (value: any) => {
    //
};
</script>

<template>
    <Head :title="page.project?.name" />
    <AppLayout>
        <ProjectLayout>
            <div class="flex flex-col space-y-6">
                <HeadingSmall title="Members" description="Manage project members and their roles." />
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
                    <p class="text-sm text-muted-foreground">No members found.</p>
                </div>
            </div>
        </ProjectLayout>
    </AppLayout>
</template>
