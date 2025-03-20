<script setup lang="ts">
import { Badge } from '@/components/ui/badge';
import { Separator } from '@/components/ui/separator';
import AppLayout from '@/layouts/AppLayout.vue';
import { label, statusColor } from '@/lib/helpers';
import { useForm, usePage } from '@inertiajs/vue3';
import { User } from 'lucide-vue-next';

const form = useForm({
    name: '',
    description: '',
    join_as: '',
});

const project = usePage().props.project as any;
</script>

<template>
    <AppLayout>
        <div class="flex flex-col gap-4 rounded-xl p-4">
            <div class="relative min-h-[100vh] flex-1 rounded-xl dark:border-sidebar-border md:min-h-min">
                <div class="flex flex-col justify-between rounded-lg p-5 sm:flex-row">
                    <div>
                        <h1 class="text-4xl font-semibold leading-tight">{{ project?.name }}</h1>
                        <p class="leading-8 text-gray-500">{{ project?.description }}</p>
                    </div>
                </div>
            </div>

            <Separator />

            <div v-if="project.members && project.members.length">
                <div
                    v-for="member in project.members"
                    :key="member.id"
                    class="flex items-center justify-between rounded-lg p-4 transition-colors hover:bg-stone-100 dark:hover:bg-stone-900"
                >
                    <div>
                        <p class="font-semibold">{{ member.name }}</p>
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
