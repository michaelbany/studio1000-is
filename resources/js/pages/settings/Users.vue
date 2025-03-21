<script setup lang="ts">
import { Head, router, usePage } from '@inertiajs/vue3';

import HeadingSmall from '@/components/HeadingSmall.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import SettingsLayout from '@/layouts/settings/Layout.vue';
import { type User as UserType, type BreadcrumbItem } from '@/types';
import { computed } from 'vue';
import { User } from 'lucide-vue-next';
import { label } from '@/lib/helpers';
import Can from '@/components/Can.vue';
import Select from '@/components/ui/select/Select.vue';
import SelectTrigger from '@/components/ui/select/SelectTrigger.vue';
import SelectItem from '@/components/ui/select/SelectItem.vue';
import SelectContent from '@/components/ui/select/SelectContent.vue';
import SelectValue from '@/components/ui/select/SelectValue.vue';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Profile settings',
        href: '/settings/profile',
    },
];

const page = computed(() => usePage().props as any);
const currentUser = page.value.auth.user as UserType;

const handleUpdateUser = (value: any) => {
    if (!value || value.split('-').length !== 2) return;
    router.patch(route('users.update',  value.split('-')[1]), {
        role: value.split('-')[0],
    });
}
</script>
<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <Head title="Users settings" />

        <SettingsLayout>
            <div class="flex flex-col space-y-6">
                <HeadingSmall title="App users" description="Manage your app users" />


                <div v-if="page.users && page.users.length">
                <div
                    v-for="user in page.users"
                    :key="user.id"
                    class="flex items-center justify-between rounded-lg p-4 transition-colors hover:bg-stone-100 dark:hover:bg-stone-900"
                >
                    <div>
                        <p class="font-semibold" :class="user.id === currentUser.id ? 'text-red-500' : ''">{{ user.name }}</p>
                        <p class="text-sm text-gray-500">{{ user.email }}</p>
                    </div>

                    <div class="flex items-center gap-2">
                        <component :is="User" class="size-5" />
                        <Can :permission="page.auth.user.id === user.id ? false : 'user:edit'">
                        <Select @update:model-value="handleUpdateUser" :default-value="`${user.role}-${user.id}`" id="status" class="mt-1 block">
                            <SelectTrigger class="w-min">
                                <SelectValue placeholder="Select a role" />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem hide-indicator class="pl-2" v-for="(role, i) in page.roles" :key="i" :value="`${role}-${user.id}`"> 
                                    {{ label(role) }}
                                </SelectItem>
                            </SelectContent>
                        </Select>
                        <template #else>
                            {{ label(user.role) }}
                        </template>
                    </Can>
                    </div>

                    
                </div>
            </div>

            <div v-else>
                <p class="text-center mt-4 text-gray-500">No users</p>
            </div>
            </div>
        </SettingsLayout>
    </AppLayout>
</template>