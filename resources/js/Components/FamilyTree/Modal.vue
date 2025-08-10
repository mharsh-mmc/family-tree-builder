<template>
  <div v-if="isOpen" class="fixed inset-0 z-50 overflow-y-auto">
    <!-- Backdrop -->
    <div 
      class="fixed inset-0 bg-black bg-opacity-50 transition-opacity" 
      @click="closeOnBackdrop ? close() : null"
    ></div>
    
    <!-- Modal -->
    <div class="flex min-h-full items-center justify-center p-4">
      <div 
        :class="[
          'relative w-full bg-white rounded-lg shadow-xl',
          maxWidth
        ]"
      >
        <!-- Header -->
        <div v-if="title || showClose" class="flex items-center justify-between p-6 border-b border-gray-200">
          <h3 v-if="title" class="text-xl font-semibold text-gray-900">
            {{ title }}
          </h3>
          <button
            v-if="showClose"
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
          <slot></slot>
        </div>
        
        <!-- Footer -->
        <div v-if="$slots.footer" class="flex items-center justify-end px-6 py-4 border-t border-gray-200 bg-gray-50 rounded-b-lg">
          <slot name="footer"></slot>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
const props = defineProps({
  isOpen: {
    type: Boolean,
    default: false
  },
  title: {
    type: String,
    default: ''
  },
  showClose: {
    type: Boolean,
    default: true
  },
  closeOnBackdrop: {
    type: Boolean,
    default: true
  },
  maxWidth: {
    type: String,
    default: 'max-w-2xl'
  }
})

const emit = defineEmits(['close'])

const close = () => {
  emit('close')
}
</script>