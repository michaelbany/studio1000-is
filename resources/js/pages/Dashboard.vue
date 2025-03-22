<script setup lang="ts">
import Can from '@/components/Can.vue';
import Badge from '@/components/ui/badge/Badge.vue';
import Button from '@/components/ui/button/Button.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { ellipsis, label, statusColor } from '@/lib/helpers';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import { Link2, Plus, Users } from 'lucide-vue-next';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
    },
];

const projects = usePage().props.projects as any;
</script>

<template>
    <Head title="Dashboard" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex flex-col gap-4 rounded-xl p-4">
            <div class="relative flex-1 rounded-xl dark:border-sidebar-border md:min-h-min">
                <div class="flex flex-col justify-between rounded-lg p-5 sm:flex-row">
                    <div>
                        <h1 class="text-4xl font-semibold leading-tight">Dashboard</h1>

                        <p class="leading-8 text-gray-500">Welcome to the Dashboard</p>
                    </div>

                    <div class="flex flex-wrap items-center gap-3 pt-3 sm:pt-0">
                        <Can permission="project:create">
                            <Button :as="Link" href="/project/create">
                                <component :is="Plus" class="size-5" />
                                Create project
                            </Button>
                        </Can>
                    </div>
                </div>
            </div>

            <div v-if="projects && projects.length">
                <div
                    v-for="project in projects"
                    :key="project.id"
                    class="flex cursor-pointer items-center justify-between rounded-lg p-4 transition-colors hover:bg-stone-100 dark:hover:bg-stone-900"
                    @click="router.visit(route('project.summary', project.id))"
                >
                    <div>
                        <p class="font-semibold">{{ project.name }}</p>
                        <p class="max-w-[800px] text-sm text-gray-500">{{ ellipsis(project.description, { length: 200 }) }}</p>
                    </div>

                    <Badge :class="statusColor(project?.status)" class="mt-2">{{ label(project?.status) }}</Badge>

                    <div class="flex items-center gap-2">
                        <div class="flex items-center gap-2">
                            <component :is="Users" class="size-5" />
                            <span>{{
                                new Set(
                                    project.members.filter((member: any) => member.membership.status === 'approved').map((member: any) => member.id),
                                ).size
                            }}</span>
                        </div>
                        <Button
                            v-if="project.external_link"
                            size="icon"
                            variant="ghost"
                            as="a"
                            :href="project.external_link"
                            target="_blank"
                            class="cursor-pointer"
                        >
                            <component :is="Link2" class="size-5" />
                        </Button>
                    </div>
                </div>
            </div>

            <div v-else>
                <p class="text-center text-gray-500">No projects</p>
            </div>
        </div>
    </AppLayout>
</template>
