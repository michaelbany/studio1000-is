<script setup lang="ts">
import Can from '@/components/Can.vue';
import HeadingSmall from '@/components/HeadingSmall.vue';
import InputError from '@/components/InputError.vue';
import Button from '@/components/ui/button/Button.vue';
import ColorPicker from '@/components/ui/ColorPicker.vue';
import Dialog from '@/components/ui/dialog/Dialog.vue';
import DialogContent from '@/components/ui/dialog/DialogContent.vue';
import DialogDescription from '@/components/ui/dialog/DialogDescription.vue';
import DialogFooter from '@/components/ui/dialog/DialogFooter.vue';
import DialogHeader from '@/components/ui/dialog/DialogHeader.vue';
import DialogTitle from '@/components/ui/dialog/DialogTitle.vue';
import { FullCalendar } from '@/components/ui/full-calendar';
import { CalendarEvent } from '@/components/ui/full-calendar/FullCalendar.vue';
import Input from '@/components/ui/input/Input.vue';
import Label from '@/components/ui/label/Label.vue';
import Select from '@/components/ui/select/Select.vue';
import SelectContent from '@/components/ui/select/SelectContent.vue';
import SelectItem from '@/components/ui/select/SelectItem.vue';
import SelectTrigger from '@/components/ui/select/SelectTrigger.vue';
import SelectValue from '@/components/ui/select/SelectValue.vue';
import Separator from '@/components/ui/separator/Separator.vue';
import Textarea from '@/components/ui/textarea/Textarea.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import ProjectLayout from '@/layouts/project/Layout.vue';
import { isDateInRange, label, toISOStringFromDateAndTime } from '@/lib/helpers';
import { Head, router, useForm, usePage } from '@inertiajs/vue3';
import { fromDate, isSameDay, type DateValue } from '@internationalized/date';
import { Pin, Plus } from 'lucide-vue-next';
import { computed, onMounted, ref } from 'vue';

const page = computed<any>(() => usePage().props);
const timeZone = Intl.DateTimeFormat().resolvedOptions().timeZone;

const events = computed<CalendarEvent[]>(() => {
    if (!page.value.schedules) return [];

    return page.value.schedules.map((schedule: any) => ({
        id: schedule.id,
        title: schedule.title,
        description: schedule.description,
        location: schedule.location_id,
        start: fromDate(new Date(schedule.start_date), timeZone),
        end: fromDate(new Date(schedule.end_date), timeZone),
        color: schedule.color,
    }));
});

const first = computed<CalendarEvent | null>(() => {
    if (!events.value.length) return null;

    return [...events.value].sort((a, b) => a.start.compare(b.start))[0];
});

const currentSelectedDate = ref<DateValue | undefined>();
const eventsInRange = ref<CalendarEvent[]>([]);

function handleDateChanged(date: DateValue | undefined) {
    if (!date) {
        eventsInRange.value = [];
        return;
    }

    currentSelectedDate.value = date;
    eventsInRange.value = events.value.filter((event) => {
        return isDateInRange(date, event.start as DateValue, event.end as DateValue);
    });
}

onMounted(() => {
    handleDateChanged(first.value?.start);
});

const createModal = ref(false);
const createForm = useForm({
    title: '',
    description: '',
    start_date: new Date().toISOString().slice(0, 10),
    start_time: '10:00',
    end_date: new Date().toISOString().slice(0, 10),
    end_time: '12:00',
    color: null,
    location_id: null,
});

const submitCreate = () => {
    const startISO = createForm.start_date && createForm.start_time ? toISOStringFromDateAndTime(createForm.start_date, createForm.start_time) : null;
    const endISO = createForm.end_date && createForm.end_time ? toISOStringFromDateAndTime(createForm.end_date, createForm.end_time) : null;

    router.post(
        route('project.schedules.store', page.value.project.id),
        {
            ...createForm.data(),
            start_date: startISO,
            end_date: endISO,
        },
        {
            preserveScroll: true,
            onSuccess: () => {
                createModal.value = false;
                createForm.reset();
                createForm.clearErrors();
            },
            onError: (errors) => {
                Object.keys(errors).forEach((key) => {
                    createForm.setError(key as keyof typeof createForm.data, errors[key]);
                });
            },
        },
    );
};

const handleDoubleClick = (date: DateValue | undefined) => {
    if (!date) return;

    createModal.value = true;
    createForm.start_date = date.toDate(timeZone).toISOString().slice(0, 10);
    createForm.end_date = date.toDate(timeZone).toISOString().slice(0, 10);
}
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
                <FullCalendar
                    :weekStartsOn="1"
                    weekdayFormat="short"
                    @update:model-value="handleDateChanged"
                    :events="events"
                    v-model="currentSelectedDate"
                    prevent-deselect
                    @double-click="handleDoubleClick"
                />

                <Separator />
                <div class="p-3">
                    <HeadingSmall title="Events" />
                    <div v-if="eventsInRange.length === 0" class="py-3 text-sm text-muted-foreground">No events found.</div>

                    <div v-else>
                        <div v-for="event in eventsInRange" :key="event.id" class="flex items-baseline gap-3 py-4">
                            <span class="w-[90px] shrink-0 text-xs text-muted-foreground">
                                <!-- same day -->
                                <template v-if="isSameDay(event.start as DateValue, event.end as DateValue)">
                                    {{ event.start.toDate(timeZone).toTimeString().slice(0, 5) }} -
                                    {{ event.end.toDate(timeZone).toTimeString().slice(0, 5) }}
                                </template>
                                <!-- Start same day: -->
                                <template v-else-if="isSameDay(event.start as DateValue, currentSelectedDate as DateValue)">
                                    Starts at {{ event.start.toDate(timeZone).toTimeString().slice(0, 5) }}
                                </template>
                                <!-- end this day -->
                                <template v-else-if="isSameDay(event.end as DateValue, currentSelectedDate as DateValue)">
                                    Ends at {{ event.end.toDate(timeZone).toTimeString().slice(0, 5) }}
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
                                    <p class="text-sm text-muted-foreground flex gap-1 items-center" v-if="event.location">
                                        <component :is="Pin" class="size-4 text-muted-foreground"  />
                                        {{ page.locations.find((location: any) => location.id === event.location)?.name }}
                                    </p>
                                    <Separator class="my-2" v-if="event.description" />
                                    <p class="text-sm text-muted-foreground">{{ event.description }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <Dialog v-model:open="createModal">
                <DialogContent class="sm:max-w-[425px]">
                    <DialogHeader>
                        <DialogTitle>Create a new Event</DialogTitle>
                        <DialogDescription> Fill in the details below to create a new event. </DialogDescription>
                    </DialogHeader>
                    <form @submit.prevent="submitCreate" class="space-y-6">
                        <div class="mt-2 grid gap-2">
                            <Label for="name">Title</Label>
                            <Input v-model="createForm.title" id="name" class="mt-1 block w-full" placeholder="e.g. Project Kickoff" />
                            <InputError class="mt-2" :message="createForm.errors.title" />
                        </div>

                        <div class="mt-2 grid gap-2">
                            <Label for="location">Location</Label>
                            <Select v-model="createForm.location_id" id="location" class="mt-1 block">
                                <SelectTrigger class="">
                                    <SelectValue placeholder="Select Location" />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectItem v-if="page.locations.length" v-for="(location, i) in page.locations" :key="i" :value="location.id">
                                        {{ label(location.name) }}
                                    </SelectItem>
                                    <SelectItem v-else value="0" disabled>No locations found</SelectItem>
                                </SelectContent>
                            </Select>
                            <InputError class="mt-2" :message="createForm.errors.location_id" />
                        </div>

                        <div class="mt-2">
                            <Label for="date">Start</Label>
                            <div class="grid grid-cols-2 gap-2">
                                <Input type="date" v-model="createForm.start_date" id="date" class="mt-1 block w-full" />
                                <Input type="time" v-model="createForm.start_time" id="date" class="mt-1 block w-full" />
                            </div>
                            <InputError class="mt-2" :message="createForm.errors.start_date" />
                        </div>
                        <div class="mt-2">
                            <Label for="date">End</Label>
                            <div class="grid grid-cols-2 gap-2">
                                <Input type="date" v-model="createForm.end_date" id="date" class="mt-1 block w-full" />
                                <Input type="time" v-model="createForm.end_time" id="date" class="mt-1 block w-full" />
                            </div>
                            <InputError class="mt-2" :message="createForm.errors.end_date" />
                        </div>

                        <div class="mt-2 grid gap-2">
                            <Label for="description">Description</Label>
                            <Textarea v-model="createForm.description" id="description" class="mt-1 block w-full" />
                            <InputError class="mt-2" :message="createForm.errors.description" />
                        </div>

                        <DialogFooter>
                            <div class="flex w-full items-center justify-between gap-2">
                                <ColorPicker v-model="createForm.color" id="color" :options="page.enum.schedule_color" />
                                <Button type="submit" :disabled="createForm.processing"> Create </Button>
                            </div>
                        </DialogFooter>
                    </form>
                </DialogContent>
            </Dialog>
        </ProjectLayout>
    </AppLayout>
</template>
