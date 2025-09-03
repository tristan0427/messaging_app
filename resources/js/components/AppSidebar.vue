<script setup lang="ts">
import { ref, onMounted } from 'vue';
import axios from 'axios';
import NavUser from '@/components/NavUser.vue';
import { Sidebar, SidebarContent, SidebarFooter, SidebarHeader } from '@/components/ui/sidebar';

const friends = ref<any[]>([]);
const group = ref<any>(null);
const selectedFriendId = ref<number | null>(null);
const selectedGroup = ref<boolean>(false);

// Define emits for parent component communication
const emit = defineEmits<{
    chatSelected: [chat: any, type: 'friend' | 'group']
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
            console.log("Friends API Response:", response);

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

            friends.value = friendsArray.map((friend: any) => ({
                id: friend.id,
                name: friend.name,
                email: friend.email,
                avatar: friend.avatar,
                status: friend.status,
                last_seen: friend.last_seen,
                last_message: friend.last_message,
                last_message_time: friend.last_message_time,
                href: friend.href || `/chat/${friend.id}`,
                avatarError: false // Track avatar loading errors
            }));

            console.log("Mapped friends:", friends.value);
        })
        .catch((error) => {
            console.error('Error fetching friends:', error);
        });
};

const fetchGroup = () => {
    console.log('Fetching group...');
    axios.get('/groups/list', {
        headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
        .then((response) => {
            console.log("Group API Response:", response);

            if (response.data.data && response.data.data.length > 0) {
                group.value = response.data.data[0]; // Get the first (and only) group
                console.log("Group loaded:", group.value);
            }
        })
        .catch((error) => {
            console.error('Error fetching group:', error);
        });
};

const selectFriend = (friend: any) => {
    selectedFriendId.value = friend.id;
    selectedGroup.value = false;
    emit('chatSelected', friend, 'friend');
    console.log('Selected friend:', friend);
};

const selectGroupChat = () => {
    selectedFriendId.value = null;
    selectedGroup.value = true;
    emit('chatSelected', group.value, 'group');
    console.log('Selected group chat:', group.value);
};

// Handle image loading errors
const handleImageError = (friend: any) => {
    console.error('Failed to load avatar for:', friend.name, 'URL:', friend.avatar);
    friend.avatarError = true;
};

onMounted(() => {
    fetchFriends();
    fetchGroup();
});

function statusColor(status: string) {
    switch (status) {
        case 'online': return 'bg-green-500';
        case 'offline': return 'bg-gray-400';
        case 'away': return 'bg-yellow-500';
        default: return 'bg-gray-400';
    }
}

function formatTime(timestamp: string) {
    if (!timestamp) return '';
    const date = new Date(timestamp);
    const now = new Date();
    const diff = now.getTime() - date.getTime();
    const hours = Math.floor(diff / (1000 * 60 * 60));

    if (hours < 1) return 'now';
    if (hours < 24) return `${hours}h`;
    const days = Math.floor(hours / 24);
    if (days < 7) return `${days}d`;
    return date.toLocaleDateString();
}

function truncateMessage(message: string, length: number = 25) {
    if (!message) return '';
    return message.length > length ? message.substring(0, length) + '...' : message;
}

// Check if avatar URL is valid
function hasValidAvatar(friend: any) {
    return friend.avatar && !friend.avatarError && friend.avatar.trim() !== '';
}
</script>

<template>
    <Sidebar collapsible="icon" variant="inset">
        <!-- Header -->
        <SidebarHeader class="p-4 border-b dark:border-gray-700">
            <h2 class="text-lg font-semibold">Chats</h2>
        </SidebarHeader>

        <!-- Chat List -->
        <SidebarContent class="flex-1 overflow-y-auto">
            <!-- Debug info -->
            <div class="p-3 text-xs text-gray-500 bg-gray-100 dark:bg-gray-800 rounded m-3">
                <div>{{ friends.length }} friends loaded</div>
                <div v-if="group">Group: {{ group.name }} ({{ group.member_count }} members)</div>
            </div>

            <!-- Group Chat (Always at the top) -->
            <div v-if="group" class="p-3">
                <div
                    @click="selectGroupChat"
                    :class="[
                        'flex items-center gap-3 rounded-lg px-3 py-3 cursor-pointer transition-colors border-2',
                        selectedGroup
                            ? 'bg-green-50 dark:bg-green-900 border-green-300 dark:border-green-700'
                            : 'hover:bg-gray-100 dark:hover:bg-gray-800 border-transparent'
                    ]"
                >
                    <!-- Group Avatar -->
                    <div class="relative">
                        <div class="h-12 w-12 rounded-lg bg-gradient-to-br from-green-400 to-blue-600 flex items-center justify-center text-white text-lg font-bold">
                            👥
                        </div>
                        <!-- Member count badge -->
                        <span class="absolute -bottom-1 -right-1 bg-green-600 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center border-2 border-white dark:border-gray-900">
                            {{ group.member_count }}
                        </span>
                    </div>

                    <!-- Group Info -->
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center justify-between mb-1">
                            <span class="text-sm font-medium text-green-600 dark:text-green-400">
                                 {{ group.name }}
                            </span>
                            <span v-if="group.last_message_time" class="text-xs text-gray-500">
                                {{ formatTime(group.last_message_time) }}
                            </span>
                        </div>
                        <p class="text-xs text-gray-500 dark:text-gray-400 truncate">
                            {{ group.description }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Divider -->
            <div v-if="group && friends.length > 0" class="mx-6 border-t border-gray-200 dark:border-gray-700 mb-2"></div>

            <!-- Friends List -->
            <div class="px-3">
                <h3 v-if="friends.length > 0" class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2 px-3">
                    Direct Messages
                </h3>
                <ul class="space-y-1">
                    <li v-for="friend in friends" :key="friend.id">
                        <div
                            @click="selectFriend(friend)"
                            :class="[
                                'flex items-center gap-3 rounded-lg px-3 py-3 cursor-pointer transition-colors',
                                selectedFriendId === friend.id
                                    ? 'bg-blue-100 dark:bg-blue-900 border border-blue-300 dark:border-blue-700'
                                    : 'hover:bg-gray-100 dark:hover:bg-gray-800'
                            ]"
                        >
                            <!-- Avatar -->
                            <div class="relative">
                                <!-- Image Avatar -->
                                <div
                                    v-if="hasValidAvatar(friend)"
                                    class="h-10 w-10 rounded-full overflow-hidden"
                                >
                                    <img
                                        :src="friend.avatar"
                                        :alt="friend.name"
                                        class="h-full w-full object-cover"
                                        @error="handleImageError(friend)"
                                    />
                                </div>
                                <!-- Fallback Avatar -->
                                <div
                                    v-else
                                    class="h-10 w-10 rounded-full bg-gray-300 dark:bg-gray-600 flex items-center justify-center text-sm font-bold text-white"
                                >
                                    {{ friend.name.charAt(0).toUpperCase() }}
                                </div>
                                <!-- Status dot -->
                                <span
                                    class="absolute -bottom-1 -right-1 h-3 w-3 rounded-full border-2 border-white dark:border-gray-900"
                                    :class="statusColor(friend.status)"
                                />
                            </div>

                            <!-- Friend Info -->
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center justify-between mb-1">
                                    <span class="text-sm font-medium truncate">{{ friend.name }}</span>
                                    <span v-if="friend.last_message_time" class="text-xs text-gray-500 flex-shrink-0">
                                        {{ formatTime(friend.last_message_time) }}
                                    </span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-xs text-gray-500 capitalize truncate">
                                        <span v-if="friend.last_message">
                                            {{ truncateMessage(friend.last_message) }}
                                        </span>
                                        <span v-else>
                                            {{ friend.status }}
                                            <span v-if="friend.last_seen"> • {{ friend.last_seen }}</span>
                                        </span>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </li>
                </ul>
            </div>

            <!-- Empty States -->
            <div v-if="!group && friends.length === 0" class="p-6 text-center text-gray-500">
                <div class="text-4xl mb-2">💬</div>
                <p class="text-sm">No chats available</p>
                <p class="text-xs mt-1">Add some friends to start chatting</p>
            </div>

            <div v-if="friends.length === 0 && group" class="p-3 text-center text-gray-500 text-xs">
                <p>No direct messages yet</p>
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
