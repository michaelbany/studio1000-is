<script setup lang="ts">
const props = defineProps<{
    options: string[];
}>();

const model = defineModel();
</script>
<template>
    <div class="flex items-center">
        <div
            v-for="(option, index) in props.options"
            :key="index"
            class="w-6 h-6 rounded-full mr-2 relative cursor-pointer bg-border transition-all opacity-100 hover:opacity-100"
            :class="{
                'opacity-75': model && model !== option,
            }"
            
            @click="model === option ? model = null : model = option"
        >
            <div :style="{ backgroundColor: option }" class="h-3 w-3 absolute top-1/2 -translate-y-1/2 translate-x-1/2 origin-center rounded-full transition-all" :class="{
                ' scale-150': model === option,
            }" />
        </div>
    </div>
</template>