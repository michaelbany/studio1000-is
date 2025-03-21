import { SharedData } from '@/types';
import { usePage } from '@inertiajs/vue3';
import { computed } from 'vue';


const permissions = computed(() => {
    return usePage<SharedData>().props.auth.permissions;
});

export const can = (permission: string | boolean) => {
    if (typeof permission === 'boolean') {
        return permission;
    } else {
        return permissions.value.includes(permission);
    }
};

export const usePermissions = () => {
    return {
        permissions,
        /**
         * Determine if user has permission to do action
         * @param permission `resource:action` or boolean
         * @returns boolean
         */
        can: can as (permission: string | boolean | undefined) => boolean,
        /**
         * Find if user has at least one of the permissions
         * @param permission array of `resource:action` or boolean
         * @returns boolean
         */
        oneOf(permission: (string | boolean)[]): boolean {
            return permission.some((p) => this.can(p));
        },
        /**
         * Formatted list of actions sorted by resource
         * @returns list of actions
         */
        list(): { [key: string]: string[] } {
            return permissions.value.reduce((acc: any, permission: string) => {
                const [resource, action] = permission.split(':');
                if (acc[resource]) {
                    acc[resource].push(action);
                } else {
                    acc[resource] = [action];
                }

                return acc;
            }, {});
        },
        /**
         * Find resources for given action
         * @param actions Action key or array of action keys
         * @returns list of resources that have the action
         */
        has(actions: string[] | string): { [key: string]: string[] } {
            actions = Array.isArray(actions) ? actions : [actions];
            return actions.reduce((acc: any, permission: string) => {
                acc[permission] = Object.keys(this.list()).filter((key) => {
                    return this.list()[key].includes(permission);
                });
                return acc;
            }, {});
        },
    };
};
