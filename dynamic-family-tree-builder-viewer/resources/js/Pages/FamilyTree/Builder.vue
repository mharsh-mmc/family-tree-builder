<template>
  <AppLayout title="Family Tree Builder">
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        Family Tree Builder
      </h2>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
          <div class="flex h-screen">
            <!-- Left Toolbar -->
            <div class="w-64 bg-gray-50 border-r border-gray-200 p-4">
              <Toolbar
                @add-person="showAddPersonModal = true"
                @connect-nodes="isConnecting = !isConnecting"
                @undo="undo"
                @redo="redo"
                @save="saveTree"
                :can-undo="canUndo"
                :can-redo="canRedo"
                :is-connecting="isConnecting"
              />
            </div>

            <!-- Main Canvas -->
            <div class="flex-1 relative">
              <VueFlow
                v-model="elements"
                :default-viewport="{ zoom: 1 }"
                :min-zoom="0.2"
                :max-zoom="4"
                class="bg-gray-100"
                @node-drag-stop="onNodeDragStop"
                @connect="onConnect"
                @edge-click="onEdgeClick"
                @node-click="onNodeClick"
                @pane-click="onPaneClick"
              >
                <template #node-familyNode="nodeProps">
                  <NodeComponent
                    :node="nodeProps"
                    @edit="editNode"
                    @delete="deleteNode"
                  />
                </template>

                <Controls />
                <MiniMap />
                <Background pattern-color="#aaa" gap="8" />
              </VueFlow>

              <!-- Connection Mode Indicator -->
              <div
                v-if="isConnecting"
                class="absolute top-4 left-1/2 transform -translate-x-1/2 bg-blue-500 text-white px-4 py-2 rounded-lg shadow-lg"
              >
                Click on nodes to connect them
              </div>
            </div>

            <!-- Right Panel -->
            <div class="w-80 bg-gray-50 border-l border-gray-200 p-4">
              <div v-if="selectedNode" class="space-y-4">
                <h3 class="text-lg font-semibold text-gray-900">Node Properties</h3>
                <NodeForm
                  :node="selectedNode"
                  @update="updateNode"
                  @close="selectedNode = null"
                />
              </div>
              <div v-else class="text-center text-gray-500 py-8">
                <p>Select a node to edit its properties</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Add Person Modal -->
    <Modal :show="showAddPersonModal" @close="showAddPersonModal = false">
      <div class="p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Add Family Member</h3>
        <NodeForm
          :is-new="true"
          @create="createNode"
          @close="showAddPersonModal = false"
        />
      </div>
    </Modal>

    <!-- Profile Modal -->
    <ProfileModal
      :show="showProfileModal"
      :node="selectedNode"
      @close="showProfileModal = false"
    />
  </AppLayout>
</template>

<script setup>
import { ref, computed, onMounted, nextTick } from 'vue'
import { useVueFlow, VueFlow } from '@vue-flow/core'
import { Background } from '@vue-flow/background'
import { MiniMap } from '@vue-flow/minimap'
import { Controls } from '@vue-flow/controls'
import '@vue-flow/core/dist/style.css'
import '@vue-flow/core/dist/theme-default.css'
import AppLayout from '@/Layouts/AppLayout.vue'
import Toolbar from '@/Components/FamilyTree/Toolbar.vue'
import NodeComponent from '@/Components/FamilyTree/NodeComponent.vue'
import NodeForm from '@/Components/FamilyTree/NodeForm.vue'
import ProfileModal from '@/Components/FamilyTree/ProfileModal.vue'
import Modal from '@/Components/FamilyTree/Modal.vue'
import { router } from '@inertiajs/vue3'

const props = defineProps({
  formats: Object,
  user: Object,
})

const { addNodes, addEdges, removeNodes, removeEdges, setNodes, setEdges, getNodes, getEdges } = useVueFlow()

// State
const elements = ref([])
const selectedNode = ref(null)
const showAddPersonModal = ref(false)
const showProfileModal = ref(false)
const isConnecting = ref(false)
const connectionSource = ref(null)

// History for undo/redo
const history = ref([])
const historyIndex = ref(-1)

// Computed
const canUndo = computed(() => historyIndex.value > 0)
const canRedo = computed(() => historyIndex.value < history.value.length - 1)

// Initialize tree from props
onMounted(() => {
  if (props.formats?.custom) {
    elements.value = [
      ...props.formats.custom.nodes,
      ...props.formats.custom.edges
    ]
    saveToHistory()
  }
})

// Methods
const saveToHistory = () => {
  const currentState = JSON.stringify(elements.value)
  history.value = history.value.slice(0, historyIndex.value + 1)
  history.value.push(currentState)
  historyIndex.value = history.value.length - 1
}

const undo = () => {
  if (canUndo.value) {
    historyIndex.value--
    elements.value = JSON.parse(history.value[historyIndex.value])
  }
}

const redo = () => {
  if (canRedo.value) {
    historyIndex.value++
    elements.value = JSON.parse(history.value[historyIndex.value])
  }
}

const createNode = async (nodeData) => {
  try {
    const response = await router.post('/family-tree/node', {
      ...nodeData,
      position_x: Math.random() * 400 + 100,
      position_y: Math.random() * 300 + 100,
    })

    if (response.ok) {
      const newNode = {
        id: response.data.node.id,
        type: 'familyNode',
        position: { x: nodeData.position_x, y: nodeData.position_y },
        data: {
          name: response.data.node.name,
          relation: response.data.node.relation,
          profilePic: response.data.node.profile_pic_url,
          dob: response.data.node.dob,
          isAlive: response.data.node.is_alive,
          dod: response.data.node.dod,
          biodata: response.data.node.biodata,
          age: response.data.node.age,
        },
      }

      addNodes(newNode)
      saveToHistory()
      showAddPersonModal.value = false
    }
  } catch (error) {
    console.error('Error creating node:', error)
  }
}

const updateNode = async (nodeData) => {
  try {
    const response = await router.patch(`/family-tree/node/${selectedNode.value.id}`, nodeData)
    
    if (response.ok) {
      const updatedNode = getNodes().value.find(n => n.id === selectedNode.value.id)
      if (updatedNode) {
        Object.assign(updatedNode.data, response.data.node)
        saveToHistory()
      }
    }
  } catch (error) {
    console.error('Error updating node:', error)
  }
}

const deleteNode = async (nodeId) => {
  try {
    const response = await router.delete(`/family-tree/node/${nodeId}`)
    
    if (response.ok) {
      removeNodes(nodeId)
      saveToHistory()
      if (selectedNode.value?.id === nodeId) {
        selectedNode.value = null
      }
    }
  } catch (error) {
    console.error('Error deleting node:', error)
  }
}

const onNodeDragStop = (event, node) => {
  saveToHistory()
}

const onConnect = async (params) => {
  try {
    const response = await router.post('/family-tree/edges', {
      source_node_id: params.source,
      target_node_id: params.target,
      relation_type: 'family',
    })

    if (response.ok) {
      const newEdge = {
        id: response.data.edge.id,
        source: params.source,
        target: params.target,
        type: 'smoothstep',
        data: { relationType: 'family' },
      }

      addEdges(newEdge)
      saveToHistory()
    }
  } catch (error) {
    console.error('Error creating edge:', error)
  }
}

const onEdgeClick = (event, edge) => {
  if (confirm('Delete this relationship?')) {
    deleteEdge(edge.id)
  }
}

const deleteEdge = async (edgeId) => {
  try {
    const response = await router.delete(`/family-tree/edges/${edgeId}`)
    
    if (response.ok) {
      removeEdges(edgeId)
      saveToHistory()
    }
  } catch (error) {
    console.error('Error deleting edge:', error)
  }
}

const onNodeClick = (event, node) => {
  selectedNode.value = node
}

const onPaneClick = () => {
  selectedNode.value = null
}

const editNode = (node) => {
  selectedNode.value = node
}

const saveTree = () => {
  // Tree is automatically saved as changes are made
  alert('Tree saved successfully!')
}
</script>

<style scoped>
.vue-flow {
  background-color: #f8fafc;
}
</style>