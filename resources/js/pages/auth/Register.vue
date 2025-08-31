<script setup lang="ts">
import RegisteredUserController from '@/actions/App/Http/Controllers/Auth/RegisteredUserController';
import InputError from '@/components/InputError.vue';
import TextLink from '@/components/TextLink.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AuthBase from '@/layouts/AuthLayout.vue';
import { login } from '@/routes';
import { Form, Head } from '@inertiajs/vue3';
import { LoaderCircle, MessageCircle, Users } from 'lucide-vue-next';
</script>

<template>
    <!-- 🎨 CUSTOMIZABLE: Updated title and description for messaging app -->
    <AuthBase
        title="Join Our Messaging Community"
        description="Create your account and start connecting with friends"
    >
        <Head title="Create Account - Messaging App" />

        <!-- 🎨 CUSTOMIZABLE: Added messaging app branding -->
        <div class="mb-6 text-center">
            <div class="flex justify-center items-center gap-2 mb-3">
                <div class="p-2 bg-blue-100 rounded-full">
                    <MessageCircle class="h-6 w-6 text-blue-600" />
                </div>
                <div class="p-2 bg-green-100 rounded-full">
                    <Users class="h-6 w-6 text-green-600" />
                </div>
            </div>
            <p class="text-sm text-muted-foreground">Connect, chat, and stay in touch</p>
        </div>

        <Form
            v-bind="RegisteredUserController.store.form()"
            :reset-on-success="['password', 'password_confirmation']"
            v-slot="{ errors, processing }"
            class="flex flex-col gap-6"
        >
            <div class="grid gap-6">
                <!-- 🎨 CUSTOMIZABLE: Updated placeholder and styling -->
                <div class="grid gap-2">
                    <Label for="name">Full Name</Label>
                    <Input
                        id="name"
                        type="text"
                        required
                        autofocus
                        :tabindex="1"
                        autocomplete="name"
                        name="name"
                        placeholder="Enter your full name"
                        class="h-11"
                    />
                    <InputError :message="errors.name" />
                </div>

                <div class="grid gap-2">
                    <Label for="email">Email Address</Label>
                    <Input
                        id="email"
                        type="email"
                        required
                        :tabindex="2"
                        autocomplete="email"
                        name="email"
                        placeholder="your.email@example.com"
                        class="h-11"
                    />
                    <InputError :message="errors.email" />
                </div>

                <div class="grid gap-2">
                    <Label for="password">Password</Label>
                    <Input
                        id="password"
                        type="password"
                        required
                        :tabindex="3"
                        autocomplete="new-password"
                        name="password"
                        placeholder="Create a strong password"
                        class="h-11"
                    />
                    <InputError :message="errors.password" />
                </div>

                <div class="grid gap-2">
                    <Label for="password_confirmation">Confirm Password</Label>
                    <Input
                        id="password_confirmation"
                        type="password"
                        required
                        :tabindex="4"
                        autocomplete="new-password"
                        name="password_confirmation"
                        placeholder="Confirm your password"
                        class="h-11"
                    />
                    <InputError :message="errors.password_confirmation" />
                </div>

                <!-- 🎨 CUSTOMIZABLE: Enhanced button styling -->
                <Button
                    type="submit"
                    class="mt-2 w-full h-11 bg-blue-600 hover:bg-blue-700 text-white font-medium transition-colors"
                    tabindex="5"
                    :disabled="processing"
                >
                    <LoaderCircle v-if="processing" class="h-4 w-4 animate-spin mr-2" />
                    <MessageCircle v-else class="h-4 w-4 mr-2" />
                    Create Account & Start Messaging
                </Button>
            </div>

            <!-- 🎨 CUSTOMIZABLE: Updated text for messaging context -->
            <div class="text-center text-sm text-muted-foreground">
                Already have an account?
                <TextLink :href="login()" class="underline underline-offset-4 text-blue-600 hover:text-blue-800" :tabindex="6">
                    Sign in to continue chatting
                </TextLink>
            </div>
        </Form>
    </AuthBase>
</template>
