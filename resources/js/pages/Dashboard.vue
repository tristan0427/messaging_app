<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import AppSidebar from '@/components/AppSidebar.vue';
import { Head, usePage } from '@inertiajs/vue3';
import { ref, onMounted, onUnmounted, nextTick } from 'vue';
import axios from 'axios';

const chatmate = ref<any | null>(null);
const chatId = ref<number | null>(null);
const messages = ref<any[]>([]);
const newMessage = ref('');
const messagesContainer = ref<HTMLElement | null>(null);
const chatType = ref<'friend' | 'group' | null>(null);

// Polling interval reference
let pollingInterval: NodeJS.Timeout | null = null;

// current logged-in user
const currentUser = usePage().props.auth.user;

async function handleChatSelected(chat: any, type: 'friend' | 'group') {
    chatmate.value = chat;
    chatType.value = type;

    // Clear existing interval when switching chats
    stopPolling();

    try {
        if (type === 'friend') {
            // Handle friend chat
            const res = await axios.get(`/chat/${chat.id}`);
            chatId.value = res.data.id;
            messages.value = res.data.messages;
        } else if (type === 'group') {
            // Handle group chat
            const res = await axios.get('/group-chat/messages');
            chatId.value = 1; // Fixed group ID since you have one global group
            messages.value = res.data.messages || [];
        }

        // Scroll to bottom after loading messages
        await nextTick();
        scrollToBottom();

        // Start polling for new messages
        startPolling();
    } catch (err) {
        console.error("Error loading chat:", err);
    }
}

async function sendMessage() {
    if (!newMessage.value.trim()) return;

    try {
        let res;

        if (chatType.value === 'friend' && chatId.value) {
            // Send direct message to friend
            res = await axios.post(`/chat/${chatId.value}/send`, {
                message: newMessage.value,
            });
        } else if (chatType.value === 'group') {
            // Send message to group
            res = await axios.post('/group-chat/send', {
                message: newMessage.value,
            });
        } else {
            return; // No valid chat selected
        }

        // Push the new message
        messages.value.push(res.data.message || res.data);
        newMessage.value = '';

        // Scroll to bottom after sending message
        await nextTick();
        scrollToBottom();
    } catch (err) {
        console.error("Error sending message:", err);
    }
}

async function fetchMessages() {
    if (!chatmate.value || !chatType.value) return;

    try {
        let res;

        if (chatType.value === 'friend') {
            res = await axios.get(`/chat/${chatmate.value.id}`);
        } else if (chatType.value === 'group') {
            res = await axios.get('/group-chat/messages');
        } else {
            return;
        }

        const newMessages = chatType.value === 'friend' ? res.data.messages : res.data.messages || [];

        // Only update if there are new messages to prevent unnecessary re-renders
        if (newMessages.length !== messages.value.length) {
            const wasAtBottom = isScrolledToBottom();
            messages.value = newMessages;

            // Auto-scroll only if user was already at bottom
            if (wasAtBottom) {
                await nextTick();
                scrollToBottom();
            }
        }
    } catch (err) {
        console.error("Error fetching messages:", err);
    }
}

function startPolling() {
    if (pollingInterval) return; // Prevent multiple intervals

    pollingInterval = setInterval(() => {
        fetchMessages();
    }, 1500);
}

function stopPolling() {
    if (pollingInterval) {
        clearInterval(pollingInterval);
        pollingInterval = null;
    }
}

function scrollToBottom() {
    if (messagesContainer.value) {
        messagesContainer.value.scrollTop = messagesContainer.value.scrollHeight;
    }
}

function isScrolledToBottom() {
    if (!messagesContainer.value) return true;

    const { scrollTop, scrollHeight, clientHeight } = messagesContainer.value;
    return scrollTop + clientHeight >= scrollHeight - 10; // 10px threshold
}

// Get display name based on chat type
function getDisplayName() {
    if (!chatmate.value) return '';
    return chatType.value === 'group' ? chatmate.value.name : chatmate.value.name;
}

// Get status display
function getStatusDisplay() {
    if (!chatmate.value) return '';
    if (chatType.value === 'group') {
        return `${chatmate.value.member_count} members`;
    }
    return chatmate.value.status || 'offline';
}

// Get avatar/icon
function getAvatarDisplay() {
    if (!chatmate.value) return '';

    if (chatType.value === 'group') {
        return '👥'; // Group emoji
    }
    return chatmate.value.name.charAt(0);
}

// Get avatar background class
function getAvatarBgClass() {
    if (chatType.value === 'group') {
        return 'bg-gradient-to-br from-green-400 to-blue-600';
    }
    return 'bg-blue-500';
}

// Get status indicator class
function getStatusClass() {
    if (chatType.value === 'group') {
        return 'bg-green-500'; // Always online for group
    }
    return chatmate.value?.status === 'online' ? 'bg-green-500' : 'bg-gray-400';
}

// Format message display for group messages
function formatMessageDisplay(msg: any) {
    if (chatType.value === 'group' && msg.user_id !== currentUser.id) {
        return `${msg.user_name}: ${msg.message}`;
    }
    return msg.message;
}

// Lifecycle hooks
onMounted(() => {
    // Start polling if there's already an active chat
    if (chatId.value) {
        startPolling();
    }
});

onUnmounted(() => {
    // Clean up interval when component is destroyed
    stopPolling();
});
</script>

<template>
    <Head title="Chat" />

    <AppLayout>
        <AppSidebar @chatSelected="handleChatSelected" />
        <div class="flex h-[90vh] rounded-xl border border-sidebar-border/70 dark:border-sidebar-border">
            <!-- Chat Panel -->
            <div class="flex flex-1 flex-col">
                <!-- If chatmate selected -->
                <template v-if="chatmate">
                    <!-- Chat Header -->
                    <div class="flex items-center gap-3 border-b border-gray-300 px-4 py-3 dark:border-gray-700">
                        <div class="relative">
                            <div
                                class="h-10 w-10 rounded-full flex items-center justify-center text-white font-semibold"
                                :class="getAvatarBgClass()">
                                {{ getAvatarDisplay() }}
                            </div>
                            <span
                                class="absolute -bottom-1 -right-1 h-3 w-3 rounded-full border-2 border-white dark:border-gray-900"
                                :class="getStatusClass()" />
                        </div>
                        <div>
                            <p class="font-medium text-gray-800 dark:text-gray-100">{{ getDisplayName() }}</p>
                            <p class="text-xs text-gray-500 capitalize">{{ getStatusDisplay() }}</p>
                        </div>
                        <!-- Chat type indicator -->
                        <div class="ml-auto">
                            <span
                                v-if="chatType === 'group'"
                                class="inline-flex items-center px-2 py-1 rounded-full text-xs bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100">
                                🌍 Group Chat
                            </span>
                            <span
                                v-else
                                class="inline-flex items-center px-2 py-1 rounded-full text-xs bg-blue-100 text-blue-800 dark:bg-blue-800 dark:text-blue-100">
                                💬 Direct Message
                            </span>
                        </div>
                    </div>

                    <!-- Messages -->
                    <div
                        ref="messagesContainer"
                        class="flex-1 overflow-y-auto p-4 space-y-3">
                        <div v-for="msg in messages" :key="msg.id" :class="[
                            'max-w-xs rounded-lg px-3 py-2 text-sm',
                            msg.user_id === currentUser.id
                                ? 'ml-auto bg-blue-500 text-white'
                                : 'mr-auto bg-gray-200 dark:bg-gray-700 dark:text-white'
                        ]">
                            {{ formatMessageDisplay(msg) }}
                        </div>

                        <!-- Empty state for messages -->
                        <div v-if="messages.length === 0" class="text-center text-gray-500 py-8">
                            <div class="text-4xl mb-2">
                                {{ chatType === 'group' ? '🌍' : '💬' }}
                            </div>
                            <p class="text-sm">
                                {{ chatType === 'group' ? 'No messages in this group yet' : 'No messages yet' }}
                            </p>
                            <p class="text-xs mt-1">
                                {{ chatType === 'group' ? 'Be the first to say something!' : 'Start the conversation!' }}
                            </p>
                        </div>
                    </div>

                    <!-- Input -->
                    <div class="flex items-center gap-2 border-t border-gray-300 p-3 dark:border-gray-700">
                        <input
                            v-model="newMessage"
                            @keyup.enter="sendMessage"
                            type="text"
                            :placeholder="chatType === 'group' ? 'Message everyone...' : 'Type a message...'"
                            class="flex-1 rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none dark:border-gray-600 dark:bg-gray-800 dark:text-white" />
                        <button
                            @click="sendMessage"
                            class="rounded-lg px-4 py-2 text-white hover:opacity-90 transition-opacity"
                            :class="chatType === 'group' ? 'bg-green-500 hover:bg-green-600' : 'bg-blue-500 hover:bg-blue-600'">
                            Send
                        </button>
                    </div>
                </template>

                <!-- If no chatmate yet -->
                <div v-else class="flex-1 flex items-center justify-center text-gray-500">
                    <div class="text-center">
                        <div class="text-6xl mb-4">💬</div>
                        <p class="text-lg font-medium mb-2">Welcome to Chat</p>
                        <p>Select a friend or the group chat from the sidebar to start messaging</p>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
