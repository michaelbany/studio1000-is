<script setup lang="ts">
import { Toaster } from '@/components/ui/sonner';
import AppLayout from '@/layouts/app/AppHeaderLayout.vue';
import type { BreadcrumbItemType } from '@/types';
import { usePage } from '@inertiajs/vue3';
import { computed, watch } from 'vue';
import { toast } from 'vue-sonner';

interface Props {
    breadcrumbs?: BreadcrumbItemType[];
}

const props = withDefaults(defineProps<Props>(), {
    breadcrumbs: () => [],
});

type Flash = {
    success?: string;
    error?: string;
    warning?: string;
    info?: string;
};

const flash = computed<Flash>(() => usePage().props.flash as Flash);

watch(flash, (flash) => {
    if (flash.success) {
        toast.success(flash.success);
    }
    if (flash.error) {
        toast.error(flash.error);
    }
    if (flash.warning) {
        toast.warning(flash.warning);
    }
    if (flash.info) {
        toast.info(flash.info);
    }
});
</script>

<template>
    <Toaster />
    <AppLayout :breadcrumbs="breadcrumbs">
        <slot />
    </AppLayout>
</template>
