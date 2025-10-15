<template>
    <div class="min-h-screen bg-gray-100">
        <nav class="bg-white border-b border-gray-100">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex items-center">
                        <h1 class="text-xl font-semibold">Dashboard</h1>
                    </div>
                    <div class="flex items-center">
                        <span class="text-sm text-gray-600">Welcome, {{ user?.name || 'Guest' }}!</span>
                    </div>
                </div>
            </div>
        </nav>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                    <div class="p-6">
                        <h2 class="text-2xl font-bold mb-4">Welcome to your Dashboard</h2>
                        <p class="text-gray-600 mb-6">
                            You're logged in! This is a sample dashboard page built with Inertia.js and Vue 3.
                        </p>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div class="bg-blue-50 p-6 rounded-lg">
                                <h3 class="font-semibold text-blue-900 mb-2">Total Users</h3>
                                <p class="text-3xl font-bold text-blue-600">{{ stats.users || 0 }}</p>
                            </div>
                            <div class="bg-green-50 p-6 rounded-lg">
                                <h3 class="font-semibold text-green-900 mb-2">Active Projects</h3>
                                <p class="text-3xl font-bold text-green-600">{{ stats.projects || 0 }}</p>
                            </div>
                            <div class="bg-purple-50 p-6 rounded-lg">
                                <h3 class="font-semibold text-purple-900 mb-2">Tasks Completed</h3>
                                <p class="text-3xl font-bold text-purple-600">{{ stats.tasks || 0 }}</p>
                            </div>
                        </div>

                        <div class="mt-8">
                            <h3 class="text-lg font-semibold mb-4">Quick Actions</h3>
                            <div class="flex gap-4">
                                <button class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                    Create New Project
                                </button>
                                <button class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                                    View Reports
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { defineProps } from 'vue'

defineProps({
    user: Object,
    stats: {
        type: Object,
        default: () => ({
            users: 0,
            projects: 0,
            tasks: 0
        })
    }
})
</script>
