<template>
    <div class="family-tree-viewer h-full">
        <!-- Toolbar -->
        <div class="bg-white border-b border-gray-200 px-4 py-3 flex items-center justify-between">
            <div class="flex items-center space-x-4">
                <h2 class="text-xl font-semibold text-gray-900">Family Tree</h2>
                <div class="flex items-center space-x-2">
                    <button
                        @click="addNewMember"
                        class="btn-primary"
                    >
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        Add Member
                    </button>
                    <button
                        @click="addConnection"
                        class="btn-outline"
                    >
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path>
                        </svg>
                        Add Connection
                    </button>
                </div>
            </div>
            
            <div class="flex items-center space-x-3">
                <button
                    @click="zoomIn"
                    class="btn-outline"
                    title="Zoom In"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7"></path>
                    </svg>
                </button>
                <button
                    @click="zoomOut"
                    class="btn-outline"
                    title="Zoom Out"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 10h3m-3 0H7"></path>
                    </svg>
                </button>
                <button
                    @click="fitView"
                    class="btn-outline"
                    title="Fit View"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"></path>
                    </svg>
                </button>
                <button
                    @click="centerView"
                    class="btn-outline"
                    title="Center View"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                </button>
            </div>
        </div>

        <!-- VueFlow Canvas -->
        <div class="flex-1 relative">
            <VueFlow
                v-model="elements"
                :default-viewport="{ zoom: 1 }"
                :min-zoom="0.1"
                :max-zoom="4"
                :node-types="nodeTypes"
                :edge-types="edgeTypes"
                :connection-line-style="{ stroke: '#3b82f6', strokeWidth: 2 }"
                :connection-mode="ConnectionMode.Loose"
                :snap-to-grid="true"
                :snap-grid="[20, 20]"
                @node-click="handleNodeClick"
                @node-double-click="handleNodeDoubleClick"
                @edge-click="handleEdgeClick"
                @connect="handleConnect"
                @pane-click="handlePaneClick"
                @node-drag-stop="handleNodeDragStop"
                class="bg-gray-50"
            >
                <template #node-custom="nodeProps">
                    <NodeComponent
                        v-bind="nodeProps"
                        @edit-node="handleEditNode"
                        @delete-node="handleDeleteNode"
                    />
                </template>
                
                <template #edge-custom="edgeProps">
                    <EdgeComponent
                        v-bind="edgeProps"
                        @edge-click="handleEdgeClick"
                        @edge-delete="handleDeleteEdge"
                    />
                </template>

                <!-- Controls -->
                <Controls />
                <MiniMap />
                <Background />
                <Panel position="top-left" class="bg-white rounded-lg shadow-md p-2">
                    <div class="text-xs text-gray-600">
                        <div>Zoom: {{ Math.round(viewport.zoom * 100) }}%</div>
                        <div>Members: {{ nodes.length }}</div>
                        <div>Connections: {{ edges.length }}</div>
                    </div>
                </Panel>
            </VueFlow>
        </div>

        <!-- Profile Modal -->
        <ProfileModal
            :show="showProfileModal"
            :member="selectedMember"
            :errors="errors"
            @close="closeProfileModal"
            @update="handleMemberUpdate"
        />

        <!-- Add Member Modal -->
        <AddMemberModal
            :show="showAddMemberModal"
            @close="closeAddMemberModal"
            @member-added="handleMemberAdded"
        />

        <!-- Add Connection Modal -->
        <AddConnectionModal
            :show="showAddConnectionModal"
            :nodes="nodes"
            @close="closeAddConnectionModal"
            @connection-added="handleConnectionAdded"
        />
    </div>
</template>

<script setup>
import { ref, computed, onMounted, nextTick } from 'vue';
import { VueFlow, useVueFlow, ConnectionMode, Controls, MiniMap, Background, Panel } from '@vueflow/core';
import { useForm } from '@inertiajs/vue3';
import NodeComponent from './NodeComponent.vue';
import EdgeComponent from './EdgeComponent.vue';
import ProfileModal from './ProfileModal.vue';
import AddMemberModal from './AddMemberModal.vue';
import AddConnectionModal from './AddConnectionModal.vue';
import '@vueflow/core/dist/style.css';
import '@vueflow/core/dist/theme-default.css';

const props = defineProps({
    initialNodes: {
        type: Array,
        default: () => []
    },
    initialEdges: {
        type: Array,
        default: () => []
    },
    errors: {
        type: Object,
        default: () => ({})
    }
});

const emit = defineEmits(['update']);

// VueFlow setup
const {
    nodes,
    edges,
    viewport,
    addNodes,
    addEdges,
    removeNodes,
    removeEdges,
    updateNode,
    setViewport,
    fitView: vueFlowFitView,
    zoomIn: vueFlowZoomIn,
    zoomOut: vueFlowZoomOut,
} = useVueFlow();

// Modal states
const showProfileModal = ref(false);
const showAddMemberModal = ref(false);
const showAddConnectionModal = ref(false);
const selectedMember = ref(null);

// Node and edge types
const nodeTypes = {
    custom: NodeComponent
};

const edgeTypes = {
    custom: EdgeComponent
};

// Initialize elements
const elements = computed({
    get: () => [...nodes.value, ...edges.value],
    set: (newElements) => {
        const newNodes = newElements.filter(el => el.type !== 'edge');
        const newEdges = newElements.filter(el => el.type === 'edge');
        nodes.value = newNodes;
        edges.value = newEdges;
    }
});

// Lifecycle
onMounted(() => {
    if (props.initialNodes.length > 0) {
        addNodes(props.initialNodes);
    }
    if (props.initialEdges.length > 0) {
        addEdges(props.initialEdges);
    }
    
    // Center view after nodes are added
    nextTick(() => {
        if (nodes.value.length > 0) {
            centerView();
        }
    });
});

// Event handlers
const handleNodeClick = (event, node) => {
    selectedMember.value = node.data;
    showProfileModal.value = true;
};

const handleNodeDoubleClick = (event, node) => {
    // Handle double-click if needed
};

const handleEdgeClick = (event, edge) => {
    // Handle edge click if needed
};

const handleConnect = (connection) => {
    const newEdge = {
        id: `edge-${Date.now()}`,
        source: connection.source,
        target: connection.target,
        type: 'custom',
        data: {
            relation_type: 'family'
        }
    };
    
    addEdges([newEdge]);
    emit('update', { nodes: nodes.value, edges: edges.value });
};

const handlePaneClick = () => {
    // Close modals when clicking on empty space
    showProfileModal.value = false;
    showAddMemberModal.value = false;
    showAddConnectionModal.value = false;
    selectedMember.value = null;
};

const handleNodeDragStop = (event, node) => {
    // Update node position in database
    updateNode(node.id, {
        position: node.position
    });
    emit('update', { nodes: nodes.value, edges: edges.value });
};

const handleEditNode = (nodeId) => {
    const node = nodes.value.find(n => n.id === nodeId);
    if (node) {
        selectedMember.value = node.data;
        showProfileModal.value = true;
    }
};

const handleDeleteNode = (nodeId) => {
    if (confirm('Are you sure you want to delete this family member? This will also remove all connections.')) {
        // Remove connected edges first
        const connectedEdges = edges.value.filter(edge => 
            edge.source === nodeId || edge.target === nodeId
        );
        removeEdges(connectedEdges.map(edge => edge.id));
        
        // Remove node
        removeNodes([nodeId]);
        emit('update', { nodes: nodes.value, edges: edges.value });
    }
};

const handleDeleteEdge = (edgeId) => {
    if (confirm('Are you sure you want to delete this connection?')) {
        removeEdges([edgeId]);
        emit('update', { nodes: nodes.value, edges: edges.value });
    }
};

const handleMemberUpdate = (updatedData) => {
    // Update node data
    const node = nodes.value.find(n => n.id === selectedMember.value.id);
    if (node) {
        updateNode(node.id, {
            data: { ...node.data, ...updatedData }
        });
        emit('update', { nodes: nodes.value, edges: edges.value });
    }
    closeProfileModal();
};

const handleMemberAdded = (newMember) => {
    const newNode = {
        id: `node-${Date.now()}`,
        type: 'custom',
        position: { x: 100, y: 100 },
        data: newMember
    };
    
    addNodes([newNode]);
    emit('update', { nodes: nodes.value, edges: edges.value });
    closeAddMemberModal();
};

const handleConnectionAdded = (connection) => {
    const newEdge = {
        id: `edge-${Date.now()}`,
        source: connection.source,
        target: connection.target,
        type: 'custom',
        data: {
            relation_type: connection.relation_type
        }
    };
    
    addEdges([newEdge]);
    emit('update', { nodes: nodes.value, edges: edges.value });
    closeAddConnectionModal();
};

// Modal controls
const addNewMember = () => {
    showAddMemberModal.value = true;
};

const addConnection = () => {
    showAddConnectionModal.value = true;
};

const closeProfileModal = () => {
    showProfileModal.value = false;
    selectedMember.value = null;
};

const closeAddMemberModal = () => {
    showAddMemberModal.value = false;
};

const closeAddConnectionModal = () => {
    showAddConnectionModal.value = false;
};

// View controls
const zoomIn = () => {
    vueFlowZoomIn();
};

const zoomOut = () => {
    vueFlowZoomOut();
};

const fitView = () => {
    vueFlowFitView();
};

const centerView = () => {
    if (nodes.value.length > 0) {
        const centerX = nodes.value.reduce((sum, node) => sum + node.position.x, 0) / nodes.value.length;
        const centerY = nodes.value.reduce((sum, node) => sum + node.position.y, 0) / nodes.value.length;
        
        setViewport({
            x: -centerX + window.innerWidth / 2,
            y: -centerY + window.innerHeight / 2,
            zoom: 1
        });
    }
};
</script>

<style scoped>
.family-tree-viewer {
    @apply flex flex-col;
}

:deep(.vue-flow__node) {
    @apply cursor-pointer;
}

:deep(.vue-flow__edge) {
    @apply cursor-pointer;
}

:deep(.vue-flow__controls) {
    @apply shadow-lg;
}

:deep(.vue-flow__minimap) {
    @apply shadow-lg;
}

:deep(.vue-flow__background) {
    @apply bg-gray-100;
}
</style>