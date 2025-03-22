<script setup lang="ts">
import { Toaster } from '@/components/ui/sonner';
import AppLayout from '@/layouts/app/AppHeaderLayout.vue';
import type { BreadcrumbItemType } from '@/types';

interface Props {
    breadcrumbs?: BreadcrumbItemType[];
}

withDefaults(defineProps<Props>(), {
    breadcrumbs: () => [],
});
</script>

<template>
    <Toaster />
    <AppLayout :breadcrumbs="breadcrumbs">
        <slot />
    </AppLayout>
</template>
