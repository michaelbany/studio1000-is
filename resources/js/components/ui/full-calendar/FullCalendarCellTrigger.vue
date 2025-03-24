<script lang="ts" setup>
import { cn } from '@/lib/utils'
import { CalendarCellTrigger, type CalendarCellTriggerProps, injectCalendarRootContext, useForwardProps } from 'reka-ui'
import { computed, type HTMLAttributes } from 'vue'

const props = defineProps<CalendarCellTriggerProps & { class?: HTMLAttributes['class'] }>()

const delegatedProps = computed(() => {
  const { class: _, ...delegated } = props

  return delegated
})

const forwardedProps = useForwardProps(delegatedProps)
const rootContext = injectCalendarRootContext();
const dayValue = props.day.day.toLocaleString(rootContext.locale.value);
</script>

<template>
  <CalendarCellTrigger
    :class="cn(
      'h-full w-full group border border-muted hover:bg-stone-50 rounded-md p-0 font-normal absolute top-0 left-0 z-10',
      '[&[data-today]:not([data-selected])]:bg-red-500/10 [&[data-today]:not([data-selected])]:text-red-500 data-[today]:text-red-500 data-[today]:focus:text-red-500',
      // // Selected
      'data-[selected]:bg-primary data-[selected]:text-primary-foreground data-[selected]:font-semibold data-[selected]:opacity-100 data-[selected]:focus:text-primary-foreground',
      // // Disabled
      'data-[disabled]:text-muted-foreground data-[disabled]:opacity-50',
      // // Unavailable
      'data-[unavailable]:text-destructive-foreground data-[unavailable]:line-through',
      // // Outside months
      'data-[outside-view]:text-muted-foreground data-[outside-view]:border-transparent data-[outside-view]:opacity-50 [&[data-outside-view][data-selected]]:bg-accent/50 [&[data-outside-view][data-selected]]:text-muted-foreground [&[data-outside-view][data-selected]]:opacity-30',
      props.class,
    )"
    v-bind="forwardedProps"
  >
  <span class="block p-1 text-xs data-[unavailable]:text-muted-foreground data-[unavailable]:line-through">
    {{ dayValue }}
  </span>
  <div class="px-1 text-xs text-muted-foreground space-y-1">
    <slot />
  </div>
  </CalendarCellTrigger>
</template>
