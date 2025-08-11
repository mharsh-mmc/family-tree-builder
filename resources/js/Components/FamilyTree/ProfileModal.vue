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
            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full">
                <!-- Header -->
                <div class="bg-primary-600 px-6 py-4">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-medium text-white">
                            {{ isEditing ? 'Edit Family Member' : 'Family Member Profile' }}
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
                    <div v-if="!isEditing" class="space-y-6">
                        <!-- Profile Picture -->
                        <div class="text-center">
                            <div class="relative inline-block">
                                <img
                                    v-if="member.profile_pic"
                                    :src="member.profile_pic"
                                    :alt="member.name"
                                    class="w-32 h-32 rounded-full object-cover border-4 border-gray-200"
                                />
                                <div
                                    v-else
                                    class="w-32 h-32 rounded-full bg-gradient-to-br from-primary-400 to-primary-600 flex items-center justify-center text-white font-bold text-4xl border-4 border-gray-200"
                                >
                                    {{ member.name?.charAt(0).toUpperCase() }}
                                </div>
                                <div
                                    v-if="!member.is_alive"
                                    class="absolute -top-2 -right-2 w-8 h-8 bg-gray-600 rounded-full flex items-center justify-center"
                                >
                                    <span class="text-white text-sm">†</span>
                                </div>
                            </div>
                        </div>

                        <!-- Basic Information -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Full Name</label>
                                <p class="text-lg text-gray-900">{{ member.name || 'Not specified' }}</p>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                                <span
                                    :class="[
                                        'inline-flex items-center px-3 py-1 rounded-full text-sm font-medium',
                                        member.is_alive
                                            ? 'bg-green-100 text-green-800'
                                            : 'bg-gray-100 text-gray-800'
                                    ]"
                                >
                                    {{ member.is_alive ? 'Living' : 'Deceased' }}
                                </span>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Date of Birth</label>
                                <p class="text-lg text-gray-900">
                                    {{ member.dob ? formatDate(member.dob) : 'Not specified' }}
                                </p>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Age</label>
                                <p class="text-lg text-gray-900">
                                    {{ formatAge(member.dob, member.dod) || 'Not specified' }} years
                                </p>
                            </div>
                            
                            <div v-if="!member.is_alive">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Date of Death</label>
                                <p class="text-lg text-gray-900">
                                    {{ member.dod ? formatDate(member.dod) : 'Not specified' }}
                                </p>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Relation</label>
                                <span
                                    :class="[
                                        'inline-flex items-center px-3 py-1 rounded-full text-sm font-medium',
                                        getRelationColor(member.relation)
                                    ]"
                                >
                                    {{ member.relation || 'Not specified' }}
                                </span>
                            </div>
                        </div>

                        <!-- Biography -->
                        <div v-if="member.biodata">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Biography</label>
                            <div class="bg-gray-50 rounded-lg p-4">
                                <p class="text-gray-900 whitespace-pre-wrap">{{ member.biodata }}</p>
                            </div>
                        </div>

                        <!-- Position Information -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Position X</label>
                                <p class="text-lg text-gray-900">{{ member.position_x || '0' }}</p>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Position Y</label>
                                <p class="text-lg text-gray-900">{{ member.position_y || '0' }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Edit Form -->
                    <form v-else @submit.prevent="saveMember" class="space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="name" class="form-label">Full Name *</label>
                                <input
                                    id="name"
                                    v-model="editForm.name"
                                    type="text"
                                    required
                                    class="form-input"
                                    :class="{ 'border-red-500': errors.name }"
                                    placeholder="Enter full name"
                                />
                                <div v-if="errors.name" class="form-error">{{ errors.name }}</div>
                            </div>
                            
                            <div>
                                <label for="relation" class="form-label">Relation</label>
                                <select
                                    id="relation"
                                    v-model="editForm.relation"
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
                                    v-model="editForm.dob"
                                    type="date"
                                    class="form-input"
                                />
                            </div>
                            
                            <div>
                                <label for="is_alive" class="form-label">Status</label>
                                <div class="flex items-center space-x-4">
                                    <label class="flex items-center">
                                        <input
                                            v-model="editForm.is_alive"
                                            type="radio"
                                            :value="true"
                                            class="mr-2"
                                        />
                                        Living
                                    </label>
                                    <label class="flex items-center">
                                        <input
                                            v-model="editForm.is_alive"
                                            type="radio"
                                            :value="false"
                                            class="mr-2"
                                        />
                                        Deceased
                                    </label>
                                </div>
                            </div>
                            
                            <div v-if="!editForm.is_alive">
                                <label for="dod" class="form-label">Date of Death</label>
                                <input
                                    id="dod"
                                    v-model="editForm.dod"
                                    type="date"
                                    class="form-input"
                                />
                            </div>
                            
                            <div class="md:col-span-2">
                                <label for="biodata" class="form-label">Biography</label>
                                <textarea
                                    id="biodata"
                                    v-model="editForm.biodata"
                                    rows="4"
                                    class="form-input"
                                    placeholder="Tell us about this family member..."
                                ></textarea>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Footer -->
                <div class="bg-gray-50 px-6 py-4 flex justify-end space-x-3">
                    <button
                        v-if="!isEditing"
                        @click="startEditing"
                        class="btn-primary"
                    >
                        Edit Profile
                    </button>
                    
                    <template v-else>
                        <button
                            @click="cancelEditing"
                            class="btn-outline"
                        >
                            Cancel
                        </button>
                        <button
                            @click="saveMember"
                            :disabled="saving"
                            class="btn-primary"
                        >
                            <span v-if="saving">Saving...</span>
                            <span v-else>Save Changes</span>
                        </button>
                    </template>
                    
                    <button
                        @click="closeModal"
                        class="btn-outline"
                    >
                        Close
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
    member: Object,
    errors: Object,
});

const emit = defineEmits(['close', 'update']);

const isEditing = ref(false);
const saving = ref(false);

const editForm = useForm({
    name: '',
    relation: '',
    dob: '',
    is_alive: true,
    dod: '',
    biodata: '',
    position_x: 0,
    position_y: 0,
});

// Watch for member changes and update form
watch(() => props.member, (newMember) => {
    if (newMember) {
        editForm.name = newMember.name || '';
        editForm.relation = newMember.relation || '';
        editForm.dob = newMember.dob || '';
        editForm.is_alive = newMember.is_alive !== undefined ? newMember.is_alive : true;
        editForm.dod = newMember.dod || '';
        editForm.biodata = newMember.biodata || '';
        editForm.position_x = newMember.position_x || 0;
        editForm.position_y = newMember.position_y || 0;
    }
}, { immediate: true });

const closeModal = () => {
    emit('close');
    isEditing.value = false;
};

const startEditing = () => {
    isEditing.value = true;
};

const cancelEditing = () => {
    isEditing.value = false;
    // Reset form to original values
    if (props.member) {
        editForm.name = props.member.name || '';
        editForm.relation = props.member.relation || '';
        editForm.dob = props.member.dob || '';
        editForm.is_alive = props.member.is_alive !== undefined ? props.member.is_alive : true;
        editForm.dod = props.member.dod || '';
        editForm.biodata = props.member.biodata || '';
        editForm.position_x = props.member.position_x || 0;
        editForm.position_y = props.member.position_y || 0;
    }
};

const saveMember = async () => {
    saving.value = true;
    
    try {
        await editForm.patch(route('family-tree.nodes.update', props.member.id), {
            onSuccess: () => {
                emit('update', editForm.data());
                isEditing.value = false;
            },
            onFinish: () => {
                saving.value = false;
            },
        });
    } catch (error) {
        saving.value = false;
        console.error('Error saving member:', error);
    }
};

const formatDate = (date) => {
    if (!date) return '';
    return new Date(date).toLocaleDateString();
};

const formatAge = (birthDate, deathDate = null) => {
    if (!birthDate) return '';
    
    const birth = new Date(birthDate);
    const end = deathDate ? new Date(deathDate) : new Date();
    const age = end.getFullYear() - birth.getFullYear();
    const monthDiff = end.getMonth() - birth.getMonth();
    
    if (monthDiff < 0 || (monthDiff === 0 && end.getDate() < birth.getDate())) {
        return age - 1;
    }
    
    return age;
};

const getRelationColor = (relation) => {
    const colors = {
        'parent': 'bg-blue-100 text-blue-800',
        'child': 'bg-green-100 text-green-800',
        'spouse': 'bg-pink-100 text-pink-800',
        'sibling': 'bg-purple-100 text-purple-800',
        'grandparent': 'bg-indigo-100 text-indigo-800',
        'grandchild': 'bg-teal-100 text-teal-800',
        'aunt': 'bg-orange-100 text-orange-800',
        'uncle': 'bg-yellow-100 text-yellow-800',
        'cousin': 'bg-red-100 text-red-800',
        'niece': 'bg-emerald-100 text-emerald-800',
        'nephew': 'bg-cyan-100 text-cyan-800',
        'in-law': 'bg-gray-100 text-gray-800',
        'step': 'bg-slate-100 text-slate-800',
        'adopted': 'bg-amber-100 text-amber-800',
        'foster': 'bg-lime-100 text-lime-800'
    };
    
    return colors[relation?.toLowerCase()] || 'bg-gray-100 text-gray-800';
};
</script>