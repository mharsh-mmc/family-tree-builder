<template>
    <div id="app">
        <Head :title="$page.props.title" />
        
        <div class="min-h-screen bg-gray-100">
            <slot />
        </div>
    </div>
</template>

<script setup>
import { Head } from '@inertiajs/vue3'
</script>