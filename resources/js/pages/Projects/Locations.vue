<script setup lang="ts">
import Can from '@/components/Can.vue';
import HeadingSmall from '@/components/HeadingSmall.vue';
import InputError from '@/components/InputError.vue';
import Button from '@/components/ui/button/Button.vue';
import Dialog from '@/components/ui/dialog/Dialog.vue';
import DialogContent from '@/components/ui/dialog/DialogContent.vue';
import DialogDescription from '@/components/ui/dialog/DialogDescription.vue';
import DialogFooter from '@/components/ui/dialog/DialogFooter.vue';
import DialogHeader from '@/components/ui/dialog/DialogHeader.vue';
import DialogTitle from '@/components/ui/dialog/DialogTitle.vue';
import Input from '@/components/ui/input/Input.vue';
import Label from '@/components/ui/label/Label.vue';
import Separator from '@/components/ui/separator/Separator.vue';
import Textarea from '@/components/ui/textarea/Textarea.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import ProjectLayout from '@/layouts/project/Layout.vue';
import { Head, useForm, usePage } from '@inertiajs/vue3';
import { Pin, Plus } from 'lucide-vue-next';
import { computed, ref } from 'vue';

const page = computed<any>(() => usePage().props);

const createModal = ref(false);
const createForm = useForm({
    name: '',
    description: '',
    address: '',
});

const submitCreate = () => {
    createForm.post(route('project.locations.store', page.value.project.id), {
        preserveScroll: true,
        onSuccess: () => {
            createModal.value = false;
            createForm.reset();
        },
    });
};

const editModal = ref(false);
const editForm = useForm({
    name: '',
    description: '',
    address: '',
    id: '',
});

const handleOpenEdit = (location: any) => {
    editForm.address = location.address;
    editForm.name = location.name;
    editForm.description = location.description;
    editForm.id = location.id;
    editModal.value = true;
};

const submitEdit = (update?: boolean) => {
    if (update) {
        editForm.put(route('project.locations.update', [page.value.project.id, editForm.id]), {
            preserveScroll: true,
            onSuccess: () => {
                editModal.value = false;
                editForm.reset();
            }
        });
    } else {
        editForm.delete(route('project.locations.destroy', [page.value.project.id, editForm.id]), {
            preserveScroll: true,
            onSuccess: () => {
                editModal.value = false;
                editForm.reset();
            }
        })
    }
};
</script>

<template>
    <Head :title="page.project?.name" />
    <AppLayout>
        <ProjectLayout>
            <div class="flex flex-col space-y-6">
                <div class="flex justify-between">
                    <HeadingSmall title="Locations" description="Manage project locations and their details." />

                    <Can permission="project:update">
                        <Button variant="ghost" size="icon" @click="createModal = true">
                            <component :is="Plus" class="size-5" />
                        </Button>
                    </Can>
                </div>

                <div v-if="!(page.locations && page.locations.length)">
                    <p class="text-sm text-muted-foreground">No locations found.</p>
                </div>

                <div v-else class="space-y-4">
                    <div v-for="location in page.locations" :key="location.id" @click="handleOpenEdit(location)" class="flex items-start rounded-lg border p-4">
                        <div class="w-10 flex-shrink-0">
                            <component :is="Pin" class="mt-1 size-5 text-primary" />
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold">{{ location.name }}</h3>
                            <p class="text-sm text-muted-foreground">{{ location.address }}</p>
                            <Separator class="my-2" v-if="location.description" />
                            <p class="text-sm text-muted-foreground">{{ location.description }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <Dialog v-model:open="createModal">
                <DialogContent class="sm:max-w-[425px]">
                    <DialogHeader>
                        <DialogTitle>Create Location</DialogTitle>
                        <DialogDescription> Fill in the details below to create a new location. </DialogDescription>
                    </DialogHeader>
                    <form @submit.prevent="submitCreate" class="space-y-6">
                        <div class="mt-2 grid gap-2">
                            <Label for="name">Name</Label>
                            <Input v-model="createForm.name" id="name" class="mt-1 block w-full" placeholder="e.g. Main Office" />
                            <InputError class="mt-2" :message="createForm.errors.name" />
                        </div>

                        <div class="mt-2 grid gap-2">
                            <Label for="address">Address</Label>
                            <Input v-model="createForm.address" id="address" class="mt-1 block w-full" placeholder="e.g. 123 Main St" />
                            <InputError class="mt-2" :message="createForm.errors.address" />
                        </div>

                        <div class="mt-2 grid gap-2">
                            <Label for="description">Description</Label>
                            <Textarea v-model="createForm.description" id="description" class="mt-1 block w-full" />
                            <InputError class="mt-2" :message="createForm.errors.description" />
                        </div>

                        <DialogFooter>
                            <Button type="submit" :disabled="createForm.processing"> Create </Button>
                        </DialogFooter>
                    </form>
                </DialogContent>
            </Dialog>

            <Dialog v-model:open="editModal">
                <DialogContent class="sm:max-w-[425px]">
                    <DialogHeader>
                        <DialogTitle>Create Location</DialogTitle>
                        <DialogDescription> Fill in the details below to create a new location. </DialogDescription>
                    </DialogHeader>
                    <form @submit.prevent="() => submitEdit(true)" class="space-y-6">
                        <div class="mt-2 grid gap-2">
                            <Label for="name">Name</Label>
                            <Input v-model="editForm.name" id="name" class="mt-1 block w-full" placeholder="e.g. Main Office" />
                            <InputError class="mt-2" :message="editForm.errors.name" />
                        </div>

                        <div class="mt-2 grid gap-2">
                            <Label for="address">Address</Label>
                            <Input v-model="editForm.address" id="address" class="mt-1 block w-full" placeholder="e.g. 123 Main St" />
                            <InputError class="mt-2" :message="editForm.errors.address" />
                        </div>

                        <div class="mt-2 grid gap-2">
                            <Label for="description">Description</Label>
                            <Textarea v-model="editForm.description" id="description" class="mt-1 block w-full" />
                            <InputError class="mt-2" :message="editForm.errors.description" />
                        </div>

                        <DialogFooter>
                            <div class="flex w-full items-center justify-between">
                                <Button type="button" variant="destructive" :disabled="editForm.processing" @click="() => submitEdit(false)"> Delete </Button>
                            </div>
                            <Button type="submit" :disabled="editForm.processing"> Create </Button>
                        </DialogFooter>
                    </form>
                </DialogContent>
            </Dialog>
        </ProjectLayout>
    </AppLayout>
</template>
