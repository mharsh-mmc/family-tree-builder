<template>
    <g>
        <!-- Main connection line -->
        <path
            :d="path"
            :stroke="edgeColor"
            :stroke-width="strokeWidth"
            fill="none"
            :class="[
                'family-tree-connection',
                { 'selected': selected },
                { 'hovered': hovered }
            ]"
            @mouseenter="handleMouseEnter"
            @mouseleave="handleMouseLeave"
            @click="handleEdgeClick"
        />
        
        <!-- Arrow marker -->
        <defs>
            <marker
                id="arrowhead"
                markerWidth="10"
                markerHeight="7"
                refX="9"
                refY="3.5"
                orient="auto"
            >
                <polygon
                    :fill="edgeColor"
                    points="0 0, 10 3.5, 0 7"
                />
            </marker>
        </defs>
        
        <!-- Edge label -->
        <text
            v-if="showLabel"
            :x="labelPosition.x"
            :y="labelPosition.y"
            text-anchor="middle"
            dominant-baseline="middle"
            class="text-xs font-medium fill-gray-700 pointer-events-none"
        >
            {{ data.relation_type || 'Family' }}
        </text>
        
        <!-- Hover effect -->
        <path
            v-if="hovered"
            :d="path"
            stroke="transparent"
            stroke-width="20"
            fill="none"
            class="cursor-pointer"
            @click="handleEdgeClick"
        />
    </g>
</template>

<script setup>
import { computed, ref } from 'vue';
import { getBezierPath, getSmoothStepPath } from '@vueflow/core';

const props = defineProps({
    id: String,
    source: String,
    target: String,
    sourceX: Number,
    sourceY: Number,
    targetX: Number,
    targetY: Number,
    sourcePosition: String,
    targetPosition: String,
    data: Object,
    selected: Boolean,
    style: Object,
    markerEnd: String,
    markerStart: String,
});

const emit = defineEmits(['edgeClick', 'edgeDelete']);

const hovered = ref(false);

const path = computed(() => {
    if (props.sourcePosition === 'right' && props.targetPosition === 'left') {
        // Horizontal connection
        return getSmoothStepPath({
            sourceX: props.sourceX,
            sourceY: props.sourceY,
            targetX: props.targetX,
            targetY: props.targetY,
            borderRadius: 8,
        });
    } else if (props.sourcePosition === 'bottom' && props.targetPosition === 'top') {
        // Vertical connection
        return getSmoothStepPath({
            sourceX: props.sourceX,
            sourceY: props.sourceY,
            targetX: props.targetX,
            targetY: props.targetY,
            borderRadius: 8,
        });
    } else {
        // Diagonal connection
        return getBezierPath({
            sourceX: props.sourceX,
            sourceY: props.sourceY,
            targetX: props.targetX,
            targetY: props.targetY,
        });
    }
});

const edgeColor = computed(() => {
    if (props.selected) return '#3b82f6'; // primary-500
    if (hovered.value) return '#1d4ed8'; // primary-700
    
    const relationType = props.data?.relation_type?.toLowerCase();
    const colors = {
        'parent': '#3b82f6', // blue
        'child': '#10b981', // green
        'spouse': '#ec4899', // pink
        'sibling': '#8b5cf6', // purple
        'grandparent': '#6366f1', // indigo
        'grandchild': '#14b8a6', // teal
        'aunt': '#f97316', // orange
        'uncle': '#eab308', // yellow
        'cousin': '#ef4444', // red
        'niece': '#059669', // emerald
        'nephew': '#0891b2', // cyan
        'in-law': '#64748b', // gray
        'step': '#475569', // slate
        'adopted': '#d97706', // amber
        'foster': '#84cc16', // lime
    };
    
    return colors[relationType] || '#6b7280'; // gray-500
});

const strokeWidth = computed(() => {
    if (props.selected) return 3;
    if (hovered.value) return 2.5;
    return 2;
});

const showLabel = computed(() => {
    return hovered.value || props.selected || props.data?.relation_type;
});

const labelPosition = computed(() => {
    const sourceX = props.sourceX;
    const sourceY = props.sourceY;
    const targetX = props.targetX;
    const targetY = props.targetY;
    
    return {
        x: (sourceX + targetX) / 2,
        y: (sourceY + targetY) / 2 - 10,
    };
});

const handleMouseEnter = () => {
    hovered.value = true;
};

const handleMouseLeave = () => {
    hovered.value = false;
};

const handleEdgeClick = () => {
    emit('edgeClick', props.id);
};

const handleEdgeDelete = () => {
    if (confirm('Are you sure you want to delete this connection?')) {
        emit('edgeDelete', props.id);
    }
};
</script>

<style scoped>
.family-tree-connection {
    transition: all 0.2s ease;
}

.family-tree-connection:hover {
    stroke-width: 3;
    filter: drop-shadow(0 2px 4px rgba(0, 0, 0, 0.1));
}

.family-tree-connection.selected {
    stroke-width: 4;
    filter: drop-shadow(0 4px 8px rgba(0, 0, 0, 0.2));
}

.family-tree-connection.hovered {
    stroke-width: 3;
    filter: drop-shadow(0 2px 4px rgba(0, 0, 0, 0.1));
}
</style>