<script setup lang="ts">
import { ellipsis } from '@/lib/helpers';

interface Props {
    title: string;
    description?: string;
}

defineProps<Props>();
</script>

<template>
    <div class="mb-8 space-y-0.5">
        <h2 class="text-xl font-semibold tracking-tight">{{ ellipsis(title, {length: 30}) }}</h2>
        <p v-if="description || $slots" class="max-w-[600px] text-sm text-muted-foreground">
            <slot>{{ description }}</slot>
        </p>
    </div>
</template>
