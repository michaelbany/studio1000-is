<script setup lang="ts">
import Can from '@/components/Can.vue';
import HeadingSmall from '@/components/HeadingSmall.vue';
import Button from '@/components/ui/button/Button.vue';
import { FullCalendar } from '@/components/ui/full-calendar';
import { CalendarEvent } from '@/components/ui/full-calendar/FullCalendar.vue';
import Separator from '@/components/ui/separator/Separator.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import ProjectLayout from '@/layouts/project/Layout.vue';
import { isDateInRange } from '@/lib/helpers';
import { Head, usePage } from '@inertiajs/vue3';
import { fromDate, isSameDay, type DateValue } from '@internationalized/date';
import { Plus } from 'lucide-vue-next';
import { computed, onMounted, ref } from 'vue';

const page = computed<any>(() => usePage().props);
const timeZone = Intl.DateTimeFormat().resolvedOptions().timeZone;

const events = computed<CalendarEvent[]>(() => {
    if (!page.value.schedules) return [];

    return page.value.schedules.map((schedule: any) => ({
        id: schedule.id,
        title: schedule.title,
        description: schedule.description,
        start: fromDate(new Date(schedule.start_date), timeZone),
        end: fromDate(new Date(schedule.end_date), timeZone),
        color: schedule.color,
    }));
});

const first = computed<CalendarEvent | null>(() => {
    if (!events.value.length) return null;

    return [...events.value].sort((a, b) => a.start.compare(b.start))[0];
});

const currentSelectedDate = ref<DateValue | undefined>(first.value?.start ?? undefined);

const eventsInRange = ref<CalendarEvent[]>([]);

function handleDateChanged(date: DateValue | undefined) {
    if (!date) return;

    currentSelectedDate.value = date;
    eventsInRange.value = events.value.filter((event) => {
        return isDateInRange(date, event.start as DateValue, event.end as DateValue);
    });
}

const createModal = ref(false);

onMounted(() => {
    handleDateChanged(first.value?.start);
});
</script>

<template>
    <Head :title="page.project?.name" />
    <AppLayout>
        <ProjectLayout>
            <div class="flex flex-col space-y-6">
                <div class="flex justify-between">
                    <HeadingSmall title="Schedule" description="Manage your project schedules." />
                    <Can permission="project:update">
                        <Button variant="ghost" size="icon" @click="createModal = true">
                            <component :is="Plus" class="size-5" />
                        </Button>
                    </Can>
                </div>
                <!-- @vue-ignore -->
                <FullCalendar :weekStartsOn="1" weekdayFormat="short" :default-value="first?.start" @update:model-value="handleDateChanged" :events="events" />
                <Separator />
                <div class="p-3">
                    <HeadingSmall title="Events" />
                    <div v-if="eventsInRange.length === 0" class="text-sm text-muted-foreground">No events found.</div>

                    <div v-else>
                        <div v-for="event in eventsInRange" :key="event.id" class="flex items-baseline gap-3 py-4">
                            <span class="w-[90px] shrink-0 text-xs text-muted-foreground">
                                <!-- same day -->
                                <template v-if="isSameDay(event.start as DateValue, event.end as DateValue)">
                                    {{ event.start.toDate(timeZone).getHours() }}:{{ event.start.toDate(timeZone).getHours() }} -
                                    {{ event.end.toDate(timeZone).getHours() }}:{{ event.end.toDate(timeZone).getMinutes() }}
                                </template>
                                <!-- Start same day: -->
                                <template v-else-if="isSameDay(event.start as DateValue, currentSelectedDate as DateValue)">
                                    Starts at {{ event.start.toDate(timeZone).getHours() }}:{{ event.start.toDate(timeZone).getHours() }}
                                </template>
                                <!-- end this day -->
                                <template v-else-if="isSameDay(event.end as DateValue, currentSelectedDate as DateValue)">
                                    Ends at {{ event.end.toDate(timeZone).getHours() }}:{{ event.end.toDate(timeZone).getMinutes() }}
                                </template>
                                <!-- start and end different day -->
                                <template v-else>
                                    Ongoing<br />({{ event.start.toDate(timeZone).toLocaleDateString('cs-CZ', { day: 'numeric', month: 'numeric' }) }}
                                    -
                                    {{ event.end.toDate(timeZone).toLocaleDateString('cs-CZ', { day: 'numeric', month: 'numeric' }) }})
                                </template>
                            </span>
                            <div>
                                <div class="space-y-2">
                                    <h3 class="flex items-center gap-2 text-base font-medium">
                                        <div class="h-2 w-2 rounded-full bg-primary" :style="{ backgroundColor: event.color }"></div>
                                        {{ event.title }}
                                    </h3>
                                    <p class="text-sm text-muted-foreground">{{ event.description }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </ProjectLayout>
    </AppLayout>
</template>
