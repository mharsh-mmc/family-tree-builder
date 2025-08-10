<template>
  <div v-if="isOpen" class="fixed inset-0 z-50 overflow-y-auto">
    <!-- Backdrop -->
    <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity" @click="close"></div>
    
    <!-- Modal -->
    <div class="flex min-h-full items-center justify-center p-4">
      <div class="relative w-full max-w-2xl bg-white rounded-lg shadow-xl">
        <!-- Header -->
        <div class="flex items-center justify-between p-6 border-b border-gray-200">
          <h3 class="text-xl font-semibold text-gray-900">
            {{ node?.data?.name }}'s Profile
          </h3>
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
          <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Profile Picture -->
            <div class="md:col-span-1">
              <div class="flex flex-col items-center">
                <div class="w-32 h-32 rounded-full overflow-hidden bg-gray-100 border-4 border-gray-200">
                  <img
                    :src="node?.data?.profilePic || '/images/default-avatar.png'"
                    :alt="node?.data?.name"
                    class="w-full h-full object-cover"
                    @error="handleImageError"
                  />
                </div>
                <div class="mt-4 text-center">
                  <h4 class="text-lg font-medium text-gray-900">{{ node?.data?.name }}</h4>
                  <p class="text-sm text-gray-600">{{ node?.data?.relation }}</p>
                  <div class="mt-2">
                    <span
                      :class="[
                        'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium',
                        node?.data?.isAlive
                          ? 'bg-green-100 text-green-800'
                          : 'bg-red-100 text-red-800'
                      ]"
                    >
                      {{ node?.data?.isAlive ? 'Living' : 'Deceased' }}
                    </span>
                  </div>
                </div>
              </div>
            </div>
            
            <!-- Details -->
            <div class="md:col-span-2">
              <div class="space-y-4">
                <!-- Basic Information -->
                <div>
                  <h5 class="text-sm font-medium text-gray-500 uppercase tracking-wide mb-2">
                    Basic Information
                  </h5>
                  <div class="grid grid-cols-2 gap-4">
                    <div>
                      <label class="block text-sm font-medium text-gray-700">Date of Birth</label>
                      <p class="mt-1 text-sm text-gray-900">
                        {{ node?.data?.dob ? formatDate(node.data.dob) : 'Not specified' }}
                      </p>
                    </div>
                    <div v-if="!node?.data?.isAlive">
                      <label class="block text-sm font-medium text-gray-700">Date of Death</label>
                      <p class="mt-1 text-sm text-gray-900">
                        {{ node?.data?.dod ? formatDate(node.data.dod) : 'Not specified' }}
                      </p>
                    </div>
                    <div v-if="node?.data?.age !== null && node?.data?.age !== undefined">
                      <label class="block text-sm font-medium text-gray-700">Age</label>
                      <p class="mt-1 text-sm text-gray-900">
                        {{ node.data.age }} years
                      </p>
                    </div>
                  </div>
                </div>
                
                <!-- Biodata -->
                <div v-if="node?.data?.biodata">
                  <h5 class="text-sm font-medium text-gray-500 uppercase tracking-wide mb-2">
                    Biography
                  </h5>
                  <p class="text-sm text-gray-900 leading-relaxed">
                    {{ node.data.biodata }}
                  </p>
                </div>
                
                <!-- Family Connections -->
                <div>
                  <h5 class="text-sm font-medium text-gray-500 uppercase tracking-wide mb-2">
                    Family Connections
                  </h5>
                  <div class="text-sm text-gray-900">
                    <p>This person is connected to the family tree as a <strong>{{ node?.data?.relation }}</strong>.</p>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        
        <!-- Footer -->
        <div class="flex items-center justify-end px-6 py-4 border-t border-gray-200 bg-gray-50 rounded-b-lg">
          <button
            @click="close"
            class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors"
          >
            Close
          </button>
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
  node: {
    type: Object,
    default: null
  }
})

const emit = defineEmits(['close'])

const close = () => {
  emit('close')
}

const formatDate = (dateString) => {
  if (!dateString) return 'Not specified'
  const date = new Date(dateString)
  return date.toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'long',
    day: 'numeric'
  })
}

const handleImageError = (event) => {
  event.target.src = '/images/default-avatar.png'
}
</script>
