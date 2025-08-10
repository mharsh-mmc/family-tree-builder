<template>
  <div class="min-h-screen bg-gray-100">
    <!-- Header -->
    <div class="bg-white shadow">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center py-6">
          <div>
            <h1 class="text-3xl font-bold text-gray-900">
              {{ user.name }}'s Family Tree
            </h1>
            <p class="text-gray-600">View and explore family relationships</p>
          </div>
          <div class="flex space-x-4">
            <a
              href="/"
              class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50"
            >
              Back to Home
            </a>
          </div>
        </div>
      </div>
    </div>

    <!-- Viewer Toolbar -->
    <div class="bg-white border-b border-gray-200">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <ViewerToolbar
          :current-format="currentFormat"
          :formats="availableFormats"
          @format-change="changeFormat"
          @zoom-in="zoomIn"
          @zoom-out="zoomOut"
          @fit-view="fitView"
        />
      </div>
    </div>

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <div class="bg-white rounded-lg shadow-lg overflow-hidden">
        <div class="h-[600px] relative">
          <VueFlow
            v-model="elements"
            :default-viewport="{ zoom: 1 }"
            :min-zoom="0.2"
            :max-zoom="4"
            class="bg-gray-50"
            @node-click="onNodeClick"
            ref="vueFlowRef"
          >
            <template #node-familyNode="nodeProps">
              <NodeComponent
                :node="nodeProps"
                :readonly="true"
                @view-profile="viewProfile"
              />
            </template>

            <Controls />
            <MiniMap />
            <Background pattern-color="#aaa" gap="8" />
          </VueFlow>
        </div>
      </div>

      <!-- Family Tree Info -->
      <div class="mt-8 bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">About This Family Tree</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
          <div>
            <h4 class="font-medium text-gray-700">Total Members</h4>
            <p class="text-2xl font-bold text-blue-600">{{ totalMembers }}</p>
          </div>
          <div>
            <h4 class="font-medium text-gray-700">Current Format</h4>
            <p class="text-lg text-gray-900 capitalize">{{ currentFormat }}</p>
          </div>
          <div>
            <h4 class="font-medium text-gray-700">Owner</h4>
            <p class="text-lg text-gray-900">{{ user.name }}</p>
          </div>
        </div>
      </div>
    </div>

    <!-- Profile Modal -->
    <ProfileModal
      :show="showProfileModal"
      :node="selectedNode"
      @close="showProfileModal = false"
    />
  </div>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue'
import { VueFlow } from '@vue-flow/core'
import { Controls } from '@vue-flow/controls'
import { MiniMap } from '@vue-flow/minimap'
import { Background } from '@vue-flow/background'
import '@vue-flow/core/dist/style.css'
import '@vue-flow/core/dist/theme-default.css'
import ViewerToolbar from '@/Components/FamilyTree/ViewerToolbar.vue'
import NodeComponent from '@/Components/FamilyTree/NodeComponent.vue'
import ProfileModal from '@/Components/FamilyTree/ProfileModal.vue'

const props = defineProps({
  formats: Object,
  user: Object,
})

// State
const elements = ref([])
const currentFormat = ref('custom')
const showProfileModal = ref(false)
const selectedNode = ref(null)
const vueFlowRef = ref(null)

// Computed
const availableFormats = computed(() => {
  if (!props.formats) return []
  return Object.keys(props.formats).map(format => ({
    key: format,
    label: format.charAt(0).toUpperCase() + format.slice(1),
    icon: getFormatIcon(format)
  }))
})

const totalMembers = computed(() => {
  if (!props.formats?.[currentFormat.value]?.nodes) return 0
  return props.formats[currentFormat.value].nodes.length
})

// Methods
const changeFormat = (format) => {
  currentFormat.value = format
  loadFormat(format)
}

const loadFormat = (format) => {
  if (props.formats?.[format]) {
    elements.value = [
      ...props.formats[format].nodes,
      ...props.formats[format].edges
    ]
  }
}

const zoomIn = () => {
  if (vueFlowRef.value) {
    const { zoomIn } = vueFlowRef.value
    zoomIn()
  }
}

const zoomOut = () => {
  if (vueFlowRef.value) {
    const { zoomOut } = vueFlowRef.value
    zoomOut()
  }
}

const fitView = () => {
  if (vueFlowRef.value) {
    const { fitView } = vueFlowRef.value
    fitView()
  }
}

const onNodeClick = (event, node) => {
  selectedNode.value = node
  viewProfile(node)
}

const viewProfile = (node) => {
  selectedNode.value = node
  showProfileModal.value = true
}

const getFormatIcon = (format) => {
  const icons = {
    custom: '🎨',
    vertical: '📊',
    horizontal: '↔️',
    circular: '⭕'
  }
  return icons[format] || '📋'
}

// Initialize
onMounted(() => {
  loadFormat('custom')
})

// Watch for format changes
watch(currentFormat, (newFormat) => {
  loadFormat(newFormat)
})
</script>

<style scoped>
.vue-flow {
  background-color: #f9fafb;
}
</style>