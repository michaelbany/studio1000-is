<script setup lang="ts">
import Heading from '@/components/Heading.vue';
import { Button } from '@/components/ui/button';
import { Separator } from '@/components/ui/separator';
import { type NavItem } from '@/types';
import { Link, usePage } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

const page = computed<any>(() => usePage().props);

const sidebarNavItems = ref<NavItem[]>([
    {
        title: 'Summary',
        href: `/project/${page.value.project.id}/summary`,
    },
    {
        title: 'Members',
        href: `/project/${page.value.project.id}/members`,
    },
    {
        title: 'Locations',
        href: `/project/${page.value.project.id}/locations`,
    },
    {
        title: 'Schedule',
        href: `/project/${page.value.project.id}/schedule`,
    },
]);

const currentPath = page.value.ziggy?.location ? new URL(page.value.ziggy.location).pathname : '';
</script>

<template>
    <div class="px-4 py-6">
        <Heading
            :title="page.project.name"
            description="Welcome to project workspace. Here you can explore and manage all aspects of this project in one centralized location."
        />

        <div class="flex flex-col space-y-8 md:space-y-0 lg:flex-row lg:space-x-12 lg:space-y-0">
            <aside class="w-full max-w-xl lg:w-48">
                <nav class="flex flex-col space-x-0 space-y-1">
                    <Button
                        v-for="item in sidebarNavItems"
                        :key="item.href"
                        variant="ghost"
                        :class="['w-full justify-start', { 'bg-muted': currentPath === item.href }]"
                        as-child
                    >
                        <Link :href="item.href">
                            {{ item.title }}
                        </Link>
                    </Button>
                </nav>
            </aside>

            <Separator class="my-6 md:hidden" />

            <div class="flex-1 md:max-w-2xl">
                <section class="max-w-xl space-y-12">
                    <slot />
                </section>
            </div>
        </div>
    </div>
</template>
