<template>
  <div class="flex items-center justify-between py-4">
    <!-- Format Selection -->
    <div class="flex items-center space-x-2">
      <span class="text-sm font-medium text-gray-700">View Format:</span>
      <div class="flex bg-gray-100 rounded-lg p-1">
        <button
          v-for="format in formats"
          :key="format.key"
          @click="$emit('format-change', format.key)"
          :class="[
            'flex items-center px-3 py-2 text-sm font-medium rounded-md transition-colors',
            currentFormat === format.key
              ? 'bg-white text-blue-600 shadow-sm'
              : 'text-gray-600 hover:text-gray-900'
          ]"
        >
          <span class="mr-2">{{ format.icon }}</span>
          {{ format.label }}
        </button>
      </div>
    </div>

    <!-- Zoom Controls -->
    <div class="flex items-center space-x-2">
      <span class="text-sm font-medium text-gray-700">Zoom:</span>
      <div class="flex bg-gray-100 rounded-lg p-1">
        <button
          @click="$emit('zoom-out')"
          class="flex items-center justify-center w-8 h-8 text-gray-600 hover:text-gray-900 hover:bg-gray-200 rounded transition-colors"
          title="Zoom Out"
        >
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM13 10H7" />
          </svg>
        </button>
        <button
          @click="$emit('fit-view')"
          class="flex items-center justify-center w-8 h-8 text-gray-600 hover:text-gray-900 hover:bg-gray-200 rounded transition-colors"
          title="Fit to View"
        >
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4" />
          </svg>
        </button>
        <button
          @click="$emit('zoom-in')"
          class="flex items-center justify-center w-8 h-8 text-gray-600 hover:text-gray-900 hover:bg-gray-200 rounded transition-colors"
          title="Zoom In"
        >
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v6m3-3H7" />
          </svg>
        </button>
      </div>
    </div>

    <!-- Share Link -->
    <div class="flex items-center space-x-2">
      <button
        @click="copyShareLink"
        class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors"
      >
        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.367 2.684 3 3 0 00-5.367-2.684z" />
        </svg>
        Share
      </button>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'

const props = defineProps({
  currentFormat: {
    type: String,
    default: 'custom'
  },
  formats: {
    type: Array,
    default: () => []
  }
})

defineEmits(['format-change', 'zoom-in', 'zoom-out', 'fit-view'])

const copyShareLink = () => {
  const url = window.location.href
  navigator.clipboard.writeText(url).then(() => {
    // You could show a toast notification here
    alert('Share link copied to clipboard!')
  }).catch(() => {
    // Fallback for older browsers
    const textArea = document.createElement('textarea')
    textArea.value = url
    document.body.appendChild(textArea)
    textArea.select()
    document.execCommand('copy')
    document.body.removeChild(textArea)
    alert('Share link copied to clipboard!')
  })
}
</script>