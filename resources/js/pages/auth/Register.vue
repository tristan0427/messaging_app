<script setup lang="ts">
import RegisteredUserController from '@/actions/App/Http/Controllers/Auth/RegisteredUserController';
import InputError from '@/components/InputError.vue';
import TextLink from '@/components/TextLink.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { login } from '@/routes';
import { Form, Head } from '@inertiajs/vue3';
import { LoaderCircle, MessageCircle, Users, Zap, Eye, EyeOff } from 'lucide-vue-next';
import { ref } from 'vue';

const showPassword = ref(false);
const showPasswordConfirmation = ref(false);

const togglePasswordVisibility = () => {
    showPassword.value = !showPassword.value;
};

const togglePasswordConfirmationVisibility = () => {
    showPasswordConfirmation.value = !showPasswordConfirmation.value;
};
</script>

<template>
    <div class="min-h-screen flex flex-col lg:flex-row relative overflow-hidden">
        <Head title="Create Account - Messaging App" />

        <!-- Clean white background -->
        <div class="absolute inset-0 bg-white"></div>

        <!-- Left side - Welcome section -->
        <div class="flex-1 lg:flex hidden items-center justify-center p-4 lg:p-8 relative z-10">
            <div class="max-w-md text-center text-gray-800">
                <div class="mb-6 lg:mb-8">
                    <div class="flex justify-center items-center gap-3 mb-4 lg:mb-6">
                        <div class="p-2 lg:p-3 bg-gradient-to-br from-purple-500 to-pink-500 backdrop-blur-sm rounded-xl lg:rounded-2xl border border-purple-300 shadow-lg">
                            <MessageCircle class="h-6 w-6 lg:h-8 lg:w-8 text-white" />
                        </div>
                        <div class="p-1.5 lg:p-2 bg-gradient-to-br from-indigo-500 to-purple-500 backdrop-blur-sm rounded-lg lg:rounded-xl border border-indigo-300 shadow-md">
                            <Zap class="h-5 w-5 lg:h-6 lg:w-6 text-white" />
                        </div>
                        <div class="p-2 lg:p-3 bg-gradient-to-br from-pink-500 to-purple-500 backdrop-blur-sm rounded-xl lg:rounded-2xl border border-pink-300 shadow-lg">
                            <Users class="h-6 w-6 lg:h-8 lg:w-8 text-white" />
                        </div>
                    </div>
                    <h1 class="text-3xl lg:text-5xl font-bold mb-3 lg:mb-4 bg-gradient-to-r from-purple-600 to-pink-600 bg-clip-text text-transparent">
                        Join Our Community
                    </h1>
                </div>

                <p class="text-base lg:text-lg text-gray-600 mb-6 lg:mb-8 leading-relaxed px-4 lg:px-0">
                    Create your account and start connecting with friends, sharing moments, and staying in touch.
                </p>
            </div>
        </div>

        <!--  Register form -->
        <div class="flex-1 flex items-center justify-center p-8 relative z-10">
            <div class="w-full max-w-md">
                <div class="bg-white/10 backdrop-blur-xl rounded-3xl border border-white/20 p-8 shadow-2xl">
                    <div class="text-center mb-8">
                        <h2 class="text-3xl font-bold bg-gradient-to-r from-purple-600 to-pink-600 bg-clip-text text-transparent mb-2">Create Account</h2>
                        <p class="text-gray-600 text-sm">Fill in your details to get started</p>
                    </div>

                    <Form
                        v-bind="RegisteredUserController.store.form()"
                        :reset-on-success="['password', 'password_confirmation']"
                        v-slot="{ errors, processing }"
                        class="space-y-6"
                    >
                        <!-- Name field -->
                        <div class="space-y-2">
                            <Label for="name" class="text-gray-600 font-medium">Full Name</Label>
                            <Input
                                id="name"
                                type="text"
                                name="name"
                                required
                                autofocus
                                :tabindex="1"
                                autocomplete="name"
                                placeholder="Enter your full name"
                                class="h-12 bg-white/10 backdrop-blur-sm border-white/30 text-black  focus:border-white/50 focus:ring-2 focus:ring-white/25 rounded-xl"
                            />
                            <InputError :message="errors.name" class="text-red-300" />
                        </div>

                        <!-- Email field -->
                        <div class="space-y-2">
                            <Label for="email" class="text-gray-600 font-medium">Email Address</Label>
                            <Input
                                id="email"
                                type="email"
                                name="email"
                                required
                                :tabindex="2"
                                autocomplete="email"
                                placeholder="Enter your email"
                                class="h-12 bg-white/10 backdrop-blur-sm border-white/30 text-black  focus:border-white/50 focus:ring-2 focus:ring-white/25 rounded-xl"
                            />
                            <InputError :message="errors.email" class="text-red-300" />
                        </div>

                        <!-- Password field -->
                        <div class="space-y-2">
                            <Label for="password" class="text-gray-600 font-medium">Password</Label>
                            <div class="relative">
                                <Input
                                    id="password"
                                    :type="showPassword ? 'text' : 'password'"
                                    name="password"
                                    required
                                    :tabindex="3"
                                    autocomplete="new-password"
                                    placeholder="Create a strong password"
                                    class="h-12 bg-white/10 backdrop-blur-sm border-white/30 text-black  focus:border-white/50 focus:ring-2 focus:ring-white/25 rounded-xl pr-12"
                                />
                                <button
                                    type="button"
                                    @click="togglePasswordVisibility"
                                    class="absolute right-3 top-1/2 transform -translate-y-1/2 text-purple-500 hover:text-purple-300 transition-colors"
                                    :tabindex="-1"
                                >
                                    <Eye v-if="!showPassword" class="h-5 w-5" />
                                    <EyeOff v-else class="h-5 w-5" />
                                </button>
                            </div>
                            <InputError :message="errors.password" class="text-red-300" />
                        </div>

                        <!-- Password confirmation field -->
                        <div class="space-y-2">
                            <Label for="password_confirmation" class="text-gray-600 font-medium">Confirm Password</Label>
                            <div class="relative">
                                <Input
                                    id="password_confirmation"
                                    :type="showPasswordConfirmation ? 'text' : 'password'"
                                    name="password_confirmation"
                                    required
                                    :tabindex="4"
                                    autocomplete="new-password"
                                    placeholder="Confirm your password"
                                    class="h-12 bg-white/10 backdrop-blur-sm border-white/30 text-black  focus:border-white/50 focus:ring-2 focus:ring-white/25 rounded-xl pr-12"
                                />
                                <button
                                    type="button"
                                    @click="togglePasswordConfirmationVisibility"
                                    class="absolute right-3 top-1/2 transform -translate-y-1/2 text-purple-500 hover:text-purple-300 transition-colors"
                                    :tabindex="-1"
                                >
                                    <Eye v-if="!showPasswordConfirmation" class="h-5 w-5" />
                                    <EyeOff v-else class="h-5 w-5" />
                                </button>
                            </div>
                            <InputError :message="errors.password_confirmation" class="text-red-300" />
                        </div>

                        <!-- Submit button -->
                        <Button
                            type="submit"
                            class="w-full h-12 bg-gradient-to-r from-orange-400 to-pink-500 hover:from-orange-500 hover:to-pink-600 text-white font-semibold transition-all duration-300 rounded-xl shadow-lg hover:shadow-xl transform hover:scale-[1.02] mt-6"
                            :tabindex="5"
                            :disabled="processing"
                        >
                            <LoaderCircle v-if="processing" class="h-5 w-5 animate-spin mr-2" />
                            <MessageCircle v-else class="h-5 w-5 mr-2" />
                            Create Account & Start Messaging
                        </Button>
                    </Form>

                    <!-- Login link -->
                    <div class="text-center mt-6 pt-6 border-t border-white/20">
                        <p class="bg-gradient-to-r from-purple-600 to-pink-600 bg-clip-text text-transparent text-sm">
                            Already have an account?
                            <TextLink
                                :href="login()"
                                class="bg-gradient-to-r from-purple-600 to-pink-600 bg-clip-text text-transparent hover:text-purple-300 font-medium ml-1 underline underline-offset-4 transition-colors"
                                :tabindex="6"
                            >
                                Sign in to continue chatting
                            </TextLink>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<!-- Clean minimalist styles -->
<style scoped>
input::placeholder {
    color:darkgray;
}

input {
    background: rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(10px);
}

input:focus {
    box-shadow: 0 0 0 2px rgba(255, 255, 255, 0.25);
    outline: none;
    border-color: rgba(255, 255, 255, 0.5);
}

* {
    transition-duration: 200ms;
    transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
}
</style>
