<template>
    <div class="min-h-screen bg-gray-100 py-6 flex flex-col justify-center sm:py-12">
        <div class="relative py-3 sm:max-w-xl sm:mx-auto">
            <div class="absolute inset-0 bg-gradient-to-r from-cyan-400 to-light-blue-500 shadow-lg transform -skew-y-6 sm:skew-y-0 sm:-rotate-6 sm:rounded-3xl"></div>
            <div class="relative px-4 py-10 bg-white shadow-lg sm:rounded-3xl sm:p-20">
                <div class="max-w-md mx-auto">
                    <div class="flex items-center space-x-5">
                        <div class="h-14 w-14 bg-gradient-to-r from-cyan-400 to-light-blue-500 rounded-full flex items-center justify-center">
                            <svg class="h-8 w-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                            </svg>
                        </div>
                        <div class="block font-semibold text-xl text-gray-700">
                            <h2 class="leading-relaxed">Welcome to Inertia + Vue 3</h2>
                            <p class="text-sm text-gray-500 font-normal leading-relaxed">Your Laravel application is ready!</p>
                        </div>
                    </div>
                    <div class="divide-y divide-gray-200">
                        <div class="py-8 text-base leading-6 space-y-4 text-gray-700 sm:text-lg sm:leading-7">
                            <p>Inertia.js is successfully configured with:</p>
                            <ul class="list-disc space-y-2 ml-6">
                                <li>Laravel {{ laravelVersion }}</li>
                                <li>Vue {{ vueVersion }}</li>
                                <li>Inertia.js</li>
                                <li>Tailwind CSS</li>
                            </ul>
                        </div>
                        <div class="pt-6 text-base leading-6 font-bold sm:text-lg sm:leading-7">
                            <p>Want to learn more?</p>
                            <p class="mt-2 space-x-4">
                                <a href="https://inertiajs.com" class="text-cyan-600 hover:text-cyan-700 underline">Inertia.js Docs</a>
                                <a href="https://vuejs.org" class="text-green-600 hover:text-green-700 underline">Vue 3 Docs</a>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { defineProps } from 'vue'
import { version } from 'vue'

defineProps({
    laravelVersion: String,
})

const vueVersion = version
</script>
