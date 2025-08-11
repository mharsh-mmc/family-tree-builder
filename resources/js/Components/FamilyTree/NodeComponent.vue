<template>
    <div
        :class="[
            'family-tree-node',
            { 'selected': selected },
            { 'is-alive': data.is_alive },
            { 'is-deceased': !data.is_alive }
        ]"
        @click="handleNodeClick"
        @dblclick="handleNodeDoubleClick"
    >
        <!-- Profile Picture -->
        <div class="flex items-center space-x-3 mb-3">
            <div class="relative">
                <img
                    v-if="data.profile_pic"
                    :src="data.profile_pic"
                    :alt="data.name"
                    class="w-12 h-12 rounded-full object-cover border-2 border-gray-200"
                />
                <div
                    v-else
                    class="w-12 h-12 rounded-full bg-gradient-to-br from-primary-400 to-primary-600 flex items-center justify-center text-white font-bold text-lg"
                >
                    {{ data.name.charAt(0).toUpperCase() }}
                </div>
                <div
                    v-if="!data.is_alive"
                    class="absolute -top-1 -right-1 w-4 h-4 bg-gray-600 rounded-full flex items-center justify-center"
                >
                    <span class="text-white text-xs">†</span>
                </div>
            </div>
            <div class="flex-1 min-w-0">
                <h3 class="text-sm font-semibold text-gray-900 truncate">
                    {{ data.name }}
                </h3>
                <p class="text-xs text-gray-500">
                    {{ formatAge(data.dob, data.dod) }} years
                </p>
            </div>
        </div>

        <!-- Basic Info -->
        <div class="space-y-2 text-xs">
            <div v-if="data.dob" class="flex items-center space-x-2">
                <span class="text-gray-400">📅</span>
                <span class="text-gray-600">{{ formatDate(data.dob) }}</span>
            </div>
            <div v-if="data.dod && !data.is_alive" class="flex items-center space-x-2">
                <span class="text-gray-400">†</span>
                <span class="text-gray-600">{{ formatDate(data.dod) }}</span>
            </div>
            <div v-if="data.relation" class="flex items-center space-x-2">
                <span class="text-gray-400">👥</span>
                <span :class="getRelationColor(data.relation)" class="px-2 py-1 rounded-full text-xs font-medium">
                    {{ data.relation }}
                </span>
            </div>
        </div>

        <!-- Action Buttons -->
        <div v-if="selected" class="absolute top-2 right-2 flex space-x-1">
            <button
                @click.stop="editNode"
                class="w-6 h-6 bg-primary-500 text-white rounded-full flex items-center justify-center hover:bg-primary-600 transition-colors"
                title="Edit"
            >
                ✏️
            </button>
            <button
                @click.stop="deleteNode"
                class="w-6 h-6 bg-red-500 text-white rounded-full flex items-center justify-center hover:bg-red-600 transition-colors"
                title="Delete"
            >
                🗑️
            </button>
        </div>

        <!-- Connection Points -->
        <div class="absolute top-1/2 left-0 w-3 h-3 bg-primary-500 rounded-full transform -translate-y-1/2 -translate-x-1/2" />
        <div class="absolute top-1/2 right-0 w-3 h-3 bg-primary-500 rounded-full transform -translate-y-1/2 translate-x-1/2" />
        <div class="absolute top-0 left-1/2 w-3 h-3 bg-primary-500 rounded-full transform -translate-x-1/2 -translate-y-1/2" />
        <div class="absolute bottom-0 left-1/2 w-3 h-3 bg-primary-500 rounded-full transform -translate-x-1/2 translate-y-1/2" />
    </div>
</template>

<script setup>
import { computed } from 'vue';
import { useVueFlow } from '@vueflow/core';

const props = defineProps({
    id: String,
    data: Object,
    selected: Boolean,
});

const emit = defineEmits(['nodeClick', 'nodeDoubleClick', 'editNode', 'deleteNode']);

const { getNode } = useVueFlow();

const handleNodeClick = () => {
    emit('nodeClick', props.id);
};

const handleNodeDoubleClick = () => {
    emit('nodeDoubleClick', props.id);
};

const editNode = () => {
    emit('editNode', props.id);
};

const deleteNode = () => {
    if (confirm('Are you sure you want to delete this family member?')) {
        emit('deleteNode', props.id);
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
    
    return colors[relation.toLowerCase()] || 'bg-gray-100 text-gray-800';
};
</script>

<style scoped>
.family-tree-node {
    @apply relative bg-white rounded-xl shadow-md border-2 border-gray-200 p-4 transition-all duration-200 cursor-pointer;
    min-width: 200px;
    max-width: 250px;
}

.family-tree-node:hover {
    @apply shadow-lg border-primary-300 transform scale-105;
}

.family-tree-node.selected {
    @apply border-primary-500 shadow-xl ring-2 ring-primary-200;
}

.family-tree-node.is-deceased {
    @apply opacity-75;
}

.family-tree-node.is-deceased .family-tree-node {
    @apply grayscale;
}
</style>