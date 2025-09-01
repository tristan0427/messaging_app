<script setup lang="ts">
import { ref, onMounted } from 'vue';
import axios from 'axios';
import NavUser from '@/components/NavUser.vue';
import { Sidebar, SidebarContent, SidebarFooter, SidebarHeader } from '@/components/ui/sidebar';

const friends = ref<any[]>([]);
const selectedFriendId = ref<number | null>(null);

// Define emits for parent component communication
const emit = defineEmits<{
    chatSelected: [friend: any]
}>();

const fetchFriends = () => {
    console.log('Fetching friends...');
    axios.get('/friends/list', {
        headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
        .then((response) => {
            console.log("Full API Response:", response);
            console.log("Response data:", response.data);
            console.log("Response status:", response.status);

            let friendsArray: any[] = [];

            if (Array.isArray(response.data)) {
                friendsArray = response.data;
                console.log("Data is direct array");
            } else if (response.data.data && Array.isArray(response.data.data)) {
                friendsArray = response.data.data;
                console.log("Data is in response.data.data");
            } else if (response.data.friends && Array.isArray(response.data.friends)) {
                friendsArray = response.data.friends;
                console.log("Data is in response.data.friends");
            } else if (response.data.props && response.data.props.friends && Array.isArray(response.data.props.friends)) {
                friendsArray = response.data.props.friends;
                console.log("Data is in response.data.props.friends (Inertia structure)");
            } else {
                console.log("No matching array structure found");
                console.log("Available keys in response.data:", Object.keys(response.data || {}));
            }

            console.log("Friends array:", friendsArray);
            console.log("Friends array length:", friendsArray.length);

            friends.value = friendsArray.map((friend: any) => ({
                id: friend.id,
                name: friend.name,
                email: friend.email,
                avatar: friend.avatar,
                status: friend.status,
                last_seen: friend.last_seen,
                href: friend.href || `/chat/${friend.id}`
            }));

            console.log("Mapped friends:", friends.value);
        })
        .catch((error) => {
            console.error('Error fetching friends:', error);
            console.error('Error details:', error.response);
        });
};

const selectFriend = (friend: any) => {
    selectedFriendId.value = friend.id;
    emit('chatSelected', friend);
    console.log('Selected friend:', friend);
};

onMounted(fetchFriends);

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
            <!-- Debug info - remove this once working -->
            <div class="p-3 text-xs text-gray-500 bg-gray-100 dark:bg-gray-800 rounded">
                <div>{{ friends.length }} friends loaded</div>
                <div>API Status: Check console for details</div>
            </div>

            <ul class="space-y-2 p-3">
                <li v-for="friend in friends" :key="friend.id">
                    <div
                        @click="selectFriend(friend)"
                        :class="[
                            'flex items-center gap-3 rounded-lg px-3 py-2 cursor-pointer transition-colors',
                            selectedFriendId === friend.id
                                ? 'bg-blue-100 dark:bg-blue-900 border border-blue-300 dark:border-blue-700'
                                : 'hover:bg-gray-100 dark:hover:bg-gray-800'
                        ]"
                    >
                        <!-- Avatar -->
                        <div class="relative">
                            <div
                                v-if="friend.avatar"
                                class="h-8 w-8 rounded-full overflow-hidden"
                            >
                                <img :src="friend.avatar" :alt="friend.name" class="h-full w-full object-cover" />
                            </div>
                            <div
                                v-else
                                class="h-8 w-8 rounded-full bg-gray-300 dark:bg-gray-600 flex items-center justify-center text-xs font-bold text-white"
                            >
                                {{ friend.name.charAt(0).toUpperCase() }}
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
                            <span class="text-xs text-gray-500 capitalize">
                                {{ friend.status }}
                                <span v-if="friend.last_seen"> • {{ friend.last_seen }}</span>
                            </span>
                        </div>
                    </div>
                </li>
            </ul>

            <!-- Show message if no friends -->
            <div v-if="friends.length === 0" class="p-3 text-center text-gray-500">
                <p class="text-sm">No friends found</p>
            </div>
        </SidebarContent>

        <!-- Footer -->
        <SidebarFooter class="p-4 border-t dark:border-gray-700">
            <NavUser />
        </SidebarFooter>
    </Sidebar>

    <!-- Page Content -->
    <slot />
</template>
