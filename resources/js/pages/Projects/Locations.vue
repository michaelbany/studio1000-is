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
                <HeadingSmall title="Locations" description="Manage project locations and their details." />

                <div v-if="page.locations && page.locations.length">

                </div>

                <div v-else>
                    <p class="text-sm text-muted-foreground">No locations found.</p>
                </div>
            </div>
        </ProjectLayout>
    </AppLayout>
</template>
