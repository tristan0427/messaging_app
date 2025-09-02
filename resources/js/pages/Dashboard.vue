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

// Polling interval reference
let pollingInterval: NodeJS.Timeout | null = null;

// current logged-in user
const currentUser = usePage().props.auth.user;

async function handleChatSelected(friend: any) {
    chatmate.value = friend;

    // Clear existing interval when switching chats
    stopPolling();

    try {
        // Get or create chat with this friend
        const res = await axios.get(`/chat/${friend.id}`);
        chatId.value = res.data.id;
        messages.value = res.data.messages;

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
    if (!newMessage.value.trim() || !chatId.value) return;

    try {
        const res = await axios.post(`/chat/${chatId.value}/send`, {
            message: newMessage.value,
        });

        // push the new message
        messages.value.push(res.data);
        newMessage.value = '';

        // Scroll to bottom after sending message
        await nextTick();
        scrollToBottom();
    } catch (err) {
        console.error("Error sending message:", err);
    }
}

async function fetchMessages() {
    if (!chatId.value || !chatmate.value) return;

    try {
        const res = await axios.get(`/chat/${chatmate.value.id}`);
        const newMessages = res.data.messages;

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
                                class="h-10 w-10 rounded-full bg-blue-500 flex items-center justify-center text-white font-semibold">
                                {{ chatmate.name.charAt(0) }}
                            </div>
                            <span
                                class="absolute -bottom-1 -right-1 h-3 w-3 rounded-full border-2 border-white dark:border-gray-900"
                                :class="chatmate.status === 'online' ? 'bg-green-500' : 'bg-gray-400'" />
                        </div>
                        <div>
                            <p class="font-medium text-gray-800 dark:text-gray-100">{{ chatmate.name }}</p>
                            <p class="text-xs text-gray-500 capitalize">{{ chatmate.status }}</p>
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
                            {{ msg.message }}
                        </div>
                    </div>

                    <!-- Input -->
                    <div class="flex items-center gap-2 border-t border-gray-300 p-3 dark:border-gray-700">
                        <input v-model="newMessage" @keyup.enter="sendMessage" type="text"
                               placeholder="Type a message..."
                               class="flex-1 rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none dark:border-gray-600 dark:bg-gray-800 dark:text-white" />
                        <button @click="sendMessage"
                                class="rounded-lg bg-blue-500 px-4 py-2 text-white hover:bg-blue-600">
                            Send
                        </button>
                    </div>
                </template>

                <!-- If no chatmate yet -->
                <div v-else class="flex-1 flex items-center justify-center text-gray-500">
                    <p>Select a friend from the sidebar to start chatting</p>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
