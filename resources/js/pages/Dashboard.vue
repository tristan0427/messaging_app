<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head } from '@inertiajs/vue3';
import { ref } from 'vue';


// Fake chatmate info (replace with real data later)
const chatmate = ref({
    id: 1,
    name: 'Alice Johnson',
    status: 'online',
});

// Fake chat messages (replace with real data later)
const messages = ref([
    { id: 1, text: 'Hello! How are you?', sender: 'other' },
    { id: 2, text: 'I’m good, thanks! How about you?', sender: 'me' },
    { id: 3, text: 'Doing great! Working on a project.', sender: 'other' },
]);

const newMessage = ref('');

function sendMessage() {
    if (!newMessage.value.trim()) return;
    messages.value.push({
        id: Date.now(),
        text: newMessage.value,
        sender: 'me',
    });
    newMessage.value = '';
}
</script>

<template>
    <Head title="Chat" />

    <AppLayout>
        <div class="flex h-[90vh] flex-col rounded-xl border border-sidebar-border/70 dark:border-sidebar-border">

            <!-- Chat Header -->
            <div class="flex items-center gap-3 border-b border-gray-300 px-4 py-3 dark:border-gray-700">
                <!-- Avatar -->
                <div class="relative">
                    <div class="h-10 w-10 rounded-full bg-blue-500 flex items-center justify-center text-white font-semibold">
                        {{ chatmate.name.charAt(0) }}
                    </div>
                    <!-- Status dot -->
                    <span
                        class="absolute -bottom-1 -right-1 h-3 w-3 rounded-full border-2 border-white dark:border-gray-900"
                        :class="chatmate.status === 'online' ? 'bg-green-500' : 'bg-gray-400'"
                    />
                </div>

                <!-- Name + status -->
                <div>
                    <p class="font-medium text-gray-800 dark:text-gray-100">{{ chatmate.name }}</p>
                    <p class="text-xs text-gray-500 capitalize">{{ chatmate.status }}</p>
                </div>
            </div>

            <!-- Chat Messages -->
            <div class="flex-1 overflow-y-auto p-4 space-y-3">
                <div
                    v-for="msg in messages"
                    :key="msg.id"
                    :class="[
                        'max-w-xs rounded-lg px-3 py-2 text-sm',
                        msg.sender === 'me'
                            ? 'ml-auto bg-blue-500 text-white'
                            : 'mr-auto bg-gray-200 dark:bg-gray-700 dark:text-white'
                    ]"
                >
                    {{ msg.text }}
                </div>
            </div>

            <!-- Input Box -->
            <div class="flex items-center gap-2 border-t border-gray-300 p-3 dark:border-gray-700">
                <input
                    v-model="newMessage"
                    @keyup.enter="sendMessage"
                    type="text"
                    placeholder="Type a message..."
                    class="flex-1 rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none dark:border-gray-600 dark:bg-gray-800 dark:text-white"
                />
                <button
                    @click="sendMessage"
                    class="rounded-lg bg-blue-500 px-4 py-2 text-white hover:bg-blue-600"
                >
                    Send
                </button>
            </div>
        </div>
    </AppLayout>
</template>
