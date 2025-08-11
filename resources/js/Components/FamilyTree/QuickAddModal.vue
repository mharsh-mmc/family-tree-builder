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
            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md sm:w-full">
                <!-- Header -->
                <div class="bg-primary-600 px-6 py-4">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-medium text-white">
                            Quick Add Member
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
                            <label for="name" class="form-label">Full Name *</label>
                            <input
                                id="name"
                                v-model="form.name"
                                type="text"
                                required
                                class="form-input"
                                placeholder="Enter full name"
                            />
                        </div>
                        
                        <div>
                            <label for="relation" class="form-label">Relation</label>
                            <select
                                id="relation"
                                v-model="form.relation"
                                class="form-input"
                            >
                                <option value="">Select relation</option>
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
                        </div>
                        
                        <div>
                            <label for="dob" class="form-label">Date of Birth</label>
                            <input
                                id="dob"
                                v-model="form.dob"
                                type="date"
                                class="form-input"
                            />
                        </div>
                        
                        <div>
                            <label class="form-label">Status</label>
                            <div class="flex items-center space-x-4 mt-2">
                                <label class="flex items-center">
                                    <input
                                        v-model="form.is_alive"
                                        type="radio"
                                        :value="true"
                                        class="mr-2"
                                    />
                                    Living
                                </label>
                                <label class="flex items-center">
                                    <input
                                        v-model="form.is_alive"
                                        type="radio"
                                        :value="false"
                                        class="mr-2"
                                    />
                                    Deceased
                                </label>
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
                        :disabled="form.processing"
                        class="btn-primary"
                    >
                        <span v-if="form.processing">Adding...</span>
                        <span v-else>Add Member</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { useForm } from '@inertiajs/vue3';

const props = defineProps({
    show: Boolean,
});

const emit = defineEmits(['close', 'member-added']);

const form = useForm({
    name: '',
    relation: '',
    dob: '',
    is_alive: true,
});

const closeModal = () => {
    emit('close');
    form.reset();
    form.clearErrors();
};

const submitForm = async () => {
    try {
        await form.post(route('family-tree.nodes.store'), {
            onSuccess: () => {
                emit('member-added', form.data());
                closeModal();
            },
        });
    } catch (error) {
        console.error('Error adding member:', error);
    }
};
</script>