<script setup lang="ts">
import HeadingSmall from '@/components/HeadingSmall.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import ProjectLayout from '@/layouts/project/Layout.vue';
import { Head, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const page = computed<any>(() => usePage().props);
</script>

<template>
    <Head :title="page.project?.name" />
    <AppLayout>
        <ProjectLayout>
            <div class="flex flex-col space-y-6">
                <HeadingSmall title="Schedule" description="Manage project members and their roles." />

                <div v-if="page.members && page.members.length">

                </div>

                <div v-else>
                    <p class="text-sm text-muted-foreground">No schedules found.</p>
                </div>
            </div>
        </ProjectLayout>
    </AppLayout>
</template>
