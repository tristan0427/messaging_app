<script setup lang="ts">
import AuthenticatedSessionController from '@/actions/App/Http/Controllers/Auth/AuthenticatedSessionController';
import InputError from '@/components/InputError.vue';
import TextLink from '@/components/TextLink.vue';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { register } from '@/routes';
import { request } from '@/routes/password';
import { Form, Head } from '@inertiajs/vue3';
import { LoaderCircle, MessageCircle, Users, Zap, Eye, EyeOff } from 'lucide-vue-next';
import { ref } from 'vue';

defineProps<{
    status?: string;
    canResetPassword: boolean;
}>();

const showPassword = ref(false);
const togglePasswordVisibility = () => {
    showPassword.value = !showPassword.value;
};
</script>

<template>
    <div class="min-h-screen flex flex-col lg:flex-row relative overflow-hidden">
        <Head title="Sign In - Messaging App" />

        <div class="absolute inset-0 bg-white"> </div>

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
                        Welcome Back
                    </h1>
                </div>

                <!-- Description -->
                <p class="text-base lg:text-lg text-gray-600 mb-6 lg:mb-8 leading-relaxed px-4 lg:px-0">
                    Connect with friends, share moments, and stay in touch with the people who matter most.
                </p>
            </div>
        </div>

        <!-- Right side - Login form -->
        <div class="flex-1 flex items-center justify-center p-8 relative z-10">
            <div class="w-full max-w-md">
                <div class="bg-white/10 backdrop-blur-xl rounded-3xl border border-white/20 p-8 shadow-2xl">
                    <div class="text-center mb-8">
                        <h2 class="text-3xl font-bold bg-gradient-to-r from-purple-600 to-pink-600 bg-clip-text text-transparent mb-2">Sign in</h2>
                        <p class="text-gray-600 text-sm">Enter your credentials to continue</p>
                    </div>

                    <!-- Status message -->
                    <div v-if="status" class="mb-6 text-center text-sm font-medium text-green-300 bg-green-500/20 backdrop-blur-sm p-3 rounded-xl border border-green-400/30">
                        {{ status }}
                    </div>

                    <Form
                        v-bind="AuthenticatedSessionController.store.form()"
                        :reset-on-success="['password']"
                        v-slot="{ errors, processing }"
                        class="space-y-6"
                    >
                        <!-- Email field -->
                        <div class="space-y-2">
                            <Label for="email" class="text-gray-600 font-medium">Email Address</Label>
                            <Input
                                id="email"
                                type="email"
                                name="email"
                                required
                                autofocus
                                :tabindex="1"
                                autocomplete="email"
                                placeholder="Enter your email"
                                class="h-12 bg-white/10 backdrop-blur-sm border-white/30 text-black  focus:border-white/50 focus:ring-2 focus:ring-white/25 rounded-xl"
                            />
                            <InputError :message="errors.email" class="text-red-300" />
                        </div>

                        <!-- Password field -->
                        <div class="space-y-2">
                            <div class="flex items-center justify-between">
                                <Label for="password" class="text-gray-600 font-medium">Password</Label>
                                <TextLink
                                    v-if="canResetPassword"
                                    :href="request()"
                                    class="text-sm bg-gradient-to-r from-purple-600 to-pink-600 bg-clip-text text-transparent hover:text-purple-300 transition-colors"
                                    :tabindex="5"
                                >
                                    Forgot password?
                                </TextLink>
                            </div>
                            <Input
                                id="password"
                                :type="showPassword ? 'text' : 'password'"
                                name="password"
                                required
                                :tabindex="2"
                                autocomplete="current-password"
                                placeholder="Enter your password"
                                class="h-12 bg-white/10 backdrop-blur-sm border-white/30 text-black focus:border-white/50 focus:ring-2 focus:ring-white/25 rounded-xl"
                            />

                            <button
                                type="button"
                                @click="togglePasswordVisibility"
                                class="absolute right-12 top-69 transform -translate-y-1/2 text-purple-500 hover:text-purple-300"
                                :tabindex="-1"
                            >
                                <Eye v-if="!showPassword" class="h-5 w-5" />
                                <EyeOff v-else class="h-5 w-5" />
                            </button>

                            <InputError :message="errors.password" class="text-red-300" />
                        </div>

                        <div class="flex items-center">
                            <Label for="remember" class="flex items-center space-x-3 cursor-pointer">
                                <Checkbox
                                    id="remember"
                                    name="remember"
                                    :tabindex="3"
                                    class="border-purple-500 data-[state=checked]:bg-purple-500 data-[state=checked]:border-purple-500"
                                />
                                <span class="text-sm bg-gradient-to-r from-purple-600 to-pink-600 bg-clip-text text-transparent">Keep me signed in</span>
                            </Label>
                        </div>

                        <!-- Submit button -->
                        <Button
                            type="submit"
                            class="w-full h-12 bg-gradient-to-r from-orange-400 to-pink-500 hover:from-orange-500 hover:to-pink-600 text-white font-semibold transition-all duration-300 rounded-xl shadow-lg hover:shadow-xl transform hover:scale-[1.02]"
                            :tabindex="4"
                            :disabled="processing"
                        >
                            <LoaderCircle v-if="processing" class="h-5 w-5 animate-spin mr-2" />
                            <span v-else>Submit</span>
                        </Button>
                    </Form>

                    <!-- Registration link -->
                    <div class="text-center mt-6 pt-6 border-t border-white/20">
                        <p class="bg-gradient-to-r from-purple-600 to-pink-600 bg-clip-text text-transparent text-sm">
                            New to our platform?
                            <TextLink
                                :href="register()"
                                class="bg-gradient-to-r from-purple-600 to-pink-600 bg-clip-text text-transparent hover:text-purple-300 font-medium ml-1 underline underline-offset-4 transition-colors"
                                :tabindex="6"
                            >
                                Create your account
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
    box-shadow: 0 0 0 1px rgb(147 51 234);
    outline: none;
}

* {
    transition-duration: 200ms;
    transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
}
</style>
