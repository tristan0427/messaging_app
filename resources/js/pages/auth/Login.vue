<script setup lang="ts">
import AuthenticatedSessionController from '@/actions/App/Http/Controllers/Auth/AuthenticatedSessionController';
import InputError from '@/components/InputError.vue';
import TextLink from '@/components/TextLink.vue';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AuthBase from '@/layouts/AuthLayout.vue';
import { register } from '@/routes';
import { request } from '@/routes/password';
import { Form, Head } from '@inertiajs/vue3';
import { LoaderCircle, MessageCircle, Users, Zap } from 'lucide-vue-next';

defineProps<{
    status?: string;
    canResetPassword: boolean;
}>();
</script>

<template>
    <!-- 🎨 CUSTOMIZABLE: Updated title for messaging app -->
    <AuthBase
        title="Welcome Back!"
        description="Sign in to continue your conversations"
    >
        <Head title="Sign In - Messaging App" />

        <!-- 🎨 CUSTOMIZABLE: Messaging app branding -->
        <div class="mb-6 text-center">
            <div class="flex justify-center items-center gap-2 mb-3">
                <div class="p-2 bg-gradient-to-r from-blue-100 to-purple-100 rounded-full">
                    <MessageCircle class="h-6 w-6 text-blue-600" />
                </div>
                <div class="p-1 bg-gradient-to-r from-green-100 to-blue-100 rounded-full">
                    <Zap class="h-4 w-4 text-green-600" />
                </div>
                <div class="p-2 bg-gradient-to-r from-purple-100 to-pink-100 rounded-full">
                    <Users class="h-6 w-6 text-purple-600" />
                </div>
            </div>
            <p class="text-sm text-muted-foreground">Your conversations are waiting</p>
        </div>

        <!-- ⚠️ KEEP: Status message structure -->
        <div v-if="status" class="mb-4 text-center text-sm font-medium text-green-600 bg-green-50 p-3 rounded-lg border border-green-200">
            {{ status }}
        </div>

        <Form
            v-bind="AuthenticatedSessionController.store.form()"
            :reset-on-success="['password']"
            v-slot="{ errors, processing }"
            class="flex flex-col gap-6"
        >
            <div class="grid gap-6">
                <!-- 🎨 CUSTOMIZABLE: Enhanced input styling -->
                <div class="grid gap-2">
                    <Label for="email">Email Address</Label>
                    <Input
                        id="email"
                        type="email"
                        name="email"
                        required
                        autofocus
                        :tabindex="1"
                        autocomplete="email"
                        placeholder="your.email@example.com"
                        class="h-11 border-gray-200 focus:border-blue-500 focus:ring-blue-500"
                    />
                    <InputError :message="errors.email" />
                </div>

                <div class="grid gap-2">
                    <div class="flex items-center justify-between">
                        <Label for="password">Password</Label>
                        <TextLink
                            v-if="canResetPassword"
                            :href="request()"
                            class="text-sm text-blue-600 hover:text-blue-800"
                            :tabindex="5"
                        >
                            Forgot password?
                        </TextLink>
                    </div>
                    <Input
                        id="password"
                        type="password"
                        name="password"
                        required
                        :tabindex="2"
                        autocomplete="current-password"
                        placeholder="Enter your password"
                        class="h-11 border-gray-200 focus:border-blue-500 focus:ring-blue-500"
                    />
                    <InputError :message="errors.password" />
                </div>

                <!-- 🎨 CUSTOMIZABLE: Enhanced checkbox styling -->
                <div class="flex items-center justify-between">
                    <Label for="remember" class="flex items-center space-x-3 cursor-pointer">
                        <Checkbox id="remember" name="remember" :tabindex="3" class="border-gray-300" />
                        <span class="text-sm text-gray-700">Keep me signed in</span>
                    </Label>
                </div>

                <!-- 🎨 CUSTOMIZABLE: Enhanced button with messaging theme -->
                <Button
                    type="submit"
                    class="mt-4 w-full h-11 bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white font-medium transition-all duration-200"
                    :tabindex="4"
                    :disabled="processing"
                >
                    <LoaderCircle v-if="processing" class="h-4 w-4 animate-spin mr-2" />
                    <MessageCircle v-else class="h-4 w-4 mr-2" />
                    Sign In & Start Chatting
                </Button>
            </div>

            <!-- 🎨 CUSTOMIZABLE: Updated registration link -->
            <div class="text-center text-sm text-muted-foreground">
                New to our messaging platform?
                <TextLink
                    :href="register()"
                    class="text-blue-600 hover:text-blue-800 font-medium underline underline-offset-4"
                    :tabindex="5"
                >
                    Create your account
                </TextLink>
            </div>
        </Form>
    </AuthBase>
</template>
