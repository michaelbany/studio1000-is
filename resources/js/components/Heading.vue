<script setup lang="ts">
interface Props {
    title: string;
    description?: string;
}

defineProps<Props>();
</script>

<template>
    <div class="mb-8 space-y-0.5">
        <h2 class="text-xl font-semibold tracking-tight">{{ title }}</h2>
        <p v-if="description || $slots" class="max-w-[600px] text-sm text-muted-foreground">
            <slot>{{ description }}</slot>
        </p>
    </div>
</template>
