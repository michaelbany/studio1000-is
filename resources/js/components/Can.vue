<script setup lang="ts">
import { can } from '@/composables/usePermissions';
import { computed } from 'vue';

  const props = withDefaults(defineProps<{
    permission: string | boolean;
  }>(), {
    permission: false,
  });

  const hasPermission = computed(() => {
    return can(props.permission);
  });
</script>
<template>
  <template v-if="hasPermission">
    <slot></slot>
  </template>
  <template v-else>
    <slot name="else"></slot>
  </template>
</template>
