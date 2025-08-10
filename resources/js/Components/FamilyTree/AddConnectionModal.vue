<template>
    <div
        v-if="show"
        class="fixed inset-0 z-50 overflow-y-auto"
        @click.self="closeModal"
    >
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <!-- Background overlay -->
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>

            <!-- Modal panel -->
            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <!-- Header -->
                <div class="bg-primary-600 px-6 py-4">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-medium text-white">
                            Add Family Connection
                        </h3>
                        <button
                            @click="closeModal"
                            class="text-white hover:text-gray-200 transition-colors"
                        >
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Content -->
                <form @submit.prevent="submitForm" class="px-6 py-6">
                    <div class="space-y-4">
                        <div>
                            <label for="source" class="form-label">From Member *</label>
                            <select
                                id="source"
                                v-model="form.source"
                                required
                                class="form-input"
                                :class="{ 'border-red-500': form.errors.source }"
                            >
                                <option value="">Select source member</option>
                                <option
                                    v-for="node in availableNodes"
                                    :key="node.id"
                                    :value="node.id"
                                >
                                    {{ node.data.name }} ({{ node.data.relation }})
                                </option>
                            </select>
                            <div v-if="form.errors.source" class="form-error">{{ form.errors.source }}</div>
                        </div>
                        
                        <div>
                            <label for="target" class="form-label">To Member *</label>
                            <select
                                id="target"
                                v-model="form.target"
                                required
                                class="form-input"
                                :class="{ 'border-red-500': form.errors.target }"
                            >
                                <option value="">Select target member</option>
                                <option
                                    v-for="node in availableTargetNodes"
                                    :key="node.id"
                                    :value="node.id"
                                >
                                    {{ node.data.name }} ({{ node.data.relation }})
                                </option>
                            </select>
                            <div v-if="form.errors.target" class="form-error">{{ form.errors.target }}</div>
                        </div>
                        
                        <div>
                            <label for="relation_type" class="form-label">Relationship Type</label>
                            <select
                                id="relation_type"
                                v-model="form.relation_type"
                                class="form-input"
                            >
                                <option value="">Select relationship type</option>
                                <option value="parent">Parent</option>
                                <option value="child">Child</option>
                                <option value="spouse">Spouse</option>
                                <option value="sibling">Sibling</option>
                                <option value="grandparent">Grandparent</option>
                                <option value="grandchild">Grandchild</option>
                                <option value="aunt">Aunt</option>
                                <option value="uncle">Uncle</option>
                                <option value="cousin">Cousin</option>
                                <option value="niece">Niece</option>
                                <option value="nephew">Nephew</option>
                                <option value="in-law">In-Law</option>
                                <option value="step">Step</option>
                                <option value="adopted">Adopted</option>
                                <option value="foster">Foster</option>
                            </select>
                            <div v-if="form.errors.relation_type" class="form-error">{{ form.errors.relation_type }}</div>
                        </div>
                        
                        <div>
                            <label for="description" class="form-label">Description (Optional)</label>
                            <textarea
                                id="description"
                                v-model="form.description"
                                rows="3"
                                class="form-input"
                                placeholder="Add any additional details about this connection..."
                            ></textarea>
                            <div v-if="form.errors.description" class="form-error">{{ form.errors.description }}</div>
                        </div>
                        
                        <!-- Validation warning -->
                        <div v-if="showValidationWarning" class="bg-yellow-50 border border-yellow-200 rounded-md p-3">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <h3 class="text-sm font-medium text-yellow-800">
                                        Connection Validation
                                    </h3>
                                    <div class="mt-2 text-sm text-yellow-700">
                                        <p>{{ validationMessage }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>

                <!-- Footer -->
                <div class="bg-gray-50 px-6 py-4 flex justify-end space-x-3">
                    <button
                        @click="closeModal"
                        class="btn-outline"
                    >
                        Cancel
                    </button>
                    <button
                        @click="submitForm"
                        :disabled="form.processing || !isValidConnection"
                        class="btn-primary"
                    >
                        <span v-if="form.processing">Adding...</span>
                        <span v-else>Add Connection</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { computed, ref, watch } from 'vue';
import { useForm } from '@inertiajs/vue3';

const props = defineProps({
    show: Boolean,
    nodes: {
        type: Array,
        default: () => []
    }
});

const emit = defineEmits(['close', 'connection-added']);

const form = useForm({
    source: '',
    target: '',
    relation_type: '',
    description: '',
});

const showValidationWarning = ref(false);
const validationMessage = ref('');

// Computed properties
const availableNodes = computed(() => props.nodes);

const availableTargetNodes = computed(() => {
    if (!form.source) return props.nodes;
    return props.nodes.filter(node => node.id !== form.source);
});

const isValidConnection = computed(() => {
    return form.source && form.target && form.source !== form.target;
});

// Watch for form changes to validate
watch([() => form.source, () => form.target, () => form.relation_type], () => {
    validateConnection();
});

const validateConnection = () => {
    if (!form.source || !form.target || form.source === form.target) {
        showValidationWarning.value = false;
        return;
    }

    const sourceNode = props.nodes.find(n => n.id === form.source);
    const targetNode = props.nodes.find(n => n.id === form.target);
    
    if (!sourceNode || !targetNode) {
        showValidationWarning.value = false;
        return;
    }

    // Check for duplicate connections
    // This would need to be implemented based on your edge data structure
    
    // Check for logical relationship conflicts
    let warning = '';
    
    if (form.relation_type === 'spouse') {
        if (sourceNode.data.relation === 'child' && targetNode.data.relation === 'child') {
            warning = 'Connecting two children as spouses may not be logical.';
        }
    } else if (form.relation_type === 'parent') {
        if (sourceNode.data.relation === 'parent' && targetNode.data.relation === 'parent') {
            warning = 'Connecting two parents may create circular relationships.';
        }
    } else if (form.relation_type === 'sibling') {
        if (sourceNode.data.relation === 'parent' && targetNode.data.relation === 'child') {
            warning = 'A parent and child cannot be siblings.';
        }
    }
    
    if (warning) {
        showValidationWarning.value = true;
        validationMessage.value = warning;
    } else {
        showValidationWarning.value = false;
    }
};

const closeModal = () => {
    emit('close');
    form.reset();
    form.clearErrors();
    showValidationWarning.value = false;
};

const submitForm = async () => {
    if (!isValidConnection.value) {
        return;
    }

    try {
        await form.post(route('family-tree.edges.store'), {
            onSuccess: () => {
                emit('connection-added', form.data());
                closeModal();
            },
        });
    } catch (error) {
        console.error('Error adding connection:', error);
    }
};
</script>