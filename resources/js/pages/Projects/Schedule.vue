<script setup lang="ts">
import Can from '@/components/Can.vue';
import HeadingSmall from '@/components/HeadingSmall.vue';
import InputError from '@/components/InputError.vue';
import Avatar from '@/components/ui/avatar/Avatar.vue';
import AvatarFallback from '@/components/ui/avatar/AvatarFallback.vue';
import Button from '@/components/ui/button/Button.vue';
import ColorPicker from '@/components/ui/ColorPicker.vue';
import Dialog from '@/components/ui/dialog/Dialog.vue';
import DialogContent from '@/components/ui/dialog/DialogContent.vue';
import DialogDescription from '@/components/ui/dialog/DialogDescription.vue';
import DialogFooter from '@/components/ui/dialog/DialogFooter.vue';
import DialogHeader from '@/components/ui/dialog/DialogHeader.vue';
import DialogTitle from '@/components/ui/dialog/DialogTitle.vue';
import DropdownMenu from '@/components/ui/dropdown-menu/DropdownMenu.vue';
import DropdownMenuCheckboxItem from '@/components/ui/dropdown-menu/DropdownMenuCheckboxItem.vue';
import DropdownMenuContent from '@/components/ui/dropdown-menu/DropdownMenuContent.vue';
import DropdownMenuTrigger from '@/components/ui/dropdown-menu/DropdownMenuTrigger.vue';
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
import Sheet from '@/components/ui/sheet/Sheet.vue';
import SheetContent from '@/components/ui/sheet/SheetContent.vue';
import SheetDescription from '@/components/ui/sheet/SheetDescription.vue';
import SheetFooter from '@/components/ui/sheet/SheetFooter.vue';
import SheetHeader from '@/components/ui/sheet/SheetHeader.vue';
import SheetTitle from '@/components/ui/sheet/SheetTitle.vue';
import Textarea from '@/components/ui/textarea/Textarea.vue';
import { can } from '@/composables/usePermissions';
import AppLayout from '@/layouts/AppLayout.vue';
import ProjectLayout from '@/layouts/project/Layout.vue';
import { inicials, isDateInRange, label, toISOStringFromDateAndTime } from '@/lib/helpers';
import { Head, router, useForm, usePage } from '@inertiajs/vue3';
import { fromDate, isSameDay, type DateValue } from '@internationalized/date';
import { Edit, Pin, Plus } from 'lucide-vue-next';
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
        participants: schedule.participants,
    })).sort((a: any, b: any) => a.start.compare(b.start));
});

const first = computed<CalendarEvent | null>(() => {
    if (!events.value.length) return null;

    return [...events.value].sort((a, b) => a.start.compare(b.start))[0];
});

const currentSelectedDate = ref<DateValue|undefined>();

const eventsInRange = computed(() => {
    if (!currentSelectedDate.value) return [];

    return events.value.filter((event) => {
        return isDateInRange(currentSelectedDate.value ?? first.value?.start, event.start as DateValue, event.end as DateValue);
    })
});

onMounted(() => {
    currentSelectedDate.value = first.value?.start;
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

const editModal = ref(false);
const editForm = useForm({
    id: 0,
    title: '',
    description: '',
    start_date: new Date().toISOString().slice(0, 10),
    start_time: '10:00',
    end_date: new Date().toISOString().slice(0, 10),
    end_time: '12:00',
    color: '',
    location_id: 0,
    participants: [],
});

const handleOpenEvent = (event: CalendarEvent) => {
    editModal.value = true;
    editForm.id = event.id;
    editForm.title = event.title;
    editForm.description = event.description;
    editForm.start_date = event.start.toDate(timeZone).toISOString().slice(0, 10);
    editForm.start_time = event.start.toDate(timeZone).toTimeString().slice(0, 5);
    editForm.end_date = event.end.toDate(timeZone).toISOString().slice(0, 10);
    editForm.end_time = event.end.toDate(timeZone).toTimeString().slice(0, 5);
    editForm.color = event.color as string;
    editForm.location_id = event.location as number;
    editForm.participants = event.participants.map((participant: any) => participant.id);
};

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

const submitEdit = () => {
    const startISO = editForm.start_date && editForm.start_time ? toISOStringFromDateAndTime(editForm.start_date, editForm.start_time) : null;
    const endISO = editForm.end_date && editForm.end_time ? toISOStringFromDateAndTime(editForm.end_date, editForm.end_time) : null;

    router.put(
        route('project.schedules.update', [page.value.project.id, editForm.id]),
        {
            ...editForm.data(),
            start_date: startISO,
            end_date: endISO,
        },
        {
            preserveScroll: true,
            onSuccess: () => {
                editModal.value = false;
                editForm.reset();
                editForm.clearErrors();
            },
            onError: (errors) => {
                Object.keys(errors).forEach((key) => {
                    editForm.setError(key as keyof typeof editForm.data, errors[key]);
                });
            },
        },
    );
};

const submitDelete = () => {

    console.log('delete', editForm.id);
    router.delete(route('project.schedules.destroy', [page.value.project.id, editForm.id]), {
        onSuccess: () => {
            editModal.value = false;
            editForm.reset();
            editForm.clearErrors();
        },
        onError: (errors) => {
            Object.keys(errors).forEach((key) => {
                editForm.setError(key as keyof typeof editForm.data, errors[key]);
            });
        },
    })
}

const handleDoubleClick = (date: DateValue | undefined) => {
    if (!date || !can('project:update')) return;

    createModal.value = true;
    createForm.start_date = date.toDate(timeZone).toISOString().slice(0, 10);
    createForm.end_date = date.toDate(timeZone).toISOString().slice(0, 10);
};
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
                            <div class="w-full">
                                <div class="w-full space-y-2">
                                    <h3 class="flex w-full items-center gap-2 text-base font-medium">
                                        <div class="h-2 w-2 rounded-full bg-primary" :style="{ backgroundColor: event.color }"></div>
                                        {{ event.title }}
                                        <Can permission="project:update">
                                            <Button size="icon" variant="ghost" @click="handleOpenEvent(event as CalendarEvent)" class="ml-auto">
                                                <component :is="Edit" class="size-4 cursor-pointer text-muted-foreground" />
                                            </Button>
                                        </Can>
                                    </h3>

                                    <p class="flex items-center gap-1 text-sm text-muted-foreground" v-if="event.location">
                                        <component :is="Pin" class="size-4 text-muted-foreground" />
                                        {{ page.locations.find((location: any) => location.id === event.location)?.name }}
                                    </p>
                                    <Separator class="my-2" v-if="event.description" />
                                    <p class="text-sm text-muted-foreground">{{ event.description }}</p>

                                    <div v-if="event.participants.length" class="!mt-4 flex items-center gap-1">
                                        <p class="text-sm text-muted-foreground">Participants:</p>
                                        <Avatar v-for="participant in event.participants" :key="participant" class="h-8 w-8">
                                            <AvatarFallback
                                                class="rounded-lg bg-neutral-200 font-semibold text-black dark:bg-neutral-700 dark:text-white"
                                            >
                                                {{ inicials(participant.user.name) }}
                                            </AvatarFallback>
                                        </Avatar>
                                    </div>
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

            <Sheet v-model:open="editModal">
                <SheetContent class="w-full sm:max-w-[700px]">
                    <SheetHeader>
                        <SheetTitle>{{ editForm.title }}</SheetTitle>
                        <SheetDescription> Make changes to this event. Click save when you're done. </SheetDescription>
                    </SheetHeader>
                    <form @submit.prevent="submitEdit" class="space-y-6">
                        <div class="mt-2 grid gap-2">
                            <Label for="name">Title</Label>
                            <Input v-model="editForm.title" id="name" class="mt-1 block w-full" placeholder="e.g. Project Kickoff" />
                            <InputError class="mt-2" :message="editForm.errors.title" />
                        </div>

                        <div class="mt-2 grid gap-2">
                            <Label for="location">Location</Label>
                            <Select v-model="editForm.location_id" id="location" class="mt-1 block">
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
                            <InputError class="mt-2" :message="editForm.errors.location_id" />
                        </div>

                        <div class="mt-2">
                            <Label for="date">Start</Label>
                            <div class="grid grid-cols-2 gap-2">
                                <Input type="date" v-model="editForm.start_date" id="date" class="mt-1 block w-full" />
                                <Input type="time" v-model="editForm.start_time" id="date" class="mt-1 block w-full" />
                            </div>
                            <InputError class="mt-2" :message="editForm.errors.start_date" />
                        </div>
                        <div class="mt-2">
                            <Label for="date">End</Label>
                            <div class="grid grid-cols-2 gap-2">
                                <Input type="date" v-model="editForm.end_date" id="date" class="mt-1 block w-full" />
                                <Input type="time" v-model="editForm.end_time" id="date" class="mt-1 block w-full" />
                            </div>
                            <InputError class="mt-2" :message="editForm.errors.end_date" />
                        </div>

                        <div class="mt-2 grid gap-2">
                            <Label for="description">Description</Label>
                            <Textarea v-model="editForm.description" id="description" class="mt-1 block w-full" />
                            <InputError class="mt-2" :message="editForm.errors.description" />
                        </div>

                        <div class="mt-2 grid gap-2">
                            <Label for="participants">Participants</Label>

                            <DropdownMenu>
                                <DropdownMenuTrigger as-child>
                                    <Button variant="outline" class="flex justify-start gap-1 p-3">
                                        <div
                                            v-if="editForm.participants.length"
                                            v-for="p in editForm.participants"
                                            :key="p"
                                            class="rounded-md bg-primary/10 px-2 py-1 text-primary"
                                        >
                                            {{ page.members.find((member: any) => member.membership.id === p)?.name }}
                                        </div>
                                        <div v-else>Select participants</div>
                                    </Button>
                                </DropdownMenuTrigger>
                                <DropdownMenuContent class="w-56">
                                    <DropdownMenuCheckboxItem
                                        v-for="participant in page.members"
                                        :key="participant.membership.id"
                                        @select="(e) => e.preventDefault()"
                                        :checked="editForm.participants.includes(participant.membership.id as never)"
                                        @update:checked="
                                            editForm.participants.includes(participant.membership.id as never)
                                                ? editForm.participants.splice(editForm.participants.indexOf(participant.membership.id as never), 1)
                                                : editForm.participants.push(participant.membership.id as never)
                                        "
                                    >
                                        {{ participant.name }}
                                    </DropdownMenuCheckboxItem>
                                </DropdownMenuContent>
                            </DropdownMenu>
                            <InputError class="mt-2" :message="editForm.errors.participants" />
                        </div>

                        <SheetFooter>
                            <div class="flex w-full items-center justify-between gap-2">
                                <ColorPicker v-model="editForm.color" id="color" :options="page.enum.schedule_color" />
                                <div class="space-x-2">
                                    <Button variant="destructive" type="button" @click="submitDelete" :disabled="editForm.processing"> Delete </Button>
                                    <Button type="submit" :disabled="editForm.processing"> Save </Button>
                                </div>
                            </div>
                        </SheetFooter>
                    </form>
                </SheetContent>
            </Sheet>
        </ProjectLayout>
    </AppLayout>
</template>
