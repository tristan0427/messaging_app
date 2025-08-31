<script setup lang="ts">
import NavUser from '@/components/NavUser.vue';
import { Sidebar, SidebarContent, SidebarFooter, SidebarHeader } from '@/components/ui/sidebar';
import { Link } from '@inertiajs/vue3'; // ✅ Import Link
import { ref } from 'vue';

// Fake friends list (replace with real data later from DB/API)
const friends = ref([
    { id: 1, name: 'Alice', status: 'online', href: '/dashboard' },
    { id: 2, name: 'Bob', status: 'offline', href: '/dashboard' },
    { id: 3, name: 'Charlie', status: 'online', href: '/dashboard' },
    { id: 4, name: 'Diana', status: 'away', href: '/dashboard' },
]);

function statusColor(status: string) {
    switch (status) {
        case 'online': return 'bg-green-500';
        case 'offline': return 'bg-gray-400';
        case 'away': return 'bg-yellow-500';
        default: return 'bg-gray-400';
    }
}
</script>

<template>
    <Sidebar collapsible="icon" variant="inset">
        <!-- Header -->
        <SidebarHeader class="p-4 border-b dark:border-gray-700">
            <h2 class="text-lg font-semibold">Chats</h2>
        </SidebarHeader>

        <!-- Friends List -->
        <SidebarContent class="flex-1 overflow-y-auto">
            <ul class="space-y-2 p-3">
                <li v-for="friend in friends" :key="friend.id">
                    <!-- ✅ Use Link so clicking navigates -->
                    <Link
                        :href="friend.href"
                        class="flex items-center gap-3 rounded-lg px-3 py-2 hover:bg-gray-100 dark:hover:bg-gray-800 cursor-pointer"
                    >
                        <!-- Avatar placeholder -->
                        <div class="relative">
                            <div class="h-8 w-8 rounded-full bg-gray-300 dark:bg-gray-600 flex items-center justify-center text-xs font-bold text-white">
                                {{ friend.name.charAt(0) }}
                            </div>
                            <!-- Status dot -->
                            <span
                                class="absolute -bottom-1 -right-1 h-3 w-3 rounded-full border-2 border-white dark:border-gray-900"
                                :class="statusColor(friend.status)"
                            />
                        </div>

                        <!-- Name + status -->
                        <div class="flex flex-col">
                            <span class="text-sm font-medium">{{ friend.name }}</span>
                            <span class="text-xs text-gray-500 capitalize">{{ friend.status }}</span>
                        </div>
                    </Link>
                </li>
            </ul>
        </SidebarContent>

        <!-- Footer -->
        <SidebarFooter class="p-4 border-t dark:border-gray-700">
            <NavUser />
        </SidebarFooter>
    </Sidebar>

    <!-- Page Content -->
    <slot />
</template>
