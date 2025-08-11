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
                            Import Family Tree
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
                <div class="px-6 py-6">
                    <div class="space-y-4">
                        <div>
                            <label for="tree_data" class="form-label">Tree Data (JSON)</label>
                            <textarea
                                id="tree_data"
                                v-model="treeData"
                                rows="8"
                                class="form-input font-mono text-sm"
                                placeholder="Paste your family tree JSON data here..."
                            ></textarea>
                            <div class="text-sm text-gray-500 mt-1">
                                Paste the exported JSON data from another family tree.
                            </div>
                        </div>
                        
                        <!-- File Upload Alternative -->
                        <div class="border-t pt-4">
                            <label class="form-label">Or upload a JSON file</label>
                            <div class="mt-2">
                                <input
                                    ref="fileInput"
                                    type="file"
                                    accept=".json"
                                    @change="handleFileUpload"
                                    class="hidden"
                                />
                                <button
                                    @click="$refs.fileInput.click()"
                                    type="button"
                                    class="btn-outline w-full"
                                >
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                                    </svg>
                                    Choose File
                                </button>
                            </div>
                            <div v-if="selectedFile" class="text-sm text-gray-600 mt-2">
                                Selected: {{ selectedFile.name }}
                            </div>
                        </div>
                        
                        <!-- Validation -->
                        <div v-if="validationError" class="bg-red-50 border border-red-200 rounded-md p-3">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <h3 class="text-sm font-medium text-red-800">
                                        Validation Error
                                    </h3>
                                    <div class="mt-2 text-sm text-red-700">
                                        <p>{{ validationError }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Preview -->
                        <div v-if="previewData" class="bg-blue-50 border border-blue-200 rounded-md p-3">
                            <h4 class="text-sm font-medium text-blue-800 mb-2">Preview</h4>
                            <div class="text-sm text-blue-700 space-y-1">
                                <p>• {{ previewData.nodes?.length || 0 }} family members</p>
                                <p>• {{ previewData.edges?.length || 0 }} family connections</p>
                                <p v-if="previewData.exported_at">• Exported: {{ formatDate(previewData.exported_at) }}</p>
                            </div>
                        </div>
                        
                        <!-- Warning -->
                        <div class="bg-yellow-50 border border-yellow-200 rounded-md p-3">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <h3 class="text-sm font-medium text-yellow-800">
                                        Warning
                                    </h3>
                                    <div class="mt-2 text-sm text-yellow-700">
                                        <p>Importing will replace your current family tree data. This action cannot be undone.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Footer -->
                <div class="bg-gray-50 px-6 py-4 flex justify-end space-x-3">
                    <button
                        @click="closeModal"
                        class="btn-outline"
                    >
                        Cancel
                    </button>
                    <button
                        @click="importTree"
                        :disabled="!isValidData || form.processing"
                        class="btn-primary"
                    >
                        <span v-if="form.processing">Importing...</span>
                        <span v-else>Import Tree</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, watch } from 'vue';
import { useForm } from '@inertiajs/vue3';

const props = defineProps({
    show: Boolean,
});

const emit = defineEmits(['close', 'tree-imported']);

const form = useForm({
    tree_data: '',
});

const treeData = ref('');
const selectedFile = ref(null);
const validationError = ref('');
const previewData = ref(null);

// Computed properties
const isValidData = computed(() => {
    return treeData.value.trim() !== '' && !validationError.value;
});

// Watch for changes in tree data
watch(treeData, (newValue) => {
    validateAndPreview(newValue);
});

const validateAndPreview = (data) => {
    validationError.value = '';
    previewData.value = null;
    
    if (!data.trim()) {
        return;
    }
    
    try {
        const parsed = JSON.parse(data);
        
        if (!parsed || typeof parsed !== 'object') {
            throw new Error('Invalid JSON format');
        }
        
        if (!Array.isArray(parsed.nodes)) {
            throw new Error('Missing or invalid nodes array');
        }
        
        if (!Array.isArray(parsed.edges)) {
            throw new Error('Missing or invalid edges array');
        }
        
        // Basic validation passed
        previewData.value = parsed;
        form.tree_data = data;
        
    } catch (error) {
        validationError.value = `Invalid JSON: ${error.message}`;
    }
};

const handleFileUpload = (event) => {
    const file = event.target.files[0];
    if (!file) return;
    
    selectedFile.value = file;
    
    const reader = new FileReader();
    reader.onload = (e) => {
        treeData.value = e.target.result;
    };
    reader.readAsText(file);
};

const importTree = async () => {
    if (!isValidData.value) {
        return;
    }
    
    try {
        await form.post(route('family-tree.import'), {
            onSuccess: () => {
                emit('tree-imported', previewData.value);
                closeModal();
            },
        });
    } catch (error) {
        console.error('Error importing tree:', error);
    }
};

const closeModal = () => {
    emit('close');
    treeData.value = '';
    selectedFile.value = null;
    validationError.value = '';
    previewData.value = null;
    form.reset();
    form.clearErrors();
};

const formatDate = (dateString) => {
    try {
        return new Date(dateString).toLocaleDateString();
    } catch {
        return 'Unknown';
    }
};
</script>