<script setup lang="ts">
import HeadingSmall from '@/components/HeadingSmall.vue';
import { FullCalendar } from '@/components/ui/full-calendar';
import { CalendarEvent } from '@/components/ui/full-calendar/FullCalendar.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import ProjectLayout from '@/layouts/project/Layout.vue';
import { isDateInRange } from '@/lib/helpers';
import { Head, usePage } from '@inertiajs/vue3';
import { fromDate, isSameDay, type DateValue } from '@internationalized/date';
import { computed, onMounted, ref } from 'vue';

const page = computed<any>(() => usePage().props);
const timeZone = Intl.DateTimeFormat().resolvedOptions().timeZone;

const events = computed<CalendarEvent[]>(() => {
    if (!page.value.schedules) return [];

    console.log('schedules', page.value.schedules);

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

    eventsInRange.value = events.value.filter((event) => {
        return isDateInRange(date, event.start as DateValue, event.end as DateValue);
    });
}

onMounted(() => {
    handleDateChanged(first.value?.start);
});
</script>

<template>
    <Head :title="page.project?.name" />
    <AppLayout>
        <ProjectLayout>
            <div class="flex flex-col space-y-6">
                <HeadingSmall title="Schedule" description="Manage your project schedules." />
                <!-- @vue-ignore -->
                <FullCalendar v-model:model-value="currentSelectedDate" @update:model-value="handleDateChanged" :events="events" />
                <div class="p-3">
                    <h2 class="my-1 text-lg font-semibold">Events</h2>
                    <div v-if="eventsInRange.length === 0" class="text-sm text-muted-foreground">No events found.</div>

                    <div v-else>
                        <div v-for="event in eventsInRange" :key="event.id" class="flex items-baseline gap-3 py-4">
                            <span class="w-[100px] shrink-0 text-sm text-muted-foreground">
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
                                    {{ event.start.toDate(timeZone).toLocaleDateString('cs-CZ', { day: 'numeric', month: 'numeric' }) }}
                                    -
                                    {{ event.end.toDate(timeZone).toLocaleDateString('cs-CZ', { day: 'numeric', month: 'numeric' }) }}
                                </template>
                            </span>
                            <div>
                                <h3 class="text-lg font-semibold">{{ event.title }}</h3>
                                <p class="text-sm text-muted-foreground">{{ event.description }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </ProjectLayout>
    </AppLayout>
</template>
