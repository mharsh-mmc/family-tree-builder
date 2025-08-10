<template>
  <div v-if="show" class="fixed inset-0 z-50 overflow-y-auto">
    <!-- Backdrop -->
    <div 
      v-if="closeable" 
      class="fixed inset-0 bg-black bg-opacity-50 transition-opacity" 
      @click="close"
    ></div>
    <div v-else class="fixed inset-0 bg-black bg-opacity-50 transition-opacity"></div>
    
    <!-- Modal -->
    <div class="flex min-h-full items-center justify-center p-4">
      <div 
        :class="[
          'relative w-full bg-white rounded-lg shadow-xl',
          maxWidth === 'sm' ? 'max-w-sm' : '',
          maxWidth === 'md' ? 'max-w-md' : '',
          maxWidth === 'lg' ? 'max-w-lg' : '',
          maxWidth === 'xl' ? 'max-w-xl' : '',
          maxWidth === '2xl' ? 'max-w-2xl' : '',
          maxWidth === '3xl' ? 'max-w-3xl' : '',
          maxWidth === '4xl' ? 'max-w-4xl' : '',
          maxWidth === '5xl' ? 'max-w-5xl' : '',
          maxWidth === '6xl' ? 'max-w-6xl' : '',
          maxWidth === '7xl' ? 'max-w-7xl' : '',
          !['sm', 'md', 'lg', 'xl', '2xl', '3xl', '4xl', '5xl', '6xl', '7xl'].includes(maxWidth) ? 'max-w-2xl' : ''
        ]"
      >
        <!-- Header -->
        <div v-if="closeable" class="flex items-center justify-end p-4 border-b border-gray-200">
          <button
            @click="close"
            class="text-gray-400 hover:text-gray-600 transition-colors"
          >
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
          </button>
        </div>
        
        <!-- Content -->
        <div class="p-6">
          <slot />
        </div>
        
        <!-- Footer -->
        <div v-if="$slots.footer" class="px-6 py-4 border-t border-gray-200 bg-gray-50 rounded-b-lg">
          <slot name="footer" />
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
const props = defineProps({
  show: {
    type: Boolean,
    default: false
  },
  maxWidth: {
    type: String,
    default: '2xl'
  },
  closeable: {
    type: Boolean,
    default: true
  }
})

const emit = defineEmits(['close'])

const close = () => {
  emit('close')
}
</script>