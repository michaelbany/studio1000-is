<script setup lang="ts">
import { Badge } from '@/components/ui/badge';
import Button from '@/components/ui/button/Button.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { label, statusColor } from '@/lib/helpers';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, usePage } from '@inertiajs/vue3';
import { Plus } from 'lucide-vue-next';

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
            <div class="relative min-h-[100vh] flex-1 rounded-xl dark:border-sidebar-border md:min-h-min">
                <div class="flex flex-col justify-between rounded-lg p-5 sm:flex-row">
                    <div>
                        <h1 class="text-4xl font-semibold leading-tight">Dashboard</h1>

                        <p class="leading-8 text-gray-500">Welcome to your Dashboard 🎉</p>
                    </div>

                    <div class="flex flex-wrap items-center gap-3 pt-3 sm:pt-0">
                        <Button :as="Link" href="/projects/create">
                            <component :is="Plus" class="size-5" />
                            Create project
                        </Button>
                    </div>
                </div>
            </div>

            <div v-if="projects && projects.length">
                <div
                    v-for="project in projects"
                    :key="project.id"
                    class="flex items-center justify-between rounded-lg p-4 transition-colors hover:bg-stone-100 dark:hover:bg-stone-900"
                >
                    <div>
                        <p class="font-semibold">{{ project.name }}</p>
                        <p class="text-sm text-gray-500">{{ project.description }}</p>
                    </div>

                    <div class="flex items-center gap-2">
                        <!-- {{ project }} -->
                        <!-- <component :is="User" class="size-5" /> -->
                        <!-- <span>{{ label(member.membership.role) }}</span> -->
                    </div>
                    <!-- <Badge :class="statusColor(member.membership.status)">{{ label(member.membership.status) }}</Badge> -->
                </div>
            </div>

            <div v-else>
                <p class="text-center text-gray-500">No projects</p>
            </div>
        </div>
    </AppLayout>
</template>
