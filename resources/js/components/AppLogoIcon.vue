<script setup lang="ts">
import type { HTMLAttributes } from 'vue';

defineOptions({
    inheritAttrs: false,
});

interface Props {
    className?: HTMLAttributes['class'];
}

defineProps<Props>();
</script>

<template>
    <!-- fill="#e4101a" -->
    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 160.01 160.01" :class="className" v-bind="$attrs" class="size-7">
        <path
            fill="currentColor"
            fill-rule="evenodd"
            clip-rule="evenodd"
            d="M104.53,47.52v2.67l-15.16,1.49v-3.86c0-5.94-2.82-9.06-8.03-9.06s-8.02,3.12-8.02,9.06v1.34c0,6.09,2.67,11.29,13.23,20.21,14.42,12.19,19.76,20.8,19.76,33.29v5.2c0,14.86-8.77,23.33-24.37,23.33s-24.37-8.47-24.37-23.33v-6.98l15.16-1.49v7.43c0,6.54,2.97,9.81,8.92,9.81s8.92-3.27,8.92-9.81v-3.42c0-6.54-3.12-11.74-13.67-20.66-14.41-12.19-19.32-20.8-19.32-32.99v-2.23c0-14.86,8.47-23.33,23.48-23.33s23.48,8.47,23.48,23.33Z"
        />
    </svg>
</template>
