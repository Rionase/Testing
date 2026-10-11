<template>
    <span class="status-badge" :class="statusClass">
        {{ props.status }}
    </span>
</template>

<script setup>
import { computed } from 'vue';
import { OrderStatus, CustomerOrderStatus } from "@/enums/OrderStatus.js";

const props = defineProps({
    status: {
        type: String,
        default: ''
    },
    isCustomerStatus: {
        type: Boolean,
        default: false
    }
});

// 1. Map Class Status Admin / Payment Gateway
const ADMIN_STATUS_CLASS = {
    [OrderStatus.INITIATED]: 'status-warning',
    [OrderStatus.PENDING]: 'status-warning',
    [OrderStatus.AUTHORIZE]: 'status-warning',

    [OrderStatus.CAPTURE]: 'status-success',
    [OrderStatus.SETTLEMENT]: 'status-success',
    [OrderStatus.FINISHED]: 'status-success',

    [OrderStatus.DENY]: 'status-danger',
    [OrderStatus.CANCEL]: 'status-danger',
    [OrderStatus.EXPIRE]: 'status-danger',
    [OrderStatus.FAILURE]: 'status-danger',

    [OrderStatus.REFUND]: 'status-info',
    [OrderStatus.PARTIAL_REFUND]: 'status-info',
    [OrderStatus.CHARGEBACK]: 'status-info',
    [OrderStatus.PARTIAL_CHARGEBACK]: 'status-info',

    [OrderStatus.ON_DELIVERY]: 'status-primary',
};

// 2. Map Class Status Customer
const CUSTOMER_STATUS_CLASS = {
    [CustomerOrderStatus.PENDING_PAYMENT]: 'status-warning',

    [CustomerOrderStatus.PAID]: 'status-success',
    [CustomerOrderStatus.COMPLETED]: 'status-success',

    [CustomerOrderStatus.FAILED]: 'status-danger',
    [CustomerOrderStatus.CANCELLED]: 'status-danger',
    [CustomerOrderStatus.EXPIRED]: 'status-danger',

    [CustomerOrderStatus.REFUNDED]: 'status-info',
    [CustomerOrderStatus.PARTIALLY_REFUNDED]: 'status-info',

    [CustomerOrderStatus.ON_DELIVERY]: 'status-primary',
};

const DEFAULT_CLASS = 'status-default';

const statusClass = computed(() => {
    if (!props.status) return DEFAULT_CLASS;

    const rawStatus = String(props.status).trim().toUpperCase();
    const mapClass = props.isCustomerStatus ? CUSTOMER_STATUS_CLASS : ADMIN_STATUS_CLASS;

    // Menangani pencarian langsung atau pencocokan format underscore / spasi
    return mapClass[rawStatus]
        || mapClass[rawStatus.replace(/\s+/g, '_')]
        || DEFAULT_CLASS;
});
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

/* Blue / Info */
.status-info {
    background-color: #e0f2fe;
    color: #0284c7;
}

/* Indigo / Primary (Khusus On Delivery) */
.status-primary {
    background-color: #e0e7ff;
    color: #4338ca;
}

/* Gray / Default */
.status-default {
    background-color: #e5e7eb;
    color: #374151;
}
</style>