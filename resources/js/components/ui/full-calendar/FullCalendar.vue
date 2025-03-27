<script lang="ts" setup>
import { cn } from '@/lib/utils'
import { CalendarRoot, type CalendarRootEmits, type CalendarRootProps, DateValue, useForwardPropsEmits } from 'reka-ui'
import { computed, type HTMLAttributes } from 'vue'
import { FullCalendarCell, FullCalendarCellTrigger, FullCalendarGrid, FullCalendarGridBody, FullCalendarGridHead, FullCalendarGridRow, FullCalendarHeadCell, FullCalendarHeader, FullCalendarHeading, FullCalendarNextButton, FullCalendarPrevButton } from '.'
import { toCalendarDateTime } from '@internationalized/date';
import { isDateInRange, onDoubleClick } from '@/lib/helpers';

export type CalendarEvent = {
    id: number;
    title: string;
    description: string;
    location?: number;
    start: DateValue;
    end: DateValue;
    color?: string;
    participants?: any;
}

type Emits = CalendarRootEmits & {
  'doubleClick': [date: DateValue | undefined];
};

const props = defineProps<CalendarRootProps & { class?: HTMLAttributes['class'], events: CalendarEvent[] }>()

const emits = defineEmits<Emits>()

const delegatedProps = computed(() => {
  const { class: _, ...delegated } = props

  return delegated
})

const forwarded = useForwardPropsEmits(delegatedProps, emits)
</script>

<template>
  <CalendarRoot
    v-slot="{ grid, weekDays }"
    :class="cn('p-3', props.class)"
    v-bind="forwarded"
  >
    <FullCalendarHeader>
      <FullCalendarPrevButton />
      <FullCalendarHeading />
      <FullCalendarNextButton />
    </FullCalendarHeader>

    <div class="flex flex-col gap-y-4 mt-4 sm:flex-row sm:gap-x-4 sm:gap-y-0">
      <FullCalendarGrid v-for="month in grid" :key="month.value.toString()">
        <FullCalendarGridHead>
          <FullCalendarGridRow>
            <FullCalendarHeadCell
              v-for="day in weekDays" :key="day"
            >
              {{ day }}
            </FullCalendarHeadCell>
          </FullCalendarGridRow>
        </FullCalendarGridHead>
        <FullCalendarGridBody>
          <FullCalendarGridRow v-for="(weekDates, index) in month.rows" :key="`weekDate-${index}`" class="mt-1 w-full">
            <FullCalendarCell
              v-for="weekDate in weekDates"
              :key="weekDate.toString()"
              :date="weekDate"
            >
              <FullCalendarCellTrigger
                :day="weekDate"
                :month="month.value"
                @click="() => onDoubleClick(() => $emit('doubleClick', weekDate))"
              >

              <template v-for="event in props.events
                .filter(e => isDateInRange(weekDate, e.start, e.end))
                .slice(0, 3)"
                :key="event.id">
                <div
                  :style="{ backgroundColor: event.color || '',}"
                  class="bg-primary opacity-65 group-data-[selected]:bg-primary-foreground text-white p-1 rounded-md" />
              </template>
            </FullCalendarCellTrigger>
            </FullCalendarCell>
          </FullCalendarGridRow>
        </FullCalendarGridBody>
      </FullCalendarGrid>
    </div>
  </CalendarRoot>
</template>
