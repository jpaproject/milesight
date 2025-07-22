<x-guest-layout>
    <div class="relative flex flex-col justify-center w-full h-screen dark:bg-gray-900 sm:p-0 lg:flex-row">
        <div class="flex flex-col flex-1 w-full lg:w-1/2">

            {{-- Back Button --}}
            {{-- <div class="w-full max-w-md pt-10 mx-auto">
                <a href="index.html"
                    class="inline-flex items-center text-sm text-gray-500 transition-colors hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300">
                    <svg class="stroke-current" xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                        viewBox="0 0 20 20" fill="none">
                        <path d="M12.7083 5L7.5 10.2083L12.7083 15.4167" stroke="" stroke-width="1.5"
                            stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                    Back to dashboard
                </a>
            </div> --}}

            <div class="flex flex-col justify-center flex-1 w-full max-w-md mx-auto">
                <div>
                    <div class="mb-5 sm:mb-8">
                        <h1 class="mb-2 font-semibold text-gray-800 text-title-sm dark:text-white/90 sm:text-title-md">
                            Sign In
                        </h1>
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            Enter your email or username and password to sign in!
                        </p>
                    </div>
                    <div>
                        <form method="POST" action="{{ route('login') }}">
                            @csrf

                            <div class="space-y-5">
                                <!-- Email -->
                                <div>
                                    <x-input-label for="login" :value="__('Email Or Username')" required />
                                    <x-text-input id="login" class="block mt-1 w-full" type="text" name="login"
                                        :value="old('login')" required autofocus autocomplete="username"
                                        placeholder="Enter your email or username" />
                                    <x-input-error :messages="$errors->get('login')" class="mt-2" />
                                </div>

                                <!-- Password -->
                                <div>
                                    <x-input-label for="login" :value="__('Password')" required />
                                    <x-password-input id="password" class="block mt-1 w-full" name="password" required
                                        autocomplete="current-password" placeholder="Enter your password" />
                                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                                </div>

                                <!-- Remember Me -->
                                <div class="flex items-center justify-between">
                                    <div x-data="{ checkboxToggle: false }">
                                        <label for="remember_me"
                                            class="flex items-center text-sm font-normal text-gray-700 cursor-pointer select-none dark:text-gray-400">
                                            <div class="relative">
                                                <input type="checkbox" id="remember_me" name="remember" class="sr-only"
                                                    @change="checkboxToggle = !checkboxToggle" />
                                                <div :class="checkboxToggle ? 'border-brand-500 bg-brand-500' :
                                                    'bg-transparent border-gray-300 dark:border-gray-700'"
                                                    class="mr-3 flex h-5 w-5 items-center justify-center rounded-md border-[1.25px]">
                                                    <span :class="checkboxToggle ? '' : 'opacity-0'">
                                                        <svg width="14" height="14" viewBox="0 0 14 14"
                                                            fill="none" xmlns="http://www.w3.org/2000/svg">
                                                            <path d="M11.6666 3.5L5.24992 9.91667L2.33325 7"
                                                                stroke="white" stroke-width="1.94437"
                                                                stroke-linecap="round" stroke-linejoin="round" />
                                                        </svg>
                                                    </span>
                                                </div>
                                            </div>
                                            Keep me logged in
                                        </label>
                                    </div>
                                </div>
                                <!-- Button -->
                                <div>
                                    <button type="submit"
                                        class="flex items-center justify-center w-full px-4 py-3 text-sm font-medium text-white transition rounded-lg bg-brand-500 shadow-theme-xs hover:bg-brand-600">
                                        Sign In
                                    </button>
                                </div>
                            </div>
                        </form>
                        {{-- <div class="mt-5">
                            <p class="text-sm font-normal text-center text-gray-700 dark:text-gray-400 sm:text-start">
                                Don't have an account?
                                <a href="/signup.html"
                                    class="text-brand-500 hover:text-brand-600 dark:text-brand-400">Sign Up</a>
                            </p>
                        </div> --}}
                    </div>
                </div>
            </div>
        </div>

        <div class="relative items-center hidden w-full h-full bg-brand-950 dark:bg-white/5 lg:grid lg:w-1/2">
            <div class="flex items-center justify-center z-1">
                <!-- ===== Common Grid Shape Start ===== -->
                <include src="./partials/common-grid-shape.html"></include>
                <div class="flex flex-col items-center max-w-xs">
                    <a href="index.html" class="block mb-4">
                        <img src="./images/logo/auth-logo.svg" alt="Logo" />
                    </a>
                    <p class="text-center text-gray-400 dark:text-white/60">
                        Instant Insights into Every Room’s Condition
                    </p>
                </div>
            </div>
        </div>

        <!-- Toggler -->
        <x-toggler />
    </div>
</x-guest-layout>
