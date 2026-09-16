<template>
    <span class="status-badge" :class="config.class">
        {{ config.label }}
    </span>
</template>

<script setup>
import { computed } from 'vue';
import { getOrderStatusConfig } from '../utils/OrderStatusUtils.js';

const props = defineProps({
    status: {
        type: String,
        default: ''
    }
});

const config = computed(() => getOrderStatusConfig(props.status));
</script>

<style scoped>
.status-badge {
    display: inline-block;
    padding: 4px 10px;
    border-radius: 6px;
    font-size: 0.75rem;
    font-weight: 700;
    letter-spacing: 0.5px;
    text-align: center;
    white-space: nowrap;
}

/* Yellow / Warning */
.status-warning {
    background-color: #fef3c7;
    color: #d97706;
}

/* Green / Success */
.status-success {
    background-color: #d1fae5;
    color: #059669;
}

/* Red / Danger */
.status-danger {
    background-color: #fee2e2;
    color: #dc2626;
}

/* Blue / Info (Khusus Refund & Chargeback) */
.status-info {
    background-color: #e0f2fe;
    color: #0284c7;
}

/* Gray / Default */
.status-default {
    background-color: #e5e7eb;
    color: #374151;
}
</style>