<template>
    <g>
        <path
            :d="connectionPath"
            stroke="#94a3b8"
            stroke-width="2"
            fill="none"
            stroke-dasharray="5 5"
            class="animated-dash"
        />
        <circle
            :cx="targetX"
            :cy="targetY"
            r="3"
            fill="#94a3b8"
        />
    </g>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
    sourceX: Number,
    sourceY: Number,
    targetX: Number,
    targetY: Number,
});

const connectionPath = computed(() => {
    const { sourceX, sourceY, targetX, targetY } = props;
    
    // Create a smooth bezier curve
    const midY = (sourceY + targetY) / 2;
    
    return `M ${sourceX} ${sourceY} C ${sourceX} ${midY}, ${targetX} ${midY}, ${targetX} ${targetY}`;
});
</script>

<style scoped>
.animated-dash {
    animation: dash 1s linear infinite;
}

@keyframes dash {
    from {
        stroke-dashoffset: 10;
    }
    to {
        stroke-dashoffset: 0;
    }
}
</style>
