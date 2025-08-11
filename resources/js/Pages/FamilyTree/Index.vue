<template>
    <AppLayout title="Family Tree">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Family Tree Builder & Viewer
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                    <!-- Welcome Section -->
                    <div class="bg-gradient-to-r from-primary-600 to-primary-700 px-6 py-8 text-white">
                        <div class="max-w-3xl">
                            <h1 class="text-3xl font-bold mb-4">
                                Build Your Family Tree
                            </h1>
                            <p class="text-primary-100 text-lg leading-relaxed">
                                Create, visualize, and explore your family connections with our interactive family tree builder. 
                                Add family members, establish relationships, and discover your family's story.
                            </p>
                        </div>
                    </div>

                    <!-- Stats Section -->
                    <div class="bg-gray-50 px-6 py-6 border-b border-gray-200">
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                            <div class="text-center">
                                <div class="text-2xl font-bold text-primary-600">{{ stats.totalMembers }}</div>
                                <div class="text-sm text-gray-600">Total Members</div>
                            </div>
                            <div class="text-center">
                                <div class="text-2xl font-bold text-green-600">{{ stats.livingMembers }}</div>
                                <div class="text-sm text-gray-600">Living Members</div>
                            </div>
                            <div class="text-center">
                                <div class="text-2xl font-bold text-blue-600">{{ stats.totalConnections }}</div>
                                <div class="text-sm text-gray-600">Family Connections</div>
                            </div>
                            <div class="text-center">
                                <div class="text-2xl font-bold text-purple-600">{{ stats.generations }}</div>
                                <div class="text-sm text-gray-600">Generations</div>
                            </div>
                        </div>
                    </div>

                    <!-- Main Content -->
                    <div class="p-6">
                        <!-- Quick Actions -->
                        <div class="mb-6 flex flex-wrap items-center gap-3">
                            <button
                                @click="showQuickAddModal = true"
                                class="btn-primary"
                            >
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                                Quick Add Member
                            </button>
                            
                            <button
                                @click="exportTree"
                                class="btn-outline"
                            >
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                Export Tree
                            </button>
                            
                            <button
                                @click="importTree"
                                class="btn-outline"
                            >
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                                </svg>
                                Import Tree
                            </button>
                            
                            <button
                                @click="resetTree"
                                class="btn-outline text-red-600 hover:text-red-700 hover:border-red-300"
                            >
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                </svg>
                                Reset Tree
                            </button>
                        </div>

                        <!-- Family Tree Viewer -->
                        <div class="bg-white border border-gray-200 rounded-lg overflow-hidden" style="height: 600px;">
                            <FamilyTreeViewer
                                :initial-nodes="initialNodes"
                                :initial-edges="initialEdges"
                                :errors="errors"
                                @update="handleTreeUpdate"
                            />
                        </div>

                        <!-- Instructions -->
                        <div class="mt-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
                            <h3 class="text-lg font-medium text-blue-800 mb-2">How to Use</h3>
                            <div class="text-sm text-blue-700 space-y-1">
                                <p>• <strong>Click</strong> on a family member to view their profile</p>
                                <p>• <strong>Drag</strong> nodes to reposition them on the canvas</p>
                                <p>• <strong>Double-click</strong> to edit member details</p>
                                <p>• <strong>Use the toolbar</strong> to add new members and connections</p>
                                <p>• <strong>Zoom and pan</strong> to explore large family trees</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Add Modal -->
        <QuickAddModal
            :show="showQuickAddModal"
            @close="showQuickAddModal = false"
            @member-added="handleQuickAddMember"
        />

        <!-- Import Modal -->
        <ImportModal
            :show="showImportModal"
            @close="showImportModal = false"
            @tree-imported="handleTreeImported"
        />
    </AppLayout>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { Head } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import FamilyTreeViewer from '@/Components/FamilyTree/FamilyTreeViewer.vue';
import QuickAddModal from '@/Components/FamilyTree/QuickAddModal.vue';
import ImportModal from '@/Components/FamilyTree/ImportModal.vue';

const props = defineProps({
    nodes: {
        type: Array,
        default: () => []
    },
    edges: {
        type: Array,
        default: () => []
    },
    stats: {
        type: Object,
        default: () => ({
            totalMembers: 0,
            livingMembers: 0,
            totalConnections: 0,
            generations: 0
        })
    },
    errors: {
        type: Object,
        default: () => ({})
    }
});

// Modal states
const showQuickAddModal = ref(false);
const showImportModal = ref(false);

// Convert backend data to VueFlow format
const initialNodes = computed(() => {
    return props.nodes.map(node => ({
        id: node.id.toString(),
        type: 'custom',
        position: {
            x: node.position_x || 0,
            y: node.position_y || 0
        },
        data: {
            id: node.id,
            name: node.name,
            relation: node.relation,
            dob: node.dob,
            dod: node.dod,
            is_alive: node.is_alive,
            biodata: node.biodata,
            profile_pic: node.profile_pic,
            position_x: node.position_x,
            position_y: node.position_y
        }
    }));
});

const initialEdges = computed(() => {
    return props.edges.map(edge => ({
        id: edge.id.toString(),
        source: edge.source_node_id.toString(),
        target: edge.target_node_id.toString(),
        type: 'custom',
        data: {
            id: edge.id,
            relation_type: edge.relation_type,
            description: edge.description
        }
    }));
});

// Event handlers
const handleTreeUpdate = (treeData) => {
    // Update tree data in backend
    console.log('Tree updated:', treeData);
};

const handleQuickAddMember = (member) => {
    // Handle quick add member
    console.log('Quick add member:', member);
};

const handleTreeImported = (treeData) => {
    // Handle tree import
    console.log('Tree imported:', treeData);
    window.location.reload();
};

// Actions
const exportTree = () => {
    const treeData = {
        nodes: props.nodes,
        edges: props.edges,
        exported_at: new Date().toISOString()
    };
    
    const dataStr = JSON.stringify(treeData, null, 2);
    const dataUri = 'data:application/json;charset=utf-8,'+ encodeURIComponent(dataStr);
    
    const exportFileDefaultName = `family-tree-${new Date().toISOString().split('T')[0]}.json`;
    
    const linkElement = document.createElement('a');
    linkElement.setAttribute('href', dataUri);
    linkElement.setAttribute('download', exportFileDefaultName);
    linkElement.click();
};

const importTree = () => {
    showImportModal.value = true;
};

const resetTree = () => {
    if (confirm('Are you sure you want to reset the entire family tree? This action cannot be undone.')) {
        // Call backend to reset tree
        console.log('Resetting tree...');
    }
};
</script>