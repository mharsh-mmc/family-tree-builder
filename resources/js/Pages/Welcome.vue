<template>
    <div class="min-h-screen bg-gradient-to-br from-blue-50 via-white to-purple-50">
        <!-- Navigation -->
        <nav class="bg-white/80 backdrop-blur-md border-b border-gray-200 sticky top-0 z-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <h1 class="text-2xl font-bold text-gray-900">
                                🌳 Family Tree Builder
                            </h1>
                        </div>
                    </div>
                    <div class="flex items-center space-x-4">
                        <Link
                            v-if="canLogin"
                            :href="route('login')"
                            class="text-gray-700 hover:text-gray-900 px-3 py-2 rounded-md text-sm font-medium transition-colors"
                        >
                            Log in
                        </Link>
                        <Link
                            v-if="canRegister"
                            :href="route('register')"
                            class="bg-primary-600 text-white hover:bg-primary-700 px-4 py-2 rounded-md text-sm font-medium transition-colors"
                        >
                            Get Started
                        </Link>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Hero Section -->
        <div class="relative overflow-hidden">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-24">
                <div class="text-center">
                    <h1 class="text-4xl sm:text-6xl font-bold text-gray-900 mb-8">
                        Build Your Family Tree
                        <span class="text-primary-600">Like Never Before</span>
                    </h1>
                    <p class="text-xl text-gray-600 mb-12 max-w-3xl mx-auto">
                        Create, visualize, and explore your family history with our interactive drag-and-drop family tree builder. 
                        Generate multiple layouts automatically and share your family story with loved ones.
                    </p>
                    <div class="flex flex-col sm:flex-row gap-4 justify-center">
                        <Link
                            :href="route('register')"
                            class="bg-primary-600 text-white px-8 py-4 rounded-lg text-lg font-semibold hover:bg-primary-700 transition-colors shadow-lg hover:shadow-xl"
                        >
                            Start Building Now
                        </Link>
                        <Link
                            :href="route('login')"
                            class="border-2 border-gray-300 text-gray-700 px-8 py-4 rounded-lg text-lg font-semibold hover:border-primary-500 hover:text-primary-600 transition-colors"
                        >
                            View Demo
                        </Link>
                    </div>
                </div>
            </div>
        </div>

        <!-- Features Section -->
        <div class="py-24 bg-white">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-16">
                    <h2 class="text-3xl font-bold text-gray-900 mb-4">
                        Powerful Features for Family Historians
                    </h2>
                    <p class="text-xl text-gray-600 max-w-2xl mx-auto">
                        Everything you need to create a comprehensive family tree that tells your family's unique story.
                    </p>
                </div>
                
                <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                    <div class="text-center p-6">
                        <div class="w-16 h-16 bg-primary-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <span class="text-2xl">🎨</span>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 mb-2">Drag & Drop Builder</h3>
                        <p class="text-gray-600">Intuitive interface for creating and organizing family connections with ease.</p>
                    </div>
                    
                    <div class="text-center p-6">
                        <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <span class="text-2xl">🔄</span>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 mb-2">Auto Layouts</h3>
                        <p class="text-gray-600">Generate Vertical, Horizontal, and Circular layouts automatically.</p>
                    </div>
                    
                    <div class="text-center p-6">
                        <div class="w-16 h-16 bg-purple-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <span class="text-2xl">👥</span>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 mb-2">Rich Profiles</h3>
                        <p class="text-gray-600">Add photos, dates, and detailed biographies for each family member.</p>
                    </div>
                    
                    <div class="text-center p-6">
                        <div class="w-16 h-16 bg-yellow-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <span class="text-2xl">🔗</span>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 mb-2">Smart Connections</h3>
                        <p class="text-gray-600">Define relationships and connection types between family members.</p>
                    </div>
                    
                    <div class="text-center p-6">
                        <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <span class="text-2xl">🌐</span>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 mb-2">Public Sharing</h3>
                        <p class="text-gray-600">Share your family tree with relatives and friends via public links.</p>
                    </div>
                    
                    <div class="text-center p-6">
                        <div class="w-16 h-16 bg-indigo-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <span class="text-2xl">📱</span>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 mb-2">Responsive Design</h3>
                        <p class="text-gray-600">Works perfectly on desktop, tablet, and mobile devices.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- CTA Section -->
        <div class="py-24 bg-primary-600">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
                <h2 class="text-3xl font-bold text-white mb-4">
                    Ready to Discover Your Family History?
                </h2>
                <p class="text-xl text-primary-100 mb-8 max-w-2xl mx-auto">
                    Join thousands of families who have already created their family trees with our platform.
                </p>
                <Link
                    :href="route('register')"
                    class="bg-white text-primary-600 px-8 py-4 rounded-lg text-lg font-semibold hover:bg-gray-100 transition-colors shadow-lg"
                >
                    Create Your Family Tree Today
                </Link>
            </div>
        </div>

        <!-- Footer -->
        <footer class="bg-gray-900 text-white py-12">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center">
                    <h3 class="text-2xl font-bold mb-4">🌳 Family Tree Builder</h3>
                    <p class="text-gray-400 mb-6">
                        Building connections, preserving memories, one family at a time.
                    </p>
                    <div class="text-sm text-gray-500">
                        &copy; {{ new Date().getFullYear() }} Family Tree Builder. All rights reserved.
                    </div>
                </div>
            </div>
        </footer>
    </div>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';

defineProps({
    canLogin: Boolean,
    canRegister: Boolean,
    laravelVersion: String,
    phpVersion: String,
});
</script>