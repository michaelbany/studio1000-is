<script setup lang="ts">
import Can from '@/components/Can.vue';
import Badge from '@/components/ui/badge/Badge.vue';
import Button from '@/components/ui/button/Button.vue';
import Separator from '@/components/ui/separator/Separator.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { ellipsis, label, statusColor } from '@/lib/helpers';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import { Link2, Plus, Projector, Users } from 'lucide-vue-next';
// import DashboardWhite from '../../images/bg-white.png';
// import DashboardDark from '../../images/bg-dark.png';

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
        <!-- <div
            style="background-image: url('/images/bg-white.png'); background-size: cover; background-position: center;"
            class="mt-6 flex h-52 w-full items-center justify-center overflow-hidden rounded-xl"
        >
        </div> -->
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

            <div v-if="!(projects && projects.length)">
                <p class="text-sm text-center text-muted-foreground">No projects found.</p>
            </div>

            <div v-else class="space-y-4">
                <div
                    v-for="project in projects"
                    :key="project.id"
                    class="flex cursor-pointer items-start rounded-lg border p-4"
                    @click="router.visit(route('project.summary', project.id))"
                >
                    <div class="w-10 flex-shrink-0">
                        <component :is="Projector" class="mt-1 size-5 text-primary" />
                    </div>
                    <div class="flex-1">
                        <h3 class="text-lg font-semibold">{{ project.name }}</h3>

                        <Separator class="my-2" v-if="project.description" />
                        <p class="text-sm text-muted-foreground">{{ ellipsis(project.description, { length: 200 }) }}</p>

                        <Button
                            variant="link"
                            as="a"
                            v-if="project.external_link"
                            :href="project.external_link"
                            target="_blank"
                            class="cursor-pointer p-0 hover:text-red-500"
                            @click.stop
                        >
                            <component :is="Link2" class="size-5" />
                        </Button>
                    </div>
                    <div class="flex items-center gap-2">
                        <Badge :class="statusColor(project?.status)">{{ label(project?.status) }}</Badge>
                        <div class="flex items-center gap-2">
                            <component :is="Users" class="size-5" />
                            <span>{{
                                new Set(
                                    project.members.filter((member: any) => member.membership.status === 'approved').map((member: any) => member.id),
                                ).size
                            }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
