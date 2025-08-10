<template>
  <div class="space-y-4">
    <h3 class="text-lg font-semibold text-gray-900">Tools</h3>
    
    <!-- Add Person Button -->
    <button
      @click="$emit('add-person')"
      class="w-full flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors"
    >
      <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
      </svg>
      Add Person
    </button>

    <!-- Connect Nodes Button -->
    <button
      @click="$emit('connect-nodes')"
      :class="[
        'w-full flex items-center justify-center px-4 py-2 border text-sm font-medium rounded-md transition-colors',
        isConnecting
          ? 'border-blue-500 text-blue-700 bg-blue-50'
          : 'border-gray-300 text-gray-700 bg-white hover:bg-gray-50'
      ]"
    >
      <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" />
      </svg>
      {{ isConnecting ? 'Connecting...' : 'Connect Nodes' }}
    </button>

    <!-- Undo/Redo Section -->
    <div class="border-t border-gray-200 pt-4">
      <h4 class="text-sm font-medium text-gray-700 mb-2">History</h4>
      <div class="flex space-x-2">
        <button
          @click="$emit('undo')"
          :disabled="!canUndo"
          :class="[
            'flex-1 flex items-center justify-center px-3 py-2 border border-gray-300 text-sm font-medium rounded-md transition-colors',
            canUndo
              ? 'text-gray-700 bg-white hover:bg-gray-50'
              : 'text-gray-400 bg-gray-100 cursor-not-allowed'
          ]"
        >
          <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6" />
          </svg>
          Undo
        </button>
        <button
          @click="$emit('redo')"
          :disabled="!canRedo"
          :class="[
            'flex-1 flex items-center justify-center px-3 py-2 border border-gray-300 text-sm font-medium rounded-md transition-colors',
            canRedo
              ? 'text-gray-700 bg-white hover:bg-gray-50'
              : 'text-gray-400 bg-gray-100 cursor-not-allowed'
          ]"
        >
          <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 10h-10a8 8 0 00-8 8v2M21 10l-6 6m6-6l-6-6" />
          </svg>
          Redo
        </button>
      </div>
    </div>

    <!-- Save Button -->
    <div class="border-t border-gray-200 pt-4">
      <button
        @click="$emit('save')"
        class="w-full flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors"
      >
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4" />
        </svg>
        Save Tree
      </button>
    </div>

    <!-- Instructions -->
    <div class="border-t border-gray-200 pt-4">
      <h4 class="text-sm font-medium text-gray-700 mb-2">Instructions</h4>
      <div class="text-xs text-gray-600 space-y-2">
        <p>• Drag nodes to reposition them</p>
        <p>• Use Connect Nodes to create relationships</p>
        <p>• Click nodes to edit properties</p>
        <p>• Use undo/redo to manage changes</p>
      </div>
    </div>
  </div>
</template>

<script setup>
defineProps({
  canUndo: {
    type: Boolean,
    default: false
  },
  canRedo: {
    type: Boolean,
    default: false
  },
  isConnecting: {
    type: Boolean,
    default: false
  }
})

defineEmits(['add-person', 'connect-nodes', 'undo', 'redo', 'save'])
</script>