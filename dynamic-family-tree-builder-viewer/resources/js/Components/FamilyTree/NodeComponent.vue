<template>
  <div
    class="family-node bg-white rounded-lg shadow-lg border-2 border-gray-200 hover:border-blue-300 transition-all duration-200 cursor-pointer"
    :class="{ 'border-blue-500': isSelected }"
    @click="handleNodeClick"
  >
    <!-- Profile Picture -->
    <div class="relative">
      <img
        :src="node.data.profilePic || '/images/default-avatar.png'"
        :alt="node.data.name"
        class="w-20 h-20 rounded-t-lg object-cover mx-auto"
        @error="handleImageError"
      />
      <!-- Status Indicator -->
      <div
        :class="[
          'absolute top-2 right-2 w-3 h-3 rounded-full',
          node.data.isAlive ? 'bg-green-500' : 'bg-red-500'
        ]"
        :title="node.data.isAlive ? 'Alive' : 'Deceased'"
      ></div>
    </div>

    <!-- Node Content -->
    <div class="p-3 text-center">
      <h3 class="font-semibold text-gray-900 text-sm truncate" :title="node.data.name">
        {{ node.data.name }}
      </h3>
      <p class="text-xs text-gray-600">{{ node.data.relation }}</p>
      
      <!-- Age Display -->
      <div v-if="node.data.age !== null" class="mt-1">
        <span class="text-xs text-gray-500">{{ node.data.age }} years old</span>
      </div>

      <!-- Action Buttons (only in builder mode) -->
      <div v-if="!readonly" class="mt-2 flex justify-center space-x-1">
        <button
          @click.stop="$emit('edit', node)"
          class="p-1 text-blue-600 hover:text-blue-800 hover:bg-blue-50 rounded transition-colors"
          title="Edit"
        >
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
          </svg>
        </button>
        <button
          @click.stop="confirmDelete"
          class="p-1 text-red-600 hover:text-red-800 hover:bg-red-50 rounded transition-colors"
          title="Delete"
        >
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
          </svg>
        </button>
      </div>

      <!-- View Profile Button (in viewer mode) -->
      <button
        v-else
        @click.stop="$emit('view-profile', node)"
        class="mt-2 w-full px-2 py-1 text-xs bg-blue-100 text-blue-700 hover:bg-blue-200 rounded transition-colors"
      >
        View Profile
      </button>
    </div>

    <!-- Connection Handles -->
    <div class="absolute top-1/2 left-0 w-3 h-3 bg-blue-500 rounded-full transform -translate-y-1/2 -translate-x-1/2"></div>
    <div class="absolute top-1/2 right-0 w-3 h-3 bg-blue-500 rounded-full transform -translate-y-1/2 translate-x-1/2"></div>
    <div class="absolute top-0 left-1/2 w-3 h-3 bg-blue-500 rounded-full transform -translate-x-1/2 -translate-y-1/2"></div>
    <div class="absolute bottom-0 left-1/2 w-3 h-3 bg-blue-500 rounded-full transform -translate-x-1/2 translate-y-1/2"></div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'

const props = defineProps({
  node: {
    type: Object,
    required: true
  },
  readonly: {
    type: Boolean,
    default: false
  }
})

defineEmits(['edit', 'delete', 'view-profile'])

const isSelected = ref(false)

const handleNodeClick = () => {
  isSelected.value = !isSelected.value
}

const confirmDelete = () => {
  if (confirm(`Are you sure you want to delete ${props.node.data.name}?`)) {
    emit('delete', props.node.id)
  }
}

const handleImageError = (event) => {
  event.target.src = '/images/default-avatar.png'
}
</script>

<style scoped>
.family-node {
  min-width: 120px;
  max-width: 150px;
}

.family-node:hover {
  transform: translateY(-2px);
  box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
}

/* VueFlow handle styles */
.vue-flow__handle {
  width: 12px;
  height: 12px;
  background: #3b82f6;
  border: 2px solid white;
  border-radius: 50%;
}

.vue-flow__handle:hover {
  background: #2563eb;
}
</style>